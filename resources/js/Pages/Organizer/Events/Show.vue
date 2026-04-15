<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

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
    slotForm.post(route('evenements.creneaux.enregistrer', props.event.slug), {
        preserveScroll: true,
        onSuccess: () => slotForm.reset('date', 'start_time', 'end_time'),
    });
}

function deleteSlot(slotId) {
    if (!confirm('Supprimer ce créneau ?')) return;
    router.delete(route('evenements.creneaux.supprimer', [props.event.slug, slotId]), {
        preserveScroll: true,
    });
}

const showEditSlot = ref(false);
const editingSlotId = ref(null);
const editingSlotBooked = ref(0);

const editSlotForm = useForm({
    date: '',
    start_time: '',
    end_time: '',
    max_volunteers: 1,
});

function openEditSlot(s) {
    editingSlotId.value = s.id;
    editingSlotBooked.value = s.active_count ?? 0;
    editSlotForm.date = s.date;
    editSlotForm.start_time = String(s.start_time).slice(0, 5);
    editSlotForm.end_time = String(s.end_time).slice(0, 5);
    editSlotForm.max_volunteers = s.max_volunteers;
    editSlotForm.clearErrors();
    showEditSlot.value = true;
}

function closeEditSlot() {
    showEditSlot.value = false;
    editingSlotId.value = null;
    editSlotForm.clearErrors();
}

function submitEditSlot() {
    if (!editingSlotId.value) return;
    editSlotForm.patch(route('evenements.creneaux.modifier', [props.event.slug, editingSlotId.value]), {
        preserveScroll: true,
        onSuccess: () => closeEditSlot(),
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
            'Dupliquer cet événement ? Les postes, créneaux et règles de rappel seront copiés dans un nouvel événement ouvert.',
        )
    ) {
        return;
    }
    router.post(route('evenements.dupliquer', props.event.slug), {});
}

const regToggle = useForm({
    registration_enabled: props.event.registration_enabled !== false,
});

watch(
    () => props.event.registration_enabled,
    (v) => {
        regToggle.registration_enabled = v !== false;
    },
);

function setRegistrationEnabled(checked) {
    regToggle.registration_enabled = checked;
    regToggle.patch(route('evenements.inscriptions_publiques', props.event.slug), {
        preserveScroll: true,
    });
}

