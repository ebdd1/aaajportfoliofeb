<script setup>
import { ref } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import HeroOpacitySlider from '@/Components/HeroOpacitySlider.vue';
import HeroPreview from '@/Components/HeroPreview.vue';
import { useConfirm } from '@/Composables/useConfirm';

const props = defineProps({
    settings: Object,
});

const activeTab = ref('umum');
const { open: confirmOpen } = useConfirm();

const tabs = [
    { id: 'umum', label: 'Umum', icon: '<svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/></svg>' },
    { id: 'media', label: 'Media', icon: '<svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="3"/></svg>' },
    { id: 'hero', label: 'Hero', icon: '<svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>' },
    { id: 'seo', label: 'SEO', icon: '<svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>' },
    { id: 'pembayaran', label: 'Bayar', icon: '<svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>' },
    { id: 'lanjutan', label: 'Lanjutan', icon: '<svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/></svg>' },
];

const form = useForm({
    site_name: props.settings?.site_name ?? '',
    site_description: props.settings?.site_description ?? '',
    google_analytics_id: props.settings?.google_analytics_id ?? '',
    maintenance_mode: props.settings?.maintenance_mode ?? false,
});

const paymentForm = useForm({
    pakasir_project: props.settings?.pakasir_project ?? '',
    pakasir_api_key: props.settings?.pakasir_api_key ?? '',
    pakasir_webhook_secret: props.settings?.pakasir_webhook_secret ?? '',
    pakasir_active: props.settings?.pakasir_active ?? false,
});

const showApiKey = ref(false);
const showWebhookSecret = ref(false);

const faviconForm = useForm({ favicon: null });
const ogForm = useForm({ og_image: null });

const seoForm = useForm({
    seo_meta_title: props.settings?.seo_meta_title ?? '',
    seo_meta_description: props.settings?.seo_meta_description ?? '',
    seo_canonical_url: props.settings?.seo_canonical_url ?? '',
    seo_robots: props.settings?.seo_robots ?? 'index, follow',
    seo_sitemap_include: props.settings?.seo_sitemap_include ?? true,
    google_analytics_id: props.settings?.google_analytics_id ?? '',
});

const heroForm = useForm({
    hero_background: null,
    hero_opacity: props.settings?.hero_opacity ?? 30,
    hero_title_font: props.settings?.hero_title_font ?? 'font-serif',
    hero_title_color: props.settings?.hero_title_color ?? '#3D3929',
    hero_title_size: props.settings?.hero_title_size ?? 'text-6xl',
    hero_subtitle_font: props.settings?.hero_subtitle_font ?? 'font-sans',
    hero_subtitle_color: props.settings?.hero_subtitle_color ?? '#8C8273',
    hero_subtitle_size: props.settings?.hero_subtitle_size ?? 'text-2xl',
    hero_stat_value_font: props.settings?.hero_stat_value_font ?? 'font-mono',
    hero_stat_value_color: props.settings?.hero_stat_value_color ?? '#3D3929',
    hero_stat_label_font: props.settings?.hero_stat_label_font ?? 'font-sans',
    hero_stat_label_color: props.settings?.hero_stat_label_color ?? '#8C8273',
    hero_stat_card_bg_color: props.settings?.hero_stat_card_bg_color ?? '#F5F1E8',
    hero_stat_card_opacity: props.settings?.hero_stat_card_opacity ?? 100,
    hero_stat_card_border: props.settings?.hero_stat_card_border ?? false,
    hero_stat_card_border_color: props.settings?.hero_stat_card_border_color ?? '#E8E2D3',
    hero_stat_card_backdrop_blur: props.settings?.hero_stat_card_backdrop_blur ?? false,
});

const fontOptions = [
    { value: 'font-serif', label: 'Serif (Fraunces)' },
    { value: 'font-sans', label: 'Sans (Inter)' },
    { value: 'font-mono', label: 'Monospace' },
];

const sizeOptions = [
    { value: 'text-3xl', label: '3XL (Kecil)' },
    { value: 'text-4xl', label: '4XL' },
    { value: 'text-5xl', label: '5XL' },
    { value: 'text-6xl', label: '6XL (Default Judul)' },
    { value: 'text-7xl', label: '7XL' },
    { value: 'text-8xl', label: '8XL (Besar)' },
];

