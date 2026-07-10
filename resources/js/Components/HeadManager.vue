<script setup>
import { watch, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useSeo } from '@/Composables/useSeo';

const props = defineProps({
    title: {
        type: String,
        default: '',
    },
    description: {
        type: String,
        default: '',
    },
    robots: {
        type: String,
        default: '',
    },
    canonical: {
        type: String,
        default: '',
    },
    ogImage: {
        type: String,
        default: '',
    },
    ogType: {
        type: String,
        default: 'website',
    },
});

const page = usePage();
const { seo, getPageTitle, getMetaDescription, getRobots, getCanonical, getOgImage } = useSeo();

function updateMeta() {
    const seoData = seo.value;

    // Title
    const title = getPageTitle(props.title);
    document.title = title;

    // Meta description
    let descEl = document.querySelector('meta[name="description"]');
    if (!descEl) {
        descEl = document.createElement('meta');
        descEl.name = 'description';
        document.head.appendChild(descEl);
    }
    const description = getMetaDescription(props.description);
    descEl.content = description;

    // Robots
    let robotsEl = document.querySelector('meta[name="robots"]');
    if (!robotsEl) {
        robotsEl = document.createElement('meta');
        robotsEl.name = 'robots';
        document.head.appendChild(robotsEl);
    }
    robotsEl.content = getRobots(props.robots);

    // Canonical URL
    let canonicalEl = document.querySelector('link[rel="canonical"]');
    if (!canonicalEl) {
        canonicalEl = document.createElement('link');
        canonicalEl.rel = 'canonical';
        document.head.appendChild(canonicalEl);
    }
    canonicalEl.href = getCanonical(props.canonical || window.location.pathname);

    // OG Title
    let ogTitleEl = document.querySelector('meta[property="og:title"]');
    if (!ogTitleEl) {
        ogTitleEl = document.createElement('meta');
        ogTitleEl.setAttribute('property', 'og:title');
        document.head.appendChild(ogTitleEl);
    }
    ogTitleEl.content = title;

    // OG Description
    let ogDescEl = document.querySelector('meta[property="og:description"]');
    if (!ogDescEl) {
        ogDescEl = document.createElement('meta');
        ogDescEl.setAttribute('property', 'og:description');
        document.head.appendChild(ogDescEl);
    }
    ogDescEl.content = description;

    // OG Image
    const ogImage = getOgImage(props.ogImage);
    if (ogImage) {
        let ogImageEl = document.querySelector('meta[property="og:image"]');
        if (!ogImageEl) {
            ogImageEl = document.createElement('meta');
            ogImageEl.setAttribute('property', 'og:image');
            document.head.appendChild(ogImageEl);
        }
        ogImageEl.content = ogImage;

        let ogImageAltEl = document.querySelector('meta[property="og:image:alt"]');
        if (!ogImageAltEl) {
            ogImageAltEl = document.createElement('meta');
            ogImageAltEl.setAttribute('property', 'og:image:alt');
            document.head.appendChild(ogImageAltEl);
        }
        ogImageAltEl.content = title;
    }

    // OG URL
    let ogUrlEl = document.querySelector('meta[property="og:url"]');
    if (!ogUrlEl) {
        ogUrlEl = document.createElement('meta');
        ogUrlEl.setAttribute('property', 'og:url');
        document.head.appendChild(ogUrlEl);
    }
    ogUrlEl.content = window.location.href;

    // OG Type
    let ogTypeEl = document.querySelector('meta[property="og:type"]');
    if (!ogTypeEl) {
        ogTypeEl = document.createElement('meta');
        ogTypeEl.setAttribute('property', 'og:type');
        document.head.appendChild(ogTypeEl);
    }
    ogTypeEl.content = props.ogType || 'website';
}

// Update on mount and when props change
onMounted(() => {
    updateMeta();
});

watch(() => [props.title, props.description, props.robots, props.canonical, props.ogImage], () => {
    updateMeta();
}, { deep: true });
</script>

<template>
    <!-- HeadManager handles meta tags via JavaScript -->
    <!-- No visual output -->
</template>
