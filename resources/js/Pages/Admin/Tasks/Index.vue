<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import {
  PlusIcon,
  ClipboardDocumentListIcon,
  ArrowPathIcon,
  CheckCircleIcon,
  ExclamationCircleIcon,
  ClockIcon,
  CalendarDaysIcon
} from '@heroicons/vue/24/outline'
import KanbanColumn from '@/Components/Admin/KanbanColumn.vue'
import TaskDonutChart from '@/Components/Admin/TaskDonutChart.vue'
import TaskModal from '@/Components/Admin/TaskModal.vue'

defineOptions({ layout: AdminLayout })

interface Task {
  id: number
  title: string
  description: string | null
  status: 'todo' | 'in_progress' | 'done'
  priority: 'low' | 'medium' | 'high' | 'critical'
  due_date: string | null
  created_at: string
}

const props = defineProps<{
  tasks: {
    todo: Task[]
    in_progress: Task[]
    done: Task[]
  }
}>()

// Modal state
const showModal = ref(false)
const selectedTask = ref<Task | null>(null)
const defaultStatus = ref<'todo' | 'in_progress' | 'done'>('todo')

// Keyboard shortcuts
const handleKeydown = (e: KeyboardEvent) => {
  if (showModal.value) return
  if ((e.target as HTMLElement)?.tagName === 'INPUT' || (e.target as HTMLElement)?.tagName === 'TEXTAREA') return

  switch (e.key.toLowerCase()) {
    case 'n':
      e.preventDefault()
      openCreate('todo')
      break
  }
}

onMounted(() => window.addEventListener('keydown', handleKeydown))
onUnmounted(() => window.removeEventListener('keydown', handleKeydown))

// Stats computed
const totalTasks = computed(() => props.tasks.todo.length + props.tasks.in_progress.length + props.tasks.done.length)
const inProgressCount = computed(() => props.tasks.in_progress.length)
const doneTodayCount = computed(() => {
  const today = new Date().toDateString()
  return props.tasks.done.filter(t => new Date(t.created_at).toDateString() === today).length
})
const overdueCount = computed(() => {
  const today = new Date().toISOString().split('T')[0]
  return [...props.tasks.todo, ...props.tasks.in_progress].filter(t => t.due_date && t.due_date < today).length
})

// Handlers
const openCreate = (status: 'todo' | 'in_progress' | 'done' = 'todo') => {
  selectedTask.value = null
  defaultStatus.value = status
  showModal.value = true
}

const openEdit = (task: Task) => {
  selectedTask.value = task
  showModal.value = true
}

const handleSaved = () => {}

const handleTaskMoved = (taskId: number, newStatus: string) => {
  router.patch(route('admin.tasks.update', taskId), { status: newStatus }, { preserveScroll: true })
}

const handleDelete = (taskId: number) => {
  if (confirm('Yakin hapus tugas ini?')) {
    router.delete(route('admin.tasks.destroy', taskId), { preserveScroll: true })
  }
}
</script>

