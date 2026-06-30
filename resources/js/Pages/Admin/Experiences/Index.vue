<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useConfirm } from '@/Composables/useConfirm';

defineOptions({
  layout: AdminLayout,
});

defineProps({
  experiences: Array,
});

const form = useForm({
  period: '',
  role: '',
  organization: '',
  description: '',
});

const isModalOpen = ref(false);
const editingExperience = ref(null);
const { open: confirmOpen } = useConfirm();

const modalTitle = computed(() => editingExperience.value ? 'Edit Pengalaman' : 'Tambah Pengalaman');

const openModal = (experience = null) => {
  if (experience) {
    editingExperience.value = experience;
    form.period = experience.period;
    form.role = experience.role;
    form.organization = experience.organization;
    form.description = experience.description || '';
  } else {
    editingExperience.value = null;
    form.reset();
  }
  isModalOpen.value = true;
};

const closeModal = () => {
  isModalOpen.value = false;
  editingExperience.value = null;
  form.reset();
};

const submit = () => {
  if (editingExperience.value) {
    form.put(route('admin.experiences.update', editingExperience.value.id), {
      onSuccess: closeModal,
    });
  } else {
    form.post(route('admin.experiences.store'), {
      onSuccess: closeModal,
    });
  }
};

const deleteExperience = async (experience) => {
  const confirmed = await confirmOpen({
    message: 'Pengalaman akan dihapus permanen. Lanjutkan?',
    variant: 'danger',
    confirmText: 'Hapus',
    cancelText: 'Batal',
  });

  if (confirmed) {
    form.delete(route('admin.experiences.destroy', experience.id));
  }
};

const toggleExperience = (experience) => {
  useForm({}).patch(route('admin.experiences.toggle', experience.id));
};
</script>

