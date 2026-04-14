<?php

namespace App\Mail;

use App\Models\Event;
use App\Models\Registration;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Support\Collection;

class OrganizerNewRegistrationMail extends Mailable implements ShouldQueue
{
    use Queueable;

    /**
     * @param  Collection<int, Registration>  $registrations
     */
    public function __construct(
        public Event $event,
        public Collection $registrations,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('Nouvelle inscription — :title', ['title' => $this->event->title]),
        );
    }

    public function content(): Content
    {
        return new Content(
            html: 'emails.organizer-new-registration',
        );
    }
}
