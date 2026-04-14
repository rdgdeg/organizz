<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Mail\ParticipantReminderMail;
use App\Models\Event;
use App\Models\Registration;
use App\Models\Slot;
use App\Services\WaitlistService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response;

class RegistrationAdminController extends Controller
{
    public function index(Request $request, Event $event): Response
    {
        $this->authorize('manageRegistrations', $event);

        $positionId = $request->query('position_id');
        $day = $request->query('day');
        $status = $request->query('status', 'active');

        $query = Registration::query()
            ->with(['slot.position'])
            ->whereHas('slot.position', fn ($q) => $q->where('event_id', $event->id));

        if ($positionId) {
            $query->whereHas('slot', fn ($q) => $q->where('position_id', $positionId));
        }

        if ($day) {
            $query->whereHas('slot', fn ($q) => $q->whereDate('date', $day));
        }

        if ($status === 'active') {
            $query->whereNull('cancelled_at');
        } elseif ($status === 'cancelled') {
            $query->whereNotNull('cancelled_at');
        }

        $rows = $query->orderByDesc('id')->limit(500)->get()->map(fn (Registration $r) => [
            'id' => $r->id,
            'firstname' => $r->firstname,
            'lastname' => $r->lastname,
            'email' => $r->email,
            'phone' => $r->phone,
            'waitlist' => $r->waitlist,
            'cancelled_at' => $r->cancelled_at?->toIso8601String(),
            'checked_in_at' => $r->checked_in_at?->toIso8601String(),
            'slot' => [
                'id' => $r->slot->id,
                'date' => $r->slot->date?->toDateString(),
                'start_time' => $r->slot->start_time,
                'end_time' => $r->slot->end_time,
                'position' => $r->slot->position->name,
            ],
        ]);

        $positions = $event->positions()->orderBy('name')->get(['id', 'name']);

        return Inertia::render('Organizer/Registrations/Index', [
            'event' => [
                'id' => $event->id,
                'slug' => $event->slug,
                'title' => $event->title,
            ],
            'filters' => [
                'position_id' => $positionId,
                'day' => $day,
                'status' => $status,
            ],
            'positions' => $positions,
            'registrations' => $rows,
        ]);
    }

    public function destroy(Request $request, Event $event, Registration $registration)
    {
        $this->authorize('manageRegistrations', $event);
        $registration->load('slot.position');
        if ($registration->slot->position->event_id !== $event->id) {
            abort(404);
        }

        DB::transaction(function () use ($registration): void {
            $wasWaitlist = $registration->waitlist;
            $slotId = $registration->slot_id;

            $registration->cancelled_at = now();
            $registration->save();

            if (! $wasWaitlist && $slotId) {
                $slot = Slot::query()->lockForUpdate()->findOrFail($slotId);
                app(WaitlistService::class)->promoteNext($slot);
            }
        });

        return back()->with('success', __('Inscription annulée.'));
    }

    public function sendRecap(Request $request, Event $event, Registration $registration)
    {
        $this->authorize('manageRegistrations', $event);
        $registration->load('slot.position.event');
        if ($registration->slot->position->event_id !== $event->id) {
            abort(404);
        }

        $regs = Registration::query()
            ->where('email', $registration->email)
            ->whereHas('slot.position', fn ($q) => $q->where('event_id', $event->id))
            ->whereNull('cancelled_at')
            ->with('slot.position')
            ->get();

        Mail::to($registration->email)->queue(
            new ParticipantReminderMail($event, $regs, 0, true)
        );

        return back()->with('success', __('Récapitulatif envoyé.'));
    }
}
