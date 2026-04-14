<?php

namespace App\Mail;

use App\Models\Registration;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Support\Collection;

class RegistrationConfirmationMail extends Mailable implements ShouldQueue
{
    use Queueable;

    /**
     * @param  Collection<int, Registration>  $registrations
     */
    public function __construct(
        public Collection $registrations,
    ) {}

    public function envelope(): Envelope
    {
        $event = $this->registrations->first()?->slot?->position?->event;

        return new Envelope(
            subject: __('Confirmation d’inscription — :title', ['title' => $event?->title ?? '']),
        );
    }

    public function content(): Content
    {
        return new Content(
            html: 'emails.registration-confirmation',
            text: 'emails.registration-confirmation-text',
        );
    }
}
