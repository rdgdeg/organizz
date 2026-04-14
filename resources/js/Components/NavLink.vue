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
        ? 'inline-flex items-center rounded-xl bg-gradient-to-r from-brand-50 to-ember-50 px-3 py-2 text-sm font-semibold text-slate-900 shadow-sm ring-1 ring-brand-200/70 transition'
        : 'inline-flex items-center rounded-xl px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-white/80 hover:text-slate-900 hover:ring-1 hover:ring-slate-200/80',
);
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>
