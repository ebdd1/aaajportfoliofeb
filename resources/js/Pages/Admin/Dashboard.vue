<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineOptions({
  layout: AdminLayout,
});

defineProps({
  stats: Object,
  recentMessages: Array,
  finance_summary: Object,
  product_summary: Object,
});

const formatRupiah = (amount) => {
  if (!amount) return 'Rp 0';
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(amount);
};
</script>

<template>
  <Head title="Dashboard" />

  <div class="py-12">
      <h1 class="font-serif text-3xl font-bold text-ink mb-8">Dashboard</h1>

      <!-- Stats Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-paper rounded-xl p-6 border border-oat-dark">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-taupe">Total Pesan</p>
              <p class="font-serif text-3xl font-bold text-ink mt-1">{{ stats.total_messages }}</p>
            </div>
            <div class="w-12 h-12 bg-terracotta/10 rounded-full flex items-center justify-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-terracotta" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
              </svg>
            </div>
          </div>
          <p v-if="stats.unread_messages > 0" class="text-sm text-terracotta mt-3">{{ stats.unread_messages }} belum dibaca</p>
        </div>

        <div class="bg-paper rounded-xl p-6 border border-oat-dark">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-taupe">Proyek Aktif</p>
              <p class="font-serif text-3xl font-bold text-ink mt-1">{{ stats.total_projects }}</p>
            </div>
            <div class="w-12 h-12 bg-terracotta/10 rounded-full flex items-center justify-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-terracotta" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>
              </svg>
            </div>
          </div>
        </div>

        <div class="bg-paper rounded-xl p-6 border border-oat-dark">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-taupe">Sertifikat</p>
              <p class="font-serif text-3xl font-bold text-ink mt-1">{{ stats.total_certs }}</p>
            </div>
            <div class="w-12 h-12 bg-terracotta/10 rounded-full flex items-center justify-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-terracotta" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="8" r="6"/><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/>
              </svg>
            </div>
          </div>
        </div>

        <div class="bg-paper rounded-xl p-6 border border-oat-dark">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-taupe">Keahlian</p>
              <p class="font-serif text-3xl font-bold text-ink mt-1">{{ stats.total_skills }}</p>
            </div>
            <div class="w-12 h-12 bg-terracotta/10 rounded-full flex items-center justify-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-terracotta" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/>
              </svg>
            </div>
          </div>
        </div>
      </div>

      <!-- Finance & Product Summary -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <!-- Finance Summary -->
        <div class="bg-cream rounded-xl p-6 border border-oat-dark">
          <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 bg-terracotta/10 rounded-lg flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-terracotta" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <rect x="2" y="6" width="20" height="12" rx="2"/>
                  <path d="M12 12h.01"/><path d="M17 12h.01"/><path d="M7 12h.01"/>
                </svg>
              </div>
              <h3 class="font-fraunces font-semibold text-ink">Keuangan</h3>
            </div>
            <Link :href="route('admin.finance.dashboard')" class="text-sm text-terracotta hover:text-terracotta/80">Kelola →</Link>
          </div>
          <div class="space-y-3">
            <div class="flex justify-between items-center">
              <span class="text-sm text-taupe">Total Saldo</span>
              <span class="font-bold text-ink">{{ formatRupiah(finance_summary?.total_balance) }}</span>
            </div>
            <div class="flex justify-between items-center">
              <span class="text-sm text-taupe">Pemasukan Bulan Ini</span>
              <span class="font-medium text-green-600">{{ formatRupiah(finance_summary?.income_this_month) }}</span>
            </div>
            <div class="flex justify-between items-center">
              <span class="text-sm text-taupe">Pengeluaran Bulan Ini</span>
              <span class="font-medium text-red-600">{{ formatRupiah(finance_summary?.expense_this_month) }}</span>
            </div>
            <div v-if="finance_summary?.outstanding_invoices > 0" class="flex justify-between items-center">
              <span class="text-sm text-taupe">Invoice Outstanding</span>
              <span class="px-2 py-0.5 bg-red-100 text-red-700 rounded-full text-xs font-medium">
                {{ finance_summary.outstanding_invoices }} ({{ formatRupiah(finance_summary.outstanding_amount) }})
              </span>
            </div>
          </div>
        </div>

        <!-- Product Summary -->
        <div class="bg-cream rounded-xl p-6 border border-oat-dark">
          <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 bg-terracotta/10 rounded-lg flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-terracotta" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7-4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                  <polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/>
                </svg>
              </div>
              <h3 class="font-fraunces font-semibold text-ink">Produk</h3>
            </div>
            <Link :href="route('admin.products.dashboard')" class="text-sm text-terracotta hover:text-terracotta/80">Kelola →</Link>
          </div>
          <div class="space-y-3">
            <div class="flex justify-between items-center">
              <span class="text-sm text-taupe">Produk Aktif</span>
              <span class="font-bold text-ink">{{ product_summary?.active_products || 0 }}</span>
            </div>
            <div v-if="product_summary?.new_orders > 0" class="flex justify-between items-center">
              <span class="text-sm text-taupe">Order Baru</span>
              <span class="px-2 py-0.5 bg-yellow-100 text-yellow-700 rounded-full text-xs font-medium">
                {{ product_summary.new_orders }} order
              </span>
            </div>
            <div class="flex justify-between items-center">
              <span class="text-sm text-taupe">Revenue Bulan Ini</span>
              <span class="font-medium text-green-600">{{ formatRupiah(product_summary?.revenue_this_month) }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Messages -->
      <div class="bg-paper rounded-xl border border-oat-dark">
        <div class="px-6 py-4 border-b border-oat-dark flex justify-between items-center">
          <h2 class="font-serif text-lg font-semibold text-ink">Pesan Terbaru</h2>
          <Link :href="route('admin.messages.index')" class="text-sm text-terracotta hover:text-terracotta-dark">
            Lihat semua
          </Link>
        </div>
        <div v-if="recentMessages && recentMessages.length > 0" class="divide-y divide-oat-dark">
          <div v-for="message in recentMessages" :key="message.id" class="px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-4">
              <div :class="message.is_read ? 'bg-oat' : 'bg-terracotta/10'" class="w-10 h-10 rounded-full flex items-center justify-center">
                <span class="font-medium" :class="message.is_read ? 'text-taupe' : 'text-terracotta'">
                  {{ message.name.charAt(0).toUpperCase() }}
                </span>
              </div>
              <div>
                <p class="font-medium text-ink" :class="message.is_read ? '' : 'font-semibold'">{{ message.name }}</p>
                <p class="text-sm text-taupe truncate max-w-xs">{{ message.message }}</p>
              </div>
            </div>
            <div class="text-right">
              <p class="text-xs text-taupe">{{ message.created_at }}</p>
              <Link v-if="!message.is_read" :href="route('admin.messages.show', message.id)"
                    class="text-xs text-terracotta hover:text-terracotta-dark">
                Baca
              </Link>
            </div>
          </div>
        </div>
        <div v-else class="px-6 py-12 text-center">
          <p class="text-taupe">Belum ada pesan masuk.</p>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="mt-8 grid grid-cols-1 md:grid-cols-4 gap-4">
        <Link :href="route('admin.projects.index')"
              class="bg-paper rounded-xl p-6 border border-oat-dark hover:border-terracotta transition-colors">
          <h3 class="font-medium text-ink mb-1">Kelola Proyek</h3>
          <p class="text-sm text-taupe">Tambah atau edit proyek portfolio</p>
        </Link>
        <Link :href="route('admin.finance.dashboard')"
              class="bg-paper rounded-xl p-6 border border-oat-dark hover:border-terracotta transition-colors">
          <h3 class="font-medium text-ink mb-1">Keuangan</h3>
          <p class="text-sm text-taupe">Kelola transaksi & invoice</p>
        </Link>
        <Link :href="route('admin.products.dashboard')"
              class="bg-paper rounded-xl p-6 border border-oat-dark hover:border-terracotta transition-colors">
          <h3 class="font-medium text-ink mb-1">Produk</h3>
          <p class="text-sm text-taupe">Kelola katalog & order</p>
        </Link>
        <Link :href="route('admin.profile.edit')"
              class="bg-paper rounded-xl p-6 border border-oat-dark hover:border-terracotta transition-colors">
          <h3 class="font-medium text-ink mb-1">Edit Profil</h3>
          <p class="text-sm text-taupe">Update informasi profil dan bio</p>
        </Link>
      </div>
  </div>
</template>
