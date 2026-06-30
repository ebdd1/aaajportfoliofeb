<script setup>
const props = defineProps({
    modelValue: {
        type: Number,
        default: 30
    },
    min: {
        type: Number,
        default: 0
    },
    max: {
        type: Number,
        default: 100
    },
    step: {
        type: Number,
        default: 5
    }
});

const emit = defineEmits(['update:modelValue']);

const updateValue = (event) => {
    emit('update:modelValue', Number(event.target.value));
};
</script>

<template>
    <div class="space-y-3">
        <div class="flex justify-between items-center">
            <label class="text-sm font-medium text-midnight-ink">Background Opacity</label>
            <span class="font-mono text-sm text-driftwood">{{ modelValue }}%</span>
        </div>
        <div class="relative">
            <input
                type="range"
                :value="modelValue"
                @input="updateValue"
                :min="min"
                :max="max"
                :step="step"
                class="w-full h-2 bg-warm-sand rounded-full appearance-none cursor-pointer
                       [&::-webkit-slider-thumb]:appearance-none
                       [&::-webkit-slider-thumb]:w-5
                       [&::-webkit-slider-thumb]:h-5
                       [&::-webkit-slider-thumb]:rounded-full
                       [&::-webkit-slider-thumb]:bg-terracotta
                       [&::-webkit-slider-thumb]:cursor-pointer
                       [&::-webkit-slider-thumb]:shadow-sm
                       [&::-webkit-slider-thumb]:transition-transform
                       [&::-webkit-slider-thumb]:hover:scale-110
                       [&::-moz-range-thumb]:w-5
                       [&::-moz-range-thumb]:h-5
                       [&::-moz-range-thumb]:rounded-full
                       [&::-moz-range-thumb]:bg-terracotta
                       [&::-moz-range-thumb]:border-0
                       [&::-moz-range-thumb]:cursor-pointer"
            />
        </div>
        <div class="flex justify-between text-xs text-driftwood">
            <span>Transparan</span>
            <span>Opaque</span>
        </div>
    </div>
</template>