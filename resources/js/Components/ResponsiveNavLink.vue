<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: {
        type: String,
        required: true,
    },
    active: {
        type: Boolean,
    },
});

const classes = computed(() =>
    props.active
        ? 'block w-full rounded-xl border border-brand-200/80 bg-gradient-to-r from-brand-50 to-ember-50 ps-3 pe-4 py-2.5 text-start text-base font-semibold text-slate-900 shadow-sm transition'
        : 'block w-full rounded-xl border border-transparent ps-3 pe-4 py-2.5 text-start text-base font-medium text-slate-600 transition hover:border-slate-200 hover:bg-white/90 hover:text-slate-900',
);
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>
