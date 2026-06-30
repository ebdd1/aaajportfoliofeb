<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useConfirm } from '@/Composables/useConfirm';

defineOptions({
  layout: AdminLayout,
});

defineProps({
  messages: Object,
});

const { open: confirmOpen } = useConfirm();

const markAsRead = (message) => {
  useForm({}).patch(route('admin.messages.read', message.id));
};

const deleteMessage = async (message) => {
  const confirmed = await confirmOpen({
    message: 'Pesan dari ' + message.name + ' akan dihapus. Lanjutkan?',
    variant: 'danger',
    confirmText: 'Hapus',
    cancelText: 'Batal',
  });

  if (confirmed) {
    useForm({}).delete(route('admin.messages.destroy', message.id));
  }
};
</script>

<template>
  <Head title="Pesan Masuk" />

  <div class="py-12">
    <h1 class="font-serif text-3xl font-bold text-ink mb-8">Pesan Masuk</h1>

      <div class="bg-paper rounded-xl border border-oat-dark overflow-hidden">
        <table class="w-full">
          <thead class="bg-oat">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-taupe uppercase tracking-wider">Pengirim</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-taupe uppercase tracking-wider">Pesan</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-taupe uppercase tracking-wider">Tanggal</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-taupe uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-right text-xs font-medium text-taupe uppercase tracking-wider">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-oat-dark">
            <tr v-for="message in messages.data" :key="message.id" class="hover:bg-cream/50">
              <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                  <div :class="message.is_read ? 'bg-oat' : 'bg-terracotta/10'" class="w-10 h-10 rounded-full flex items-center justify-center">
                    <span class="font-medium" :class="message.is_read ? 'text-taupe' : 'text-terracotta'">
                      {{ message.name.charAt(0).toUpperCase() }}
                    </span>
                  </div>
                  <div>
                    <p class="font-medium text-ink" :class="message.is_read ? '' : 'font-semibold'">{{ message.name }}</p>
                    <p class="text-sm text-taupe">{{ message.email }}</p>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4">
                <p class="text-ink truncate max-w-md">{{ message.message }}</p>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-taupe">
                {{ message.created_at }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="message.is_read ? 'bg-oat text-taupe' : 'bg-terracotta text-cream'" class="px-2 py-1 rounded text-xs font-medium">
                  {{ message.is_read ? 'Dibaca' : 'Baru' }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <button v-if="!message.is_read" @click="markAsRead(message)" class="text-terracotta hover:text-terracotta-dark mr-3 cursor-pointer">Tandai Dibaca</button>
                <button @click="deleteMessage(message)" class="text-red-500 hover:text-red-700">Hapus</button>
              </td>
            </tr>
          </tbody>
        </table>

        <!-- Pagination -->
        <div v-if="messages.links" class="px-6 py-4 border-t border-oat-dark flex justify-between items-center">
          <p class="text-sm text-taupe">
            Menampilkan {{ messages.from }} - {{ messages.to }} dari {{ messages.total }}
          </p>
          <div class="flex gap-2">
            <Link v-for="(link, index) in messages.links" :key="index"
                  :href="link.url"
                  :class="link.active ? 'bg-terracotta text-cream' : 'bg-oat text-taupe hover:bg-oat-dark'"
                  class="px-3 py-1 rounded text-sm font-medium"
                  v-html="link.label" />
          </div>
        </div>
      </div>
  </div>
</template>
