<script setup>
import { ref } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { router, useForm } from '@inertiajs/vue3';
import { useConfirm } from '@/Composables/useConfirm';

defineOptions({ layout: AdminLayout });

const props = defineProps({
    tags: Object,
    filters: Object,
});

const { open: confirmOpen } = useConfirm();

const search = ref(props.filters?.search || '');
const isModalOpen = ref(false);
const editingTag = ref(null);

const form = useForm({
    name: '',
    slug: '',
});

const openModal = (tag = null) => {
    if (tag) {
        editingTag.value = tag;
        form.name = tag.name;
        form.slug = tag.slug || '';
    } else {
        editingTag.value = null;
        form.reset();
    }
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
    editingTag.value = null;
    form.reset();
};

const handleSearch = () => {
    router.get(route('admin.blog.tags.index'), {
        search: search.value,
    }, { preserveState: true });
};

const submit = () => {
    if (editingTag.value) {
        form.post(route('admin.blog.tags.update', editingTag.value.id), {
            onSuccess: closeModal,
        });
    } else {
        form.post(route('admin.blog.tags.store'), {
            onSuccess: closeModal,
        });
    }
};

const deleteTag = async (tag) => {
    const confirmed = await confirmOpen({
        message: `Tag "${tag.name}" akan dihapus. Post terkait tidak akan dihapus. Lanjutkan?`,
        variant: 'danger',
        confirmText: 'Hapus',
        cancelText: 'Batal',
    });

    if (confirmed) {
        router.delete(route('admin.blog.tags.destroy', tag.id));
    }
};
</script>

<template>
    <div>
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="font-serif text-2xl font-bold text-ink">Blog Tags</h1>
                <p class="text-sm text-taupe mt-1">Kelola tags blog</p>
            </div>
            <button
                @click="openModal()"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-terracotta text-cream font-medium rounded-xl hover:bg-terracotta-dark transition-colors"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 5v14M5 12h14"/>
                </svg>
                Tambah Tag
            </button>
        </div>

        <!-- Filters -->
        <div class="mb-6">
            <input
                v-model="search"
                type="text"
                placeholder="Cari tag..."
                class="w-full px-4 py-2.5 bg-paper border border-oat-dark rounded-xl text-ink placeholder-taupe/50 focus:outline-none focus:ring-2 focus:ring-terracotta/50 focus:border-terracotta"
                @keyup.enter="handleSearch"
            />
        </div>

        <!-- Tags Grid -->
        <div class="bg-paper rounded-2xl border border-oat-dark overflow-hidden">
            <div class="p-6">
                <div v-if="tags.data.length === 0" class="text-center py-12">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-oat flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-taupe" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"/>
                            <line x1="7" y1="7" x2="7.01" y2="7"/>
                        </svg>
                    </div>
                    <h3 class="font-medium text-ink mb-1">Belum ada tag</h3>
                    <p class="text-sm text-taupe mb-4">Tambahkan tag untuk label post</p>
                    <button
                        @click="openModal()"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-terracotta text-cream font-medium rounded-xl hover:bg-terracotta-dark transition-colors"
                    >
                        Tambah Tag
                    </button>
                </div>

                <div v-else class="flex flex-wrap gap-3">
                    <div
                        v-for="tag in tags.data"
                        :key="tag.id"
                        class="inline-flex items-center gap-3 bg-oat/50 rounded-xl px-4 py-3 hover:bg-oat transition-colors group"
                    >
                        <div>
                            <span class="font-medium text-ink">{{ tag.name }}</span>
                            <span class="text-xs text-taupe ml-2">({{ tag.posts_count || 0 }})</span>
                        </div>
                        <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button
                                @click="openModal(tag)"
                                class="p-1 text-taupe hover:text-terracotta transition-colors"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                                    <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                </svg>
                            </button>
                            <button
                                @click="deleteTag(tag)"
                                class="p-1 text-taupe hover:text-red-600 transition-colors"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M3 6h18M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="tags.data.length > 0" class="px-4 py-3 border-t border-oat-dark">
                <div class="flex items-center justify-between">
                    <p class="text-sm text-taupe">
                        Menampilkan {{ tags.from }} - {{ tags.to }} dari {{ tags.total }} tag
                    </p>
                    <div class="flex gap-1">
                        <button
                            v-for="link in tags.links"
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
                                {{ editingTag ? 'Edit Tag' : 'Tambah Tag' }}
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
                                    placeholder="Nama tag"
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

                            <div class="flex gap-3 pt-2">
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="flex-1 px-4 py-2.5 bg-terracotta text-cream font-medium rounded-lg hover:bg-terracotta-dark disabled:opacity-50 transition-colors"
                                >
                                    {{ form.processing ? 'Menyimpan...' : (editingTag ? 'Simpan' : 'Tambah') }}
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
