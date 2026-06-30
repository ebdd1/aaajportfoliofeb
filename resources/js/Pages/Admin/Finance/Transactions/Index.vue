<script setup>
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ref, computed } from 'vue';
import { useConfirm } from '@/Composables/useConfirm';

defineOptions({ layout: AdminLayout });

const props = defineProps({
  transactions: Object,
  wallets: Object,
  categories: Object,
  filters: Object,
  totals: Object,
});

const { open: confirmOpen } = useConfirm();
const showModal = ref(false);
const editingTransaction = ref(null);

const form = useForm({
  wallet_id: '',
  type: 'expense',
  category_id: '',
  amount: '',
  description: '',
  date: new Date().toISOString().split('T')[0],
  notes: '',
});

const formatRupiah = (amount) => {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(amount);
};

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
};

const openCreate = () => {
  editingTransaction.value = null;
  form.reset();
  showModal.value = true;
};

const submit = () => {
  if (editingTransaction.value) {
    form.put(route('admin.finance.transactions.update', editingTransaction.value.id), {
      onSuccess: () => { showModal.value = false; form.reset(); },
    });
  } else {
    form.post(route('admin.finance.transactions.store'), {
      onSuccess: () => { showModal.value = false; form.reset(); },
    });
  }
};

const deleteTransaction = (transaction) => {
  confirmOpen({
    title: 'Hapus Transaksi',
    message: `Yakin ingin menghapus transaksi "${transaction.description}"?`,
    onConfirm: () => {
      const deleteForm = useForm();
      deleteForm.delete(route('admin.finance.transactions.destroy', transaction.id));
    },
  });
};

const filteredCategories = computed(() => {
  return props.categories.filter(c => c.type === form.type || c.type === 'both');
});
</script>

