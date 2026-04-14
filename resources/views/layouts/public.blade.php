<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#2563eb">
    <meta name="description" content="Organizz — inscriptions bénévoles pensées pour le smartphone : lien à partager par SMS ou messagerie, page tactile, sans appli à installer.">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <title>@yield('title', config('app.name'))</title>
    <link rel="icon" href="/organizz-icon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/organizz-icon.svg">
    <link rel="manifest" href="/site.webmanifest">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/js/public.js'])
</head>
<body class="flex min-h-screen flex-col bg-[#f4f6f9] font-sans text-slate-900 antialiased">
    <div class="pointer-events-none fixed inset-0 bg-mesh opacity-60" aria-hidden="true"></div>
    <div class="pointer-events-none fixed inset-0 bg-[linear-gradient(180deg,rgb(255_255_255/0.92)_0%,transparent_35%,rgb(244_246_249/0.95)_100%)]" aria-hidden="true"></div>

    <header class="relative z-30 border-b border-slate-200/80 bg-white/85 pt-[env(safe-area-inset-top)] shadow-[0_1px_0_rgb(15_23_42/0.04)] backdrop-blur-xl">
        <div class="mx-auto flex h-16 max-w-6xl items-center justify-between gap-3 px-4 sm:h-[4.25rem] sm:px-6">
            <a href="{{ url('/') }}" class="group flex shrink-0 items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-gradient-to-br from-brand-500 to-brand-700 text-white shadow-lg shadow-brand-600/20 ring-1 ring-brand-500/20">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M8 5v14M16 5v14M5 8h4M15 8h4M5 12h4M15 12h4M5 16h4M15 16h4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </span>
                <span class="flex flex-col">
                    <span class="text-base font-extrabold tracking-tight text-slate-900 group-hover:text-brand-700">{{ config('app.name') }}</span>
                    <span class="hidden text-[11px] font-medium uppercase tracking-wider text-slate-500 sm:block">Pensé pour le GSM</span>
                </span>
            </a>

            <nav class="hidden flex-1 items-center justify-center gap-1 md:flex lg:gap-2" aria-label="Navigation principale">
                <a href="{{ url('/') }}#top" class="rounded-xl px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-900">Accueil</a>
                <a href="{{ url('/') }}#features" class="rounded-xl px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-900">Fonctionnalités</a>
                <a href="{{ url('/') }}#stats" class="rounded-xl px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-900">Pourquoi Organizz</a>
                <a href="{{ url('/') }}#tarifs" class="rounded-xl px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-900">Tarifs</a>
            </nav>

            <div class="flex items-center gap-2 sm:gap-3">
                <details class="landing-nav-mobile relative md:hidden">
                    <summary class="flex cursor-pointer items-center justify-center rounded-xl border border-slate-200 bg-white p-2.5 text-slate-600 shadow-sm transition hover:bg-slate-50" aria-label="Ouvrir le menu">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </summary>
                    <div class="absolute right-0 top-full z-50 mt-2 w-56 rounded-2xl border border-slate-100 bg-white py-2 shadow-xl shadow-slate-900/10 ring-1 ring-slate-900/5">
                        @auth
                            <a href="{{ route('dashboard') }}" class="block px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-brand-50 hover:text-brand-800">Espace organisateur</a>
                        @endauth
                        <a href="{{ url('/') }}#top" class="block px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-brand-50 hover:text-brand-800">Accueil</a>
                        <a href="{{ url('/') }}#features" class="block px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-brand-50 hover:text-brand-800">Fonctionnalités</a>
                        <a href="{{ url('/') }}#stats" class="block px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-brand-50 hover:text-brand-800">Pourquoi Organizz</a>
                        <a href="{{ url('/') }}#tarifs" class="block px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-brand-50 hover:text-brand-800">Tarifs</a>
                        @guest
                            <a href="{{ route('login') }}" class="block px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-brand-50 hover:text-brand-800">Connexion</a>
                        @endguest
                    </div>
                </details>

                @auth
                    <a href="{{ route('dashboard') }}" class="hidden rounded-xl px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-brand-50 hover:text-brand-800 sm:inline">Espace organisateur</a>
                @else
                    <a href="{{ route('login') }}" class="hidden rounded-xl px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-900 sm:inline">Connexion</a>
                    <a href="{{ route('register') }}" class="rounded-2xl bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md shadow-brand-600/25 transition hover:bg-brand-700 sm:px-5">S’inscrire gratuitement</a>
                @endauth
            </div>
        </div>
    </header>

    <main id="top" class="relative z-10 w-full flex-1 scroll-mt-20">
        @yield('content')
    </main>

    <footer class="relative z-10 border-t border-slate-200/80 bg-white py-12 pb-[max(3rem,env(safe-area-inset-bottom))]">
        <div class="mx-auto max-w-6xl px-4 text-center sm:px-6">
            <p class="text-sm font-semibold text-slate-800">{{ config('app.name') }}</p>
            <p class="mt-2 text-xs text-slate-500">
                Pensé pour le téléphone : partagez un lien (SMS, WhatsApp…), vos bénévoles s’inscrivent au doigt — créneaux et rappels au même endroit.
            </p>
        </div>
    </footer>
</body>
</html>
