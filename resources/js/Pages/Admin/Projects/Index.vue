<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useConfirm } from '@/Composables/useConfirm';

defineOptions({ layout: AdminLayout });

defineProps({ projects: Array });

const { open: confirmOpen } = useConfirm();

// ── State ──────────────────────────────────────────────────────────────────
const isModalOpen = ref(false);
const editingProject = ref(null);
const imagePreview = ref(null);
const newTag = ref('');

const form = useForm({
  title: '',
  description: '',
  tags: [],
  repo_url: '',
  demo_url: '',
  repo_status: 'coming_soon',
  is_featured: false,
  display_order: 0,
  image: null,
});

// ── Computed ──────────────────────────────────────────────────────────────
const modalTitle = computed(() => editingProject.value ? 'Edit Proyek' : 'Tambah Proyek');

const repoStatusOptions = [
  { value: 'available', label: 'Tersedia' },
  { value: 'coming_soon', label: 'Segera Tersedia' },
  { value: 'private', label: 'Private' },
];

const repoStatusLabel = (status) => {
  const opt = repoStatusOptions.find(o => o.value === status);
  return opt ? opt.label : status;
};

// ── Helpers ───────────────────────────────────────────────────────────────
const openModal = (project = null) => {
  if (project) {
    editingProject.value = project;
    form.title = project.title;
    form.description = project.description;
    form.tags = [...(project.tags || [])];
    form.repo_url = project.repo_url || '';
    form.demo_url = project.demo_url || '';
    form.repo_status = project.repo_status || 'coming_soon';
    form.is_featured = project.is_featured || false;
    form.display_order = project.display_order || 0;
    form.image = null;
    imagePreview.value = project.image_url || null;
  } else {
    editingProject.value = null;
    form.reset();
    form.tags = [];
    form.repo_status = 'coming_soon';
    form.display_order = 0;
    imagePreview.value = null;
  }
  newTag.value = '';
  isModalOpen.value = true;
};

const closeModal = () => {
  isModalOpen.value = false;
  editingProject.value = null;
  imagePreview.value = null;
  form.reset();
  newTag.value = '';
};

const handleImage = (e) => {
  const file = e.target.files[0];
  if (!file) return;
  form.image = file;
  imagePreview.value = URL.createObjectURL(file);
};

const addTag = () => {
  const tag = newTag.value.trim();
  if (tag && !form.tags.includes(tag)) {
    form.tags.push(tag);
    newTag.value = '';
  }
};

const removeTag = (idx) => form.tags.splice(idx, 1);

const submit = () => {
  const opts = { forceFormData: true, onSuccess: closeModal };
  if (editingProject.value) {
    form.post(route('admin.projects.update', editingProject.value.id), {
      ...opts,
      headers: { 'X-HTTP-Method-Override': 'PUT' },
    });
  } else {
    form.post(route('admin.projects.store'), opts);
  }
};

// ── Actions ───────────────────────────────────────────────────────────────
const deleteProject = async (project) => {
  const confirmed = await confirmOpen({
    message: `Proyek "${project.title}" akan dihapus permanen. Lanjutkan?`,
    variant: 'danger',
    confirmText: 'Hapus',
    cancelText: 'Batal',
  });
  if (confirmed) useForm({}).delete(route('admin.projects.destroy', project.id));
};

const toggleActive = (project) => useForm({}).patch(route('admin.projects.toggle', project.id));
</script>

