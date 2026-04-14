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
    createForm.post(route('events.positions.store', props.event.id));
}

function patchPosition(p) {
    router.patch(
        route('events.positions.update', [props.event.id, p.id]),
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
    router.delete(route('events.positions.destroy', [props.event.id, id]));
}

function regenerate(id) {
    router.post(route('events.positions.regenerate', [props.event.id, id]), {}, { preserveScroll: true });
}
</script>

<template>
    <Head :title="`Postes — ${event.title}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col gap-2">
                <h2 class="text-xl font-semibold text-gray-800">Postes — {{ event.title }}</h2>
                <Link
                    :href="route('events.show', event.id)"
                    class="text-sm text-brand-700 hover:underline"
                >
                    ← Retour événement
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-4xl space-y-8 sm:px-6 lg:px-8">
                <div
                    v-if="page.props.flash?.success"
                    class="rounded-md bg-green-50 p-4 text-sm text-green-800"
                >
                    {{ page.props.flash.success }}
                </div>

                <div class="rounded-lg bg-white p-6 shadow">
                    <h3 class="text-lg font-medium">Nouveau poste</h3>
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
                                class="mt-1 w-full rounded-md border-gray-300"
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
                            <InputLabel for="slot_duration_minutes" value="Durée créneau (min)" />
                            <TextInput
                                id="slot_duration_minutes"
                                v-model="createForm.slot_duration_minutes"
                                type="number"
                                min="15"
                                class="mt-1 block w-full"
                                required
                            />
                        </div>
                        <div>
                            <InputLabel for="required_per_slot" value="Personnes / créneau" />
                            <TextInput
                                id="required_per_slot"
                                v-model="createForm.required_per_slot"
                                type="number"
                                min="1"
                                class="mt-1 block w-full"
                                required
                            />
                        </div>
                        <div class="flex items-end sm:col-span-2">
                            <PrimaryButton :disabled="createForm.processing">Créer et générer les créneaux</PrimaryButton>
                        </div>
                    </form>
                </div>

                <div
                    v-for="p in positions"
                    :key="p.id"
                    class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm"
                >
                    <form class="space-y-4" @submit.prevent="patchPosition(p)">
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
                                    class="mt-1 w-full rounded-md border-gray-300"
                                />
                            </div>
                            <div>
                                <label class="text-sm text-gray-700">Couleur</label>
                                <input
                                    v-model="p.color"
                                    type="text"
                                    class="mt-1 block w-full rounded-md border-gray-300 font-mono"
                                />
                            </div>
                            <div>
                                <label class="text-sm text-gray-700">Durée (min)</label>
                                <input
                                    v-model.number="p.slot_duration_minutes"
                                    type="number"
                                    min="15"
                                    class="mt-1 block w-full rounded-md border-gray-300"
                                />
                            </div>
                            <div>
                                <label class="text-sm text-gray-700">Personnes / créneau</label>
                                <input
                                    v-model.number="p.required_per_slot"
                                    type="number"
                                    min="1"
                                    class="mt-1 block w-full rounded-md border-gray-300"
                                />
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <PrimaryButton type="submit">Enregistrer</PrimaryButton>
                            <SecondaryButton type="button" @click="regenerate(p.id)">
                                Régénérer créneaux
                            </SecondaryButton>
                            <SecondaryButton type="button" class="text-red-700" @click="destroyPosition(p.id)">
                                Supprimer
                            </SecondaryButton>
                        </div>
                        <p class="text-sm text-gray-500">{{ p.slots_count }} créneau(x)</p>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
