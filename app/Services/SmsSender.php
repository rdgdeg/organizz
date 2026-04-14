<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsSender
{
    /** Envoie un SMS via l’API Twilio si TWILIO_* est configuré ; sinon journalise seulement. */
    public function send(string $e164Phone, string $body): bool
    {
        $sid = config('services.twilio.sid');
        $token = config('services.twilio.token');
        $from = config('services.twilio.from');

        if (! $sid || ! $token || ! $from) {
            Log::debug('SMS non envoyé (Twilio non configuré).', ['to' => $e164Phone]);

            return false;
        }

        $url = 'https://api.twilio.com/2010-04-01/Accounts/'.$sid.'/Messages.json';

        $response = Http::withBasicAuth($sid, $token)->asForm()->post($url, [
            'From' => $from,
            'To' => $e164Phone,
            'Body' => $body,
        ]);

        if ($response->failed()) {
            Log::warning('Échec envoi SMS Twilio.', ['status' => $response->status(), 'body' => $response->body()]);

            return false;
        }

        return true;
    }
}
