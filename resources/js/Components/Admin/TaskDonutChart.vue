<script setup lang="ts">
import { computed } from 'vue'
import VueApexCharts from 'vue3-apexcharts'
import { ChartPieIcon } from '@heroicons/vue/24/outline'

const props = defineProps<{
  todoCount: number
  inProgressCount: number
  doneCount: number
}>()

const total = computed(() => props.todoCount + props.inProgressCount + props.doneCount)

const chartOptions = computed(() => ({
  chart: {
    type: 'donut' as const,
    background: 'transparent',
    toolbar: { show: false },
  },
  labels: ['To Do', 'In Progress', 'Done'],
  colors: ['#9C9484', '#3b82f6', '#10b981'],
  plotOptions: {
    pie: {
      donut: {
        size: '70%',
        labels: {
          show: true,
          total: {
            show: true,
            label: 'Total',
            color: '#3D3929',
            fontSize: '14px',
            fontFamily: 'Inter, sans-serif',
            formatter: () => `${total.value}`,
          },
        },
      },
    },
  },
  legend: {
    position: 'bottom' as const,
    fontFamily: 'Inter, sans-serif',
    fontSize: '12px',
    labels: { colors: '#6B6456' },
  },
  dataLabels: { enabled: false },
  stroke: { width: 0 },
  tooltip: { enabled: false },
  responsive: [{ breakpoint: 480, options: { legend: { position: 'bottom' } } }],
}))

const chartSeries = computed(() => [props.todoCount, props.inProgressCount, props.doneCount])

const stats = computed(() => [
  { label: 'To Do', count: props.todoCount, color: 'bg-gray-400' },
  { label: 'In Progress', count: props.inProgressCount, color: 'bg-blue-500' },
  { label: 'Done', count: props.doneCount, color: 'bg-emerald-500' },
])
</script>

<template>
  <div class="bg-paper rounded-2xl border border-oat-dark p-5">
    <h3 class="font-serif text-ink text-base mb-4">Progress Task</h3>

    <!-- Chart or Empty State -->
    <div v-if="total > 0" class="space-y-4">
      <VueApexCharts
        type="donut"
        height="200"
        :options="chartOptions"
        :series="chartSeries"
      />

      <!-- Stats Row -->
      <div class="flex justify-center gap-6 pt-2">
        <div v-for="stat in stats" :key="stat.label" class="flex items-center gap-2">
          <span :class="stat.color" class="w-2 h-2 rounded-full"></span>
          <span class="font-mono text-xs text-taupe">{{ stat.label }}:</span>
          <span class="font-mono text-xs font-medium text-ink">{{ stat.count }}</span>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else class="flex flex-col items-center justify-center py-12 text-center">
      <ChartPieIcon class="w-10 h-10 text-taupe-light mb-3" />
      <p class="text-sm text-taupe">Belum ada data task</p>
      <p class="text-xs text-taupe-light mt-1">Tambahkan task untuk melihat progress</p>
    </div>
  </div>
</template>
