<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    events: Object,
});

function statusLabel(status) {
    const m = { open: 'Ouvert', closed: 'Fermé', archived: 'Archivé', draft: 'Brouillon' };
    return m[status] ?? status;
}
</script>

<template>
    <Head title="Administration — tous les événements" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <h2 class="text-2xl font-bold tracking-tight text-slate-900">Tous les événements</h2>
                <Link
                    :href="route('administration.utilisateurs.index')"
                    class="text-sm font-semibold text-brand-700 underline-offset-2 hover:underline"
                >
                    Utilisateurs
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-6xl sm:px-6 lg:px-8">
                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-slate-700">Titre</th>
                                <th class="px-4 py-3 text-left font-semibold text-slate-700">Organisateur</th>
                                <th class="px-4 py-3 text-left font-semibold text-slate-700">Dates</th>
                                <th class="px-4 py-3 text-left font-semibold text-slate-700">Statut</th>
                                <th class="px-4 py-3 text-right font-semibold text-slate-700">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="e in events.data" :key="e.id" class="bg-white">
                                <td class="px-4 py-3 font-medium text-slate-900">{{ e.title }}</td>
                                <td class="px-4 py-3 text-slate-600">
                                    <div>{{ e.owner?.name }}</div>
                                    <div class="text-xs text-slate-500">{{ e.owner?.email }}</div>
                                </td>
                                <td class="px-4 py-3 tabular-nums text-slate-700">
                                    {{ e.date_start }} → {{ e.date_end }}
                                </td>
                                <td class="px-4 py-3">{{ statusLabel(e.status) }}</td>
                                <td class="px-4 py-3 text-right">
                                    <Link
                                        :href="route('evenements.montrer', e.slug)"
                                        class="text-xs font-semibold text-brand-700 hover:underline"
                                    >
                                        Ouvrir
                                    </Link>
                                    <span class="mx-1 text-slate-300">|</span>
                                    <Link
                                        :href="route('evenements.editer', e.slug)"
                                        class="text-xs font-semibold text-slate-700 hover:underline"
                                    >
                                        Modifier
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div v-if="events.prev_page_url || events.next_page_url" class="flex gap-4 border-t border-slate-100 px-4 py-3">
                        <Link v-if="events.prev_page_url" :href="events.prev_page_url" preserve-scroll class="text-sm text-brand-700">
                            Précédent
                        </Link>
                        <Link v-if="events.next_page_url" :href="events.next_page_url" preserve-scroll class="text-sm text-brand-700">
                            Suivant
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
