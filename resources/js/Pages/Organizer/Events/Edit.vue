<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useDaySchedules } from '@/composables/useDaySchedules';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    event: Object,
});

const customFieldsText = ref(JSON.stringify(props.event.custom_fields ?? [], null, 2));

const form = useForm({
    title: props.event.title,
    description: props.event.description ?? '',
    additional_info: props.event.additional_info ?? '',
    recommendations: props.event.recommendations ?? '',
    practical_details: props.event.practical_details ?? '',
    date_start: props.event.date_start,
    date_end: props.event.date_end,
    daily_window_start: props.event.daily_window_start,
    daily_window_end: props.event.daily_window_end,
    status: props.event.status,
    notify_organizer_on_registration: props.event.notify_organizer_on_registration,
    waitlist_enabled: props.event.waitlist_enabled ?? true,
    participant_edit_deadline_hours: props.event.participant_edit_deadline_hours ?? 48,
    matching_enabled: props.event.matching_enabled ?? true,
    registration_enabled: props.event.registration_enabled !== false,
    custom_fields: props.event.custom_fields ?? [],
});

const { usePerDay, daySchedules, isMultiDay, appendToFormPayload } = useDaySchedules(
    form,
    props.event.day_schedules ?? null,
);

function submit() {
    try {
        form.custom_fields = JSON.parse(customFieldsText.value || '[]');
    } catch {
        alert('Le JSON des champs personnalisés est invalide.');
        return;
    }
    form.transform((data) => appendToFormPayload(data)).patch(route('evenements.mettre_a_jour', props.event.slug));
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
                    <div>
                        <InputLabel for="additional_info" value="Informations complémentaires" />
                        <p class="mt-0.5 text-xs text-gray-500">
                            Affichées sur la page publique sous la description (accès, contact, etc.).
                        </p>
                        <textarea
                            id="additional_info"
                            v-model="form.additional_info"
                            rows="3"
                            class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500"
                        />
                    </div>
                    <div>
                        <InputLabel for="recommendations" value="Recommandations" />
                        <p class="mt-0.5 text-xs text-gray-500">
                            Conseils vestimentaires, matériel à prévoir, etc.
                        </p>
                        <textarea
                            id="recommendations"
                            v-model="form.recommendations"
                            rows="3"
                            class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500"
                        />
                    </div>
                    <div>
                        <InputLabel for="practical_details" value="Détails pratiques" />
                        <p class="mt-0.5 text-xs text-gray-500">
                            Stationnement, restauration, point de rendez-vous…
                        </p>
                        <textarea
                            id="practical_details"
                            v-model="form.practical_details"
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
                    <div
                        v-if="isMultiDay"
                        class="rounded-lg border border-slate-200 bg-slate-50/80 p-4"
                    >
                        <label class="flex cursor-pointer items-start gap-3 text-sm text-slate-800">
                            <input
                                v-model="usePerDay"
                                type="checkbox"
                                class="mt-0.5 rounded border-slate-300 text-brand-600"
                            />
                            <span>
                                <span class="font-medium">Horaires différents selon les jours</span>
                                <span class="mt-1 block text-xs font-normal text-slate-600">
                                    Décochez pour la même fenêtre chaque jour. Sinon, désactivez un jour pour ne
                                    générer aucun créneau ce jour-là (régénérez les créneaux depuis les postes).
                                </span>
                            </span>
                        </label>
                        <div v-if="usePerDay && daySchedules.length" class="mt-4 space-y-3">
                            <div
                                v-for="row in daySchedules"
                                :key="row.date"
                                class="flex flex-col gap-2 rounded-md border border-white bg-white p-3 sm:flex-row sm:items-center sm:gap-4"
                            >
                                <label class="flex min-w-[10rem] items-center gap-2 text-sm">
                                    <input
                                        v-model="row.enabled"
                                        type="checkbox"
                                        class="rounded border-slate-300 text-brand-600"
                                    />
                                    <span class="font-medium">{{ row.date }}</span>
                                </label>
                                <div class="flex flex-1 flex-wrap items-center gap-2">
                                    <TextInput
                                        v-model="row.window_start"
                                        type="time"
                                        class="w-36"
                                        :disabled="!row.enabled"
                                    />
                                    <span class="text-slate-500">→</span>
                                    <TextInput
                                        v-model="row.window_end"
                                        type="time"
                                        class="w-36"
                                        :disabled="!row.enabled"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                    <InputError class="mt-2" :message="form.errors.day_schedules" />
                    <div>
                        <InputLabel for="status" value="Statut" />
                        <select
                            id="status"
                            v-model="form.status"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                        >
                            <option value="open">Ouvert (inscriptions possibles si activées ci-dessous)</option>
                            <option value="closed">Fermé (page publique consultable, pas de nouvelles inscriptions)</option>
                            <option value="archived">Archivé (masqué du public)</option>
                        </select>
                    </div>
                    <label class="flex items-center gap-2 text-sm text-gray-700">
                        <input
                            v-model="form.registration_enabled"
                            type="checkbox"
                            class="rounded border-gray-300 text-brand-600"
                            :disabled="form.status !== 'open'"
                        />
                        <span>
                            Autoriser les inscriptions sur la page publique
                            <span v-if="form.status !== 'open'" class="block text-xs font-normal text-amber-800">
                                (passez le statut à « Ouvert » pour rouvrir les inscriptions)
                            </span>
                        </span>
                    </label>
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
