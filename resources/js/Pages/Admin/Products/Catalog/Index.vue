<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { CubeIcon } from '@heroicons/vue/24/outline';

defineOptions({ layout: AdminLayout });

const props = defineProps({
  products: Object,
  filters: Object,
  categories: {
    type: Array,
    default: () => [],
  },
});

const getStatusColor = (status) => {
  const colors = {
    idea: 'bg-gray-100 text-gray-700',
    building: 'bg-yellow-100 text-yellow-700',
    active: 'bg-green-100 text-green-700',
    paused: 'bg-orange-100 text-orange-700',
    discontinued: 'bg-red-100 text-red-700',
  };
  return colors[status] || '';
};

const getStatusLabel = (status) => {
  const labels = {
    idea: 'Ide',
    building: 'Dibangun',
    active: 'Aktif',
    paused: 'Dijeda',
    discontinued: 'Dihentikan',
  };
  return labels[status] || status;
};

const getTypeLabel = (type) => {
  const labels = {
    service: 'Jasa',
    digital_product: 'Produk Digital',
    saas: 'SaaS',
    physical: 'Fisik',
  };
  return labels[type] || type;
};

const formatRupiah = (amount) => {
  if (amount === null) return 'Hubungi';
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(amount);
};
</script>

<template>
  <Head title="Katalog Produk" />

  <div class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-fraunces font-bold text-ink">Katalog Produk</h1>
        <Link :href="route('admin.products.catalog.create')" class="px-4 py-2 bg-terracotta text-white rounded-lg hover:bg-terracotta/90">
          + Produk Baru
        </Link>
      </div>

      <!-- Filters -->
      <div class="bg-paper border border-oat-dark rounded-xl p-4 mb-6">
        <form @submit.prevent="router.get(route('admin.products.catalog.index'), filters)" class="flex gap-4 flex-wrap">
          <select v-model="filters.status" class="px-3 py-2 border border-oat rounded-lg">
            <option value="">Semua Status</option>
            <option value="idea">Ide</option>
            <option value="building">Dibangun</option>
            <option value="active">Aktif</option>
            <option value="paused">Dijeda</option>
            <option value="discontinued">Dihentikan</option>
          </select>
          <select v-model="filters.type" class="px-3 py-2 border border-oat rounded-lg">
            <option value="">Semua Tipe</option>
            <option value="service">Jasa</option>
            <option value="digital_product">Produk Digital</option>
            <option value="saas">SaaS</option>
            <option value="physical">Fisik</option>
          </select>
          <select v-model="filters.category" class="px-3 py-2 border border-oat rounded-lg">
            <option value="">Semua Kategori</option>
            <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
          </select>
          <button type="submit" class="px-4 py-2 bg-terracotta text-white rounded-lg">Filter</button>
        </form>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div v-for="product in products.data" :key="product.id" class="bg-paper border border-oat-dark rounded-2xl p-6">
          <div class="flex items-start justify-between mb-2">
            <div>
              <h3 class="font-fraunces font-semibold text-ink text-lg">{{ product.name }}</h3>
              <div class="flex items-center gap-2 text-sm text-taupe">
                <span>{{ getTypeLabel(product.type) }}</span>
                <span v-if="product.category">• {{ product.category.name }}</span>
              </div>
            </div>
            <span class="px-2 py-1 rounded-full text-xs font-medium" :class="getStatusColor(product.status)">
              {{ getStatusLabel(product.status) }}
            </span>
          </div>

          <p class="text-sm text-taupe mb-4 line-clamp-2" v-if="product.short_description">
            {{ product.short_description }}
          </p>

          <div class="flex items-center justify-between mb-4">
            <span class="text-lg font-bold text-terracotta">{{ formatRupiah(product.price) }}</span>
            <div class="flex items-center gap-3 text-sm text-taupe">
              <CubeIcon class="w-4 h-4 mr-1" /> {{ product.orders_count || 0 }}
            </div>
          </div>

          <div class="flex gap-2">
            <Link :href="route('admin.products.catalog.edit', product.id)" class="flex-1 px-3 py-2 border border-oat rounded-lg text-center text-taupe hover:bg-cream text-sm">
              Edit
            </Link>
            <Link :href="route('admin.products.roadmap.index', product.id)" class="flex-1 px-3 py-2 border border-oat rounded-lg text-center text-taupe hover:bg-cream text-sm">
              Roadmap
            </Link>
            <button
              @click="router.patch(route('admin.products.catalog.toggle-public', product.id))" class="flex-1 px-3 py-2 rounded-lg text-center text-sm"
              :class="product.is_public ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'"
            >
              {{ product.is_public ? 'Public' : 'Private' }}
            </button>
          </div>
        </div>
        <div v-if="products.data.length === 0" class="col-span-full text-center text-taupe py-12 bg-paper rounded-xl border border-dashed border-oat">
          <p>Belum ada produk</p>
        </div>
      </div>
    </div>
  </div>
</template>
