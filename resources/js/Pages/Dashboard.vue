<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-brand-600/90">Vue d’ensemble</p>
                <h2 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">Tableau de bord</h2>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="organizer-card overflow-hidden p-8 sm:p-10">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="text-lg font-semibold text-slate-900">Bienvenue</p>
                            <p class="mt-1 text-sm text-slate-600">Vous êtes connecté à votre espace organisateur.</p>
                        </div>
                        <span
                            class="inline-flex items-center rounded-full bg-gradient-to-r from-brand-500/15 to-ember-500/20 px-4 py-2 text-sm font-semibold text-slate-800 ring-1 ring-slate-200/80"
                            >Prêt à planifier</span
                        >
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
