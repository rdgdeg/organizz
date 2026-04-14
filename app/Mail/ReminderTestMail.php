<?php

namespace App\Mail;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class ReminderTestMail extends Mailable implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Event $event,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('Test de rappel — :title', ['title' => $this->event->title]),
        );
    }

    public function content(): Content
    {
        return new Content(
            html: 'emails.reminder-test',
        );
    }
}
