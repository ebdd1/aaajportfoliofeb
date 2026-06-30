<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ref, onUnmounted } from 'vue';

defineOptions({
  layout: AdminLayout,
});

defineProps({
  profile: Object,
});

const form = useForm({
  name: '',
  tagline: '',
  bio: '',
  email: '',
  university: '',
  major: '',
  semester: '',
  meta_title: '',
  meta_description: '',
});

const photoForm = useForm({
  photo: null,
});

const photoPreview = ref(null);

const isEditing = ref(false);

const edit = (profile) => {
  form.name = profile.name;
  form.tagline = profile.tagline;
  form.bio = profile.bio;
  form.email = profile.email;
  form.university = profile.university;
  form.major = profile.major;
  form.semester = profile.semester;
  form.meta_title = profile.meta_title || '';
  form.meta_description = profile.meta_description || '';
  isEditing.value = true;
};

const submit = () => {
  form.patch(route('admin.profile.update'), {
    onSuccess: () => {
      isEditing.value = false;
    },
  });
};

const MAX_FILE_SIZE = 2097152; // 2MB in bytes
const ALLOWED_TYPES = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

const onPhotoChange = (event) => {
  const file = event.target.files[0];
  if (file) {
    // Validate file size
    if (file.size > MAX_FILE_SIZE) {
      alert('File terlalu besar. Maks 2MB.');
      event.target.value = '';
      return;
    }

    // Validate MIME type
    if (!ALLOWED_TYPES.includes(file.type)) {
      alert('Format tidak didukung. Gunakan JPG, PNG, WebP, atau GIF.');
      event.target.value = '';
      return;
    }

    photoForm.photo = file;
    photoPreview.value = URL.createObjectURL(file);
  }
};

const cancelPreview = () => {
  photoForm.photo = null;
  if (photoPreview.value) {
    URL.revokeObjectURL(photoPreview.value);
  }
  photoPreview.value = null;
};

onUnmounted(() => {
  if (photoPreview.value) {
    URL.revokeObjectURL(photoPreview.value);
  }
});

const submitPhoto = () => {
  photoForm.post(route('admin.profile.photo'), {
    onSuccess: () => {
      photoForm.reset('photo');
      if (photoPreview.value) {
        URL.revokeObjectURL(photoPreview.value);
      }
      photoPreview.value = null;
    },
  });
};
</script>

