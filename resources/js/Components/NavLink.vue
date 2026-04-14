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
        ? 'inline-flex items-center rounded-lg px-3 py-2 text-sm font-semibold text-brand-800 bg-brand-50 ring-1 ring-brand-200/80 transition'
        : 'inline-flex items-center rounded-lg px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-900',
);
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>
