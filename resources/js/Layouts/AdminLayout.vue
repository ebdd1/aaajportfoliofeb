<script setup>
import { ref, computed, watchEffect } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import ConfirmProvider from '@/Components/ConfirmProvider.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';

const page = usePage();
const isSidebarOpen = ref(true);
const settingsOpen = ref(false);
const financeOpen = ref(false);
const productOpen = ref(false);
const socialLinksOpen = ref(false);
const blogOpen = ref(false);

// Auto-expand collapsible sections based on current URL
watchEffect(() => {
    financeOpen.value = page.url?.startsWith('/admin/finance');
    productOpen.value = page.url?.startsWith('/admin/products-v2');
    socialLinksOpen.value = page.url?.startsWith('/social-links');
    blogOpen.value = page.url?.startsWith('/admin/blog');
});

const isSettingsActive = computed(() =>
    route().current('admin.profile.edit') ||
    route().current('admin.settings.web')
);

const currentRoute = computed(() => page.props.ziggy?.route || '');

const isActive = (routeName, href) => {
    return currentRoute.value === routeName || route().current(routeName) || (href && page.url === href);
};

const isFinanceActive = computed(() => {
    return currentRoute.value.startsWith('admin.finance') || page.url?.startsWith('/admin/finance');
});

const isSocialLinksActive = computed(() => {
    return page.url?.startsWith('/social-links');
});

const isProductActive = computed(() => {
    return currentRoute.value.startsWith('admin.products') || page.url?.startsWith('/admin/products-v2');
});

const isBlogActive = computed(() => {
    return page.url?.startsWith('/admin/blog');
});

const navItems = computed(() => [
    {
        name: 'Dashboard',
        route: 'admin.dashboard',
        href: '/admin',
        icon: `<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="9" rx="1"/><rect x="14" y="3" width="7" height="5" rx="1"/><rect x="14" y="12" width="7" height="9" rx="1"/><rect x="3" y="16" width="7" height="5" rx="1"/></svg>`,
    },
    {
        name: 'Proyek',
        route: 'admin.projects.index',
        href: route('admin.projects.index'),
        icon: `<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 19a2 2 0 01-2 2H4a2 2 0 01-2-2V5a2 2 0 012-2h5l2 3h9a2 2 0 012 2z"/></svg>`,
    },
    {
        name: 'Keahlian',
        route: 'admin.skills.index',
        href: route('admin.skills.index'),
        icon: `<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>`,
    },
    {
        name: 'Pengalaman',
        route: 'admin.experiences.index',
        href: route('admin.experiences.index'),
        icon: `<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v16"/></svg>`,
    },
    {
        name: 'Sertifikat',
        route: 'admin.certificates.index',
        href: route('admin.certificates.index'),
        icon: `<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/></svg>`,
    },
    {
        name: 'CV',
        route: 'admin.cv.index',
        href: route('admin.cv.index'),
        icon: `<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>`,
    },
    {
        name: 'Produk Digital',
        route: 'admin.products.index',
        href: route('admin.products.index'),
        icon: `<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>`,
    },
    {
        name: 'Social Links',
        route: 'admin.social-links.index',
        href: '/admin/social-links',
        icon: `<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71"/></svg>`,
    },
    {
        name: 'Manajemen Waktu',
        route: 'admin.tasks.index',
        href: '/admin/tasks',
        icon: `<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><path d="M8 14h.01"/><path d="M12 14h.01"/><path d="M16 14h.01"/><path d="M8 18h.01"/><path d="M12 18h.01"/><path d="M16 18h.01"/></svg>`,
    },
    {
        name: 'Pesan',
        route: 'admin.messages.index',
        href: route('admin.messages.index'),
        icon: `<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>`,
    },
]);

