<script setup>
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useConfirm } from '@/Composables/useConfirm';

defineOptions({ layout: AdminLayout });

const props = defineProps({
  cvs: Array,
  activeCv: Object,
});

const form = useForm({
  cv: null,
});

const selectedFileName = ref('');
const { open: confirmOpen } = useConfirm();

const handleFileSelect = (event) => {
  const file = event.target.files[0];
  if (file) {
    form.cv = file;
    selectedFileName.value = file.name;
  }
};

const upload = () => {
  form.post(route('admin.cv.store'), {
    preserveScroll: true,
    onSuccess: () => {
      form.reset();
      selectedFileName.value = '';
    },
  });
};

const activate = (cv) => {
  router.patch(route('admin.cv.activate', cv.id), {}, {
    preserveScroll: true,
  });
};

const deleteCv = async (cv) => {
  const confirmed = await confirmOpen({
    message: `Hapus "${cv.original_filename}"? Tindakan ini permanen.`,
    variant: 'danger',
    confirmText: 'Hapus',
    cancelText: 'Batal',
  });

  if (confirmed) {
    router.delete(route('admin.cv.destroy', cv.id), {
      preserveScroll: true,
    });
  }
};

const formatFileSize = (bytes) => {
  if (bytes === 0) return '0 B';
  const k = 1024;
  const sizes = ['B', 'KB', 'MB', 'GB'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
};
</script>

<template>
  <Head title="Kelola CV" />

  <div class="py-12">
    <div class="max-w-5xl mx-auto">
      <div class="mb-8">
        <h1 class="text-3xl font-fraunces font-bold text-ink">Kelola CV</h1>
        <p class="text-sm text-taupe mt-1">Upload dan kelola file CV Anda (format PDF).</p>
      </div>

      <div v-if="$page.props.flash?.success" class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm">
        {{ $page.props.flash.success }}
      </div>

      <!-- Upload Form -->
      <div class="bg-paper rounded-2xl border border-oat-dark p-6 mb-6">
        <h2 class="text-lg font-semibold text-ink mb-4 flex items-center gap-2">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-terracotta" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
            <polyline points="17 8 12 3 7 8"/>
            <line x1="12" y1="3" x2="12" y2="15"/>
          </svg>
          Upload CV Baru
        </h2>
        
        <form @submit.prevent="upload" class="space-y-4">
          <div class="border-2 border-dashed border-oat-dark rounded-xl p-6 text-center hover:border-terracotta transition-colors">
            <input 
              type="file" 
              @change="handleFileSelect" 
              accept=".pdf"
              id="cv-upload"
              class="hidden"
            />
            <label for="cv-upload" class="cursor-pointer">
              <div class="flex flex-col items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-taupe" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                  <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                  <polyline points="14 2 14 8 20 8"/>
                  <line x1="12" y1="18" x2="12" y2="12"/>
                  <line x1="9" y1="15" x2="15" y2="15"/>
                </svg>
                <div>
                  <p class="text-sm font-medium text-ink">Klik untuk upload file PDF</p>
                  <p class="text-xs text-taupe mt-1">Maksimal 10 MB</p>
                </div>
              </div>
            </label>
          </div>

          <div v-if="selectedFileName" class="flex items-center gap-3 p-3 bg-oat rounded-lg">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
              <polyline points="14 2 14 8 20 8"/>
            </svg>
            <p class="text-sm text-ink font-medium flex-1">{{ selectedFileName }}</p>
            <button type="button" @click="form.reset(); selectedFileName = ''" class="text-taupe hover:text-red-600">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18"/>
                <line x1="6" y1="6" x2="18" y2="18"/>
              </svg>
            </button>
          </div>

          <div v-if="form.errors.cv" class="text-red-500 text-sm bg-red-50 px-3 py-2 rounded-lg">
            {{ form.errors.cv }}
          </div>

          <button 
            type="submit" 
            :disabled="!form.cv || form.processing"
            class="w-full px-6 py-3 bg-terracotta text-white font-medium rounded-xl hover:bg-terracotta/90 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
          >
            <span v-if="form.processing" class="flex items-center justify-center gap-2">
              <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10" opacity="0.25"/>
                <path d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" opacity="0.75"/>
              </svg>
              Mengupload...
            </span>
            <span v-else>Upload CV</span>
          </button>
        </form>
      </div>

      <!-- CV List -->
      <div class="bg-paper rounded-2xl border border-oat-dark overflow-hidden">
        <div class="px-6 py-4 border-b border-oat-dark bg-cream/50">
          <h2 class="text-lg font-semibold text-ink">CV Tersimpan</h2>
        </div>
        
        <div v-if="cvs.length === 0" class="p-12 text-center">
          <p class="text-taupe text-sm">Belum ada CV yang diupload. Upload CV pertama Anda di atas.</p>
        </div>

        <div v-else class="divide-y divide-oat-dark">
          <div v-for="cv in cvs" :key="cv.id" class="p-5 hover:bg-oat/30 transition-colors group">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-4 flex-1">
                <div class="w-12 h-12 rounded-xl bg-red-50 border border-red-100 flex items-center justify-center">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-red-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                  </svg>
                </div>
                <div class="flex-1">
                  <div class="flex items-center gap-3 mb-1">
                    <p class="font-semibold text-ink">{{ cv.original_filename }}</p>
                    <span v-if="cv.is_active" class="px-2 py-0.5 bg-green-100 text-green-700 text-xs font-medium rounded-full">CV Aktif</span>
                  </div>
                  <p class="text-sm text-taupe">{{ formatFileSize(cv.file_size) }}</p>
                </div>
              </div>

              <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                <a :href="`/storage/${cv.file_path}`" target="_blank" class="px-3 py-1.5 bg-blue-100 text-blue-700 text-xs font-medium rounded-lg hover:bg-blue-200 transition-colors">
                  Lihat
                </a>
                <button 
                  v-if="!cv.is_active"
                  @click="activate(cv)" 
                  class="px-3 py-1.5 bg-green-100 text-green-700 text-xs font-medium rounded-lg hover:bg-green-200 transition-colors"
                >
                  Aktifkan
                </button>
                <button 
                  @click="deleteCv(cv)" 
                  class="px-3 py-1.5 bg-red-100 text-red-700 text-xs font-medium rounded-lg hover:bg-red-200 transition-colors"
                >
                  Hapus
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
