<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Registration;
use Illuminate\Http\RedirectResponse;

class RegistrationCheckInController extends Controller
{
    public function __invoke(Event $event, Registration $registration): RedirectResponse
    {
        $this->authorize('manageRegistrations', $event);

        $registration->load('slot.position');
        if ($registration->slot->position->event_id !== $event->id) {
            abort(404);
        }

        if ($registration->waitlist || $registration->cancelled_at) {
            return back()->withErrors(['checkin' => __('Pointage impossible pour cette inscription.')]);
        }

        if ($registration->checked_in_at) {
            $registration->checked_in_at = null;
        } else {
            $registration->checked_in_at = now();
        }
        $registration->save();

        return back()->with('success', __('Pointage mis à jour.'));
    }
}
