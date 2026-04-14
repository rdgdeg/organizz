<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Mail\OrganizerNewRegistrationMail;
use App\Mail\RegistrationConfirmationMail;
use App\Models\Event;
use App\Models\ParticipantEmailToken;
use App\Models\Registration;
use App\Models\Slot;
use App\Rules\PhoneBeFr;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class EventPublicController extends Controller
{
    public function show(string $slug): View
    {
        $event = Event::query()->where('slug', $slug)->firstOrFail();

        if (! $event->isOpen()) {
            abort(404);
        }

        $positions = $event->positions()->orderBy('id')->with(['slots' => function ($q): void {
            $q->orderBy('date')->orderBy('start_time')->withCount([
                'registrations as active_count' => function ($q2): void {
                    $q2->whereNull('cancelled_at')->where('waitlist', false);
                },
            ]);
        }])->get();

        $byDay = [];
        foreach ($positions as $pos) {
            foreach ($pos->slots as $slot) {
                $d = $slot->date?->toDateString();
                if (! isset($byDay[$d])) {
                    $byDay[$d] = [];
                }
                $waitlistCount = $slot->registrations()
                    ->whereNull('cancelled_at')
                    ->where('waitlist', true)
                    ->count();
                $full = $slot->active_count >= $slot->max_volunteers;
                $byDay[$d][] = [
                    'position' => $pos->name,
                    'position_id' => $pos->id,
                    'position_color' => $pos->color,
                    'slot_id' => $slot->id,
                    'start_time' => substr((string) $slot->start_time, 0, 5),
                    'end_time' => substr((string) $slot->end_time, 0, 5),
                    'max' => $slot->max_volunteers,
                    'active' => $slot->active_count,
                    'full' => $full,
                    'waitlist_count' => $waitlistCount,
                    'can_waitlist' => $full && $event->waitlist_enabled,
                ];
            }
        }
        ksort($byDay);

        return view('public.event', [
            'event' => $event,
            'days' => $byDay,
        ]);
    }

    public function register(Request $request, string $slug): RedirectResponse
    {
        if ($request->filled('company')) {
            abort(422);
        }

        $event = Event::query()->where('slug', $slug)->firstOrFail();
        if (! $event->isOpen()) {
            abort(404);
        }

        $validated = $request->validate([
            'firstname' => ['required', 'string', 'max:120'],
            'lastname' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:32', new PhoneBeFr],
            'slot_ids' => ['required', 'array', 'min:1'],
            'slot_ids.*' => ['integer', 'exists:slots,id'],
            'preferred_reminder_channel' => ['nullable', 'in:email,sms,push'],
        ]);

        $customAnswers = $this->validateCustomAnswers($request, $event);

        $slotIds = array_values(array_unique($validated['slot_ids']));
        $owner = $event->user;
        $limit = config('plans.free.max_registrations_per_event');
        if ($owner->plan !== 'pro') {
            $current = Registration::query()
                ->whereHas('slot.position', fn ($q) => $q->where('event_id', $event->id))
                ->whereNull('cancelled_at')
                ->where('waitlist', false)
                ->count();
            $newConfirmed = 0;
            foreach ($slotIds as $slotId) {
                $slot = Slot::query()->with('position')->findOrFail($slotId);
                if ($slot->position->event_id !== $event->id) {
                    abort(403);
                }
                $active = $slot->registrations()->whereNull('cancelled_at')->where('waitlist', false)->count();
                $willWaitlist = $active >= $slot->max_volunteers && $event->waitlist_enabled;
                if (! $willWaitlist) {
                    $newConfirmed++;
                }
            }
            if ($current + $newConfirmed > $limit) {
                throw ValidationException::withMessages([
                    'slot_ids' => __('Limite d’inscriptions atteinte pour cet événement.'),
                ]);
            }
        }

        foreach ($slotIds as $slotId) {
            $slot = Slot::query()->with('position')->findOrFail($slotId);
            if ($slot->position->event_id !== $event->id) {
                abort(403);
            }
            $exists = Registration::query()
                ->where('slot_id', $slot->id)
                ->where('email', $validated['email'])
                ->whereNull('cancelled_at')
                ->exists();
            if ($exists) {
                throw ValidationException::withMessages([
                    'email' => __('Vous êtes déjà inscrit sur ce créneau (ou en liste d’attente).'),
                ]);
            }
        }

        $channel = $validated['preferred_reminder_channel'] ?? 'email';
        if ($channel === 'sms' && empty($validated['phone'])) {
            throw ValidationException::withMessages([
                'phone' => __('Un numéro de téléphone est requis pour les rappels par SMS.'),
            ]);
        }

        $batchId = (string) Str::uuid();

        DB::transaction(function () use ($event, $validated, $slotIds, $batchId, $customAnswers, $channel): void {
            foreach ($slotIds as $slotId) {
                /** @var Slot $slot */
                $slot = Slot::query()->lockForUpdate()->with('position')->findOrFail($slotId);
                if ($slot->position->event_id !== $event->id) {
                    abort(403);
                }

                $active = $slot->registrations()->whereNull('cancelled_at')->where('waitlist', false)->count();
                $isFull = $active >= $slot->max_volunteers;

                if ($isFull) {
                    if (! $event->waitlist_enabled) {
                        throw ValidationException::withMessages([
                            'slot_ids' => __('Un ou plusieurs créneaux sont complets.'),
                        ]);
                    }
                    $maxPos = (int) Registration::query()
                        ->where('slot_id', $slot->id)
                        ->where('waitlist', true)
                        ->whereNull('cancelled_at')
                        ->max('waitlist_position');

                    Registration::query()->create([
                        'slot_id' => $slot->id,
                        'batch_id' => $batchId,
                        'firstname' => $validated['firstname'],
                        'lastname' => $validated['lastname'],
                        'email' => $validated['email'],
                        'phone' => $validated['phone'] ?? null,
                        'custom_field_answers' => $customAnswers,
                        'waitlist' => true,
                        'waitlist_position' => $maxPos + 1,
                        'token' => (string) Str::uuid(),
                        'preferred_reminder_channel' => $channel,
                    ]);

                    continue;
                }

                Registration::query()->create([
                    'slot_id' => $slot->id,
                    'batch_id' => $batchId,
                    'firstname' => $validated['firstname'],
                    'lastname' => $validated['lastname'],
                    'email' => $validated['email'],
                    'phone' => $validated['phone'] ?? null,
                    'custom_field_answers' => $customAnswers,
                    'waitlist' => false,
                    'token' => (string) Str::uuid(),
                    'preferred_reminder_channel' => $channel,
                ]);
            }
        });

        ParticipantEmailToken::forEmail($validated['email']);

        $regs = Registration::query()->where('batch_id', $batchId)->with('slot.position')->get();

        Mail::to($validated['email'])->queue(new RegistrationConfirmationMail($regs));

        if ($event->notify_organizer_on_registration && $event->user?->email) {
            Mail::to($event->user->email)->queue(new OrganizerNewRegistrationMail($event, $regs));
        }

        return redirect()->route('public.evenement.confirmation', ['slug' => $slug, 'batch' => $batchId]);
    }

    public function confirm(string $slug, string $batch): View
    {
        $event = Event::query()->where('slug', $slug)->firstOrFail();
        $regs = Registration::query()
            ->where('batch_id', $batch)
            ->whereHas('slot.position', fn ($q) => $q->where('event_id', $event->id))
            ->with('slot.position')
            ->get();

        if ($regs->isEmpty()) {
            abort(404);
        }

        $portal = ParticipantEmailToken::query()->where('email', strtolower($regs->first()->email))->first();

        return view('public.confirm', [
            'event' => $event,
            'registrations' => $regs,
            'participant_portal_url' => $portal ? route('participant.espace', ['token' => $portal->token]) : null,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function validateCustomAnswers(Request $request, Event $event): array
    {
        $defs = $event->custom_fields ?? [];
        if (! is_array($defs)) {
            return [];
        }

        $input = $request->input('custom_fields', []);
        if (! is_array($input)) {
            $input = [];
        }

        $answers = [];
        foreach ($defs as $def) {
            if (! is_array($def) || empty($def['id']) || empty($def['type'])) {
                continue;
            }
            $id = (string) $def['id'];
            $label = (string) ($def['label'] ?? $id);
            $val = $input[$id] ?? null;
            $required = ! empty($def['required']);
            if ($required && ($val === null || $val === '')) {
                throw ValidationException::withMessages([
                    "custom_fields.$id" => __('Le champ :label est requis.', ['label' => $label]),
                ]);
            }
            if ($val !== null && $val !== '') {
                if (($def['type'] ?? '') === 'select' && isset($def['options']) && is_array($def['options']) && ! in_array($val, $def['options'], true)) {
                    throw ValidationException::withMessages([
                        "custom_fields.$id" => __('Valeur invalide pour :label.', ['label' => $label]),
                    ]);
                }
                $answers[$id] = is_string($val) ? mb_substr($val, 0, 2000) : $val;
            }
        }

        return $answers;
    }
}
