<script setup>
import { Head, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineOptions({ layout: AdminLayout });

const form = useForm({
  name: '',
  type: 'bank',
  balance: 0,
  color: '#6b7280',
  icon: 'WalletIcon',
  is_active: true,
});

const submit = () => {
  form.post(route('admin.finance.wallets.store'), {
    onSuccess: () => router.visit(route('admin.finance.wallets.index')),
  });
};
</script>

<template>
  <Head title="Tambah Dompet" />
  <div class="py-12">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center gap-4 mb-8">
        <button @click="router.visit(route('admin.finance.wallets.index'))" class="text-taupe hover:text-ink">
          ← Kembali
        </button>
        <h1 class="text-3xl font-fraunces font-bold text-ink">Dompet Baru</h1>
      </div>

      <div class="bg-paper border border-oat-dark rounded-2xl p-6">
        <form @submit.prevent="submit" class="space-y-6">
          <div>
            <label class="block text-sm font-medium text-ink mb-1">Nama Dompet *</label>
            <input v-model="form.name" type="text" class="w-full px-4 py-2 border border-oat rounded-lg focus:ring-2 focus:ring-terracotta" required />
            <p v-if="form.errors.name" class="text-red-600 text-sm mt-1">{{ form.errors.name }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-ink mb-1">Tipe</label>
            <select v-model="form.type" class="w-full px-4 py-2 border border-oat rounded-lg focus:ring-2 focus:ring-terracotta">
              <option value="bank">Bank</option>
              <option value="ewallet">E-Wallet</option>
              <option value="cash">Cash</option>
              <option value="savings">Tabungan</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-ink mb-1">Saldo Awal</label>
            <input v-model.number="form.balance" type="number" min="0" class="w-full px-4 py-2 border border-oat rounded-lg focus:ring-2 focus:ring-terracotta" />
          </div>

          <div>
            <label class="block text-sm font-medium text-ink mb-1">Warna</label>
            <div class="flex gap-4">
              <input v-model="form.color" type="color" class="w-12 h-12 rounded-lg border border-oat cursor-pointer" />
              <input v-model="form.color" type="text" class="flex-1 px-4 py-2 border border-oat rounded-lg" />
            </div>
          </div>

          <div class="flex items-center gap-3">
            <input v-model="form.is_active" type="checkbox" id="is_active" class="w-5 h-5 rounded border-oat text-terracotta focus:ring-terracotta" />
            <label for="is_active" class="text-ink">Dompet Aktif</label>
          </div>

          <div class="flex gap-4 pt-4">
            <button type="button" @click="router.visit(route('admin.finance.wallets.index'))" class="flex-1 px-6 py-3 border border-oat rounded-xl text-taupe hover:bg-cream">
              Batal
            </button>
            <button type="submit" class="flex-1 px-6 py-3 bg-terracotta text-white rounded-xl hover:bg-terracotta/90 disabled:opacity-50" :disabled="form.processing">
              {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>
