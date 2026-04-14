<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Mail\RegistrationCancelledMail;
use App\Models\Registration;
use App\Models\Slot;
use App\Services\WaitlistService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class RegistrationTokenController extends Controller
{
    public function cancel(string $token): View
    {
        $registration = Registration::query()->where('token', $token)->firstOrFail();
        $registration->load('slot.position.event');

        if ($registration->cancelled_at) {
            return view('public.cancelled', [
                'event' => $registration->slot->position->event,
                'already' => true,
            ]);
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

        $registration->refresh();
        $registration->load('slot.position.event');

        Mail::to($registration->email)->queue(new RegistrationCancelledMail($registration));

        return view('public.cancelled', [
            'event' => $registration->slot->position->event,
            'already' => false,
        ]);
    }

    public function confirm(string $token): View
    {
        $registration = Registration::query()->where('token', $token)->firstOrFail();
        $registration->load('slot.position.event');

        if (! $registration->confirmed_at) {
            $registration->confirmed_at = now();
            $registration->save();
        }

        return view('public.confirmed', [
            'event' => $registration->slot->position->event,
            'registration' => $registration,
        ]);
    }
}
