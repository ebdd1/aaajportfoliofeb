<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useConfirm } from '@/Composables/useConfirm';

defineOptions({
  layout: AdminLayout,
});

defineProps({
  skills: Array,
});

const form = useForm({
  category_number: '',
  category_label: '',
  category_title: '',
  tags: [],
});

const newTag = ref('');
const isModalOpen = ref(false);
const editingSkill = ref(null);
const { open: confirmOpen } = useConfirm();

const openModal = (skill = null) => {
  if (skill) {
    editingSkill.value = skill;
    form.category_number = skill.category_number;
    form.category_label = skill.category_label;
    form.category_title = skill.category_title;
    form.tags = [...skill.tags];
  } else {
    editingSkill.value = null;
    form.reset();
    form.tags = [];
  }
  newTag.value = '';
  isModalOpen.value = true;
};

const closeModal = () => {
  isModalOpen.value = false;
  editingSkill.value = null;
  form.reset();
};

const addTag = () => {
  if (newTag.value.trim() && !form.tags.includes(newTag.value.trim())) {
    form.tags.push(newTag.value.trim());
    newTag.value = '';
  }
};

const removeTag = (index) => {
  form.tags.splice(index, 1);
};

const submit = () => {
  if (editingSkill.value) {
    form.put(route('admin.skills.update', editingSkill.value.id), {
      preserveScroll: true,
      onSuccess: () => {
        closeModal();
      },
      onError: (errors) => {
        console.error('Update failed:', errors);
      }
    });
  } else {
    form.post(route('admin.skills.store'), {
      preserveScroll: true,
      onSuccess: () => {
        closeModal();
      },
      onError: (errors) => {
        console.error('Store failed:', errors);
      }
    });
  }
};

const deleteSkill = async (skill) => {
  const confirmed = await confirmOpen({
    message: 'Skill akan dihapus permanen. Lanjutkan?',
    variant: 'danger',
    confirmText: 'Hapus',
    cancelText: 'Batal',
  });

  if (confirmed) {
    form.delete(route('admin.skills.destroy', skill.id));
  }
};

const toggleSkill = (skill) => {
  form.patch(route('admin.skills.toggle', skill.id));
};
</script>

<template>
  <Head title="Kelola Keahlian" />

  <div class="py-12">
    <div class="flex justify-between items-center mb-8">
      <h1 class="font-serif text-3xl font-bold text-ink">Kelola Keahlian</h1>
      <button @click="openModal()" class="px-4 py-2 bg-terracotta text-cream font-medium rounded-full hover:bg-terracotta-dark">
        Tambah Skill
      </button>
    </div>

    <div class="bg-paper rounded-xl border border-oat-dark overflow-hidden">
      <table class="w-full">
        <thead class="bg-oat">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-taupe uppercase tracking-wider">Kategori</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-taupe uppercase tracking-wider">Judul</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-taupe uppercase tracking-wider">Tags</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-taupe uppercase tracking-wider">Status</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-taupe uppercase tracking-wider">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-oat-dark">
          <tr v-for="skill in skills" :key="skill.id" class="hover:bg-cream/50">
            <td class="px-6 py-4 whitespace-nowrap">
              <span class="font-mono text-xs text-terracotta bg-terracotta/10 px-2 py-1 rounded">{{ skill.category_number }}</span>
              <p class="text-sm text-taupe mt-1">{{ skill.category_label }}</p>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-ink font-medium">{{ skill.category_title }}</td>
            <td class="px-6 py-4">
              <div class="flex flex-wrap gap-1">
                <span v-for="tag in skill.tags" :key="tag" class="font-mono text-xs bg-oat text-taupe px-2 py-1 rounded">{{ tag }}</span>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <button @click="toggleSkill(skill)"
                      :class="skill.is_active ? 'text-green-600' : 'text-taupe'"
                      class="text-sm font-medium">
                {{ skill.is_active ? 'Aktif' : 'Nonaktif' }}
              </button>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <button @click="openModal(skill)" class="text-terracotta hover:text-terracotta-dark mr-3">Edit</button>
              <button @click="deleteSkill(skill)" class="text-red-500 hover:text-red-700">Hapus</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Modal -->
  <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center">
    <div class="fixed inset-0 bg-ink/50" @click="closeModal"></div>
    <div class="relative bg-paper rounded-xl p-6 w-full max-w-lg mx-4">
      <h2 class="font-serif text-xl font-bold text-ink mb-4">
        {{ editingSkill ? 'Edit Skill' : 'Tambah Skill' }}
      </h2>
      <form @submit.prevent="submit">
        <div class="space-y-4">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-ink mb-1">Nomor Kategori</label>
              <input v-model="form.category_number" type="text" placeholder="01" required
                     class="w-full px-4 py-2 border border-oat-dark rounded-lg focus:outline-none focus:ring-2 focus:ring-terracotta" />
            </div>
            <div>
              <label class="block text-sm font-medium text-ink mb-1">Label Kategori</label>
              <input v-model="form.category_label" type="text" placeholder="Pengumpulan Informasi" required
                     class="w-full px-4 py-2 border border-oat-dark rounded-lg focus:outline-none focus:ring-2 focus:ring-terracotta" />
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-ink mb-1">Judul (English)</label>
            <input v-model="form.category_title" type="text" placeholder="Reconnaissance" required
                   class="w-full px-4 py-2 border border-oat-dark rounded-lg focus:outline-none focus:ring-2 focus:ring-terracotta" />
          </div>
          <div>
            <label class="block text-sm font-medium text-ink mb-1">Tags</label>
            <div class="flex gap-2 mb-2">
              <input v-model="newTag" type="text" placeholder="Tambah tag..."
                     @keydown.enter.prevent="addTag"
                     class="flex-1 px-4 py-2 border border-oat-dark rounded-lg focus:outline-none focus:ring-2 focus:ring-terracotta" />
              <button type="button" @click="addTag" class="px-4 py-2 bg-oat text-ink rounded-lg hover:bg-oat-dark">+</button>
            </div>
            <div class="flex flex-wrap gap-2">
              <span v-for="(tag, index) in form.tags" :key="index" class="inline-flex items-center gap-1 bg-oat text-taupe text-sm px-2 py-1 rounded">
                {{ tag }}
                <button type="button" @click="removeTag(index)" class="text-taupe hover:text-red-500">×</button>
              </span>
            </div>
          </div>
        </div>
        <div class="flex gap-4 mt-6">
          <button type="submit" :disabled="form.processing"
                  class="px-6 py-2 bg-terracotta text-cream font-medium rounded-full hover:bg-terracotta-dark disabled:opacity-50">
            {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
          </button>
          <button type="button" @click="closeModal"
                  class="px-6 py-2 border border-oat-dark text-taupe font-medium rounded-full hover:bg-oat">
            Batal
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
