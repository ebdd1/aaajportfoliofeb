<script setup>
import { Head, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineOptions({ layout: AdminLayout });

const form = useForm({
  wallet_id: '',
  type: 'expense',
  category_id: '',
  amount: '',
  description: '',
  date: new Date().toISOString().split('T')[0],
  notes: '',
});

const submit = () => {
  form.post(route('admin.finance.transactions.store'), {
    onSuccess: () => router.visit(route('admin.finance.transactions.index')),
  });
};
</script>

<template>
  <Head title="Tambah Transaksi" />
  <div class="py-12">
    <div class="max-w-2xl mx-auto px-4">
      <div class="flex items-center gap-4 mb-8">
        <button @click="router.visit(route('admin.finance.transactions.index'))" class="text-taupe hover:text-ink">← Kembali</button>
        <h1 class="text-3xl font-fraunces font-bold text-ink">Transaksi Baru</h1>
      </div>
      <div class="bg-paper border border-oat-dark rounded-2xl p-6">
        <form @submit.prevent="submit" class="space-y-6">
          <div>
            <label class="block text-sm font-medium text-ink mb-2">Tipe Transaksi</label>
            <div class="flex gap-6">
              <label class="flex items-center gap-2 cursor-pointer">
                <input v-model="form.type" type="radio" value="income" class="w-5 h-5 text-green-600" />
                <span class="text-green-600 font-medium">Pemasukan</span>
              </label>
              <label class="flex items-center gap-2 cursor-pointer">
                <input v-model="form.type" type="radio" value="expense" class="w-5 h-5 text-red-600" />
                <span class="text-red-600 font-medium">Pengeluaran</span>
              </label>
            </div>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-ink mb-1">Dompet *</label>
              <select v-model="form.wallet_id" class="w-full px-4 py-2 border border-oat rounded-lg" required>
                <option value="">Pilih Dompet</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-ink mb-1">Kategori *</label>
              <select v-model="form.category_id" class="w-full px-4 py-2 border border-oat rounded-lg" required>
                <option value="">Pilih Kategori</option>
              </select>
            </div>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-ink mb-1">Jumlah *</label>
              <input v-model.number="form.amount" type="number" min="1" class="w-full px-4 py-2 border border-oat rounded-lg" required />
            </div>
            <div>
              <label class="block text-sm font-medium text-ink mb-1">Tanggal *</label>
              <input v-model="form.date" type="date" class="w-full px-4 py-2 border border-oat rounded-lg" required />
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-ink mb-1">Deskripsi *</label>
            <input v-model="form.description" type="text" class="w-full px-4 py-2 border border-oat rounded-lg" required />
          </div>
          <div>
            <label class="block text-sm font-medium text-ink mb-1">Catatan</label>
            <textarea v-model="form.notes" class="w-full px-4 py-2 border border-oat rounded-lg" rows="3"></textarea>
          </div>
          <div class="flex gap-4 pt-4">
            <button type="button" @click="router.visit(route('admin.finance.transactions.index'))" class="flex-1 px-6 py-3 border border-oat rounded-xl text-taupe hover:bg-cream">Batal</button>
            <button type="submit" class="flex-1 px-6 py-3 bg-terracotta text-white rounded-xl hover:bg-terracotta/90" :disabled="form.processing">
              {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>