const financeItems = [
    { name: 'Dashboard', route: 'admin.finance.dashboard', href: '/admin/finance' },
    { name: 'Transaksi', route: 'admin.finance.transactions.index', href: '/admin/finance/transactions' },
    { name: 'Invoice', route: 'admin.finance.invoices.index', href: '/admin/finance/invoices' },
    { name: 'Dompet', route: 'admin.finance.wallets.index', href: '/admin/finance/wallets' },
    { name: 'Budget', route: 'admin.finance.budgets.index', href: '/admin/finance/budgets' },
    { name: 'Target Tabungan', route: 'admin.finance.savings-goals.index', href: '/admin/finance/savings-goals' },
];

const productItems = [
    { name: 'Dashboard', route: 'admin.products.dashboard', href: '/admin/products-v2' },
    { name: 'Katalog', route: 'admin.products.catalog.index', href: '/admin/products-v2/catalog' },
    { name: 'Order', route: 'admin.products.orders.index', href: '/admin/products-v2/orders' },
];

const blogItems = [
    { name: 'Posts', route: 'admin.blog.posts.index', href: '/admin/blog/posts' },
    { name: 'Kategori', route: 'admin.blog.categories.index', href: '/admin/blog/categories' },
    { name: 'Tags', route: 'admin.blog.tags.index', href: '/admin/blog/tags' },
];
</script>

