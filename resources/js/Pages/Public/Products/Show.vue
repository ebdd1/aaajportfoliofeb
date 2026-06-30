<script setup>
import Navigation from '@/Components/Navigation.vue';
import Footer from '@/Components/Footer.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useCart } from '@/stores/cart';

const props = defineProps({
    product: Object,
    relatedProducts: Array,
});

const { addItem, isInCart } = useCart();
const isAdding = ref(false);
const addedToCart = ref(false);

const formatPrice = (price) => {
    return 'Rp ' + new Intl.NumberFormat('id-ID').format(price);
};

const handleAddToCart = async () => {
    if (!props.product || isAdding.value) return;
    isAdding.value = true;
    const result = await addItem(props.product);
    if (result.success) {
        addedToCart.value = true;
        setTimeout(() => { addedToCart.value = false; }, 2000);
    }
    isAdding.value = false;
};

const productInCart = () => props.product ? isInCart(props.product.id) : false;
</script>

<template>
    <Head>
        <title>{{ product?.meta_title || product?.name }} - Febryanus</title>
        <meta name="description" :content="product?.meta_description || product?.short_description" />
        <meta property="og:title" :content="product?.meta_title || product?.name" />
        <meta property="og:description" :content="product?.meta_description || product?.short_description" />
        <meta property="og:image" :content="product?.thumbnail || '/og-image.png'" />
        <meta property="og:url" :content="'https://greyhound-quicken-schedule.ngrok-free.dev/products/' + product?.slug" />
        <meta property="og:type" content="product" />
        <meta property="product:price:amount" :content="product?.price" />
        <meta property="product:price:currency" content="IDR" />
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" :content="product?.meta_title || product?.name" />
        <meta name="twitter:description" :content="product?.meta_description || product?.short_description" />
        <meta name="twitter:image" :content="product?.thumbnail || '/og-image.png'" />
        <meta property="product:brand" content="Febryanus" />
        <meta property="product:availability" :content="product?.is_active ? 'in stock' : 'out of stock'" />
    </Head>
    <div class="min-h-screen flex flex-col bg-cream">
        <Navigation />

        <main class="flex-1">
            <!-- Product Content -->
            <section class="py-8 lg:py-12">
                <div class="container mx-auto px-4">
                    <!-- Breadcrumb - di bawah navbar dengan spacing -->
                    <div class="mt-16 mb-6">
                        <nav class="flex items-center gap-2 text-sm">
                            <Link href="/" class="text-taupe hover:text-terracotta transition-colors">Home</Link>
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-taupe/40 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>
                            <Link href="/products" class="text-taupe hover:text-terracotta transition-colors">Produk Digital</Link>
                            <template v-if="product?.name">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-taupe/40 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>
                                <span class="text-ink font-medium">{{ product.name }}</span>
                            </template>
                        </nav>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12">
                        <!-- Main Content -->
                        <div class="lg:col-span-7 xl:col-span-8">
                            <!-- Product Image -->
                            <div class="bg-paper rounded-2xl border border-oat-dark overflow-hidden mb-8">
                                <div class="aspect-video bg-gradient-to-br from-terracotta/5 to-oat relative">
                                    <img v-if="product?.thumbnail" :src="product.thumbnail" :alt="product.name" class="w-full h-full object-cover" />
                                    <div v-else class="w-full h-full flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-20 h-20 text-terracotta/20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                            <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                                            <polyline points="14 2 14 8 20 8"/>
                                            <line x1="16" y1="13" x2="8" y2="13"/>
                                            <line x1="16" y1="17" x2="8" y2="17"/>
                                        </svg>
                                    </div>
                                    <span v-if="product?.version" class="absolute top-4 right-4 px-3 py-1.5 bg-ink/80 backdrop-blur-sm text-cream text-xs font-mono rounded-lg">
                                        v{{ product.version }}
                                    </span>
                                </div>
                            </div>

                            <!-- Product Info -->
                            <div class="bg-paper rounded-2xl border border-oat-dark p-6 md:p-8 mb-8">
                                <div class="flex flex-wrap items-center gap-3 mb-4">
                                    <span v-if="product?.category" class="px-3 py-1.5 bg-terracotta/10 text-terracotta text-sm font-medium rounded-lg">
                                        {{ product.category.name }}
                                    </span>
                                    <div class="flex items-center gap-1.5 text-sm text-taupe">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                        <span>{{ product?.downloads || 0 }} downloads</span>
                                    </div>
                                </div>

                                <h1 class="font-serif text-3xl md:text-4xl font-bold text-ink mb-4">{{ product?.name }}</h1>

                                <div class="prose prose-sm max-w-none text-taupe">
                                    <div v-html="product?.description || '<p class=\'text-taupe/60 italic\'>Tidak ada deskripsi produk.</p>'"></div>
                                </div>
                            </div>

                            <!-- Features -->
                            <div v-if="product?.features && product.features.length > 0" class="bg-paper rounded-2xl border border-oat-dark p-6 md:p-8">
                                <h2 class="font-serif text-xl font-bold text-ink mb-6">Fitur</h2>
                                <ul class="space-y-4">
                                    <li v-for="(feature, index) in product.features" :key="index" class="flex items-start gap-3">
                                        <span class="w-5 h-5 rounded-full bg-terracotta/10 flex items-center justify-center shrink-0 mt-0.5">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 text-terracotta" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                                        </span>
                                        <span class="text-taupe">{{ feature }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- Sidebar -->
                        <div class="lg:col-span-5 xl:col-span-4">
                            <div class="lg:sticky lg:top-24 space-y-6">
                                <!-- Price Card -->
                                <div class="bg-paper rounded-2xl border border-oat-dark overflow-hidden">
                                    <div class="p-6 text-center border-b border-oat-dark/50">
                                        <p class="text-sm text-taupe mb-2">Harga</p>
                                        <p class="font-serif text-4xl font-bold text-terracotta">{{ formatPrice(product?.price || 0) }}</p>
                                    </div>
                                    <div class="p-6 space-y-3">
                                        <!-- Buy Now -->
                                        <a :href="route('checkout.show', product?.slug)"
                                            class="block w-full py-3.5 bg-terracotta text-cream font-semibold rounded-xl hover:bg-terracotta-dark active:scale-[0.98] transition-all text-center">
                                            Beli Sekarang
                                        </a>

                                        <!-- Add to Cart Button -->
                                        <button
                                            @click="handleAddToCart"
                                            :disabled="isAdding || productInCart()"
                                            :class="[
                                                'w-full py-3 px-6 rounded-xl transition-all text-center flex items-center justify-center gap-2 font-medium',
                                                productInCart()
                                                    ? 'bg-green-50 border-2 border-green-200 text-green-700 cursor-default'
                                                    : 'border-2 border-transparent bg-oat text-ink hover:bg-oat-dark hover:border-terracotta/20 active:scale-[0.98]'
                                            ]"
                                        >
                                            <svg v-if="productInCart()" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                                <polyline points="20 6 9 17 4 12"/>
                                            </svg>
                                            <svg v-else-if="isAdding" class="w-5 h-5 animate-spin" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M21 12a9 9 0 11-2.65-6.35A9 9 0 0121 12"/>
                                            </svg>
                                            <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
                                                <path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 002-1.61L23 6H6"/>
                                            </svg>
                                            {{ productInCart() ? 'Ada di Keranjang' : (isAdding ? 'Menambahkan...' : 'Tambah Keranjang') }}
                                        </button>

                                        <!-- Demo Button -->
                                        <a v-if="product?.demo_url" :href="product.demo_url" target="_blank"
                                            class="block w-full py-2.5 text-sm border border-oat-dark text-taupe hover:border-terracotta/30 hover:text-ink rounded-xl transition-colors text-center">
                                            Lihat Demo
                                        </a>
                                    </div>
                                </div>

                                <!-- Benefits -->
                                <div class="bg-paper rounded-2xl border border-oat-dark p-6">
                                    <h3 class="font-semibold text-ink mb-4">Yang Anda Dapat</h3>
                                    <ul class="space-y-3">
                                        <li class="flex items-start gap-3 text-sm text-taupe">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-green-600 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                            File lengkap & dokumentasi
                                        </li>
                                        <li class="flex items-start gap-3 text-sm text-taupe">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-green-600 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                            Support via email
                                        </li>
                                        <li class="flex items-start gap-3 text-sm text-taupe">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-green-600 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                            Update gratis seumur hidup
                                        </li>
                                        <li v-if="product?.license" class="flex items-start gap-3 text-sm text-taupe">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-green-600 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                            {{ product.license }}
                                        </li>
                                    </ul>
                                </div>

                                <!-- Quick Info -->
                                <div class="bg-oat/30 rounded-2xl border border-oat-dark/50 p-6">
                                    <dl class="space-y-3 text-sm">
                                        <div v-if="product?.version" class="flex justify-between">
                                            <dt class="text-taupe">Versi</dt>
                                            <dd class="text-ink font-medium">v{{ product.version }}</dd>
                                        </div>
                                        <div v-if="product?.license" class="flex justify-between">
                                            <dt class="text-taupe">Lisensi</dt>
                                            <dd class="text-ink font-medium">{{ product.license }}</dd>
                                        </div>
                                        <div class="flex justify-between">
                                            <dt class="text-taupe">Format</dt>
                                            <dd class="text-ink font-medium">Source Code</dd>
                                        </div>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Related Products -->
            <section v-if="relatedProducts && relatedProducts.length > 0" class="py-12 bg-oat/30 border-t border-oat-dark/50">
                <div class="container mx-auto px-4">
                    <h2 class="font-serif text-2xl font-bold text-ink mb-8">Produk Terkait</h2>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
                        <div v-for="item in relatedProducts" :key="item.id" class="bg-paper rounded-xl border border-oat-dark overflow-hidden hover:shadow-lg hover:border-terracotta/30 transition-all group">
                            <Link :href="route('products.show', item.slug)">
                                <div class="aspect-[4/3] bg-terracotta/5 overflow-hidden">
                                    <img v-if="item.thumbnail" :src="item.thumbnail" :alt="item.name" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                                    <div v-else class="w-full h-full flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-terracotta/20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                    </div>
                                </div>
                                <div class="p-4">
                                    <p class="text-xs text-terracotta font-medium mb-1">{{ item.category?.name }}</p>
                                    <h3 class="font-medium text-ink text-sm line-clamp-1">{{ item.name }}</h3>
                                    <p class="font-semibold text-ink mt-2">{{ formatPrice(item.price) }}</p>
                                </div>
                            </Link>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <Footer :social-links="socialLinks" />
    </div>
</template>
