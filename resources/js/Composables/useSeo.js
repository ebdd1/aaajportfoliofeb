import { usePage } from '@inertiajs/vue3';

/**
 * Composable for managing SEO meta tags
 */
export function useSeo() {
    const page = usePage();

    const seo = computed(() => page.props.seo || {});

    /**
     * Set page title
     */
    function setTitle(title) {
        page.props.pageTitle = title;
    }

    /**
     * Get SEO-aware page title
     */
    function getPageTitle(pageTitle) {
        if (pageTitle) {
            return pageTitle;
        }
        return seo.value.seo_meta_title || 'Portfolio Febryanus';
    }

    /**
     * Get meta description
     */
    function getMetaDescription(customDescription) {
        return customDescription || seo.value.seo_meta_description || '';
    }

    /**
     * Get robots content
     */
    function getRobots(customRobots) {
        return customRobots || seo.value.seo_robots || 'index, follow';
    }

    /**
     * Get canonical URL
     */
    function getCanonical(customPath) {
        const baseUrl = seo.value.seo_canonical_url || window.location.origin;
        return customPath ? `${baseUrl}${customPath}` : baseUrl;
    }

    /**
     * Get OG image URL
     */
    function getOgImage(customImage) {
        return customImage || seo.value.og_image_url || null;
    }

    return {
        seo,
        setTitle,
        getPageTitle,
        getMetaDescription,
        getRobots,
        getCanonical,
        getOgImage,
    };
}