<template>
  <ConfirmProvider>
    <div class="min-h-screen bg-cream flex">
      <!-- Mobile Backdrop -->
      <div
        v-if="isSidebarOpen"
        class="fixed inset-0 bg-ink/50 z-30 lg:hidden"
        @click="isSidebarOpen = false"
      />

    <!-- Sidebar -->
    <aside
      :class="isSidebarOpen ? 'translate-x-0' : '-translate-x-full'"
      class="fixed left-0 top-0 h-screen w-64 bg-paper border-r border-oat-dark flex-col z-40 transition-transform duration-300 lg:translate-x-0 overflow-y-auto"
    >
      <!-- Logo -->
      <div class="h-16 flex items-center justify-between px-4 border-b border-oat-dark shrink-0">
        <Link href="/admin" class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-xl bg-terracotta flex items-center justify-center shrink-0">
            <span class="font-serif text-xl font-bold text-cream">F</span>
          </div>
          <span class="font-serif text-lg font-bold text-ink">Admin</span>
        </Link>
      </div>

      <!-- Navigation -->
      <nav class="flex-1 py-4 px-3 space-y-1">
        <Link
          v-for="item in navItems"
          :key="item.route"
          :href="item.href"
          :class="[
            'flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200',
            isActive(item.route, item.href)
              ? 'bg-terracotta text-cream'
              : 'text-taupe hover:bg-oat hover:text-ink'
          ]"
        >
          <span v-html="item.icon" class="shrink-0"></span>
          <span class="font-medium text-sm">{{ item.name }}</span>
        </Link>

        <!-- Divider: Keuangan -->
        <div class="pt-4 pb-2">
          <button
            @click="financeOpen = !financeOpen"
            :class="[
              'w-full flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200',
              isFinanceActive
                ? 'bg-terracotta/10 text-terracotta'
                : 'text-taupe hover:bg-oat hover:text-ink'
            ]"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="2" y="6" width="20" height="12" rx="2"/>
              <path d="M12 12h.01"/><path d="M17 12h.01"/><path d="M7 12h.01"/>
            </svg>
            <span class="font-medium text-sm flex-1 text-left">Keuangan</span>
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 transition-transform" :class="financeOpen ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="6 9 12 15 18 9"/>
            </svg>
          </button>
          <div v-show="financeOpen" class="mt-1 ml-4 pl-3 border-l border-oat-dark space-y-0.5">
            <Link
              v-for="item in financeItems"
              :key="item.route"
              :href="item.href"
              class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-all"
              :class="isActive(item.route, item.href) ? 'bg-terracotta text-cream' : 'text-taupe hover:bg-oat hover:text-ink'"
            >
              {{ item.name }}
            </Link>
          </div>
        </div>

        <!-- Divider: Produk -->
        <div class="pt-2 pb-2">
          <button
            @click="productOpen = !productOpen"
            :class="[
              'w-full flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200',
              isProductActive
                ? 'bg-terracotta/10 text-terracotta'
                : 'text-taupe hover:bg-oat hover:text-ink'
            ]"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/>
              <polyline points="3.27 6.96 12 12.01 20.73 6.96"/>
              <line x1="12" y1="22.08" x2="12" y2="12"/>
            </svg>
            <span class="font-medium text-sm flex-1 text-left">Produk</span>
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 transition-transform" :class="productOpen ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="6 9 12 15 18 9"/>
            </svg>
          </button>
          <div v-show="productOpen" class="mt-1 ml-4 pl-3 border-l border-oat-dark space-y-0.5">
            <Link
              v-for="item in productItems"
              :key="item.route"
              :href="item.href"
              class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-all"
              :class="isActive(item.route, item.href) ? 'bg-terracotta text-cream' : 'text-taupe hover:bg-oat hover:text-ink'"
            >
              {{ item.name }}
            </Link>
          </div>
        </div>

        <!-- Divider: Blog -->
        <div class="pt-2 pb-2">
          <button
            @click="blogOpen = !blogOpen"
            :class="[
              'w-full flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200',
              isBlogActive
                ? 'bg-terracotta/10 text-terracotta'
                : 'text-taupe hover:bg-oat hover:text-ink'
            ]"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
              <polyline points="14 2 14 8 20 8"/>
              <line x1="16" y1="13" x2="8" y2="13"/>
              <line x1="16" y1="17" x2="8" y2="17"/>
              <polyline points="10 9 9 9 8 9"/>
            </svg>
            <span class="font-medium text-sm flex-1 text-left">Blog</span>
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 transition-transform" :class="blogOpen ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="6 9 12 15 18 9"/>
            </svg>
          </button>
          <div v-show="blogOpen" class="mt-1 ml-4 pl-3 border-l border-oat-dark space-y-0.5">
            <Link
              v-for="item in blogItems"
              :key="item.route"
              :href="item.href"
              class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-all"
              :class="isActive(item.route, item.href) ? 'bg-terracotta text-cream' : 'text-taupe hover:bg-oat hover:text-ink'"
            >
              {{ item.name }}
            </Link>
          </div>
        </div>
      </nav>

      <!-- Settings group -->
      <div class="px-3 pb-2">
        <button
          @click="settingsOpen = !settingsOpen"
          :class="[
            'w-full flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200',
            isSettingsActive
              ? 'bg-terracotta/10 text-terracotta'
              : 'text-taupe hover:bg-oat hover:text-ink'
          ]"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/>
          </svg>
          <span class="font-medium text-sm flex-1 text-left">Pengaturan</span>
          <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 transition-transform duration-200" :class="settingsOpen ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="6 9 12 15 18 9"/>
          </svg>
        </button>

        <!-- Sub-items -->
        <div v-show="settingsOpen || isSettingsActive" class="mt-1 ml-4 pl-3 border-l border-oat-dark space-y-0.5">
          <Link
            :href="route('admin.profile.edit')"
            :class="[
              'flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-all duration-200',
              isActive('admin.profile.edit')
                ? 'bg-terracotta text-cream'
                : 'text-taupe hover:bg-oat hover:text-ink'
            ]"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/>
            </svg>
            <span>Profil Saya</span>
          </Link>
          <Link
            :href="route('admin.settings.web')"
            :class="[
              'flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-all duration-200',
              isActive('admin.settings.web')
                ? 'bg-terracotta text-cream'
                : 'text-taupe hover:bg-oat hover:text-ink'
            ]"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 010 20M12 2a15.3 15.3 0 000 20"/>
            </svg>
            <span>Pengaturan Web</span>
          </Link>
        </div>
      </div>

      <!-- Sidebar Footer -->
      <div class="p-4 border-t border-oat-dark space-y-1 shrink-0">
        <Link
          :href="route('home')"
          target="_blank"
          class="flex items-center gap-2 text-sm text-taupe hover:text-terracotta transition-colors"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/>
            <polyline points="15 3 21 3 15 3"/>
            <line x1="10" y1="14" x2="21" y2="3"/>
          </svg>
          Lihat Portfolio
        </Link>
        <Link
          :href="route('logout')"
          method="post"
          as="button"
          type="button"
          class="w-full flex items-center gap-2 text-sm text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg px-3 py-2 transition-colors"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/>
            <polyline points="16 17 21 12 16 7"/>
            <line x1="21" y1="12" x2="9" y2="12"/>
          </svg>
          Logout
        </Link>
      </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-h-screen w-full lg:ml-64">
      <!-- Top Navbar -->
      <header class="h-16 bg-paper border-b border-oat-dark flex items-center justify-between px-4 lg:px-6 sticky top-0 z-20">
        <div class="flex items-center gap-4">
          <button
            @click="isSidebarOpen = !isSidebarOpen"
            class="lg:hidden p-2 -ml-2 text-taupe hover:text-ink hover:bg-oat rounded-lg transition-colors"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
          </button>
          <span class="font-serif text-xl font-bold text-ink">Portfolio Admin</span>
        </div>

        <div class="flex items-center gap-4">
          <Link
            :href="route('home')"
            target="_blank"
            class="hidden lg:flex items-center gap-2 text-sm text-taupe hover:text-terracotta transition-colors"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/>
              <polyline points="15 3 21 3 15 3"/>
              <line x1="10" y1="14" x2="21" y2="3"/>
            </svg>
            Lihat Portfolio
          </Link>

          <Dropdown align="right" width="48">
            <template #trigger>
              <button class="flex items-center gap-3 p-1.5 rounded-xl hover:bg-oat transition-colors">
                <div class="w-9 h-9 rounded-lg bg-terracotta/10 flex items-center justify-center">
                  <span class="font-medium text-terracotta">{{ $page.props.auth.user.name.charAt(0) }}</span>
                </div>
                <span class="hidden sm:block text-sm font-medium text-ink">{{ $page.props.auth.user.name }}</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-taupe" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <polyline points="6 9 12 15 18 9"/>
                </svg>
              </button>
            </template>

            <template #content>
              <div class="px-4 py-3 border-b border-oat-dark">
                <p class="text-sm font-medium text-ink">{{ $page.props.auth.user.name }}</p>
                <p class="text-xs text-taupe truncate">{{ $page.props.auth.user.email }}</p>
              </div>
              <div class="py-1">
                <DropdownLink :href="route('admin.profile.edit')" class="flex items-center gap-2">
                  Edit Profil
                </DropdownLink>
                <DropdownLink :href="route('home')" target="_blank" class="flex items-center gap-2">
                  Lihat Portfolio
                </DropdownLink>
              </div>
              <div class="py-1 border-t border-oat-dark">
                <Link
                  :href="route('logout')"
                  method="post"
                  as="button"
                  type="button"
                  class="w-full flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors"
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
      <main class="flex-1 p-4 lg:p-6 mt-auto">
        <slot />
      </main>

      <!-- Footer -->
      <footer class="bg-oat border-t border-oat-dark px-4 lg:px-6 py-4">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-2 text-sm text-taupe">
          <p>© {{ new Date().getFullYear() }} Febryanus Tambing. All rights reserved.</p>
          <div class="flex items-center gap-4">
            <Link :href="route('home')" target="_blank" class="hover:text-terracotta transition-colors">
              Portfolio
            </Link>
            <span class="text-oat-dark">|</span>
            <Link :href="route('admin.dashboard')" class="hover:text-terracotta transition-colors">
              Admin Dashboard
            </Link>
          </div>
        </div>
      </footer>
    </div>
  </div>
  </ConfirmProvider>
</template>
