<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineOptions({ layout: AdminLayout });

const props = defineProps({
  invoice: Object,
});

const formatRupiah = (amount) => {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(amount);
};

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
};

const getStatusColor = (status) => {
  const colors = {
    draft: 'bg-gray-100 text-gray-700',
    sent: 'bg-blue-100 text-blue-700',
    paid: 'bg-green-100 text-green-700',
    overdue: 'bg-red-100 text-red-700',
    cancelled: 'bg-gray-200 text-gray-600',
  };
  return colors[status] || colors.draft;
};
</script>

<template>
  <Head title="Invoice" />

  <div class="py-12">
    <div class="max-w-4xl mx-auto px-4">
      <div class="flex items-center justify-between mb-8">
        <div class="flex items-center gap-4">
          <button @click="router.visit(route('admin.finance.invoices.index'))" class="text-taupe hover:text-ink">← Kembali</button>
          <h1 class="text-3xl font-fraunces font-bold text-ink">Invoice {{ invoice.invoice_number }}</h1>
        </div>
        <div class="flex gap-3">
          <a
            :href="route('admin.finance.invoices.pdf', invoice.id)"
            target="_blank"
            class="px-4 py-2 border border-oat rounded-lg text-taupe hover:bg-cream"
          >
            Download PDF
          </a>
          <Link
            v-if="invoice.status === 'draft'"
            :href="route('admin.finance.invoices.edit', invoice.id)"
            class="px-4 py-2 bg-terracotta text-white rounded-lg hover:bg-terracotta/90"
          >
            Edit
          </Link>
        </div>
      </div>

      <div class="bg-paper border border-oat-dark rounded-2xl p-8">
        <!-- Header -->
        <div class="flex justify-between items-start mb-8">
          <div>
            <h2 class="text-2xl font-fraunces font-bold text-terracotta">INVOICE</h2>
            <p class="text-taupe">{{ invoice.invoice_number }}</p>
          </div>
          <span class="px-4 py-2 rounded-full text-sm font-medium" :class="getStatusColor(invoice.status)">
            {{ invoice.status.toUpperCase() }}
          </span>
        </div>

        <!-- Client & Dates -->
        <div class="grid grid-cols-2 gap-8 mb-8">
          <div>
            <h3 class="text-sm font-medium text-taupe mb-2">DITAGIHKAN KE</h3>
            <p class="font-medium text-ink">{{ invoice.client_name }}</p>
            <p class="text-taupe" v-if="invoice.client_company">{{ invoice.client_company }}</p>
            <p class="text-taupe" v-if="invoice.client_email">{{ invoice.client_email }}</p>
          </div>
          <div class="text-right">
            <div class="mb-2">
              <span class="text-sm font-medium text-taupe">Tanggal Terbit: </span>
              <span class="text-ink">{{ formatDate(invoice.issue_date) }}</span>
            </div>
            <div>
              <span class="text-sm font-medium text-taupe">Jatuh Tempo: </span>
              <span class="text-ink" :class="{ 'text-red-600': invoice.is_overdue }">{{ formatDate(invoice.due_date) }}</span>
            </div>
            <div v-if="invoice.paid_at">
              <span class="text-sm font-medium text-taupe">Dibayar: </span>
              <span class="text-green-600">{{ formatDate(invoice.paid_at) }}</span>
            </div>
          </div>
        </div>

        <!-- Items Table -->
        <div class="mb-8">
          <table class="w-full">
            <thead>
              <tr class="border-b-2 border-oat">
                <th class="text-left py-3 text-sm font-medium text-taupe">Deskripsi</th>
                <th class="text-right py-3 text-sm font-medium text-taupe">Qty</th>
                <th class="text-right py-3 text-sm font-medium text-taupe">Harga</th>
                <th class="text-right py-3 text-sm font-medium text-taupe">Total</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in invoice.items" :key="item.id" class="border-b border-oat">
                <td class="py-3 text-ink">{{ item.description }}</td>
                <td class="py-3 text-right text-ink">{{ item.quantity }}</td>
                <td class="py-3 text-right text-ink">{{ formatRupiah(item.unit_price) }}</td>
                <td class="py-3 text-right font-medium text-ink">{{ formatRupiah(item.amount) }}</td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Totals -->
        <div class="flex justify-end mb-8">
          <div class="w-64">
            <div class="flex justify-between py-2">
              <span class="text-taupe">Subtotal</span>
              <span class="text-ink">{{ formatRupiah(invoice.subtotal) }}</span>
            </div>
            <div class="flex justify-between py-2" v-if="invoice.discount > 0">
              <span class="text-taupe">Diskon</span>
              <span class="text-ink">-{{ formatRupiah(invoice.discount) }}</span>
            </div>
            <div class="flex justify-between py-2" v-if="invoice.tax_percentage > 0">
              <span class="text-taupe">Pajak ({{ invoice.tax_percentage }}%)</span>
              <span class="text-ink">{{ formatRupiah(invoice.tax_amount) }}</span>
            </div>
            <div class="flex justify-between py-3 border-t-2 border-oat">
              <span class="text-lg font-fraunces font-semibold text-ink">Total</span>
              <span class="text-lg font-fraunces font-bold text-terracotta">{{ formatRupiah(invoice.total) }}</span>
            </div>
          </div>
        </div>

        <!-- Notes -->
        <div v-if="invoice.notes" class="border-t border-oat pt-6">
          <h3 class="text-sm font-medium text-taupe mb-2">CATATAN</h3>
          <p class="text-ink">{{ invoice.notes }}</p>
        </div>
      </div>
    </div>
  </div>
</template>
