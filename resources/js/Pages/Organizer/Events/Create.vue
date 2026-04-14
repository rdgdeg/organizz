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
    date_start: '',
    date_end: '',
    daily_window_start: '08:00',
    daily_window_end: '20:00',
    status: 'draft',
});

function submit() {
    form.post(route('events.store'));
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
                    <div>
                        <InputLabel for="status" value="Statut" />
                        <select
                            id="status"
                            v-model="form.status"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500"
                        >
                            <option value="draft">Brouillon</option>
                            <option value="open">Ouvert</option>
                            <option value="closed">Fermé</option>
                            <option value="archived">Archivé</option>
                        </select>
                        <InputError class="mt-2" :message="form.errors.status" />
                    </div>
                    <div class="flex justify-end">
                        <PrimaryButton :disabled="form.processing">Enregistrer</PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
