import { inject } from 'vue';

export function useConfirm() {
    const confirm = inject('confirm');
    if (!confirm) {
        console.warn('ConfirmModal provider not found. Make sure ConfirmModal is mounted in the layout.');
    }
    return confirm || {
        open: async (options = {}) => {
            console.warn('Confirm not available');
            return false;
        },
    };
}
