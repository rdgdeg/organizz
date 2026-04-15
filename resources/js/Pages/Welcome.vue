<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <Head title="Bienvenue" />

    <div class="min-h-screen bg-slate-50 text-slate-800">
        <div class="mx-auto flex min-h-screen max-w-4xl flex-col px-6 py-12">
            <header class="flex flex-wrap items-center justify-between gap-4">
                <p class="text-lg font-bold text-brand-700">Organizz</p>
                <nav v-if="canLogin" class="flex flex-wrap items-center gap-3 text-sm font-medium">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="route('dashboard')"
                        class="rounded-lg px-3 py-2 text-slate-700 transition hover:bg-white hover:shadow-sm"
                    >
                        Tableau de bord
                    </Link>
                    <template v-else>
                        <Link
                            :href="route('login')"
                            class="rounded-lg px-3 py-2 text-slate-700 transition hover:bg-white hover:shadow-sm"
                        >
                            Connexion
                        </Link>
                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="rounded-lg bg-brand-600 px-4 py-2 text-white shadow-sm transition hover:bg-brand-700"
                        >
                            Inscription
                        </Link>
                    </template>
                </nav>
            </header>

            <main class="mt-12 flex flex-1 flex-col justify-center">
                <h1 class="text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">
                    Bienvenue sur Organizz
                </h1>
                <p class="mt-4 max-w-2xl text-lg text-slate-600">
                    Cette page est une page technique Laravel/Inertia. L’accueil public du site se trouve à la racine
                    <code class="rounded bg-slate-200/80 px-1.5 py-0.5 text-sm">/</code> (landing marketing).
                </p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <Link
                        :href="route('accueil')"
                        class="inline-flex rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700"
                    >
                        Aller à l’accueil
                    </Link>
                    <a
                        href="https://laravel.com/docs"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-800 shadow-sm transition hover:border-slate-300"
                    >
                        Documentation Laravel (EN)
                    </a>
                </div>
            </main>

            <footer class="mt-12 border-t border-slate-200 pt-8 text-center text-sm text-slate-500">
                Laravel v{{ laravelVersion }} · PHP v{{ phpVersion }}
            </footer>
        </div>
    </div>
</template>