<template>
  <Head title="Manajemen Waktu" />

  <div class="py-6 lg:py-8 space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
      <div>
        <h1 class="font-serif text-2xl lg:text-3xl font-bold text-ink tracking-tight">Manajemen Waktu</h1>
        <p class="text-sm text-taupe mt-1">Kelola tugas harian dan jadwal dengan efisien</p>
      </div>
      <button
        @click="openCreate('todo')"
        class="group flex items-center gap-2 bg-terracotta text-cream rounded-full pl-4 pr-5 py-2.5 font-medium shadow-sm hover:shadow-md hover:bg-terracotta-dark active:scale-95 transition-all duration-200"
      >
        <PlusIcon class="w-4 h-4" />
        <span class="hidden sm:inline">Tugas Baru</span>
        <span class="sm:hidden">Baru</span>
      </button>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-4">
      <div class="group bg-paper rounded-xl border border-oat-dark p-4 hover:shadow-md transition-all duration-200 cursor-default">
        <div class="flex items-start justify-between">
          <div class="p-2 rounded-lg bg-terracotta/10">
            <ClipboardDocumentListIcon class="w-4 h-4 text-terracotta" />
          </div>
          <span class="text-xs text-taupe font-mono opacity-60">Total</span>
        </div>
        <p class="font-serif text-2xl lg:text-3xl font-bold text-ink mt-3">{{ totalTasks }}</p>
        <p class="text-xs text-taupe mt-0.5">tugas</p>
      </div>

      <div class="group bg-paper rounded-xl border border-oat-dark p-4 hover:shadow-md transition-all duration-200 cursor-default">
        <div class="flex items-start justify-between">
          <div class="p-2 rounded-lg bg-blue-50">
            <ArrowPathIcon class="w-4 h-4 text-blue-500" />
          </div>
          <span class="text-xs text-taupe font-mono opacity-60">Progress</span>
        </div>
        <p class="font-serif text-2xl lg:text-3xl font-bold text-ink mt-3">{{ inProgressCount }}</p>
        <p class="text-xs text-taupe mt-0.5">sedang dikerjakan</p>
      </div>

      <div class="group bg-paper rounded-xl border border-oat-dark p-4 hover:shadow-md transition-all duration-200 cursor-default">
        <div class="flex items-start justify-between">
          <div class="p-2 rounded-lg bg-emerald-50">
            <CheckCircleIcon class="w-4 h-4 text-emerald-500" />
          </div>
          <span class="text-xs text-taupe font-mono opacity-60">Selesai</span>
        </div>
        <p class="font-serif text-2xl lg:text-3xl font-bold text-ink mt-3">{{ doneTodayCount }}</p>
        <p class="text-xs text-taupe mt-0.5">hari ini</p>
      </div>

      <div class="group bg-paper rounded-xl border border-oat-dark p-4 hover:shadow-md transition-all duration-200 cursor-default">
        <div class="flex items-start justify-between">
          <div :class="['p-2 rounded-lg', overdueCount > 0 ? 'bg-red-50' : 'bg-oat']">
            <ExclamationCircleIcon :class="['w-4 h-4', overdueCount > 0 ? 'text-red-500' : 'text-taupe']" />
          </div>
          <span :class="['text-xs font-mono opacity-60', overdueCount > 0 ? 'text-red-500' : 'text-taupe']">Urgent</span>
        </div>
        <p :class="['font-serif text-2xl lg:text-3xl font-bold mt-3', overdueCount > 0 ? 'text-red-500' : 'text-ink']">
          {{ overdueCount }}
        </p>
        <p class="text-xs mt-0.5 text-taupe">terlambat</p>
      </div>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
      <!-- Chart Panel -->
      <div class="xl:col-span-3 order-2 xl:order-1">
        <div class="sticky top-6">
          <TaskDonutChart
            :todo-count="tasks.todo.length"
            :in-progress-count="tasks.in_progress.length"
            :done-count="tasks.done.length"
          />
        </div>
      </div>

      <!-- Kanban Board -->
      <div class="xl:col-span-9 order-1 xl:order-2">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
          <KanbanColumn
            title="To Do"
            status="todo"
            :tasks="tasks.todo"
            color="gray"
            @task-moved="handleTaskMoved"
            @add-task="openCreate"
            @edit-task="openEdit"
            @delete-task="handleDelete"
          />
          <KanbanColumn
            title="In Progress"
            status="in_progress"
            :tasks="tasks.in_progress"
            color="blue"
            @task-moved="handleTaskMoved"
            @add-task="openCreate"
            @edit-task="openEdit"
            @delete-task="handleDelete"
          />
          <KanbanColumn
            title="Done"
            status="done"
            :tasks="tasks.done"
            color="green"
            @task-moved="handleTaskMoved"
            @add-task="openCreate"
            @edit-task="openEdit"
            @delete-task="handleDelete"
          />
        </div>
      </div>
    </div>
  </div>

  <!-- Modal -->
  <TaskModal
    :show="showModal"
    :task="selectedTask"
    :default-status="defaultStatus"
    @close="showModal = false"
    @saved="handleSaved"
  />
</template>
