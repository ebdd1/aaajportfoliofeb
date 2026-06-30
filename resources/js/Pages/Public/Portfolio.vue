<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { computed, onMounted } from 'vue';
import Navigation from '@/Components/Navigation.vue';
import Footer from '@/Components/Footer.vue';

const props = defineProps({
  profile: Object,
  stats: Object,
  skills: Array,
  experiences: Array,
  projects: Array,
  certificates: Array,
  socialLinks: Array,
  hasActiveCv: Boolean,
  settings: Object,
  latestPosts: Array,
});

// Helper untuk mengubah hex color + opacity jadi rgba string
const getCardBgStyle = () => {
  const hex = props.settings?.hero_stat_card_bg_color || '#F5F1E8';
  const opacity = props.settings?.hero_stat_card_opacity ?? 100;
  
  if (opacity === 100) return hex;
  
  // Jika opacity 0, background transparan
  if (opacity === 0) return 'transparent';
  
  // Convert hex to rgb
  let r = 0, g = 0, b = 0;
  if (hex.length === 4) {
    r = parseInt(hex[1] + hex[1], 16);
    g = parseInt(hex[2] + hex[2], 16);
    b = parseInt(hex[3] + hex[3], 16);
  } else if (hex.length === 7) {
    r = parseInt(hex.substring(1, 3), 16);
    g = parseInt(hex.substring(3, 5), 16);
    b = parseInt(hex.substring(5, 7), 16);
  }
  
  return `rgba(${r}, ${g}, ${b}, ${opacity / 100})`;
};

const personSchema = computed(() => ({
  '@context': 'https://schema.org',
  '@type': 'Person',
  name: props.profile?.name,
  jobTitle: props.profile?.tagline,
  description: props.profile?.bio,
  email: props.profile?.email,
  url: window.location.origin,
  image: props.profile?.photo_url,
  sameAs: props.socialLinks?.map(link => link.url) || [],
  knowsAbout: props.skills?.flatMap(s => s.tags) || [],
  alumniOf: props.profile?.university ? {
    '@type': 'CollegeOrUniversity',
    name: props.profile.university
  } : undefined,
}));

// Split text into characters for animation
const splitTextIntoChars = (element) => {
  const text = element.textContent;
  element.textContent = '';
  // style parent container untuk memastikan rata kiri
  element.style.display = 'block';
  element.style.textAlign = 'left';

  // Pecah teks, tapi biarkan spasi sebagai text node biasa agar word-wrap bekerja normal
  text.split('').forEach((char, index) => {
    if (char === ' ') {
      element.appendChild(document.createTextNode(' '));
      return;
    }
    const span = document.createElement('span');
    span.textContent = char;
    span.style.display = 'inline-block';
    span.style.opacity = '0';
    span.style.transform = 'translateY(20px) scale(0.8)';
    span.style.transition = `opacity 0.6s cubic-bezier(0.16, 1, 0.3, 1) ${index * 50}ms, transform 0.6s cubic-bezier(0.16, 1, 0.3, 1) ${index * 50}ms`;
    element.appendChild(span);
  });
};

// Intersection Observer for reveal animation
onMounted(() => {
  // Split hero title into characters for split reveal animation
  const heroTitle = document.querySelector('.hero-title');
  if (heroTitle) {
    splitTextIntoChars(heroTitle);
  }

  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    document.querySelectorAll('.reveal').forEach((el) => el.classList.add('is-visible'));
    // Show all hero title chars immediately for reduced motion
    if (heroTitle) {
      heroTitle.querySelectorAll('span').forEach(span => {
        span.style.opacity = '1';
        span.style.transform = 'none';
      });
    }
    return;
  }

  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-visible');

          // Trigger hero title animation
          if (entry.target.classList.contains('hero-title')) {
            entry.target.querySelectorAll('span').forEach(span => {
              span.style.opacity = '1';
              span.style.transform = 'translateY(0) scale(1)';
            });
          }
        }
      });
    },
    { threshold: 0.1 }
  );

  document.querySelectorAll('.reveal').forEach((el) => observer.observe(el));
});
</script>

