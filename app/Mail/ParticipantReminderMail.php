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

class ParticipantReminderMail extends Mailable implements ShouldQueue
{
    use Queueable;

    /**
     * @param  Collection<int, Registration>  $registrations
     */
    public function __construct(
        public Event $event,
        public Collection $registrations,
        public int $daysBefore = 0,
        public bool $manual = false,
    ) {}

    public function envelope(): Envelope
    {
        $subject = $this->manual
            ? __('Votre planning — :title', ['title' => $this->event->title])
            : __('Rappel — événement dans :days jours — :title', [
                'days' => $this->daysBefore,
                'title' => $this->event->title,
            ]);

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            html: 'emails.participant-reminder',
            text: 'emails.participant-reminder-text',
        );
    }
}
