<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Registration;
use App\Models\Slot;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
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
                'registration_enabled' => $e->registration_enabled,
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
            'additional_info' => ['nullable', 'string'],
            'recommendations' => ['nullable', 'string'],
            'practical_details' => ['nullable', 'string'],
            'date_start' => ['required', 'date'],
            'date_end' => ['required', 'date', 'after_or_equal:date_start'],
            'daily_window_start' => ['nullable', 'date_format:H:i'],
            'daily_window_end' => ['nullable', 'date_format:H:i'],
            'use_per_day_schedule' => ['sometimes', 'boolean'],
            'day_schedules' => ['nullable', 'array'],
        ]);

        $daySchedules = $this->normalizeDaySchedules($request, $validated['date_start'], $validated['date_end']);

        $slug = $this->uniqueSlug($validated['title']);
        $token = $this->uniquePublicToken();
        $embed = $this->uniqueEmbedToken();

        $event = $request->user()->events()->create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'additional_info' => $validated['additional_info'] ?? null,
            'recommendations' => $validated['recommendations'] ?? null,
            'practical_details' => $validated['practical_details'] ?? null,
            'slug' => $slug,
            'date_start' => $validated['date_start'],
            'date_end' => $validated['date_end'],
            'daily_window_start' => ($validated['daily_window_start'] ?? '08:00').':00',
            'daily_window_end' => ($validated['daily_window_end'] ?? '20:00').':00',
            'day_schedules' => $daySchedules,
            'status' => 'open',
            'registration_enabled' => true,
            'public_link_token' => $token,
            'embed_token' => $embed,
        ]);

        return redirect()->route('evenements.montrer', $event)->with('success', __('Événement créé.'));
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

        $slotQuery = Slot::query()->whereHas('position', fn ($q) => $q->where('event_id', $event->id));
        $capacityPlaces = (int) (clone $slotQuery)->sum('max_volunteers');
        $slotsCount = (int) (clone $slotQuery)->count();
        $positionsCount = $event->positions()->count();
        $spotsOpen = max(0, $capacityPlaces - $totalRegs);
        $fillPercentPlaces = $capacityPlaces > 0 ? (int) round(100 * $totalRegs / $capacityPlaces) : 0;
        $waitlistCount = Registration::query()
            ->whereHas('slot.position', fn ($q) => $q->where('event_id', $event->id))
            ->where('waitlist', true)
            ->whereNull('cancelled_at')
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
                'public_url' => url('/evenement/'.$event->slug),
                'embed_url' => $event->embed_token ? url('/integration/'.$event->embed_token) : null,
                'qr_url' => url('/evenement/'.$event->slug.'/qr.png'),
                'waitlist_enabled' => $event->waitlist_enabled,
                'participant_edit_deadline_hours' => $event->participant_edit_deadline_hours,
                'matching_enabled' => $event->matching_enabled,
                'registration_enabled' => $event->registration_enabled,
            ],
            'positions' => $positions,
            'stats' => [
                'total_registrations' => $totalRegs,
                'fill_rate' => $fillRate,
                'capacity_places' => $capacityPlaces,
                'spots_open' => $spotsOpen,
                'fill_percent_places' => $fillPercentPlaces,
                'slots_count' => $slotsCount,
                'positions_count' => $positionsCount,
                'waitlist_count' => $waitlistCount,
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
                'slug' => $event->slug,
                'title' => $event->title,
                'description' => $event->description,
                'additional_info' => $event->additional_info,
                'recommendations' => $event->recommendations,
                'practical_details' => $event->practical_details,
                'date_start' => $event->date_start?->toDateString(),
                'date_end' => $event->date_end?->toDateString(),
                'daily_window_start' => substr((string) $event->daily_window_start, 0, 5),
                'daily_window_end' => substr((string) $event->daily_window_end, 0, 5),
                'day_schedules' => $event->day_schedules,
                'status' => $event->status,
                'notify_organizer_on_registration' => $event->notify_organizer_on_registration,
                'waitlist_enabled' => $event->waitlist_enabled,
                'participant_edit_deadline_hours' => $event->participant_edit_deadline_hours,
                'matching_enabled' => $event->matching_enabled,
                'registration_enabled' => $event->registration_enabled,
                'custom_fields' => $event->custom_fields ?? [],
            ],
        ]);
    }

    public function updateRegistrationEnabled(Request $request, Event $event)
    {
        $this->authorize('configure', $event);

        $validated = $request->validate([
            'registration_enabled' => ['required', 'boolean'],
        ]);

        $event->registration_enabled = $validated['registration_enabled'];
        $event->save();

        return redirect()->back()->with(
            'success',
            $event->registration_enabled
                ? __('Les inscriptions publiques sont ouvertes.')
                : __('Les inscriptions publiques sont désactivées. La page publique reste consultable.')
        );
    }

    public function update(Request $request, Event $event)
    {
        $this->authorize('update', $event);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'additional_info' => ['nullable', 'string'],
            'recommendations' => ['nullable', 'string'],
            'practical_details' => ['nullable', 'string'],
            'date_start' => ['required', 'date'],
            'date_end' => ['required', 'date', 'after_or_equal:date_start'],
            'daily_window_start' => ['nullable', 'date_format:H:i'],
            'daily_window_end' => ['nullable', 'date_format:H:i'],
            'status' => ['required', 'in:open,closed,archived'],
            'registration_enabled' => ['boolean'],
            'notify_organizer_on_registration' => ['boolean'],
            'waitlist_enabled' => ['boolean'],
            'participant_edit_deadline_hours' => ['required', 'integer', 'min:1', 'max:720'],
            'matching_enabled' => ['boolean'],
            'custom_fields' => ['nullable', 'array'],
            'use_per_day_schedule' => ['sometimes', 'boolean'],
            'day_schedules' => ['nullable', 'array'],
        ]);

        $daySchedules = $this->normalizeDaySchedules($request, $validated['date_start'], $validated['date_end']);

        $event->fill([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'additional_info' => $validated['additional_info'] ?? null,
            'recommendations' => $validated['recommendations'] ?? null,
            'practical_details' => $validated['practical_details'] ?? null,
            'date_start' => $validated['date_start'],
            'date_end' => $validated['date_end'],
            'daily_window_start' => ($validated['daily_window_start'] ?? '08:00').':00',
            'daily_window_end' => ($validated['daily_window_end'] ?? '20:00').':00',
            'day_schedules' => $daySchedules,
            'status' => $validated['status'],
            'registration_enabled' => (bool) ($validated['registration_enabled'] ?? true),
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

        return redirect()->route('evenements.montrer', $event)->with('success', __('Événement mis à jour.'));
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

    /**
     * Planning par jour (multi-jours) : null = même fenêtre quotidienne pour chaque jour.
     *
     * @return array<int, array{date: string, enabled: bool, window_start: ?string, window_end: ?string}>|null
     */
    private function normalizeDaySchedules(Request $request, string $dateStart, string $dateEnd): ?array
    {
        if (! $request->boolean('use_per_day_schedule')) {
            return null;
        }

        $start = Carbon::parse($dateStart)->startOfDay();
        $end = Carbon::parse($dateEnd)->startOfDay();
        if ($start->gt($end)) {
            return null;
        }

        $allowed = [];
        for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
            $allowed[] = $d->toDateString();
        }

        $rows = $request->input('day_schedules');
        if (! is_array($rows) || $rows === []) {
            throw ValidationException::withMessages([
                'day_schedules' => __('Indiquez le planning par jour ou désactivez cette option.'),
            ]);
        }

        if (count($rows) !== count($allowed)) {
            throw ValidationException::withMessages([
                'day_schedules' => __('Le nombre de jours ne correspond pas à la période de l’événement.'),
            ]);
        }

        $out = [];
        foreach ($rows as $i => $row) {
            if (! is_array($row)) {
                throw ValidationException::withMessages([
                    'day_schedules' => __('Données de planning invalides.'),
                ]);
            }
            $date = $row['date'] ?? null;
            if (! is_string($date) || ! in_array($date, $allowed, true)) {
                throw ValidationException::withMessages([
                    "day_schedules.{$i}.date" => __('Date invalide dans le planning.'),
                ]);
            }
            $enabled = filter_var($row['enabled'] ?? false, FILTER_VALIDATE_BOOLEAN);
            $ws = $row['window_start'] ?? null;
            $we = $row['window_end'] ?? null;
            if ($enabled) {
                if (empty($ws) || empty($we)) {
                    throw ValidationException::withMessages([
                        "day_schedules.{$i}.window_start" => __('Indiquez le début et la fin pour chaque jour activé.'),
                    ]);
                }
                try {
                    $wsNorm = Carbon::createFromFormat('H:i', substr((string) $ws, 0, 5))->format('H:i');
                    $weNorm = Carbon::createFromFormat('H:i', substr((string) $we, 0, 5))->format('H:i');
                } catch (\Throwable) {
                    throw ValidationException::withMessages([
                        "day_schedules.{$i}.window_start" => __('Format d’heure invalide (HH:MM).'),
                    ]);
                }
                $sMin = $this->timeToMinutesFromString($wsNorm.':00');
                $eMin = $this->timeToMinutesFromString($weNorm.':00');
                if ($eMin <= $sMin) {
                    throw ValidationException::withMessages([
                        "day_schedules.{$i}.window_end" => __('La fin doit être après le début.'),
                    ]);
                }
                $out[] = [
                    'date' => $date,
                    'enabled' => true,
                    'window_start' => $wsNorm,
                    'window_end' => $weNorm,
                ];
            } else {
                $out[] = [
                    'date' => $date,
                    'enabled' => false,
                    'window_start' => null,
                    'window_end' => null,
                ];
            }
        }

        usort($out, fn ($a, $b) => strcmp($a['date'], $b['date']));
        $sortedDates = array_column($out, 'date');
        if ($sortedDates !== $allowed) {
            throw ValidationException::withMessages([
                'day_schedules' => __('Chaque jour de la période doit apparaître une fois dans le planning.'),
            ]);
        }

        return $out;
    }

    private function timeToMinutesFromString(string $time): int
    {
        $time = trim($time);
        if (strlen($time) >= 8) {
            $time = substr($time, 0, 8);
        }
        $parts = explode(':', $time);
        $h = (int) ($parts[0] ?? 0);
        $m = (int) ($parts[1] ?? 0);
        $s = (int) ($parts[2] ?? 0);

        return $h * 60 + $m + (int) round($s / 60);
    }
}
