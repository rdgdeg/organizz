<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Position;
use App\Models\Registration;
use App\Models\Slot;
use App\Models\User;
use Illuminate\View\View;

class LandingController extends Controller
{
    public function __invoke(): View
    {
        return view('public.landing', [
            'stats' => $this->platformStats(),
        ]);
    }

    /**
     * @return array{
     *   events_open:int,
     *   organizers:int,
     *   slots:int,
     *   capacity:int,
     *   registrations:int,
     *   spots_open:int,
     *   waitlist:int,
     *   fill_percent:int,
     *   positions:int
     * }
     */
    private function platformStats(): array
    {
        $capacity = (int) Slot::query()->sum('max_volunteers');
        $registrations = (int) Registration::query()->confirmedBooking()->count();
        $spotsOpen = max(0, $capacity - $registrations);

        return [
            'events_open' => (int) Event::query()->where('status', 'open')->count(),
            'organizers' => (int) User::query()->count(),
            'slots' => (int) Slot::query()->count(),
            'positions' => (int) Position::query()->count(),
            'capacity' => $capacity,
            'registrations' => $registrations,
            'spots_open' => $spotsOpen,
            'waitlist' => (int) Registration::query()
                ->where('waitlist', true)
                ->whereNull('cancelled_at')
                ->count(),
            'fill_percent' => $capacity > 0 ? (int) round(100 * $registrations / $capacity) : 0,
        ];
    }
}
