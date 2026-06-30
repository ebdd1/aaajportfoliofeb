<script setup>
import { ref, computed } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { useConfirm } from '@/Composables/useConfirm';

defineOptions({ layout: AdminLayout });

const props = defineProps({
    posts: Object,
    filters: Object,
});

const search = ref(props.filters?.search || '');
const selectedStatus = ref(props.filters?.status || '');
const { open: confirmOpen } = useConfirm();

const statusOptions = [
    { value: '', label: 'Semua Status' },
    { value: 'draft', label: 'Draft' },
    { value: 'published', label: 'Dipublikasi' },
];

const formatDate = (date) => {
    if (!date) return '-';
    return new Intl.DateTimeFormat('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric'
    }).format(new Date(date));
};

const handleSearch = () => {
    router.get(route('admin.blog.posts.index'), {
        search: search.value,
        status: selectedStatus.value,
    }, { preserveState: true });
};

const toggleFeatured = (post) => {
    router.patch(route('admin.blog.posts.toggle-featured', post.id));
};

const toggleStatus = (post) => {
    router.patch(route('admin.blog.posts.toggle-status', post.id));
};

const deletePost = async (post) => {
    const confirmed = await confirmOpen({
        message: `Post "${post.title}" akan dihapus permanen. Lanjutkan?`,
        variant: 'danger',
        confirmText: 'Hapus',
        cancelText: 'Batal',
    });

    if (confirmed) {
        router.delete(route('admin.blog.posts.destroy', post.id));
    }
};
</script>

<template>
    <div>
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="font-serif text-2xl font-bold text-ink">Blog Posts</h1>
                <p class="text-sm text-taupe mt-1">Kelola artikel blog</p>
            </div>
            <Link
                :href="route('admin.blog.posts.create')"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-terracotta text-cream font-medium rounded-xl hover:bg-terracotta-dark transition-colors"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 5v14M5 12h14"/>
                </svg>
                Tambah Post
            </Link>
        </div>

        <!-- Filters -->
        <div class="mb-6 flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <input
                    v-model="search"
                    type="text"
                    placeholder="Cari post..."
                    class="w-full px-4 py-2.5 bg-paper border border-oat-dark rounded-xl text-ink placeholder-taupe/50 focus:outline-none focus:ring-2 focus:ring-terracotta/50 focus:border-terracotta"
                    @keyup.enter="handleSearch"
                />
            </div>
            <select
                v-model="selectedStatus"
                class="px-4 py-2.5 bg-paper border border-oat-dark rounded-xl text-ink focus:outline-none focus:ring-2 focus:ring-terracotta/50 focus:border-terracotta"
                @change="handleSearch"
            >
                <option v-for="opt in statusOptions" :key="opt.value" :value="opt.value">
                    {{ opt.label }}
                </option>
            </select>
        </div>

        <!-- Posts Table -->
        <div class="bg-paper rounded-2xl border border-oat-dark overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-oat/50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-taupe uppercase tracking-wider">Post</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-taupe uppercase tracking-wider">Kategori</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-taupe uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-taupe uppercase tracking-wider">Views</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-taupe uppercase tracking-wider">Tanggal</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-taupe uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-oat-dark">
                        <tr v-for="post in posts.data" :key="post.id" class="hover:bg-oat/30 transition-colors">
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-lg bg-terracotta/10 flex items-center justify-center overflow-hidden shrink-0">
                                        <img v-if="post.featured_image_url" :src="post.featured_image_url" class="w-full h-full object-cover" />
                                        <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-terracotta" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                                            <polyline points="14 2 14 8 20 8"/>
                                            <line x1="16" y1="13" x2="8" y2="13"/>
                                            <line x1="16" y1="17" x2="8" y2="17"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-medium text-ink">{{ post.title }}</p>
                                        <p class="text-xs text-taupe truncate max-w-[200px]">{{ post.excerpt || '-' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex flex-wrap gap-1">
                                    <span
                                        v-for="cat in (post.categories || []).slice(0, 2)"
                                        :key="cat.id"
                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-oat text-taupe"
                                    >
                                        {{ cat.name }}
                                    </span>
                                    <span v-if="(post.categories || []).length > 2" class="text-xs text-taupe">
                                        +{{ post.categories.length - 2 }}
                                    </span>
                                    <span v-if="!post.categories || post.categories.length === 0" class="text-xs text-taupe">-</span>
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex flex-col gap-1">
                                    <button
                                        @click="toggleStatus(post)"
                                        :class="[
                                            'px-2 py-1 rounded-lg text-xs font-medium transition-colors w-fit',
                                            post.status === 'published'
                                                ? 'bg-green-100 text-green-700 hover:bg-green-200'
                                                : 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200'
                                        ]"
                                    >
                                        {{ post.status === 'published' ? 'Published' : 'Draft' }}
                                    </button>
                                    <button
                                        @click="toggleFeatured(post)"
                                        :class="[
                                            'px-2 py-1 rounded-lg text-xs font-medium transition-colors w-fit',
                                            post.is_featured
                                                ? 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200'
                                                : 'bg-oat text-taupe hover:bg-oat-dark'
                                        ]"
                                    >
                                        {{ post.is_featured ? 'Featured' : 'Normal' }}
                                    </button>
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <span class="text-taupe">{{ post.view_count || 0 }}</span>
                            </td>
                            <td class="px-4 py-4">
                                <span class="text-sm text-taupe">{{ formatDate(post.published_at || post.created_at) }}</span>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <Link
                                        :href="route('admin.blog.posts.edit', post.id)"
                                        class="p-2 text-taupe hover:text-terracotta hover:bg-oat rounded-lg transition-colors"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                                            <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                        </svg>
                                    </Link>
                                    <button
                                        @click="deletePost(post)"
                                        class="p-2 text-taupe hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M3 6h18M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Empty State -->
            <div v-if="posts.data.length === 0" class="p-12 text-center">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-oat flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-taupe" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                    </svg>
                </div>
                <h3 class="font-medium text-ink mb-1">Belum ada post</h3>
                <p class="text-sm text-taupe mb-4">Mulai tambahkan artikel blog pertama Anda</p>
                <Link
                    :href="route('admin.blog.posts.create')"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-terracotta text-cream font-medium rounded-xl hover:bg-terracotta-dark transition-colors"
                >
                    Tambah Post
                </Link>
            </div>

            <!-- Pagination -->
            <div v-if="posts.data.length > 0" class="px-4 py-3 border-t border-oat-dark">
                <div class="flex items-center justify-between">
                    <p class="text-sm text-taupe">
                        Menampilkan {{ posts.from }} - {{ posts.to }} dari {{ posts.total }} post
                    </p>
                    <div class="flex gap-1">
                        <Link
                            v-for="link in posts.links"
                            :key="link.label"
                            :href="link.url || '#'"
                            :class="[
                                'px-3 py-1.5 text-sm rounded-lg transition-colors',
                                link.active
                                    ? 'bg-terracotta text-cream'
                                    : 'text-taupe hover:bg-oat'
                            ]"
                            :preserve-state="true"
                        >
                            <span v-html="link.label"></span>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
