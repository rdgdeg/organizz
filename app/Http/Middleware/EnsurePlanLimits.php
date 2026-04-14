<?php

namespace App\Http\Middleware;

use App\Models\Event;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class EnsurePlanLimits
{
    /**
     * @param  string  $type  create_event|create_position|create_reminder|export_csv
     */
    public function handle(Request $request, Closure $next, string $type): Response
    {
        $user = $request->user();
        if (! $user) {
            abort(403);
        }

        if ($user->plan === 'pro') {
            return $next($request);
        }

        $limits = config('plans.free');

        if ($type === 'create_event') {
            $active = $user->events()->where('status', '!=', 'archived')->count();
            if ($active >= $limits['max_active_events']) {
                return redirect()->route('upgrade')->with('message', __('Limite du plan gratuit atteinte pour les événements actifs.'));
            }
        }

        if ($type === 'create_position' || $type === 'create_reminder') {
            $event = $request->route('event');
            if (! $event instanceof Event) {
                $event = Event::query()->findOrFail($request->route('event'));
            }
            if (! Gate::forUser($user)->allows('configure', $event)) {
                abort(403);
            }
            if ($type === 'create_position') {
                if ($event->positions()->count() >= $limits['max_positions_per_event']) {
                    return redirect()->route('upgrade')->with('message', __('Limite du plan gratuit atteinte pour les postes par événement.'));
                }
            }
            if ($type === 'create_reminder') {
                if ($event->reminderRules()->count() >= $limits['max_reminder_rules_per_event']) {
                    return redirect()->route('upgrade')->with('message', __('Limite du plan gratuit atteinte pour les règles de rappel.'));
                }
            }
        }

        if ($type === 'export_csv') {
            if (! $limits['csv_export']) {
                return redirect()->route('upgrade')->with('message', __('L’export CSV est réservé au plan Pro.'));
            }
        }

        return $next($request);
    }
}
