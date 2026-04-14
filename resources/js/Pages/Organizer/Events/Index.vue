<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    events: Array,
});
</script>

<template>
    <Head title="Événements" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <h2 class="text-2xl font-bold tracking-tight text-slate-900">
                    Mes événements
                </h2>
                <Link :href="route('events.create')">
                    <PrimaryButton>Nouvel événement</PrimaryButton>
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-card">
                    <ul class="divide-y divide-slate-100">
                        <li v-for="e in events" :key="e.id" class="px-6 py-5 transition hover:bg-slate-50/80">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <Link
                                        :href="route('events.show', e.id)"
                                        class="text-lg font-semibold text-slate-900 hover:text-brand-700"
                                    >
                                        {{ e.title }}
                                    </Link>
                                    <p class="mt-1 text-sm text-slate-500">
                                        {{ e.date_start }} → {{ e.date_end }} ·
                                        <span class="capitalize">{{ e.status }}</span>
                                    </p>
                                </div>
                                <Link
                                    :href="route('events.show', e.id)"
                                    class="inline-flex items-center justify-center rounded-xl bg-brand-50 px-4 py-2 text-sm font-semibold text-brand-800 ring-1 ring-brand-200/80 transition hover:bg-brand-100"
                                >
                                    Ouvrir
                                </Link>
                            </div>
                        </li>
                        <li v-if="!events.length" class="px-6 py-16 text-center text-slate-500">
                            Aucun événement pour le moment. Créez votre premier événement pour inviter des bénévoles.
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
