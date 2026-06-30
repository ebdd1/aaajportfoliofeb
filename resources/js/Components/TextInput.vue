<script setup>
import { onMounted, ref } from 'vue';

const model = defineModel({
    type: String,
    required: true,
});

const input = ref(null);

onMounted(() => {
    if (input.value && input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value?.focus() });
</script>

<template>
    <input
        class="w-full px-4 py-3 bg-oat border-0 shadow-sm rounded-xl text-ink placeholder-taupe transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-terracotta focus:bg-paper"
        v-model="model"
        ref="input"
    />
</template>
