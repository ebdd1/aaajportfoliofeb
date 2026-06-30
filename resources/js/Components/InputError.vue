<script setup>
defineProps({
    message: {
        type: String,
    },
});
</script>

<template>
    <p v-show="message" class="text-sm text-red-500 mt-2 flex items-center gap-1.5">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="8" x2="12" y2="12"/>
            <line x1="12" y1="16" x2="12.01" y2="16"/>
        </svg>
        {{ message }}
    </p>
</template>
