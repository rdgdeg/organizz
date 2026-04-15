<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Throwable;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     *
     * Change à chaque `npm run build` → Inertia force un rechargement complet si la version ne correspond plus
     * (évite de garder un vieux bundle JS en mémoire côté client).
     */
    public function version(Request $request): ?string
    {
        $manifest = public_path('build/manifest.json');
        if (is_file($manifest)) {
            try {
                $hash = md5_file($manifest);

                return $hash !== false ? $hash : parent::version($request);
            } catch (Throwable) {
                return parent::version($request);
            }
        }

        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
            'flash' => [
                'success' => $request->session()->get('success'),
                'message' => $request->session()->get('message'),
            ],
        ];
    }
}
