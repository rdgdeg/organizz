<script setup>
defineProps({
    variant: {
        type: String,
        default: 'brand',
        validator: (v) => ['brand', 'inverse'].includes(v),
    },
});
</script>

<template>
    <svg viewBox="0 0 32 32" class="h-9 w-9 shrink-0" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        <rect
            width="32"
            height="32"
            rx="9"
            :class="variant === 'inverse' ? 'fill-white/20' : 'fill-brand-600'"
        />
        <path
            d="M9 10h14v2H9V10zm0 5h9v2H9v-2zm0 5h11v2H9v-2z"
            :class="variant === 'inverse' ? 'fill-white' : 'fill-white'"
            opacity="0.95"
        />
        <circle
            cx="24"
            cy="11"
            r="3"
            :class="variant === 'inverse' ? 'fill-white/50' : 'fill-brand-200'"
        />
    </svg>
</template>
