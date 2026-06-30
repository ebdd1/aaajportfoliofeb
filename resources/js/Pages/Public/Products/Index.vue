<script setup>
import { computed, toValue } from 'vue';
import Navigation from '@/Components/Navigation.vue';
import Footer from '@/Components/Footer.vue';
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    products: Object,
    categories: Array,
    featuredProducts: Array,
    filters: Object,
});

const search = ref(toValue(() => props.filters?.search || ''));
const selectedCategory = ref(toValue(() => props.filters?.category || ''));

// SEO meta
const metaDescription = computed(() => {
    const cat = selectedCategory.value ? props.categories?.find(c => c.id == selectedCategory.value)?.name : 'Produk Digital';
    return `Jual ${cat} berkualitas tinggi. Script, template, dan tools untuk developer Indonesia. Harga terjangkau support garansi.`;
});

const metaTitle = computed(() => {
    const cat = selectedCategory.value ? props.categories?.find(c => c.id == selectedCategory.value)?.name + ' - ' : '';
    return `${cat}Jual Produk Digital Premium - Febryanus`;
});

const ogImage = computed(() => props.featuredProducts?.[0]?.thumbnail || '/og-image.png');

const formatPrice = (price) => {
    return 'Rp ' + new Intl.NumberFormat('id-ID').format(price);
};

const handleSearch = () => {
    const params = {};
    if (search.value) params.search = search.value;
    if (selectedCategory.value) params.category = selectedCategory.value;
    window.location.href = route('products.index', params);
};
</script>

