<script setup>
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useConfirm } from '@/Composables/useConfirm';

defineOptions({ layout: AdminLayout });

const props = defineProps({
  socialLinks: Array,
});

const showModal = ref(false);
const editingLink = ref(null);
const { open: confirmOpen } = useConfirm();

const form = useForm({
  platform: '',
  label: '',
  url: '',
  icon: '',
  is_active: true,
});

const platforms = [
  { value: 'github', label: 'GitHub', icon: 'github' },
  { value: 'linkedin', label: 'LinkedIn', icon: 'linkedin' },
  { value: 'twitter', label: 'Twitter / X', icon: 'twitter' },
  { value: 'instagram', label: 'Instagram', icon: 'instagram' },
  { value: 'facebook', label: 'Facebook', icon: 'facebook' },
  { value: 'youtube', label: 'YouTube', icon: 'youtube' },
  { value: 'tiktok', label: 'TikTok', icon: 'tiktok' },
  { value: 'whatsapp', label: 'WhatsApp', icon: 'whatsapp' },
  { value: 'telegram', label: 'Telegram', icon: 'telegram' },
  { value: 'email', label: 'Email', icon: 'email' },
  { value: 'website', label: 'Website', icon: 'website' },
  { value: 'other', label: 'Other', icon: 'link' },
];

const openCreate = () => {
  editingLink.value = null;
  form.reset();
  form.is_active = true;
  showModal.value = true;
};

const openEdit = (link) => {
  editingLink.value = link;
  form.platform = link.platform;
  form.label = link.label;
  form.url = link.url;
  form.icon = link.icon || '';
  form.is_active = link.is_active;
  showModal.value = true;
};

const submit = () => {
  if (editingLink.value) {
    form.put(route('admin.social-links.update', editingLink.value.id), {
      preserveScroll: true,
      onSuccess: () => { showModal.value = false; form.reset(); },
    });
  } else {
    form.post(route('admin.social-links.store'), {
      preserveScroll: true,
      onSuccess: () => { showModal.value = false; form.reset(); },
    });
  }
};

const toggleActive = (link) => {
  router.patch(route('admin.social-links.toggle', link.id), {}, {
    preserveScroll: true,
  });
};

const deleteLink = async (link) => {
  const confirmed = await confirmOpen({
    message: `Hapus link "${link.label}"? Tindakan ini permanen.`,
    variant: 'danger',
    confirmText: 'Hapus',
    cancelText: 'Batal',
  });

  if (confirmed) {
    router.delete(route('admin.social-links.destroy', link.id), {
      preserveScroll: true,
    });
  }
};

const getSocialIcon = (platform) => {
  const icons = {
    github: `<svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.477 2 2 6.477 2 12c0 4.42 2.865 8.17 6.839 9.49.5.092.682-.217.682-.482 0-.237-.008-.866-.013-1.7-2.782.603-3.369-1.34-3.369-1.34-.454-1.156-1.11-1.463-1.11-1.463-.908-.62.069-.608.069-.608 1.003.07 1.531 1.03 1.531 1.03.892 1.529 2.341 1.087 2.91.831.092-.646.35-1.086.636-1.336-2.22-.253-4.555-1.11-4.555-4.943 0-1.091.39-1.984 1.029-2.683-.103-.253-.446-1.27.098-2.647 0 0 .84-.269 2.75 1.025A9.578 9.578 0 0112 6.836c.85.004 1.705.114 2.504.336 1.909-1.294 2.747-1.025 2.747-1.025.546 1.377.203 2.394.1 2.647.64.699 1.028 1.592 1.028 2.683 0 3.842-2.339 4.687-4.566 4.935.359.309.678.919.678 1.852 0 1.336-.012 2.415-.012 2.743 0 .267.18.578.688.48C19.138 20.167 22 16.418 22 12c0-5.523-4.477-10-10-10z"/></svg>`,
    linkedin: `<svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>`,
    twitter: `<svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>`,
    instagram: `<svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2c2.717 0 3.056.01 4.122.06 1.065.05 1.79.217 2.428.465.66.254 1.216.598 1.772 1.153a4.908 4.908 0 0 1 1.153 1.772c.247.637.415 1.363.465 2.428.047 1.066.06 1.405.06 4.122 0 2.717-.01 3.056-.06 4.122-.05 1.065-.218 1.79-.465 2.428a4.883 4.883 0 0 1-1.153 1.772 4.915 4.915 0 0 1-1.772 1.153c-.637.247-1.363.415-2.428.465-1.066.047-1.405.06-4.122.06-2.717 0-3.056-.01-4.122-.06-1.065-.05-1.79-.218-2.428-.465a4.89 4.89 0 0 1-1.772-1.153 4.904 4.904 0 0 1-1.153-1.772c-.248-.637-.415-1.363-.465-2.428C2.013 15.056 2 14.717 2 12c0-2.717.01-3.056.06-4.122.05-1.066.217-1.79.465-2.428a4.88 4.88 0 0 1 1.153-1.772A4.897 4.897 0 0 1 5.45 2.525c.638-.248 1.362-.415 2.428-.465C8.944 2.013 9.283 2 12 2zm0 5a5 5 0 1 0 0 10 5 5 0 0 0 0-10zm6.5-.25a1.25 1.25 0 0 0-2.5 0 1.25 1.25 0 0 0 2.5 0zM12 9a3 3 0 1 1 0 6 3 3 0 0 1 0-6z"/></svg>`,
    facebook: `<svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>`,
    youtube: `<svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>`,
    tiktok: `<svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg>`,
    whatsapp: `<svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>`,
    telegram: `<svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>`,
    email: `<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>`,
    website: `<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>`,
    other: `<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>`,
  };
  return icons[platform] || icons.other;
};
</script>

