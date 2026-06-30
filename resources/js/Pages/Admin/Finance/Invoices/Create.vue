<script setup>
import { Head, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ref, computed } from 'vue';

defineOptions({ layout: AdminLayout });

const props = defineProps({
  invoice_number: String,
  wallets: Object,
});

const form = useForm({
  invoice_number: props.invoice_number,
  client_name: '',
  client_email: '',
  client_company: '',
  issue_date: new Date().toISOString().split('T')[0],
  due_date: '',
  discount: 0,
  tax_percentage: 0,
  notes: '',
  wallet_id: '',
  items: [{ description: '', quantity: 1, unit_price: 0 }],
});

const addItem = () => {
  form.items.push({ description: '', quantity: 1, unit_price: 0 });
};

const removeItem = (index) => {
  if (form.items.length > 1) {
    form.items.splice(index, 1);
  }
};

const itemTotal = (item) => {
  return item.quantity * item.unit_price;
};

const subtotal = computed(() => {
  return form.items.reduce((sum, item) => sum + itemTotal(item), 0);
});

const taxAmount = computed(() => {
  return (subtotal.value - form.discount) * (form.tax_percentage / 100);
});

const total = computed(() => {
  return subtotal.value - form.discount + taxAmount.value;
});

const formatRupiah = (amount) => {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(amount);
};

const submit = () => {
  form.post(route('admin.finance.invoices.store'), {
    onSuccess: () => router.visit(route('admin.finance.invoices.index')),
  });
};
</script>

<template>
  <Head title="Invoice Baru" />
  <div class="py-12">
    <div class="max-w-4xl mx-auto px-4">
      <div class="flex items-center gap-4 mb-8">
        <button @click="router.visit(route('admin.finance.invoices.index'))" class="text-taupe hover:text-ink">← Kembali</button>
        <h1 class="text-3xl font-fraunces font-bold text-ink">Invoice Baru</h1>
      </div>

      <div class="bg-paper border border-oat-dark rounded-2xl p-6">
        <form @submit.prevent="submit" class="space-y-8">
          <!-- Client Info -->
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-ink mb-1">Nama Klien *</label>
              <input v-model="form.client_name" type="text" class="w-full px-4 py-2 border border-oat rounded-lg" required />
            </div>
            <div>
              <label class="block text-sm font-medium text-ink mb-1">Email Klien</label>
              <input v-model="form.client_email" type="email" class="w-full px-4 py-2 border border-oat rounded-lg" />
            </div>
            <div>
              <label class="block text-sm font-medium text-ink mb-1">Perusahaan</label>
              <input v-model="form.client_company" type="text" class="w-full px-4 py-2 border border-oat rounded-lg" />
            </div>
            <div>
              <label class="block text-sm font-medium text-ink mb-1">Nomor Invoice</label>
              <input v-model="form.invoice_number" type="text" class="w-full px-4 py-2 border border-oat rounded-lg bg-cream" />
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-ink mb-1">Tanggal Terbit *</label>
              <input v-model="form.issue_date" type="date" class="w-full px-4 py-2 border border-oat rounded-lg" required />
            </div>
            <div>
              <label class="block text-sm font-medium text-ink mb-1">Jatuh Tempo *</label>
              <input v-model="form.due_date" type="date" class="w-full px-4 py-2 border border-oat rounded-lg" required />
            </div>
          </div>

          <!-- Items -->
          <div>
            <h3 class="text-lg font-fraunces font-semibold text-ink mb-4">Item</h3>
            <div class="space-y-3">
              <div v-for="(item, index) in form.items" :key="index" class="flex gap-3 items-start">
                <div class="flex-1">
                  <input v-model="item.description" type="text" placeholder="Deskripsi" class="w-full px-4 py-2 border border-oat rounded-lg" required />
                </div>
                <div class="w-24">
                  <input v-model.number="item.quantity" type="number" min="1" placeholder="Qty" class="w-full px-4 py-2 border border-oat rounded-lg" required />
                </div>
                <div class="w-32">
                  <input v-model.number="item.unit_price" type="number" min="0" placeholder="Harga" class="w-full px-4 py-2 border border-oat rounded-lg" required />
                </div>
                <div class="w-32 text-right pt-2 font-medium text-ink">
                  {{ formatRupiah(itemTotal(item)) }}
                </div>
                <button type="button" @click="removeItem(index)" class="text-red-600 hover:text-red-700 p-2">×</button>
              </div>
            </div>
            <button type="button" @click="addItem" class="mt-3 text-terracotta hover:text-terracotta/80 text-sm">+ Tambah Item</button>
          </div>

          <!-- Totals -->
          <div class="border-t border-oat pt-4">
            <div class="flex justify-end">
              <div class="w-64 space-y-2">
                <div class="flex justify-between">
                  <span class="text-taupe">Subtotal</span>
                  <span class="text-ink font-medium">{{ formatRupiah(subtotal) }}</span>
                </div>
                <div class="flex justify-between items-center">
                  <span class="text-taupe">Diskon</span>
                  <input v-model.number="form.discount" type="number" min="0" class="w-32 px-2 py-1 border border-oat rounded-lg text-right" />
                </div>
                <div class="flex justify-between items-center">
                  <span class="text-taupe">Pajak (%)</span>
                  <input v-model.number="form.tax_percentage" type="number" min="0" max="100" class="w-32 px-2 py-1 border border-oat rounded-lg text-right" />
                </div>
                <div class="flex justify-between pt-2 border-t border-oat">
                  <span class="text-lg font-fraunces font-semibold text-ink">Total</span>
                  <span class="text-lg font-fraunces font-bold text-terracotta">{{ formatRupiah(total) }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Notes -->
          <div>
            <label class="block text-sm font-medium text-ink mb-1">Catatan</label>
            <textarea v-model="form.notes" class="w-full px-4 py-2 border border-oat rounded-lg" rows="3"></textarea>
          </div>

          <div class="flex gap-4">
            <button type="button" @click="router.visit(route('admin.finance.invoices.index'))" class="flex-1 px-6 py-3 border border-oat rounded-xl text-taupe hover:bg-cream">Batal</button>
            <button type="submit" class="flex-1 px-6 py-3 bg-terracotta text-white rounded-xl hover:bg-terracotta/90" :disabled="form.processing">
              {{ form.processing ? 'Menyimpan...' : 'Simpan Invoice' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>
