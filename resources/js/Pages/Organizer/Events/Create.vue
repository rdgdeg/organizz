<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    title: '',
    description: '',
    additional_info: '',
    recommendations: '',
    practical_details: '',
    date_start: '',
    date_end: '',
    daily_window_start: '08:00',
    daily_window_end: '20:00',
});

function submit() {
    form.post(route('evenements.enregistrer'));
}
</script>

<template>
    <Head title="Créer un événement" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Créer un événement
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
                        <InputError class="mt-2" :message="form.errors.description" />
                    </div>
                    <div>
                        <InputLabel for="additional_info" value="Informations complémentaires" />
                        <textarea
                            id="additional_info"
                            v-model="form.additional_info"
                            rows="3"
                            class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500"
                        />
                        <InputError class="mt-2" :message="form.errors.additional_info" />
                    </div>
                    <div>
                        <InputLabel for="recommendations" value="Recommandations" />
                        <textarea
                            id="recommendations"
                            v-model="form.recommendations"
                            rows="3"
                            class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500"
                        />
                        <InputError class="mt-2" :message="form.errors.recommendations" />
                    </div>
                    <div>
                        <InputLabel for="practical_details" value="Détails pratiques" />
                        <textarea
                            id="practical_details"
                            v-model="form.practical_details"
                            rows="4"
                            class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500"
                        />
                        <InputError class="mt-2" :message="form.errors.practical_details" />
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
                            <InputError class="mt-2" :message="form.errors.date_start" />
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
                            <InputError class="mt-2" :message="form.errors.date_end" />
                        </div>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <InputLabel for="daily_window_start" value="Fenêtre début (quotidien)" />
                            <TextInput
                                id="daily_window_start"
                                v-model="form.daily_window_start"
                                type="time"
                                class="mt-1 block w-full"
                            />
                        </div>
                        <div>
                            <InputLabel for="daily_window_end" value="Fenêtre fin (quotidien)" />
                            <TextInput
                                id="daily_window_end"
                                v-model="form.daily_window_end"
                                type="time"
                                class="mt-1 block w-full"
                            />
                        </div>
                    </div>
                    <p class="text-sm text-slate-600">
                        L’événement est créé <strong>visible</strong> sur le lien public ; vous pourrez désactiver les inscriptions depuis la fiche événement.
                    </p>
                    <div class="flex justify-end">
                        <PrimaryButton :disabled="form.processing">Enregistrer</PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