<template>
  <Head title="Kelola Proyek" />

  <div class="py-12">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
      <h1 class="font-serif text-3xl font-bold text-ink">Kelola Proyek</h1>
      <button @click="openModal()" class="px-5 py-2 bg-terracotta text-cream text-sm font-medium rounded-full hover:bg-terracotta-dark transition-colors">
        + Tambah Proyek
      </button>
    </div>

    <!-- Empty state -->
    <div v-if="projects.length === 0" class="bg-paper rounded-xl border border-oat-dark p-16 text-center">
      <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-oat flex items-center justify-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-taupe" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M22 19a2 2 0 01-2 2H4a2 2 0 01-2-2V5a2 2 0 012-2h5l2 3h9a2 2 0 012 2z"/>
        </svg>
      </div>
      <h3 class="font-serif text-lg font-semibold text-ink mb-2">Belum Ada Proyek</h3>
      <p class="text-taupe mb-6">Tambahkan proyek pertama Anda.</p>
      <button @click="openModal()" class="px-6 py-2 bg-terracotta text-cream font-medium rounded-full hover:bg-terracotta-dark transition-colors">
        + Tambah Proyek
      </button>
    </div>

    <!-- Table -->
    <div v-else class="bg-paper rounded-xl border border-oat-dark overflow-hidden">
      <table class="w-full">
        <thead class="bg-oat">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-taupe uppercase tracking-wider">Proyek</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-taupe uppercase tracking-wider">Tags</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-taupe uppercase tracking-wider">Repo</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-taupe uppercase tracking-wider">Featured</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-taupe uppercase tracking-wider">Status</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-taupe uppercase tracking-wider">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-oat-dark">
          <tr v-for="project in projects" :key="project.id" class="hover:bg-cream/50">

            <!-- Project info -->
            <td class="px-6 py-4">
              <div class="flex items-center gap-3">
                <img v-if="project.image_url" :src="project.image_url" :alt="project.title"
                  class="w-10 h-10 rounded-lg object-cover border border-oat-dark shrink-0" />
                <div v-else class="w-10 h-10 rounded-lg bg-oat flex items-center justify-center shrink-0">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-taupe" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/>
                    <polyline points="21 15 16 10 5 21"/>
                  </svg>
                </div>
                <div>
                  <p class="font-medium text-ink text-sm">{{ project.title }}</p>
                  <p class="text-xs text-taupe truncate max-w-[180px]">{{ project.description }}</p>
                </div>
              </div>
            </td>

            <!-- Tags -->
            <td class="px-6 py-4">
              <div class="flex flex-wrap gap-1 max-w-[140px]">
                <span v-for="tag in (project.tags || []).slice(0, 3)" :key="tag"
                  class="font-mono text-xs bg-oat text-taupe px-1.5 py-0.5 rounded">{{ tag }}</span>
                <span v-if="(project.tags || []).length > 3" class="font-mono text-xs text-taupe">
                  +{{ project.tags.length - 3 }}
                </span>
              </div>
            </td>

            <!-- Repo status -->
            <td class="px-6 py-4 whitespace-nowrap">
              <span class="font-mono text-xs bg-terracotta/10 text-terracotta px-2 py-0.5 rounded">
                {{ repoStatusLabel(project.repo_status) }}
              </span>
            </td>

            <!-- Featured -->
            <td class="px-6 py-4 whitespace-nowrap text-sm">
              <span :class="project.is_featured ? 'text-amber-600' : 'text-taupe'">
                {{ project.is_featured ? '⭐ Ya' : 'Tidak' }}
              </span>
            </td>

            <!-- Active toggle -->
            <td class="px-6 py-4 whitespace-nowrap">
              <button @click="toggleActive(project)"
                :class="project.is_active ? 'text-green-600' : 'text-taupe'"
                class="text-sm font-medium hover:underline">
                {{ project.is_active ? 'Aktif' : 'Nonaktif' }}
              </button>
            </td>

            <!-- Actions -->
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-3">
              <a v-if="project.demo_url" :href="project.demo_url" target="_blank"
                class="text-taupe hover:text-ink">Preview</a>
              <a v-if="project.repo_url" :href="project.repo_url" target="_blank"
                class="text-taupe hover:text-ink">Repo</a>
              <button @click="openModal(project)" class="text-terracotta hover:text-terracotta-dark">Edit</button>
              <button @click="deleteProject(project)" class="text-red-500 hover:text-red-700">Hapus</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Modal Add/Edit -->
  <Teleport to="body">
    <Transition enter-active-class="duration-200 ease-out" enter-from-class="opacity-0"
      enter-to-class="opacity-100" leave-active-class="duration-150 ease-in"
      leave-from-class="opacity-100" leave-to-class="opacity-0">

      <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-start justify-center p-4 overflow-y-auto">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-ink/60 backdrop-blur-sm" @click="closeModal" />

        <!-- Modal -->
        <div class="relative bg-paper rounded-2xl shadow-2xl w-full max-w-2xl my-8">
          <!-- Header -->
          <div class="flex items-center justify-between px-6 py-4 border-b border-oat-dark">
            <h2 class="font-serif text-xl font-bold text-ink">{{ modalTitle }}</h2>
            <button @click="closeModal" class="text-taupe hover:text-ink transition-colors">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
              </svg>
            </button>
          </div>

          <form @submit.prevent="submit" class="p-6 space-y-5">

            <!-- Image upload + preview -->
            <div>
              <label class="block text-sm font-medium text-ink mb-2">Gambar Proyek</label>
              <div class="flex gap-4 items-start">
                <!-- Preview box -->
                <div class="w-32 h-24 rounded-xl border-2 border-dashed border-oat-dark overflow-hidden shrink-0 bg-oat flex items-center justify-center">
                  <img v-if="imagePreview" :src="imagePreview" class="w-full h-full object-cover" alt="preview" />
                  <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-taupe/50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/>
                    <polyline points="21 15 16 10 5 21"/>
                  </svg>
                </div>
                <div class="flex-1">
                  <input type="file" accept="image/*" @change="handleImage" class="hidden" id="proj-image" />
                  <label for="proj-image"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm border border-oat-dark rounded-lg cursor-pointer hover:bg-oat transition-colors text-ink">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/>
                      <line x1="12" y1="3" x2="12" y2="15"/>
                    </svg>
                    Upload Gambar
                  </label>
                  <p class="text-xs text-taupe mt-2">JPG, PNG, WebP. Maks 2MB.</p>
                  <p v-if="form.errors.image" class="text-red-500 text-xs mt-1">{{ form.errors.image }}</p>
                </div>
              </div>
            </div>

            <!-- Title -->
            <div>
              <label class="block text-sm font-medium text-ink mb-1">Judul Proyek <span class="text-red-500">*</span></label>
              <input v-model="form.title" type="text" placeholder="Nama proyek Anda" required
                class="w-full px-4 py-2.5 border border-oat-dark rounded-lg focus:outline-none focus:ring-2 focus:ring-terracotta" />
              <p v-if="form.errors.title" class="text-red-500 text-xs mt-1">{{ form.errors.title }}</p>
            </div>

            <!-- Description -->
            <div>
              <label class="block text-sm font-medium text-ink mb-1">Deskripsi <span class="text-red-500">*</span></label>
              <textarea v-model="form.description" rows="3" placeholder="Jelaskan proyek Anda secara singkat..." required
                class="w-full px-4 py-2.5 border border-oat-dark rounded-lg focus:outline-none focus:ring-2 focus:ring-terracotta resize-none"></textarea>
              <p v-if="form.errors.description" class="text-red-500 text-xs mt-1">{{ form.errors.description }}</p>
            </div>

            <!-- Tags -->
            <div>
              <label class="block text-sm font-medium text-ink mb-1">Tags <span class="text-red-500">*</span></label>
              <div class="flex gap-2 mb-2">
                <input v-model="newTag" type="text" placeholder="Tambah tag (Enter)"
                  @keydown.enter.prevent="addTag"
                  class="flex-1 px-4 py-2 border border-oat-dark rounded-lg focus:outline-none focus:ring-2 focus:ring-terracotta text-sm" />
                <button type="button" @click="addTag"
                  class="px-4 py-2 bg-oat text-ink rounded-lg hover:bg-oat-dark transition-colors text-sm">+</button>
              </div>
              <div v-if="form.tags.length" class="flex flex-wrap gap-2">
                <span v-for="(tag, idx) in form.tags" :key="idx"
                  class="inline-flex items-center gap-1 bg-terracotta/10 text-terracotta text-xs font-mono px-2.5 py-1 rounded-full">
                  {{ tag }}
                  <button type="button" @click="removeTag(idx)" class="hover:text-red-600 ml-0.5">×</button>
                </span>
              </div>
              <p v-if="form.errors.tags" class="text-red-500 text-xs mt-1">{{ form.errors.tags }}</p>
            </div>

            <!-- URLs -->
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-ink mb-1">URL Repository</label>
                <input v-model="form.repo_url" type="url" placeholder="https://github.com/..."
                  class="w-full px-4 py-2.5 border border-oat-dark rounded-lg focus:outline-none focus:ring-2 focus:ring-terracotta text-sm" />
                <p v-if="form.errors.repo_url" class="text-red-500 text-xs mt-1">{{ form.errors.repo_url }}</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-ink mb-1">URL Demo / Live</label>
                <input v-model="form.demo_url" type="url" placeholder="https://..."
                  class="w-full px-4 py-2.5 border border-oat-dark rounded-lg focus:outline-none focus:ring-2 focus:ring-terracotta text-sm" />
                <p v-if="form.errors.demo_url" class="text-red-500 text-xs mt-1">{{ form.errors.demo_url }}</p>
              </div>
            </div>

            <!-- Repo status + Featured + Order -->
            <div class="grid grid-cols-3 gap-4">
              <div>
                <label class="block text-sm font-medium text-ink mb-1">Status Repo <span class="text-red-500">*</span></label>
                <select v-model="form.repo_status"
                  class="w-full px-4 py-2.5 border border-oat-dark rounded-lg focus:outline-none focus:ring-2 focus:ring-terracotta text-sm bg-paper">
                  <option v-for="opt in repoStatusOptions" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-ink mb-1">Urutan</label>
                <input v-model.number="form.display_order" type="number" min="0"
                  class="w-full px-4 py-2.5 border border-oat-dark rounded-lg focus:outline-none focus:ring-2 focus:ring-terracotta text-sm" />
              </div>
              <div class="flex flex-col justify-center">
                <label class="flex items-center gap-2 cursor-pointer">
                  <input v-model="form.is_featured" type="checkbox"
                    class="w-4 h-4 accent-terracotta rounded" />
                  <span class="text-sm font-medium text-ink">Tampilkan di Featured</span>
                </label>
              </div>
            </div>

            <!-- Actions -->
            <div class="flex gap-3 pt-2">
              <button type="submit" :disabled="form.processing"
                class="flex-1 px-4 py-2.5 bg-terracotta text-cream font-medium rounded-full hover:bg-terracotta-dark disabled:opacity-50 transition-colors">
                {{ form.processing ? 'Menyimpan...' : (editingProject ? 'Simpan Perubahan' : 'Tambah Proyek') }}
              </button>
              <button type="button" @click="closeModal"
                class="px-4 py-2.5 border border-oat-dark text-taupe font-medium rounded-full hover:bg-oat transition-colors">
                Batal
              </button>
            </div>
          </form>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>
