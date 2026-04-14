@extends('layouts.public')

@section('title', 'Organizz — Bénévolat au téléphone, sans friction')

@section('content')
    {{-- Hero — style SaaS clair, maquettes flottantes --}}
    <section class="relative overflow-hidden pt-10 pb-16 sm:pt-14 lg:pb-24" aria-labelledby="hero-heading">
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(ellipse_80%_50%_at_50%_-20%,rgb(59_130_246/0.12),transparent)]" aria-hidden="true"></div>
        <div class="pointer-events-none absolute right-0 top-24 h-72 w-72 rounded-full bg-brand-200/30 blur-3xl" aria-hidden="true"></div>
        <div class="pointer-events-none absolute -left-20 bottom-0 h-64 w-64 rounded-full bg-indigo-200/25 blur-3xl" aria-hidden="true"></div>

        <div class="relative mx-auto grid max-w-6xl items-center gap-12 px-4 sm:px-6 lg:grid-cols-2 lg:gap-10 lg:pt-4">
            <div class="max-w-xl">
                <p class="inline-flex items-center gap-2 rounded-full border border-brand-200/80 bg-white/90 px-4 py-1.5 text-xs font-semibold uppercase tracking-wider text-brand-800 shadow-sm">
                    <span class="relative flex h-2 w-2">
                        <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-brand-400 opacity-75"></span>
                        <span class="relative inline-flex h-2 w-2 rounded-full bg-brand-500"></span>
                    </span>
                    Optimisé GSM · Essai gratuit
                </p>
                <h1 id="hero-heading" class="mt-6 text-4xl font-extrabold tracking-tight text-slate-900 sm:text-5xl lg:text-[3.25rem] lg:leading-[1.1]">
                    Vos bénévoles s’inscrivent
                    <span class="bg-gradient-to-r from-brand-600 to-indigo-600 bg-clip-text text-transparent">depuis leur téléphone.</span>
                </h1>
                <p class="mt-5 text-lg leading-relaxed text-slate-600 sm:text-xl">
                    Un lien à envoyer par <strong class="font-semibold text-slate-800">SMS</strong>, <strong class="font-semibold text-slate-800">WhatsApp</strong> ou affiche : vos équipes choisissent leurs créneaux sur une page <strong class="font-semibold text-slate-800">tactile</strong>, en 4G, sans appli à installer. Vous pilotez tout depuis le web.
                </p>
                <div class="mt-9 flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-center">
                    <a href="{{ route('register') }}" class="landing-btn-primary w-full justify-center sm:w-auto">
                        Commencer gratuitement
                    </a>
                    <a href="#features" class="landing-btn-ghost w-full justify-center sm:w-auto">
                        <svg class="h-5 w-5 text-brand-600" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M8 5v14l11-7z"/></svg>
                        Voir l’expérience mobile
                    </a>
                </div>
                <p class="mt-6 text-sm text-slate-500">Grandes zones tactiles, formulaire lisible au soleil, raccourci « Ajouter à l’écran d’accueil » sur iPhone &amp; Android.</p>
            </div>

            {{-- Maquettes flottantes --}}
            <div class="relative mx-auto min-h-[400px] w-full max-w-lg lg:mx-0 lg:max-w-none lg:min-h-[440px]">
                <div class="absolute left-4 top-8 z-10 w-[min(100%,280px)] animate-float landing-mock-card p-4 sm:left-0">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <span class="text-xs font-semibold uppercase tracking-wide text-slate-400">Vue d’ensemble</span>
                        <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-semibold text-emerald-700">+12 %</span>
                    </div>
                    <p class="mt-3 text-sm font-bold text-slate-800">Remplissage des créneaux</p>
                    <div class="mt-4 flex h-28 items-end justify-between gap-1.5 px-1">
                        @foreach ([35, 55, 40, 70, 45, 85, 65, 90, 75] as $h)
                            <span class="w-full rounded-t-md bg-gradient-to-t from-brand-600 to-brand-400" style="height: {{ $h }}%; opacity: {{ 0.65 + ($loop->index % 4) * 0.08 }}"></span>
                        @endforeach
                    </div>
                    <div class="mt-3 flex justify-between text-[10px] font-medium text-slate-400">
                        <span>Lun</span><span>Mar</span><span>Mer</span><span>Jeu</span><span>Ven</span><span>Sam</span><span>Dim</span>
                    </div>
                </div>

                <div class="absolute right-0 top-0 z-20 w-[min(100%,300px)] animate-float-delayed landing-mock-card p-4 sm:right-2 lg:right-0">
                    <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-brand-100 text-xs font-bold text-brand-700">O</span>
                        <div>
                            <p class="text-sm font-semibold text-slate-800">Festival été 2026</p>
                            <p class="text-xs text-slate-500">12 postes · 240 créneaux</p>
                        </div>
                    </div>
                    <ul class="mt-3 space-y-2">
                        <li class="flex items-center justify-between rounded-xl bg-slate-50 px-3 py-2 text-xs">
                            <span class="font-medium text-slate-700">Accueil · 9h–12h</span>
                            <span class="rounded-md bg-white px-2 py-0.5 font-semibold text-brand-600 shadow-sm">3/4</span>
                        </li>
                        <li class="flex items-center justify-between rounded-xl bg-slate-50 px-3 py-2 text-xs">
                            <span class="font-medium text-slate-700">Bar · 18h–23h</span>
                            <span class="rounded-md bg-white px-2 py-0.5 font-semibold text-emerald-600 shadow-sm">Complet</span>
                        </li>
                        <li class="flex items-center justify-between rounded-xl border border-dashed border-brand-200/80 bg-brand-50/50 px-3 py-2 text-xs">
                            <span class="font-medium text-slate-700">Logistique · 14h–18h</span>
                            <span class="text-brand-600 font-semibold">Inscrire</span>
                        </li>
                    </ul>
                </div>

                <div class="absolute bottom-4 left-1/2 z-30 w-[min(100%,320px)] -translate-x-1/2 landing-mock-card p-4 sm:bottom-8 sm:left-auto sm:right-8 sm:translate-x-0">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Inscriptions</p>
                            <p class="mt-1 text-2xl font-bold text-slate-900">186</p>
                            <p class="text-xs text-emerald-600">+28 cette semaine</p>
                        </div>
                        <div class="rounded-xl bg-brand-50 p-2">
                            <svg class="h-8 w-8 text-brand-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Preuve sociale / logos --}}
    <section class="border-y border-slate-200/80 bg-white/70 py-10 backdrop-blur-sm">
        <div class="mx-auto max-w-6xl px-4 sm:px-6">
            <p class="text-center text-sm font-medium text-slate-500">Les bénévoles ouvrent le lien sur leur téléphone — inscription en deux minutes, où qu’ils soient</p>
            <div class="mt-8 flex flex-wrap items-center justify-center gap-3 sm:gap-4">
                @foreach (['Collectif culturel', 'Marathon local', 'Foyer étudiant', 'Festival jazz', 'Solidarité quartier'] as $name)
                    <div class="flex items-center gap-2 rounded-full border border-slate-200/90 bg-white px-5 py-2.5 text-sm font-semibold text-slate-600 shadow-sm">
                        <span class="flex h-7 w-7 items-center justify-center rounded-full bg-slate-100 text-xs font-bold text-slate-500">{{ mb_substr($name, 0, 1) }}</span>
                        {{ $name }}
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Stats + carte analytics --}}
    <section id="stats" class="scroll-mt-24 py-20 lg:py-28">
        <div class="mx-auto max-w-6xl px-4 sm:px-6">
            <div class="grid gap-14 lg:grid-cols-2 lg:items-center lg:gap-16">
                <div>
                    <p class="text-xs font-bold uppercase tracking-widest text-brand-600">Téléphone d’abord</p>
                    <h2 class="mt-3 text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">Pensé pour le creux de la main</h2>
                    <p class="mt-4 text-lg text-slate-600">
                        La page d’inscription est la même sur un petit écran qu’au bureau : listes dépliables, cases faciles à toucher, numéro de portable au bon format. Vous envoyez le lien ; eux confirment depuis le GSM.
                    </p>
                    <div class="mt-10 grid grid-cols-2 gap-6 sm:gap-8">
                        <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-card">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-100 text-brand-700">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            </div>
                            <p class="mt-4 text-3xl font-bold text-slate-900">1</p>
                            <p class="text-sm font-medium text-slate-500">Lien à coller dans un SMS</p>
                        </div>
                        <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-card">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-100 text-indigo-700">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            </div>
                            <p class="mt-4 text-3xl font-bold text-slate-900">4G</p>
                            <p class="text-sm font-medium text-slate-500">Fluide même en extérieur</p>
                        </div>
                    </div>
                </div>
                <div class="relative">
                    <div class="landing-mock-card relative overflow-hidden p-6 sm:p-8">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-semibold text-slate-800">Progression des inscriptions</span>
                            <span class="rounded-full bg-brand-50 px-3 py-1 text-xs font-semibold text-brand-700">Temps réel</span>
                        </div>
                        <p class="mt-1 text-4xl font-bold text-slate-900">94<span class="text-2xl text-slate-400">%</span></p>
                        <p class="text-sm text-slate-500">Objectif de remplissage — événement pilote</p>
                        <div class="mt-8 flex h-40 items-end justify-between gap-2">
                            @foreach ([20, 35, 28, 50, 45, 62, 58, 78, 85, 92, 88, 94] as $h)
                                <span class="w-full rounded-t-lg bg-gradient-to-t from-brand-600/90 to-brand-300/80" style="height: {{ $h }}%"></span>
                            @endforeach
                        </div>
                        <div class="mt-3 flex justify-between text-xs font-medium text-slate-400">
                            <span>Jan</span><span>Fév</span><span>Mar</span><span>Avr</span><span>Mai</span><span>Juin</span>
                        </div>
                    </div>
                    <div class="absolute -right-4 -bottom-4 -z-10 h-40 w-40 rounded-full bg-brand-200/40 blur-2xl" aria-hidden="true"></div>
                </div>
            </div>
        </div>
    </section>

    {{-- Grille fonctionnalités + listes --}}
    <section id="features" class="scroll-mt-24 border-t border-slate-200/80 bg-white py-20 lg:py-28">
        <div class="mx-auto max-w-6xl px-4 sm:px-6">
            <div class="mx-auto max-w-2xl text-center">
                <p class="text-xs font-bold uppercase tracking-widest text-brand-600">Produit</p>
                <h2 class="mt-3 text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">Du terrain au GSM, sans friction</h2>
                <p class="mt-4 text-lg text-slate-600">
                    Les organisateurs travaillent sur ordinateur ; les bénévoles vivent sur téléphone — Organizz relie les deux.
                </p>
            </div>
            <div class="mt-16 grid gap-8 lg:grid-cols-3">
                <article class="rounded-3xl border border-slate-100 bg-[#fafbfc] p-8 shadow-card transition hover:border-brand-100 hover:shadow-lg">
                    <div class="flex h-14 w-14 items-center justify-center rounded-full bg-white shadow-md ring-1 ring-slate-100">
                        <svg class="h-7 w-7 text-brand-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <h3 class="mt-6 text-xl font-bold text-slate-900">Créneaux intelligents</h3>
                    <p class="mt-2 text-slate-600">Génération automatique selon vos dates et la durée de chaque poste.</p>
                    <ul class="mt-6 space-y-3">
                        <li class="landing-check"><svg class="mt-0.5 h-5 w-5 shrink-0 text-brand-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Événements sur plusieurs jours</li>
                        <li class="landing-check"><svg class="mt-0.5 h-5 w-5 shrink-0 text-brand-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Fenêtre horaire par jour</li>
                        <li class="landing-check"><svg class="mt-0.5 h-5 w-5 shrink-0 text-brand-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Lecture confortable sur petit écran</li>
                    </ul>
                </article>
                <article class="rounded-3xl border border-slate-100 bg-[#fafbfc] p-8 shadow-card transition hover:border-brand-100 hover:shadow-lg">
                    <div class="flex h-14 w-14 items-center justify-center rounded-full bg-white shadow-md ring-1 ring-slate-100">
                        <svg class="h-7 w-7 text-brand-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                    </div>
                    <h3 class="mt-6 text-xl font-bold text-slate-900">Lien à partager partout</h3>
                    <p class="mt-2 text-slate-600">Une URL courte à coller en SMS, groupe WhatsApp ou QR code — ouverture directe sur le navigateur du téléphone.</p>
                    <ul class="mt-6 space-y-3">
                        <li class="landing-check"><svg class="mt-0.5 h-5 w-5 shrink-0 text-brand-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> URL unique par événement</li>
                        <li class="landing-check"><svg class="mt-0.5 h-5 w-5 shrink-0 text-brand-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Places restantes en direct</li>
                        <li class="landing-check"><svg class="mt-0.5 h-5 w-5 shrink-0 text-brand-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Confirmation par e-mail (et rappels)</li>
                    </ul>
                </article>
                <article class="rounded-3xl border border-slate-100 bg-[#fafbfc] p-8 shadow-card transition hover:border-brand-100 hover:shadow-lg">
                    <div class="flex h-14 w-14 items-center justify-center rounded-full bg-white shadow-md ring-1 ring-slate-100">
                        <svg class="h-7 w-7 text-brand-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <h3 class="mt-6 text-xl font-bold text-slate-900">Emails & rappels</h3>
                    <p class="mt-2 text-slate-600">Le bon message au bon moment : confirmations, annulations, rappels.</p>
                    <ul class="mt-6 space-y-3">
                        <li class="landing-check"><svg class="mt-0.5 h-5 w-5 shrink-0 text-brand-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Messages personnalisables</li>
                        <li class="landing-check"><svg class="mt-0.5 h-5 w-5 shrink-0 text-brand-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Règles de rappel par événement</li>
                        <li class="landing-check"><svg class="mt-0.5 h-5 w-5 shrink-0 text-brand-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> File d’envoi fiable</li>
                    </ul>
                </article>
            </div>
        </div>
    </section>

    {{-- Étapes --}}
    <section class="border-t border-slate-200/80 bg-[#f4f6f9] py-20 lg:py-24">
        <div class="mx-auto max-w-6xl px-4 sm:px-6">
            <h2 class="text-center text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">Comment ça marche</h2>
            <div class="mt-14 grid gap-10 md:grid-cols-3">
                <div class="rounded-3xl border border-white bg-white p-8 text-center shadow-card">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-brand-600 text-xl font-bold text-white shadow-lg shadow-brand-600/30">1</div>
                    <h3 class="mt-5 text-lg font-bold text-slate-900">Créez l’événement</h3>
                    <p class="mt-2 text-slate-600">Dates, description et fenêtre horaire pour générer les créneaux.</p>
                </div>
                <div class="rounded-3xl border border-white bg-white p-8 text-center shadow-card">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-brand-600 text-xl font-bold text-white shadow-lg shadow-brand-600/30">2</div>
                    <h3 class="mt-5 text-lg font-bold text-slate-900">Ajoutez vos postes</h3>
                    <p class="mt-2 text-slate-600">Bar, accueil, logistique… durée et effectifs par créneau.</p>
                </div>
                <div class="rounded-3xl border border-white bg-white p-8 text-center shadow-card">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-brand-600 text-xl font-bold text-white shadow-lg shadow-brand-600/30">3</div>
                    <h3 class="mt-5 text-lg font-bold text-slate-900">Diffusez le lien (SMS, réseaux…)</h3>
                    <p class="mt-2 text-slate-600">Les bénévoles s’inscrivent depuis leur GSM ; vous suivez le remplissage en temps réel.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Tarifs (aperçu) --}}
    <section id="tarifs" class="scroll-mt-24 border-t border-slate-200/80 bg-white py-20 lg:py-28">
        <div class="mx-auto max-w-6xl px-4 sm:px-6">
            <div class="mx-auto max-w-2xl text-center">
                <p class="text-xs font-bold uppercase tracking-widest text-brand-600">Tarifs</p>
                <h2 class="mt-3 text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">Commencez gratuitement</h2>
                <p class="mt-4 text-lg text-slate-600">Passez à une offre supérieure quand vous avez besoin d’export CSV ou de quotas étendus.</p>
            </div>
            <div class="mx-auto mt-12 grid max-w-3xl gap-8 md:grid-cols-2">
                <div class="rounded-3xl border border-slate-200 bg-[#fafbfc] p-8 shadow-card">
                    <p class="text-sm font-semibold text-slate-500">Gratuit</p>
                    <p class="mt-2 text-4xl font-bold text-slate-900">0 €</p>
                    <p class="mt-4 text-sm text-slate-600">Pour tester Organizz sur vos premiers événements.</p>
                    <ul class="mt-6 space-y-2 text-sm text-slate-600">
                        <li class="landing-check"><svg class="mt-0.5 h-5 w-5 shrink-0 text-brand-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Événements & inscriptions</li>
                        <li class="landing-check"><svg class="mt-0.5 h-5 w-5 shrink-0 text-brand-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Emails de confirmation</li>
                    </ul>
                    <a href="{{ route('register') }}" class="landing-btn-primary mt-8 w-full justify-center">Créer un compte</a>
                </div>
                <div class="rounded-3xl border-2 border-brand-200 bg-gradient-to-b from-brand-50/80 to-white p-8 shadow-lg shadow-brand-900/5">
                    <p class="text-sm font-semibold text-brand-800">Organisateur actif</p>
                    <p class="mt-2 text-4xl font-bold text-slate-900">Sur mesure</p>
                    <p class="mt-4 text-sm text-slate-600">Exports, limites relevées et rappels avancés selon votre plan.</p>
                    <ul class="mt-6 space-y-2 text-sm text-slate-600">
                        <li class="landing-check"><svg class="mt-0.5 h-5 w-5 shrink-0 text-brand-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Export CSV des inscriptions</li>
                        <li class="landing-check"><svg class="mt-0.5 h-5 w-5 shrink-0 text-brand-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Quotas adaptés à votre usage</li>
                    </ul>
                    @auth
                        <a href="{{ route('upgrade') }}" class="landing-btn-primary mt-8 w-full justify-center">Voir mon plan</a>
                    @else
                        <a href="{{ route('register') }}" class="landing-btn-primary mt-8 w-full justify-center">S’inscrire pour débloquer</a>
                    @endauth
                </div>
            </div>
        </div>
    </section>

    {{-- CTA final --}}
    <section class="relative overflow-hidden bg-gradient-to-br from-brand-700 via-brand-800 to-slate-900 px-4 py-20 sm:px-6">
        <div class="pointer-events-none absolute inset-0 opacity-30" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.06\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')" aria-hidden="true"></div>
        <div class="relative mx-auto max-w-3xl text-center">
            <h2 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">Prêt à remplir vos créneaux depuis la poche ?</h2>
            <p class="mt-4 text-lg text-blue-100/90">
                Créez votre événement, envoyez le lien par SMS à votre équipe : la première inscription mobile ne prend que quelques minutes.
            </p>
            <div class="mt-10 flex flex-col items-center justify-center gap-4 sm:flex-row">
                <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-2xl bg-white px-8 py-3.5 text-sm font-bold text-brand-700 shadow-xl transition hover:bg-blue-50">
                    Commencer gratuitement
                </a>
                <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-2xl border border-white/30 bg-white/10 px-8 py-3.5 text-sm font-semibold text-white backdrop-blur-sm transition hover:bg-white/20">
                    J’ai déjà un compte
                </a>
            </div>
        </div>
    </section>
@endsection
