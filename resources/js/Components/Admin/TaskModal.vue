<script setup lang="ts">
import { computed, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { XMarkIcon } from '@heroicons/vue/24/outline'

interface Task {
  id: number
  title: string
  description: string | null
  status: 'todo' | 'in_progress' | 'done'
  priority: 'low' | 'medium' | 'high' | 'critical'
  due_date: string | null
}

const props = defineProps<{
  show: boolean
  task: Task | null
  defaultStatus?: 'todo' | 'in_progress' | 'done'
}>()

const emit = defineEmits<{
  close: []
  saved: []
}>()

const isEdit = computed(() => !!props.task)

const form = useForm({
  title: '',
  description: '',
  status: 'todo',
  priority: 'medium',
  due_date: '',
})

watch(() => props.show, (val) => {
  if (val) {
    if (props.task) {
      form.title = props.task.title
      form.description = props.task.description || ''
      form.status = props.task.status
      form.priority = props.task.priority
      form.due_date = props.task.due_date || ''
    } else {
      form.reset()
      form.status = props.defaultStatus || 'todo'
      form.priority = 'medium'
    }
  }
})

const submit = () => {
  if (isEdit.value) {
    form.patch(route('admin.tasks.update', props.task?.id), {
      preserveScroll: true,
      onSuccess: () => {
        emit('saved')
        emit('close')
      }
    })
  } else {
    form.post(route('admin.tasks.store'), {
      preserveScroll: true,
      onSuccess: () => {
        emit('saved')
        emit('close')
      }
    })
  }
}

const handleClose = () => {
  form.reset()
  emit('close')
}
</script>

<template>
  <Teleport to="body">
    <Transition
      enter-active-class="transition ease-out duration-200"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition ease-in duration-150"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div v-if="show" class="fixed inset-0 z-50">
        <!-- Overlay -->
        <div class="fixed inset-0 bg-ink/40 backdrop-blur-sm" @click="handleClose"></div>

        <!-- Dialog -->
        <div class="fixed inset-0 flex items-center justify-center p-4">
          <Transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition ease-in duration-150"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95"
          >
            <div
              v-if="show"
              class="bg-paper rounded-2xl shadow-2xl max-w-md w-full border border-oat-dark"
            >
              <!-- Header -->
              <div class="flex items-center justify-between p-6 border-b border-oat-dark">
                <h2 class="font-serif text-lg text-ink">
                  {{ isEdit ? 'Edit Tugas' : 'Tugas Baru' }}
                </h2>
                <button @click="handleClose" class="p-1 hover:bg-oat rounded-lg transition-colors">
                  <XMarkIcon class="w-5 h-5 text-taupe" />
                </button>
              </div>

              <!-- Body -->
              <form @submit.prevent="submit" class="p-6 space-y-4">
                <!-- Title -->
                <div>
                  <label class="block text-sm font-medium text-ink mb-1">Judul</label>
                  <input
                    v-model="form.title"
                    type="text"
                    required
                    maxlength="255"
                    class="w-full px-4 py-2.5 rounded-xl border border-oat-dark bg-cream/50 focus:border-terracotta focus:ring-2 focus:ring-terracotta/20 outline-none transition-all"
                    placeholder="Nama tugas..."
                  />
                  <p v-if="form.errors.title" class="text-red-500 text-xs mt-1">{{ form.errors.title }}</p>
                </div>

                <!-- Description -->
                <div>
                  <label class="block text-sm font-medium text-ink mb-1">Deskripsi</label>
                  <textarea
                    v-model="form.description"
                    rows="3"
                    maxlength="1000"
                    class="w-full px-4 py-2.5 rounded-xl border border-oat-dark bg-cream/50 focus:border-terracotta focus:ring-2 focus:ring-terracotta/20 outline-none transition-all resize-none"
                    placeholder="Detail tugas (opsional)..."
                  ></textarea>
                  <p v-if="form.errors.description" class="text-red-500 text-xs mt-1">{{ form.errors.description }}</p>
                </div>

                <!-- Priority & Status Row -->
                <div class="grid grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-ink mb-1">Prioritas</label>
                    <select
                      v-model="form.priority"
                      class="w-full px-4 py-2.5 rounded-xl border border-oat-dark bg-cream/50 focus:border-terracotta focus:ring-2 focus:ring-terracotta/20 outline-none transition-all"
                    >
                      <option value="low">Low</option>
                      <option value="medium">Medium</option>
                      <option value="high">High</option>
                      <option value="critical">Critical</option>
                    </select>
                    <p v-if="form.errors.priority" class="text-red-500 text-xs mt-1">{{ form.errors.priority }}</p>
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-ink mb-1">Status</label>
                    <select
                      v-model="form.status"
                      class="w-full px-4 py-2.5 rounded-xl border border-oat-dark bg-cream/50 focus:border-terracotta focus:ring-2 focus:ring-terracotta/20 outline-none transition-all"
                    >
                      <option value="todo">To Do</option>
                      <option value="in_progress">In Progress</option>
                      <option value="done">Done</option>
                    </select>
                    <p v-if="form.errors.status" class="text-red-500 text-xs mt-1">{{ form.errors.status }}</p>
                  </div>
                </div>

                <!-- Due Date -->
                <div>
                  <label class="block text-sm font-medium text-ink mb-1">Tanggal Jatuh Tempo</label>
                  <input
                    v-model="form.due_date"
                    type="date"
                    class="w-full px-4 py-2.5 rounded-xl border border-oat-dark bg-cream/50 focus:border-terracotta focus:ring-2 focus:ring-terracotta/20 outline-none transition-all"
                  />
                  <p v-if="form.errors.due_date" class="text-red-500 text-xs mt-1">{{ form.errors.due_date }}</p>
                </div>

                <!-- Footer -->
                <div class="flex justify-end gap-3 pt-4 border-t border-oat-dark -mx-6 -mb-6 px-6 py-4">
                  <button
                    type="button"
                    @click="handleClose"
                    class="px-5 py-2.5 rounded-full border border-oat-dark text-taupe hover:bg-oat transition-colors"
                  >
                    Batal
                  </button>
                  <button
                    type="submit"
                    :disabled="form.processing"
                    class="px-5 py-2.5 rounded-full bg-terracotta text-cream font-medium hover:bg-terracotta-dark transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                  >
                    <span v-if="form.processing" class="w-4 h-4 border-2 border-cream/30 border-t-cream rounded-full animate-spin"></span>
                    {{ isEdit ? 'Simpan' : 'Tambah' }}
                  </button>
                </div>
              </form>
            </div>
          </Transition>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>
