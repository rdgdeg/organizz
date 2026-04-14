<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class EventController extends Controller
{
    public function index(Request $request): Response
    {
        $uid = $request->user()->id;
        $events = Event::query()
            ->where(function ($q) use ($uid): void {
                $q->where('user_id', $uid)
                    ->orWhereHas('collaborators', fn ($q2) => $q2->where('users.id', $uid));
            })
            ->orderByDesc('updated_at')
            ->get()
            ->map(fn (Event $e) => [
                'id' => $e->id,
                'title' => $e->title,
                'slug' => $e->slug,
                'status' => $e->status,
                'date_start' => $e->date_start?->toDateString(),
                'date_end' => $e->date_end?->toDateString(),
                'public_link_token' => $e->public_link_token,
            ]);

        return Inertia::render('Organizer/Events/Index', [
            'events' => $events,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Organizer/Events/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'date_start' => ['required', 'date'],
            'date_end' => ['required', 'date', 'after_or_equal:date_start'],
            'daily_window_start' => ['nullable', 'date_format:H:i'],
            'daily_window_end' => ['nullable', 'date_format:H:i'],
            'status' => ['required', 'in:draft,open,closed,archived'],
        ]);

        $slug = $this->uniqueSlug($validated['title']);
        $token = $this->uniquePublicToken();
        $embed = $this->uniqueEmbedToken();

        $event = $request->user()->events()->create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'slug' => $slug,
            'date_start' => $validated['date_start'],
            'date_end' => $validated['date_end'],
            'daily_window_start' => ($validated['daily_window_start'] ?? '08:00').':00',
            'daily_window_end' => ($validated['daily_window_end'] ?? '20:00').':00',
            'status' => $validated['status'],
            'public_link_token' => $token,
            'embed_token' => $embed,
        ]);

        return redirect()->route('events.show', $event)->with('success', __('Événement créé.'));
    }

    public function show(Request $request, Event $event): Response
    {
        $this->authorize('view', $event);

        $event->load([
            'positions' => function ($q): void {
                $q->orderBy('id');
            },
        ]);

        $positions = $event->positions->map(function ($p) {
            $slots = $p->slots()->withCount([
                'registrations as active_count' => function ($q): void {
                    $q->whereNull('cancelled_at')->where('waitlist', false);
                },
            ])->orderBy('date')->orderBy('start_time')->get();

            $slotsPayload = $slots->map(fn ($s) => [
                'id' => $s->id,
                'date' => $s->date?->toDateString(),
                'start_time' => $s->start_time,
                'end_time' => $s->end_time,
                'max_volunteers' => $s->max_volunteers,
                'active_count' => $s->active_count,
                'percent' => $s->max_volunteers > 0 ? round(($s->active_count / $s->max_volunteers) * 100) : 0,
            ]);

            $totalSlots = $slots->count();
            $filled = $slots->filter(fn ($s) => $s->active_count >= $s->max_volunteers)->count();
            $crit = $slots->filter(function ($s) {
                if ($s->max_volunteers <= 0) {
                    return false;
                }

                return ($s->active_count / $s->max_volunteers) < 0.5;
            })->count();

            return [
                'id' => $p->id,
                'name' => $p->name,
                'color' => $p->color,
                'slots' => $slotsPayload,
                'stats' => [
                    'total_slots' => $totalSlots,
                    'filled_slots' => $filled,
                    'critical_slots' => $crit,
                ],
            ];
        });

        $totalRegs = Registration::query()
            ->whereHas('slot.position', fn ($q) => $q->where('event_id', $event->id))
            ->whereNull('cancelled_at')
            ->where('waitlist', false)
            ->count();

        $maxSlots = $positions->sum(fn ($p) => count($p['slots']));
        $filledSlots = $positions->sum(fn ($p) => collect($p['slots'])->filter(fn ($s) => $s['active_count'] >= $s['max_volunteers'])->count());
        $fillRate = $maxSlots > 0 ? round(($filledSlots / $maxSlots) * 100) : 0;

        $daysUntilStart = (int) now()->startOfDay()->diffInDays($event->date_start->copy()->startOfDay(), false);
        $j3Critical = $daysUntilStart === 3 && $fillRate < 50;

        $signupTimeline = Registration::query()
            ->whereHas('slot.position', fn ($q) => $q->where('event_id', $event->id))
            ->whereNull('cancelled_at')
            ->where('waitlist', false)
            ->selectRaw('date(created_at) as d')
            ->selectRaw('count(*) as c')
            ->groupBy('d')
            ->orderBy('d')
            ->get()
            ->map(fn ($row) => ['date' => $row->d, 'count' => (int) $row->c]);

        $user = $request->user();

        return Inertia::render('Organizer/Events/Show', [
            'event' => [
                'id' => $event->id,
                'title' => $event->title,
                'description' => $event->description,
                'slug' => $event->slug,
                'status' => $event->status,
                'date_start' => $event->date_start?->toDateString(),
                'date_end' => $event->date_end?->toDateString(),
                'daily_window_start' => substr((string) $event->daily_window_start, 0, 5),
                'daily_window_end' => substr((string) $event->daily_window_end, 0, 5),
                'public_link_token' => $event->public_link_token,
                'embed_token' => $event->embed_token,
                'notify_organizer_on_registration' => $event->notify_organizer_on_registration,
                'public_url' => url('/event/'.$event->slug),
                'embed_url' => $event->embed_token ? url('/embed/'.$event->embed_token) : null,
                'qr_url' => url('/event/'.$event->slug.'/qr.png'),
                'waitlist_enabled' => $event->waitlist_enabled,
                'participant_edit_deadline_hours' => $event->participant_edit_deadline_hours,
                'matching_enabled' => $event->matching_enabled,
            ],
            'positions' => $positions,
            'stats' => [
                'total_registrations' => $totalRegs,
                'fill_rate' => $fillRate,
                'days_until_start' => $daysUntilStart,
                'j3_critical' => $j3Critical,
                'signup_timeline' => $signupTimeline,
            ],
            'permissions' => [
                'configure' => $user->can('configure', $event),
                'manageRegistrations' => $user->can('manageRegistrations', $event),
                'delete' => $user->can('delete', $event),
                'manageCollaborators' => $user->can('manageCollaborators', $event),
            ],
        ]);
    }

    public function edit(Event $event): Response
    {
        $this->authorize('update', $event);

        return Inertia::render('Organizer/Events/Edit', [
            'event' => [
                'id' => $event->id,
                'title' => $event->title,
                'description' => $event->description,
                'date_start' => $event->date_start?->toDateString(),
                'date_end' => $event->date_end?->toDateString(),
                'daily_window_start' => substr((string) $event->daily_window_start, 0, 5),
                'daily_window_end' => substr((string) $event->daily_window_end, 0, 5),
                'status' => $event->status,
                'notify_organizer_on_registration' => $event->notify_organizer_on_registration,
                'waitlist_enabled' => $event->waitlist_enabled,
                'participant_edit_deadline_hours' => $event->participant_edit_deadline_hours,
                'matching_enabled' => $event->matching_enabled,
                'custom_fields' => $event->custom_fields ?? [],
            ],
        ]);
    }

    public function update(Request $request, Event $event)
    {
        $this->authorize('update', $event);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'date_start' => ['required', 'date'],
            'date_end' => ['required', 'date', 'after_or_equal:date_start'],
            'daily_window_start' => ['nullable', 'date_format:H:i'],
            'daily_window_end' => ['nullable', 'date_format:H:i'],
            'status' => ['required', 'in:draft,open,closed,archived'],
            'notify_organizer_on_registration' => ['boolean'],
            'waitlist_enabled' => ['boolean'],
            'participant_edit_deadline_hours' => ['required', 'integer', 'min:1', 'max:720'],
            'matching_enabled' => ['boolean'],
            'custom_fields' => ['nullable', 'array'],
        ]);

        $event->fill([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'date_start' => $validated['date_start'],
            'date_end' => $validated['date_end'],
            'daily_window_start' => ($validated['daily_window_start'] ?? '08:00').':00',
            'daily_window_end' => ($validated['daily_window_end'] ?? '20:00').':00',
            'status' => $validated['status'],
            'notify_organizer_on_registration' => (bool) ($validated['notify_organizer_on_registration'] ?? false),
            'waitlist_enabled' => (bool) ($validated['waitlist_enabled'] ?? true),
            'participant_edit_deadline_hours' => (int) $validated['participant_edit_deadline_hours'],
            'matching_enabled' => (bool) ($validated['matching_enabled'] ?? true),
            'custom_fields' => $validated['custom_fields'] ?? null,
        ]);

        if ($event->isDirty('title')) {
            $event->slug = $this->uniqueSlug($validated['title'], $event->id);
        }

        $event->save();

        return redirect()->route('events.show', $event)->with('success', __('Événement mis à jour.'));
    }

    public function destroy(Event $event)
    {
        $this->authorize('delete', $event);
        $event->delete();

        return redirect()->route('dashboard')->with('success', __('Événement supprimé.'));
    }

    private function uniqueSlug(string $title, ?int $exceptId = null): string
    {
        $base = Str::slug($title);
        if ($base === '') {
            $base = 'evenement';
        }
        $slug = $base;
        $i = 0;
        while (Event::query()
            ->where('slug', $slug)
            ->when($exceptId, fn ($q) => $q->where('id', '!=', $exceptId))
            ->exists()) {
            $i++;
            $slug = $base.'-'.$i;
        }

        return $slug;
    }

    private function uniquePublicToken(): string
    {
        do {
            $token = Str::random(48);
        } while (Event::query()->where('public_link_token', $token)->exists());

        return $token;
    }

    private function uniqueEmbedToken(): string
    {
        do {
            $token = Str::random(48);
        } while (Event::query()->where('embed_token', $token)->exists());

        return $token;
    }
}
