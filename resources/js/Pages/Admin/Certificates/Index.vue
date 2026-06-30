<script setup>
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useConfirm } from '@/Composables/useConfirm';

defineOptions({ layout: AdminLayout });

const props = defineProps({
  certificates: Array,
});

const form = useForm({
  title: '',
  issuer: '',
  issued_date: '',
  credential_url: '',
  file: null,
  image: null,
});

const isModalOpen = ref(false);
const editingCertificate = ref(null);
const { open: confirmOpen } = useConfirm();
const isParsing = ref(false);
const parseError = ref(null);

const openModal = (certificate = null) => {
  editingCertificate.value = certificate;
  
  if (certificate) {
    form.title = certificate.title;
    form.issuer = certificate.issuer;
    form.issued_date = certificate.issued_date;
    form.credential_url = certificate.credential_url || '';
    form.file = null;
    form.image = null;
  } else {
    form.reset();
  }
  
  parseError.value = null;
  isModalOpen.value = true;
};

const closeModal = () => {
  isModalOpen.value = false;
  editingCertificate.value = null;
  form.reset();
  parseError.value = null;
};

const handleFileChange = async (event) => {
  const file = event.target.files[0];
  if (!file) return;
  
  form.file = file;
  
  if (file.type !== 'application/pdf') {
    parseError.value = 'Hanya file PDF yang dapat diparse.';
    return;
  }
  
  await parsePDF(file);
};

const parsePDF = async (file) => {
  isParsing.value = true;
  parseError.value = null;
  
  try {
    const formData = new FormData();
    formData.append('file', file);

    const response = await fetch(route('admin.certificates.parse'), {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'Accept': 'application/json',
      },
      body: formData,
    });

    const data = await response.json();

    if (data.error) {
      parseError.value = data.error;
    } else {
      if (data.title) form.title = data.title;
      if (data.issuer) form.issuer = data.issuer;
      if (data.issue_date) form.issued_date = data.issue_date;
      if (data.credential_url) form.credential_url = data.credential_url;

      if (data.title || data.issuer) {
        parseError.value = null;
      } else {
        parseError.value = 'Tidak dapat mengekstrak data otomatis. Isi secara manual.';
      }
    }
  } catch (error) {
    parseError.value = 'Gagal memproses file. Isi secara manual.';
  } finally {
    isParsing.value = false;
  }
};

const submit = () => {
  if (editingCertificate.value) {
    form.post(route('admin.certificates.update', editingCertificate.value.id), {
      preserveScroll: true,
      onSuccess: closeModal,
      forceFormData: true,
    });
  } else {
    form.post(route('admin.certificates.store'), {
      preserveScroll: true,
      onSuccess: closeModal,
      forceFormData: true,
    });
  }
};

const deleteCertificate = async (certificate) => {
  const confirmed = await confirmOpen({
    message: `Hapus sertifikat "${certificate.title}"? Tindakan ini permanen.`,
    variant: 'danger',
    confirmText: 'Hapus',
    cancelText: 'Batal',
  });

  if (confirmed) {
    router.delete(route('admin.certificates.destroy', certificate.id), {
      preserveScroll: true,
    });
  }
};

const toggleCertificate = (certificate) => {
  router.patch(route('admin.certificates.toggle', certificate.id), {}, {
    preserveScroll: true,
  });
};
</script>