function scrollToCreneaux() {
    document.getElementById('section-creneaux')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
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
                        :href="route('evenements.editer', event.slug)"
                        class="inline-flex items-center rounded-full border border-slate-200/90 bg-white/90 px-3.5 py-1.5 text-sm font-medium text-slate-800 shadow-sm transition hover:border-brand-200 hover:bg-brand-50/80"
                    >
                        Modifier
                    </Link>
                    <Link
                        v-if="permissions.configure"
                        :href="route('evenements.postes.index', event.slug)"
                        class="inline-flex items-center rounded-full border border-slate-200/90 bg-white/90 px-3.5 py-1.5 text-sm font-medium text-slate-800 shadow-sm transition hover:border-ember-200 hover:bg-ember-50/80"
                    >
                        Postes
                    </Link>
                    <Link
                        v-if="permissions.manageRegistrations"
                        :href="route('evenements.inscriptions.index', event.slug)"
                        class="inline-flex items-center rounded-full border border-slate-200/90 bg-white/90 px-3.5 py-1.5 text-sm font-medium text-slate-800 shadow-sm transition hover:border-brand-200 hover:bg-brand-50/80"
                    >
                        Inscriptions
                    </Link>
                    <Link
                        v-if="permissions.configure"
                        :href="route('evenements.rappels.index', event.slug)"
                        class="inline-flex items-center rounded-full border border-slate-200/90 bg-white/90 px-3.5 py-1.5 text-sm font-medium text-slate-800 shadow-sm transition hover:border-ember-200 hover:bg-ember-50/80"
                    >
                        Rappels
                    </Link>
                    <a
                        v-if="permissions.manageRegistrations"
                        :href="route('evenements.export', event.slug)"
                        class="inline-flex items-center rounded-full border border-slate-200/90 bg-white/90 px-3.5 py-1.5 text-sm font-medium text-slate-800 shadow-sm transition hover:border-brand-200 hover:bg-brand-50/80"
                    >
                        Exporter (CSV)
                    </a>
                    <Link
                        v-if="permissions.manageCollaborators"
                        :href="route('evenements.coorganisateurs.index', event.slug)"
                        class="inline-flex items-center rounded-full border border-slate-200/90 bg-white/90 px-3.5 py-1.5 text-sm font-medium text-slate-800 shadow-sm transition hover:border-ember-200 hover:bg-ember-50/80"
                    >
                        Co-organisateurs
                    </Link>
                </nav>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-5xl space-y-5 sm:px-6 lg:px-8">
                <div
                    v-if="page.props.flash?.success"
                    class="rounded-2xl border border-emerald-200/80 bg-gradient-to-r from-emerald-50 to-teal-50/80 p-4 text-sm font-medium text-emerald-900 shadow-sm"
                >
                    {{ page.props.flash.success }}
                </div>

                <!-- Postes en premier : visible sans scroller -->
                <section
                    id="section-postes"
                    class="organizer-card scroll-mt-24 border-t-4 border-t-violet-500 p-4 sm:p-5"
                    aria-labelledby="postes-heading"
                >
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <h3 id="postes-heading" class="text-[11px] font-bold uppercase tracking-[0.18em] text-violet-700/90">
                                Postes de l’événement
                            </h3>
                            <p class="mt-1 text-sm text-slate-600">Rôles (accueil, bar…) et grille horaire — configuration dans Postes.</p>
                        </div>
                        <div class="flex flex-col gap-2 sm:flex-row sm:flex-wrap sm:justify-end">
                            <SecondaryButton
                                type="button"
                                class="inline-flex w-full items-center justify-center gap-2 sm:w-auto"
                                @click="scrollToCreneaux"
                            >
                                Aller aux créneaux
                                <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                                </svg>
                            </SecondaryButton>
                            <Link
                                v-if="permissions.configure"
                                :href="route('evenements.postes.index', event.slug)"
                                class="inline-flex shrink-0 items-center justify-center rounded-xl bg-gradient-to-r from-violet-600 to-violet-500 px-4 py-2.5 text-sm font-semibold text-white shadow-md shadow-violet-500/25 transition hover:from-violet-700 hover:to-violet-600"
                            >
                                Gérer les postes
                            </Link>
                            <Link
                                v-else
                                :href="route('evenements.postes.index', event.slug)"
                                class="inline-flex shrink-0 items-center justify-center rounded-xl border border-violet-200 bg-white px-4 py-2.5 text-sm font-semibold text-violet-900 shadow-sm transition hover:bg-violet-50"
                            >
                                Voir les postes
                            </Link>
                        </div>
                    </div>

                    <div
                        v-if="!positions.length"
                        class="mt-4 rounded-2xl border border-dashed border-violet-200/90 bg-violet-50/50 px-4 py-6 text-center text-sm text-slate-700"
                    >
                        Aucun poste pour l’instant.
                        <Link
                            v-if="permissions.configure"
                            :href="route('evenements.postes.index', event.slug)"
                            class="font-semibold text-violet-800 underline decoration-violet-300 underline-offset-2 hover:text-violet-950"
                            >Créer un poste</Link
                        >
                        pour générer les créneaux.
                    </div>
                    <ul v-else class="mt-3 grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
                        <li
                            v-for="p in positions"
                            :key="p.id"
                            class="flex min-w-0 items-center gap-3 rounded-xl border border-slate-100 bg-white/90 px-3 py-2.5 shadow-sm ring-1 ring-slate-100/80"
                        >
                            <span
                                class="h-9 w-9 shrink-0 rounded-xl shadow-inner ring-2 ring-white"
                                :style="{ background: p.color }"
                                aria-hidden="true"
                            />
                            <div class="min-w-0 flex-1">
                                <p class="truncate font-semibold text-slate-900">{{ p.name }}</p>
                                <p class="text-xs text-slate-500">
                                    {{ p.slots.length }} créneau(x)
                                    <span v-if="p.stats?.critical_slots" class="text-amber-800">
                                        · {{ p.stats.critical_slots }} sous 50&nbsp;%
                                    </span>
                                </p>
                            </div>
                        </li>
                    </ul>
                    <p v-if="positions.length" class="mt-2 text-xs text-slate-500">
                        <Link
                            :href="route('evenements.postes.index', event.slug)"
                            class="font-medium text-violet-800 hover:underline"
                            >Postes</Link
                        >
                        — durées, régénération de grille, nouveaux rôles.
                    </p>
                </section>

                <div
                    v-if="permissions.configure && event.status === 'open'"
                    class="organizer-card border-t-4 border-t-emerald-500 p-4 sm:p-5"
                >
                    <label class="flex cursor-pointer items-start gap-3">
                        <input
                            type="checkbox"
                            class="mt-1 h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500 disabled:opacity-50"
                            :checked="regToggle.registration_enabled"
                            :disabled="regToggle.processing"
                            @change="setRegistrationEnabled($event.target.checked)"
                        />
                        <span>
                            <span class="font-semibold text-slate-900">Inscriptions sur la page publique</span>
                            <span class="mt-1 block text-sm text-slate-600">
                                Décochez pour empêcher de nouvelles inscriptions tout en laissant la page consultable (créneaux visibles).
                            </span>
                        </span>
                    </label>
                </div>
                <div
                    v-else-if="permissions.configure && event.status !== 'open'"
                    class="rounded-2xl border border-amber-200/80 bg-amber-50/90 p-4 text-sm text-amber-950"
                >
                    <span class="font-semibold">Inscriptions fermées</span>
                    <span class="mt-1 block text-amber-900/90">
                        Le statut n’est pas « Ouvert » — passez par « Modifier » pour changer le statut si vous souhaitez rouvrir les inscriptions.
                    </span>
                </div>

                <!-- Bandeau stats : une seule ligne dense (pas de grille de grosses cartes) -->
                <section
                    class="rounded-xl border border-slate-200/90 bg-white px-3 py-2 shadow-sm ring-1 ring-slate-100/80"
                    aria-label="Indicateurs rapides"
                >
                    <div
                        class="flex flex-col gap-2 text-sm leading-snug text-slate-800 sm:flex-row sm:flex-wrap sm:items-center sm:gap-x-1 sm:gap-y-1"
                    >
                        <span class="sr-only">Indicateurs :</span>
                        <span class="font-semibold tabular-nums text-slate-900">
                            {{ stats.total_registrations }}/{{ stats.capacity_places }}
                        </span>
                        <span class="hidden text-slate-300 sm:inline" aria-hidden="true">·</span>
                        <span class="flex min-w-0 items-center gap-2 sm:inline-flex">
                            <span
                                class="inline-block h-1.5 max-w-[5rem] flex-1 overflow-hidden rounded-full bg-slate-200 sm:max-w-[6rem] sm:flex-none sm:w-20"
                            >
                                <span
                                    class="block h-full rounded-full bg-gradient-to-r from-brand-500 to-ember-400"
                                    :style="{ width: Math.min(100, stats.fill_percent_places) + '%' }"
                                />
                            </span>
                            <span class="text-xs text-slate-600">{{ stats.fill_percent_places }}%</span>
                            <span class="text-xs text-slate-500">({{ stats.spots_open }} libres)</span>
                        </span>
                        <span class="hidden text-slate-300 sm:inline" aria-hidden="true">·</span>
                        <span class="text-xs text-slate-600">
                            <span class="font-medium text-slate-500">Créneaux</span>
                            {{ stats.slots_count }}
                            <span class="text-slate-400">·</span>
                            {{ stats.fill_rate }}% complets
                        </span>
                        <span class="hidden text-slate-300 sm:inline" aria-hidden="true">·</span>
                        <span class="text-xs text-slate-600">
                            <span class="font-medium text-slate-500">Attente</span>
                            {{ stats.waitlist_count }}
                        </span>
                        <span class="hidden text-slate-300 sm:inline" aria-hidden="true">·</span>
                        <span class="text-xs font-semibold tabular-nums text-slate-900">
                            <template v-if="stats.days_until_start > 0">J-{{ stats.days_until_start }}</template>
                            <template v-else-if="stats.days_until_start === 0">Jour J</template>
                            <template v-else>Passé</template>
                        </span>
                    </div>
                    <p
                        v-if="stats.j3_critical"
                        class="mt-2 border-t border-amber-100/90 pt-2 text-[11px] font-semibold text-amber-950"
                    >
                        Alerte J-3 : moins de 50&nbsp;% des créneaux sont complets.
                    </p>
                    <details class="mt-1.5 border-t border-slate-100 pt-1.5 text-[11px] text-slate-500">
                        <summary class="cursor-pointer select-none font-medium text-slate-600 hover:text-slate-800">
                            Détail des chiffres
                        </summary>
                        <ul class="mt-1.5 space-y-0.5 pl-0.5">
                            <li>Capacité : {{ stats.capacity_places }} places (tous créneaux).</li>
                            <li>Places restantes : {{ stats.spots_open }} (hors liste d’attente).</li>
                            <li>Part de créneaux « complet » : {{ stats.fill_rate }}%.</li>
                        </ul>
                    </details>
                </section>

                <div class="grid gap-3 sm:grid-cols-2">
                    <div class="organizer-card relative overflow-hidden border-t-4 border-t-brand-400 p-4 sm:p-4">
                        <div class="flex items-start gap-2.5">
                            <span
                                class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-slate-900 text-white shadow-md"
                            >
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"
                                    />
                                </svg>
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-500">Lien public</p>
                                <SecondaryButton type="button" class="mt-2 w-full sm:w-auto" @click="copyLink">
                                    {{ copied ? 'Copié !' : 'Copier le lien' }}
                                </SecondaryButton>
                                <p class="mt-1.5 break-all text-[10px] leading-relaxed text-slate-500">{{ event.public_url }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="organizer-card relative overflow-hidden border-t-4 border-t-slate-700 p-4 sm:p-4">
                        <div class="flex items-start gap-2.5">
                            <span
                                class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-slate-700 to-slate-900 text-white shadow-md"
                            >
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"
                                    />
                                </svg>
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-500">QR & intégration</p>
                                <div class="mt-2 flex flex-wrap gap-2">
                                    <a
                                        :href="event.qr_url"
                                        target="_blank"
                                        rel="noopener"
                                        class="inline-flex items-center rounded-lg bg-brand-50 px-2.5 py-1 text-xs font-semibold text-brand-800 ring-1 ring-brand-200/80 transition hover:bg-brand-100"
                                        >QR (PNG)</a
                                    >
                                    <SecondaryButton v-if="event.embed_url" type="button" class="!py-1 text-xs" @click="copyEmbed">
                                        {{ copiedEmbed ? 'Copié !' : 'Intégration' }}
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
                    class="organizer-card p-4"
                >
                    <h3 class="text-sm font-semibold text-slate-800">Inscriptions dans le temps</h3>
                    <div class="mt-3 flex h-20 items-end gap-1">
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

                <div id="section-creneaux" class="scroll-mt-24">
                    <h3 class="mb-1 text-[11px] font-bold uppercase tracking-[0.18em] text-brand-600/90">
                        Créneaux par poste
                    </h3>
                    <p class="mb-5 text-sm text-slate-600">
                        Jusqu’à trois fiches par ligne sur grand écran — ouvrez une fiche pour voir les horaires et les actions.
                    </p>

                    <div
                        v-if="!positions.length"
                        class="rounded-3xl border-2 border-dashed border-slate-300/90 bg-white/60 p-10 text-center text-slate-600 shadow-inner"
                    >
                        Aucun poste. Ajoutez-en depuis
                        <Link
                            v-if="permissions.configure"
                            :href="route('evenements.postes.index', event.slug)"
                            class="font-medium text-brand-700 hover:underline"
                            >Postes</Link
                        >.
                    </div>

                    <div v-else class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
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
                                                    @click="openEditSlot(s)"
                                                >
                                                    Modifier
                                                </SecondaryButton>
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
                                        :href="route('evenements.postes.index', event.slug)"
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

        <Modal :show="showEditSlot" max-width="lg" @close="closeEditSlot">
            <div class="p-6">
                <h2 class="text-lg font-semibold text-slate-900">Modifier le créneau</h2>
                <p class="mt-1 text-sm text-slate-600">
                    Ajustez la date, les horaires et la capacité. Le nombre de places ne peut pas être inférieur au nombre
                    d’inscriptions actives ({{ editingSlotBooked }}).
                </p>
                <form class="mt-6 space-y-4" @submit.prevent="submitEditSlot">
                    <div>
                        <InputLabel for="edit_slot_date" value="Date" />
                        <input
                            id="edit_slot_date"
                            v-model="editSlotForm.date"
                            type="date"
                            class="organizer-input mt-1 w-full max-w-xs"
                            :min="event.date_start"
                            :max="event.date_end"
                            required
                        />
                        <InputError class="mt-1" :message="editSlotForm.errors.date" />
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <InputLabel for="edit_slot_start" value="Début" />
                            <input
                                id="edit_slot_start"
                                v-model="editSlotForm.start_time"
                                type="time"
                                class="organizer-input mt-1 w-full"
                                required
                            />
                            <InputError class="mt-1" :message="editSlotForm.errors.start_time" />
                        </div>
                        <div>
                            <InputLabel for="edit_slot_end" value="Fin" />
                            <input
                                id="edit_slot_end"
                                v-model="editSlotForm.end_time"
                                type="time"
                                class="organizer-input mt-1 w-full"
                                required
                            />
                            <InputError class="mt-1" :message="editSlotForm.errors.end_time" />
                        </div>
                    </div>
                    <div>
                        <InputLabel for="edit_slot_max" value="Places" />
                        <input
                            id="edit_slot_max"
                            v-model.number="editSlotForm.max_volunteers"
                            type="number"
                            class="organizer-input mt-1 w-full max-w-xs"
                            :min="Math.max(1, editingSlotBooked)"
                            max="500"
                            required
                        />
                        <InputError class="mt-1" :message="editSlotForm.errors.max_volunteers" />
                    </div>
                    <div class="flex justify-end gap-2 pt-2">
                        <SecondaryButton type="button" @click="closeEditSlot">Annuler</SecondaryButton>
                        <PrimaryButton :disabled="editSlotForm.processing">Enregistrer</PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
