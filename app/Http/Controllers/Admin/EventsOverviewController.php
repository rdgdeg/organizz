<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EventsOverviewController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $events = Event::query()
            ->with(['user:id,name,email'])
            ->orderByDesc('updated_at')
            ->paginate(40)
            ->through(fn (Event $e) => [
                'id' => $e->id,
                'slug' => $e->slug,
                'title' => $e->title,
                'status' => $e->status,
                'registration_enabled' => $e->registration_enabled,
                'date_start' => $e->date_start?->toDateString(),
                'date_end' => $e->date_end?->toDateString(),
                'owner' => [
                    'name' => $e->user?->name,
                    'email' => $e->user?->email,
                ],
                'updated_at' => $e->updated_at?->toIso8601String(),
            ]);

        return Inertia::render('Admin/Events/Index', [
            'events' => $events,
        ]);
    }
}
