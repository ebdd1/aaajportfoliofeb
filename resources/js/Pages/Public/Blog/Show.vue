<script setup>
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import DOMPurify from 'dompurify';
import BlogLayout from '@/Layouts/BlogLayout.vue';

defineOptions({ layout: BlogLayout });

const props = defineProps({
    post: Object,
    relatedPosts: Array,
});

const siteUrl = window.location.origin;
const siteName = 'Febryanus Tambing';

const formatDate = (date) => {
    if (!date) return '';
    return new Intl.DateTimeFormat('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    }).format(new Date(date));
};

const formatIsoDate = (date) => {
    if (!date) return '';
    return new Date(date).toISOString();
};

// Sanitize content to prevent XSS
const sanitizedContent = computed(() => {
    if (!props.post.content) return '';
    return DOMPurify.sanitize(props.post.content, {
        ALLOWED_TAGS: ['p', 'br', 'strong', 'em', 'u', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'ul', 'ol', 'li', 'blockquote', 'pre', 'code', 'a', 'img', 'table', 'thead', 'tbody', 'tr', 'th', 'td', 'hr', 'span', 'div'],
        ALLOWED_ATTR: ['href', 'src', 'alt', 'class', 'target', 'rel'],
    });
});

const seoTitle = computed(() => `${props.post.title} - ${siteName}`);
const seoDescription = computed(() => props.post.excerpt || `Baca artikel ${props.post.title} di blog ${siteName}`);
const canonicalUrl = computed(() => `${siteUrl}/blog/${props.post.slug}`);

const articleSchema = computed(() => ({
    '@context': 'https://schema.org',
    '@type': 'Article',
    'headline': props.post.title,
    'description': props.post.excerpt,
    'image': props.post.featured_image_url || `${siteUrl}/og-default.png`,
    'datePublished': formatIsoDate(props.post.published_at),
    'dateModified': formatIsoDate(props.post.updated_at),
    'author': {
        '@type': 'Person',
        'name': props.post.author?.name || 'Febryanus Tambing',
        'url': siteUrl
    },
    'publisher': {
        '@type': 'Person',
        'name': siteName,
        'url': siteUrl
    },
    'mainEntityOfPage': {
        '@type': 'WebPage',
        '@id': canonicalUrl.value
    },
    'keywords': props.post.tags?.map(t => t.name).join(', ') || ''
}));

const breadcrumbsSchema = computed(() => ({
    '@context': 'https://schema.org',
    '@type': 'BreadcrumbList',
    'itemListElement': [
        {
            '@type': 'ListItem',
            'position': 1,
            'name': 'Home',
            'item': siteUrl
        },
        {
            '@type': 'ListItem',
            'position': 2,
            'name': 'Blog',
            'item': `${siteUrl}/blog`
        },
        {
            '@type': 'ListItem',
            'position': 3,
            'name': props.post.title,
            'item': canonicalUrl.value
        }
    ]
}));
</script>

<template>
    <Head>
        <title>{{ seoTitle }}</title>
        <meta name="description" :content="seoDescription" />
        <meta name="keywords" :content="post.tags?.map(t => t.name).join(', ') || ''" />
        <meta name="author" :content="post.author?.name || 'Febryanus Tambing'" />
        <link rel="canonical" :href="canonicalUrl" />

        <!-- Open Graph -->
        <meta property="og:type" content="article" />
        <meta property="og:title" :content="seoTitle" />
        <meta property="og:description" :content="seoDescription" />
        <meta property="og:url" :content="canonicalUrl" />
        <meta property="og:site_name" :content="siteName" />
        <meta property="og:image" :content="post.featured_image_url || `${siteUrl}/og-default.png`" />
        <meta property="og:image:width" content="1200" />
        <meta property="og:image:height" content="630" />

        <!-- Article Meta -->
        <meta property="article:published_time" :content="formatIsoDate(post.published_at)" />
        <meta property="article:modified_time" :content="formatIsoDate(post.updated_at)" />
        <meta property="article:author" :content="post.author?.name || 'Febryanus Tambing'" />
        <meta v-for="cat in (post.categories || [])" :key="cat.id" property="article:section" :content="cat.name" />
        <meta v-for="tag in (post.tags || [])" :key="tag.id" property="article:tag" :content="tag.name" />

        <!-- Twitter Card -->
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" :content="seoTitle" />
        <meta name="twitter:description" :content="seoDescription" />
        <meta name="twitter:image" :content="post.featured_image_url || `${siteUrl}/og-default.png`" />
        <meta name="twitter:url" :content="canonicalUrl" />

        <!-- JSON-LD Structured Data -->
        <script type="application/ld+json" v-html="JSON.stringify(articleSchema)" />
        <script type="application/ld+json" v-html="JSON.stringify(breadcrumbsSchema)" />
    </Head>

    <div class="min-h-screen bg-cream">
        <!-- Hero Section -->
        <section class="bg-paper border-b border-oat-dark">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <!-- Breadcrumb -->
                <nav class="mb-6">
                    <ol class="flex items-center gap-2 text-sm">
                        <li>
                            <Link href="/" class="text-taupe hover:text-terracotta transition-colors">Home</Link>
                        </li>
                        <li class="text-taupe">/</li>
                        <li>
                            <Link href="/blog" class="text-taupe hover:text-terracotta transition-colors">Blog</Link>
                        </li>
                        <li class="text-taupe">/</li>
                        <li class="text-ink truncate max-w-[200px]">{{ post.title }}</li>
                    </ol>
                </nav>

                <!-- Categories -->
                <div class="flex flex-wrap gap-2 mb-4">
                    <span
                        v-for="cat in (post.categories || [])"
                        :key="cat.id"
                        class="text-sm font-medium text-terracotta bg-terracotta/10 px-3 py-1 rounded-full"
                    >
                        {{ cat.name }}
                    </span>
                </div>

                <!-- Title -->
                <h1 class="font-serif text-3xl lg:text-4xl font-bold text-ink mb-4">
                    {{ post.title }}
                </h1>

                <!-- Meta -->
                <div class="flex flex-wrap items-center gap-4 text-sm text-taupe">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-terracotta/10 flex items-center justify-center">
                            <span class="font-medium text-terracotta text-sm">{{ post.author?.name?.charAt(0) || 'A' }}</span>
                        </div>
                        <span>{{ post.author?.name }}</span>
                    </div>
                    <span>{{ formatDate(post.published_at) }}</span>
                    <span>{{ post.reading_time }} min read</span>
                    <span>{{ post.view_count }} views</span>
                </div>
            </div>
        </section>

        <!-- Featured Image -->
        <section v-if="post.featured_image_url" class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 -mt-8 relative z-10">
            <div class="bg-paper rounded-2xl overflow-hidden border border-oat-dark shadow-lg">
                <img
                    :src="post.featured_image_url"
                    :alt="post.title"
                    loading="lazy"
                    class="w-full max-h-[500px] object-cover"
                />
            </div>
        </section>

        <!-- Content -->
        <section class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="bg-paper rounded-2xl border border-oat-dark p-6 lg:p-10">
                <!-- Tags -->
                <div v-if="post.tags && post.tags.length > 0" class="flex flex-wrap gap-2 mb-8 pb-8 border-b border-oat-dark">
                    <span
                        v-for="tag in post.tags"
                        :key="tag.id"
                        class="text-sm font-mono text-taupe bg-oat px-3 py-1 rounded-lg"
                    >
                        #{{ tag.name }}
                    </span>
                </div>

                <!-- Excerpt -->
                <p v-if="post.excerpt" class="text-lg text-taupe font-medium mb-8 pb-8 border-b border-oat-dark italic">
                    {{ post.excerpt }}
                </p>

                <!-- Content -->
                <article class="prose prose-lg max-w-none
                    prose-headings:font-serif prose-headings:text-ink
                    prose-p:text-taupe prose-p:leading-relaxed
                    prose-a:text-terracotta prose-a:no-underline hover:prose-a:underline
                    prose-blockquote:border-l-terracotta prose-blockquote:text-taupe prose-blockquote:italic
                    prose-code:text-ink prose-code:bg-oat prose-code:px-1.5 prose-code:py-0.5 prose-code:rounded prose-code:before:content-none prose-code:after:content-none
                    prose-pre:bg-ink prose-pre:text-cream prose-pre:rounded-lg
                    prose-img:rounded-lg prose-img:my-8
                    prose-hr:border-oat-dark">
                    <div class="text-ink leading-relaxed whitespace-pre-wrap" v-html="sanitizedContent"></div>
                </article>

                <!-- Share Section -->
                <div class="mt-12 pt-8 border-t border-oat-dark">
                    <p class="text-sm text-taupe mb-4">Bagikan artikel ini:</p>
                    <div class="flex gap-3">
                        <a
                            :href="`https://twitter.com/intent/tweet?text=${encodeURIComponent(post.title)}&url=${encodeURIComponent(canonicalUrl)}`"
                            target="_blank"
                            rel="noopener noreferrer"
                            aria-label="Bagikan di Twitter"
                            class="p-2 bg-oat rounded-lg text-taupe hover:text-terracotta hover:bg-oat-dark transition-colors duration-200 active:scale-95"
                        >
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                            </svg>
                        </a>
                        <a
                            :href="`https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(canonicalUrl)}`"
                            target="_blank"
                            rel="noopener noreferrer"
                            aria-label="Bagikan di LinkedIn"
                            class="p-2 bg-oat rounded-lg text-taupe hover:text-terracotta hover:bg-oat-dark transition-colors duration-200 active:scale-95"
                        >
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                            </svg>
                        </a>
                        <a
                            :href="`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(canonicalUrl)}`"
                            target="_blank"
                            rel="noopener noreferrer"
                            aria-label="Bagikan di Facebook"
                            class="p-2 bg-oat rounded-lg text-taupe hover:text-terracotta hover:bg-oat-dark transition-colors duration-200 active:scale-95"
                        >
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Related Posts -->
        <section v-if="relatedPosts && relatedPosts.length > 0" class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
            <h2 class="font-serif text-2xl font-bold text-ink mb-6">Related Posts</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <Link
                    v-for="related in relatedPosts"
                    :key="related.id"
                    :href="route('blog.show', related.slug)"
                    class="group bg-paper rounded-2xl overflow-hidden border border-oat-dark hover:shadow-lg transition-shadow"
                >
                    <div class="aspect-video bg-oat overflow-hidden">
                        <img
                            v-if="related.featured_image_url"
                            :src="related.featured_image_url"
                            :alt="related.title"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                        />
                        <div v-else class="w-full h-full flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-taupe/30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                                <polyline points="14 2 14 8 20 8"/>
                            </svg>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="flex items-center gap-2 mb-2">
                            <span
                                v-for="cat in (related.categories || []).slice(0, 1)"
                                :key="cat.id"
                                class="text-xs font-medium text-terracotta bg-terracotta/10 px-2 py-0.5 rounded-full"
                            >
                                {{ cat.name }}
                            </span>
                            <span class="text-xs text-taupe">{{ related.reading_time }} min</span>
                        </div>
                        <h3 class="font-serif font-semibold text-ink group-hover:text-terracotta transition-colors line-clamp-2">
                            {{ related.title }}
                        </h3>
                    </div>
                </Link>
            </div>
        </section>

        <!-- Back to Blog -->
        <section class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
            <Link
                href="/blog"
                class="inline-flex items-center gap-2 text-taupe hover:text-terracotta transition-colors"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
                Kembali ke Blog
            </Link>
        </section>
    </div>
</template>
