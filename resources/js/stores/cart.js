import { ref, computed, watch } from 'vue';

const CART_STORAGE_KEY = 'portfolio_cart';

// Reactive state
const items = ref([]);
const isLoading = ref(false);
const isDrawerOpen = ref(false);
const toast = ref({ show: false, message: '', type: 'success' });

// Load from localStorage
const loadFromStorage = () => {
    try {
        const stored = localStorage.getItem(CART_STORAGE_KEY);
        if (stored) items.value = JSON.parse(stored);
    } catch (e) {
        console.error('Failed to load cart:', e);
    }
};

// Save to localStorage
watch(items, (newItems) => {
    try {
        localStorage.setItem(CART_STORAGE_KEY, JSON.stringify(newItems));
    } catch (e) {
        console.error('Failed to save cart:', e);
    }
}, { deep: true });

loadFromStorage();

// Toast helpers
const showToast = (message, type = 'success') => {
    toast.value = { show: true, message, type };
};
const hideToast = () => { toast.value.show = false; };

// Computed
const count = computed(() => items.value.reduce((sum, item) => sum + item.quantity, 0));
const total = computed(() => items.value.reduce((sum, item) => sum + (item.product?.price || 0) * item.quantity, 0));
const isEmpty = computed(() => items.value.length === 0);
const isInCart = (productId) => items.value.some(item => item.product_id === productId);
const getItem = (productId) => items.value.find(item => item.product_id === productId);

// Actions
const addItem = async (product) => {
    if (isInCart(product.id)) {
        showToast('Produk sudah ada di keranjang', 'warning');
        return { success: false, message: 'Sudah ada' };
    }

    if (window.Laravel?.user) {
        isLoading.value = true;
        try {
            const response = await fetch('/api/cart', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content },
                body: JSON.stringify({ product_id: product.id }),
            });
            const data = await response.json();
            if (data.success) {
                items.value.push({ id: data.item?.id, product_id: product.id, quantity: 1, product });
                openDrawer();
                showToast('Ditambahkan ke keranjang!', 'success');
                return { success: true, message: 'Ditambahkan!' };
            }
            showToast(data.message || 'Gagal', 'error');
            return { success: false, message: data.message };
        } catch (e) {
            showToast('Terjadi kesalahan', 'error');
            return { success: false, message: 'Error' };
        } finally {
            isLoading.value = false;
        }
    }

    items.value.push({ id: Date.now(), product_id: product.id, quantity: 1, product });
    openDrawer();
    showToast('Ditambahkan ke keranjang!', 'success');
    return { success: true, message: 'Ditambahkan!' };
};

const removeItem = async (productId) => {
    const item = getItem(productId);
    if (!item) return;

    if (window.Laravel?.user) {
        isLoading.value = true;
        try {
            await fetch(`/api/cart/${productId}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content }});
        } catch (e) { console.error('Failed:', e); }
        finally { isLoading.value = false; }
    }

    items.value = items.value.filter(i => i.product_id !== productId);
    showToast('Dihapus dari keranjang', 'info');
};

const updateQuantity = async (productId, quantity) => {
    const item = getItem(productId);
    if (!item) return;
    if (quantity <= 0) { removeItem(productId); return; }

    if (window.Laravel?.user) {
        isLoading.value = true;
        try {
            await fetch(`/api/cart/${productId}`, { method: 'PATCH', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content }, body: JSON.stringify({ quantity }) });
        } catch (e) { console.error('Failed:', e); }
        finally { isLoading.value = false; }
    }
    item.quantity = quantity;
};

const clearCart = async () => {
    if (window.Laravel?.user) {
        isLoading.value = true;
        try { await fetch('/api/cart', { method: 'DELETE', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content }});
        } catch (e) { console.error('Failed:', e); }
        finally { isLoading.value = false; }
    }
    items.value = [];
    showToast('Keranjang dikosongkan', 'info');
};

const syncWithServer = async () => {
    if (!window.Laravel?.user) return;
    isLoading.value = true;
    try {
        const response = await fetch('/api/cart');
        const data = await response.json();
        if (data.items) {
            data.items.forEach(serverItem => {
                if (!items.value.find(i => i.product_id === serverItem.product_id)) {
                    items.value.push(serverItem);
                }
            });
        }
    } catch (e) { console.error('Failed:', e); }
    finally { isLoading.value = false; }
};

// Drawer controls
const openDrawer = () => { isDrawerOpen.value = true; };
const closeDrawer = () => { isDrawerOpen.value = false; };
const toggleDrawer = () => { isDrawerOpen.value = !isDrawerOpen.value; };

export const useCart = () => ({
    items, isLoading, isDrawerOpen, toast,
    count, total, isEmpty, isInCart, getItem,
    addItem, removeItem, updateQuantity, clearCart, syncWithServer,
    openDrawer, closeDrawer, toggleDrawer, showToast, hideToast,
});