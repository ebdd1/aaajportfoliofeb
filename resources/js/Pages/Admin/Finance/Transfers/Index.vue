<script setup>
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ref } from 'vue';
import { useConfirm } from '@/Composables/useConfirm';

defineOptions({ layout: AdminLayout });

const props = defineProps({
  transfers: Object,
  wallets: Object,
  filters: Object,
});

const { open: confirmOpen } = useConfirm();
const showModal = ref(false);

const form = useForm({
  from_wallet_id: '',
  to_wallet_id: '',
  amount: '',
  fee: 0,
  description: '',
  date: new Date().toISOString().split('T')[0],
});

const formatRupiah = (amount) => {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(amount);
};

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
};

const openCreate = () => {
  form.reset();
  showModal.value = true;
};

const submit = () => {
  form.post(route('admin.finance.transfers.store'), {
    onSuccess: () => { showModal.value = false; form.reset(); },
  });
};

const deleteTransfer = (transfer) => {
  confirmOpen({
    title: 'Hapus Transfer',
    message: `Yakin ingin menghapus transfer ini?`,
    onConfirm: () => router.delete(route('admin.finance.transfers.destroy', transfer.id)),
  });
};
</script>

<template>
  <Head title="Transfer" />

  <div class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-fraunces font-bold text-ink">Transfer</h1>
        <button @click="openCreate" class="px-4 py-2 bg-terracotta text-white rounded-lg hover:bg-terracotta/90">
          + Transfer Baru
        </button>
      </div>

      <div class="bg-paper border border-oat-dark rounded-2xl overflow-hidden">
        <table class="w-full">
          <thead class="bg-cream">
            <tr>
              <th class="text-left py-3 px-4 text-sm font-medium text-taupe">Tanggal</th>
              <th class="text-left py-3 px-4 text-sm font-medium text-taupe">Dari</th>
              <th class="text-left py-3 px-4 text-sm font-medium text-taupe">Ke</th>
              <th class="text-right py-3 px-4 text-sm font-medium text-taupe">Jumlah</th>
              <th class="text-right py-3 px-4 text-sm font-medium text-taupe">Fee</th>
              <th class="text-center py-3 px-4 text-sm font-medium text-taupe">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="transfer in transfers.data" :key="transfer.id" class="border-t border-oat hover:bg-cream/50">
              <td class="py-3 px-4 text-ink">{{ formatDate(transfer.date) }}</td>
              <td class="py-3 px-4 text-ink">{{ transfer.from_wallet?.name }}</td>
              <td class="py-3 px-4 text-ink">{{ transfer.to_wallet?.name }}</td>
              <td class="py-3 px-4 text-right font-medium text-ink">{{ formatRupiah(transfer.amount) }}</td>
              <td class="py-3 px-4 text-right text-taupe">{{ formatRupiah(transfer.fee) }}</td>
              <td class="py-3 px-4 text-center">
                <button @click="deleteTransfer(transfer)" class="text-red-600 hover:text-red-700 text-sm">Hapus</button>
              </td>
            </tr>
            <tr v-if="transfers.data.length === 0">
              <td colspan="6" class="py-12 text-center text-taupe">Belum ada transfer</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Modal -->
  <div v-if="showModal" class="fixed inset-0 bg-ink/50 flex items-center justify-center z-50" @click.self="showModal = false">
    <div class="bg-paper rounded-2xl p-6 w-full max-w-md">
      <h2 class="text-xl font-fraunces font-semibold text-ink mb-4">Transfer Baru</h2>
      <form @submit.prevent="submit" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-ink mb-1">Dari Dompet</label>
          <select v-model="form.from_wallet_id" class="w-full px-4 py-2 border border-oat rounded-lg" required>
            <option value="">Pilih Dompet</option>
            <option v-for="wallet in wallets" :key="wallet.id" :value="wallet.id">{{ wallet.name }}</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-ink mb-1">Ke Dompet</label>
          <select v-model="form.to_wallet_id" class="w-full px-4 py-2 border border-oat rounded-lg" required>
            <option value="">Pilih Dompet</option>
            <option v-for="wallet in wallets" :key="wallet.id" :value="wallet.id">{{ wallet.name }}</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-ink mb-1">Jumlah</label>
          <input v-model.number="form.amount" type="number" min="1" class="w-full px-4 py-2 border border-oat rounded-lg" required />
        </div>
        <div>
          <label class="block text-sm font-medium text-ink mb-1">Fee</label>
          <input v-model.number="form.fee" type="number" min="0" class="w-full px-4 py-2 border border-oat rounded-lg" />
        </div>
        <div>
          <label class="block text-sm font-medium text-ink mb-1">Tanggal</label>
          <input v-model="form.date" type="date" class="w-full px-4 py-2 border border-oat rounded-lg" required />
        </div>
        <div class="flex gap-3 pt-2">
          <button type="button" @click="showModal = false" class="flex-1 px-4 py-2 border border-oat rounded-lg text-taupe">Batal</button>
          <button type="submit" class="flex-1 px-4 py-2 bg-terracotta text-white rounded-lg" :disabled="form.processing">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</template>
