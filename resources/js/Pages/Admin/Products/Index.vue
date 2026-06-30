<script setup>
defineOptions({ layout: AdminLayout });
import { ref } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { useConfirm } from '@/Composables/useConfirm';

const props = defineProps({
    products: Object,
    categories: Array,
    filters: Object,
});

const search = ref(props.filters?.search || '');
const selectedCategory = ref(props.filters?.category || '');
const { open: confirmOpen } = useConfirm();

const formatPrice = (price) => {
    return 'Rp ' + new Intl.NumberFormat('id-ID').format(price);
};

const formatDate = (date) => {
    return new Intl.DateTimeFormat('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric'
    }).format(new Date(date));
};

const handleSearch = () => {
    router.get(route('admin.products.index'), {
        search: search.value,
        category: selectedCategory.value,
    }, { preserveState: true });
};

const toggleFeatured = (product) => {
    router.patch(route('admin.products.toggleFeatured', product.id));
};

const toggleActive = (product) => {
    router.patch(route('admin.products.toggleActive', product.id));
};

const deleteProduct = async (product) => {
    const confirmed = await confirmOpen({
        message: `Produk "${product.name}" akan dihapus permanen. Lanjutkan?`,
        variant: 'danger',
        confirmText: 'Hapus',
        cancelText: 'Batal',
    });

    if (confirmed) {
        router.delete(route('admin.products.destroy', product.id));
    }
};
</script>

<template>
    <div>
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="font-serif text-2xl font-bold text-ink">Produk Digital</h1>
                <p class="text-sm text-taupe mt-1">Kelola produk digital yang dijual</p>
            </div>
            <Link
                :href="route('admin.products.create')"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-terracotta text-cream font-medium rounded-xl hover:bg-terracotta-dark transition-colors"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 5v14M5 12h14"/>
                </svg>
                Tambah Produk
            </Link>
        </div>

        <!-- Filters -->
        <div class="mb-6 flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <input
                    v-model="search"
                    type="text"
                    placeholder="Cari produk..."
                    class="w-full px-4 py-2.5 bg-paper border border-oat-dark rounded-xl text-ink placeholder-taupe/50 focus:outline-none focus:ring-2 focus:ring-terracotta/50 focus:border-terracotta"
                    @keyup.enter="handleSearch"
                />
            </div>
            <select
                v-model="selectedCategory"
                class="px-4 py-2.5 bg-paper border border-oat-dark rounded-xl text-ink focus:outline-none focus:ring-2 focus:ring-terracotta/50 focus:border-terracotta"
                @change="handleSearch"
            >
                <option value="">Semua Kategori</option>
                <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                    {{ cat.name }}
                </option>
            </select>
        </div>

        <!-- Products Table -->
        <div class="bg-paper rounded-2xl border border-oat-dark overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-oat/50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-taupe uppercase tracking-wider">Produk</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-taupe uppercase tracking-wider">Kategori</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-taupe uppercase tracking-wider">Harga</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-taupe uppercase tracking-wider">Downloads</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-taupe uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-taupe uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-oat-dark">
                        <tr v-for="product in products.data" :key="product.id" class="hover:bg-oat/30 transition-colors">
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-lg bg-terracotta/10 flex items-center justify-center overflow-hidden">
                                        <img v-if="product.thumbnail" :src="product.thumbnail" class="w-full h-full object-cover" />
                                        <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-terracotta" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                                            <polyline points="14 2 14 8 20 8"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-medium text-ink">{{ product.name }}</p>
                                        <p class="text-xs text-taupe truncate max-w-[200px]">{{ product.short_description || '-' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-oat text-taupe">
                                    {{ product.category?.name || '-' }}
                                </span>
                            </td>
                            <td class="px-4 py-4">
                                <span class="font-semibold text-ink">{{ formatPrice(product.price) }}</span>
                            </td>
                            <td class="px-4 py-4">
                                <span class="text-taupe">{{ product.downloads || 0 }}</span>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex flex-wrap gap-1">
                                    <button
                                        @click="toggleFeatured(product)"
                                        :class="[
                                            'px-2 py-1 rounded-lg text-xs font-medium transition-colors',
                                            product.is_featured
                                                ? 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200'
                                                : 'bg-oat text-taupe hover:bg-oat-dark'
                                        ]"
                                    >
                                        {{ product.is_featured ? 'Featured' : 'Normal' }}
                                    </button>
                                    <button
                                        @click="toggleActive(product)"
                                        :class="[
                                            'px-2 py-1 rounded-lg text-xs font-medium transition-colors',
                                            product.is_active
                                                ? 'bg-green-100 text-green-700 hover:bg-green-200'
                                                : 'bg-red-100 text-red-700 hover:bg-red-200'
                                        ]"
                                    >
                                        {{ product.is_active ? 'Aktif' : 'Nonaktif' }}
                                    </button>
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <Link
                                        :href="route('admin.products.edit', product.id)"
                                        class="p-2 text-taupe hover:text-terracotta hover:bg-oat rounded-lg transition-colors"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                                            <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                        </svg>
                                    </Link>
                                    <button
                                        @click="deleteProduct(product)"
                                        class="p-2 text-taupe hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M3 6h18M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Empty State -->
            <div v-if="products.data.length === 0" class="p-12 text-center">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-oat flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-taupe" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                    </svg>
                </div>
                <h3 class="font-medium text-ink mb-1">Belum ada produk</h3>
                <p class="text-sm text-taupe mb-4">Mulai tambahkan produk digital pertama Anda</p>
                <Link
                    :href="route('admin.products.create')"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-terracotta text-cream font-medium rounded-xl hover:bg-terracotta-dark transition-colors"
                >
                    Tambah Produk
                </Link>
            </div>

            <!-- Pagination -->
            <div v-if="products.data.length > 0" class="px-4 py-3 border-t border-oat-dark">
                <div class="flex items-center justify-between">
                    <p class="text-sm text-taupe">
                        Menampilkan {{ products.from }} - {{ products.to }} dari {{ products.total }} produk
                    </p>
                    <div class="flex gap-1">
                        <Link
                            v-for="link in products.links"
                            :key="link.label"
                            :href="link.url || '#'"
                            :class="[
                                'px-3 py-1.5 text-sm rounded-lg transition-colors',
                                link.active
                                    ? 'bg-terracotta text-cream'
                                    : 'text-taupe hover:bg-oat'
                            ]"
                            :preserve-state="true"
                        >
                            <span v-html="link.label"></span>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
