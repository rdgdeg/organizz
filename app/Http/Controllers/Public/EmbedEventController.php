<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Response;

class EmbedEventController extends Controller
{
    public function __invoke(string $embedToken): Response
    {
        $event = Event::query()->where('embed_token', $embedToken)->firstOrFail();
        if (! $event->isPubliclyVisible()) {
            abort(404);
        }

        $positions = $event->positions()->orderBy('id')->with(['slots' => function ($q): void {
            $q->orderBy('date')->orderBy('start_time')->withCount([
                'registrations as active_count' => function ($q2): void {
                    $q2->whereNull('cancelled_at')->where('waitlist', false);
                },
            ]);
        }])->get();

        $byDay = [];
        foreach ($positions as $pos) {
            foreach ($pos->slots as $slot) {
                $d = $slot->date?->toDateString();
                if (! isset($byDay[$d])) {
                    $byDay[$d] = [];
                }
                $waitlistCount = $slot->registrations()
                    ->whereNull('cancelled_at')
                    ->where('waitlist', true)
                    ->count();
                $byDay[$d][] = [
                    'position' => $pos->name,
                    'position_id' => $pos->id,
                    'position_color' => $pos->color,
                    'slot_id' => $slot->id,
                    'start_time' => substr((string) $slot->start_time, 0, 5),
                    'end_time' => substr((string) $slot->end_time, 0, 5),
                    'max' => $slot->max_volunteers,
                    'active' => $slot->active_count,
                    'full' => $slot->active_count >= $slot->max_volunteers,
                    'waitlist_count' => $waitlistCount,
                ];
            }
        }
        ksort($byDay);

        return response()
            ->view('public.embed-event', [
                'event' => $event,
                'days' => $byDay,
                'embed' => true,
            ])
            ->header('Content-Security-Policy', 'frame-ancestors *');
    }
}
