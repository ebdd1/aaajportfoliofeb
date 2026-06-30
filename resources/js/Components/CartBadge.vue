<script setup>
import { useCart } from '@/stores/cart';

const { count, toggleDrawer } = useCart();
</script>

<template>
    <button
        @click="toggleDrawer"
        aria-label="Buka keranjang belanja"
        class="relative p-2.5 rounded-xl hover:bg-oat/80 transition-all duration-200 group flex items-center justify-center"
    >
        <div class="relative">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-taupe group-hover:text-terracotta transition-colors duration-200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
                <path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 002-1.61L23 6H6"/>
            </svg>
        </div>
        <Transition name="badge">
            <span
                v-if="count > 0"
                class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] px-1 bg-terracotta text-cream text-[10px] font-bold rounded-full flex items-center justify-center shadow-sm"
            >
                {{ count > 9 ? '9+' : count }}
            </span>
        </Transition>
    </button>
</template>

<style scoped>
.badge-enter-active { animation: badgePop 0.4s cubic-bezier(0.34, 1.56, 0.64, 1); }
.badge-leave-active { animation: badgePop 0.2s ease-in reverse; }
@keyframes badgePop {
    0% { transform: scale(0); opacity: 0; }
    50% { transform: scale(1.3); }
    100% { transform: scale(1); opacity: 1; }
}
</style>