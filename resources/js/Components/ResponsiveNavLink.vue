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
        ? 'block w-full rounded-lg border border-brand-200 bg-brand-50 ps-3 pe-4 py-2.5 text-start text-base font-semibold text-brand-900 transition'
        : 'block w-full rounded-lg border border-transparent ps-3 pe-4 py-2.5 text-start text-base font-medium text-slate-600 transition hover:border-slate-200 hover:bg-slate-50 hover:text-slate-900',
);
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>
