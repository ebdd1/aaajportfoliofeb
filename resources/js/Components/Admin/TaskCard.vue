<script setup lang="ts">
import { computed } from 'vue'
import { CalendarDaysIcon, TrashIcon } from '@heroicons/vue/24/outline'
import type { ComputedRef, Ref } from 'vue'

interface Task {
  id: number
  title: string
  description: string | null
  status: 'todo' | 'in_progress' | 'done'
  priority: 'low' | 'medium' | 'high' | 'critical'
  due_date: string | null
  created_at: string
}

const props = defineProps<{ task: Task }>()
const emit = defineEmits<{ edit: [task: Task]; delete: [id: number] }>()

const priorityMap = {
  critical: { bg: 'bg-red-100', text: 'text-red-700' },
  high: { bg: 'bg-orange-100', text: 'text-orange-700' },
  medium: { bg: 'bg-yellow-100', text: 'text-yellow-700' },
  low: { bg: 'bg-green-100', text: 'text-green-700' }
}

const priorityLabel = { critical: 'Critical', high: 'High', medium: 'Medium', low: 'Low' }

const styles = computed(() => priorityMap[props.task.priority] || priorityMap.medium)
const label = computed(() => priorityLabel[props.task.priority] || 'Medium')

const isOverdue = computed(() => {
  if (!props.task.due_date) return false
  return new Date(props.task.due_date) < new Date()
})

const formatDate = computed(() => {
  if (!props.task.due_date) return null
  return new Date(props.task.due_date).toLocaleDateString('id-ID', { day: 'numeric', month: 'short' })
})

const onDelete = () => {
  if (confirm('Hapus tugas ini?')) {
    emit('delete', props.task.id)
  }
}
</script>

<template>
  <div
    class="bg-paper rounded-xl border border-oat-dark p-4 cursor-pointer hover:shadow-md transition-all select-none"
    @click="emit('edit', task)"
  >
    <div class="flex justify-between items-start mb-2">
      <span class="font-mono text-xs uppercase px-2 py-0.5 rounded" :class="[styles.bg, styles.text]">
        {{ label }}
      </span>
      <button @click.stop="onDelete" class="p-1 rounded hover:bg-red-50">
        <TrashIcon class="w-4 h-4" :class="isOverdue ? 'text-red-400' : 'text-taupe-light'" />
      </button>
    </div>

    <h4 class="font-medium text-sm text-ink line-clamp-2 mb-1">{{ task.title }}</h4>

    <p v-if="task.description" class="text-xs text-taupe line-clamp-2 mb-3">
      {{ task.description }}
    </p>
    <div v-else class="mb-3"></div>

    <div class="flex items-center justify-between text-xs">
      <div v-if="task.due_date" class="flex items-center gap-1" :class="isOverdue ? 'text-red-500' : 'text-taupe'">
        <CalendarDaysIcon class="w-3.5 h-3.5" />
        <span>{{ isOverdue ? 'Terlambat' : formatDate }}</span>
      </div>
      <div v-else></div>
    </div>
  </div>
</template>
