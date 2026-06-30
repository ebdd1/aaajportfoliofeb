<script setup>
import { computed } from 'vue';

const props = defineProps({
    backgroundUrl: {
        type: String,
        default: null
    },
    opacity: {
        type: Number,
        default: 30
    }
});

const overlayOpacity = computed(() => (100 - props.opacity) / 100);
</script>

<template>
    <div class="rounded-3xl overflow-hidden border border-ash-border shadow-elevated">
        <div class="bg-warm-sand px-4 py-2 border-b border-ash-border">
            <span class="font-mono text-xs text-driftwood">Live Preview</span>
        </div>

        <div class="relative h-48 overflow-hidden">
            <!-- Background with overlay -->
            <div v-if="backgroundUrl" class="absolute inset-0 bg-cover bg-center" :style="{ backgroundImage: `url(${backgroundUrl})` }">
                <div class="absolute inset-0 bg-parchment-white" :style="{ opacity: overlayOpacity }"></div>
            </div>

            <!-- Fallback gradient -->
            <div v-else class="absolute inset-0 bg-gradient-to-br from-parchment-white via-warm-sand/30 to-parchment-white"></div>

            <!-- Preview Content -->
            <div class="relative z-10 p-4">
                <p class="font-serif text-lg font-light text-midnight-ink tracking-tight">Febryanus Tambing</p>
                <p class="text-sm text-driftwood tracking-wide">Cybersecurity Enthusiast & API Specialist</p>
            </div>
        </div>
    </div>
</template>