<template>
    <Head>
        <title>{{ metaTitle }}</title>
        <meta property="og:title" :content="metaTitle" />
        <meta property="og:description" :content="metaDescription" />
        <meta property="og:image" :content="ogImage" />
        <meta property="og:url" content="https://greyhound-quicken-schedule.ngrok-free.dev/products" />
        <meta property="og:type" content="website" />
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" :content="metaTitle" />
        <meta name="twitter:description" :content="metaDescription" />
        <meta name="twitter:image" :content="ogImage" />
    </Head>
    <div class="min-h-screen flex flex-col bg-cream">
        <Navigation />
        
        <main class="flex-1">
            <!-- Hero Section -->
            <section class="relative py-20 bg-gradient-to-br from-terracotta/10 via-cream to-cream overflow-hidden">
                <div class="absolute inset-0 overflow-hidden pointer-events-none">
                    <div class="absolute -top-40 -right-40 w-[600px] h-[600px] bg-terracotta/5 rounded-full blur-3xl"></div>
                    <div class="absolute -bottom-40 -left-40 w-[600px] h-[600px] bg-terracotta/5 rounded-full blur-3xl"></div>
                </div>
                <div class="container mx-auto px-4 relative z-10">
                    <div class="max-w-3xl mx-auto text-center">
                        <h1 class="font-serif text-4xl md:text-5xl font-bold text-ink mb-4">Produk Digital</h1>
                        <p class="text-lg text-taupe mb-8">Script, template, dan tools berkualitas untuk kebutuhan development Anda</p>
                        <div class="flex flex-col sm:flex-row gap-3 max-w-xl mx-auto">
                            <input v-model="search" type="text" placeholder="Cari produk..."
                                class="flex-1 px-5 py-3 bg-paper border border-oat-dark rounded-xl text-ink placeholder-taupe/50 focus:outline-none focus:ring-2 focus:ring-terracotta/50"
                                @keyup.enter="handleSearch" />
                            <button @click="handleSearch"
                                class="px-6 py-3 bg-terracotta text-cream font-medium rounded-xl hover:bg-terracotta-dark transition-colors">
                                Cari
                            </button>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Featured Products -->
            <section v-if="featuredProducts?.length > 0" class="py-12 bg-cream">
                <div class="container mx-auto px-4">
                    <h2 class="font-serif text-2xl font-bold text-ink mb-6 flex items-center gap-2">
                        <span class="text-yellow-500 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 fill-current" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                        </span> Produk Unggulan
                    </h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div v-for="product in featuredProducts" :key="product.id"
                            class="bg-paper rounded-2xl border border-oat-dark overflow-hidden hover:shadow-lg hover:border-terracotta/30 transition-all cursor-pointer">
                            <Link :href="route('products.show', product.slug)">
                                <div class="aspect-[4/3] bg-terracotta/10 relative">
                                    <img loading="lazy" v-if="product.thumbnail" :src="product.thumbnail" :alt="product.name" class="w-full h-full object-cover" />
                                    <div v-else class="w-full h-full flex items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-terracotta/30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16.5 9.4l-9-5.19M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
                                        </div>
                                </div>
                            </Link>
                            <div class="p-4">
                                <p class="text-xs text-terracotta font-medium">{{ product.category?.name }}</p>
                                <h3 class="font-semibold text-ink line-clamp-1">{{ product.name }}</h3>
                                <p class="text-sm text-taupe mt-1 line-clamp-2">{{ product.short_description }}</p>
                                <div class="flex justify-between items-center mt-3">
                                    <span class="font-bold text-ink">{{ formatPrice(product.price) }}</span>
                                    <span v-if="product.version" class="text-xs text-taupe">v{{ product.version }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- All Products -->
            <section class="py-12 bg-cream">
                <div class="container mx-auto px-4">
                    <div class="flex flex-col lg:flex-row gap-8">
                        <!-- Categories Sidebar -->
                        <aside class="lg:w-64 shrink-0">
                            <div class="bg-paper rounded-2xl border border-oat-dark p-5 sticky top-24">
                                <h3 class="font-semibold text-ink mb-4">Kategori</h3>
                                <nav class="space-y-1">
                                    <Link :href="route('products.index')"
                                        :class="[
                                            'block px-3 py-2 rounded-lg text-sm transition-colors',
                                            !selectedCategory ? 'bg-terracotta text-cream font-medium' : 'text-taupe hover:bg-oat hover:text-ink'
                                        ]">
                                        Semua Produk
                                    </Link>
                                    <Link v-for="cat in categories" :key="cat.id"
                                        :href="route('products.index', { category: cat.id })"
                                        :class="[
                                            'block px-3 py-2 rounded-lg text-sm transition-colors',
                                            selectedCategory == cat.id ? 'bg-terracotta text-cream font-medium' : 'text-taupe hover:bg-oat hover:text-ink'
                                        ]">
                                        {{ cat.name }}
                                        <span class="text-xs opacity-70">({{ cat.products_count || 0 }})</span>
                                    </Link>
                                </nav>
                            </div>
                        </aside>

                        <!-- Products Grid -->
                        <div class="flex-1">
                            <div class="flex justify-between items-center mb-6">
                                <h2 class="font-serif text-2xl font-bold text-ink">
                                    {{ selectedCategory ? categories.find(c => c.id == selectedCategory)?.name : 'Semua Produk' }}
                                </h2>
                                <span class="text-sm text-taupe">{{ products?.total || 0 }} produk</span>
                            </div>

                            <div v-if="products?.data?.length > 0" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                                <div v-for="product in products.data" :key="product.id"
                                    class="bg-paper rounded-2xl border border-oat-dark overflow-hidden hover:shadow-lg hover:border-terracotta/30 transition-all cursor-pointer">
                                    <Link :href="route('products.show', product.slug)" class="block">
                                        <div class="aspect-[4/3] bg-terracotta/10 relative">
                                            <img loading="lazy" v-if="product.thumbnail" :src="product.thumbnail" :alt="product.name" class="w-full h-full object-cover" />
                                            <div v-else class="w-full h-full flex items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-terracotta/30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16.5 9.4l-9-5.19M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
                                        </div>
                                            <span v-if="product.is_featured" class="absolute top-3 left-3 px-2 py-1 bg-yellow-500 text-white text-xs font-medium rounded-lg">Featured</span>
                                        </div>
                                        <div class="p-4">
                                            <p class="text-xs text-terracotta font-medium">{{ product.category?.name }}</p>
                                            <h3 class="font-semibold text-ink line-clamp-1 mt-1">{{ product.name }}</h3>
                                            <p class="text-sm text-taupe mt-1 line-clamp-2">{{ product.short_description || '-' }}</p>
                                            <div class="flex justify-between items-center mt-3">
                                                <span class="font-bold text-ink">{{ formatPrice(product.price) }}</span>
                                                <span v-if="product.version" class="text-xs text-taupe">v{{ product.version }}</span>
                                            </div>
                                        </div>
                                    </Link>
                                    <!-- Add to Cart Button -->
                                    <div class="px-4 pb-4">
                                        <Link :href="route('checkout.show', product.slug)"
                                            class="block w-full py-2 bg-terracotta text-cream text-center font-medium rounded-xl hover:bg-terracotta-dark transition-colors">
                                            + Keranjang
                                        </Link>
                                    </div>
                                </div>
                            </div>

                            <!-- Empty State -->
                            <div v-else class="bg-paper rounded-2xl border border-oat-dark p-12 text-center">
                                <div class="flex items-center justify-center mb-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-terracotta/30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16.5 9.4l-9-5.19M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
                                </div>
                                <h3 class="font-medium text-ink mb-1">Produk tidak ditemukan</h3>
                                <p class="text-sm text-taupe">Coba kata kunci atau kategori lain</p>
                            </div>

                            <!-- Pagination -->
                            <div v-if="products?.last_page > 1" class="mt-8 flex justify-center">
                                <div class="flex gap-1">
                                    <Link v-for="link in products.links" :key="link.label"
                                        :href="link.url || '#'"
                                        :class="[
                                            'px-4 py-2 rounded-xl text-sm transition-colors',
                                            link.active ? 'bg-terracotta text-cream font-medium' : 'bg-paper border border-oat-dark text-taupe hover:bg-oat'
                                        ]"
                                        v-html="link.label"
                                        preserve-state />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <Footer :social-links="socialLinks" />
    </div>
</template>
