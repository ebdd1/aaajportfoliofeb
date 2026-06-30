<script setup lang="ts">
import { ref, watch, onMounted, onUnmounted } from 'vue'
import { PlusIcon, InboxIcon } from '@heroicons/vue/24/outline'
import TaskCard from './TaskCard.vue'
import Sortable from 'sortablejs'

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
  title: string
  status: 'todo' | 'in_progress' | 'done'
  tasks: Task[]
  color: string
}>()

const emit = defineEmits<{
  'task-moved': [taskId: number, newStatus: string]
  'add-task': [status: string]
  'edit-task': [task: Task]
  'delete-task': [taskId: number]
}>()

const containerRef = ref<HTMLElement | null>(null)
const localTasks = ref<Task[]>([...props.tasks])

watch(() => props.tasks, (newTasks) => {
  localTasks.value = [...newTasks]
}, { deep: true })

const colorMap: Record<string, string> = {
  todo: 'bg-gray-400',
  in_progress: 'bg-blue-500',
  done: 'bg-emerald-500'
}

let sortableInstance: Sortable | null = null

onMounted(() => {
  if (containerRef.value) {
    sortableInstance = new Sortable(containerRef.value, {
      group: 'tasks',
      animation: 200,
      ghostClass: 'opacity-40',
      chosenClass: 'shadow-xl scale-105',
      dragClass: 'cursor-grabbing',
      handle: '.task-card',
      onEnd: (evt) => {
        const taskId = evt.item.dataset.taskId
        const fromStatus = evt.from.dataset.status
        const toStatus = evt.to.dataset.status

        if (taskId && fromStatus !== toStatus) {
          emit('task-moved', Number(taskId), toStatus || props.status)
        }
      }
    })
  }
})

onUnmounted(() => {
  if (sortableInstance) {
    sortableInstance.destroy()
  }
})
</script>

<template>
  <div class="bg-cream/50 rounded-2xl p-4 border border-oat-dark/50">
    <!-- Header -->
    <div class="flex items-center gap-2 mb-4">
      <span :class="colorMap[status]" class="w-2.5 h-2.5 rounded-full"></span>
      <h3 class="font-sans font-semibold text-ink text-sm">{{ title }}</h3>
      <span class="font-mono text-xs bg-oat text-taupe px-2 py-0.5 rounded-full">
        {{ localTasks.length }}
      </span>
      <button
        @click="emit('add-task', status)"
        class="ml-auto p-1.5 rounded-full hover:bg-terracotta hover:text-cream transition-all duration-200"
      >
        <PlusIcon class="w-4 h-4" />
      </button>
    </div>

    <!-- Tasks List -->
    <div
      ref="containerRef"
      :data-status="status"
      class="min-h-[150px] space-y-3"
    >
      <div
        v-for="task in localTasks"
        :key="task.id"
        :data-task-id="task.id"
        class="task-card cursor-grab active:cursor-grabbing"
      >
        <TaskCard
          :task="task"
          @edit="emit('edit-task', $event)"
          @delete="emit('delete-task', $event)"
        />
      </div>
    </div>

    <!-- Empty State -->
    <div
      v-if="localTasks.length === 0"
      class="min-h-[150px] flex flex-col items-center justify-center text-center py-8"
    >
      <InboxIcon class="w-8 h-8 text-taupe-light mb-2" />
      <p class="text-sm text-taupe">Belum ada tugas</p>
      <p class="text-xs text-taupe-light mt-1">Klik + untuk tambah tugas baru</p>
    </div>
  </div>
</template>
