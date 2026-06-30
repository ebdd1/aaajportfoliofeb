<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineOptions({
  layout: AdminLayout,
});

const props = defineProps({
  stats: Object,
});

const form = useForm({
  projects_count: props.stats.projects_count,
  semesters_count: props.stats.semesters_count,
  experiences_count: props.stats.experiences_count,
});

const saved = ref(false);

const submit = () => {
  form.patch(route('admin.stats.update'), {
    onSuccess: () => {
      saved.value = true;
      setTimeout(() => (saved.value = false), 3000);
    },
  });
};
</script>

<template>
  <Head title="Edit Statistik" />

  <div class="py-12">
    <h1 class="font-serif text-3xl font-bold text-ink mb-8">Edit Statistik Hero</h1>

      <div class="bg-paper rounded-xl border border-oat-dark p-6">
        <form @submit.prevent="submit">
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-ink mb-1">Jumlah Proyek Dibangun</label>
              <input v-model="form.projects_count" type="number" min="0" required
                     class="w-full px-4 py-2 border border-oat-dark rounded-lg focus:outline-none focus:ring-2 focus:ring-terracotta" />
            </div>
            <div>
              <label class="block text-sm font-medium text-ink mb-1">Semester Berjalan</label>
              <input v-model="form.semesters_count" type="number" min="0" required
                     class="w-full px-4 py-2 border border-oat-dark rounded-lg focus:outline-none focus:ring-2 focus:ring-terracotta" />
            </div>
            <div>
              <label class="block text-sm font-medium text-ink mb-1">Pengalaman Kerja</label>
              <input v-model="form.experiences_count" type="number" min="0" required
                     class="w-full px-4 py-2 border border-oat-dark rounded-lg focus:outline-none focus:ring-2 focus:ring-terracotta" />
            </div>
          </div>

          <button type="submit" :disabled="form.processing"
                  class="mt-6 px-6 py-2 bg-terracotta text-cream font-medium rounded-full hover:bg-terracotta-dark disabled:opacity-50">
            {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
          </button>
          <span v-if="saved" class="ml-4 text-sm text-green-600 font-medium">Tersimpan!</span>
        </form>
      </div>

      <!-- Success Message -->
      <p v-if="$page.props.flash?.success" class="mt-4 text-sm text-green-600 bg-green-50 px-4 py-2 rounded-lg">
        {{ $page.props.flash.success }}
      </p>
  </div>
</template>
