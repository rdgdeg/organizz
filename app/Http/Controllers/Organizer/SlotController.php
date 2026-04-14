<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Position;
use App\Models\Slot;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SlotController extends Controller
{
    public function store(Request $request, Event $event)
    {
        $this->authorize('configure', $event);

        $validated = $request->validate([
            'position_id' => ['required', 'exists:positions,id'],
            'date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i'],
        ]);

        $position = Position::query()->findOrFail($validated['position_id']);
        if ($position->event_id !== $event->id) {
            abort(404);
        }

        $startAt = Carbon::createFromFormat('H:i', $validated['start_time']);
        $endAt = Carbon::createFromFormat('H:i', $validated['end_time']);
        if ($endAt->lte($startAt)) {
            return back()->withErrors(['end_time' => __('L’heure de fin doit être après le début.')]);
        }

        Slot::query()->create([
            'position_id' => $position->id,
            'date' => $validated['date'],
            'start_time' => $startAt->format('H:i:s'),
            'end_time' => $endAt->format('H:i:s'),
            'max_volunteers' => $position->required_per_slot,
        ]);

        return back()->with('success', __('Créneau ajouté.'));
    }

    public function destroy(Event $event, Slot $slot)
    {
        $this->authorize('configure', $event);
        $slot->load('position');
        if ($slot->position->event_id !== $event->id) {
            abort(404);
        }

        if ($slot->registrations()->whereNull('cancelled_at')->exists()) {
            return back()->withErrors(['slot' => __('Impossible de supprimer un créneau avec des inscriptions actives.')]);
        }

        $slot->delete();

        return back()->with('success', __('Créneau supprimé.'));
    }
}
