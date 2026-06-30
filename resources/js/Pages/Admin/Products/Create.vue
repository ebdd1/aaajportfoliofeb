<script setup>
defineOptions({ layout: AdminLayout });
import { ref } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, router } from '@inertiajs/vue3';

const props = defineProps({
    categories: Array,
});

const form = ref({
    category_id: '',
    name: '',
    slug: '',
    short_description: '',
    description: '',
    price: '',
    thumbnail: '',
    version: '',
    is_featured: false,
    is_active: true,
});

const errors = ref({});
const isSubmitting = ref(false);

const submit = () => {
    isSubmitting.value = true;
    errors.value = {};

    router.post(route('admin.products.store'), form.value, {
        onError: (err) => {
            errors.value = err;
            isSubmitting.value = false;
        },
        onSuccess: () => {
            isSubmitting.value = false;
        },
    });
};

const formatPriceInput = () => {
    const num = form.value.price.replace(/\D/g, '');
    form.value.price = num ? parseInt(num) : '';
};
</script>

<template>
    <div>
        <div class="mb-6">
            <Link
                :href="route('admin.products.index')"
                class="inline-flex items-center gap-2 text-sm text-taupe hover:text-terracotta transition-colors mb-2"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
                Kembali ke Daftar Produk
            </Link>
            <h1 class="font-serif text-2xl font-bold text-ink">Tambah Produk Baru</h1>
            <p class="text-sm text-taupe mt-1">Tambahkan produk digital baru untuk dijual</p>
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <div class="bg-paper rounded-2xl border border-oat-dark p-6">
                <h2 class="font-semibold text-ink mb-4">Informasi Dasar</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-ink mb-1.5">Nama Produk *</label>
                        <input
                            v-model="form.name"
                            type="text"
                            class="w-full px-4 py-2.5 bg-cream border border-oat-dark rounded-xl text-ink focus:outline-none focus:ring-2 focus:ring-terracotta/50 focus:border-terracotta"
                            placeholder="Contoh: Script Bot WhatsApp"
                        />
                        <p v-if="errors.name" class="text-red-500 text-sm mt-1">{{ errors.name }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-ink mb-1.5">Slug</label>
                        <input
                            v-model="form.slug"
                            type="text"
                            class="w-full px-4 py-2.5 bg-cream border border-oat-dark rounded-xl text-ink focus:outline-none focus:ring-2 focus:ring-terracotta/50 focus:border-terracotta"
                            placeholder="auto-generate dari nama"
                        />
                        <p class="text-xs text-taupe mt-1">Biarkan kosong untuk auto-generate</p>
                        <p v-if="errors.slug" class="text-red-500 text-sm mt-1">{{ errors.slug }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-ink mb-1.5">Kategori *</label>
                        <select
                            v-model="form.category_id"
                            class="w-full px-4 py-2.5 bg-cream border border-oat-dark rounded-xl text-ink focus:outline-none focus:ring-2 focus:ring-terracotta/50 focus:border-terracotta"
                        >
                            <option value="">Pilih Kategori</option>
                            <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                                {{ cat.name }}
                            </option>
                        </select>
                        <p v-if="errors.category_id" class="text-red-500 text-sm mt-1">{{ errors.category_id }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-ink mb-1.5">Harga (Rp) *</label>
                        <input
                            v-model="form.price"
                            type="text"
                            @input="formatPriceInput"
                            class="w-full px-4 py-2.5 bg-cream border border-oat-dark rounded-xl text-ink focus:outline-none focus:ring-2 focus:ring-terracotta/50 focus:border-terracotta"
                            placeholder="50000"
                        />
                        <p class="text-xs text-taupe mt-1">Masukkan angka tanpa titik, contoh: 50000</p>
                        <p v-if="errors.price" class="text-red-500 text-sm mt-1">{{ errors.price }}</p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-ink mb-1.5">Deskripsi Singkat</label>
                        <input
                            v-model="form.short_description"
                            type="text"
                            maxlength="255"
                            class="w-full px-4 py-2.5 bg-cream border border-oat-dark rounded-xl text-ink focus:outline-none focus:ring-2 focus:ring-terracotta/50 focus:border-terracotta"
                            placeholder="Deskripsi singkat produk (maks 255 karakter)"
                        />
                        <p class="text-xs text-taupe mt-1">{{ form.short_description?.length || 0 }}/255 karakter</p>
                        <p v-if="errors.short_description" class="text-red-500 text-sm mt-1">{{ errors.short_description }}</p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-ink mb-1.5">Deskripsi Lengkap</label>
                        <textarea
                            v-model="form.description"
                            rows="5"
                            class="w-full px-4 py-2.5 bg-cream border border-oat-dark rounded-xl text-ink focus:outline-none focus:ring-2 focus:ring-terracotta/50 focus:border-terracotta resize-none"
                            placeholder="Jelaskan detail produk, fitur, requirements, dll"
                        ></textarea>
                        <p v-if="errors.description" class="text-red-500 text-sm mt-1">{{ errors.description }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-paper rounded-2xl border border-oat-dark p-6">
                <h2 class="font-semibold text-ink mb-4">File & Media</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-ink mb-1.5">URL Thumbnail</label>
                        <input
                            v-model="form.thumbnail"
                            type="url"
                            class="w-full px-4 py-2.5 bg-cream border border-oat-dark rounded-xl text-ink focus:outline-none focus:ring-2 focus:ring-terracotta/50 focus:border-terracotta"
                            placeholder="https://example.com/thumbnail.jpg"
                        />
                        <p class="text-xs text-taupe mt-1">Link gambar thumbnail produk</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-ink mb-1.5">Versi</label>
                        <input
                            v-model="form.version"
                            type="text"
                            class="w-full px-4 py-2.5 bg-cream border border-oat-dark rounded-xl text-ink focus:outline-none focus:ring-2 focus:ring-terracotta/50 focus:border-terracotta"
                            placeholder="1.0.0"
                        />
                        <p class="text-xs text-taupe mt-1">Versi software/script</p>
                    </div>
                </div>
            </div>

            <div class="bg-paper rounded-2xl border border-oat-dark p-6">
                <h2 class="font-semibold text-ink mb-4">Status</h2>
                
                <div class="flex flex-wrap gap-6">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input
                            v-model="form.is_featured"
                            type="checkbox"
                            class="w-5 h-5 rounded border-oat-dark text-terracotta focus:ring-terracotta/50"
                        />
                        <span class="text-sm text-ink">Tampilkan di Featured</span>
                    </label>

                    <label class="flex items-center gap-3 cursor-pointer">
                        <input
                            v-model="form.is_active"
                            type="checkbox"
                            class="w-5 h-5 rounded border-oat-dark text-terracotta focus:ring-terracotta/50"
                        />
                        <span class="text-sm text-ink">Produk Aktif</span>
                    </label>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <Link
                    :href="route('admin.products.index')"
                    class="px-6 py-2.5 text-taupe hover:text-ink border border-oat-dark rounded-xl transition-colors"
                >
                    Batal
                </Link>
                <button
                    type="submit"
                    :disabled="isSubmitting"
                    class="px-6 py-2.5 bg-terracotta text-cream font-medium rounded-xl hover:bg-terracotta-dark disabled:opacity-50 transition-colors flex items-center gap-2"
                >
                    <svg v-if="isSubmitting" class="w-5 h-5 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                    </svg>
                    {{ isSubmitting ? 'Menyimpan...' : 'Simpan Produk' }}
                </button>
            </div>
        </form>
    </div>
</template>