<template>
  <Head title="Social Links" />

  <div class="py-12">
    <div class="max-w-5xl mx-auto">
      <div class="flex items-center justify-between mb-8">
        <div>
          <h1 class="text-3xl font-fraunces font-bold text-ink">Social Links</h1>
          <p class="text-sm text-taupe mt-1">Kelola link media sosial yang ditampilkan di portfolio.</p>
        </div>
        <button @click="openCreate" class="px-5 py-2.5 bg-terracotta text-white font-medium rounded-xl hover:bg-terracotta/90 transition-colors shadow-sm">
          + Tambah Link
        </button>
      </div>

      <div v-if="$page.props.flash?.success" class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm">
        {{ $page.props.flash.success }}
      </div>

      <!-- Social Links List -->
      <div class="bg-paper rounded-2xl border border-oat-dark overflow-hidden">
        <div v-if="socialLinks.length === 0" class="p-12 text-center">
          <p class="text-taupe text-sm">Belum ada social link. Klik "Tambah Link" untuk memulai.</p>
        </div>

        <div v-else class="divide-y divide-oat-dark">
          <div v-for="link in socialLinks" :key="link.id" class="p-5 hover:bg-oat/30 transition-colors group">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-4 flex-1">
                <div class="w-12 h-12 rounded-xl bg-oat border border-oat-dark flex items-center justify-center text-ink" v-html="getSocialIcon(link.platform)">
                </div>
                <div class="flex-1">
                  <div class="flex items-center gap-3 mb-1">
                    <p class="font-semibold text-ink">{{ link.label }}</p>
                    <span v-if="link.is_active" class="px-2 py-0.5 bg-green-100 text-green-700 text-xs font-medium rounded-full">Aktif</span>
                    <span v-else class="px-2 py-0.5 bg-gray-100 text-gray-600 text-xs font-medium rounded-full">Nonaktif</span>
                  </div>
                  <p class="text-sm text-taupe break-all">{{ link.url }}</p>
                </div>
              </div>

              <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                <button @click="toggleActive(link)" class="px-3 py-1.5 text-xs font-medium rounded-lg transition-colors" :class="link.is_active ? 'bg-gray-100 text-gray-700 hover:bg-gray-200' : 'bg-green-100 text-green-700 hover:bg-green-200'">
                  {{ link.is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                </button>
                <button @click="openEdit(link)" class="px-3 py-1.5 bg-blue-100 text-blue-700 text-xs font-medium rounded-lg hover:bg-blue-200 transition-colors">
                  Edit
                </button>
                <button @click="deleteLink(link)" class="px-3 py-1.5 bg-red-100 text-red-700 text-xs font-medium rounded-lg hover:bg-red-200 transition-colors">
                  Hapus
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Form -->
  <div v-if="showModal" class="fixed inset-0 bg-ink/60 backdrop-blur-sm flex items-center justify-center z-50 p-4" @click.self="showModal = false">
    <div class="bg-paper rounded-2xl shadow-xl w-full max-w-lg overflow-hidden">
      <div class="px-6 py-4 border-b border-oat-dark flex justify-between items-center bg-cream/50">
        <h2 class="text-lg font-semibold text-ink">{{ editingLink ? 'Edit Social Link' : 'Tambah Social Link' }}</h2>
        <button @click="showModal = false" class="text-taupe hover:text-ink">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="18" y1="6" x2="6" y2="18"/>
            <line x1="6" y1="6" x2="18" y2="18"/>
          </svg>
        </button>
      </div>
      
      <form @submit.prevent="submit" class="p-6 space-y-5">
        <div>
          <label class="block text-sm font-medium text-ink mb-1">Platform *</label>
          <select v-model="form.platform" class="w-full px-4 py-2.5 border border-oat-dark rounded-xl focus:border-terracotta focus:ring-0 bg-white" required>
            <option value="" disabled>Pilih platform</option>
            <option v-for="platform in platforms" :key="platform.value" :value="platform.value">{{ platform.label }}</option>
          </select>
          <p v-if="form.errors.platform" class="text-red-500 text-xs mt-1">{{ form.errors.platform }}</p>
        </div>

        <div>
          <label class="block text-sm font-medium text-ink mb-1">Label *</label>
          <input v-model="form.label" type="text" placeholder="Contoh: @username atau My Portfolio" class="w-full px-4 py-2.5 border border-oat-dark rounded-xl focus:border-terracotta focus:ring-0" required />
          <p v-if="form.errors.label" class="text-red-500 text-xs mt-1">{{ form.errors.label }}</p>
        </div>
        
        <div>
          <label class="block text-sm font-medium text-ink mb-1">URL *</label>
          <input v-model="form.url" type="url" placeholder="https://github.com/username" class="w-full px-4 py-2.5 border border-oat-dark rounded-xl focus:border-terracotta focus:ring-0" required />
          <p v-if="form.errors.url" class="text-red-500 text-xs mt-1">{{ form.errors.url }}</p>
        </div>

        <div class="flex items-center gap-2 p-4 bg-oat rounded-xl">
          <input type="checkbox" v-model="form.is_active" class="rounded border-oat-dark text-terracotta focus:ring-terracotta" id="is_active" />
          <label for="is_active" class="text-sm font-medium text-ink cursor-pointer">Aktifkan link ini</label>
        </div>
        
        <div class="flex gap-3 pt-4 border-t border-oat-dark">
          <button type="button" @click="showModal = false" class="flex-1 px-4 py-2.5 border border-oat-dark rounded-xl text-ink font-medium hover:bg-oat transition-colors">Batal</button>
          <button type="submit" class="flex-1 px-4 py-2.5 bg-terracotta text-white rounded-xl font-medium hover:bg-terracotta/90 transition-colors" :disabled="form.processing">
            {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