<template>
  <Head title="Transaksi" />

  <div class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-fraunces font-bold text-ink">Transaksi</h1>
        <div class="flex gap-3">
          <a
            :href="route('admin.finance.transactions.export', filters)"
            class="px-4 py-2 border border-oat rounded-lg text-taupe hover:bg-cream transition-colors"
          >
            Export CSV
          </a>
          <button @click="openCreate" class="px-4 py-2 bg-terracotta text-white rounded-lg hover:bg-terracotta/90">
            + Transaksi Baru
          </button>
        </div>
      </div>

      <div class="bg-paper border border-oat-dark rounded-2xl p-4 mb-6">
        <form @submit.prevent="router.get(route('admin.finance.transactions.index'), { ...filters })" class="flex flex-wrap gap-4">
          <select v-model="filters.wallet_id" class="px-3 py-2 border border-oat rounded-lg">
            <option value="">Semua Dompet</option>
            <option v-for="wallet in wallets" :key="wallet.id" :value="wallet.id">{{ wallet.name }}</option>
          </select>
          <select v-model="filters.type" class="px-3 py-2 border border-oat rounded-lg">
            <option value="">Semua Tipe</option>
            <option value="income">Pemasukan</option>
            <option value="expense">Pengeluaran</option>
          </select>
          <input v-model="filters.date_from" type="date" class="px-3 py-2 border border-oat rounded-lg" />
          <input v-model="filters.date_to" type="date" class="px-3 py-2 border border-oat rounded-lg" />
          <input v-model="filters.search" type="text" class="flex-1 px-3 py-2 border border-oat rounded-lg" placeholder="Cari..." />
          <button type="submit" class="px-4 py-2 bg-terracotta text-white rounded-lg">Filter</button>
        </form>
      </div>

      <div class="grid grid-cols-2 gap-4 mb-6">
        <div class="bg-green-50 border border-green-200 rounded-xl p-4">
          <p class="text-sm text-green-700">Total Pemasukan</p>
          <p class="text-xl font-bold text-green-600">{{ formatRupiah(totals.income) }}</p>
        </div>
        <div class="bg-red-50 border border-red-200 rounded-xl p-4">
          <p class="text-sm text-red-700">Total Pengeluaran</p>
          <p class="text-xl font-bold text-red-600">{{ formatRupiah(totals.expense) }}</p>
        </div>
      </div>

      <div class="bg-paper border border-oat-dark rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-cream">
              <tr>
                <th class="text-left py-3 px-4 text-sm font-medium text-taupe">Tanggal</th>
                <th class="text-left py-3 px-4 text-sm font-medium text-taupe">Kategori</th>
                <th class="text-left py-3 px-4 text-sm font-medium text-taupe">Deskripsi</th>
                <th class="text-left py-3 px-4 text-sm font-medium text-taupe">Dompet</th>
                <th class="text-right py-3 px-4 text-sm font-medium text-taupe">Jumlah</th>
                <th class="text-center py-3 px-4 text-sm font-medium text-taupe">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="transaction in transactions.data" :key="transaction.id" class="border-t border-oat hover:bg-cream/50">
                <td class="py-3 px-4 text-ink">{{ formatDate(transaction.date) }}</td>
                <td class="py-3 px-4 text-ink">{{ transaction.category?.name }}</td>
                <td class="py-3 px-4 text-ink">{{ transaction.description }}</td>
                <td class="py-3 px-4 text-ink">{{ transaction.wallet?.name }}</td>
                <td class="py-3 px-4 text-right font-bold" :class="transaction.type === 'income' ? 'text-green-600' : 'text-red-600'">
                  {{ transaction.type === 'income' ? '+' : '-' }}{{ formatRupiah(transaction.amount) }}
                </td>
                <td class="py-3 px-4 text-center">
                  <button @click="deleteTransaction(transaction)" class="text-red-600 hover:text-red-700 text-sm">Hapus</button>
                </td>
              </tr>
              <tr v-if="transactions.data.length === 0">
                <td colspan="6" class="py-12 text-center text-taupe">Belum ada transaksi</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="mt-4 flex justify-center" v-if="transactions.last_page > 1">
        <div class="flex gap-2">
          <Link
            v-for="link in transactions.links"
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
      <h2 class="text-xl font-fraunces font-semibold text-ink mb-4">Transaksi Baru</h2>
      <form @submit.prevent="submit" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-ink mb-1">Tipe</label>
          <div class="flex gap-4">
            <label class="flex items-center gap-2">
              <input v-model="form.type" type="radio" value="income" class="text-green-600" />
              <span class="text-green-600">Pemasukan</span>
            </label>
            <label class="flex items-center gap-2">
              <input v-model="form.type" type="radio" value="expense" class="text-red-600" />
              <span class="text-red-600">Pengeluaran</span>
            </label>
          </div>
        </div>
        <div>
          <label class="block text-sm font-medium text-ink mb-1">Dompet</label>
          <select v-model="form.wallet_id" class="w-full px-4 py-2 border border-oat rounded-lg" required>
            <option value="">Pilih Dompet</option>
            <option v-for="wallet in wallets" :key="wallet.id" :value="wallet.id">{{ wallet.name }}</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-ink mb-1">Kategori</label>
          <select v-model="form.category_id" class="w-full px-4 py-2 border border-oat rounded-lg" required>
            <option value="">Pilih Kategori</option>
            <option v-for="cat in filteredCategories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-ink mb-1">Jumlah</label>
          <input v-model.number="form.amount" type="number" step="1000" min="1" class="w-full px-4 py-2 border border-oat rounded-lg" required />
        </div>
        <div>
          <label class="block text-sm font-medium text-ink mb-1">Deskripsi</label>
          <input v-model="form.description" type="text" class="w-full px-4 py-2 border border-oat rounded-lg" required />
        </div>
        <div>
          <label class="block text-sm font-medium text-ink mb-1">Tanggal</label>
          <input v-model="form.date" type="date" class="w-full px-4 py-2 border border-oat rounded-lg" required />
        </div>
        <div>
          <label class="block text-sm font-medium text-ink mb-1">Catatan</label>
          <textarea v-model="form.notes" class="w-full px-4 py-2 border border-oat rounded-lg" rows="2"></textarea>
        </div>
        <div class="flex gap-3 pt-2">
          <button type="button" @click="showModal = false" class="flex-1 px-4 py-2 border border-oat rounded-lg text-taupe">Batal</button>
          <button type="submit" class="flex-1 px-4 py-2 bg-terracotta text-white rounded-lg" :disabled="form.processing">
            {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
