<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';
import { CheckCircleIcon, ExclamationCircleIcon, InformationCircleIcon } from '@heroicons/vue/24/outline';

interface Toast {
    id: number;
    message: string;
    type: string;
    undoAction?: () => void;
}

const toasts = ref<Toast[]>([]);
let toastId = 0;

const show = (message: string, type = 'success', undoAction?: () => void) => {
    const id = ++toastId;
    toasts.value.push({ id, message, type, undoAction });

    // Auto remove after 4s (longer for error)
    const duration = type === 'error' ? 6000 : 4000;
    setTimeout(() => {
        remove(id);
    }, duration);

    return id;
};

const remove = (id: number) => {
    const index = toasts.value.findIndex(t => t.id === id);
    if (index > -1) {
        toasts.value.splice(index, 1);
    }
};

const undo = (toast: Toast) => {
    if (toast.undoAction) {
        toast.undoAction();
    }
    remove(toast.id);
};

// Global event listeners for flash messages
onMounted(() => {
    window.addEventListener('toast:success', ((e: CustomEvent) => { show(e.detail.message, 'success', e.detail?.undoAction); }) as EventListener);
    window.addEventListener('toast:error', ((e: CustomEvent) => { show(e.detail.message, 'error'); }) as EventListener);
    window.addEventListener('toast:info', ((e: CustomEvent) => { show(e.detail.message, 'info'); }) as EventListener);
});

onUnmounted(() => {
    window.removeEventListener('toast:success', (() => {}) as EventListener);
    window.removeEventListener('toast:error', (() => {}) as EventListener);
    window.removeEventListener('toast:info', (() => {}) as EventListener);
});

// Expose for programmatic use
defineExpose({ show, remove });

const icons: Record<string, any> = {
    success: CheckCircleIcon,
    error: ExclamationCircleIcon,
    info: InformationCircleIcon,
    warning: ExclamationCircleIcon,
};

const colors: Record<string, string> = {
    success: 'bg-emerald-50 border-emerald-200 text-emerald-800',
    error: 'bg-red-50 border-red-200 text-red-800',
    info: 'bg-blue-50 border-blue-200 text-blue-800',
    warning: 'bg-amber-50 border-amber-200 text-amber-800',
};

const iconColors: Record<string, string> = {
    success: 'text-emerald-500',
    error: 'text-red-500',
    info: 'text-blue-500',
    warning: 'text-amber-500',
};
</script>

<template>
    <Teleport to="body">
        <div class="fixed top-4 right-4 z-[100] space-y-3 max-w-sm">
            <TransitionGroup
                enter-active-class="transition duration-300 ease-out"
                enter-from-class="translate-x-full opacity-0"
                enter-to-class="translate-x-0 opacity-100"
                leave-active-class="transition duration-200 ease-in"
                leave-from-class="translate-x-0 opacity-100"
                leave-to-class="translate-x-full opacity-0"
            >
                <div
                    v-for="toast in toasts"
                    :key="toast.id"
                    :class="[
                        'rounded-xl border shadow-lg backdrop-blur-sm px-4 py-3 flex items-start gap-3 min-w-[300px]',
                        colors[toast.type]
                    ]"
                >
                    <component :is="icons[toast.type]" :class="['w-5 h-5 shrink-0 mt-0.5', iconColors[toast.type]]" />

                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium">{{ toast.message }}</p>

                        <!-- Undo Action -->
                        <div v-if="toast.undoAction" class="mt-2 flex gap-3">
                            <button
                                @click="undo(toast)"
                                class="text-xs font-medium text-current opacity-80 hover:opacity-100 underline hover:no-underline"
                            >
                                Undo
                            </button>
                        </div>
                    </div>

                    <button
                        @click="remove(toast.id)"
                        class="shrink-0 opacity-60 hover:opacity-100 transition-opacity"
                    >
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </TransitionGroup>
        </div>
    </Teleport>
</template>