<template>
  <Head title="Edit Profil" />

  <div class="py-12">
    <div class="flex justify-between items-center mb-8">
        <h1 class="font-serif text-3xl font-bold text-ink">Edit Profil</h1>
        <Link :href="route('home')" target="_blank" class="text-sm text-terracotta hover:text-terracotta-dark">
          Lihat Portfolio
        </Link>
      </div>

      <!-- Photo Section -->
      <div class="bg-paper rounded-xl border border-oat-dark p-6 mb-6">
        <h2 class="font-serif text-lg font-semibold text-ink mb-4">Foto Profil</h2>

        <!-- STATE 1: No photo & no preview -->
        <div v-if="!profile.photo_url && !photoPreview">
          <label class="inline-flex items-center gap-2 px-4 py-2 bg-terracotta text-cream text-sm font-medium rounded-full hover:bg-terracotta-dark cursor-pointer">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" />
            </svg>
            Upload Foto
            <input type="file" @change="onPhotoChange" accept="image/*" class="hidden" />
          </label>
          <p class="text-xs text-taupe mt-2">JPG, PNG. Maks 2MB.</p>
        </div>

        <!-- STATE 2: Preview local -->
        <div v-else-if="photoPreview" class="flex items-center gap-6">
          <div class="relative">
            <img :src="photoPreview" alt="Preview" class="w-24 h-24 rounded-full object-cover ring-2 ring-terracotta" />
            <span class="absolute -bottom-1 -right-1 px-2 py-0.5 bg-terracotta text-cream text-xs rounded-full">Preview</span>
          </div>
          <div class="space-y-2">
            <div class="flex gap-2">
              <button type="button" @click="submitPhoto" :disabled="photoForm.processing"
                      class="px-4 py-2 bg-terracotta text-cream text-sm font-medium rounded-full hover:bg-terracotta-dark disabled:opacity-50">
                {{ photoForm.processing ? 'Mengupload...' : 'Upload Sekarang' }}
              </button>
              <button type="button" @click="cancelPreview"
                      class="px-4 py-2 border border-oat-dark text-taupe text-sm font-medium rounded-full hover:bg-oat">
                Batal
              </button>
            </div>
            <p class="text-xs text-taupe">Klik upload untuk menyimpan foto</p>
          </div>
        </div>

        <!-- STATE 3: Has existing photo -->
        <div v-else class="flex items-center gap-6">
          <img :src="profile.photo_url" :alt="profile.name" class="w-24 h-24 rounded-full object-cover" />
          <div>
            <label class="inline-flex items-center gap-2 px-4 py-2 bg-terracotta text-cream text-sm font-medium rounded-full hover:bg-terracotta-dark cursor-pointer">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z" />
              </svg>
              Ganti Foto
              <input type="file" @change="onPhotoChange" accept="image/*" class="hidden" />
            </label>
            <p class="text-xs text-taupe mt-2">JPG, PNG. Maks 2MB.</p>
          </div>
        </div>
      </div>

      <!-- Profile Form -->
      <div class="bg-paper rounded-xl border border-oat-dark p-6">
        <div class="flex justify-between items-center mb-6">
          <h2 class="font-serif text-lg font-semibold text-ink">Informasi Profil</h2>
          <button v-if="!isEditing" @click="edit(profile)" class="text-sm text-terracotta hover:text-terracotta-dark">
            Edit
          </button>
        </div>

        <form @submit.prevent="submit" v-if="isEditing">
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-ink mb-1">Nama Lengkap</label>
              <input v-model="form.name" type="text" required
                     class="w-full px-4 py-2 border border-oat-dark rounded-lg focus:outline-none focus:ring-2 focus:ring-terracotta" />
              <p v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-ink mb-1">Tagline</label>
              <input v-model="form.tagline" type="text" required
                     class="w-full px-4 py-2 border border-oat-dark rounded-lg focus:outline-none focus:ring-2 focus:ring-terracotta" />
            </div>

            <div>
              <label class="block text-sm font-medium text-ink mb-1">Bio</label>
              <textarea v-model="form.bio" rows="4" required
                        class="w-full px-4 py-2 border border-oat-dark rounded-lg focus:outline-none focus:ring-2 focus:ring-terracotta resize-none"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-ink mb-1">Email</label>
                <input v-model="form.email" type="email" required
                       class="w-full px-4 py-2 border border-oat-dark rounded-lg focus:outline-none focus:ring-2 focus:ring-terracotta" />
              </div>
              <div>
                <label class="block text-sm font-medium text-ink mb-1">Semester</label>
                <input v-model="form.semester" type="text" required
                       class="w-full px-4 py-2 border border-oat-dark rounded-lg focus:outline-none focus:ring-2 focus:ring-terracotta" />
              </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-ink mb-1">Universitas</label>
                <input v-model="form.university" type="text" required
                       class="w-full px-4 py-2 border border-oat-dark rounded-lg focus:outline-none focus:ring-2 focus:ring-terracotta" />
              </div>
              <div>
                <label class="block text-sm font-medium text-ink mb-1">Jurusan</label>
                <input v-model="form.major" type="text" required
                       class="w-full px-4 py-2 border border-oat-dark rounded-lg focus:outline-none focus:ring-2 focus:ring-terracotta" />
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-ink mb-1">Meta Title (SEO)</label>
              <input v-model="form.meta_title" type="text"
                     class="w-full px-4 py-2 border border-oat-dark rounded-lg focus:outline-none focus:ring-2 focus:ring-terracotta" />
            </div>

            <div>
              <label class="block text-sm font-medium text-ink mb-1">Meta Description (SEO)</label>
              <textarea v-model="form.meta_description" rows="2"
                        class="w-full px-4 py-2 border border-oat-dark rounded-lg focus:outline-none focus:ring-2 focus:ring-terracotta resize-none"></textarea>
            </div>
          </div>

          <div class="flex gap-4 mt-6">
            <button type="submit" :disabled="form.processing"
                    class="px-6 py-2 bg-terracotta text-cream font-medium rounded-full hover:bg-terracotta-dark disabled:opacity-50">
              {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
            </button>
            <button type="button" @click="isEditing = false"
                    class="px-6 py-2 border border-oat-dark text-taupe font-medium rounded-full hover:bg-oat">
              Batal
            </button>
          </div>
        </form>

        <div v-else class="space-y-3">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <p class="text-xs text-taupe">Nama</p>
              <p class="text-ink">{{ profile.name }}</p>
            </div>
            <div>
              <p class="text-xs text-taupe">Email</p>
              <p class="text-ink">{{ profile.email }}</p>
            </div>
            <div>
              <p class="text-xs text-taupe">Universitas</p>
              <p class="text-ink">{{ profile.university }}</p>
            </div>
            <div>
              <p class="text-xs text-taupe">Jurusan</p>
              <p class="text-ink">{{ profile.major }}</p>
            </div>
          </div>
          <div>
            <p class="text-xs text-taupe">Tagline</p>
            <p class="text-ink">{{ profile.tagline }}</p>
          </div>
          <div>
            <p class="text-xs text-taupe">Bio</p>
            <p class="text-ink">{{ profile.bio }}</p>
          </div>
        </div>
      </div>
  </div>
</template>
