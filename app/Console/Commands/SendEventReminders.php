<?php

namespace App\Console\Commands;

use App\Mail\ParticipantReminderMail;
use App\Models\Event;
use App\Models\Registration;
use App\Models\ReminderRule;
use App\Services\SmsSender;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendEventReminders extends Command
{
    protected $signature = 'events:send-reminders';

    protected $description = 'Envoie les emails de rappel selon les règles actives';

    public function handle(): int
    {
        $now = Carbon::now();
        $today = $now->toDateString();
        $hm = $now->format('H:i');

        $rules = ReminderRule::query()
            ->where('active', true)
            ->with('event.user')
            ->get();

        foreach ($rules as $rule) {
            $ruleHm = strlen((string) $rule->time_of_day) > 5
                ? substr((string) $rule->time_of_day, 0, 5)
                : (string) $rule->time_of_day;
            if ($ruleHm !== $hm) {
                continue;
            }

            /** @var Event $event */
            $event = $rule->event;
            if (! $event || $event->status !== 'open') {
                continue;
            }

            $target = Carbon::parse($event->date_start)->startOfDay()->subDays($rule->days_before)->toDateString();
            if ($target !== $today) {
                continue;
            }

            $emails = Registration::query()
                ->select('email')
                ->whereHas('slot.position', fn ($q) => $q->where('event_id', $event->id))
                ->whereNull('cancelled_at')
                ->where('waitlist', false)
                ->whereNull('reminder_sent_at')
                ->distinct()
                ->pluck('email');

            foreach ($emails as $email) {
                $regs = Registration::query()
                    ->where('email', $email)
                    ->whereHas('slot.position', fn ($q) => $q->where('event_id', $event->id))
                    ->whereNull('cancelled_at')
                    ->where('waitlist', false)
                    ->whereNull('reminder_sent_at')
                    ->with('slot.position')
                    ->get();

                if ($regs->isEmpty()) {
                    continue;
                }

                $first = $regs->first();
                $channel = $first->preferred_reminder_channel ?? 'email';

                if ($channel === 'sms' && $first->phone) {
                    $lines = $regs->map(fn (Registration $r) => $r->slot->date?->format('d/m').' '.$r->slot->position->name)->implode('; ');
                    $text = __('Rappel :title — :lines', ['title' => $event->title, 'lines' => $lines]);
                    app(SmsSender::class)->send($first->phone, $text);
                } else {
                    Mail::to($email)->queue(
                        new ParticipantReminderMail($event, $regs, $rule->days_before, false)
                    );
                }

                Registration::query()
                    ->whereIn('id', $regs->pluck('id'))
                    ->update(['reminder_sent_at' => now()]);
            }
        }

        return self::SUCCESS;
    }
}
