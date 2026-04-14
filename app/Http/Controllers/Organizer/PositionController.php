<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Position;
use App\Services\SlotGeneratorService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PositionController extends Controller
{
    public function __construct(
        private SlotGeneratorService $slotGenerator,
    ) {}

    public function index(Request $request, Event $event): Response
    {
        $this->authorize('view', $event);

        $user = $request->user();

        $positions = $event->positions()->orderBy('id')->get()->map(function (Position $p) {
            $slots = $p->slots()
                ->withCount([
                    'registrations as booked' => function ($q): void {
                        $q->whereNull('cancelled_at')->where('waitlist', false);
                    },
                ])
                ->orderBy('date')
                ->orderBy('start_time')
                ->get()
                ->map(fn ($s) => [
                    'id' => $s->id,
                    'date' => $s->date?->toDateString(),
                    'start_time' => is_string($s->start_time) ? substr($s->start_time, 0, 5) : substr((string) $s->start_time, 0, 5),
                    'end_time' => is_string($s->end_time) ? substr($s->end_time, 0, 5) : substr((string) $s->end_time, 0, 5),
                    'max_volunteers' => $s->max_volunteers,
                    'booked' => (int) $s->booked,
                ]);

            return [
                'id' => $p->id,
                'name' => $p->name,
                'description' => $p->description ?? '',
                'color' => $p->color,
                'slot_duration_minutes' => $p->slot_duration_minutes,
                'required_per_slot' => $p->required_per_slot,
                'slots_count' => $slots->count(),
                'slots' => $slots,
            ];
        });

        return Inertia::render('Organizer/Positions/Index', [
            'event' => [
                'id' => $event->id,
                'title' => $event->title,
                'slug' => $event->slug,
                'date_start' => $event->date_start?->toDateString(),
                'date_end' => $event->date_end?->toDateString(),
            ],
            'positions' => $positions,
            'permissions' => [
                'configure' => $user->can('configure', $event),
            ],
        ]);
    }

    public function store(Request $request, Event $event)
    {
        $this->authorize('configure', $event);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'slot_duration_minutes' => ['required', 'integer', 'min:15', 'max:1440'],
            'required_per_slot' => ['required', 'integer', 'min:1', 'max:500'],
        ]);

        $position = $event->positions()->create($validated);
        $this->slotGenerator->regenerate($position);

        return redirect()->route('evenements.postes.index', $event)->with('success', __('Poste créé et créneaux générés.'));
    }

    public function update(Request $request, Event $event, Position $position)
    {
        $this->authorize('configure', $event);
        if ($position->event_id !== $event->id) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'slot_duration_minutes' => ['required', 'integer', 'min:15', 'max:1440'],
            'required_per_slot' => ['required', 'integer', 'min:1', 'max:500'],
        ]);

        $position->update($validated);

        return redirect()->route('evenements.postes.index', $event)->with(
            'success',
            __('Poste enregistré. Pour recalculer la grille horaire (durée, fenêtre), utilisez « Régénérer les créneaux ».')
        );
    }

    public function destroy(Event $event, Position $position)
    {
        $this->authorize('configure', $event);
        if ($position->event_id !== $event->id) {
            abort(404);
        }

        $position->delete();

        return redirect()->route('evenements.postes.index', $event)->with('success', __('Poste supprimé.'));
    }

    public function regenerate(Event $event, Position $position)
    {
        $this->authorize('configure', $event);
        if ($position->event_id !== $event->id) {
            abort(404);
        }

        $this->slotGenerator->regenerate($position);

        return back()->with('success', __('Créneaux régénérés.'));
    }
}
