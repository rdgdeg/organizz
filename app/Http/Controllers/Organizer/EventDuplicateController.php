<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Services\EventDuplicatorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EventDuplicateController extends Controller
{
    public function __invoke(Request $request, Event $event, EventDuplicatorService $duplicator): RedirectResponse
    {
        $this->authorize('configure', $event);

        $validated = $request->validate([
            'date_start' => ['nullable', 'date'],
            'date_end' => ['nullable', 'date', 'after_or_equal:date_start'],
        ]);

        $copy = $duplicator->duplicate(
            $event,
            $request->user(),
            $validated['date_start'] ?? null,
            $validated['date_end'] ?? null,
        );

        return redirect()->route('events.show', $copy)->with('success', __('Événement dupliqué (brouillon).'));
    }
}
