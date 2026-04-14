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
    collaborators: Array,
    roleOptions: Array,
});

const page = usePage();

const form = useForm({
    email: '',
    role: 'registrations',
});

function submit() {
    form.post(route('evenements.coorganisateurs.ajouter', props.event.slug), {
        preserveScroll: true,
        onSuccess: () => form.reset('email'),
    });
}

function remove(userId) {
    if (!confirm('Retirer cet accès ?')) return;
    router.delete(route('evenements.coorganisateurs.retirer', [props.event.slug, userId]));
}
</script>

<template>
    <Head title="Co-organisateurs" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col gap-2">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Co-organisateurs</h2>
                <Link :href="route('evenements.montrer', event.slug)" class="text-sm text-brand-700 hover:underline">
                    ← Retour événement
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
                <div
                    v-if="page.props.flash?.success"
                    class="mb-4 rounded-md bg-green-50 p-4 text-sm text-green-800"
                >
                    {{ page.props.flash.success }}
                </div>

                <div class="rounded-lg bg-white p-6 shadow">
                    <p class="text-sm text-gray-600">
                        Invitez un utilisateur existant (il doit déjà avoir un compte Organizz). Les rôles :
                        <strong>admin</strong> (tout sauf propriété), <strong>inscriptions</strong> (inscriptions &
                        pointage), <strong>lecture seule</strong>.
                    </p>
                    <form class="mt-6 flex flex-wrap items-end gap-4" @submit.prevent="submit">
                        <div class="min-w-[200px] flex-1">
                            <InputLabel for="email" value="Email du co-organisateur" />
                            <TextInput
                                id="email"
                                v-model="form.email"
                                type="email"
                                class="mt-1 block w-full"
                                required
                                autocomplete="off"
                            />
                            <InputError class="mt-1" :message="form.errors.email" />
                        </div>
                        <div>
                            <InputLabel for="role" value="Rôle" />
                            <select
                                id="role"
                                v-model="form.role"
                                class="mt-1 block rounded-md border-gray-300 shadow-sm"
                            >
                                <option v-for="opt in roleOptions" :key="opt.value" :value="opt.value">
                                    {{ opt.label }}
                                </option>
                            </select>
                        </div>
                        <PrimaryButton :disabled="form.processing">Inviter</PrimaryButton>
                    </form>
                </div>

                <ul class="mt-6 divide-y divide-gray-200 rounded-lg bg-white shadow">
                    <li
                        v-for="c in collaborators"
                        :key="c.id"
                        class="flex flex-wrap items-center justify-between gap-2 px-4 py-3"
                    >
                        <div>
                            <p class="font-medium text-gray-900">{{ c.name }}</p>
                            <p class="text-sm text-gray-500">{{ c.email }}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs text-slate-700">{{
                                c.role
                            }}</span>
                            <SecondaryButton type="button" class="text-red-700" @click="remove(c.id)">
                                Retirer
                            </SecondaryButton>
                        </div>
                    </li>
                    <li v-if="!collaborators.length" class="px-4 py-6 text-sm text-gray-500">
                        Aucun co-organisateur pour l’instant.
                    </li>
                </ul>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
