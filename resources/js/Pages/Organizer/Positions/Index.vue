<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';

const props = defineProps({
    event: Object,
    positions: Array,
    permissions: {
        type: Object,
        default: () => ({ configure: true }),
    },
});

const page = usePage();

const createForm = useForm({
    name: '',
    description: '',
    color: '#6366f1',
    slot_duration_minutes: 120,
    required_per_slot: 1,
});

function createPosition() {
    createForm.post(route('evenements.postes.enregistrer', props.event.slug));
}

function patchPosition(p) {
    router.patch(
        route('evenements.postes.modifier', [props.event.slug, p.id]),
        {
            name: p.name,
            description: p.description ?? '',
            color: p.color,
            slot_duration_minutes: p.slot_duration_minutes,
            required_per_slot: p.required_per_slot,
        },
        { preserveScroll: true },
    );
}

function destroyPosition(id) {
    if (!confirm('Supprimer ce poste et ses créneaux ?')) return;
    router.delete(route('evenements.postes.supprimer', [props.event.slug, id]));
}

function regenerate(id) {
    if (
        !confirm(
            'Régénérer la grille ? Les créneaux sans inscription seront supprimés et recréés selon la durée et les dates de l’événement.',
        )
    ) {
        return;
    }
    router.post(route('evenements.postes.regenerer', [props.event.slug, id]), {}, { preserveScroll: true });
}

function saveSlotMax(slotId, rawValue, booked) {
    const v = parseInt(String(rawValue), 10);
    if (Number.isNaN(v) || v < 1 || v > 500) {
        return;
    }
    if (v < booked) {
        alert(`Minimum : ${booked} place(s) déjà réservée(s).`);
        return;
    }
    router.patch(
        route('evenements.creneaux.modifier', [props.event.slug, slotId]),
        { max_volunteers: v },
        { preserveScroll: true },
    );
}
</script>

