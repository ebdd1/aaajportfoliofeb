<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { computed, ref, onMounted } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { CubeIcon, EnvelopeIcon, CurrencyDollarIcon, ChevronRightIcon } from '@heroicons/vue/24/outline';

defineOptions({ layout: AdminLayout });

const props = defineProps({
  productsSummary: { type: Array, default: () => [] },
  newOrders: { type: Array, default: () => [] },
  recentOrders: { type: Array, default: () => [] },
  pipelineByStatus: { type: Object, default: () => ({}) },
  stats: { type: Object, default: () => ({}) },
});

const isLoading = ref(true);

onMounted(() => {
  // Inertia already loaded data, just show brief loading for perceived performance
  setTimeout(() => isLoading.value = false, 150);
});

// Safe accessors dengan fallback untuk prevent crash
const safeStats = computed(() => ({
  active_products: props.stats?.active_products ?? 0,
  new_orders_count: props.stats?.new_orders_count ?? 0,
  revenue_this_month: props.stats?.revenue_this_month ?? 0,
}));

const safePipeline = computed(() => ({
  new: props.pipelineByStatus?.new ?? 0,
  in_discussion: props.pipelineByStatus?.in_discussion ?? 0,
  in_progress: props.pipelineByStatus?.in_progress ?? 0,
  completed: props.pipelineByStatus?.completed ?? 0,
  cancelled: props.pipelineByStatus?.cancelled ?? 0,
}));

const formatRupiah = (amount) => {
  if (!amount) return 'Rp 0';
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(amount);
};

const getStatusColor = (status) => {
  const colors = {
    new: 'bg-yellow-100 text-yellow-700',
    in_discussion: 'bg-blue-100 text-blue-700',
    in_progress: 'bg-purple-100 text-purple-700',
    completed: 'bg-green-100 text-green-700',
    cancelled: 'bg-gray-100 text-gray-600',
  };
  return colors[status] || '';
};

const getStatusLabel = (status) => {
  const labels = {
    new: 'Baru',
    in_discussion: 'Diskusi',
    in_progress: 'Dikerjakan',
    completed: 'Selesai',
    cancelled: 'Batal',
  };
  return labels[status] || status;
};

const totalOrders = () => {
  return Object.values(props.pipelineByStatus).reduce((a, b) => a + b, 0);
};
</script>

