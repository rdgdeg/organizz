<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Position;
use App\Models\ReminderRule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;

class EventDuplicatorService
{
    public function duplicate(Event $source, User $owner, ?string $dateStart = null, ?string $dateEnd = null): Event
    {
        $start = $dateStart ? Carbon::parse($dateStart)->startOfDay() : $source->date_start->copy();
        $end = $dateEnd ? Carbon::parse($dateEnd)->startOfDay() : $source->date_end->copy();

        $shiftDays = (int) $source->date_start->copy()->startOfDay()->diffInDays($start->copy()->startOfDay(), false);

        $slug = $this->uniqueSlug($source->title.' copie');
        $token = $this->uniquePublicToken();
        $embed = $this->uniqueEmbedToken();

        $event = $owner->events()->create([
            'title' => $source->title.' (copie)',
            'description' => $source->description,
            'additional_info' => $source->additional_info,
            'recommendations' => $source->recommendations,
            'practical_details' => $source->practical_details,
            'slug' => $slug,
            'date_start' => $start,
            'date_end' => $end,
            'daily_window_start' => $source->daily_window_start,
            'daily_window_end' => $source->daily_window_end,
            'day_schedules' => $source->day_schedules,
            'status' => 'open',
            'registration_enabled' => true,
            'public_link_token' => $token,
            'notify_organizer_on_registration' => $source->notify_organizer_on_registration,
            'custom_fields' => $source->custom_fields,
            'waitlist_enabled' => $source->waitlist_enabled,
            'participant_edit_deadline_hours' => $source->participant_edit_deadline_hours,
            'matching_enabled' => $source->matching_enabled,
            'embed_token' => $embed,
        ]);

        foreach ($source->positions()->orderBy('id')->get() as $pos) {
            /** @var Position $newPos */
            $newPos = $event->positions()->create([
                'name' => $pos->name,
                'description' => $pos->description,
                'color' => $pos->color,
                'slot_duration_minutes' => $pos->slot_duration_minutes,
                'required_per_slot' => $pos->required_per_slot,
            ]);

            foreach ($pos->slots()->orderBy('date')->orderBy('start_time')->get() as $slot) {
                $newDate = $slot->date->copy()->addDays($shiftDays);
                $newPos->slots()->create([
                    'date' => $newDate,
                    'start_time' => $slot->start_time,
                    'end_time' => $slot->end_time,
                    'max_volunteers' => $slot->max_volunteers,
                    'sort_order' => $slot->sort_order,
                ]);
            }
        }

        foreach ($source->reminderRules()->orderBy('id')->get() as $rule) {
            ReminderRule::query()->create([
                'event_id' => $event->id,
                'days_before' => $rule->days_before,
                'time_of_day' => $rule->time_of_day,
                'active' => $rule->active,
            ]);
        }

        return $event;
    }

    private function uniqueSlug(string $title): string
    {
        $base = Str::slug($title);
        if ($base === '') {
            $base = 'evenement';
        }
        $slug = $base;
        $i = 0;
        while (Event::query()->where('slug', $slug)->exists()) {
            $i++;
            $slug = $base.'-'.$i;
        }

        return $slug;
    }

    private function uniquePublicToken(): string
    {
        do {
            $token = Str::random(48);
        } while (Event::query()->where('public_link_token', $token)->exists());

        return $token;
    }

    private function uniqueEmbedToken(): string
    {
        do {
            $token = Str::random(48);
        } while (Event::query()->where('embed_token', $token)->exists());

        return $token;
    }
}
