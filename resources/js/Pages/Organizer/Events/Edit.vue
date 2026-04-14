<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    event: Object,
});

const customFieldsText = ref(JSON.stringify(props.event.custom_fields ?? [], null, 2));

const form = useForm({
    title: props.event.title,
    description: props.event.description ?? '',
    date_start: props.event.date_start,
    date_end: props.event.date_end,
    daily_window_start: props.event.daily_window_start,
    daily_window_end: props.event.daily_window_end,
    status: props.event.status,
    notify_organizer_on_registration: props.event.notify_organizer_on_registration,
    waitlist_enabled: props.event.waitlist_enabled ?? true,
    participant_edit_deadline_hours: props.event.participant_edit_deadline_hours ?? 48,
    matching_enabled: props.event.matching_enabled ?? true,
    custom_fields: props.event.custom_fields ?? [],
});

function submit() {
    try {
        form.custom_fields = JSON.parse(customFieldsText.value || '[]');
    } catch {
        alert('Le JSON des champs personnalisés est invalide.');
        return;
    }
    form.patch(route('evenements.mettre_a_jour', props.event.slug));
}
</script>

<template>
    <Head title="Modifier l'événement" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Modifier l'événement
            </h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
                <form class="space-y-6 bg-white p-6 shadow sm:rounded-lg" @submit.prevent="submit">
                    <div>
                        <InputLabel for="title" value="Titre" />
                        <TextInput
                            id="title"
                            v-model="form.title"
                            type="text"
                            class="mt-1 block w-full"
                            required
                        />
                        <InputError class="mt-2" :message="form.errors.title" />
                    </div>
                    <div>
                        <InputLabel for="description" value="Description" />
                        <textarea
                            id="description"
                            v-model="form.description"
                            rows="4"
                            class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500"
                        />
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <InputLabel for="date_start" value="Date début" />
                            <TextInput
                                id="date_start"
                                v-model="form.date_start"
                                type="date"
                                class="mt-1 block w-full"
                                required
                            />
                        </div>
                        <div>
                            <InputLabel for="date_end" value="Date fin" />
                            <TextInput
                                id="date_end"
                                v-model="form.date_end"
                                type="date"
                                class="mt-1 block w-full"
                                required
                            />
                        </div>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <InputLabel for="daily_window_start" value="Fenêtre début" />
                            <TextInput
                                id="daily_window_start"
                                v-model="form.daily_window_start"
                                type="time"
                                class="mt-1 block w-full"
                            />
                        </div>
                        <div>
                            <InputLabel for="daily_window_end" value="Fenêtre fin" />
                            <TextInput
                                id="daily_window_end"
                                v-model="form.daily_window_end"
                                type="time"
                                class="mt-1 block w-full"
                            />
                        </div>
                    </div>
                    <div>
                        <InputLabel for="status" value="Statut" />
                        <select
                            id="status"
                            v-model="form.status"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                        >
                            <option value="draft">Brouillon</option>
                            <option value="open">Ouvert</option>
                            <option value="closed">Fermé</option>
                            <option value="archived">Archivé</option>
                        </select>
                    </div>
                    <label class="flex items-center gap-2 text-sm text-gray-700">
                        <input
                            v-model="form.notify_organizer_on_registration"
                            type="checkbox"
                            class="rounded border-gray-300 text-brand-600"
                        />
                        Notifier l'organisateur à chaque inscription
                    </label>
                    <label class="flex items-center gap-2 text-sm text-gray-700">
                        <input
                            v-model="form.waitlist_enabled"
                            type="checkbox"
                            class="rounded border-gray-300 text-brand-600"
                        />
                        Autoriser la liste d’attente quand un créneau est plein
                    </label>
                    <label class="flex items-center gap-2 text-sm text-gray-700">
                        <input
                            v-model="form.matching_enabled"
                            type="checkbox"
                            class="rounded border-gray-300 text-brand-600"
                        />
                        Afficher le filtre « disponibilités » sur la page publique
                    </label>
                    <div>
                        <InputLabel
                            for="participant_edit_deadline_hours"
                            value="Délai (heures avant le début de l’événement) pour modifier / annuler côté bénévole"
                        />
                        <TextInput
                            id="participant_edit_deadline_hours"
                            v-model.number="form.participant_edit_deadline_hours"
                            type="number"
                            min="1"
                            max="720"
                            class="mt-1 block w-full max-w-xs"
                            required
                        />
                    </div>
                    <div>
                        <InputLabel
                            for="custom_fields_json"
                            value="Champs personnalisés (JSON — tableau d’objets id, label, type, required, options)"
                        />
                        <textarea
                            id="custom_fields_json"
                            v-model="customFieldsText"
                            rows="8"
                            class="mt-1 w-full rounded-md border-gray-300 font-mono text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500"
                            placeholder='[{"id":"tshirt","label":"Taille T-shirt","type":"select","required":true,"options":["S","M","L","XL"]}]'
                        />
                    </div>
                    <div class="flex justify-end">
                        <PrimaryButton :disabled="form.processing">Enregistrer</PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
