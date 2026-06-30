<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import BlogLayout from '@/Layouts/BlogLayout.vue';

defineOptions({ layout: BlogLayout });

const props = defineProps({
    posts: Object,
    featuredPosts: Array,
    categories: Array,
    tags: Array,
    filters: Object,
});

const siteUrl = window.location.origin;

const selectedCategory = ref(props.filters?.category || '');
const selectedTag = ref(props.filters?.tag || '');

const pageTitle = computed(() => {
    if (selectedCategory.value) {
        const cat = props.categories?.find(c => c.slug === selectedCategory.value);
        return cat ? `${cat.name} - Blog` : 'Blog';
    }
    if (selectedTag.value) {
        const tag = props.tags?.find(t => t.slug === selectedTag.value);
        return tag ? `Tag: ${tag.name} - Blog` : 'Blog';
    }
    return 'Blog - Febryanus Tambing';
});

const formatDate = (date) => {
    if (!date) return '';
    return new Intl.DateTimeFormat('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }).format(new Date(date));
};

const handleCategoryFilter = (slug) => {
    selectedCategory.value = selectedCategory.value === slug ? '' : slug;
    selectedTag.value = '';
    router.get(route('blog.index'), { category: selectedCategory.value || null, tag: null }, { preserveState: true });
};

const handleTagFilter = (slug) => {
    selectedTag.value = selectedTag.value === slug ? '' : slug;
    selectedCategory.value = '';
    router.get(route('blog.index'), { tag: selectedTag.value || null, category: null }, { preserveState: true });
};

const clearFilters = () => {
    selectedCategory.value = '';
    selectedTag.value = '';
    router.get(route('blog.index'), {}, { preserveState: true });
};
</script>

<template>
    <Head>
        <title>{{ pageTitle }}</title>
        <meta name="description" content="Thoughts, tutorials, and insights about programming" />
        <link rel="canonical" :href="siteUrl + '/blog'" />
        <link rel="alternate" type="application/rss+xml" title="RSS Feed" :href="siteUrl + '/blog/feed'" />
    </Head>

    <div class="min-h-screen bg-cream">
        <!-- Hero -->
        <section class="bg-paper border-b border-oat-dark pt-6 sm:pt-0">
            <div class="container-padding py-12 lg:py-16">
                <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6">
                    <div>
                        <div class="inline-flex items-center gap-2 px-3 py-1 bg-terracotta/10 rounded-full mb-4">
                            <span class="w-1.5 h-1.5 bg-terracotta rounded-full animate-pulse"></span>
                            <span class="text-xs font-medium text-terracotta uppercase tracking-wide">Writing</span>
                        </div>
                        <h1 class="font-serif text-3xl sm:text-4xl lg:text-5xl font-bold text-ink mb-3">
                            <span class="text-terracotta">Thoughts</span> & Insights
                        </h1>
                        <p class="text-base lg:text-lg text-taupe max-w-lg">
                            Exploring code, creativity, and technology.
                        </p>
                    </div>
                    <div class="hidden lg:flex items-center gap-6">
                        <div class="text-center">
                            <p class="font-serif text-3xl font-bold text-ink">{{ posts.total }}</p>
                            <p class="text-xs text-taupe">Articles</p>
                        </div>
                        <div class="w-px h-12 bg-oat-dark"></div>
                        <div class="text-center">
                            <p class="font-serif text-3xl font-bold text-ink">{{ categories?.length || 0 }}</p>
                            <p class="text-xs text-taupe">Topics</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Main Content -->
        <div class="container-padding py-8 lg:py-10">
            <div class="flex flex-col lg:flex-row gap-8 lg:gap-10">
                <!-- Posts Area -->
                <main class="flex-1 min-w-0">
                    <!-- Featured -->
                    <section v-if="featuredPosts?.length" class="mb-8">
                        <div class="flex items-center gap-4 mb-5">
                            <h2 class="font-serif text-lg lg:text-xl font-bold text-ink">Featured</h2>
                            <div class="flex-1 h-px bg-gradient-to-r from-terracotta/30 to-transparent"></div>
                        </div>

                        <!-- Desktop Grid -->
                        <div class="hidden md:grid grid-cols-3 gap-5">
                            <Link
                                v-for="post in featuredPosts" :key="post.id"
                                :href="route('blog.show', post.slug)"
                                class="group bg-paper rounded-xl overflow-hidden border border-oat-dark hover:border-terracotta/30 hover:shadow-lg transition-all duration-200"
                            >
                                <div class="aspect-[4/3] bg-oat overflow-hidden">
                                    <img
                                        v-if="post.featured_image_url"
                                        :src="post.featured_image_url"
                                        :alt="post.title"
                                        loading="lazy"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                    />
                                    <div v-else class="w-full h-full flex items-center justify-center bg-gradient-to-br from-oat to-oat-dark">
                                        <svg class="w-10 h-10 text-taupe/20" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2H8l-6-4-6 4z"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="p-5">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span
                                            v-for="cat in (post.categories || []).slice(0, 1)" :key="cat.id"
                                            class="text-xs font-medium text-terracotta bg-terracotta/10 px-2 py-0.5 rounded-full"
                                        >{{ cat.name }}</span>
                                        <span class="text-xs text-taupe/60">{{ post.reading_time }} min</span>
                                    </div>
                                    <h3 class="font-serif text-base font-semibold text-ink group-hover:text-terracotta transition-colors line-clamp-2 mb-2">
                                        {{ post.title }}
                                    </h3>
                                    <p class="text-sm text-taupe line-clamp-2 mb-3">{{ post.excerpt }}</p>
                                    <div class="flex items-center justify-between pt-3 border-t border-oat-dark">
                                        <div class="flex items-center gap-2">
                                            <div class="w-6 h-6 rounded-full bg-terracotta/10 flex items-center justify-center">
                                                <span class="text-[10px] font-medium text-terracotta">{{ post.author?.name?.charAt(0) || 'A' }}</span>
                                            </div>
                                            <span class="text-xs text-taupe">{{ post.author?.name }}</span>
                                        </div>
                                        <span class="text-xs text-taupe/60">{{ formatDate(post.published_at) }}</span>
                                    </div>
                                </div>
                            </Link>
                        </div>

                        <!-- Mobile Scroll -->
                        <div class="md:hidden flex gap-4 overflow-x-auto pb-4 -mx-6 px-6 scrollbar-hide snap-x snap-mandatory">
                            <Link
                                v-for="post in featuredPosts" :key="'mobile-' + post.id"
                                :href="route('blog.show', post.slug)"
                                class="flex-shrink-0 w-72 bg-paper rounded-xl overflow-hidden border border-oat-dark snap-start"
                            >
                                <div class="aspect-video overflow-hidden bg-oat">
                                    <img v-if="post.featured_image_url" :src="post.featured_image_url" :alt="post.title" class="w-full h-full object-cover" />
                                    <div v-else class="w-full h-full bg-oat"></div>
                                </div>
                                <div class="p-4">
                                    <span class="text-xs text-terracotta bg-terracotta/10 px-2 py-0.5 rounded-full">{{ post.categories?.[0]?.name }}</span>
                                    <h3 class="font-serif text-base font-semibold text-ink mt-2 line-clamp-2">{{ post.title }}</h3>
                                    <p class="text-xs text-taupe mt-1">{{ formatDate(post.published_at) }}</p>
                                </div>
                            </Link>
                        </div>
                    </section>

                    <!-- Latest Posts -->
                    <section>
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                            <div>
                                <h2 class="font-serif text-lg lg:text-xl font-bold text-ink">
                                    {{ selectedCategory || selectedTag ? 'Filtered Posts' : 'Latest Articles' }}
                                </h2>
                                <p v-if="!selectedCategory && !selectedTag" class="text-sm text-taupe mt-1">
                                    {{ posts.total }} articles published
                                </p>
                            </div>
                            <div v-if="selectedCategory || selectedTag" class="flex items-center gap-2">
                                <span class="text-sm text-taupe">Filtered:</span>
                                <span class="flex items-center gap-1.5 px-3 py-1 bg-terracotta/10 text-sm text-terracotta rounded-full">
                                    {{ selectedCategory || selectedTag }}
                                    <button @click="clearFilters" class="ml-1 hover:text-terracotta-dark">✕</button>
                                </span>
                            </div>
                        </div>

                        <!-- Empty State -->
                        <div v-if="posts.data.length === 0" class="bg-paper rounded-xl border border-oat-dark p-12 text-center">
                            <div class="w-16 h-16 mx-auto mb-4 rounded-xl bg-oat/50 flex items-center justify-center text-taupe/40">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2H8l-6-4-6 4z"/>
                                </svg>
                            </div>
                            <h3 class="font-serif text-lg font-semibold text-ink mb-2">No articles found</h3>
                            <p class="text-taupe mb-4">Adjust your filters or browse all articles.</p>
                            <button @click="clearFilters" class="inline-flex items-center gap-2 px-5 py-2.5 bg-terracotta text-cream text-sm font-medium rounded-lg hover:bg-terracotta-dark transition-colors">
                                Clear filters
                            </button>
                        </div>

                        <!-- Posts List -->
                        <div v-else class="space-y-4">
                            <Link
                                v-for="post in posts.data" :key="post.id"
                                :href="route('blog.show', post.slug)"
                                class="group block bg-paper rounded-xl overflow-hidden border border-oat-dark/50 hover:border-terracotta/30 hover:shadow-lg transition-all duration-200"
                            >
                                <div class="flex flex-col sm:flex-row">
                                    <div class="sm:w-48 h-40 sm:h-auto overflow-hidden bg-oat shrink-0">
                                        <img
                                            v-if="post.featured_image_url"
                                            :src="post.featured_image_url"
                                            :alt="post.title"
                                            loading="lazy"
                                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                        />
                                        <div v-else class="w-full h-full bg-gradient-to-br from-oat to-oat-dark flex items-center justify-center">
                                            <svg class="w-8 h-8 text-taupe/20" fill="none" stroke="currentColor" stroke-width="1">
                                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2H8l-6-4-6 4z"/>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="flex-1 p-5 flex flex-col justify-center">
                                        <div class="flex items-center gap-2 mb-2">
                                            <span
                                                v-for="cat in (post.categories || []).slice(0, 2)" :key="cat.id"
                                                class="text-xs font-medium text-terracotta bg-terracotta/10 px-2 py-0.5 rounded-full"
                                            >{{ cat.name }}</span>
                                            <span class="hidden sm:inline text-xs text-taupe/60">{{ post.reading_time }} min read</span>
                                            <span class="hidden lg:inline text-xs text-taupe/60">{{ post.view_count }} views</span>
                                        </div>
                                        <h3 class="font-serif text-base lg:text-lg font-semibold text-ink group-hover:text-terracotta transition-colors line-clamp-2 mb-2">
                                            {{ post.title }}
                                        </h3>
                                        <p class="text-sm text-taupe line-clamp-2 mb-3 hidden sm:block">{{ post.excerpt }}</p>
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-2">
                                                <div class="w-6 h-6 rounded-full bg-terracotta/10 flex items-center justify-center">
                                                    <span class="text-xs font-medium text-terracotta">{{ post.author?.name?.charAt(0) || 'A' }}</span>
                                                </div>
                                                <span class="text-xs sm:text-sm text-taupe">{{ post.author?.name }}</span>
                                                <span class="text-taupe/40">•</span>
                                                <span class="text-xs sm:text-sm text-taupe/60">{{ formatDate(post.published_at) }}</span>
                                            </div>
                                            <span class="hidden sm:flex items-center gap-1 text-sm font-medium text-terracotta opacity-0 group-hover:opacity-100 transition-opacity">
                                                Read →
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </Link>
                        </div>

                        <!-- Pagination -->
                        <div v-if="posts.data.length > 0" class="mt-8">
                            <nav class="flex items-center justify-center gap-1 flex-wrap">
                                <Link
                                    v-for="link in posts.links" :key="link.label"
                                    :href="link.url || '#'"
                                    :class="[
                                        'min-w-[2.25rem] h-9 px-2 flex items-center justify-center rounded-lg text-xs sm:text-sm font-medium transition-all duration-150',
                                        link.active
                                            ? 'bg-terracotta text-cream shadow-sm'
                                            : 'bg-paper text-taupe border border-oat-dark hover:bg-oat'
                                    ]"
                                >
                                    <span v-html="link.label"></span>
                                </Link>
                            </nav>
                        </div>
                    </section>
                </main>

                <!-- Sidebar -->
                <aside class="lg:w-64 shrink-0">
                    <div class="lg:sticky lg:top-24 space-y-5">
                        <!-- Categories -->
                        <div class="bg-paper rounded-xl border border-oat-dark p-5">
                            <h3 class="font-serif text-base font-semibold text-ink mb-4">Topics</h3>
                            <div class="space-y-1">
                                <button
                                    v-for="cat in categories" :key="cat.id"
                                    @click="handleCategoryFilter(cat.slug)"
                                    :class="[
                                        'w-full flex items-center justify-between px-3 py-2 rounded-lg text-sm transition-all duration-150',
                                        selectedCategory === cat.slug
                                            ? 'bg-terracotta text-cream font-medium'
                                            : 'hover:bg-oat text-ink hover:translate-x-1'
                                    ]"
                                >
                                    <span>{{ cat.name }}</span>
                                    <span :class="selectedCategory === cat.slug ? 'text-cream/70' : 'text-taupe'">
                                        {{ cat.posts_count || 0 }}
                                    </span>
                                </button>
                            </div>
                        </div>

                        <!-- Tags -->
                        <div class="bg-paper rounded-xl border border-oat-dark p-5">
                            <h3 class="font-serif text-base font-semibold text-ink mb-4">Tags</h3>
                            <div class="flex flex-wrap gap-2">
                                <button
                                    v-for="tag in tags" :key="tag.id"
                                    @click="handleTagFilter(tag.slug)"
                                    :class="[
                                        'px-3 py-1.5 rounded-full text-sm transition-all duration-150',
                                        selectedTag === tag.slug
                                            ? 'bg-terracotta text-cream'
                                            : 'bg-oat text-taupe hover:bg-oat-dark hover:text-ink'
                                    ]"
                                >
                                    #{{ tag.name }}
                                </button>
                            </div>
                        </div>

                        <!-- RSS -->
                        <div class="bg-gradient-to-br from-terracotta to-terracotta-dark rounded-xl p-5 text-cream">
                            <div class="flex items-center gap-2 mb-2">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M6.18 15.64a2.18 2.18 0 0 1 2.18 2.18C8.36 19.01 7.38 20 6.18 20 4.98 20 4 19.01 4 17.82a2.18 2.18 0 0 1 2.18-2.18M4 4.44A15.56 15.56 0 0 1 19.56 20h-2.83A12.73 12.73 0 0 1 4 7.27V4.44m0 5.66a9.9 9.9 0 0 1 9.9 9.9h-2.83A7.07 7.07 0 0 1 4 12.93V10.1z"/>
                                </svg>
                                <h3 class="font-serif text-base font-semibold">RSS Feed</h3>
                            </div>
                            <p class="text-cream/80 text-sm mb-3">Subscribe via RSS reader.</p>
                            <a
                                :href="siteUrl + '/blog/feed'"
                                target="_blank"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-cream text-terracotta text-sm font-medium rounded-lg hover:bg-cream/90 transition-colors"
                            >
                                Subscribe
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M18 13v6a2 2 0 0 1-2 2h-3a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v6M15 3h3m0 0v3m0-3h-3"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </div>
</template>

<style scoped>
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
</style>
