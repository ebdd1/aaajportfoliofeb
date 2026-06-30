<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { ref, computed, onMounted, onUnmounted } from 'vue';
import ContactModal from '@/Components/ContactModal.vue';
import CartBadge from '@/Components/CartBadge.vue';
import CartDrawer from '@/Components/CartDrawer.vue';
import Toast from '@/Components/Toast.vue';
import { useCart } from '@/stores/cart';

const props = defineProps({
    profile: { type: Object, default: null },
    showNav: { type: Boolean, default: true },
    variant: { type: String, default: 'default' },
});

const { syncWithServer, toast, hideToast } = useCart();
onMounted(() => {
    if (window.Laravel?.user) syncWithServer();
});

const page = usePage();
const isLoggedIn = computed(() => page.props.auth?.user != null);
const currentPath = computed(() => page.url);

// Get proper href - if anchor link and not on homepage, go to homepage + anchor
const getHref = (anchor) => {
    const baseUrl = '/';
    return baseUrl + anchor;
};

const navItems = [
    { label: 'Tentang', anchor: '#tentang' },
    { label: 'Keahlian', anchor: '#keahlian' },
    { label: 'Pengalaman', anchor: '#pengalaman' },
    { label: 'Proyek', anchor: '#proyek' },
    { label: 'Blog', href: '/blog' },
];

const isScrolled = ref(false);
const isMobileMenuOpen = ref(false);
const showContactModal = ref(false);

const handleScroll = () => {
    isScrolled.value = window.scrollY > 20;
};

onMounted(() => {
    window.addEventListener('scroll', handleScroll, { passive: true });
});

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
});

const navClasses = computed(() => {
    const base = 'fixed top-0 left-0 right-0 z-[100] transition-all duration-300';
    if (isScrolled.value) {
        return `${base} bg-parchment-white border-b border-ash-border`;
    }
    return `${base} bg-parchment-white border-b border-ash-border`;
});
</script>

<template>
    <nav :class="navClasses" @keydown.escape="isMobileMenuOpen = false">
        <div class="container-padding mx-auto">
            <!-- Desktop Layout -->
            <div class="hidden md:flex items-center justify-between h-16">
                <!-- Logo -->
                <Link href="/" class="flex items-center gap-3 group">
                    <div class="relative">
                        <div class="w-10 h-10 rounded-full bg-terracotta flex items-center justify-center transition-transform duration-300 group-hover:scale-110 group-hover:rotate-3">
                            <span class="font-serif text-lg font-bold text-cream">{{ profile?.name?.charAt(0) || 'F' }}</span>
                        </div>
                        <div class="absolute -inset-1 bg-terracotta/20 rounded-full blur-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    </div>
                    <span class="font-serif text-lg font-semibold text-ink/90 tracking-tight">
                        {{ profile?.name?.split(' ')[0] || 'Febryanus' }}
                    </span>
                </Link>

                <!-- Center Nav Links -->
                <div v-if="variant === 'default'" class="flex items-center gap-1">
                    <Link
                        v-for="item in navItems"
                        :key="item.label"
                        :href="item.href ? item.href : getHref(item.anchor)"
                        :tabindex="isMobileMenuOpen ? -1 : 0"
                        class="px-4 py-2 text-sm font-medium text-ink hover:text-terracotta transition-colors duration-200 rounded-full hover:bg-warm-sand">
                        {{ item.label }}
                    </Link>
                    <!-- Produk Saya - hanya untuk user yang login -->
                    <Link v-if="isLoggedIn"
                       href="/products"
                       class="px-4 py-2 text-sm font-medium text-terracotta hover:text-terracotta-dark transition-colors duration-200 rounded-full hover:bg-terracotta/10">
                        Produk Saya
                    </Link>
                </div>

                <!-- Right Side - Cart + Contact -->
                <div class="flex items-center gap-2">
                    <CartBadge />
                    <button
                        v-if="variant === 'default' || variant === 'minimal'"
                        @click="showContactModal = true"
                        class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-cream bg-midnight-ink rounded-full border border-ash-border hover:bg-ink/90 active:scale-[0.98] transition-all duration-200 shadow-sm"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/>
                        </svg>
                        <span class="hidden lg:inline">Hubungi</span>
                    </button>
                </div>
            </div>

            <!-- Mobile Layout -->
            <div class="md:hidden flex items-center justify-between h-16">
                <!-- Logo -->
                <Link href="/" class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-terracotta flex items-center justify-center">
                        <span class="font-serif text-sm font-bold text-cream">{{ profile?.name?.charAt(0) || 'F' }}</span>
                    </div>
                    <span class="font-serif text-base font-semibold text-ink">
                        {{ profile?.name?.split(' ')[0] || 'Feb' }}
                    </span>
                </Link>

                <!-- Mobile Menu Button -->
                <button
                    @click="isMobileMenuOpen = !isMobileMenuOpen"
                    @keydown.escape="isMobileMenuOpen = false"
                    class="p-3 text-taupe hover:text-ink transition-colors"
                    :aria-expanded="isMobileMenuOpen"
                    aria-label="Toggle menu"
                >
                    <svg v-if="!isMobileMenuOpen" xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Mobile Menu Dropdown -->
            <Transition
                enter-active-class="transition-all duration-300 ease-out"
                enter-from-class="opacity-0 -translate-y-4"
                enter-to-class="opacity-100 translate-y-0"
                leave-active-class="transition-all duration-200 ease-in"
                leave-from-class="opacity-100 translate-y-0"
                leave-to-class="opacity-0 -translate-y-4"
            >
                <div v-if="isMobileMenuOpen && variant === 'default'" class="md:hidden absolute top-16 left-0 right-0 z-[90] bg-parchment-white border-b border-ash-border shadow-elevated">
                    <div class="container-padding mx-auto py-4 space-y-1">
                        <Link
                            v-for="item in navItems"
                            :key="item.label"
                            :href="item.href ? item.href : getHref(item.anchor)"
                            @click="isMobileMenuOpen = false"
                            class="block px-4 py-3 min-h-[44px] text-sm font-medium text-ink hover:text-terracotta hover:bg-warm-sand rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-terracotta">
                            {{ item.label }}
                        </Link>
                        <div class="pt-3 border-t border-ash-border">
                            <button
                                @click="showContactModal = true; isMobileMenuOpen = false"
                                class="w-full flex items-center justify-center gap-2 px-4 py-3 text-sm font-medium text-cream bg-midnight-ink rounded-full hover:bg-ink/90 transition-colors"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/>
                                </svg>
                        Hubungi
                            </button>
                        </div>
                    </div>
                </div>
            </Transition>
        </div>
    </nav>

    <ContactModal :show="showContactModal" @close="showContactModal = false" />
    <CartDrawer />
    <Toast
        :show="toast.show"
        :message="toast.message"
        :type="toast.type"
        @close="hideToast"
    />
</template>