<template>
  <Head title="Dashboard Produk" />

  <div class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <h1 class="text-3xl font-fraunces font-bold text-ink mb-8">Dashboard Produk</h1>

      <!-- Summary Cards -->
      <div v-if="isLoading" class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div v-for="i in 3" :key="i" class="bg-paper border border-oat-dark rounded-2xl p-6">
          <div class="animate-pulse">
            <div class="flex items-center gap-3 mb-2">
              <div class="w-10 h-10 bg-oat rounded-lg"></div>
              <div class="h-4 bg-oat rounded w-24"></div>
            </div>
            <div class="h-8 bg-oat rounded w-16 mt-2"></div>
          </div>
        </div>
      </div>

      <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-paper border border-oat-dark rounded-2xl p-6">
          <div class="flex items-center gap-3 mb-2">
            <div class="w-10 h-10 bg-terracotta/10 rounded-lg flex items-center justify-center">
              <CubeIcon class="w-5 h-5 text-terracotta"/>
            </div>
            <span class="text-taupe text-sm">Produk Aktif</span>
          </div>
          <p class="text-3xl font-bold text-ink">{{ safeStats.active_products }}</p>
        </div>
        <div class="bg-paper border border-oat-dark rounded-2xl p-6">
          <div class="flex items-center gap-3 mb-2">
            <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
              <EnvelopeIcon class="w-5 h-5 text-yellow-600"/>
            </div>
            <span class="text-taupe text-sm">Order Baru</span>
          </div>
          <p class="text-3xl font-bold text-yellow-600">{{ safeStats.new_orders_count }}</p>
        </div>
        <div class="bg-paper border border-oat-dark rounded-2xl p-6">
          <div class="flex items-center gap-3 mb-2">
            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
              <CurrencyDollarIcon class="w-5 h-5 text-green-600"/>
            </div>
            <span class="text-taupe text-sm">Revenue Bulan Ini</span>
          </div>
          <p class="text-2xl font-bold text-green-600">{{ formatRupiah(safeStats.revenue_this_month) }}</p>
        </div>
      </div>

      <!-- Pipeline -->
      <div class="bg-paper border border-oat-dark rounded-2xl p-6 mb-8">
        <h2 class="text-lg font-fraunces font-semibold text-ink mb-4">Pipeline Order</h2>
        <div class="flex gap-2">
          <div class="flex-1 text-center p-4 bg-yellow-50 rounded-xl">
            <p class="text-2xl font-bold text-yellow-600">{{ safePipeline.new }}</p>
            <p class="text-sm text-yellow-700">Baru</p>
          </div>
          <div class="flex-1 text-center p-4 bg-blue-50 rounded-xl">
            <p class="text-2xl font-bold text-blue-600">{{ safePipeline.in_discussion }}</p>
            <p class="text-sm text-blue-700">Diskusi</p>
          </div>
          <div class="flex-1 text-center p-4 bg-purple-50 rounded-xl">
            <p class="text-2xl font-bold text-purple-600">{{ safePipeline.in_progress }}</p>
            <p class="text-sm text-purple-700">Dikerjakan</p>
          </div>
          <div class="flex-1 text-center p-4 bg-green-50 rounded-xl">
            <p class="text-2xl font-bold text-green-600">{{ safePipeline.completed }}</p>
            <p class="text-sm text-green-700">Selesai</p>
          </div>
        </div>
      </div>

      <!-- Recent Orders -->
      <div class="bg-paper border border-oat-dark rounded-2xl p-6 mb-8">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-lg font-fraunces font-semibold text-ink">Order Terbaru</h2>
          <Link :href="route('admin.products.orders.index')" class="text-sm text-terracotta hover:text-terracotta/80">Lihat Semua →</Link>
        </div>
        <div class="space-y-3">
          <Link v-for="order in recentOrders"
                :key="order.id"
                :href="route('admin.products.orders.show', order.id)"
                class="flex items-center justify-between py-3 border-b border-oat last:border-0 hover:bg-cream/30 transition-colors cursor-pointer">
            <div>
              <p class="font-medium text-ink">{{ order.client_name }}</p>
              <p class="text-sm text-taupe">{{ order.product?.name }}</p>
            </div>
            <span class="px-2 py-1 rounded-full text-xs font-medium" :class="getStatusColor(order.status)">
              {{ getStatusLabel(order.status) }}
            </span>
          </Link>
          <div v-if="recentOrders.length === 0" class="text-center text-taupe py-8">
            <p>Belum ada order</p>
          </div>
        </div>
      </div>

      <!-- Products Grid -->
      <div class="bg-paper border border-oat-dark rounded-2xl p-6">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-lg font-fraunces font-semibold text-ink">Produk Saya</h2>
          <Link :href="route('admin.products.catalog.index')" class="text-sm text-terracotta hover:text-terracotta/80">Kelola →</Link>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <Link v-for="product in productsSummary"
                :key="product.id"
                :href="route('admin.products.catalog.edit', product.id)"
                class="block border border-oat rounded-xl p-4 hover:border-terracotta hover:shadow-md transition-all cursor-pointer group">
            <div class="flex items-center justify-between mb-2">
              <h3 class="font-medium text-ink group-hover:text-terracotta transition-colors">{{ product.name }}</h3>
              <span class="px-2 py-1 rounded-full text-xs bg-cream text-taupe">{{ product.type }}</span>
            </div>
            <div class="grid grid-cols-2 gap-2 text-sm mb-3">
              <div>
                <span class="text-taupe">Order:</span>
                <span class="font-medium text-ink ml-1">{{ product.total_orders }}</span>
              </div>
              <div>
                <span class="text-taupe">Aktif:</span>
                <span class="font-medium text-ink ml-1">{{ product.active_orders }}</span>
              </div>
            </div>
            <div class="flex items-center gap-1 text-xs text-terracotta opacity-0 group-hover:opacity-100 transition-opacity">
              <span>Kelola produk</span>
              <ChevronRightIcon class="w-4 h-4" />
            </div>
          </Link>
          <div v-if="productsSummary.length === 0" class="col-span-full text-center text-taupe py-8">
            <p>Belum ada produk</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
