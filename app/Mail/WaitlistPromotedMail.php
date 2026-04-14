<?php

namespace App\Mail;

use App\Models\Registration;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class WaitlistPromotedMail extends Mailable implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Registration $registration,
    ) {}

    public function envelope(): Envelope
    {
        $event = $this->registration->slot?->position?->event;

        return new Envelope(
            subject: __('Une place s’est libérée — :title', ['title' => $event?->title ?? '']),
        );
    }

    public function content(): Content
    {
        return new Content(
            html: 'emails.waitlist-promoted',
            text: 'emails.waitlist-promoted-text',
        );
    }
}
