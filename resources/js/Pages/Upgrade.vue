<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';

const page = usePage();
</script>

<template>
    <Head title="Passer en Pro" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold text-gray-800">Passer en Pro</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-2xl px-4 text-center sm:px-6">
                <p v-if="page.props.flash?.message" class="rounded-md bg-amber-50 p-4 text-amber-900">
                    {{ page.props.flash.message }}
                </p>
                <p class="mt-6 text-gray-600">
                    Le plan Pro sera disponible prochainement. Vous utilisez actuellement le plan gratuit avec ses
                    limites.
                </p>
                <Link
                    :href="route('dashboard')"
                    class="mt-8 inline-block text-brand-700 hover:underline"
                >
                    Retour au tableau de bord
                </Link>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