const subtitleSizeOptions = [
    { value: 'text-lg', label: 'Large' },
    { value: 'text-xl', label: 'XL' },
    { value: 'text-2xl', label: '2XL (Default Subjudul)' },
    { value: 'text-3xl', label: '3XL' },
];
const heroOpacity = ref(props.settings?.hero_opacity ?? 30);
const heroBackgroundUrl = ref(props.settings?.hero_background_url ?? null);

const submit = () => form.patch(route('admin.settings.web.update'));

const submitPayment = () => {
    paymentForm.post(route('admin.settings.web.payment'), { preserveScroll: true });
};

const submitFavicon = () => {
    faviconForm.post(route('admin.settings.web.favicon'), { onSuccess: () => faviconForm.reset() });
};

const submitOgImage = () => {
    ogForm.post(route('admin.settings.web.og-image'), { onSuccess: () => ogForm.reset() });
};

const submitSeo = () => {
    seoForm.patch(route('admin.settings.web.seo'), { preserveScroll: true });
};

const submitHero = () => {
    heroForm.hero_opacity = heroOpacity.value;
    heroForm.post(route('admin.settings.web.hero'), {
        preserveScroll: true,
        onSuccess: () => {
            heroForm.reset('hero_background');
        },
    });
};

const deleteHeroBackground = async () => {
    const confirmed = await confirmOpen({
        message: 'Hapus background hero? Gambar akan dihapus permanen.',
        variant: 'danger',
        confirmText: 'Hapus',
        cancelText: 'Batal',
    });

    if (confirmed) {
        router.delete(route('admin.settings.web.hero.destroy'), {
            onSuccess: () => { heroBackgroundUrl.value = null; },
        });
    }
};
</script>

