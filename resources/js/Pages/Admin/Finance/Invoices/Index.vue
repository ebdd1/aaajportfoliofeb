<script setup>
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ref } from 'vue';
import { useConfirm } from '@/Composables/useConfirm';

defineOptions({ layout: AdminLayout });

const props = defineProps({
  invoices: Object,
  wallets: Object,
  filters: Object,
  stats: Object,
});

const { open: confirmOpen } = useConfirm();
const showMarkPaidModal = ref(false);
const selectedInvoice = ref(null);
const markPaidForm = useForm({ wallet_id: '' });

const formatRupiah = (amount) => {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(amount);
};

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
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

const getStatusLabel = (status) => {
  const labels = {
    draft: 'Draft',
    sent: 'Terkirim',
    paid: 'Lunas',
    overdue: 'Jatuh Tempo',
    cancelled: 'Dibatalkan',
  };
  return labels[status] || status;
};

const openMarkPaid = (invoice) => {
  selectedInvoice.value = invoice;
  markPaidForm.reset();
  showMarkPaidModal.value = true;
};

const submitMarkPaid = () => {
  markPaidForm.patch(route('admin.finance.invoices.mark-paid', selectedInvoice.value.id), {
    onSuccess: () => { showMarkPaidModal.value = false; selectedInvoice.value = null; },
  });
};

const deleteInvoice = (invoice) => {
  confirmOpen({
    title: 'Hapus Invoice',
    message: `Yakin ingin menghapus invoice "${invoice.invoice_number}"?`,
    onConfirm: () => router.delete(route('admin.finance.invoices.destroy', invoice.id)),
  });
};
</script>

<template>
  <Head title="Invoice" />

  <div class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-fraunces font-bold text-ink">Invoice</h1>
        <Link
          :href="route('admin.finance.invoices.create')"
          class="px-4 py-2 bg-terracotta text-white rounded-lg hover:bg-terracotta/90"
        >
          + Invoice Baru
        </Link>
      </div>

      <!-- Stats -->
      <div class="grid grid-cols-2 gap-4 mb-6">
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
          <p class="text-sm text-yellow-700">Outstanding</p>
          <p class="text-xl font-bold text-yellow-600">{{ formatRupiah(stats.total_outstanding) }}</p>
          <p class="text-xs text-yellow-600">{{ stats.count_outstanding }} invoice</p>
        </div>
      </div>

      <!-- Filter -->
      <div class="bg-paper border border-oat-dark rounded-xl p-4 mb-6">
        <form @submit.prevent="router.get(route('admin.finance.invoices.index'), filters)" class="flex gap-4">
          <select v-model="filters.status" class="px-3 py-2 border border-oat rounded-lg">
            <option value="">Semua Status</option>
            <option value="draft">Draft</option>
            <option value="sent">Terkirim</option>
            <option value="paid">Lunas</option>
            <option value="overdue">Jatuh Tempo</option>
            <option value="cancelled">Dibatalkan</option>
          </select>
          <button type="submit" class="px-4 py-2 bg-terracotta text-white rounded-lg">Filter</button>
        </form>
      </div>

      <div class="bg-paper border border-oat-dark rounded-2xl overflow-hidden">
        <table class="w-full">
          <thead class="bg-cream">
            <tr>
              <th class="text-left py-3 px-4 text-sm font-medium text-taupe">Invoice</th>
              <th class="text-left py-3 px-4 text-sm font-medium text-taupe">Klien</th>
              <th class="text-left py-3 px-4 text-sm font-medium text-taupe">Jatuh Tempo</th>
              <th class="text-right py-3 px-4 text-sm font-medium text-taupe">Total</th>
              <th class="text-center py-3 px-4 text-sm font-medium text-taupe">Status</th>
              <th class="text-center py-3 px-4 text-sm font-medium text-taupe">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="invoice in invoices.data" :key="invoice.id" class="border-t border-oat hover:bg-cream/50">
              <td class="py-3 px-4">
                <Link :href="route('admin.finance.invoices.show', invoice.id)" class="text-terracotta hover:underline font-medium">
                  {{ invoice.invoice_number }}
                </Link>
              </td>
              <td class="py-3 px-4 text-ink">{{ invoice.client_name }}</td>
              <td class="py-3 px-4 text-ink">{{ formatDate(invoice.due_date) }}</td>
              <td class="py-3 px-4 text-right font-medium text-ink">{{ formatRupiah(invoice.total) }}</td>
              <td class="py-3 px-4 text-center">
                <span class="px-2 py-1 rounded-full text-xs font-medium" :class="getStatusColor(invoice.status)">
                  {{ getStatusLabel(invoice.status) }}
                </span>
              </td>
              <td class="py-3 px-4 text-center">
                <div class="flex gap-2 justify-center">
                  <button
                    v-if="['sent', 'overdue'].includes(invoice.status)"
                    @click="openMarkPaid(invoice)"
                    class="text-green-600 hover:text-green-700 text-sm"
                  >
                    Lunas
                  </button>
                  <Link :href="route('admin.finance.invoices.edit', invoice.id)" class="text-terracotta hover:text-terracotta/80 text-sm">Edit</Link>
                  <button
                    v-if="invoice.status === 'draft'"
                    @click="deleteInvoice(invoice)"
                    class="text-red-600 hover:text-red-700 text-sm"
                  >Hapus</button>
                </div>
              </td>
            </tr>
            <tr v-if="invoices.data.length === 0">
              <td colspan="6" class="py-12 text-center text-taupe">Belum ada invoice</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Mark Paid Modal -->
  <div v-if="showMarkPaidModal" class="fixed inset-0 bg-ink/50 flex items-center justify-center z-50">
    <div class="bg-paper rounded-2xl p-6 w-full max-w-md">
      <h2 class="text-xl font-fraunces font-semibold text-ink mb-4">Tandai Lunas</h2>
      <p class="text-taupe mb-4">Invoice {{ selectedInvoice?.invoice_number }} - {{ formatRupiah(selectedInvoice?.total) }}</p>
      <form @submit.prevent="submitMarkPaid" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-ink mb-1">Terima di Dompet</label>
          <select v-model="markPaidForm.wallet_id" class="w-full px-4 py-2 border border-oat rounded-lg" required>
            <option value="">Pilih Dompet</option>
            <option v-for="wallet in wallets" :key="wallet.id" :value="wallet.id">{{ wallet.name }}</option>
          </select>
        </div>
        <div class="flex gap-3">
          <button type="button" @click="showMarkPaidModal = false" class="flex-1 px-4 py-2 border border-oat rounded-lg text-taupe">Batal</button>
          <button type="submit" class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg" :disabled="markPaidForm.processing">Konfirmasi</button>
        </div>
      </form>
    </div>
  </div>
</template>
