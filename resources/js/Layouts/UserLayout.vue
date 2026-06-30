<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import CartBadge from '@/Components/CartBadge.vue';
import CartDrawer from '@/Components/CartDrawer.vue';
import { useCart } from '@/stores/cart';
import {
    HomeIcon,
    ShoppingBagIcon,
    ArrowLeftIcon,
    ArrowRightOnRectangleIcon,
    Bars3Icon,
    XMarkIcon,
    Cog6ToothIcon
} from '@heroicons/vue/24/outline';

defineProps({
    title: String,
});

const page = usePage();
const isSidebarOpen = ref(false);

const { syncWithServer } = useCart();
onMounted(() => {
    syncWithServer();
});

const user = computed(() => page.props.auth?.user);
</script>

<template>
    <div class="min-h-screen bg-cream flex font-sans">
        <!-- Mobile Backdrop -->
        <div
            v-if="isSidebarOpen"
            class="fixed inset-0 bg-ink/50 z-30 lg:hidden"
            @click="isSidebarOpen = false"
        />

        <!-- Sidebar -->
        <aside
            :class="isSidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed left-0 top-0 h-screen w-64 bg-paper border-r border-oat-dark flex flex-col z-40 transition-transform duration-300 lg:translate-x-0"
        >
            <!-- Logo -->
            <div class="h-16 flex items-center justify-between px-4 border-b border-oat-dark shrink-0">
                <Link href="/" class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-terracotta flex items-center justify-center shrink-0">
                        <span class="font-serif text-xl font-bold text-cream">{{ user?.name?.charAt(0) || 'U' }}</span>
                    </div>
                    <span class="font-serif text-lg font-bold text-ink">User Area</span>
                </Link>
                <button @click="isSidebarOpen = false" aria-label="Tutup menu navigasi" class="lg:hidden p-1 text-taupe hover:text-ink">
                    <XMarkIcon class="w-6 h-6" />
                </button>
            </div>

            <!-- Links -->
            <nav class="flex-1 py-4 px-3 space-y-1 overflow-y-auto">
                <Link
                    :href="route('user.dashboard.home')"
                    :class="[
                        'flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200',
                        route().current('user.dashboard.home')
                            ? 'bg-terracotta text-cream shadow-sm'
                            : 'text-taupe hover:bg-oat hover:text-ink'
                    ]"
                    @click="isSidebarOpen = false"
                >
                    <HomeIcon class="w-5 h-5 shrink-0" />
                    <span class="font-medium text-sm">Dashboard</span>
                </Link>

                <Link
                    :href="route('user.purchases')"
                    :class="[
                        'flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200',
                        route().current('user.purchases')
                            ? 'bg-terracotta text-cream shadow-sm'
                            : 'text-taupe hover:bg-oat hover:text-ink'
                    ]"
                    @click="isSidebarOpen = false"
                >
                    <ShoppingBagIcon class="w-5 h-5 shrink-0" />
                    <span class="font-medium text-sm">Pembelian Saya</span>
                </Link>

                <Link
                    href="/profile"
                    :class="[
                        'flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200',
                        route().current() === '/profile' || route().current('profile.edit')
                            ? 'bg-terracotta text-cream shadow-sm'
                            : 'text-taupe hover:bg-oat hover:text-ink'
                    ]"
                    @click="isSidebarOpen = false"
                >
                    <Cog6ToothIcon class="w-5 h-5 shrink-0" />
                    <span class="font-medium text-sm">Pengaturan Akun</span>
                </Link>
            </nav>

            <!-- Sidebar Footer -->
            <div class="p-4 border-t border-oat-dark space-y-1 shrink-0">
                <Link
                    href="/"
                    class="flex items-center gap-2 text-sm text-taupe hover:text-terracotta transition-colors px-3 py-2"
                >
                    <ArrowLeftIcon class="w-4 h-4" />
                    Kembali ke Portfolio
                </Link>
                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    type="button"
                    class="w-full flex items-center gap-2 text-sm text-red-600 hover:bg-red-50 transition-colors px-3 py-2 rounded-lg"
                >
                    <ArrowRightOnRectangleIcon class="w-4 h-4" />
                    Keluar
                </Link>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-h-screen w-full lg:ml-64 overflow-x-hidden">
            <!-- Top Navbar -->
            <header class="h-16 bg-paper border-b border-oat-dark flex items-center justify-between px-4 lg:px-6 sticky top-0 z-20">
                <div class="flex items-center gap-4">
                    <button
                        @click="isSidebarOpen = true"
                        aria-label="Buka menu navigasi"
                        class="lg:hidden p-2 -ml-2 text-taupe hover:text-ink hover:bg-oat rounded-lg transition-colors"
                    >
                        <Bars3Icon class="w-6 h-6" />
                    </button>
                    <span class="font-serif text-xl font-bold text-ink hidden sm:block">{{ title || 'Dashboard' }}</span>
                </div>

                <div class="flex items-center gap-4">
                    <CartBadge />
                    <Dropdown align="right" width="48">
                        <template #trigger>
                            <button class="flex items-center gap-3 p-1.5 rounded-xl hover:bg-oat transition-colors focus:outline-none">
                                <div class="w-9 h-9 rounded-lg bg-terracotta/10 flex items-center justify-center">
                                    <span class="font-medium text-terracotta">{{ user?.name?.charAt(0) || 'U' }}</span>
                                </div>
                                <span class="hidden sm:block text-sm font-medium text-ink">{{ user?.name || 'User' }}</span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-taupe" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="6 9 12 15 18 9"/>
                                </svg>
                            </button>
                        </template>

                        <template #content>
                            <div class="px-4 py-3 border-b border-oat-dark">
                                <p class="text-sm font-medium text-ink">{{ user?.name || 'User' }}</p>
                                <p class="text-xs text-taupe truncate">{{ user?.email || 'user@example.com' }}</p>
                            </div>
                            <div class="py-1 border-t border-oat-dark">
                                <Link
                                    :href="route('logout')"
                                    method="post"
                                    as="button"
                                    type="button"
                                    class="w-full flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors text-left"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/>
                                        <polyline points="16 17 21 12 16 7"/>
                                        <line x1="21" y1="12" x2="9" y2="12"/>
                                    </svg>
                                    Logout
                                </Link>
                            </div>
                        </template>
                    </Dropdown>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 p-4 lg:p-8 w-full">
                <slot />
            </main>

            <!-- Footer -->
            <footer class="bg-paper border-t border-oat-dark px-4 lg:px-6 py-4 mt-auto">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-2 text-sm text-taupe">
                    <p>© {{ new Date().getFullYear() }} Febryanus Tambing. All rights reserved.</p>
                    <div class="flex items-center gap-4">
                        <Link href="/" class="hover:text-terracotta transition-colors">
                            Portfolio
                        </Link>
                    </div>
                </div>
            </footer>
        </div>

        <CartDrawer />
    </div>
</template>
