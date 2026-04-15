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
<body class="flex min-h-screen flex-col bg-organizer-app bg-organizer-grid font-sans text-slate-900 antialiased">
    {{-- Un seul calque décoratif (au lieu de 3) pour limiter les couches de composition au scroll --}}
    <div
        class="pointer-events-none fixed inset-0 -z-10 opacity-90"
        style="background: radial-gradient(at 40% 20%, rgb(59 130 246 / 0.14) 0px, transparent 50%), linear-gradient(to bottom right, rgb(59 130 246 / 0.04), transparent 45%, rgb(255 107 53 / 0.05)), linear-gradient(180deg, rgb(255 255 255 / 0.9) 0%, transparent 38%, rgb(241 245 249 / 0.95) 100%)"
        aria-hidden="true"
    ></div>

    <header
        class="relative z-30 border-b border-slate-200/80 bg-white pt-[env(safe-area-inset-top)] shadow-[0_1px_0_rgb(15_23_42/0.06)]"
    >
        <div
            class="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-transparent via-brand-400/35 to-ember-400/40"
            aria-hidden="true"
        ></div>
        <div class="relative mx-auto flex h-16 max-w-6xl items-center justify-between gap-3 px-4 sm:h-[4.25rem] sm:px-6">
            <a href="{{ url('/') }}" class="group flex shrink-0 items-center gap-3">
                <span
                    class="relative flex h-10 w-10 items-center justify-center rounded-2xl bg-gradient-to-br from-brand-600 via-brand-500 to-brand-700 text-white shadow-lg shadow-brand-600/30 ring-1 ring-white/25"
                >
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M8 5v14M16 5v14M5 8h4M15 8h4M5 12h4M15 12h4M5 16h4M15 16h4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    <span class="absolute -right-0.5 -top-0.5 h-2 w-2 rounded-full bg-ember-400 ring-2 ring-white" aria-hidden="true"></span>
                </span>
                <span class="flex flex-col">
                    <span class="text-base font-extrabold tracking-tight text-slate-900 group-hover:text-brand-700">{{ config('app.name') }}</span>
                    <span class="hidden text-[11px] font-semibold uppercase tracking-[0.14em] text-brand-700/85 sm:block">Bénévolat & créneaux</span>
                </span>
            </a>

            <nav class="hidden flex-1 items-center justify-center gap-1 md:flex lg:gap-2" aria-label="Navigation principale">
                <a href="{{ url('/') }}#top" class="rounded-xl px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-white/90 hover:text-slate-900 hover:ring-1 hover:ring-slate-200/80">Accueil</a>
                <a href="{{ url('/') }}#features" class="rounded-xl px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-white/90 hover:text-slate-900 hover:ring-1 hover:ring-slate-200/80">Fonctionnalités</a>
                <a href="{{ url('/') }}#stats" class="rounded-xl px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-white/90 hover:text-slate-900 hover:ring-1 hover:ring-slate-200/80">Pourquoi Organizz</a>
                <a href="{{ url('/') }}#tarifs" class="rounded-xl px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-white/90 hover:text-slate-900 hover:ring-1 hover:ring-slate-200/80">Tarifs</a>
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
                    <a
                        href="{{ route('register') }}"
                        class="rounded-2xl bg-gradient-to-r from-brand-600 to-brand-500 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-brand-600/25 transition hover:from-brand-700 hover:to-brand-600 hover:shadow-xl sm:px-5"
                        >S’inscrire gratuitement</a
                    >
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
