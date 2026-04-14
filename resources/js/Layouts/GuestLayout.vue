<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="flex min-h-screen flex-col bg-slate-50 lg:flex-row">
        <div class="relative flex flex-1 flex-col justify-between overflow-hidden bg-gradient-to-br from-brand-800 via-brand-700 to-slate-900 px-8 py-12 text-white lg:max-w-md lg:px-12 lg:py-16">
            <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(ellipse_80%_60%_at_30%_20%,rgb(45_212_191/0.25),transparent)]" />
            <div class="pointer-events-none absolute -right-20 top-20 h-64 w-64 rounded-full bg-teal-400/20 blur-3xl" />
            <div class="relative">
                <Link href="/" class="inline-flex items-center gap-3 rounded-xl text-white/90 transition hover:text-white">
                    <ApplicationLogo variant="inverse" />
                    <span class="text-sm font-bold tracking-tight">Retour à l’accueil</span>
                </Link>
                <h1 class="mt-12 text-3xl font-bold leading-tight tracking-tight lg:text-4xl">
                    Pilotez vos équipes de bénévoles en toute simplicité.
                </h1>
                <p class="mt-4 max-w-sm text-base leading-relaxed text-teal-100/90">
                    Compte organisateur : création d’événements, créneaux et suivi des inscriptions en un seul endroit.
                </p>
            </div>
            <p class="relative mt-12 text-xs text-teal-200/80 lg:mt-0">Connexion sécurisée · Données hébergées en Europe</p>
        </div>

        <div class="flex flex-1 flex-col justify-center px-4 py-10 sm:px-8 lg:px-16">
            <div class="mx-auto w-full max-w-md">
                <div class="rounded-2xl border border-slate-200/80 bg-white p-8 shadow-card sm:p-10">
                    <slot />
                </div>
            </div>
        </div>
    </div>
</template>
