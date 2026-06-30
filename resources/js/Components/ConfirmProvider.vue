<script setup>
import { ref, provide } from 'vue';
import ConfirmDialog from '@/Components/ConfirmDialog.vue';

const confirmState = ref({
    open: false,
    message: '',
    title: '',
    variant: 'default',
    confirmText: 'Ya',
    cancelText: 'Batal',
});

const confirm = {
    open: async (options = {}) => {
        confirmState.value = {
            open: true,
            title: options.title || 'Konfirmasi',
            message: options.message || 'Apakah Anda yakin?',
            variant: options.variant || 'default',
            confirmText: options.confirmText || 'Ya',
            cancelText: options.cancelText || 'Batal',
            onConfirm: options.onConfirm,
        };
        
        // Return a promise that resolves when confirmed
        return new Promise((resolve) => {
            confirmState.value.resolve = resolve;
        });
    },
};

provide('confirm', confirm);

const handleConfirm = () => {
    confirmState.value.open = false;
    if (confirmState.value.resolve) {
        confirmState.value.resolve(true);
    }
    // Call the onConfirm callback if it exists
    if (confirmState.value.onConfirm) {
        confirmState.value.onConfirm();
    }
};

const handleCancel = () => {
    confirmState.value.open = false;
    if (confirmState.value.resolve) {
        confirmState.value.resolve(false);
    }
};
</script>

<template>
    <div>
        <slot />
        <ConfirmDialog
            :confirm-state="confirmState"
            :on-confirm="handleConfirm"
            :on-cancel="handleCancel"
        />
    </div>
</template>