<template>
    <Head :title="`Postes — ${event.title}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col gap-2">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Postes — {{ event.title }}</h2>
                <Link
                    :href="route('evenements.montrer', event.slug)"
                    class="text-sm text-brand-700 hover:underline"
                >
                    ← Retour événement
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-5xl space-y-8 sm:px-6 lg:px-8">
                <div
                    v-if="page.props.flash?.success"
                    class="rounded-md bg-green-50 p-4 text-sm text-green-800"
                >
                    {{ page.props.flash.success }}
                </div>
                <div
                    v-if="page.props.errors?.max_volunteers"
                    class="rounded-md border border-red-200 bg-red-50 p-4 text-sm text-red-800"
                >
                    {{ page.props.errors.max_volunteers }}
                </div>

                <div
                    v-if="permissions.configure"
                    class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm"
                >
                    <h3 class="text-lg font-semibold text-gray-900">Nouveau poste</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Un poste = un rôle (accueil, bar, etc.). Les créneaux sont générés automatiquement selon les
                        dates de l’événement et la durée choisie.
                    </p>
                    <form class="mt-4 grid gap-4 sm:grid-cols-2" @submit.prevent="createPosition">
                        <div class="sm:col-span-2">
                            <InputLabel for="name" value="Nom" />
                            <TextInput id="name" v-model="createForm.name" class="mt-1 block w-full" required />
                            <InputError class="mt-1" :message="createForm.errors.name" />
                        </div>
                        <div class="sm:col-span-2">
                            <InputLabel for="description" value="Description" />
                            <textarea
                                id="description"
                                v-model="createForm.description"
                                rows="2"
                                class="mt-1 w-full rounded-md border-gray-300 shadow-sm"
                            />
                        </div>
                        <div>
                            <InputLabel for="color" value="Couleur" />
                            <TextInput
                                id="color"
                                v-model="createForm.color"
                                type="text"
                                class="mt-1 block w-full font-mono"
                                required
                            />
                        </div>
                        <div>
                            <InputLabel for="slot_duration_minutes" value="Durée d’un créneau (minutes)" />
                            <TextInput
                                id="slot_duration_minutes"
                                v-model="createForm.slot_duration_minutes"
                                type="number"
                                min="15"
                                class="mt-1 block w-full"
                                required
                            />
                        </div>
                        <div class="sm:col-span-2">
                            <InputLabel for="required_per_slot" value="Places par défaut (nouvelle grille)" />
                            <TextInput
                                id="required_per_slot"
                                v-model="createForm.required_per_slot"
                                type="number"
                                min="1"
                                class="mt-1 block w-full max-w-xs"
                                required
                            />
                            <p class="mt-1 text-xs text-gray-500">
                                Valeur appliquée à la génération ; vous pourrez ajuster chaque créneau ensuite.
                            </p>
                        </div>
                        <div class="flex items-end sm:col-span-2">
                            <PrimaryButton :disabled="createForm.processing">Créer et générer les créneaux</PrimaryButton>
                        </div>
                    </form>
                </div>

                <div v-if="!positions.length" class="rounded-xl border border-dashed border-gray-300 bg-gray-50/80 p-10 text-center text-gray-600">
                    Aucun poste pour l’instant. Créez-en un ci-dessus.
                </div>

                <div v-else class="space-y-4">
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Vos postes</h3>
                    <p class="text-sm text-gray-600">
                        Ouvrez une carte pour modifier le poste et le nombre de <strong>places par créneau</strong>.
                    </p>

                    <div class="grid gap-4 md:grid-cols-2">
                        <details
                            v-for="p in positions"
                            :key="p.id"
                            class="group rounded-2xl border border-slate-200 bg-white shadow-sm open:border-brand-400 open:ring-2 open:ring-brand-100"
                        >
                            <summary
                                class="flex cursor-pointer list-none items-center gap-4 p-4 transition hover:bg-slate-50 [&::-webkit-details-marker]:hidden"
                            >
                                <span
                                    class="h-14 w-1.5 shrink-0 rounded-full shadow-inner"
                                    :style="{ background: p.color }"
                                />
                                <div class="min-w-0 flex-1 text-left">
                                    <p class="truncate text-lg font-semibold text-gray-900">{{ p.name }}</p>
                                    <p class="text-sm text-gray-500">
                                        {{ p.slots_count }} créneau(x)
                                        <span v-if="!permissions.configure" class="text-amber-700"> · lecture seule</span>
                                    </p>
                                </div>
                                <span
                                    class="shrink-0 rounded-full bg-slate-100 px-2 py-1 text-xs font-medium text-slate-600 group-open:hidden"
                                >
                                    Ouvrir
                                </span>
                                <span
                                    class="hidden shrink-0 rounded-full bg-brand-100 px-2 py-1 text-xs font-medium text-brand-800 group-open:inline"
                                >
                                    Fermer
                                </span>
                            </summary>

                            <div class="space-y-6 border-t border-slate-100 p-4 pt-5">
                                <form v-if="permissions.configure" class="space-y-4" @submit.prevent="patchPosition(p)">
                                    <p class="text-xs font-medium text-gray-500">Paramètres du poste</p>
                                    <div class="grid gap-4 sm:grid-cols-2">
                                        <div class="sm:col-span-2">
                                            <InputLabel :for="'name-' + p.id" value="Nom" />
                                            <TextInput
                                                :id="'name-' + p.id"
                                                v-model="p.name"
                                                class="mt-1 block w-full"
                                                required
                                            />
                                        </div>
                                        <div class="sm:col-span-2">
                                            <label class="text-sm text-gray-700">Description</label>
                                            <textarea
                                                v-model="p.description"
                                                rows="2"
                                                class="mt-1 w-full rounded-md border-gray-300 shadow-sm"
                                            />
                                        </div>
                                        <div>
                                            <label class="text-sm text-gray-700">Couleur</label>
                                            <input
                                                v-model="p.color"
                                                type="text"
                                                class="mt-1 block w-full rounded-md border-gray-300 font-mono shadow-sm"
                                            />
                                        </div>
                                        <div>
                                            <label class="text-sm text-gray-700">Durée créneau (min)</label>
                                            <input
                                                v-model.number="p.slot_duration_minutes"
                                                type="number"
                                                min="15"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                            />
                                        </div>
                                        <div class="sm:col-span-2">
                                            <label class="text-sm text-gray-700">Places par défaut (prochaine régénération)</label>
                                            <input
                                                v-model.number="p.required_per_slot"
                                                type="number"
                                                min="1"
                                                class="mt-1 block w-full max-w-xs rounded-md border-gray-300 shadow-sm"
                                            />
                                        </div>
                                    </div>
                                    <div class="flex flex-wrap gap-2">
                                        <PrimaryButton type="submit">Enregistrer le poste</PrimaryButton>
                                        <SecondaryButton type="button" @click="regenerate(p.id)">
                                            Régénérer les créneaux
                                        </SecondaryButton>
                                        <SecondaryButton type="button" class="text-red-700" @click="destroyPosition(p.id)">
                                            Supprimer le poste
                                        </SecondaryButton>
                                    </div>
                                </form>
                                <div v-else class="rounded-lg bg-slate-50 p-3 text-sm text-gray-600">
                                    {{ p.name }} — {{ p.slots_count }} créneau(x). Modification réservée aux organisateurs.
                                </div>

                                <div v-if="p.slots?.length" class="overflow-hidden rounded-xl border border-slate-200">
                                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                                        <thead class="bg-slate-50">
                                            <tr>
                                                <th class="px-3 py-2 text-left font-semibold text-gray-700">Date</th>
                                                <th class="px-3 py-2 text-left font-semibold text-gray-700">Horaire</th>
                                                <th class="px-3 py-2 text-left font-semibold text-gray-700">Places</th>
                                                <th class="px-3 py-2 text-center font-semibold text-gray-700">Inscrits</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100 bg-white">
                                            <tr v-for="s in p.slots" :key="s.id" class="hover:bg-slate-50/80">
                                                <td class="whitespace-nowrap px-3 py-2 text-gray-900">
                                                    {{
                                                        s.date
                                                            ? new Date(s.date + 'T12:00:00').toLocaleDateString('fr-FR', {
                                                                  weekday: 'short',
                                                                  day: 'numeric',
                                                                  month: 'short',
                                                              })
                                                            : '—'
                                                    }}
                                                </td>
                                                <td class="whitespace-nowrap px-3 py-2 text-gray-700">
                                                    {{ s.start_time }} – {{ s.end_time }}
                                                </td>
                                                <td class="px-3 py-2">
                                                    <div v-if="permissions.configure" class="flex flex-col gap-0.5">
                                                        <label class="sr-only">Places pour ce créneau</label>
                                                        <input
                                                            type="number"
                                                            class="w-24 rounded-md border-gray-300 text-center shadow-sm"
                                                            :min="Math.max(1, s.booked)"
                                                            max="500"
                                                            :value="s.max_volunteers"
                                                            title="Modifiez puis quittez le champ pour enregistrer"
                                                            @change="
                                                                (e) =>
                                                                    saveSlotMax(
                                                                        s.id,
                                                                        e.target.value,
                                                                        s.booked,
                                                                    )
                                                            "
                                                        />
                                                        <span class="text-[10px] text-gray-400">Entrée ou clic hors champ</span>
                                                    </div>
                                                    <span v-else class="font-medium">{{ s.max_volunteers }}</span>
                                                </td>
                                                <td class="px-3 py-2 text-center text-gray-600">{{ s.booked }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <p v-else class="text-sm text-gray-500">Aucun créneau — utilisez « Régénérer les créneaux ».</p>
                            </div>
                        </details>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
