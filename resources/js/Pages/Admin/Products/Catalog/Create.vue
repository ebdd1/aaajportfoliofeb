<script setup>
import { Head, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { computed } from 'vue';

defineOptions({ layout: AdminLayout });

const props = defineProps({
  categories: {
    type: Array,
    default: () => [],
  },
});

const form = useForm({
  name: '',
  category_id: '',
  type: 'service',
  status: 'idea',
  short_description: '',
  description: '',
  price: '',
  demo_url: '',
  repo_url: '',
  landing_url: '',
  tags: [],
  is_public: false,
});

const submit = () => {
  form.post(route('admin.products.catalog.store'), {
    onSuccess: () => router.visit(route('admin.products.catalog.index')),
  });
};
</script>

<template>
  <Head title="Produk Baru" />
  <div class="py-12">
    <div class="max-w-2xl mx-auto px-4">
      <div class="flex items-center gap-4 mb-8">
        <button @click="router.visit(route('admin.products.catalog.index'))" class="text-taupe hover:text-ink">← Kembali</button>
        <h1 class="text-3xl font-fraunces font-bold text-ink">Produk Baru</h1>
      </div>

      <div class="bg-paper border border-oat-dark rounded-2xl p-6">
        <form @submit.prevent="submit" class="space-y-6">
          <div>
            <label class="block text-sm font-medium text-ink mb-1">Nama Produk *</label>
            <input v-model="form.name" type="text" class="w-full px-4 py-2 border border-oat rounded-lg" required />
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
            <label class="block text-sm font-medium text-ink mb-1">Tipe</label>
            <select v-model="form.type" class="w-full px-4 py-2 border border-oat rounded-lg">
              <option value="service">Jasa</option>
              <option value="digital_product">Produk Digital</option>
              <option value="saas">SaaS</option>
              <option value="physical">Fisik</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-ink mb-1">Kategori</label>
            <select v-model="form.category_id" class="w-full px-4 py-2 border border-oat rounded-lg">
              <option value="">-- Pilih Kategori --</option>
              <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-ink mb-1">Status</label>
              <select v-model="form.status" class="w-full px-4 py-2 border border-oat rounded-lg">
                <option value="idea">Ide</option>
                <option value="building">Dibangun</option>
                <option value="active">Aktif</option>
                <option value="paused">Dijeda</option>
                <option value="discontinued">Dihentikan</option>
              </select>
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-ink mb-1">Deskripsi Singkat</label>
            <textarea v-model="form.short_description" class="w-full px-4 py-2 border border-oat rounded-lg" rows="2"></textarea>
          </div>

          <div>
            <label class="block text-sm font-medium text-ink mb-1">Deskripsi Lengkap</label>
            <textarea v-model="form.description" class="w-full px-4 py-2 border border-oat rounded-lg" rows="5"></textarea>
          </div>

          <div>
            <label class="block text-sm font-medium text-ink mb-1">Harga (kosongkan jika gratis/contact)</label>
            <input v-model.number="form.price" type="number" min="0" class="w-full px-4 py-2 border border-oat rounded-lg" />
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label class="block text-sm font-medium text-ink mb-1">Demo URL</label>
              <input v-model="form.demo_url" type="url" class="w-full px-4 py-2 border border-oat rounded-lg" />
            </div>
            <div>
              <label class="block text-sm font-medium text-ink mb-1">Repo URL</label>
              <input v-model="form.repo_url" type="url" class="w-full px-4 py-2 border border-oat rounded-lg" />
            </div>
            <div>
              <label class="block text-sm font-medium text-ink mb-1">Landing URL</label>
              <input v-model="form.landing_url" type="url" class="w-full px-4 py-2 border border-oat rounded-lg" />
            </div>
          </div>

          <div class="flex items-center gap-3">
            <input v-model="form.is_public" type="checkbox" id="is_public" class="w-5 h-5 rounded border-oat" />
            <label for="is_public" class="text-ink">Tampilkan di halaman publik</label>
          </div>

          <div class="flex gap-4 pt-4">
            <button type="button" @click="router.visit(route('admin.products.catalog.index'))" class="flex-1 px-6 py-3 border border-oat rounded-xl text-taupe hover:bg-cream">Batal</button>
            <button type="submit" class="flex-1 px-6 py-3 bg-terracotta text-white rounded-xl hover:bg-terracotta/90" :disabled="form.processing">
              {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>
