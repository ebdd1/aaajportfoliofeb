<script setup>
import { Head, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineOptions({ layout: AdminLayout });

const props = defineProps({
  wallet: Object,
});

const form = useForm({
  name: props.wallet.name,
  type: props.wallet.type,
  balance: props.wallet.balance,
  color: props.wallet.color,
  icon: props.wallet.icon,
  is_active: props.wallet.is_active,
});

const submit = () => {
  form.put(route('admin.finance.wallets.update', props.wallet.id), {
    onSuccess: () => router.visit(route('admin.finance.wallets.index')),
  });
};
</script>

<template>
  <Head title="Edit Dompet" />
  <div class="py-12">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center gap-4 mb-8">
        <button @click="router.visit(route('admin.finance.wallets.index'))" class="text-taupe hover:text-ink">
          ← Kembali
        </button>
        <h1 class="text-3xl font-fraunces font-bold text-ink">Edit Dompet</h1>
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
            <label class="block text-sm font-medium text-ink mb-1">Saldo</label>
            <input v-model.number="form.balance" type="number" min="0" class="w-full px-4 py-2 border border-oat rounded-lg focus:ring-2 focus:ring-terracotta" />
            <p class="text-xs text-taupe mt-1">Mengubah saldo secara manual tidak akan membuat transaksi.</p>
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