<template>
  <Head title="Kelola Sertifikat" />

  <div class="py-12">
    <div class="max-w-6xl mx-auto">
      <div class="flex justify-between items-center mb-8">
        <div>
          <h1 class="text-3xl font-fraunces font-bold text-ink">Kelola Sertifikat</h1>
          <p class="text-sm text-taupe mt-1">Upload sertifikat dan lisensi profesional Anda.</p>
        </div>
        <button @click="openModal()" class="px-5 py-2.5 bg-terracotta text-white font-medium rounded-xl hover:bg-terracotta/90 transition-colors shadow-sm">
          + Tambah Sertifikat
        </button>
      </div>

      <div v-if="$page.props.flash?.success" class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm">
        {{ $page.props.flash.success }}
      </div>

      <!-- Empty State -->
      <div v-if="certificates.length === 0" class="bg-paper rounded-2xl border border-oat-dark p-12 text-center">
        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-oat flex items-center justify-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-taupe" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="8" r="6"/>
            <path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/>
          </svg>
        </div>
        <h3 class="text-lg font-semibold text-ink mb-2">Belum Ada Sertifikat</h3>
        <p class="text-taupe mb-6">Tambahkan sertifikat atau credential Anda.</p>
        <button @click="openModal()" class="px-6 py-2.5 bg-terracotta text-white font-medium rounded-xl hover:bg-terracotta/90 transition-colors">
          Tambah Sertifikat Pertama
        </button>
      </div>

      <!-- Certificates Grid -->
      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div v-for="certificate in certificates" :key="certificate.id" class="bg-paper rounded-2xl border border-oat-dark overflow-hidden hover:shadow-lg transition-shadow group">
          <div class="p-5">
            <div class="flex items-start justify-between mb-3">
              <div class="flex-1">
                <h3 class="font-semibold text-ink mb-1 line-clamp-2">{{ certificate.title }}</h3>
                <p class="text-sm text-taupe">{{ certificate.issuer }}</p>
              </div>
              <span v-if="certificate.is_active" class="px-2 py-0.5 bg-green-100 text-green-700 text-xs font-medium rounded-full">Aktif</span>
              <span v-else class="px-2 py-0.5 bg-gray-100 text-gray-600 text-xs font-medium rounded-full">Nonaktif</span>
            </div>

            <div class="flex items-center gap-2 text-xs text-taupe mb-4">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                <line x1="16" y1="2" x2="16" y2="6"/>
                <line x1="8" y1="2" x2="8" y2="6"/>
                <line x1="3" y1="10" x2="21" y2="10"/>
              </svg>
              {{ certificate.issued_date }}
            </div>

            <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
              <button @click="toggleCertificate(certificate)" class="flex-1 px-3 py-1.5 text-xs font-medium rounded-lg transition-colors" :class="certificate.is_active ? 'bg-gray-100 text-gray-700 hover:bg-gray-200' : 'bg-green-100 text-green-700 hover:bg-green-200'">
                {{ certificate.is_active ? 'Nonaktifkan' : 'Aktifkan' }}
              </button>
              <button @click="openModal(certificate)" class="flex-1 px-3 py-1.5 bg-blue-100 text-blue-700 text-xs font-medium rounded-lg hover:bg-blue-200 transition-colors">
                Edit
              </button>
              <button @click="deleteCertificate(certificate)" class="px-3 py-1.5 bg-red-100 text-red-700 text-xs font-medium rounded-lg hover:bg-red-200 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <polyline points="3 6 5 6 21 6"/>
                  <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal -->
  <div v-if="isModalOpen" class="fixed inset-0 bg-ink/60 backdrop-blur-sm flex items-center justify-center z-50 p-4" @click.self="closeModal">
    <div class="bg-paper rounded-2xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
      <div class="px-6 py-4 border-b border-oat-dark flex justify-between items-center sticky top-0 bg-paper z-10">
        <h2 class="text-lg font-semibold text-ink">{{ editingCertificate ? 'Edit Sertifikat' : 'Tambah Sertifikat' }}</h2>
        <button @click="closeModal" class="text-taupe hover:text-ink">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="18" y1="6" x2="6" y2="18"/>
            <line x1="6" y1="6" x2="18" y2="18"/>
          </svg>
        </button>
      </div>
      
      <form @submit.prevent="submit" class="p-6 space-y-5">
        <!-- File Upload -->
        <div v-if="!editingCertificate">
          <label class="block text-sm font-medium text-ink mb-2">Upload File PDF Sertifikat</label>
          <input type="file" @change="handleFileChange" accept=".pdf" class="w-full text-sm text-taupe file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-oat file:text-ink hover:file:bg-oat-dark cursor-pointer" />
          <p v-if="isParsing" class="text-xs text-blue-600 mt-1 flex items-center gap-1">
            <svg class="animate-spin h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="10" opacity="0.25"/>
              <path d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" opacity="0.75"/>
            </svg>
            Membaca PDF otomatis...
          </p>
          <p v-if="parseError" class="text-xs text-amber-600 mt-1">{{ parseError }}</p>
        </div>

        <!-- Image Upload (Optional) -->
        <div>
          <label class="block text-sm font-medium text-ink mb-2">Upload Gambar Badge (Opsional)</label>
          <input type="file" @change="form.image = $event.target.files[0]" accept="image/*" class="w-full text-sm text-taupe file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-oat file:text-ink hover:file:bg-oat-dark cursor-pointer" />
        </div>

        <!-- Form Fields -->
        <div>
          <label class="block text-sm font-medium text-ink mb-1">Judul Sertifikat *</label>
          <input v-model="form.title" type="text" placeholder="Misal: AWS Certified Solutions Architect" class="w-full px-4 py-2.5 border border-oat-dark rounded-xl focus:border-terracotta focus:ring-0" required />
          <p v-if="form.errors.title" class="text-red-500 text-xs mt-1">{{ form.errors.title }}</p>
        </div>

        <div>
          <label class="block text-sm font-medium text-ink mb-1">Penerbit/Issuer *</label>
          <input v-model="form.issuer" type="text" placeholder="Misal: Amazon Web Services" class="w-full px-4 py-2.5 border border-oat-dark rounded-xl focus:border-terracotta focus:ring-0" required />
          <p v-if="form.errors.issuer" class="text-red-500 text-xs mt-1">{{ form.errors.issuer }}</p>
        </div>

        <div>
          <label class="block text-sm font-medium text-ink mb-1">Tanggal Terbit *</label>
          <input v-model="form.issued_date" type="date" class="w-full px-4 py-2.5 border border-oat-dark rounded-xl focus:border-terracotta focus:ring-0" required />
          <p v-if="form.errors.issued_date" class="text-red-500 text-xs mt-1">{{ form.errors.issued_date }}</p>
        </div>

        <div>
          <label class="block text-sm font-medium text-ink mb-1">URL Credential (Opsional)</label>
          <input v-model="form.credential_url" type="url" placeholder="https://..." class="w-full px-4 py-2.5 border border-oat-dark rounded-xl focus:border-terracotta focus:ring-0" />
          <p v-if="form.errors.credential_url" class="text-red-500 text-xs mt-1">{{ form.errors.credential_url }}</p>
        </div>

        <div class="flex gap-3 pt-4 border-t border-oat-dark">
          <button type="button" @click="closeModal" class="flex-1 px-4 py-2.5 border border-oat-dark rounded-xl text-ink font-medium hover:bg-oat transition-colors">Batal</button>
          <button type="submit" class="flex-1 px-4 py-2.5 bg-terracotta text-white rounded-xl font-medium hover:bg-terracotta/90 transition-colors" :disabled="form.processing || (!editingCertificate && !form.file)">
            {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
