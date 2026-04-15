<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    events: Array,
});

function statusLabel(status) {
    const m = { open: 'Ouvert', closed: 'Fermé', archived: 'Archivé', draft: 'Brouillon' };
    return m[status] ?? status;
}

/** Bordure supérieure de carte selon le statut */
function statusBorderClass(status) {
    const m = {
        open: 'border-t-emerald-500',
        closed: 'border-t-slate-400',
        archived: 'border-t-slate-600',
        draft: 'border-t-amber-500',
    };
    return m[status] ?? 'border-t-brand-500';
}

function statusBadgeClass(status) {
    const m = {
        open: 'bg-emerald-50 text-emerald-900 ring-emerald-200/80',
        closed: 'bg-slate-100 text-slate-800 ring-slate-200/80',
        archived: 'bg-slate-200 text-slate-800 ring-slate-300/80',
        draft: 'bg-amber-50 text-amber-950 ring-amber-200/80',
    };
    return m[status] ?? 'bg-slate-50 text-slate-800 ring-slate-200/80';
}
</script>

<template>
    <Head title="Événements" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <h2 class="text-2xl font-bold tracking-tight text-slate-900">
                    Mes événements
                </h2>
                <Link :href="route('evenements.creer')">
                    <PrimaryButton>Nouvel événement</PrimaryButton>
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-6xl sm:px-6 lg:px-8">
                <div
                    v-if="events.length"
                    class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3"
                >
                    <Link
                        v-for="e in events"
                        :key="e.id"
                        :href="route('evenements.montrer', e.slug)"
                        class="group flex min-h-[11rem] flex-col rounded-2xl border border-slate-200/90 bg-white p-5 shadow-card ring-1 ring-slate-100/80 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:ring-offset-2"
                        :class="['border-t-4', statusBorderClass(e.status)]"
                    >
                        <div class="min-w-0 flex-1">
                            <h3
                                class="line-clamp-2 text-lg font-bold leading-snug text-slate-900 transition group-hover:text-brand-800"
                            >
                                {{ e.title }}
                            </h3>
                            <p class="mt-2 text-sm text-slate-600">
                                <span class="tabular-nums">{{ e.date_start }}</span>
                                <span class="text-slate-400"> → </span>
                                <span class="tabular-nums">{{ e.date_end }}</span>
                            </p>
                            <div class="mt-3 flex flex-wrap items-center gap-2">
                                <span
                                    class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1"
                                    :class="statusBadgeClass(e.status)"
                                >
                                    {{ statusLabel(e.status) }}
                                </span>
                                <span
                                    v-if="e.status === 'open' && e.registration_enabled === false"
                                    class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-semibold text-amber-900 ring-1 ring-amber-200/80"
                                >
                                    Inscriptions désactivées
                                </span>
                            </div>
                        </div>
                        <div
                            class="mt-4 flex items-center justify-between border-t border-slate-100 pt-4 text-sm font-semibold text-brand-700"
                        >
                            <span>Tableau de bord</span>
                            <span
                                class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-brand-50 text-brand-700 ring-1 ring-brand-200/80 transition group-hover:bg-brand-100"
                                aria-hidden="true"
                            >
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </span>
                        </div>
                    </Link>
                </div>

                <div
                    v-else
                    class="rounded-2xl border border-dashed border-slate-300/90 bg-white/80 px-6 py-16 text-center shadow-inner"
                >
                    <p class="text-slate-600">
                        Aucun événement pour le moment.
                    </p>
                    <Link
                        :href="route('evenements.creer')"
                        class="mt-4 inline-flex items-center justify-center rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700"
                    >
                        Créer un événement
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
