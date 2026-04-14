<?php

namespace App\Services;

use App\Mail\WaitlistPromotedMail;
use App\Models\Registration;
use App\Models\Slot;
use Illuminate\Support\Facades\Mail;

class WaitlistService
{
    public function promoteNext(Slot $slot): void
    {
        $next = Registration::query()
            ->where('slot_id', $slot->id)
            ->where('waitlist', true)
            ->whereNull('cancelled_at')
            ->orderBy('waitlist_position')
            ->lockForUpdate()
            ->first();

        if (! $next) {
            return;
        }

        $next->waitlist = false;
        $next->waitlist_position = null;
        $next->save();

        Mail::to($next->email)->queue(new WaitlistPromotedMail($next));
    }
}
