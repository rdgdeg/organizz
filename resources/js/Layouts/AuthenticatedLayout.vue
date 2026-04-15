<script setup>
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);
</script>

<template>
    <div class="relative min-h-screen bg-organizer-app bg-organizer-grid">
        <div
            class="pointer-events-none fixed inset-0 bg-gradient-to-br from-brand-500/[0.03] via-transparent to-ember-500/[0.06]"
            aria-hidden="true"
        />
        <nav
            class="sticky top-0 z-50 border-b border-slate-200/80 bg-white shadow-[0_1px_0_0_rgb(241_245_249/0.9)]"
        >
            <div
                class="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-transparent via-brand-400/40 to-ember-400/50"
                aria-hidden="true"
            />
            <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 justify-between">
                    <div class="flex min-w-0 flex-1 items-center gap-3">
                        <Link
                            :href="route('dashboard')"
                            class="group flex shrink-0 items-center gap-3 rounded-xl outline-none ring-brand-500/0 transition focus-visible:ring-2 focus-visible:ring-brand-500"
                        >
                            <span
                                class="relative flex h-10 w-10 items-center justify-center rounded-2xl bg-gradient-to-br from-brand-600 via-brand-500 to-brand-700 shadow-lg shadow-brand-600/30 ring-1 ring-white/25 transition group-hover:shadow-ember-glow"
                            >
                                <ApplicationLogo variant="inverse" class="relative z-10 h-8 w-8" />
                                <span
                                    class="absolute -right-0.5 -top-0.5 h-2.5 w-2.5 rounded-full bg-ember-400 ring-2 ring-white"
                                    aria-hidden="true"
                                />
                            </span>
                            <span class="hidden flex-col sm:flex">
                                <span class="truncate text-sm font-bold leading-tight text-slate-900">Espace organisateur</span>
                                <span
                                    class="text-[11px] font-semibold uppercase tracking-[0.14em] text-brand-700/90"
                                    >Organizz</span
                                >
                            </span>
                        </Link>

                        <div class="hidden space-x-1 sm:ms-6 sm:flex sm:items-center">
                            <NavLink :href="route('dashboard')" :active="route().current('dashboard')">
                                Tableau de bord
                            </NavLink>
                        </div>
                    </div>

                    <div class="hidden sm:ms-6 sm:flex sm:items-center">
                        <div class="relative">
                            <Dropdown align="right" width="48">
                                <template #trigger>
                                    <span class="inline-flex rounded-xl">
                                        <button
                                            type="button"
                                            class="inline-flex items-center gap-2 rounded-xl border border-slate-200/90 bg-white/90 px-3 py-2 text-sm font-medium text-slate-800 shadow-sm transition hover:border-brand-200 hover:bg-brand-50/50 focus:outline-none focus:ring-2 focus:ring-brand-500/25"
                                        >
                                            <span class="max-w-[10rem] truncate">{{ $page.props.auth.user.name }}</span>
                                            <svg class="h-4 w-4 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </span>
                                </template>

                                <template #content>
                                    <DropdownLink :href="route('profile.edit')">Mon profil</DropdownLink>
                                    <DropdownLink :href="route('logout')" method="post" as="button"> Déconnexion </DropdownLink>
                                </template>
                            </Dropdown>
                        </div>
                    </div>

                    <div class="-me-2 flex items-center sm:hidden">
                        <button
                            type="button"
                            class="inline-flex items-center justify-center rounded-lg p-2 text-slate-500 transition hover:bg-slate-100 hover:text-slate-700"
                            @click="showingNavigationDropdown = !showingNavigationDropdown"
                        >
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path
                                    :class="{ hidden: showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16"
                                />
                                <path
                                    :class="{ hidden: !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"
                                />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <div :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }" class="border-t border-slate-100 bg-white sm:hidden">
                <div class="space-y-1 px-4 pb-3 pt-2">
                    <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')">
                        Tableau de bord
                    </ResponsiveNavLink>
                </div>

                <div class="border-t border-slate-100 pb-3 pt-3">
                    <div class="px-4">
                        <div class="text-base font-semibold text-slate-900">{{ $page.props.auth.user.name }}</div>
                        <div class="text-sm text-slate-500">{{ $page.props.auth.user.email }}</div>
                    </div>
                    <div class="mt-3 space-y-1 px-2">
                        <ResponsiveNavLink :href="route('profile.edit')">Mon profil</ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('logout')" method="post" as="button"> Déconnexion </ResponsiveNavLink>
                    </div>
                </div>
            </div>
        </nav>

        <header
            v-if="$slots.header"
            class="relative border-b border-slate-200/70 bg-gradient-to-b from-white via-white to-slate-50/80 shadow-sm"
        >
            <div
                class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-brand-400/0 via-brand-400/30 to-ember-400/0"
                aria-hidden="true"
            />
            <div class="relative mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
                <slot name="header" />
            </div>
        </header>

        <main class="relative">
            <slot />
        </main>
    </div>
</template>
