<script setup>
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useConfirm } from '@/Composables/useConfirm';

defineOptions({ layout: AdminLayout });

const props = defineProps({
  product: Object,
  roadmapItems: Object,
  stats: Object,
});

const showModal = ref(false);
const editingItem = ref(null);
const { open: confirmOpen } = useConfirm();

const form = useForm({
  title: '',
  description: '',
  status: 'todo',
  priority: 'medium',
  category: 'feature',
  estimated_hours: '',
  due_date: '',
});

const getPriorityColor = (priority) => {
  const colors = {
    low: 'bg-gray-100 text-gray-600',
    medium: 'bg-yellow-100 text-yellow-700',
    high: 'bg-orange-100 text-orange-700',
    critical: 'bg-red-100 text-red-700',
  };
  return colors[priority] || '';
};

const getPriorityLabel = (priority) => {
  const labels = { low: 'Rendah', medium: 'Sedang', high: 'Tinggi', critical: 'Kritis' };
  return labels[priority] || priority;
};

const openCreate = () => {
  editingItem.value = null;
  form.reset();
  showModal.value = true;
};

const submit = () => {
  if (editingItem.value) {
    form.put(route('admin.products.roadmap.update', [props.product.id, editingItem.value.id]), {
      onSuccess: () => { showModal.value = false; form.reset(); },
    });
  } else {
    form.post(route('admin.products.roadmap.store', props.product.id), {
      onSuccess: () => { showModal.value = false; form.reset(); },
    });
  }
};

const updateStatus = (item, newStatus) => {
  useForm().patch(route('admin.products.roadmap.update-status', [props.product.id, item.id]), {
    preserveScroll: true,
    data: { status: newStatus },
  });
};

const deleteItem = async (item) => {
  const confirmed = await confirmOpen({
    message: `Item "${item.title}" akan dihapus dari roadmap. Lanjutkan?`,
    variant: 'danger',
    confirmText: 'Hapus',
    cancelText: 'Batal',
  });

  if (confirmed) {
    router.delete(route('admin.products.roadmap.destroy', [props.product.id, item.id]), {
      preserveScroll: true,
    });
  }
};
</script>

