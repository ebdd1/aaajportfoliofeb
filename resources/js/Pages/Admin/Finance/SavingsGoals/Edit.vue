<script setup>
import { Head, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineOptions({ layout: AdminLayout });

const props = defineProps({
  goal: Object,
  wallets: Object,
});

const form = useForm({
  name: props.goal.name,
  target_amount: props.goal.target_amount,
  current_amount: props.goal.current_amount,
  target_date: props.goal.target_date || '',
  wallet_id: props.goal.wallet_id || '',
  color: props.goal.color,
  icon: props.goal.icon,
  is_achieved: props.goal.is_achieved,
});

const submit = () => {
  form.put(route('admin.finance.savings-goals.update', props.goal.id), {
    onSuccess: () => router.visit(route('admin.finance.savings-goals.index')),
  });
};
</script>

<template>
  <Head title="Edit Target Tabungan" />
  <div class="py-12">
    <div class="max-w-2xl mx-auto px-4">
      <div class="flex items-center gap-4 mb-8">
        <button @click="router.visit(route('admin.finance.savings-goals.index'))" class="text-taupe hover:text-ink">← Kembali</button>
        <h1 class="text-3xl font-fraunces font-bold text-ink">Edit Target Tabungan</h1>
      </div>

      <div class="bg-paper border border-oat-dark rounded-2xl p-6">
        <form @submit.prevent="submit" class="space-y-6">
          <div>
            <label class="block text-sm font-medium text-ink mb-1">Nama Target *</label>
            <input v-model="form.name" type="text" class="w-full px-4 py-2 border border-oat rounded-lg" required />
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-ink mb-1">Target Jumlah *</label>
              <input v-model.number="form.target_amount" type="number" min="1" class="w-full px-4 py-2 border border-oat rounded-lg" required />
            </div>
            <div>
              <label class="block text-sm font-medium text-ink mb-1">Dana Terkumpul</label>
              <input v-model.number="form.current_amount" type="number" min="0" class="w-full px-4 py-2 border border-oat rounded-lg" />
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-ink mb-1">Target Tanggal</label>
            <input v-model="form.target_date" type="date" class="w-full px-4 py-2 border border-oat rounded-lg" />
          </div>
          <div>
            <label class="block text-sm font-medium text-ink mb-1">Warna</label>
            <input v-model="form.color" type="color" class="w-full h-12 rounded-lg border border-oat" />
          </div>
          <div class="flex items-center gap-3">
            <input v-model="form.is_achieved" type="checkbox" id="is_achieved" class="w-5 h-5 rounded border-oat" />
            <label for="is_achieved" class="text-ink">Sudah Tercapai</label>
          </div>
          <div class="flex gap-4 pt-4">
            <button type="button" @click="router.visit(route('admin.finance.savings-goals.index'))" class="flex-1 px-6 py-3 border border-oat rounded-xl text-taupe hover:bg-cream">Batal</button>
            <button type="submit" class="flex-1 px-6 py-3 bg-terracotta text-white rounded-xl hover:bg-terracotta/90" :disabled="form.processing">
              {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>
