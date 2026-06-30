<script setup>
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineOptions({ layout: AdminLayout });

const props = defineProps({
  order: Object,
});

const statusForm = useForm({
  status: props.order.status,
  notes: props.order.notes,
  agreed_price: props.order.agreed_price || '',
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
  return new Date(date).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
};

const updateStatus = () => {
  statusForm.patch(route('admin.products.orders.update-status', props.order.id), {
    preserveScroll: true,
  });
};
</script>

<template>
  <Head title="Detail Order" />

  <div class="py-12">
    <div class="max-w-3xl mx-auto px-4">
      <div class="flex items-center gap-4 mb-8">
        <button @click="router.visit(route('admin.products.orders.index'))" class="text-taupe hover:text-ink">← Kembali</button>
        <h1 class="text-3xl font-fraunces font-bold text-ink">Detail Order</h1>
      </div>

      <div class="bg-paper border border-oat-dark rounded-2xl p-6 mb-6">
        <div class="flex items-center justify-between mb-6">
          <span class="px-4 py-2 rounded-full text-sm font-medium" :class="getStatusColor(order.status)">
            {{ getStatusLabel(order.status) }}
          </span>
        </div>

        <div class="grid grid-cols-2 gap-6 mb-6">
          <div>
            <h3 class="text-sm font-medium text-taupe mb-1">Klien</h3>
            <p class="font-medium text-ink">{{ order.client_name }}</p>
            <p class="text-sm text-taupe">{{ order.client_email }}</p>
            <p class="text-sm text-taupe" v-if="order.client_phone">{{ order.client_phone }}</p>
            <p class="text-sm text-taupe" v-if="order.client_company">{{ order.client_company }}</p>
          </div>
          <div>
            <h3 class="text-sm font-medium text-taupe mb-1">Produk</h3>
            <p class="font-medium text-ink">{{ order.product?.name }}</p>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-6 mb-6">
          <div>
            <h3 class="text-sm font-medium text-taupe mb-1">Harga Quoted</h3>
            <p class="font-medium text-ink">{{ formatRupiah(order.quoted_price) }}</p>
          </div>
          <div>
            <h3 class="text-sm font-medium text-taupe mb-1">Harga Agreed</h3>
            <p class="font-medium text-ink">{{ formatRupiah(order.agreed_price) }}</p>
          </div>
        </div>

        <div v-if="order.notes" class="border-t border-oat pt-6">
          <h3 class="text-sm font-medium text-taupe mb-2">Catatan</h3>
          <p class="text-ink">{{ order.notes }}</p>
        </div>
      </div>

      <!-- Update Status -->
      <div class="bg-paper border border-oat-dark rounded-2xl p-6">
        <h2 class="text-lg font-fraunces font-semibold text-ink mb-4">Update Status</h2>
        <form @submit.prevent="updateStatus" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-ink mb-1">Status</label>
            <select v-model="statusForm.status" class="w-full px-4 py-2 border border-oat rounded-lg">
              <option value="new">Baru</option>
              <option value="in_discussion">Diskusi</option>
              <option value="in_progress">Sedang Dikerjakan</option>
              <option value="completed">Selesai</option>
              <option value="cancelled">Dibatalkan</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-ink mb-1">Harga Deal</label>
            <input v-model.number="statusForm.agreed_price" type="number" min="0" class="w-full px-4 py-2 border border-oat rounded-lg" />
          </div>
          <div>
            <label class="block text-sm font-medium text-ink mb-1">Catatan</label>
            <textarea v-model="statusForm.notes" class="w-full px-4 py-2 border border-oat rounded-lg" rows="3"></textarea>
          </div>
          <button type="submit" class="w-full px-4 py-2 bg-terracotta text-white rounded-lg hover:bg-terracotta/90" :disabled="statusForm.processing">
            {{ statusForm.processing ? 'Menyimpan...' : 'Update Status' }}
          </button>
        </form>
      </div>
    </div>
  </div>
</template>
