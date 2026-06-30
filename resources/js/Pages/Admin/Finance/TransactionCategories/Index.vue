<script setup>
import { Head, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ref } from 'vue';
import { useConfirm } from '@/Composables/useConfirm';

defineOptions({ layout: AdminLayout });

const props = defineProps({
  incomeCategories: Object,
  expenseCategories: Object,
});

const showModal = ref(false);
const editingCategory = ref(null);
const { open: confirmOpen } = useConfirm();

const form = useForm({
  name: '',
  type: 'expense',
  icon: 'TagIcon',
  color: '#6b7280',
});

const openCreate = (type = 'expense') => {
  editingCategory.value = null;
  form.reset();
  form.type = type;
  showModal.value = true;
};

const submit = () => {
  if (editingCategory.value) {
    form.put(route('admin.finance.transaction-categories.update', editingCategory.value.id), {
      onSuccess: () => { showModal.value = false; form.reset(); },
    });
  } else {
    form.post(route('admin.finance.transaction-categories.store'), {
      onSuccess: () => { showModal.value = false; form.reset(); },
    });
  }
};

const deleteCategory = async (category) => {
  const confirmed = await confirmOpen({
    message: `Kategori "${category.name}" akan dihapus permanen. Lanjutkan?`,
    variant: 'danger',
    confirmText: 'Hapus',
    cancelText: 'Batal',
  });

  if (confirmed) {
    router.delete(route('admin.finance.transaction-categories.destroy', category.id));
  }
};
</script>

<template>
  <Head title="Kategori Transaksi" />

  <div class="py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
      <h1 class="text-3xl font-fraunces font-bold text-ink mb-8">Kategori Transaksi</h1>

      <!-- Income Categories -->
      <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-xl font-fraunces font-semibold text-ink">Pemasukan</h2>
          <button @click="openCreate('income')" class="text-sm text-terracotta hover:text-terracotta/80">+ Tambah</button>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
          <div
            v-for="cat in incomeCategories"
            :key="cat.id"
            class="bg-paper border border-oat-dark rounded-xl p-4 flex items-center gap-3"
          >
            <div class="w-8 h-8 rounded-lg flex items-center justify-center" :style="{ backgroundColor: cat.color + '20' }">
              <span class="text-sm">📁</span>
            </div>
            <div class="flex-1">
              <p class="font-medium text-ink text-sm">{{ cat.name }}</p>
            </div>
            <button @click="deleteCategory(cat)" class="text-red-500 hover:text-red-700 text-xs">×</button>
          </div>
        </div>
      </div>

      <!-- Expense Categories -->
      <div>
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-xl font-fraunces font-semibold text-ink">Pengeluaran</h2>
          <button @click="openCreate('expense')" class="text-sm text-terracotta hover:text-terracotta/80">+ Tambah</button>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
          <div
            v-for="cat in expenseCategories"
            :key="cat.id"
            class="bg-paper border border-oat-dark rounded-xl p-4 flex items-center gap-3"
          >
            <div class="w-8 h-8 rounded-lg flex items-center justify-center" :style="{ backgroundColor: cat.color + '20' }">
              <span class="text-sm">📁</span>
            </div>
            <div class="flex-1">
              <p class="font-medium text-ink text-sm">{{ cat.name }}</p>
            </div>
            <button @click="deleteCategory(cat)" class="text-red-500 hover:text-red-700 text-xs">×</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal -->
  <div v-if="showModal" class="fixed inset-0 bg-ink/50 flex items-center justify-center z-50" @click.self="showModal = false">
    <div class="bg-paper rounded-2xl p-6 w-full max-w-md">
      <h2 class="text-xl font-fraunces font-semibold text-ink mb-4">Kategori Baru</h2>
      <form @submit.prevent="submit" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-ink mb-1">Nama</label>
          <input v-model="form.name" type="text" class="w-full px-4 py-2 border border-oat rounded-lg" required />
        </div>
        <div>
          <label class="block text-sm font-medium text-ink mb-1">Tipe</label>
          <select v-model="form.type" class="w-full px-4 py-2 border border-oat rounded-lg">
            <option value="income">Pemasukan</option>
            <option value="expense">Pengeluaran</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-ink mb-1">Warna</label>
          <input v-model="form.color" type="color" class="w-full h-10 rounded-lg border border-oat" />
        </div>
        <div class="flex gap-3 pt-2">
          <button type="button" @click="showModal = false" class="flex-1 px-4 py-2 border border-oat rounded-lg text-taupe">Batal</button>
          <button type="submit" class="flex-1 px-4 py-2 bg-terracotta text-white rounded-lg">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</template>
