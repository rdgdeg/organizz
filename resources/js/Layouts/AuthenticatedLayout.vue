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
    <div class="min-h-screen bg-slate-50">
        <nav class="sticky top-0 z-50 border-b border-slate-200/80 bg-white/90 shadow-sm backdrop-blur-md">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 justify-between">
                    <div class="flex min-w-0 flex-1 items-center gap-3">
                        <Link :href="route('dashboard')" class="flex shrink-0 items-center gap-3 rounded-lg outline-none ring-brand-500/0 transition focus-visible:ring-2 focus-visible:ring-brand-500">
                            <ApplicationLogo />
                            <span class="hidden flex-col sm:flex">
                                <span class="truncate text-sm font-bold leading-tight text-slate-900">Espace organisateur</span>
                                <span class="text-[11px] font-medium uppercase tracking-wider text-slate-500">Planification</span>
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
                                            class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 shadow-sm transition hover:border-slate-300 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-brand-500/20"
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

        <header v-if="$slots.header" class="border-b border-slate-200/80 bg-white shadow-sm">
            <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
                <slot name="header" />
            </div>
        </header>

        <main>
            <slot />
        </main>
    </div>
</template>
