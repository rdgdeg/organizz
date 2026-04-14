<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\ParticipantEmailToken;
use App\Models\Registration;
use App\Models\Slot;
use App\Services\WaitlistService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ParticipantPortalController extends Controller
{
    public function show(string $token): View
    {
        $row = ParticipantEmailToken::query()->where('token', $token)->firstOrFail();

        $registrations = Registration::query()
            ->where('email', $row->email)
            ->whereNull('cancelled_at')
            ->with(['slot.position.event'])
            ->orderByDesc('id')
            ->limit(200)
            ->get();

        return view('public.participant-portal', [
            'token' => $token,
            'email' => $row->email,
            'registrations' => $registrations,
        ]);
    }

    public function cancel(Request $request, string $token): RedirectResponse
    {
        $row = ParticipantEmailToken::query()->where('token', $token)->firstOrFail();

        $validated = $request->validate([
            'registration_id' => ['required', 'integer'],
        ]);

        $registration = Registration::query()
            ->where('id', $validated['registration_id'])
            ->where('email', $row->email)
            ->whereNull('cancelled_at')
            ->firstOrFail();

        $registration->load('slot.position.event');
        $event = $registration->slot->position->event;

        if (! $event->canParticipantEditNow()) {
            return back()->withErrors(['portal' => __('Le délai pour modifier ou annuler est dépassé.')]);
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

        return redirect()->route('participant.espace', ['token' => $token])->with('success', __('Créneau annulé.'));
    }
}