<template>
  <Head title="Roadmap" />

  <div class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between mb-8">
        <div class="flex items-center gap-4">
          <button @click="router.visit(route('admin.products.catalog.index'))" class="text-taupe hover:text-ink">← Kembali</button>
          <h1 class="text-3xl font-fraunces font-bold text-ink">Roadmap: {{ product.name }}</h1>
        </div>
        <button @click="openCreate" class="px-4 py-2 bg-terracotta text-white rounded-lg hover:bg-terracotta/90">
          + Item Baru
        </button>
      </div>

      <!-- Stats -->
      <div class="grid grid-cols-4 gap-4 mb-8">
        <div class="bg-cream rounded-xl p-4 text-center">
          <p class="text-2xl font-bold text-ink">{{ stats.total }}</p>
          <p class="text-sm text-taupe">Total</p>
        </div>
        <div class="bg-yellow-50 rounded-xl p-4 text-center">
          <p class="text-2xl font-bold text-yellow-600">{{ stats.todo }}</p>
          <p class="text-sm text-yellow-700">Todo</p>
        </div>
        <div class="bg-purple-50 rounded-xl p-4 text-center">
          <p class="text-2xl font-bold text-purple-600">{{ stats.in_progress }}</p>
          <p class="text-sm text-purple-700">In Progress</p>
        </div>
        <div class="bg-green-50 rounded-xl p-4 text-center">
          <p class="text-2xl font-bold text-green-600">{{ stats.done }}</p>
          <p class="text-sm text-green-700">Done</p>
        </div>
      </div>

      <!-- Kanban Board -->
      <div class="grid grid-cols-4 gap-4">
        <!-- Todo -->
        <div class="bg-cream rounded-xl p-4">
          <h3 class="font-fraunces font-semibold text-ink mb-4">Todo ({{ roadmapItems.todo?.length || 0 }})</h3>
          <div class="space-y-3">
            <div v-for="item in roadmapItems.todo" :key="item.id" class="bg-paper border border-oat rounded-lg p-3">
              <p class="font-medium text-ink text-sm mb-2">{{ item.title }}</p>
              <div class="flex items-center justify-between">
                <span class="text-xs px-2 py-0.5 rounded" :class="getPriorityColor(item.priority)">
                  {{ getPriorityLabel(item.priority) }}
                </span>
                <div class="flex gap-1">
                  <button @click="updateStatus(item, 'in_progress')" class="text-xs text-blue-600 hover:underline">→</button>
                  <button @click="deleteItem(item)" class="text-xs text-red-600 hover:underline">×</button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- In Progress -->
        <div class="bg-purple-50 rounded-xl p-4">
          <h3 class="font-fraunces font-semibold text-purple-700 mb-4">In Progress ({{ roadmapItems.in_progress?.length || 0 }})</h3>
          <div class="space-y-3">
            <div v-for="item in roadmapItems.in_progress" :key="item.id" class="bg-paper border border-oat rounded-lg p-3">
              <p class="font-medium text-ink text-sm mb-2">{{ item.title }}</p>
              <div class="flex items-center justify-between">
                <span class="text-xs px-2 py-0.5 rounded" :class="getPriorityColor(item.priority)">
                  {{ getPriorityLabel(item.priority) }}
                </span>
                <div class="flex gap-1">
                  <button @click="updateStatus(item, 'done')" class="text-xs text-green-600 hover:underline">✓</button>
                  <button @click="updateStatus(item, 'todo')" class="text-xs text-yellow-600 hover:underline">←</button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Done -->
        <div class="bg-green-50 rounded-xl p-4">
          <h3 class="font-fraunces font-semibold text-green-700 mb-4">Done ({{ roadmapItems.done?.length || 0 }})</h3>
          <div class="space-y-3">
            <div v-for="item in roadmapItems.done" :key="item.id" class="bg-paper border border-green-200 rounded-lg p-3">
              <p class="font-medium text-ink text-sm mb-2 line-through opacity-60">{{ item.title }}</p>
              <div class="flex items-center justify-between">
                <span class="text-xs text-green-600">✓ Selesai</span>
                <button @click="updateStatus(item, 'todo')" class="text-xs text-yellow-600 hover:underline">↩</button>
              </div>
            </div>
          </div>
        </div>

        <!-- Cancelled -->
        <div class="bg-gray-100 rounded-xl p-4">
          <h3 class="font-fraunces font-semibold text-gray-600 mb-4">Cancelled ({{ roadmapItems.cancelled?.length || 0 }})</h3>
          <div class="space-y-3">
            <div v-for="item in roadmapItems.cancelled" :key="item.id" class="bg-paper border border-gray-300 rounded-lg p-3 opacity-60">
              <p class="font-medium text-ink text-sm mb-2 line-through">{{ item.title }}</p>
              <button @click="updateStatus(item, 'todo')" class="text-xs text-gray-600 hover:underline">↩ Restore</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal -->
  <div v-if="showModal" class="fixed inset-0 bg-ink/50 flex items-center justify-center z-50" @click.self="showModal = false">
    <div class="bg-paper rounded-2xl p-6 w-full max-w-md">
      <h2 class="text-xl font-fraunces font-semibold text-ink mb-4">Item Roadmap</h2>
      <form @submit.prevent="submit" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-ink mb-1">Judul *</label>
          <input v-model="form.title" type="text" class="w-full px-4 py-2 border border-oat rounded-lg" required />
        </div>
        <div>
          <label class="block text-sm font-medium text-ink mb-1">Deskripsi</label>
          <textarea v-model="form.description" class="w-full px-4 py-2 border border-oat rounded-lg" rows="2"></textarea>
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-ink mb-1">Prioritas</label>
            <select v-model="form.priority" class="w-full px-4 py-2 border border-oat rounded-lg">
              <option value="low">Rendah</option>
              <option value="medium">Sedang</option>
              <option value="high">Tinggi</option>
              <option value="critical">Kritis</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-ink mb-1">Kategori</label>
            <select v-model="form.category" class="w-full px-4 py-2 border border-oat rounded-lg">
              <option value="feature">Fitur</option>
              <option value="bug">Bug</option>
              <option value="improvement">Perbaikan</option>
              <option value="research">Riset</option>
            </select>
          </div>
        </div>
        <div class="flex gap-3 pt-2">
          <button type="button" @click="showModal = false" class="flex-1 px-4 py-2 border border-oat rounded-lg text-taupe">Batal</button>
          <button type="submit" class="flex-1 px-4 py-2 bg-terracotta text-white rounded-lg">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</template>