<template>
    <AdminLayout>
        <div class="max-w-5xl mx-auto">
            <div class="mb-6">
                <h1 class="font-serif text-2xl font-bold text-ink">Pengaturan Web</h1>
                <p class="text-taupe text-sm mt-1">Kelola konfigurasi situs portfolio kamu.</p>
            </div>

            <div v-if="$page.props.flash?.success" class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm">
                {{ $page.props.flash.success }}
            </div>

            <div class="flex gap-0 bg-paper rounded-2xl border border-oat-dark overflow-hidden min-h-[480px]">
                <nav class="w-52 shrink-0 border-r border-oat-dark py-4 flex flex-col gap-0.5 px-2">
                    <button v-for="tab in tabs" :key="tab.id" @click="activeTab = tab.id"
                        :class="['w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 text-left',
                            activeTab === tab.id ? 'bg-terracotta text-cream' : 'text-taupe hover:bg-oat hover:text-ink']">
                        <span v-html="tab.icon" class="shrink-0" />
                        {{ tab.label }}
                    </button>
                </nav>

                <div class="flex-1 p-6 overflow-y-auto">
                    <!-- Tab: Umum -->
                    <div v-show="activeTab === 'umum'" class="space-y-5">
                        <h2 class="font-medium text-ink mb-4">Informasi Umum</h2>
                        <div>
                            <label class="block text-sm font-medium text-ink mb-1">Nama situs</label>
                            <input v-model="form.site_name" type="text"
                                class="w-full px-4 py-2.5 rounded-xl border border-oat-dark bg-cream focus:outline-none focus:border-terracotta transition-colors text-sm"
                                placeholder="Portfolio Febryanus" />
                        </div>
                        <div class="flex justify-end pt-2">
                            <button @click="submit" :disabled="form.processing"
                                class="px-6 py-2.5 bg-terracotta text-cream rounded-xl font-medium text-sm hover:bg-terracotta/90 transition-colors disabled:opacity-50">
                                {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
                            </button>
                        </div>
                    </div>

                    <!-- Tab: Media -->
                    <div v-show="activeTab === 'media'" class="space-y-6">
                        <div>
                            <h2 class="font-medium text-ink mb-1">Media Situs</h2>
                            <p class="text-xs text-taupe">Favicon dan gambar Open Graph untuk media sosial.</p>
                        </div>

                        <!-- Favicon -->
                        <div class="flex items-center gap-4 p-4 bg-oat rounded-xl border border-oat-dark">
                            <div class="w-14 h-14 rounded-xl bg-paper border border-oat-dark flex items-center justify-center overflow-hidden shrink-0">
                                <img v-if="settings?.favicon_url" :src="settings.favicon_url" alt="Favicon" class="w-10 h-10 object-contain" />
                                <span v-else class="text-taupe text-xs">None</span>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-ink mb-1">Favicon</p>
                                <p class="text-[10px] text-taupe mb-2">Format: PNG, JPG, WebP. Maksimal 512KB.</p>
                                <input type="file" accept="image/png,image/jpeg,image/webp"
                                    class="block w-full text-sm text-taupe file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-paper file:text-ink hover:file:bg-oat-dark transition-colors"
                                    @change="faviconForm.favicon = $event.target.files[0]" />
                            </div>
                            <button @click="submitFavicon" :disabled="faviconForm.processing || !faviconForm.favicon"
                                class="px-4 py-2 bg-terracotta text-cream rounded-xl text-xs font-medium hover:bg-terracotta/90 transition-colors disabled:opacity-40 shrink-0">
                                Upload
                            </button>
                        </div>

                        <!-- OG Image -->
                        <div class="space-y-3">
                            <div class="flex items-center gap-4 p-4 bg-oat rounded-xl border border-oat-dark">
                                <div class="w-28 h-16 rounded-xl bg-paper border border-oat-dark flex items-center justify-center overflow-hidden shrink-0">
                                    <img v-if="settings?.og_image_url" :src="settings.og_image_url" alt="OG Image" class="w-full h-full object-cover" />
                                    <span v-else class="text-taupe text-[10px]">None</span>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-ink mb-1">Open Graph Image</p>
                                    <p class="text-[10px] text-taupe mb-2">Gambar untuk preview saat link di-share ke media sosial.</p>
                                    <p class="text-[10px] text-taupe mb-2">Rekomendasi: 1200×630px. Format: PNG, JPG, WebP. Maksimal 2MB.</p>
                                    <input type="file" accept="image/png,image/jpeg,image/webp"
                                        class="block w-full text-sm text-taupe file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-paper file:text-ink hover:file:bg-oat-dark transition-colors"
                                        @change="ogForm.og_image = $event.target.files[0]" />
                                </div>
                                <button @click="submitOgImage" :disabled="ogForm.processing || !ogForm.og_image"
                                    class="px-4 py-2 bg-terracotta text-cream rounded-xl text-xs font-medium hover:bg-terracotta/90 transition-colors disabled:opacity-40 shrink-0">
                                    Upload
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Tab: Hero -->
                    <div v-show="activeTab === 'hero'" class="space-y-6">
                        <div>
                            <h2 class="font-medium text-ink mb-1">Hero Section</h2>
                            <p class="text-xs text-taupe">Kustomisasi background, tipografi, dan transparansi hero section.</p>
                        </div>

                        <!-- Tipografi Judul -->
                        <div class="p-5 bg-oat rounded-2xl space-y-4 border border-oat-dark">
                            <h3 class="text-sm font-semibold text-ink border-b border-oat-dark pb-2">Teks Judul (Nama)</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-ink mb-1">Font Family</label>
                                    <select v-model="heroForm.hero_title_font" class="w-full text-sm px-3 py-2 rounded-lg border border-oat-dark bg-paper focus:border-terracotta focus:ring-0">
                                        <option v-for="opt in fontOptions" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-ink mb-1">Ukuran Font</label>
                                    <select v-model="heroForm.hero_title_size" class="w-full text-sm px-3 py-2 rounded-lg border border-oat-dark bg-paper focus:border-terracotta focus:ring-0">
                                        <option v-for="opt in sizeOptions" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-ink mb-1">Warna Font</label>
                                    <div class="flex gap-2">
                                        <input v-model="heroForm.hero_title_color" type="color" class="h-9 w-12 rounded cursor-pointer border border-oat-dark bg-paper p-0.5" />
                                        <input v-model="heroForm.hero_title_color" type="text" class="flex-1 text-sm px-3 py-2 rounded-lg border border-oat-dark bg-paper font-mono uppercase" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tipografi Subjudul -->
                        <div class="p-5 bg-oat rounded-2xl space-y-4 border border-oat-dark">
                            <h3 class="text-sm font-semibold text-ink border-b border-oat-dark pb-2">Teks Subjudul (Tagline)</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-ink mb-1">Font Family</label>
                                    <select v-model="heroForm.hero_subtitle_font" class="w-full text-sm px-3 py-2 rounded-lg border border-oat-dark bg-paper focus:border-terracotta focus:ring-0">
                                        <option v-for="opt in fontOptions" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-ink mb-1">Ukuran Font</label>
                                    <select v-model="heroForm.hero_subtitle_size" class="w-full text-sm px-3 py-2 rounded-lg border border-oat-dark bg-paper focus:border-terracotta focus:ring-0">
                                        <option v-for="opt in subtitleSizeOptions" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-ink mb-1">Warna Font</label>
                                    <div class="flex gap-2">
                                        <input v-model="heroForm.hero_subtitle_color" type="color" class="h-9 w-12 rounded cursor-pointer border border-oat-dark bg-paper p-0.5" />
                                        <input v-model="heroForm.hero_subtitle_color" type="text" class="flex-1 text-sm px-3 py-2 rounded-lg border border-oat-dark bg-paper font-mono uppercase" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tipografi Stats/Angka (3 Proyek dll) -->
                        <div class="p-5 bg-oat rounded-2xl space-y-4 border border-oat-dark">
                            <h3 class="text-sm font-semibold text-ink border-b border-oat-dark pb-2">Teks Statistik Hero (Angka & Label)</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Value (Angka) -->
                                <div class="space-y-3">
                                    <p class="text-xs font-semibold text-taupe">Angka (Value)</p>
                                    <div>
                                        <label class="block text-xs font-medium text-ink mb-1">Font Family</label>
                                        <select v-model="heroForm.hero_stat_value_font" class="w-full text-sm px-3 py-2 rounded-lg border border-oat-dark bg-paper focus:border-terracotta focus:ring-0">
                                            <option v-for="opt in fontOptions" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-ink mb-1">Warna Font</label>
                                        <div class="flex gap-2">
                                            <input v-model="heroForm.hero_stat_value_color" type="color" class="h-9 w-12 rounded cursor-pointer border border-oat-dark bg-paper p-0.5" />
                                            <input v-model="heroForm.hero_stat_value_color" type="text" class="flex-1 text-sm px-3 py-2 rounded-lg border border-oat-dark bg-paper font-mono uppercase" />
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Label (Teks) -->
                                <div class="space-y-3">
                                    <p class="text-xs font-semibold text-taupe">Teks (Label)</p>
                                    <div>
                                        <label class="block text-xs font-medium text-ink mb-1">Font Family</label>
                                        <select v-model="heroForm.hero_stat_label_font" class="w-full text-sm px-3 py-2 rounded-lg border border-oat-dark bg-paper focus:border-terracotta focus:ring-0">
                                            <option v-for="opt in fontOptions" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-ink mb-1">Warna Font</label>
                                        <div class="flex gap-2">
                                            <input v-model="heroForm.hero_stat_label_color" type="color" class="h-9 w-12 rounded cursor-pointer border border-oat-dark bg-paper p-0.5" />
                                            <input v-model="heroForm.hero_stat_label_color" type="text" class="flex-1 text-sm px-3 py-2 rounded-lg border border-oat-dark bg-paper font-mono uppercase" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tipografi Stats/Card Style -->
                        <div class="p-5 bg-oat rounded-2xl space-y-4 border border-oat-dark">
                            <h3 class="text-sm font-semibold text-ink border-b border-oat-dark pb-2">Style Kotak Statistik (Cards)</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Background Color & Opacity -->
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-xs font-medium text-ink mb-1">Warna Background Card</label>
                                        <div class="flex gap-2">
                                            <input v-model="heroForm.hero_stat_card_bg_color" type="color" class="h-9 w-12 rounded cursor-pointer border border-oat-dark bg-paper p-0.5" />
                                            <input v-model="heroForm.hero_stat_card_bg_color" type="text" class="flex-1 text-sm px-3 py-2 rounded-lg border border-oat-dark bg-paper font-mono uppercase" />
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <div class="flex justify-between items-center mb-1">
                                            <label class="text-xs font-medium text-ink">Transparansi Background (Opacity)</label>
                                            <span class="text-xs text-taupe font-mono">{{ heroForm.hero_stat_card_opacity }}%</span>
                                        </div>
                                        <input v-model="heroForm.hero_stat_card_opacity" type="range" min="0" max="100" step="5" class="w-full accent-terracotta" />
                                        <p class="text-[10px] text-taupe mt-1">0% = Hilang total (Transparan) | 100% = Solid</p>
                                    </div>
                                </div>
                                
                                <!-- Border & Blur -->
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between p-3 bg-paper border border-oat-dark rounded-xl">
                                        <div>
                                            <p class="text-sm font-medium text-ink">Efek Kaca (Glass Blur)</p>
                                            <p class="text-[10px] text-taupe">Bagus jika background agak transparan.</p>
                                        </div>
                                        <button type="button" @click="heroForm.hero_stat_card_backdrop_blur = !heroForm.hero_stat_card_backdrop_blur"
                                            :class="['relative inline-flex h-5 w-9 items-center rounded-full transition-colors',
                                                heroForm.hero_stat_card_backdrop_blur ? 'bg-terracotta' : 'bg-oat-dark']">
                                            <span :class="['inline-block h-3 w-3 transform rounded-full bg-white transition-transform',
                                                heroForm.hero_stat_card_backdrop_blur ? 'translate-x-5' : 'translate-x-1']" />
                                        </button>
                                    </div>
                                    
                                    <div class="p-3 bg-paper border border-oat-dark rounded-xl space-y-3">
                                        <div class="flex items-center justify-between">
                                            <p class="text-sm font-medium text-ink">Tampilkan Border</p>
                                            <button type="button" @click="heroForm.hero_stat_card_border = !heroForm.hero_stat_card_border"
                                                :class="['relative inline-flex h-5 w-9 items-center rounded-full transition-colors',
                                                    heroForm.hero_stat_card_border ? 'bg-terracotta' : 'bg-oat-dark']">
                                                <span :class="['inline-block h-3 w-3 transform rounded-full bg-white transition-transform',
                                                    heroForm.hero_stat_card_border ? 'translate-x-5' : 'translate-x-1']" />
                                            </button>
                                        </div>
                                        
                                        <div v-if="heroForm.hero_stat_card_border">
                                            <label class="block text-[10px] font-medium text-taupe mb-1 uppercase tracking-wider">Warna Border</label>
                                            <div class="flex gap-2">
                                                <input v-model="heroForm.hero_stat_card_border_color" type="color" class="h-8 w-10 rounded cursor-pointer border border-oat-dark p-0.5" />
                                                <input v-model="heroForm.hero_stat_card_border_color" type="text" class="flex-1 text-xs px-2 py-1.5 rounded-md border border-oat-dark font-mono uppercase" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Background Section -->
                        <div class="space-y-4">
                            <p class="text-sm font-medium text-ink">Background Image</p>
                            <div v-if="heroBackgroundUrl" class="rounded-3xl overflow-hidden border border-ash-border shadow-elevated">
                                <img :src="heroBackgroundUrl" alt="Hero Background" class="w-full h-48 object-cover" />
                            </div>
                            <div class="flex items-center gap-4 p-4 bg-warm-sand rounded-xl">
                                <input type="file" accept="image/jpeg,image/png,image/webp"
                                    class="flex-1 text-sm text-driftwood file:mr-3 file:py-2 file:px-4 file:rounded-full file:border file:border-ash-border file:text-xs file:font-medium file:bg-parchment-white file:text-midnight-ink hover:file:bg-white transition-colors"
                                    @change="heroForm.hero_background = $event.target.files[0]" />
                                <button @click="submitHero" :disabled="heroForm.processing"
                                    class="px-5 py-2.5 bg-midnight-ink text-cream rounded-full text-sm font-medium hover:bg-ink/90 transition-colors disabled:opacity-40">
                                    Upload
                                </button>
                            </div>
                            <button v-if="heroBackgroundUrl" @click="deleteHeroBackground" class="text-sm text-driftwood hover:text-red-500 transition-colors">
                                Remove Background
                            </button>
                        </div>

                        <div class="p-5 bg-warm-sand rounded-3xl">
                            <HeroOpacitySlider v-model="heroOpacity" />
                        </div>

                        <HeroPreview :backgroundUrl="heroBackgroundUrl" :opacity="heroOpacity" />

                        <div class="flex justify-end">
                            <button @click="submitHero" :disabled="heroForm.processing"
                                class="px-6 py-3 bg-midnight-ink text-cream rounded-full font-medium text-sm hover:bg-ink/90 transition-colors disabled:opacity-50">
                                Simpan Pengaturan
                            </button>
                        </div>
                    </div>

                    <!-- Tab: SEO -->
                    <div v-show="activeTab === 'seo'" class="space-y-6">
                        <div>
                            <h2 class="font-medium text-ink mb-1">SEO & Metadata</h2>
                            <p class="text-xs text-taupe">Optimasi untuk mesin pencari dan media sosial.</p>
                        </div>

                        <!-- Meta Tags -->
                        <div class="p-5 bg-oat rounded-2xl space-y-4 border border-oat-dark">
                            <h3 class="text-sm font-semibold text-ink border-b border-oat-dark pb-2">Meta Tags</h3>

                            <div>
                                <label class="block text-xs font-medium text-ink mb-1">Meta Title</label>
                                <input v-model="seoForm.seo_meta_title" type="text"
                                    class="w-full px-4 py-2.5 rounded-xl border border-oat-dark bg-paper focus:outline-none focus:border-terracotta transition-colors text-sm"
                                    placeholder="Portfolio Febryanus - Full Stack Developer" />
                                <p class="text-[10px] text-taupe mt-1">Idealnya 50-60 karakter. {{ seoForm.seo_meta_title?.length || 0 }} karakter</p>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-ink mb-1">Meta Description</label>
                                <textarea v-model="seoForm.seo_meta_description" rows="3"
                                    class="w-full px-4 py-2.5 rounded-xl border border-oat-dark bg-paper focus:outline-none focus:border-terracotta transition-colors text-sm resize-none"
                                    placeholder="Deskripsi singkat tentang portfolio kamu untuk ditampilkan di hasil pencarian..."></textarea>
                                <p class="text-[10px] text-taupe mt-1">Idealnya 150-160 karakter. {{ seoForm.seo_meta_description?.length || 0 }} karakter</p>
                            </div>
                        </div>

                        <!-- Robots & Sitemap -->
                        <div class="p-5 bg-oat rounded-2xl space-y-4 border border-oat-dark">
                            <h3 class="text-sm font-semibold text-ink border-b border-oat-dark pb-2">Indexing & Sitemap</h3>

                            <div>
                                <label class="block text-xs font-medium text-ink mb-1">Robots Directive</label>
                                <select v-model="seoForm.seo_robots" class="w-full text-sm px-3 py-2.5 rounded-xl border border-oat-dark bg-paper focus:outline-none focus:border-terracotta transition-colors">
                                    <option value="index, follow">Index, Follow (Default)</option>
                                    <option value="index, nofollow">Index, NoFollow</option>
                                    <option value="noindex, follow">NoIndex, Follow</option>
                                    <option value="noindex, nofollow">NoIndex, NoFollow</option>
                                </select>
                                <p class="text-[10px] text-taupe mt-1">Instruksi untuk robot mesin pencari.</p>
                            </div>

                            <div class="flex items-center justify-between p-4 bg-paper border border-oat-dark rounded-xl">
                                <div>
                                    <p class="text-sm font-medium text-ink">Include in Sitemap</p>
                                    <p class="text-[10px] text-taupe mt-0.5">Tampilkan halaman di XML sitemap.</p>
                                </div>
                                <button type="button" @click="seoForm.seo_sitemap_include = !seoForm.seo_sitemap_include"
                                    :class="['relative inline-flex h-6 w-11 items-center rounded-full transition-colors duration-200',
                                        seoForm.seo_sitemap_include ? 'bg-terracotta' : 'bg-oat-dark']">
                                    <span :class="['inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform duration-200',
                                        seoForm.seo_sitemap_include ? 'translate-x-6' : 'translate-x-1']" />
                                </button>
                            </div>
                        </div>

                        <!-- Canonical URL -->
                        <div class="p-5 bg-oat rounded-2xl space-y-4 border border-oat-dark">
                            <h3 class="text-sm font-semibold text-ink border-b border-oat-dark pb-2">Canonical URL</h3>

                            <div>
                                <label class="block text-xs font-medium text-ink mb-1">Canonical URL</label>
                                <input v-model="seoForm.seo_canonical_url" type="url"
                                    class="w-full px-4 py-2.5 rounded-xl border border-oat-dark bg-paper focus:outline-none focus:border-terracotta transition-colors text-sm font-mono"
                                    placeholder="https://febryanmyid.up.railway.app" />
                                <p class="text-[10px] text-taupe mt-1">URL utama untuk menghindari konten duplikat.</p>
                            </div>
                        </div>

                        <!-- Google Analytics -->
                        <div class="p-5 bg-oat rounded-2xl space-y-4 border border-oat-dark">
                            <h3 class="text-sm font-semibold text-ink border-b border-oat-dark pb-2">Analytics</h3>

                            <div>
                                <label class="block text-xs font-medium text-ink mb-1">Google Analytics ID</label>
                                <input v-model="seoForm.google_analytics_id" type="text"
                                    class="w-full px-4 py-2.5 rounded-xl border border-oat-dark bg-paper focus:outline-none focus:border-terracotta transition-colors text-sm font-mono"
                                    placeholder="G-XXXXXXXXXX" />
                                <p class="text-[10px] text-taupe mt-1">Tracking ID untuk Google Analytics 4.</p>
                            </div>
                        </div>

                        <div class="flex justify-end pt-2">
                            <button @click="submitSeo" :disabled="seoForm.processing"
                                class="px-6 py-2.5 bg-terracotta text-cream rounded-xl font-medium text-sm hover:bg-terracotta/90 transition-colors disabled:opacity-50">
                                {{ seoForm.processing ? 'Menyimpan...' : 'Simpan SEO' }}
                            </button>
                        </div>
                    </div>

                    <!-- Tab: Pembayaran -->
                    <div v-show="activeTab === 'pembayaran'" class="space-y-6">
                        <div>
                            <h2 class="font-medium text-ink mb-1">Pengaturan Pembayaran</h2>
                            <p class="text-xs text-taupe">Konfigurasi gateway pembayaran Pakasir.</p>
                        </div>

                        <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-700">
                            <strong>Info:</strong> Pakasir adalah payment gateway untuk Indonesia. Dapatkan credentials di dashboard Pakasir.
                        </div>

                        <!-- Pakasir Project ID -->
                        <div class="p-5 bg-oat rounded-2xl space-y-4 border border-oat-dark">
                            <h3 class="text-sm font-semibold text-ink border-b border-oat-dark pb-2">Project Configuration</h3>

                            <div>
                                <label class="block text-xs font-medium text-ink mb-1">Project ID / Merchant ID</label>
                                <input v-model="paymentForm.pakasir_project" type="text"
                                    class="w-full px-4 py-2.5 rounded-xl border border-oat-dark bg-paper focus:outline-none focus:border-terracotta transition-colors text-sm"
                                    placeholder="project_xxxxxx" />
                                <p class="text-[10px] text-taupe mt-1">ID project dari dashboard Pakasir.</p>
                            </div>
                        </div>

                        <!-- API Credentials -->
                        <div class="p-5 bg-oat rounded-2xl space-y-4 border border-oat-dark">
                            <h3 class="text-sm font-semibold text-ink border-b border-oat-dark pb-2">API Credentials</h3>

                            <div>
                                <label class="block text-xs font-medium text-ink mb-1">API Key</label>
                                <div class="relative">
                                    <input v-model="paymentForm.pakasir_api_key" :type="showApiKey ? 'text' : 'password'"
                                        class="w-full px-4 py-2.5 rounded-xl border border-oat-dark bg-paper focus:outline-none focus:border-terracotta transition-colors text-sm font-mono pr-10"
                                        placeholder="pak_live_xxxxxx" />
                                    <button type="button" @click="showApiKey = !showApiKey"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-taupe hover:text-ink">
                                        <svg v-if="showApiKey" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                                        <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </button>
                                </div>
                                <p class="text-[10px] text-taupe mt-1">API key dari dashboard Pakasir.</p>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-ink mb-1">Webhook Secret</label>
                                <div class="relative">
                                    <input v-model="paymentForm.pakasir_webhook_secret" :type="showWebhookSecret ? 'text' : 'password'"
                                        class="w-full px-4 py-2.5 rounded-xl border border-oat-dark bg-paper focus:outline-none focus:border-terracotta transition-colors text-sm font-mono pr-10"
                                        placeholder="whsec_xxxxxx" />
                                    <button type="button" @click="showWebhookSecret = !showWebhookSecret"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-taupe hover:text-ink">
                                        <svg v-if="showWebhookSecret" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                                        <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </button>
                                </div>
                                <p class="text-[10px] text-taupe mt-1">Secret untuk verify webhook signature.</p>
                            </div>
                        </div>

                        <!-- Active Toggle -->
                        <div class="flex items-center justify-between p-4 bg-oat rounded-xl border border-oat-dark">
                            <div>
                                <p class="text-sm font-medium text-ink">Aktifkan Pembayaran</p>
                                <p class="text-[10px] text-taupe mt-0.5">Aktifkan agar customer bisa melakukan pembayaran.</p>
                            </div>
                            <button type="button" @click="paymentForm.pakasir_active = !paymentForm.pakasir_active"
                                :class="['relative inline-flex h-6 w-11 items-center rounded-full transition-colors duration-200',
                                    paymentForm.pakasir_active ? 'bg-terracotta' : 'bg-oat-dark']">
                                <span :class="['inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform duration-200',
                                    paymentForm.pakasir_active ? 'translate-x-6' : 'translate-x-1']" />
                            </button>
                        </div>

                        <div class="flex justify-end pt-2">
                            <button @click="submitPayment" :disabled="paymentForm.processing"
                                class="px-6 py-2.5 bg-terracotta text-cream rounded-xl font-medium text-sm hover:bg-terracotta/90 transition-colors disabled:opacity-50">
                                {{ paymentForm.processing ? 'Menyimpan...' : 'Simpan' }}
                            </button>
                        </div>
                    </div>

                    <!-- Tab: Lanjutan -->
                    <div v-show="activeTab === 'lanjutan'" class="space-y-5">
                        <h2 class="font-medium text-ink">Pengaturan Lanjutan</h2>
                        <div class="flex items-center justify-between p-4 bg-oat rounded-xl">
                            <div>
                                <p class="text-sm font-medium text-ink">Mode Maintenance</p>
                                <p class="text-xs text-taupe mt-0.5">Sembunyikan portfolio dari publik.</p>
                            </div>
                            <button type="button" @click="form.maintenance_mode = !form.maintenance_mode"
                                :class="['relative inline-flex h-6 w-11 items-center rounded-full transition-colors duration-200',
                                    form.maintenance_mode ? 'bg-terracotta' : 'bg-oat-dark']">
                                <span :class="['inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform duration-200',
                                    form.maintenance_mode ? 'translate-x-6' : 'translate-x-1']" />
                            </button>
                        </div>
                        <div class="flex justify-end pt-2">
                            <button @click="submit" :disabled="form.processing"
                                class="px-6 py-2.5 bg-terracotta text-cream rounded-xl font-medium text-sm hover:bg-terracotta/90 transition-colors disabled:opacity-50">
                                Simpan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
