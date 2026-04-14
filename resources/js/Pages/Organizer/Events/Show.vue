<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InputError from '@/Components/InputError.vue';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    event: Object,
    positions: Array,
    stats: Object,
    permissions: {
        type: Object,
        default: () => ({
            configure: true,
            manageRegistrations: true,
            delete: true,
            manageCollaborators: false,
        }),
    },
});

const page = usePage();
const copied = ref(false);

function copyLink() {
    navigator.clipboard.writeText(props.event.public_url);
    copied.value = true;
    setTimeout(() => (copied.value = false), 2000);
}

const slotForm = useForm({
    position_id: props.positions[0]?.id ?? null,
    date: '',
    start_time: '',
    end_time: '',
});

function addSlot() {
    slotForm.post(route('events.slots.store', props.event.id), {
        preserveScroll: true,
        onSuccess: () => slotForm.reset('date', 'start_time', 'end_time'),
    });
}

function deleteSlot(slotId) {
    if (!confirm('Supprimer ce créneau ?')) return;
    router.delete(route('events.slots.destroy', [props.event.id, slotId]), {
        preserveScroll: true,
    });
}

const copiedEmbed = ref(false);

function copyEmbed() {
    if (!props.event.embed_url) return;
    navigator.clipboard.writeText(props.event.embed_url);
    copiedEmbed.value = true;
    setTimeout(() => (copiedEmbed.value = false), 2000);
}

function duplicateEvent() {
    if (
        !confirm(
            'Dupliquer cet événement en brouillon ? Les postes, créneaux et règles de rappel seront copiés.',
        )
    ) {
        return;
    }
    router.post(route('events.duplicate', props.event.id), {});
}
</script>

