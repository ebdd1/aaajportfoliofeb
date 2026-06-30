<script setup>
import { ref } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { router, useForm } from '@inertiajs/vue3';
import { useConfirm } from '@/Composables/useConfirm';

defineOptions({ layout: AdminLayout });

const props = defineProps({
    categories: Object,
    filters: Object,
});

const { open: confirmOpen } = useConfirm();

const search = ref(props.filters?.search || '');
const isModalOpen = ref(false);
const editingCategory = ref(null);

const form = useForm({
    name: '',
    slug: '',
    description: '',
});

const openModal = (category = null) => {
    if (category) {
        editingCategory.value = category;
        form.name = category.name;
        form.slug = category.slug || '';
        form.description = category.description || '';
    } else {
        editingCategory.value = null;
        form.reset();
    }
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
    editingCategory.value = null;
    form.reset();
};

const handleSearch = () => {
    router.get(route('admin.blog.categories.index'), {
        search: search.value,
    }, { preserveState: true });
};

const submit = () => {
    if (editingCategory.value) {
        form.post(route('admin.blog.categories.update', editingCategory.value.id), {
            onSuccess: closeModal,
        });
    } else {
        form.post(route('admin.blog.categories.store'), {
            onSuccess: closeModal,
        });
    }
};

const deleteCategory = async (category) => {
    const confirmed = await confirmOpen({
        message: `Kategori "${category.name}" akan dihapus. Post terkait tidak akan dihapus. Lanjutkan?`,
        variant: 'danger',
        confirmText: 'Hapus',
        cancelText: 'Batal',
    });

    if (confirmed) {
        router.delete(route('admin.blog.categories.destroy', category.id));
    }
};
</script>