<template>
  <Head>
    <title>{{ profile.meta_title || profile.name }}</title>
    <meta name="description" :content="profile.meta_description" />
    <link rel="canonical" :href="route('home')" />
    <meta property="og:title" :content="profile.meta_title || profile.name" />
    <meta property="og:description" :content="profile.meta_description" />
    <meta property="og:image" :content="profile.photo_url || '/og-image.png'" />
    <meta property="og:url" :content="route('home')" />
    <meta property="og:type" content="website" />
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" :content="profile.meta_title || profile.name" />
    <meta name="twitter:description" :content="profile.meta_description" />
    <meta name="twitter:image" :content="profile.photo_url || '/og-image.png'" />
    <component :is="'script'" type="application/ld+json">{{ JSON.stringify(personSchema) }}</component>
  </Head>

  <div class="min-h-screen bg-parchment-white flex flex-col">
    <Navigation :profile="profile" variant="default" />

    <!-- ============================================
        HERO SECTION - ElevenLabs Style with Dynamic Background
    ============================================ -->
    <section id="hero" class="relative min-h-screen flex items-center pt-20 pb-12 overflow-hidden">
      <!-- Dynamic Background with Opacity -->
      <div v-if="settings?.hero_background_url" class="absolute inset-0 bg-cover bg-center" :style="{ backgroundImage: `url('${settings.hero_background_url}')` }">
        <div class="absolute inset-0 bg-parchment-white" :style="{ opacity: (100 - (settings?.hero_opacity ?? 30)) / 100 }"></div>
      </div>
      <div v-else class="absolute inset-0 bg-gradient-to-br from-parchment-white via-warm-sand/30 to-parchment-white"></div>

      <div class="container-padding mx-auto relative z-10 w-full">
        <!-- Badge -->
        <div class="reveal opacity-0 mb-6">
          <span class="inline-flex items-center gap-2 px-4 py-2 bg-warm-sand rounded-full border border-ash-border">
            <span class="w-2 h-2 bg-terracotta rounded-full"></span>
            <span class="font-mono text-xs text-driftwood">Available for Projects</span>
          </span>
        </div>

        <!-- Name & Title -->
        <div class="reveal opacity-0 mb-6 text-left" style="transition-delay: 100ms">
          <h1
            class="hero-title reveal text-left"
            :class="[
              settings?.hero_title_font || 'font-serif',
              settings?.hero_title_size || 'text-6xl lg:text-7xl'
            ]"
            :style="{ color: settings?.hero_title_color || '#3D3929' }"
            style="font-weight: 300; letter-spacing: -0.03em; line-height: 1.1; margin-bottom: 1rem;"
          >
            {{ profile.name }}
          </h1>
          <p 
            :class="[
              settings?.hero_subtitle_font || 'font-sans',
              settings?.hero_subtitle_size || 'text-2xl'
            ]"
            :style="{ color: settings?.hero_subtitle_color || '#8C8273' }"
            class="max-w-xl tracking-wide sm:text-xl md:text-2xl"
          >
            {{ profile.tagline }}
          </p>
        </div>

        <!-- Bio -->
        <p class="reveal opacity-0 text-left text-driftwood leading-relaxed mb-8 max-w-lg tracking-wide" style="transition-delay: 200ms">
          {{ profile.bio }}
        </p>

        <!-- CTA Buttons - ElevenLabs Pill Style -->
        <div class="reveal opacity-0 flex flex-col sm:flex-row gap-3 mb-10" style="transition-delay: 300ms">
          <a href="#proyek" class="group inline-flex items-center justify-center gap-2 px-6 py-3 bg-midnight-ink text-cream font-medium rounded-full border border-ash-border hover:bg-ink/90 active:scale-[0.98] transition-all duration-200">
            Lihat Proyek
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 transition-transform group-hover:translate-x-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>
            </svg>
          </a>
          <Link v-if="hasActiveCv" :href="route('cv.download')" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-transparent text-midnight-ink font-medium rounded-full border border-ash-border hover:bg-warm-sand transition-all duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>
            </svg>
            Download CV
          </Link>
        </div>

        <!-- Stats - Warm Sand Cards -->
        <div class="reveal opacity-0 grid grid-cols-3 gap-3 sm:gap-6" style="transition-delay: 400ms">
          <div
            class="rounded-3xl p-5 text-center transition-all duration-300"
            :class="{ 
              'backdrop-blur-md': settings?.hero_stat_card_backdrop_blur,
              'border': settings?.hero_stat_card_border
            }"
            :style="{ 
              backgroundColor: getCardBgStyle(),
              borderColor: settings?.hero_stat_card_border ? (settings?.hero_stat_card_border_color || '#E8E2D3') : 'transparent'
            }"
          >
            <p 
              :class="[settings?.hero_stat_value_font || 'font-mono']" 
              :style="{ color: settings?.hero_stat_value_color || '#3D3929' }"
              class="text-3xl sm:text-4xl font-medium"
            >{{ stats.projects_count }}</p>
            <p 
              :class="[settings?.hero_stat_label_font || 'font-sans']" 
              :style="{ color: settings?.hero_stat_label_color || '#8C8273' }"
              class="text-sm mt-2"
            >Proyek</p>
          </div>
          <div 
            class="rounded-3xl p-5 text-center transition-all duration-300"
            :class="{ 
              'backdrop-blur-md': settings?.hero_stat_card_backdrop_blur,
              'border': settings?.hero_stat_card_border
            }"
            :style="{ 
              backgroundColor: getCardBgStyle(),
              borderColor: settings?.hero_stat_card_border ? (settings?.hero_stat_card_border_color || '#E8E2D3') : 'transparent'
            }"
          >
            <p 
              :class="[settings?.hero_stat_value_font || 'font-mono']" 
              :style="{ color: settings?.hero_stat_value_color || '#3D3929' }"
              class="text-3xl sm:text-4xl font-medium"
            >{{ stats.semesters_count }}</p>
            <p 
              :class="[settings?.hero_stat_label_font || 'font-sans']" 
              :style="{ color: settings?.hero_stat_label_color || '#8C8273' }"
              class="text-sm mt-2"
            >Semester</p>
          </div>
          <div 
            class="rounded-3xl p-5 text-center transition-all duration-300"
            :class="{ 
              'backdrop-blur-md': settings?.hero_stat_card_backdrop_blur,
              'border': settings?.hero_stat_card_border
            }"
            :style="{ 
              backgroundColor: getCardBgStyle(),
              borderColor: settings?.hero_stat_card_border ? (settings?.hero_stat_card_border_color || '#E8E2D3') : 'transparent'
            }"
          >
            <p 
              :class="[settings?.hero_stat_value_font || 'font-mono']" 
              :style="{ color: settings?.hero_stat_value_color || '#3D3929' }"
              class="text-3xl sm:text-4xl font-medium"
            >{{ stats.experiences_count }}</p>
            <p 
              :class="[settings?.hero_stat_label_font || 'font-sans']" 
              :style="{ color: settings?.hero_stat_label_color || '#8C8273' }"
              class="text-sm mt-2"
            >Pengalaman</p>
          </div>
        </div>
      </div>
    </section>

    <!-- ============================================
        TENTANG SECTION - ElevenLabs Style
    ============================================ -->
    <section id="tentang" class="section-padding bg-warm-sand relative overflow-hidden">
      <div class="container-padding mx-auto relative z-10">
        <!-- Section Header -->
        <div class="reveal opacity-0 mb-10">
          <h2 class="font-serif text-3xl sm:text-4xl md:text-5xl font-light text-midnight-ink tracking-tight">
            Kenalan <span class="text-terracotta">Yuk</span>
          </h2>
        </div>

        <!-- Mobile: Photo First, then Info -->
        <div class="space-y-8 lg:grid lg:grid-cols-5 lg:gap-12 lg:items-center">
          <!-- Photo - White Elevated Card -->
          <div class="reveal opacity-0 lg:col-span-2">
            <div class="relative max-w-[180px] xs:max-w-[200px] sm:max-w-xs mx-auto lg:mx-0">
              <!-- Photo -->
              <div v-if="profile.photo_url" class="relative rounded-3xl overflow-hidden">
                <img :src="profile.photo_url" :alt="profile.name" class="w-full h-auto object-cover" />
              </div>


            </div>
          </div>

          <!-- Info Cards - Stack on Mobile -->
          <div class="reveal opacity-0 lg:col-span-3 space-y-4" style="transition-delay: 150ms">
            <!-- University Card -->
            <div class="group bg-white rounded-2xl p-5 transition-all duration-300 hover:bg-warm-sand/50">
              <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-warm-sand flex items-center justify-center shrink-0 group-hover:bg-terracotta/10 transition-colors">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-midnight-ink" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/>
                  </svg>
                </div>
                <div class="min-w-0">
                  <p class="text-xs text-driftwood uppercase tracking-wide mb-1">Universitas</p>
                  <p class="font-medium text-midnight-ink">{{ profile.university }}</p>
                </div>
              </div>
            </div>

            <!-- Semester Card -->
            <div class="group bg-white rounded-2xl p-5 transition-all duration-300 hover:bg-warm-sand/50">
              <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-warm-sand flex items-center justify-center shrink-0 group-hover:bg-terracotta/10 transition-colors">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-midnight-ink" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                  </svg>
                </div>
                <div>
                  <p class="text-xs text-driftwood uppercase tracking-wide mb-1">Semester</p>
                  <p class="font-medium text-midnight-ink">{{ profile.semester }}</p>
                </div>
              </div>
            </div>

            <!-- Email Card -->
            <div class="group bg-white rounded-2xl p-5 transition-all duration-300 hover:bg-warm-sand/50">
              <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-warm-sand flex items-center justify-center shrink-0 group-hover:bg-terracotta/10 transition-colors">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-midnight-ink" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/>
                  </svg>
                </div>
                <div class="min-w-0">
                  <p class="text-xs text-driftwood uppercase tracking-wide mb-1">Email</p>
                  <p class="font-medium text-midnight-ink text-sm truncate">{{ profile.email }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ============================================
        SKILLS SECTION - ElevenLabs Style
    ============================================ -->
    <section id="keahlian" class="section-padding bg-parchment-white">
      <div class="container-padding mx-auto">
        <!-- Section Header -->
        <div class="reveal opacity-0 text-center mb-12">
          <h2 class="font-serif text-3xl sm:text-4xl md:text-5xl font-light text-midnight-ink tracking-tight">Keahlian Saya</h2>
        </div>

        <!-- Skills Grid - Warm Sand Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
          <div v-for="(skill, index) in skills" :key="skill.id" class="reveal opacity-0" :style="`transition-delay: ${index * 80}ms`">
            <div class="group bg-warm-sand rounded-3xl p-6 h-full transition-all duration-300 hover:bg-warm-sand/80">
              <div class="flex items-start gap-4 mb-4">
                <div class="w-12 h-12 rounded-2xl bg-parchment-white flex items-center justify-center shrink-0">
                  <span class="font-mono text-lg font-medium text-midnight-ink">{{ skill.category_number }}</span>
                </div>
                <div class="flex-1 min-w-0">
                  <p class="text-xs text-terracotta uppercase tracking-wider mb-1">{{ skill.category_label }}</p>
                  <h3 class="font-serif text-lg font-medium text-midnight-ink leading-tight">{{ skill.category_title }}</h3>
                </div>
              </div>
              <div class="flex flex-wrap gap-2">
                <span v-for="tag in skill.tags" :key="tag" class="text-xs font-mono bg-parchment-white text-driftwood px-3 py-1.5 rounded-full border border-ash-border">{{ tag }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ============================================
        EXPERIENCE SECTION - ElevenLabs Style
    ============================================ -->
    <section id="pengalaman" class="section-padding bg-warm-sand">
      <div class="container-padding mx-auto">
        <!-- Section Header -->
        <div class="reveal opacity-0 text-center mb-12">
          <h2 class="font-serif text-3xl sm:text-4xl md:text-5xl font-light text-midnight-ink tracking-tight">Pengalaman</h2>
        </div>

        <!-- Timeline - Clean and Minimal -->
        <div class="max-w-2xl mx-auto space-y-5">
          <div v-for="(exp, index) in experiences" :key="exp.id" class="reveal opacity-0 relative" :style="`transition-delay: ${index * 100}ms`">
            <!-- Timeline Line -->
            <div v-if="index < experiences.length - 1" class="absolute left-5 top-11 bottom-0 w-px bg-ash-border"></div>

            <div class="flex gap-4">
              <!-- Timeline Dot -->
              <div class="w-10 h-10 rounded-full bg-terracotta flex items-center justify-center shrink-0">
                <div class="w-3 h-3 rounded-full bg-cream"></div>
              </div>

              <!-- Content Card -->
              <div class="flex-1 bg-white rounded-2xl p-5">
                <p class="font-mono text-xs text-driftwood mb-2">{{ exp.period }}</p>
                <h3 class="font-serif text-lg font-medium text-midnight-ink mb-1">{{ exp.role }}</h3>
                <p class="text-terracotta text-sm mb-2">{{ exp.organization }}</p>
                <p v-if="exp.description" class="text-driftwood text-sm leading-relaxed">{{ exp.description }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ============================================
        PROJECTS SECTION - ElevenLabs Style
    ============================================ -->
    <section id="proyek" class="section-padding bg-parchment-white">
      <div class="container-padding mx-auto">
        <!-- Section Header -->
        <div class="reveal opacity-0 text-center mb-12">
          <h2 class="font-serif text-3xl sm:text-4xl md:text-5xl font-light text-midnight-ink tracking-tight">Proyek Saya</h2>
        </div>

        <!-- Projects Grid - White Elevated Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
          <div v-for="(project, index) in projects" :key="project.id" class="reveal opacity-0" :style="`transition-delay: ${index * 80}ms`">
            <div class="group bg-white rounded-3xl overflow-hidden h-full flex flex-col shadow-elevated">
              <!-- Image -->
              <div class="relative aspect-video overflow-hidden">
                <img v-if="project.image_path" :src="project.image_url" :alt="project.title" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" loading="lazy" />
                <div v-else class="w-full h-full bg-warm-sand flex items-center justify-center">
                  <span class="font-mono text-6xl text-driftwood/30">{{ project.title.charAt(0) }}</span>
                </div>
                <!-- Featured Badge -->
                <span v-if="project.is_featured" class="absolute top-3 left-3 px-3 py-1 bg-midnight-ink text-cream text-xs font-mono rounded-full">
                  Featured
                </span>
              </div>

              <!-- Content -->
              <div class="p-5 flex-1 flex flex-col">
                <h3 class="font-serif text-lg font-medium text-midnight-ink mb-2 group-hover:text-terracotta transition-colors">{{ project.title }}</h3>
                <p class="text-driftwood text-sm mb-4 line-clamp-2 flex-1">{{ project.description }}</p>

                <!-- Tags -->
                <div class="flex flex-wrap gap-2 mb-4">
                  <span v-for="tag in project.tags" :key="tag" class="text-xs font-mono bg-warm-sand text-driftwood px-2.5 py-1 rounded-full">{{ tag }}</span>
                </div>

                <!-- Status -->
                <div class="flex items-center gap-2 pt-3 border-t border-ash-border">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-terracotta" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M15 22v-4a4.8 4.8 0 0 0-1-3.5c3 0 6-2 6-5.5.08-1.25-.27-2.48-1-3.5.28-1.15.28-2.35 0-3.5 0 0-1 0-3 1.5-2.64-.5-5.36-.5-8 0C6 2 5 2 5 2c-.3 1.15-.3 2.35 0 3.5A5.403 5.403 0 0 0 4 9c0 3.5 3 5.5 6 5.5-.39.49-.68 1.05-.85 1.65-.17.6-.22 1.23-.15 1.85v4"/>
                    <path d="M9 18c-4.51 2-5-2-7-2"/>
                  </svg>
                  <span class="font-mono text-xs text-terracotta">{{ project.repo_status_label }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ============================================
        CERTIFICATES SECTION - ElevenLabs Style
    ============================================ -->
    <section id="sertifikat" class="section-padding bg-warm-sand">
      <div class="container-padding mx-auto">
        <!-- Section Header -->
        <div class="reveal opacity-0 text-center mb-12">
          <h2 class="font-serif text-3xl sm:text-4xl md:text-5xl font-light text-midnight-ink tracking-tight">Prestasi Saya</h2>
          <p class="text-driftwood mt-3 max-w-lg mx-auto">Sertifikat dan lisensi profesional yang telah saya dapatkan</p>
        </div>

        <!-- Certificates Grid - White Elevated Cards -->
        <div v-if="certificates && certificates.length > 0" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
          <div v-for="(cert, index) in certificates" :key="cert.id" class="reveal opacity-0" :style="`transition-delay: ${index * 80}ms`">
            <div class="group bg-white rounded-3xl overflow-hidden h-full flex flex-col shadow-elevated">
              <!-- Certificate Image -->
              <div class="aspect-video bg-parchment-white overflow-hidden">
                <img v-if="cert.image_url" :src="cert.image_url" :alt="cert.title" class="w-full h-full object-contain p-4 transition-transform duration-500 group-hover:scale-105" loading="lazy" />
                <div v-else class="w-full h-full flex items-center justify-center">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-driftwood/30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/>
                  </svg>
                </div>
              </div>

              <!-- Content -->
              <div class="p-5 flex-1 flex flex-col">
                <div class="flex items-start gap-3 mb-3">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-terracotta shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                  </svg>
                  <div>
                    <h3 class="font-serif text-base font-medium text-midnight-ink leading-snug group-hover:text-terracotta transition-colors">{{ cert.title }}</h3>
                    <p class="text-terracotta text-sm">{{ cert.issuer }}</p>
                    <p v-if="cert.issued_date" class="font-mono text-xs text-driftwood mt-1">
                      {{ new Date(cert.issued_date).toLocaleDateString('id-ID', { year: 'numeric', month: 'long' }) }}
                    </p>
                  </div>
                </div>

                <!-- Actions -->
                <div class="flex flex-wrap gap-2 mt-auto pt-3">
                  <a v-if="cert.credential_url" :href="cert.credential_url" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 font-mono text-xs px-4 py-2 rounded-full bg-midnight-ink text-cream hover:bg-ink/90 transition-colors cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/>
                    </svg>
                    Verify
                  </a>
                  <a v-if="cert.file_url" :href="cert.file_url" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 font-mono text-xs px-4 py-2 rounded-full border border-ash-border text-driftwood hover:bg-warm-sand transition-colors cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                    Download
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="reveal opacity-0 text-center py-16">
          <div class="flex flex-col items-center justify-center py-12">
            <div class="w-20 h-20 rounded-3xl bg-parchment-white flex items-center justify-center mb-4">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-driftwood/40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <circle cx="12" cy="8" r="6"/>
                <path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/>
              </svg>
            </div>
            <h3 class="font-medium text-midnight-ink mb-2">Belum Ada Sertifikat</h3>
            <p class="text-driftwood text-sm text-center max-w-xs">Tambahkan sertifikat profesional untuk ditampilkan di portfolio</p>
          </div>
        </div>
      </div>
    </section>

    <!-- ============================================
        BLOG SECTION
    ============================================ -->
    <section id="blog" class="section-padding bg-parchment-white" v-if="latestPosts && latestPosts.length > 0">
      <div class="container-padding mx-auto">
        <!-- Section Header -->
        <div class="reveal opacity-0 flex items-end justify-between mb-12">
          <div>
            <h2 class="font-serif text-3xl sm:text-4xl md:text-5xl font-light text-midnight-ink tracking-tight">Artikel Terbaru</h2>
            <p class="text-driftwood mt-3">Thoughts, tutorials, dan insights</p>
          </div>
          <Link href="/blog" class="hidden sm:inline-flex items-center gap-2 px-5 py-2.5 bg-midnight-ink text-cream text-sm font-medium rounded-full hover:bg-ink/90 transition-colors">
            Lihat Semua
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>
            </svg>
          </Link>
        </div>

        <!-- Blog Grid - White Elevated Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
          <div v-for="(post, index) in latestPosts" :key="post.id" class="reveal opacity-0" :style="`transition-delay: ${index * 80}ms`">
            <Link :href="`/blog/${post.slug}`" class="group block bg-white rounded-3xl overflow-hidden h-full flex flex-col shadow-elevated hover:shadow-elevated-hover transition-shadow duration-300">
              <!-- Featured Image -->
              <div class="relative aspect-[16/9] overflow-hidden">
                <img v-if="post.featured_image_url" :src="post.featured_image_url" :alt="post.title" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" loading="lazy" />
                <div v-else class="w-full h-full bg-gradient-to-br from-warm-sand to-parchment-white flex items-center justify-center">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-driftwood/30" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                  </svg>
                </div>
                <!-- Category Badge -->
                <span v-if="post.categories && post.categories.length > 0" class="absolute top-3 left-3 px-3 py-1 bg-cream/90 backdrop-blur-sm text-terracotta text-xs font-mono rounded-full">
                  {{ post.categories[0].name }}
                </span>
              </div>

              <!-- Content -->
              <div class="p-5 flex-1 flex flex-col">
                <h3 class="font-serif text-lg font-medium text-midnight-ink mb-2 line-clamp-2 group-hover:text-terracotta transition-colors">{{ post.title }}</h3>
                <p v-if="post.excerpt" class="text-driftwood text-sm mb-4 line-clamp-2 flex-1">{{ post.excerpt }}</p>

                <!-- Meta -->
                <div class="flex items-center justify-between text-xs text-driftwood/70 pt-3 border-t border-ash-border">
                  <span>{{ post.author?.name }}</span>
                  <div class="flex items-center gap-3">
                    <span>{{ new Date(post.published_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short' }) }}</span>
                    <span class="flex items-center gap-1">
                      <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 12"/>
                      </svg>
                      {{ post.reading_time }} min
                    </span>
                  </div>
                </div>
              </div>
            </Link>
          </div>
        </div>

        <!-- Mobile CTA -->
        <div class="sm:hidden mt-8 text-center">
          <Link href="/blog" class="inline-flex items-center gap-2 px-6 py-3 bg-midnight-ink text-cream text-sm font-medium rounded-full hover:bg-ink/90 transition-colors">
            Lihat Semua Artikel
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>
            </svg>
          </Link>
        </div>
      </div>
    </section>

    <Footer :profile="profile" :social-links="socialLinks" variant="default" :show-social="true" />
  </div>
</template>