<template>
    <Head :title="event.title" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col gap-4">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-brand-600/90">Événement</p>
                    <h2 class="mt-1 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
                        {{ event.title }}
                    </h2>
                </div>
                <nav class="flex flex-wrap gap-2" aria-label="Actions événement">
                    <Link
                        v-if="permissions.configure"
                        :href="route('events.edit', event.id)"
                        class="inline-flex items-center rounded-full border border-slate-200/90 bg-white/90 px-3.5 py-1.5 text-sm font-medium text-slate-800 shadow-sm transition hover:border-brand-200 hover:bg-brand-50/80"
                    >
                        Modifier
                    </Link>
                    <Link
                        v-if="permissions.configure"
                        :href="route('events.positions.index', event.id)"
                        class="inline-flex items-center rounded-full border border-slate-200/90 bg-white/90 px-3.5 py-1.5 text-sm font-medium text-slate-800 shadow-sm transition hover:border-ember-200 hover:bg-ember-50/80"
                    >
                        Postes
                    </Link>
                    <Link
                        v-if="permissions.manageRegistrations"
                        :href="route('events.registrations.index', event.id)"
                        class="inline-flex items-center rounded-full border border-slate-200/90 bg-white/90 px-3.5 py-1.5 text-sm font-medium text-slate-800 shadow-sm transition hover:border-brand-200 hover:bg-brand-50/80"
                    >
                        Inscriptions
                    </Link>
                    <Link
                        v-if="permissions.configure"
                        :href="route('events.reminders.index', event.id)"
                        class="inline-flex items-center rounded-full border border-slate-200/90 bg-white/90 px-3.5 py-1.5 text-sm font-medium text-slate-800 shadow-sm transition hover:border-ember-200 hover:bg-ember-50/80"
                    >
                        Rappels
                    </Link>
                    <a
                        v-if="permissions.manageRegistrations"
                        :href="route('events.export', event.id)"
                        class="inline-flex items-center rounded-full border border-slate-200/90 bg-white/90 px-3.5 py-1.5 text-sm font-medium text-slate-800 shadow-sm transition hover:border-brand-200 hover:bg-brand-50/80"
                    >
                        Export CSV
                    </a>
                    <Link
                        v-if="permissions.manageCollaborators"
                        :href="route('events.collaborators.index', event.id)"
                        class="inline-flex items-center rounded-full border border-slate-200/90 bg-white/90 px-3.5 py-1.5 text-sm font-medium text-slate-800 shadow-sm transition hover:border-ember-200 hover:bg-ember-50/80"
                    >
                        Co-organisateurs
                    </Link>
                </nav>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <div
                    v-if="page.props.flash?.success"
                    class="rounded-2xl border border-emerald-200/80 bg-gradient-to-r from-emerald-50 to-teal-50/80 p-4 text-sm font-medium text-emerald-900 shadow-sm"
                >
                    {{ page.props.flash.success }}
                </div>

                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div
                        class="organizer-card relative overflow-hidden border-t-4 border-t-brand-500 p-5"
                    >
                        <div class="flex items-start gap-3">
                            <span
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-brand-500 to-brand-600 text-white shadow-md shadow-brand-600/25"
                            >
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"
                                    />
                                </svg>
                            </span>
                            <div class="min-w-0">
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Inscriptions actives</p>
                                <p class="mt-1 text-3xl font-bold tabular-nums text-slate-900">{{ stats.total_registrations }}</p>
                            </div>
                        </div>
                    </div>
                    <div
                        class="organizer-card relative overflow-hidden border-t-4 border-t-ember-500 p-5"
                    >
                        <div class="flex items-start gap-3">
                            <span
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-ember-400 to-ember-600 text-white shadow-md shadow-ember-500/30"
                            >
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                                    />
                                </svg>
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Remplissage créneaux</p>
                                <p class="mt-1 text-3xl font-bold tabular-nums text-slate-900">{{ stats.fill_rate }}%</p>
                                <p
                                    v-if="stats.j3_critical"
                                    class="mt-2 rounded-lg bg-amber-100/90 px-2 py-1 text-xs font-semibold text-amber-900"
                                >
                                    Alerte J-3 : moins de 50 % des créneaux sont complets.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div
                        class="organizer-card relative overflow-hidden border-t-4 border-t-brand-400 p-5 sm:col-span-1"
                    >
                        <div class="flex items-start gap-3">
                            <span
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-slate-900 text-white shadow-md"
                            >
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"
                                    />
                                </svg>
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Lien public</p>
                                <SecondaryButton type="button" class="mt-3 w-full sm:w-auto" @click="copyLink">
                                    {{ copied ? 'Copié !' : 'Copier le lien' }}
                                </SecondaryButton>
                                <p class="mt-2 break-all text-[11px] leading-relaxed text-slate-500">{{ event.public_url }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="organizer-card relative overflow-hidden border-t-4 border-t-slate-700 p-5">
                        <div class="flex items-start gap-3">
                            <span
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-slate-700 to-slate-900 text-white shadow-md"
                            >
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"
                                    />
                                </svg>
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">QR & intégration</p>
                                <div class="mt-3 flex flex-wrap gap-2">
                                    <a
                                        :href="event.qr_url"
                                        target="_blank"
                                        rel="noopener"
                                        class="inline-flex items-center rounded-lg bg-brand-50 px-2.5 py-1 text-xs font-semibold text-brand-800 ring-1 ring-brand-200/80 transition hover:bg-brand-100"
                                        >QR PNG</a
                                    >
                                    <SecondaryButton v-if="event.embed_url" type="button" class="!py-1 text-xs" @click="copyEmbed">
                                        {{ copiedEmbed ? 'Copié !' : 'Iframe' }}
                                    </SecondaryButton>
                                </div>
                                <p v-if="event.embed_url" class="mt-2 break-all text-[11px] text-slate-500">
                                    &lt;iframe src="{{ event.embed_url }}" width="100%" height="800"&gt;&lt;/iframe&gt;
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    v-if="stats.signup_timeline && stats.signup_timeline.length"
                    class="organizer-card p-5"
                >
                    <h3 class="text-sm font-semibold text-slate-800">Inscriptions dans le temps</h3>
                    <div class="mt-4 flex h-28 items-end gap-1">
                        <div
                            v-for="row in stats.signup_timeline"
                            :key="row.date"
                            class="flex min-w-0 flex-1 flex-col items-center justify-end"
                            :title="row.date + ': ' + row.count"
                        >
                            <div
                                class="w-full rounded-t bg-gradient-to-t from-brand-600 to-brand-400 shadow-sm"
                                :style="{
                                    height:
                                        Math.max(8, Math.min(80, row.count * 10)) + 'px',
                                }"
                            />
                            <span class="mt-1 truncate text-[10px] font-medium text-slate-500">{{ row.date.slice(5) }}</span>
                        </div>
                    </div>
                </div>

                <div v-if="permissions.configure" class="flex flex-wrap gap-2">
                    <SecondaryButton type="button" @click="duplicateEvent">Dupliquer l’événement</SecondaryButton>
                </div>

                <div v-if="permissions.configure" class="organizer-card organizer-card-deco p-6 sm:p-8">
                    <div class="organizer-card-inner">
                        <div class="flex flex-wrap items-end justify-between gap-3">
                            <div>
                                <h3 class="text-lg font-bold text-slate-900">Ajouter un créneau manuellement</h3>
                                <p class="mt-1 text-sm text-slate-600">
                                    Rattaché à un poste — pratique pour un horaire exceptionnel.
                                </p>
                            </div>
                            <span
                                class="hidden rounded-full bg-gradient-to-r from-brand-500/15 to-ember-500/15 px-3 py-1 text-xs font-semibold text-slate-700 ring-1 ring-slate-200/80 sm:inline-flex"
                                >Manuel</span
                            >
                        </div>
                        <form class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-6" @submit.prevent="addSlot">
                            <div>
                                <label class="text-sm font-medium text-slate-700">Poste</label>
                                <select v-model="slotForm.position_id" class="organizer-input" required>
                                    <option v-for="p in positions" :key="p.id" :value="p.id">
                                        {{ p.name }}
                                    </option>
                                </select>
                                <InputError class="mt-1" :message="slotForm.errors.position_id" />
                            </div>
                            <div>
                                <label class="text-sm font-medium text-slate-700">Date</label>
                                <input v-model="slotForm.date" type="date" class="organizer-input" required />
                                <InputError class="mt-1" :message="slotForm.errors.date" />
                            </div>
                            <div>
                                <label class="text-sm font-medium text-slate-700">Début</label>
                                <input v-model="slotForm.start_time" type="time" class="organizer-input" required />
                                <InputError class="mt-1" :message="slotForm.errors.start_time" />
                            </div>
                            <div>
                                <label class="text-sm font-medium text-slate-700">Fin</label>
                                <input v-model="slotForm.end_time" type="time" class="organizer-input" required />
                                <InputError class="mt-1" :message="slotForm.errors.end_time" />
                            </div>
                            <div class="flex items-end lg:col-span-2">
                                <PrimaryButton :disabled="slotForm.processing" class="w-full sm:w-auto">Ajouter</PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>

                <div>
                    <h3 class="mb-1 text-[11px] font-bold uppercase tracking-[0.18em] text-brand-600/90">
                        Créneaux par poste
                    </h3>
                    <p class="mb-5 text-sm text-slate-600">
                        Ouvrez une carte pour afficher les horaires — moins de défilement, plus de clarté.
                    </p>

                    <div
                        v-if="!positions.length"
                        class="rounded-3xl border-2 border-dashed border-slate-300/90 bg-white/60 p-10 text-center text-slate-600 shadow-inner"
                    >
                        Aucun poste. Ajoutez-en depuis
                        <Link
                            v-if="permissions.configure"
                            :href="route('events.positions.index', event.id)"
                            class="font-medium text-brand-700 hover:underline"
                            >Postes</Link
                        >.
                    </div>

                    <div v-else class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                        <details
                            v-for="(pos, idx) in positions"
                            :key="pos.id"
                            class="group organizer-card open:ring-2 open:ring-brand-200/80"
                            :open="idx === 0"
                        >
                            <summary
                                class="flex cursor-pointer list-none items-start gap-3 rounded-3xl p-5 transition [&::-webkit-details-marker]:hidden hover:bg-slate-50/80"
                            >
                                <span
                                    class="mt-0.5 flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl shadow-inner ring-2 ring-white"
                                    :style="{ background: pos.color }"
                                />
                                <div class="min-w-0 flex-1 text-left">
                                    <p class="text-lg font-bold leading-tight text-slate-900">{{ pos.name }}</p>
                                    <p class="mt-1 text-sm text-slate-600">
                                        {{ pos.slots.length }} créneau(x)
                                        <span
                                            v-if="pos.stats.critical_slots"
                                            class="ml-1 inline-flex items-center rounded-full bg-amber-100 px-2 py-0.5 text-xs font-semibold text-amber-900"
                                        >
                                            {{ pos.stats.critical_slots }} sous 50&nbsp;%
                                        </span>
                                    </p>
                                </div>
                                <span
                                    class="shrink-0 rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600 ring-1 ring-slate-200/80 group-open:hidden"
                                >
                                    Ouvrir
                                </span>
                                <span
                                    class="hidden shrink-0 rounded-full bg-gradient-to-r from-brand-500 to-brand-600 px-2.5 py-1 text-xs font-semibold text-white shadow-sm group-open:inline"
                                >
                                    Fermer
                                </span>
                            </summary>

                            <div class="border-t border-slate-100/90 px-4 pb-5 pt-3">
                                <ul v-if="pos.slots.length" class="max-h-[min(420px,55vh)] space-y-2 overflow-y-auto pr-1">
                                    <li
                                        v-for="s in pos.slots"
                                        :key="s.id"
                                        class="rounded-2xl border border-slate-100 bg-gradient-to-br from-white to-slate-50/90 p-3 shadow-sm"
                                    >
                                        <div
                                            class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between sm:gap-3"
                                        >
                                            <div class="min-w-0 text-sm text-slate-800">
                                                <span class="font-semibold tabular-nums text-slate-900">{{ s.date }}</span>
                                                <span class="text-slate-600">
                                                    · {{ String(s.start_time).slice(0, 5) }}–{{
                                                        String(s.end_time).slice(0, 5)
                                                    }}
                                                    · {{ s.active_count }}/{{ s.max_volunteers }} inscrits
                                                </span>
                                            </div>
                                            <div class="flex shrink-0 items-center gap-3">
                                                <div class="h-2.5 w-full min-w-[6rem] overflow-hidden rounded-full bg-slate-200/90 sm:w-28">
                                                    <div
                                                        class="h-full rounded-full bg-gradient-to-r from-brand-500 via-brand-400 to-ember-400 transition-all"
                                                        :style="{ width: Math.min(100, s.percent) + '%' }"
                                                    />
                                                </div>
                                                <SecondaryButton
                                                    v-if="permissions.configure"
                                                    type="button"
                                                    class="!py-1.5 text-xs"
                                                    @click="deleteSlot(s.id)"
                                                >
                                                    Supprimer
                                                </SecondaryButton>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                                <p v-else class="px-1 py-4 text-sm text-slate-500">
                                    Aucun créneau — utilisez l’écran
                                    <Link
                                        v-if="permissions.configure"
                                        :href="route('events.positions.index', event.id)"
                                        class="font-medium text-brand-700 hover:underline"
                                        >Postes</Link
                                    >
                                    pour générer la grille.
                                </p>
                            </div>
                        </details>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