<template>
    <div>
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="font-serif text-2xl font-bold text-ink">Blog Kategori</h1>
                <p class="text-sm text-taupe mt-1">Kelola kategori blog</p>
            </div>
            <button
                @click="openModal()"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-terracotta text-cream font-medium rounded-xl hover:bg-terracotta-dark transition-colors"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 5v14M5 12h14"/>
                </svg>
                Tambah Kategori
            </button>
        </div>

        <!-- Filters -->
        <div class="mb-6">
            <input
                v-model="search"
                type="text"
                placeholder="Cari kategori..."
                class="w-full px-4 py-2.5 bg-paper border border-oat-dark rounded-xl text-ink placeholder-taupe/50 focus:outline-none focus:ring-2 focus:ring-terracotta/50 focus:border-terracotta"
                @keyup.enter="handleSearch"
            />
        </div>

        <!-- Categories Grid -->
        <div class="bg-paper rounded-2xl border border-oat-dark overflow-hidden">
            <div class="p-6">
                <div v-if="categories.data.length === 0" class="text-center py-12">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-oat flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-taupe" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 19a2 2 0 01-2 2H4a2 2 0 01-2-2V5a2 2 0 012-2h5l2 3h9a2 2 0 012 2z"/>
                        </svg>
                    </div>
                    <h3 class="font-medium text-ink mb-1">Belum ada kategori</h3>
                    <p class="text-sm text-taupe mb-4">Tambahkan kategori untuk mengorganisir post</p>
                    <button
                        @click="openModal()"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-terracotta text-cream font-medium rounded-xl hover:bg-terracotta-dark transition-colors"
                    >
                        Tambah Kategori
                    </button>
                </div>

                <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div
                        v-for="category in categories.data"
                        :key="category.id"
                        class="bg-oat/30 rounded-xl p-4 hover:bg-oat/50 transition-colors"
                    >
                        <div class="flex items-start justify-between">
                            <div class="flex-1 min-w-0">
                                <h3 class="font-medium text-ink truncate">{{ category.name }}</h3>
                                <p class="text-xs text-taupe font-mono truncate mt-0.5">{{ category.slug }}</p>
                                <p v-if="category.description" class="text-sm text-taupe mt-2 line-clamp-2">
                                    {{ category.description }}
                                </p>
                                <p class="text-xs text-taupe mt-2">
                                    {{ category.posts_count || 0 }} post
                                </p>
                            </div>
                            <div class="flex items-center gap-1 ml-3">
                                <button
                                    @click="openModal(category)"
                                    class="p-2 text-taupe hover:text-terracotta hover:bg-oat rounded-lg transition-colors"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                                        <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                    </svg>
                                </button>
                                <button
                                    @click="deleteCategory(category)"
                                    class="p-2 text-taupe hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M3 6h18M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="categories.data.length > 0" class="px-4 py-3 border-t border-oat-dark">
                <div class="flex items-center justify-between">
                    <p class="text-sm text-taupe">
                        Menampilkan {{ categories.from }} - {{ categories.to }} dari {{ categories.total }} kategori
                    </p>
                    <div class="flex gap-1">
                        <button
                            v-for="link in categories.links"
                            :key="link.label"
                            @click="link.url && router.visit(link.url)"
                            :class="[
                                'px-3 py-1.5 text-sm rounded-lg transition-colors',
                                link.active
                                    ? 'bg-terracotta text-cream'
                                    : 'text-taupe hover:bg-oat'
                            ]"
                            :disabled="!link.url"
                        >
                            <span v-html="link.label"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <Teleport to="body">
            <Transition enter-active-class="duration-200 ease-out" enter-from-class="opacity-0"
                enter-to-class="opacity-100" leave-active-class="duration-150 ease-in"
                leave-from-class="opacity-100" leave-to-class="opacity-0">
                <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-start justify-center p-4 overflow-y-auto">
                    <div class="absolute inset-0 bg-ink/60 backdrop-blur-sm" @click="closeModal" />

                    <div class="relative bg-paper rounded-2xl shadow-2xl w-full max-w-md my-8">
                        <div class="flex items-center justify-between px-6 py-4 border-b border-oat-dark">
                            <h2 class="font-serif text-xl font-bold text-ink">
                                {{ editingCategory ? 'Edit Kategori' : 'Tambah Kategori' }}
                            </h2>
                            <button @click="closeModal" class="text-taupe hover:text-ink transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                                </svg>
                            </button>
                        </div>

                        <form @submit.prevent="submit" class="p-6 space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-ink mb-1">Nama <span class="text-red-500">*</span></label>
                                <input
                                    v-model="form.name"
                                    type="text"
                                    placeholder="Nama kategori"
                                    required
                                    class="w-full px-4 py-2.5 border border-oat-dark rounded-lg focus:outline-none focus:ring-2 focus:ring-terracotta"
                                />
                                <p v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-ink mb-1">Slug</label>
                                <input
                                    v-model="form.slug"
                                    type="text"
                                    placeholder="auto-generate-dari-nama"
                                    class="w-full px-4 py-2.5 border border-oat-dark rounded-lg focus:outline-none focus:ring-2 focus:ring-terracotta font-mono text-sm"
                                />
                                <p class="text-xs text-taupe mt-1">Biarkan kosong untuk generate otomatis</p>
                                <p v-if="form.errors.slug" class="text-red-500 text-xs mt-1">{{ form.errors.slug }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-ink mb-1">Deskripsi</label>
                                <textarea
                                    v-model="form.description"
                                    rows="2"
                                    placeholder="Deskripsi kategori (opsional)"
                                    class="w-full px-4 py-2.5 border border-oat-dark rounded-lg focus:outline-none focus:ring-2 focus:ring-terracotta resize-none"
                                ></textarea>
                                <p v-if="form.errors.description" class="text-red-500 text-xs mt-1">{{ form.errors.description }}</p>
                            </div>

                            <div class="flex gap-3 pt-2">
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="flex-1 px-4 py-2.5 bg-terracotta text-cream font-medium rounded-lg hover:bg-terracotta-dark disabled:opacity-50 transition-colors"
                                >
                                    {{ form.processing ? 'Menyimpan...' : (editingCategory ? 'Simpan' : 'Tambah') }}
                                </button>
                                <button
                                    type="button"
                                    @click="closeModal"
                                    class="px-4 py-2.5 border border-oat-dark text-taupe font-medium rounded-lg hover:bg-oat transition-colors"
                                >
                                    Batal
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </Transition>
        </Teleport>
    </div>
</template>
