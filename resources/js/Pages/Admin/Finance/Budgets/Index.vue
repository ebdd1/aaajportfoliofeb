<script setup>
import { Head, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ref } from 'vue';
import { useConfirm } from '@/Composables/useConfirm';

import { ChartBarIcon } from '@heroicons/vue/24/outline';

defineOptions({ layout: AdminLayout });

const props = defineProps({
  budgets: Object,
  expenseCategories: Object,
  currentMonth: String,
});

const showModal = ref(false);
const { open: confirmOpen } = useConfirm();

const form = useForm({
  category_id: '',
  month: props.currentMonth,
  amount: '',
});

const formatRupiah = (amount) => {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(amount);
};

const openCreate = () => {
  form.reset();
  form.month = props.currentMonth;
  showModal.value = true;
};

const submit = () => {
  form.post(route('admin.finance.budgets.store'), {
    onSuccess: () => { showModal.value = false; form.reset(); },
  });
};

const deleteBudget = async (budget) => {
  const confirmed = await confirmOpen({
    message: `Budget untuk "${budget.category?.name}" akan dihapus. Lanjutkan?`,
    variant: 'danger',
    confirmText: 'Hapus',
    cancelText: 'Batal',
  });

  if (confirmed) {
    router.delete(route('admin.finance.budgets.destroy', budget.id));
  }
};

const calcPercentage = (spent, amount) => {
    if (!amount || parseFloat(amount) === 0) return 0;
    return Math.min(100, (parseFloat(spent) / parseFloat(amount)) * 100);
};

const getProgressColor = (percentage) => {
  if (percentage < 75) return 'bg-green-500';
  if (percentage < 90) return 'bg-yellow-500';
  return 'bg-red-500';
};
</script>

<template>
  <Head title="Budget" />

  <div class="py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-fraunces font-bold text-ink">Budget Bulanan</h1>
        <button @click="openCreate" class="px-4 py-2 bg-terracotta text-white rounded-lg hover:bg-terracotta/90">
          + Budget Baru
        </button>
      </div>

      <div class="bg-paper border border-oat-dark rounded-2xl p-6 mb-6">
        <p class="text-taupe">Menampilkan budget untuk bulan <span class="font-medium text-ink">{{ currentMonth }}</span></p>
      </div>

      <div class="space-y-4">
        <div v-for="budget in budgets" :key="budget.id" class="bg-paper border border-oat-dark rounded-xl p-4">
          <div class="flex items-center justify-between mb-2">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-lg flex items-center justify-center" :style="{ backgroundColor: budget.category?.color + '20' }">
                <ChartBarIcon class="w-5 h-5" :style="{ color: budget.category?.color }" />
              </div>
              <div>
                <p class="font-medium text-ink">{{ budget.category?.name }}</p>
                <p class="text-sm text-taupe">{{ formatRupiah(budget.spent) }} / {{ formatRupiah(budget.amount) }}</p>
              </div>
            </div>
            <div class="flex items-center gap-4">
              <span class="text-sm font-medium" :class="{ 'text-red-600': calcPercentage(budget.spent, budget.amount) >= 100 }">
                {{ Math.round(calcPercentage(budget.spent, budget.amount)) }}%
              </span>
              <button @click="deleteBudget(budget)" class="text-red-600 hover:text-red-700 text-sm">Hapus</button>
            </div>
          </div>
          <div class="w-full bg-cream rounded-full h-3">
            <div
              class="h-3 rounded-full transition-all"
              :class="getProgressColor(calcPercentage(budget.spent, budget.amount))"
              :style="{ width: Math.min(calcPercentage(budget.spent, budget.amount), 100) + '%' }"
            ></div>
          </div>
        </div>
        <div v-if="budgets.length === 0" class="text-center text-taupe py-12 bg-paper rounded-xl border border-oat">
          <p>Belum ada budget untuk bulan ini</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal -->
  <div v-if="showModal" class="fixed inset-0 bg-ink/50 flex items-center justify-center z-50" @click.self="showModal = false">
    <div class="bg-paper rounded-2xl p-6 w-full max-w-md">
      <h2 class="text-xl font-fraunces font-semibold text-ink mb-4">Budget Baru</h2>
      <form @submit.prevent="submit" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-ink mb-1">Kategori</label>
          <select v-model="form.category_id" class="w-full px-4 py-2 border border-oat rounded-lg" required>
            <option value="">Pilih Kategori</option>
            <option v-for="cat in expenseCategories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-ink mb-1">Jumlah Budget</label>
          <input v-model.number="form.amount" type="number" min="1000" class="w-full px-4 py-2 border border-oat rounded-lg" required />
        </div>
        <div class="flex gap-3 pt-2">
          <button type="button" @click="showModal = false" class="flex-1 px-4 py-2 border border-oat rounded-lg text-taupe">Batal</button>
          <button type="submit" class="flex-1 px-4 py-2 bg-terracotta text-white rounded-lg">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</template>
