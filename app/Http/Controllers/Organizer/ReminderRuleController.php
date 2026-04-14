<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Mail\ReminderTestMail;
use App\Models\Event;
use App\Models\ReminderRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response;

class ReminderRuleController extends Controller
{
    public function index(Event $event): Response
    {
        $this->authorize('view', $event);

        $rules = $event->reminderRules()->orderBy('id')->get()->map(fn (ReminderRule $r) => [
            'id' => $r->id,
            'days_before' => $r->days_before,
            'time_of_day' => substr((string) $r->time_of_day, 0, 5),
            'active' => $r->active,
        ]);

        return Inertia::render('Organizer/Reminders/Index', [
            'event' => [
                'id' => $event->id,
                'slug' => $event->slug,
                'title' => $event->title,
                'date_start' => $event->date_start?->toDateString(),
            ],
            'rules' => $rules,
        ]);
    }

    public function store(Request $request, Event $event)
    {
        $this->authorize('configure', $event);

        $validated = $request->validate([
            'days_before' => ['required', 'integer', 'min:0', 'max:365'],
            'time_of_day' => ['required', 'regex:/^\d{2}:\d{2}$/'],
            'active' => ['boolean'],
        ]);

        $event->reminderRules()->create([
            'days_before' => $validated['days_before'],
            'time_of_day' => $validated['time_of_day'],
            'active' => (bool) ($validated['active'] ?? true),
        ]);

        return redirect()->route('evenements.rappels.index', $event)->with('success', __('Règle créée.'));
    }

    public function update(Request $request, Event $event, ReminderRule $reminderRule)
    {
        $this->authorize('configure', $event);
        if ($reminderRule->event_id !== $event->id) {
            abort(404);
        }

        $validated = $request->validate([
            'days_before' => ['required', 'integer', 'min:0', 'max:365'],
            'time_of_day' => ['required', 'regex:/^\d{2}:\d{2}$/'],
            'active' => ['boolean'],
        ]);

        $reminderRule->update([
            'days_before' => $validated['days_before'],
            'time_of_day' => $validated['time_of_day'],
            'active' => (bool) ($validated['active'] ?? false),
        ]);

        return redirect()->route('evenements.rappels.index', $event)->with('success', __('Règle mise à jour.'));
    }

    public function destroy(Event $event, ReminderRule $reminderRule)
    {
        $this->authorize('configure', $event);
        if ($reminderRule->event_id !== $event->id) {
            abort(404);
        }

        $reminderRule->delete();

        return redirect()->route('evenements.rappels.index', $event)->with('success', __('Règle supprimée.'));
    }

    public function testEmail(Request $request, Event $event)
    {
        $this->authorize('configure', $event);

        Mail::to($request->user()->email)->queue(new ReminderTestMail($event));

        return back()->with('success', __('Email de test envoyé.'));
    }
}
