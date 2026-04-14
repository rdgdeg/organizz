<script setup>
import { onMounted, ref } from 'vue';

const model = defineModel({
    type: String,
    required: true,
});

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        class="w-full rounded-xl border-slate-200 bg-white px-3 py-2 shadow-sm transition focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20"
        v-model="model"
        ref="input"
    />
</template>
