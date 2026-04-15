<?php

namespace App\Services;

use App\Models\Position;
use App\Models\Slot;
use Carbon\Carbon;

class SlotGeneratorService
{
    /**
     * Supprime les créneaux sans inscription active puis régénère la grille.
     */
    public function regenerate(Position $position): int
    {
        $event = $position->event;
        $duration = max(1, (int) $position->slot_duration_minutes);
        $maxVolunteers = max(1, (int) $position->required_per_slot);

        $position->slots()->whereDoesntHave('registrations', function ($q): void {
            $q->whereNull('cancelled_at');
        })->delete();

        $created = 0;
        $startDate = Carbon::parse($event->date_start)->startOfDay();
        $endDate = Carbon::parse($event->date_end)->startOfDay();

        $daySchedules = $event->day_schedules;
        $usePerDay = is_array($daySchedules) && count($daySchedules) > 0;

        for ($d = $startDate->copy(); $d->lte($endDate); $d->addDay()) {
            $dayStr = $d->toDateString();

            if ($usePerDay) {
                $entry = collect($daySchedules)->firstWhere('date', $dayStr);
                if (! $entry || empty($entry['enabled'])) {
                    continue;
                }
                $ws = (string) ($entry['window_start'] ?? '08:00');
                $we = (string) ($entry['window_end'] ?? '20:00');
                $wsStr = strlen($ws) <= 5 ? $ws.':00' : $ws;
                $weStr = strlen($we) <= 5 ? $we.':00' : $we;
                $windowStart = $this->timeToMinutes($wsStr);
                $windowEnd = $this->timeToMinutes($weStr);
            } else {
                $windowStart = $this->timeToMinutes((string) ($event->daily_window_start ?? '08:00:00'));
                $windowEnd = $this->timeToMinutes((string) ($event->daily_window_end ?? '20:00:00'));
            }

            if ($windowEnd <= $windowStart) {
                continue;
            }

            $cursor = $windowStart;
            while ($cursor + $duration <= $windowEnd) {
                Slot::query()->create([
                    'position_id' => $position->id,
                    'date' => $dayStr,
                    'start_time' => $this->minutesToTime($cursor),
                    'end_time' => $this->minutesToTime($cursor + $duration),
                    'max_volunteers' => $maxVolunteers,
                ]);
                $created++;
                $cursor += $duration;
            }
        }

        return $created;
    }

    private function timeToMinutes(string $time): int
    {
        $time = trim($time);
        if (strlen($time) >= 8) {
            $time = substr($time, 0, 8);
        }
        $parts = explode(':', $time);
        $h = (int) ($parts[0] ?? 0);
        $m = (int) ($parts[1] ?? 0);
        $s = (int) ($parts[2] ?? 0);

        return $h * 60 + $m + (int) round($s / 60);
    }

    private function minutesToTime(int $minutes): string
    {
        $h = intdiv($minutes, 60) % 24;
        $m = $minutes % 60;

        return sprintf('%02d:%02d:00', $h, $m);
    }
}
