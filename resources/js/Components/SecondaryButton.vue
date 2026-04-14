<script setup>
defineProps({
    type: {
        type: String,
        default: 'button',
    },
});
</script>

<template>
    <button
        :type="type"
        class="inline-flex items-center justify-center rounded-xl border border-slate-200/90 bg-white/95 px-5 py-2.5 text-sm font-semibold text-slate-800 shadow-sm transition hover:border-ember-200/80 hover:bg-gradient-to-r hover:from-white hover:to-ember-50/90 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:ring-offset-1 disabled:opacity-40"
    >
        <slot />
    </button>
</template>
