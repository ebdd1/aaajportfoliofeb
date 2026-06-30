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
        class="inline-flex items-center justify-center px-5 py-2.5 bg-transparent text-midnight-ink font-medium rounded-full border border-ash-border hover:bg-warm-sand active:scale-[0.98] transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
    >
        <slot />
    </button>
</template>
