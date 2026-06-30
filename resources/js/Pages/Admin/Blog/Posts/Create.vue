<script setup>
import { ref } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, useForm, router } from '@inertiajs/vue3';
import Editor from '@/Components/Blog/Editor.vue';

defineOptions({ layout: AdminLayout });

const props = defineProps({
    categories: Array,
    tags: Array,
});

const form = useForm({
    title: '',
    slug: '',
    excerpt: '',
    content: '',
    featured_image: null,
    status: 'draft',
    published_at: '',
    is_featured: false,
    category_ids: [],
    tag_ids: [],
});

const imagePreview = ref(null);
const newCategory = ref('');
const showNewCategory = ref(false);
const newTag = ref('');
const showNewTag = ref(false);

const statusOptions = [
    { value: 'draft', label: 'Draft' },
    { value: 'published', label: 'Dipublikasi' },
];

const handleImage = (e) => {
    const file = e.target.files[0];
    if (!file) return;
    form.featured_image = file;
    imagePreview.value = URL.createObjectURL(file);
};

const removeImage = () => {
    form.featured_image = null;
    imagePreview.value = null;
};

const toggleCategory = (categoryId) => {
    const idx = form.category_ids.indexOf(categoryId);
    if (idx === -1) {
        form.category_ids.push(categoryId);
    } else {
        form.category_ids.splice(idx, 1);
    }
};

const toggleTag = (tagId) => {
    const idx = form.tag_ids.indexOf(tagId);
    if (idx === -1) {
        form.tag_ids.push(tagId);
    } else {
        form.tag_ids.splice(idx, 1);
    }
};

const submit = () => {
    form.post(route('admin.blog.posts.store'), {
        forceFormData: true,
        onSuccess: () => {
            // Success handled by redirect
        },
    });
};

const goBack = () => {
    router.visit(route('admin.blog.posts.index'));
};
</script>

