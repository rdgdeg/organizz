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
            <div class="flex flex-col gap-2">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    {{ event.title }}
                </h2>
                <div class="flex flex-wrap gap-2 text-sm">
                    <Link
                        v-if="permissions.configure"
                        :href="route('events.edit', event.id)"
                        class="text-brand-700 hover:underline"
                    >
                        Modifier
                    </Link>
                    <span v-if="permissions.configure" class="text-gray-300">|</span>
                    <Link
                        v-if="permissions.configure"
                        :href="route('events.positions.index', event.id)"
                        class="text-brand-700 hover:underline"
                    >
                        Postes
                    </Link>
                    <span v-if="permissions.configure" class="text-gray-300">|</span>
                    <Link
                        v-if="permissions.manageRegistrations"
                        :href="route('events.registrations.index', event.id)"
                        class="text-brand-700 hover:underline"
                    >
                        Inscriptions
                    </Link>
                    <span v-if="permissions.manageRegistrations" class="text-gray-300">|</span>
                    <Link
                        v-if="permissions.configure"
                        :href="route('events.reminders.index', event.id)"
                        class="text-brand-700 hover:underline"
                    >
                        Rappels
                    </Link>
                    <span v-if="permissions.configure" class="text-gray-300">|</span>
                    <a
                        v-if="permissions.manageRegistrations"
                        :href="route('events.export', event.id)"
                        class="text-brand-700 hover:underline"
                    >
                        Export CSV
                    </a>
                    <span v-if="permissions.manageCollaborators" class="text-gray-300">|</span>
                    <Link
                        v-if="permissions.manageCollaborators"
                        :href="route('events.collaborators.index', event.id)"
                        class="text-brand-700 hover:underline"
                    >
                        Co-organisateurs
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <div
                    v-if="page.props.flash?.success"
                    class="rounded-md bg-green-50 p-4 text-sm text-green-800"
                >
                    {{ page.props.flash.success }}
                </div>

                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="rounded-lg bg-white p-4 shadow">
                        <p class="text-sm text-gray-500">Inscriptions actives</p>
                        <p class="text-2xl font-semibold">{{ stats.total_registrations }}</p>
                    </div>
                    <div class="rounded-lg bg-white p-4 shadow">
                        <p class="text-sm text-gray-500">Taux de remplissage (créneaux)</p>
                        <p class="text-2xl font-semibold">{{ stats.fill_rate }}%</p>
                        <p v-if="stats.j3_critical" class="mt-2 text-xs font-semibold text-amber-800">
                            Alerte J-3 : moins de 50 % des créneaux sont complets.
                        </p>
                    </div>
                    <div class="rounded-lg bg-white p-4 shadow">
                        <p class="text-sm text-gray-500">Lien public</p>
                        <SecondaryButton type="button" class="mt-2" @click="copyLink">
                            {{ copied ? 'Copié !' : 'Copier le lien' }}
                        </SecondaryButton>
                        <p class="mt-2 break-all text-xs text-gray-500">{{ event.public_url }}</p>
                    </div>
                    <div class="rounded-lg bg-white p-4 shadow">
                        <p class="text-sm text-gray-500">QR & intégration</p>
                        <div class="mt-2 flex flex-wrap gap-2">
                            <a
                                :href="event.qr_url"
                                target="_blank"
                                rel="noopener"
                                class="text-sm text-brand-700 hover:underline"
                                >QR PNG</a
                            >
                            <SecondaryButton v-if="event.embed_url" type="button" class="!py-1 text-xs" @click="copyEmbed">
                                {{ copiedEmbed ? 'Copié !' : 'Iframe' }}
                            </SecondaryButton>
                        </div>
                        <p v-if="event.embed_url" class="mt-1 break-all text-[11px] text-gray-500">
                            &lt;iframe src="{{ event.embed_url }}" width="100%" height="800"&gt;&lt;/iframe&gt;
                        </p>
                    </div>
                </div>

                <div
                    v-if="stats.signup_timeline && stats.signup_timeline.length"
                    class="rounded-lg bg-white p-4 shadow"
                >
                    <h3 class="text-sm font-medium text-gray-700">Inscriptions dans le temps</h3>
                    <div class="mt-3 flex h-28 items-end gap-1">
                        <div
                            v-for="row in stats.signup_timeline"
                            :key="row.date"
                            class="flex min-w-0 flex-1 flex-col items-center justify-end"
                            :title="row.date + ': ' + row.count"
                        >
                            <div
                                class="w-full rounded-t bg-brand-500"
                                :style="{
                                    height:
                                        Math.max(8, Math.min(80, row.count * 10)) + 'px',
                                }"
                            />
                            <span class="mt-1 truncate text-[10px] text-gray-500">{{ row.date.slice(5) }}</span>
                        </div>
                    </div>
                </div>

                <div v-if="permissions.configure" class="flex flex-wrap gap-2">
                    <SecondaryButton type="button" @click="duplicateEvent">Dupliquer l’événement</SecondaryButton>
                </div>

                <div v-if="permissions.configure" class="rounded-lg bg-white p-6 shadow">
                    <h3 class="text-lg font-medium">Ajouter un créneau manuellement</h3>
                    <form class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-5" @submit.prevent="addSlot">
                        <div>
                            <label class="text-sm text-gray-700">Poste</label>
                            <select
                                v-model="slotForm.position_id"
                                class="mt-1 block w-full rounded-md border-gray-300"
                                required
                            >
                                <option v-for="p in positions" :key="p.id" :value="p.id">
                                    {{ p.name }}
                                </option>
                            </select>
                            <InputError class="mt-1" :message="slotForm.errors.position_id" />
                        </div>
                        <div>
                            <label class="text-sm text-gray-700">Date</label>
                            <input
                                v-model="slotForm.date"
                                type="date"
                                class="mt-1 block w-full rounded-md border-gray-300"
                                required
                            />
                            <InputError class="mt-1" :message="slotForm.errors.date" />
                        </div>
                        <div>
                            <label class="text-sm text-gray-700">Début</label>
                            <input
                                v-model="slotForm.start_time"
                                type="time"
                                class="mt-1 block w-full rounded-md border-gray-300"
                                required
                            />
                            <InputError class="mt-1" :message="slotForm.errors.start_time" />
                        </div>
                        <div>
                            <label class="text-sm text-gray-700">Fin</label>
                            <input
                                v-model="slotForm.end_time"
                                type="time"
                                class="mt-1 block w-full rounded-md border-gray-300"
                                required
                            />
                            <InputError class="mt-1" :message="slotForm.errors.end_time" />
                        </div>
                        <div class="flex items-end">
                            <PrimaryButton :disabled="slotForm.processing">Ajouter</PrimaryButton>
                        </div>
                    </form>
                </div>

                <div class="space-y-6">
                    <div
                        v-for="pos in positions"
                        :key="pos.id"
                        class="rounded-lg bg-white p-6 shadow"
                    >
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <h3 class="text-lg font-medium">
                                <span
                                    class="me-2 inline-block h-3 w-3 rounded-full align-middle"
                                    :style="{ background: pos.color }"
                                />
                                {{ pos.name }}
                            </h3>
                            <p v-if="pos.stats.critical_slots" class="text-sm text-amber-700">
                                {{ pos.stats.critical_slots }} créneau(x) sous 50%
                            </p>
                        </div>
                        <ul class="mt-4 space-y-3">
                            <li
                                v-for="s in pos.slots"
                                :key="s.id"
                                class="flex flex-col gap-2 rounded-md border border-slate-100 p-3 sm:flex-row sm:items-center sm:justify-between"
                            >
                                <div class="text-sm">
                                    <span class="font-medium">{{ s.date }}</span>
                                    · {{ String(s.start_time).slice(0, 5) }}–{{
                                        String(s.end_time).slice(0, 5)
                                    }}
                                    · {{ s.active_count }}/{{ s.max_volunteers }} inscrits
                                </div>
                                <div class="h-2 w-full max-w-xs rounded bg-gray-200 sm:mx-4">
                                    <div
                                        class="h-2 rounded bg-brand-500"
                                        :style="{ width: Math.min(100, s.percent) + '%' }"
                                    />
                                </div>
                                <SecondaryButton
                                    v-if="permissions.configure"
                                    type="button"
                                    @click="deleteSlot(s.id)"
                                >
                                    Supprimer
                                </SecondaryButton>
                            </li>
                            <li v-if="!pos.slots.length" class="text-sm text-gray-500">
                                Aucun créneau — créez un poste ou régénérez depuis l’écran Postes.
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
