<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ref } from 'vue';
import { useConfirm } from '@/Composables/useConfirm';

import { BanknotesIcon } from '@heroicons/vue/24/outline';

defineOptions({ layout: AdminLayout });

const props = defineProps({
  wallets: Object,
  filters: Object,
});

const { open: confirmOpen } = useConfirm();
const showModal = ref(false);
const editingWallet = ref(null);

const form = useForm({
  name: '',
  type: 'bank',
  balance: 0,
  color: '#6b7280',
  icon: 'WalletIcon',
  is_active: true,
});

const openCreate = () => {
  editingWallet.value = null;
  form.reset();
  showModal.value = true;
};

const openEdit = (wallet) => {
  editingWallet.value = wallet;
  form.name = wallet.name;
  form.type = wallet.type;
  form.balance = wallet.balance;
  form.color = wallet.color;
  form.icon = wallet.icon;
  form.is_active = wallet.is_active;
  showModal.value = true;
};

const submit = () => {
  if (editingWallet.value) {
    form.put(route('admin.finance.wallets.update', editingWallet.value.id), {
      onSuccess: () => { showModal.value = false; form.reset(); },
    });
  } else {
    form.post(route('admin.finance.wallets.store'), {
      onSuccess: () => { showModal.value = false; form.reset(); },
    });
  }
};

const deleteWallet = (wallet) => {
  confirmOpen({
    title: 'Hapus Dompet',
    message: `Yakin ingin menghapus dompet "${wallet.name}"?`,
    onConfirm: () => {
      const deleteForm = useForm();
      deleteForm.delete(route('admin.finance.wallets.destroy', wallet.id));
    },
  });
};

const formatRupiah = (amount) => {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(amount);
};
</script>

<template>
  <Head title="Kelola Dompet" />

  <div class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-fraunces font-bold text-ink">Kelola Dompet</h1>
        <button
          @click="openCreate"
          class="px-4 py-2 bg-terracotta text-white rounded-lg hover:bg-terracotta/90 transition-colors"
        >
          + Dompet Baru
        </button>
      </div>

      <div class="bg-paper border border-oat-dark rounded-2xl overflow-hidden">
        <table class="w-full">
          <thead class="bg-cream">
            <tr>
              <th class="text-left py-4 px-6 text-sm font-medium text-taupe">Nama</th>
              <th class="text-left py-4 px-6 text-sm font-medium text-taupe">Tipe</th>
              <th class="text-right py-4 px-6 text-sm font-medium text-taupe">Saldo</th>
              <th class="text-center py-4 px-6 text-sm font-medium text-taupe">Status</th>
              <th class="text-center py-4 px-6 text-sm font-medium text-taupe">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="wallet in wallets.data" :key="wallet.id" class="border-t border-oat hover:bg-cream/50">
              <td class="py-4 px-6">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 rounded-lg flex items-center justify-center" :style="{ backgroundColor: wallet.color + '20' }">
                    <BanknotesIcon class="w-5 h-5" :style="{ color: wallet.color }" />
                  </div>
                  <span class="font-medium text-ink">{{ wallet.name }}</span>
                </div>
              </td>
              <td class="py-4 px-6 text-ink capitalize">{{ wallet.type }}</td>
              <td class="py-4 px-6 text-right font-bold text-ink">{{ formatRupiah(wallet.balance) }}</td>
              <td class="py-4 px-6 text-center">
                <span
                  :class="wallet.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'"
                  class="px-2 py-1 rounded-full text-xs font-medium"
                >
                  {{ wallet.is_active ? 'Aktif' : 'Nonaktif' }}
                </span>
              </td>
              <td class="py-4 px-6 text-center">
                <button @click="openEdit(wallet)" class="text-terracotta hover:text-terracotta/80 mr-3">Edit</button>
                <button @click="deleteWallet(wallet)" class="text-red-600 hover:text-red-700">Hapus</button>
              </td>
            </tr>
            <tr v-if="wallets.data.length === 0">
              <td colspan="5" class="py-12 text-center text-taupe">
                Belum ada dompet. <button @click="openCreate" class="text-terracotta hover:underline">Tambah sekarang</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="mt-4 flex justify-center" v-if="wallets.last_page > 1">
        <div class="flex gap-2">
          <Link
            v-for="link in wallets.links"
            :key="link.label"
            :href="link.url"
            v-html="link.label"
            class="px-3 py-1 rounded-lg text-sm"
            :class="link.active ? 'bg-terracotta text-white' : 'bg-paper text-taupe hover:bg-cream'"
          />
        </div>
      </div>
    </div>
  </div>

  <div v-if="showModal" class="fixed inset-0 bg-ink/50 flex items-center justify-center z-50" @click.self="showModal = false">
    <div class="bg-paper rounded-2xl p-6 w-full max-w-md">
      <h2 class="text-xl font-fraunces font-semibold text-ink mb-4">
        {{ editingWallet ? 'Edit Dompet' : 'Dompet Baru' }}
      </h2>
      <form @submit.prevent="submit" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-ink mb-1">Nama</label>
          <input v-model="form.name" type="text" class="w-full px-4 py-2 border border-oat rounded-lg" required />
        </div>
        <div>
          <label class="block text-sm font-medium text-ink mb-1">Tipe</label>
          <select v-model="form.type" class="w-full px-4 py-2 border border-oat rounded-lg">
            <option value="bank">Bank</option>
            <option value="ewallet">E-Wallet</option>
            <option value="cash">Cash</option>
            <option value="savings">Tabungan</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-ink mb-1">Saldo Awal</label>
          <input v-model.number="form.balance" type="number" step="0.01" min="0" class="w-full px-4 py-2 border border-oat rounded-lg" />
        </div>
        <div>
          <label class="block text-sm font-medium text-ink mb-1">Warna</label>
          <div class="flex gap-2">
            <input v-model="form.color" type="color" class="w-12 h-10 rounded border border-oat" />
            <input v-model="form.color" type="text" class="flex-1 px-4 py-2 border border-oat rounded-lg" />
          </div>
        </div>
        <div class="flex items-center gap-2">
          <input v-model="form.is_active" type="checkbox" id="is_active" class="w-4 h-4" />
          <label for="is_active" class="text-sm text-ink">Aktif</label>
        </div>
        <div class="flex gap-3 pt-4">
          <button type="button" @click="showModal = false" class="flex-1 px-4 py-2 border border-oat rounded-lg text-taupe hover:bg-cream">Batal</button>
          <button type="submit" class="flex-1 px-4 py-2 bg-terracotta text-white rounded-lg hover:bg-terracotta/90" :disabled="form.processing">
            {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