<template>
    <div>
        <div class="mb-6">
            <button
                @click="goBack"
                class="inline-flex items-center gap-2 text-taupe hover:text-ink transition-colors mb-4"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
                Kembali
            </button>
            <h1 class="font-serif text-2xl font-bold text-ink">Tambah Post Baru</h1>
            <p class="text-sm text-taupe mt-1">Buat artikel blog baru</p>
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Title -->
                    <div class="bg-paper rounded-xl border border-oat-dark p-6">
                        <h2 class="font-serif text-lg font-semibold text-ink mb-4">Konten</h2>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-ink mb-1">Judul <span class="text-red-500">*</span></label>
                                <input
                                    v-model="form.title"
                                    type="text"
                                    placeholder="Judul artikel"
                                    required
                                    class="w-full px-4 py-2.5 border border-oat-dark rounded-lg focus:outline-none focus:ring-2 focus:ring-terracotta"
                                />
                                <p v-if="form.errors.title" class="text-red-500 text-xs mt-1">{{ form.errors.title }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-ink mb-1">Slug</label>
                                <input
                                    v-model="form.slug"
                                    type="text"
                                    placeholder="auto-generate-dari-judul"
                                    class="w-full px-4 py-2.5 border border-oat-dark rounded-lg focus:outline-none focus:ring-2 focus:ring-terracotta font-mono text-sm"
                                />
                                <p class="text-xs text-taupe mt-1">Biarkan kosong untuk generate otomatis dari judul</p>
                                <p v-if="form.errors.slug" class="text-red-500 text-xs mt-1">{{ form.errors.slug }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-ink mb-1">Excerpt</label>
                                <textarea
                                    v-model="form.excerpt"
                                    rows="2"
                                    placeholder="Ringkasan singkat artikel..."
                                    class="w-full px-4 py-2.5 border border-oat-dark rounded-lg focus:outline-none focus:ring-2 focus:ring-terracotta resize-none"
                                ></textarea>
                                <p class="text-xs text-taupe mt-1">Tampil di daftar blog dan meta description</p>
                                <p v-if="form.errors.excerpt" class="text-red-500 text-xs mt-1">{{ form.errors.excerpt }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-ink mb-1">Konten <span class="text-red-500">*</span></label>
                                <Editor
                                    v-model="form.content"
                                    placeholder="Tulis konten artikel di sini..."
                                    @upload-error="(msg) => form.setError('content', msg)"
                                />
                                <p class="text-xs text-taupe mt-1">Waktu baca akan dihitung otomatis. Drag & drop gambar untuk menambahkan.</p>
                                <p v-if="form.errors.content" class="text-red-500 text-xs mt-1">{{ form.errors.content }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Featured Image -->
                    <div class="bg-paper rounded-xl border border-oat-dark p-6">
                        <h2 class="font-serif text-lg font-semibold text-ink mb-4">Featured Image</h2>

                        <div v-if="!imagePreview">
                            <input type="file" accept="image/*" @change="handleImage" class="hidden" id="featured-image" />
                            <label
                                for="featured-image"
                                class="flex flex-col items-center justify-center w-full h-48 border-2 border-dashed border-oat-dark rounded-xl cursor-pointer hover:bg-oat/50 transition-colors"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-taupe/50 mb-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <rect x="3" y="3" width="18" height="18" rx="2"/>
                                    <circle cx="8.5" cy="8.5" r="1.5"/>
                                    <polyline points="21 15 16 10 5 21"/>
                                </svg>
                                <span class="text-sm text-taupe">Klik untuk upload gambar</span>
                                <span class="text-xs text-taupe/70 mt-1">JPG, PNG, WebP. Maks 5MB</span>
                            </label>
                        </div>

                        <div v-else class="relative">
                            <img :src="imagePreview" class="w-full h-48 object-cover rounded-xl" alt="Preview" />
                            <button
                                type="button"
                                @click="removeImage"
                                class="absolute top-2 right-2 p-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M18 6L6 18M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        <p v-if="form.errors.featured_image" class="text-red-500 text-xs mt-2">{{ form.errors.featured_image }}</p>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Publish Settings -->
                    <div class="bg-paper rounded-xl border border-oat-dark p-6">
                        <h2 class="font-serif text-lg font-semibold text-ink mb-4">Pengaturan</h2>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-ink mb-1">Status</label>
                                <select
                                    v-model="form.status"
                                    class="w-full px-4 py-2.5 border border-oat-dark rounded-lg focus:outline-none focus:ring-2 focus:ring-terracotta"
                                >
                                    <option v-for="opt in statusOptions" :key="opt.value" :value="opt.value">
                                        {{ opt.label }}
                                    </option>
                                </select>
                                <p v-if="form.errors.status" class="text-red-500 text-xs mt-1">{{ form.errors.status }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-ink mb-1">Tanggal Publikasi</label>
                                <input
                                    v-model="form.published_at"
                                    type="datetime-local"
                                    class="w-full px-4 py-2.5 border border-oat-dark rounded-lg focus:outline-none focus:ring-2 focus:ring-terracotta"
                                />
                                <p class="text-xs text-taupe mt-1">Kosongkan jika ingin publikasi sekarang</p>
                                <p v-if="form.errors.published_at" class="text-red-500 text-xs mt-1">{{ form.errors.published_at }}</p>
                            </div>

                            <div class="flex items-center gap-3">
                                <input
                                    v-model="form.is_featured"
                                    type="checkbox"
                                    id="is_featured"
                                    class="w-4 h-4 accent-terracotta rounded"
                                />
                                <label for="is_featured" class="text-sm font-medium text-ink">Tampilkan di Featured</label>
                            </div>
                        </div>
                    </div>

                    <!-- Categories -->
                    <div class="bg-paper rounded-xl border border-oat-dark p-6">
                        <h2 class="font-serif text-lg font-semibold text-ink mb-4">Kategori</h2>

                        <div class="space-y-2 max-h-48 overflow-y-auto">
                            <label
                                v-for="category in categories"
                                :key="category.id"
                                class="flex items-center gap-2 cursor-pointer"
                            >
                                <input
                                    type="checkbox"
                                    :checked="form.category_ids.includes(category.id)"
                                    @change="toggleCategory(category.id)"
                                    class="w-4 h-4 accent-terracotta rounded"
                                />
                                <span class="text-sm text-ink">{{ category.name }}</span>
                            </label>
                            <p v-if="categories.length === 0" class="text-sm text-taupe">Belum ada kategori</p>
                        </div>
                    </div>

                    <!-- Tags -->
                    <div class="bg-paper rounded-xl border border-oat-dark p-6">
                        <h2 class="font-serif text-lg font-semibold text-ink mb-4">Tags</h2>

                        <div class="space-y-2 max-h-48 overflow-y-auto">
                            <label
                                v-for="tag in tags"
                                :key="tag.id"
                                class="flex items-center gap-2 cursor-pointer"
                            >
                                <input
                                    type="checkbox"
                                    :checked="form.tag_ids.includes(tag.id)"
                                    @change="toggleTag(tag.id)"
                                    class="w-4 h-4 accent-terracotta rounded"
                                />
                                <span class="text-sm text-ink">{{ tag.name }}</span>
                            </label>
                            <p v-if="tags.length === 0" class="text-sm text-taupe">Belum ada tag</p>
                        </div>
                    </div>

                    <!-- Submit -->
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full px-4 py-3 bg-terracotta text-cream font-medium rounded-xl hover:bg-terracotta-dark disabled:opacity-50 transition-colors"
                    >
                        {{ form.processing ? 'Menyimpan...' : 'Simpan Post' }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</template>