<template>
  <Head title="Kelola Pengalaman" />

  <div class="py-12">
    <div class="flex justify-between items-center mb-8">
      <h1 class="font-serif text-3xl font-bold text-ink">Kelola Pengalaman</h1>
      <button
        @click="openModal()"
        class="px-4 py-2 bg-terracotta text-cream font-medium rounded-full hover:bg-terracotta-dark transition-colors"
      >
        Tambah Pengalaman
      </button>
    </div>

    <!-- Empty State -->
    <div v-if="experiences.length === 0" class="bg-paper rounded-xl border border-oat-dark p-12 text-center">
      <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-oat flex items-center justify-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-taupe" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <rect x="2" y="7" width="20" height="14" rx="2" ry="2"/>
          <path d="M16 21V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v16"/>
        </svg>
      </div>
      <h3 class="font-serif text-lg font-semibold text-ink mb-2">Belum Ada Pengalaman</h3>
      <p class="text-taupe mb-6">Tambahkan pengalaman kerja atau organisasi Anda.</p>
      <button
        @click="openModal()"
        class="px-6 py-2 bg-terracotta text-cream font-medium rounded-full hover:bg-terracotta-dark transition-colors"
      >
        Tambah Pengalaman
      </button>
    </div>

    <!-- Experiences Table -->
    <div v-else class="bg-paper rounded-xl border border-oat-dark overflow-hidden">
      <table class="w-full">
        <thead class="bg-oat">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-taupe uppercase tracking-wider">Periode</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-taupe uppercase tracking-wider">Posisi</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-taupe uppercase tracking-wider">Organisasi</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-taupe uppercase tracking-wider">Deskripsi</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-taupe uppercase tracking-wider">Status</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-taupe uppercase tracking-wider">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-oat-dark">
          <tr v-for="exp in experiences" :key="exp.id" class="hover:bg-cream/50">
            <td class="px-6 py-4 whitespace-nowrap">
              <span class="font-mono text-xs text-terracotta bg-terracotta/10 px-2 py-1 rounded">{{ exp.period }}</span>
            </td>
            <td class="px-6 py-4 text-ink font-medium">{{ exp.role }}</td>
            <td class="px-6 py-4 text-taupe">{{ exp.organization }}</td>
            <td class="px-6 py-4 text-taupe text-sm max-w-xs truncate">{{ exp.description || '-' }}</td>
            <td class="px-6 py-4 whitespace-nowrap">
              <button
                @click="toggleExperience(exp)"
                :class="exp.is_active ? 'text-green-600' : 'text-taupe'"
                class="text-sm font-medium hover:underline"
              >
                {{ exp.is_active ? 'Aktif' : 'Nonaktif' }}
              </button>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <button
                @click="openModal(exp)"
                class="text-terracotta hover:text-terracotta-dark mr-4"
              >
                Edit
              </button>
              <button
                @click="deleteExperience(exp)"
                class="text-red-500 hover:text-red-700"
              >
                Hapus
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Modal -->
  <Teleport to="body">
    <Transition
      enter-active-class="duration-200 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="duration-150 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-ink/60 backdrop-blur-sm" @click="closeModal"></div>

        <!-- Modal -->
        <div class="relative bg-paper rounded-2xl shadow-2xl w-full max-w-lg">
          <div class="p-6 border-b border-oat-dark">
            <h2 class="font-serif text-xl font-bold text-ink">{{ modalTitle }}</h2>
          </div>

          <form @submit.prevent="submit" class="p-6">
            <div class="space-y-4">
              <!-- Period -->
              <div>
                <label class="block text-sm font-medium text-ink mb-1">Periode</label>
                <input
                  v-model="form.period"
                  type="text"
                  placeholder="2022 - Sekarang"
                  required
                  class="w-full px-4 py-2.5 border border-oat-dark rounded-lg focus:outline-none focus:ring-2 focus:ring-terracotta"
                />
                <p v-if="form.errors.period" class="text-red-500 text-xs mt-1">{{ form.errors.period }}</p>
              </div>

              <!-- Role -->
              <div>
                <label class="block text-sm font-medium text-ink mb-1">Posisi / Jabatan</label>
                <input
                  v-model="form.role"
                  type="text"
                  placeholder="Web Developer"
                  required
                  class="w-full px-4 py-2.5 border border-oat-dark rounded-lg focus:outline-none focus:ring-2 focus:ring-terracotta"
                />
                <p v-if="form.errors.role" class="text-red-500 text-xs mt-1">{{ form.errors.role }}</p>
              </div>

              <!-- Organization -->
              <div>
                <label class="block text-sm font-medium text-ink mb-1">Organisasi / Perusahaan</label>
                <input
                  v-model="form.organization"
                  type="text"
                  placeholder="PT Contoh Indonesia"
                  required
                  class="w-full px-4 py-2.5 border border-oat-dark rounded-lg focus:outline-none focus:ring-2 focus:ring-terracotta"
                />
                <p v-if="form.errors.organization" class="text-red-500 text-xs mt-1">{{ form.errors.organization }}</p>
              </div>

              <!-- Description -->
              <div>
                <label class="block text-sm font-medium text-ink mb-1">Deskripsi (Opsional)</label>
                <textarea
                  v-model="form.description"
                  rows="3"
                  placeholder="Jelaskan tanggung jawab dan pencapaian Anda..."
                  class="w-full px-4 py-2.5 border border-oat-dark rounded-lg focus:outline-none focus:ring-2 focus:ring-terracotta resize-none"
                ></textarea>
                <p v-if="form.errors.description" class="text-red-500 text-xs mt-1">{{ form.errors.description }}</p>
              </div>
            </div>

            <!-- Actions -->
            <div class="flex gap-3 mt-6">
              <button
                type="submit"
                :disabled="form.processing"
                class="flex-1 px-4 py-2.5 bg-terracotta text-cream font-medium rounded-full hover:bg-terracotta-dark disabled:opacity-50 transition-colors"
              >
                {{ form.processing ? 'Menyimpan...' : (editingExperience ? 'Simpan' : 'Tambah') }}
              </button>
              <button
                type="button"
                @click="closeModal"
                class="px-4 py-2.5 border border-oat-dark text-taupe font-medium rounded-full hover:bg-oat transition-colors"
              >
                Batal
              </button>
            </div>
          </form>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>
