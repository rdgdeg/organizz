<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    event: Object,
    filters: Object,
    positions: Array,
    registrations: Array,
});

const positionId = ref(props.filters.position_id || '');
const day = ref(props.filters.day || '');
const status = ref(props.filters.status || 'active');

function applyFilters() {
    router.get(
        route('evenements.inscriptions.index', props.event.slug),
        {
            position_id: positionId.value || undefined,
            day: day.value || undefined,
            status: status.value,
        },
        { preserveState: true, replace: true },
    );
}

function cancelReg(id) {
    if (!confirm('Annuler cette inscription ?')) return;
    router.delete(route('evenements.inscriptions.supprimer', [props.event.slug, id]));
}

function recap(id) {
    router.post(route('evenements.inscriptions.recap', [props.event.slug, id]));
}

function toggleCheckin(id) {
    router.post(route('evenements.inscriptions.presence', [props.event.slug, id]), {}, { preserveScroll: true });
}
</script>

<template>
    <Head :title="`Inscriptions — ${event.title}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col gap-2">
                <h2 class="text-xl font-semibold text-gray-800">Inscriptions — {{ event.title }}</h2>
                <Link
                    :href="route('evenements.montrer', event.slug)"
                    class="text-sm text-brand-700 hover:underline"
                >
                    ← Retour événement
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-6xl sm:px-6 lg:px-8">
                <div class="mb-6 flex flex-wrap gap-4 rounded-lg bg-white p-4 shadow">
                    <div>
                        <label class="text-xs text-gray-500">Poste</label>
                        <select
                            v-model="positionId"
                            class="mt-1 block rounded-md border-gray-300"
                            @change="applyFilters"
                        >
                            <option value="">Tous</option>
                            <option v-for="p in positions" :key="p.id" :value="p.id">
                                {{ p.name }}
                            </option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">Jour</label>
                        <input
                            v-model="day"
                            type="date"
                            class="mt-1 block rounded-md border-gray-300"
                            @change="applyFilters"
                        />
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">Statut</label>
                        <select
                            v-model="status"
                            class="mt-1 block rounded-md border-gray-300"
                            @change="applyFilters"
                        >
                            <option value="active">Actives</option>
                            <option value="cancelled">Annulées</option>
                            <option value="all">Toutes</option>
                        </select>
                    </div>
                </div>

                <div class="overflow-x-auto rounded-lg bg-white shadow">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left">Nom</th>
                                <th class="px-4 py-2 text-left">E-mail</th>
                                <th class="px-4 py-2 text-left">Tél.</th>
                                <th class="px-4 py-2 text-left">Poste / créneau</th>
                                <th class="px-4 py-2 text-left">Att.</th>
                                <th class="px-4 py-2 text-left">Pointage</th>
                                <th class="px-4 py-2 text-left"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr v-for="r in registrations" :key="r.id">
                                <td class="px-4 py-2">
                                    {{ r.firstname }} {{ r.lastname }}
                                    <span v-if="r.cancelled_at" class="text-xs text-red-600">(annulé)</span>
                                </td>
                                <td class="px-4 py-2">{{ r.email }}</td>
                                <td class="px-4 py-2">{{ r.phone }}</td>
                                <td class="px-4 py-2">
                                    {{ r.slot.position }} · {{ r.slot.date }}
                                    {{ String(r.slot.start_time).slice(0, 5) }}
                                </td>
                                <td class="px-4 py-2 text-xs">
                                    <span v-if="r.waitlist" class="rounded bg-amber-100 px-1.5 py-0.5 text-amber-900"
                                        >Liste d’attente</span
                                    >
                                    <span v-else class="text-gray-400">—</span>
                                </td>
                                <td class="px-4 py-2">
                                    <button
                                        v-if="!r.cancelled_at && !r.waitlist"
                                        type="button"
                                        class="rounded border px-2 py-1 text-xs"
                                        :class="
                                            r.checked_in_at
                                                ? 'border-emerald-600 bg-emerald-50 text-emerald-900'
                                                : 'border-gray-300 text-gray-700'
                                        "
                                        @click="toggleCheckin(r.id)"
                                    >
                                        {{ r.checked_in_at ? 'Présent' : 'Absent' }}
                                    </button>
                                </td>
                                <td class="px-4 py-2 text-right">
                                    <SecondaryButton
                                        v-if="!r.cancelled_at"
                                        type="button"
                                        class="me-2"
                                        @click="recap(r.id)"
                                    >
                                        Récap
                                    </SecondaryButton>
                                    <SecondaryButton
                                        v-if="!r.cancelled_at"
                                        type="button"
                                        class="text-red-700"
                                        @click="cancelReg(r.id)"
                                    >
                                        Annuler
                                    </SecondaryButton>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
