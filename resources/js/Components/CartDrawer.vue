<script setup>
import { ref, watch, onMounted, onUnmounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import { useCart } from '@/stores/cart';

const { items, total, isEmpty, removeItem, updateQuantity, clearCart, closeDrawer, isDrawerOpen, count } = useCart();

const drawerRef = ref(null);
const isConfirmingClear = ref(false);

const formatPrice = (price) => price ? 'Rp ' + new Intl.NumberFormat('id-ID').format(price) : 'Rp 0';

const handleCheckout = () => {
    closeDrawer();
    window.location.href = '/checkout/summary';
};

const handleClearCart = () => {
    isConfirmingClear.value = true;
};

const confirmClearCart = () => {
    clearCart();
    isConfirmingClear.value = false;
};

const cancelClearCart = () => {
    isConfirmingClear.value = false;
};

// Escape key to close drawer
const handleKeydown = (e) => {
    if (e.key === 'Escape' && isDrawerOpen.value) {
        closeDrawer();
    }
};

onMounted(() => {
    document.addEventListener('keydown', handleKeydown);
});

onUnmounted(() => {
    document.removeEventListener('keydown', handleKeydown);
});
</script>

<template>
    <Teleport to="body">
        <!-- Backdrop -->
        <Transition name="fade">
            <div v-if="isDrawerOpen" class="fixed inset-0 bg-ink/40 backdrop-blur-sm z-50" @click="closeDrawer" aria-hidden="true" />
        </Transition>

        <!-- Drawer -->
        <Transition name="slide">
            <div
                v-if="isDrawerOpen"
                ref="drawerRef"
                role="dialog"
                aria-modal="true"
                aria-labelledby="cart-title"
                class="fixed right-0 top-0 h-full w-full max-w-md bg-paper shadow-2xl z-50 flex flex-col"
            >
                <!-- Header -->
                <div class="flex items-center justify-between p-5 border-b border-oat-dark/60">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-terracotta/10 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-terracotta" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
                                <path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 002-1.61L23 6H6"/>
                            </svg>
                        </div>
                        <div>
                            <h2 id="cart-title" class="font-serif text-lg font-bold text-ink">Keranjang</h2>
                            <p class="text-xs text-taupe">{{ count }} item{{ count > 1 ? 's' : '' }}</p>
                        </div>
                    </div>
                    <button @click="closeDrawer" aria-label="Tutup keranjang" class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-oat/50 transition-colors focus:outline-none focus:ring-2 focus:ring-terracotta/50">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-taupe" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                        </svg>
                    </button>
                </div>

                <!-- Content -->
                <div class="flex-1 overflow-y-auto">
                    <!-- Empty State -->
                    <div v-if="isEmpty" class="flex flex-col items-center justify-center h-full p-8 text-center">
                        <div class="w-24 h-24 rounded-full bg-oat/50 flex items-center justify-center mb-6">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-taupe/40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
                                <path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 002-1.61L23 6H6"/>
                            </svg>
                        </div>
                        <h3 class="font-serif text-xl font-bold text-ink mb-2">Keranjang Kosong</h3>
                        <p class="text-sm text-taupe mb-6">Yuk mulai belanja produk digital berkualitas</p>
                        <Link href="/products" @click="closeDrawer"
                            class="px-6 py-2.5 bg-terracotta text-cream text-sm font-medium rounded-xl hover:bg-terracotta-dark transition-colors">
                            Jelajahi Produk
                        </Link>
                    </div>

                    <!-- Items -->
                    <div v-else class="p-5 space-y-3">
                        <TransitionGroup name="list">
                            <div v-for="item in items" :key="item.id || item.product_id"
                                class="flex gap-3 p-3 bg-oat/30 rounded-xl group hover:bg-oat/50 transition-colors">
                                <!-- Image -->
                                <div class="w-16 h-16 rounded-lg bg-terracotta/5 flex items-center justify-center overflow-hidden shrink-0">
                                    <img v-if="item.product?.thumbnail" :src="item.product.thumbnail" :alt="item.product.name" class="w-full h-full object-cover" />
                                    <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-terracotta/40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/>
                                    </svg>
                                </div>

                                <!-- Info -->
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-medium text-ink line-clamp-1">{{ item.product?.name }}</h4>
                                    <p class="text-sm font-bold text-terracotta mt-0.5">{{ formatPrice(item.product?.price) }}</p>
                                    <!-- Quantity -->
                                    <div class="flex items-center gap-2 mt-2">
                                        <button @click="updateQuantity(item.product_id, item.quantity - 1)"
                                            class="w-7 h-7 rounded-lg bg-paper border border-oat-dark/60 hover:bg-oat transition-colors focus:outline-none focus:ring-2 focus:ring-terracotta/50">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-taupe" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                        </button>
                                        <span class="w-5 text-center text-sm font-medium text-ink">{{ item.quantity }}</span>
                                        <button @click="updateQuantity(item.product_id, item.quantity + 1)"
                                            class="w-7 h-7 rounded-lg bg-paper border border-oat-dark/60 hover:bg-oat transition-colors focus:outline-none focus:ring-2 focus:ring-terracotta/50">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-taupe" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <!-- Remove -->
                                <button @click="removeItem(item.product_id)" aria-label="Hapus item"
                                    class="w-8 h-8 rounded-lg hover:bg-red-50 transition-colors flex items-center justify-center opacity-40 group-hover:opacity-100 focus:opacity-100 focus:ring-2 focus:ring-red-500/50">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-red-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/>
                                    </svg>
                                </button>
                            </div>
                        </TransitionGroup>
                    </div>
                </div>

                <!-- Clear Cart Confirmation -->
                <Transition name="fade">
                    <div v-if="isConfirmingClear" class="absolute inset-0 bg-paper/95 flex items-center justify-center p-6 z-10">
                        <div class="text-center">
                            <h3 class="font-serif text-lg font-bold text-ink mb-2">Kosongkan keranjang?</h3>
                            <p class="text-sm text-taupe mb-4">Semua item akan dihapus dari keranjang.</p>
                            <div class="flex gap-3 justify-center">
                                <button @click="cancelClearCart"
                                    class="px-4 py-2 border border-oat-dark rounded-xl text-ink hover:bg-oat transition-colors">
                                    Batal
                                </button>
                                <button @click="confirmClearCart"
                                    class="px-4 py-2 bg-red-500 text-white rounded-xl hover:bg-red-600 transition-colors">
                                    Ya, Kosongkan
                                </button>
                            </div>
                        </div>
                    </div>
                </Transition>

                <!-- Footer -->
                <div v-if="!isEmpty && !isConfirmingClear" class="border-t border-oat-dark/60 p-5 space-y-4">
                    <!-- Total -->
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-taupe">Total</span>
                        <div class="text-right">
                            <p class="font-serif text-2xl font-bold text-terracotta">{{ formatPrice(total) }}</p>
                            <p class="text-xs text-taupe">Termasuk ppn</p>
                        </div>
                    </div>

                    <!-- CTA -->
                    <button @click="handleCheckout"
                        class="w-full py-3.5 bg-terracotta text-cream font-semibold rounded-xl hover:bg-terracotta-dark active:scale-[0.98] transition-all focus:outline-none focus:ring-2 focus:ring-terracotta/50">
                        Checkout Sekarang
                    </button>
                    <button @click="handleClearCart" class="w-full py-2.5 text-sm text-taupe hover:text-red-500 transition-colors">
                        Kosongkan Keranjang
                    </button>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.25s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }

.slide-enter-active, .slide-leave-active { transition: transform 0.3s cubic-bezier(0.32, 0.72, 0, 1); }
.slide-enter-from, .slide-leave-to { transform: translateX(100%); }

.list-enter-active { transition: all 0.3s ease; }
.list-leave-active { transition: all 0.2s ease; position: absolute; width: calc(100% - 2.5rem); }
.list-enter-from { opacity: 0; transform: translateX(20px); }
.list-leave-to { opacity: 0; transform: translateX(-20px); }
</style>
