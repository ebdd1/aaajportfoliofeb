<script setup>
import { computed } from 'vue';

const props = defineProps({
    confirmState: {
        type: Object,
        required: true,
    },
    onConfirm: {
        type: Function,
        required: true,
    },
    onCancel: {
        type: Function,
        default: () => {},
    },
});

const iconBgClasses = computed(() => {
    return props.confirmState.variant === 'danger'
        ? 'bg-red-100'
        : 'bg-terracotta/10';
});

const iconColorClasses = computed(() => {
    return props.confirmState.variant === 'danger'
        ? 'text-red-600'
        : 'text-terracotta';
});

const confirmButtonClasses = computed(() => {
    return props.confirmState.variant === 'danger'
        ? 'bg-red-500 hover:bg-red-600 text-white'
        : 'bg-terracotta hover:bg-terracotta-dark text-cream';
});

const handleConfirm = () => props.onConfirm(props.confirmState.resolve);
const handleCancel = () => {
    props.onCancel(props.confirmState.resolve);
    props.confirmState.open = false;
};
</script>

<template>
    <Teleport to="body">
        <Transition
            appear
            enter-active-class="duration-200 ease-out"
            enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="duration-150 ease-in"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95"
        >
            <div
                v-if="confirmState.open"
                class="fixed inset-0 z-50 flex items-center justify-center p-4"
            >
                <!-- Backdrop -->
                <div
                    class="absolute inset-0 bg-ink/60 backdrop-blur-sm"
                    @click="handleCancel"
                />

                <!-- Modal Card -->
                <div class="relative bg-paper rounded-2xl shadow-2xl max-w-sm w-full p-8">
                    <!-- Icon + Title -->
                    <div class="flex flex-col items-center text-center">
                        <div :class="['w-16 h-16 rounded-full flex items-center justify-center mb-6', iconBgClasses]">
                            <!-- Warning Triangle -->
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                :class="['w-8 h-8', iconColorClasses]"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" />
                                <line x1="12" y1="9" x2="12" y2="13" />
                                <line x1="12" y1="17" x2="12.01" y2="17" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-ink mb-2">Apakah Anda yakin?</h3>
                        <p class="text-sm text-taupe text-center mb-6">
                            {{ confirmState.message }}
                        </p>
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-3">
                        <button
                            type="button"
                            class="flex-1 px-4 py-2.5 rounded-full border border-oat-dark text-ink hover:bg-oat transition-colors"
                            @click="handleCancel"
                        >
                            {{ confirmState.cancelText || 'Batal' }}
                        </button>
                        <button
                            type="button"
                            :class="['flex-1 px-4 py-2.5 rounded-full transition-colors', confirmButtonClasses]"
                            @click="handleConfirm"
                        >
                            {{ confirmState.confirmText || 'Ya' }}
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
