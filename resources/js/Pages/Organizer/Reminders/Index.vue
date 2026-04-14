<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';

const props = defineProps({
    event: Object,
    rules: Array,
});

const createForm = useForm({
    days_before: 3,
    time_of_day: '09:00',
    active: true,
});

function createRule() {
    createForm.post(route('evenements.rappels.enregistrer', props.event.slug));
}

function updateRule(r) {
    router.patch(
        route('evenements.rappels.modifier', [props.event.slug, r.id]),
        {
            days_before: r.days_before,
            time_of_day: r.time_of_day,
            active: r.active,
        },
        { preserveScroll: true },
    );
}

function destroyRule(id) {
    if (!confirm('Supprimer cette règle ?')) return;
    router.delete(route('evenements.rappels.supprimer', [props.event.slug, id]));
}

function testEmail() {
    router.post(route('evenements.rappels.test', props.event.slug));
}
</script>

<template>
    <Head :title="`Rappels — ${event.title}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col gap-2">
                <h2 class="text-xl font-semibold text-gray-800">Rappels — {{ event.title }}</h2>
                <Link
                    :href="route('evenements.montrer', event.slug)"
                    class="text-sm text-brand-700 hover:underline"
                >
                    ← Retour événement
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-3xl space-y-8 sm:px-6 lg:px-8">
                <div class="rounded-lg bg-white p-6 shadow">
                    <h3 class="text-lg font-medium">Nouvelle règle</h3>
                    <form class="mt-4 flex flex-wrap items-end gap-4" @submit.prevent="createRule">
                        <div>
                            <label class="text-sm text-gray-700">Jours avant l’événement</label>
                            <input
                                v-model.number="createForm.days_before"
                                type="number"
                                min="0"
                                class="mt-1 block rounded-md border-gray-300"
                            />
                        </div>
                        <div>
                            <label class="text-sm text-gray-700">Heure d’envoi</label>
                            <input
                                v-model="createForm.time_of_day"
                                type="time"
                                class="mt-1 block rounded-md border-gray-300"
                            />
                        </div>
                        <label class="flex items-center gap-2 text-sm">
                            <input v-model="createForm.active" type="checkbox" class="rounded border-gray-300" />
                            Active
                        </label>
                        <PrimaryButton :disabled="createForm.processing">Ajouter</PrimaryButton>
                    </form>
                </div>

                <div
                    v-for="r in rules"
                    :key="r.id"
                    class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm"
                >
                    <form class="flex flex-wrap items-end gap-4" @submit.prevent="updateRule(r)">
                        <div>
                            <label class="text-sm text-gray-700">Jours avant</label>
                            <input
                                v-model.number="r.days_before"
                                type="number"
                                min="0"
                                class="mt-1 block rounded-md border-gray-300"
                            />
                        </div>
                        <div>
                            <label class="text-sm text-gray-700">Heure</label>
                            <input v-model="r.time_of_day" type="time" class="mt-1 block rounded-md border-gray-300" />
                        </div>
                        <label class="flex items-center gap-2 text-sm">
                            <input v-model="r.active" type="checkbox" class="rounded border-gray-300" />
                            Active
                        </label>
                        <PrimaryButton type="submit">Enregistrer</PrimaryButton>
                        <SecondaryButton type="button" class="text-red-700" @click="destroyRule(r.id)">
                            Supprimer
                        </SecondaryButton>
                    </form>
                </div>

                <div class="rounded-lg bg-brand-50 p-6">
                    <p class="text-sm text-brand-900">
                        Les rappels sont envoyés lorsque la date correspond (jour de l’événement − jours avant) et
                        à l’heure indiquée (précision à la minute, cron requis).
                    </p>
                    <SecondaryButton type="button" class="mt-4" @click="testEmail">
                        Envoyer un email de test
                    </SecondaryButton>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
