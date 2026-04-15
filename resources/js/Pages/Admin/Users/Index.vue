<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { reactive, watchEffect } from 'vue';

const props = defineProps({
    users: Object,
});

const createForm = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    plan: 'free',
    is_super_admin: false,
});

const drafts = reactive({});

watchEffect(() => {
    for (const u of props.users.data) {
        if (!drafts[u.id]) {
            drafts[u.id] = { plan: u.plan, is_super_admin: u.is_super_admin };
        }
    }
});

function submitCreate() {
    createForm.post(route('administration.utilisateurs.enregistrer'), {
        preserveScroll: true,
        onSuccess: () => createForm.reset(),
    });
}

function updateRow(userId) {
    const d = drafts[userId];
    if (!d) {
        return;
    }
    router.patch(
        route('administration.utilisateurs.mettre_a_jour', userId),
        { plan: d.plan, is_super_admin: d.is_super_admin },
        { preserveScroll: true },
    );
}

function destroyUser(userId) {
    if (!confirm('Supprimer définitivement ce compte ?')) {
        return;
    }
    router.delete(route('administration.utilisateurs.supprimer', userId), { preserveScroll: true });
}
</script>

<template>
    <Head title="Administration — utilisateurs" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <h2 class="text-2xl font-bold tracking-tight text-slate-900">Utilisateurs</h2>
                <div class="flex flex-wrap gap-2">
                    <Link
                        :href="route('administration.evenements.index')"
                        class="text-sm font-semibold text-brand-700 underline-offset-2 hover:underline"
                    >
                        Tous les événements
                    </Link>
                    <Link
                        :href="route('dashboard')"
                        class="text-sm font-semibold text-slate-600 underline-offset-2 hover:underline"
                    >
                        Tableau de bord
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-6xl space-y-10 sm:px-6 lg:px-8">
                <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-bold text-slate-900">Créer un compte</h3>
                    <form class="mt-4 grid gap-4 sm:grid-cols-2" @submit.prevent="submitCreate">
                        <div>
                            <InputLabel for="c_name" value="Nom" />
                            <TextInput id="c_name" v-model="createForm.name" type="text" class="mt-1 block w-full" required />
                            <InputError class="mt-1" :message="createForm.errors.name" />
                        </div>
                        <div>
                            <InputLabel for="c_email" value="E-mail" />
                            <TextInput id="c_email" v-model="createForm.email" type="email" class="mt-1 block w-full" required />
                            <InputError class="mt-1" :message="createForm.errors.email" />
                        </div>
                        <div>
                            <InputLabel for="c_pw" value="Mot de passe" />
                            <TextInput id="c_pw" v-model="createForm.password" type="password" class="mt-1 block w-full" required />
                            <InputError class="mt-1" :message="createForm.errors.password" />
                        </div>
                        <div>
                            <InputLabel for="c_pw2" value="Confirmation" />
                            <TextInput
                                id="c_pw2"
                                v-model="createForm.password_confirmation"
                                type="password"
                                class="mt-1 block w-full"
                                required
                            />
                        </div>
                        <div>
                            <InputLabel for="c_plan" value="Plan" />
                            <select
                                id="c_plan"
                                v-model="createForm.plan"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500"
                            >
                                <option value="free">Gratuit</option>
                                <option value="pro">Pro</option>
                            </select>
                        </div>
                        <label class="mt-6 flex items-center gap-2 text-sm text-slate-700 sm:col-span-2">
                            <input v-model="createForm.is_super_admin" type="checkbox" class="rounded border-gray-300 text-brand-600" />
                            Super administrateur
                        </label>
                        <div class="sm:col-span-2">
                            <PrimaryButton :disabled="createForm.processing">Créer le compte</PrimaryButton>
                        </div>
                    </form>
                </section>

                <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200 text-sm">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-4 py-3 text-left font-semibold text-slate-700">Nom</th>
                                    <th class="px-4 py-3 text-left font-semibold text-slate-700">E-mail</th>
                                    <th class="px-4 py-3 text-left font-semibold text-slate-700">Événements</th>
                                    <th class="px-4 py-3 text-left font-semibold text-slate-700">Plan / super</th>
                                    <th class="px-4 py-3 text-right font-semibold text-slate-700">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr v-for="u in users.data" :key="u.id" class="bg-white">
                                    <td class="px-4 py-3 font-medium text-slate-900">{{ u.name }}</td>
                                    <td class="px-4 py-3 text-slate-600">{{ u.email }}</td>
                                    <td class="px-4 py-3 tabular-nums text-slate-700">{{ u.events_count }}</td>
                                    <td class="px-4 py-3">
                                        <div v-if="drafts[u.id]" class="flex flex-col gap-2 sm:flex-row sm:items-center">
                                            <select
                                                v-model="drafts[u.id].plan"
                                                class="rounded-md border-gray-300 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500"
                                            >
                                                <option value="free">Gratuit</option>
                                                <option value="pro">Pro</option>
                                            </select>
                                            <label class="flex items-center gap-1.5 text-xs text-slate-600">
                                                <input v-model="drafts[u.id].is_super_admin" type="checkbox" class="rounded border-gray-300 text-brand-600" />
                                                Super admin
                                            </label>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <button
                                            type="button"
                                            class="mr-2 text-xs font-semibold text-brand-700 hover:underline"
                                            @click="updateRow(u.id)"
                                        >
                                            Enregistrer
                                        </button>
                                        <button
                                            type="button"
                                            class="text-xs font-semibold text-red-600 hover:underline"
                                            @click="destroyUser(u.id)"
                                        >
                                            Supprimer
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-if="users.prev_page_url || users.next_page_url" class="flex gap-4 border-t border-slate-100 px-4 py-3">
                        <Link v-if="users.prev_page_url" :href="users.prev_page_url" preserve-scroll class="text-sm text-brand-700">
                            Précédent
                        </Link>
                        <Link v-if="users.next_page_url" :href="users.next_page_url" preserve-scroll class="text-sm text-brand-700">
                            Suivant
                        </Link>
                    </div>
                </section>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
