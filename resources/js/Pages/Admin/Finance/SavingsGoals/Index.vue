<script setup>
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ref } from 'vue';

import { FlagIcon } from '@heroicons/vue/24/outline';

defineOptions({ layout: AdminLayout });

const props = defineProps({
  goals: Object,
  wallets: Object,
  filters: Object,
});

const showModal = ref(false);
const showAddFundsModal = ref(false);
const selectedGoal = ref(null);
const addFundsForm = useForm({ amount: '' });

const formatRupiah = (amount) => {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(amount);
};

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
};

const openCreate = () => {
  selectedGoal.value = null;
  router.visit(route('admin.finance.savings-goals.create'));
};

const openAddFunds = (goal) => {
  selectedGoal.value = goal;
  addFundsForm.reset();
  showAddFundsModal.value = true;
};

const submitAddFunds = () => {
  addFundsForm.patch(route('admin.finance.savings-goals.add-funds', selectedGoal.value.id), {
    onSuccess: () => { showAddFundsModal.value = false; selectedGoal.value = null; },
  });
};
</script>

<template>
  <Head title="Target Tabungan" />

  <div class="py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-fraunces font-bold text-ink">Target Tabungan</h1>
        <button @click="openCreate" class="px-4 py-2 bg-terracotta text-white rounded-lg hover:bg-terracotta/90">
          + Target Baru
        </button>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div
          v-for="goal in goals.data"
          :key="goal.id"
          class="bg-paper border border-oat-dark rounded-xl p-4"
        >
          <div class="flex items-center gap-3 mb-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center" :style="{ backgroundColor: goal.color + '20' }">
              <FlagIcon class="w-6 h-6" :style="{ color: goal.color }" />
            </div>
            <div>
              <p class="font-fraunces font-semibold text-ink">{{ goal.name }}</p>
              <span v-if="goal.is_achieved" class="text-xs text-green-600 font-medium">✓ Tercapai</span>
            </div>
          </div>

          <div class="mb-3">
            <div class="flex justify-between text-sm mb-1">
              <span class="text-taupe">{{ formatRupiah(goal.current_amount) }}</span>
              <span class="text-ink font-medium">{{ formatRupiah(goal.target_amount) }}</span>
            </div>
            <div class="w-full bg-cream rounded-full h-3">
              <div
                class="h-3 rounded-full transition-all"
                :class="goal.is_achieved ? 'bg-green-500' : 'bg-terracotta'"
                :style="{ width: goal.progress_percentage + '%' }"
              ></div>
            </div>
          </div>

          <div class="flex justify-between items-center">
            <span class="text-sm text-taupe" v-if="goal.target_date">Target: {{ formatDate(goal.target_date) }}</span>
            <span class="text-sm text-taupe" v-else>&nbsp;</span>
            <button
              v-if="!goal.is_achieved"
              @click="openAddFunds(goal)"
              class="text-sm text-terracotta hover:text-terracotta/80 font-medium"
            >
              + Dana
            </button>
          </div>
        </div>
        <div v-if="goals.data.length === 0" class="col-span-full text-center text-taupe py-12 bg-cream rounded-xl border border-dashed border-oat">
          <p>Belum ada target tabungan</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Add Funds Modal -->
  <div v-if="showAddFundsModal" class="fixed inset-0 bg-ink/50 flex items-center justify-center z-50">
    <div class="bg-paper rounded-2xl p-6 w-full max-w-md">
      <h2 class="text-xl font-fraunces font-semibold text-ink mb-4">Tambah Dana</h2>
      <p class="text-taupe mb-4">{{ selectedGoal?.name }} - {{ formatRupiah(selectedGoal?.target_amount - selectedGoal?.current_amount) }} tersisa</p>
      <form @submit.prevent="submitAddFunds" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-ink mb-1">Jumlah</label>
          <input v-model.number="addFundsForm.amount" type="number" min="1" class="w-full px-4 py-2 border border-oat rounded-lg" required />
        </div>
        <div class="flex gap-3">
          <button type="button" @click="showAddFundsModal = false" class="flex-1 px-4 py-2 border border-oat rounded-lg text-taupe">Batal</button>
          <button type="submit" class="flex-1 px-4 py-2 bg-terracotta text-white rounded-lg">Tambah</button>
        </div>
      </form>
    </div>
  </div>
</template>
