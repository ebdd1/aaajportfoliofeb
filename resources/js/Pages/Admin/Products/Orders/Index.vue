<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineOptions({ layout: AdminLayout });

const props = defineProps({
  orders: Object,
  products: Object,
  filters: Object,
});

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

const formatRupiah = (amount) => {
  if (!amount) return '-';
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(amount);
};

const formatDate = (date) => {
  if (!date) return '-';
  return new Date(date).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
};
</script>

<template>
  <Head title="Order Masuk" />

  <div class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-fraunces font-bold text-ink">Order Masuk</h1>
      </div>

      <!-- Filters -->
      <div class="bg-paper border border-oat-dark rounded-xl p-4 mb-6">
        <form @submit.prevent="router.get(route('admin.products.orders.index'), filters)" class="flex gap-4">
          <select v-model="filters.product_id" class="px-3 py-2 border border-oat rounded-lg">
            <option value="">Semua Produk</option>
            <option v-for="product in products" :key="product.id" :value="product.id">{{ product.name }}</option>
          </select>
          <select v-model="filters.status" class="px-3 py-2 border border-oat rounded-lg">
            <option value="">Semua Status</option>
            <option value="new">Baru</option>
            <option value="in_discussion">Diskusi</option>
            <option value="in_progress">Dikerjakan</option>
            <option value="completed">Selesai</option>
            <option value="cancelled">Batal</option>
          </select>
          <button type="submit" class="px-4 py-2 bg-terracotta text-white rounded-lg">Filter</button>
        </form>
      </div>

      <div class="bg-paper border border-oat-dark rounded-2xl overflow-hidden">
        <table class="w-full">
          <thead class="bg-cream">
            <tr>
              <th class="text-left py-3 px-4 text-sm font-medium text-taupe">Klien</th>
              <th class="text-left py-3 px-4 text-sm font-medium text-taupe">Produk</th>
              <th class="text-left py-3 px-4 text-sm font-medium text-taupe">Tanggal</th>
              <th class="text-right py-3 px-4 text-sm font-medium text-taupe">Harga</th>
              <th class="text-center py-3 px-4 text-sm font-medium text-taupe">Status</th>
              <th class="text-center py-3 px-4 text-sm font-medium text-taupe">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="order in orders.data" :key="order.id" class="border-t border-oat hover:bg-cream/50">
              <td class="py-3 px-4">
                <p class="font-medium text-ink">{{ order.client_name }}</p>
                <p class="text-sm text-taupe">{{ order.client_email }}</p>
              </td>
              <td class="py-3 px-4 text-ink">{{ order.product?.name }}</td>
              <td class="py-3 px-4 text-ink">{{ formatDate(order.created_at) }}</td>
              <td class="py-3 px-4 text-right font-medium text-ink">{{ formatRupiah(order.agreed_price || order.quoted_price) }}</td>
              <td class="py-3 px-4 text-center">
                <span class="px-2 py-1 rounded-full text-xs font-medium" :class="getStatusColor(order.status)">
                  {{ getStatusLabel(order.status) }}
                </span>
              </td>
              <td class="py-3 px-4 text-center">
                <Link :href="route('admin.products.orders.show', order.id)" class="text-terracotta hover:text-terracotta/80 text-sm">Detail</Link>
              </td>
            </tr>
            <tr v-if="orders.data.length === 0">
              <td colspan="6" class="py-12 text-center text-taupe">Belum ada order</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>
