<script setup>
import { ref, watch, onBeforeUnmount } from 'vue';
import { useEditor, EditorContent } from '@tiptap/vue-3';
import StarterKit from '@tiptap/starter-kit';
import Image from '@tiptap/extension-image';
import Link from '@tiptap/extension-link';
import Placeholder from '@tiptap/extension-placeholder';
import CodeBlock from '@tiptap/extension-code-block';
import { Color } from '@tiptap/extension-color';
import { TextStyle } from '@tiptap/extension-text-style';
import axios from 'axios';

const props = defineProps({
    modelValue: {
        type: String,
        default: '',
    },
    placeholder: {
        type: String,
        default: 'Tulis konten di sini...',
    },
});

const emit = defineEmits(['update:modelValue', 'upload-error']);

const isUploading = ref(false);
const uploadProgress = ref(0);

const editor = useEditor({
    content: props.modelValue,
    extensions: [
        StarterKit.configure({
            codeBlock: false,
        }),
        Image.configure({
            inline: true,
            allowBase64: true,
            HTMLAttributes: {
                class: 'rounded-lg max-w-full',
            },
        }),
        Link.configure({
            openOnClick: false,
            HTMLAttributes: {
                class: 'text-terracotta underline hover:text-terracotta-dark',
            },
        }),
        Placeholder.configure({
            placeholder: props.placeholder,
        }),
        CodeBlock.configure({
            HTMLAttributes: {
                class: 'bg-ink text-cream p-4 rounded-lg font-mono text-sm overflow-x-auto',
            },
        }),
        TextStyle,
        Color,
    ],
    editorProps: {
        attributes: {
            class: 'prose prose-lg max-w-none focus:outline-none min-h-[300px] px-4 py-3',
        },
        handleDrop: (view, event, slice, moved) => {
            if (!moved && event.dataTransfer?.files.length) {
                const file = event.dataTransfer.files[0];
                if (file.type.startsWith('image/')) {
                    event.preventDefault();
                    uploadImage(file);
                    return true;
                }
            }
            return false;
        },
        handlePaste: (view, event) => {
            const items = event.clipboardData?.items;
            if (items) {
                for (const item of items) {
                    if (item.type.startsWith('image/')) {
                        const file = item.getAsFile();
                        if (file) {
                            uploadImage(file);
                            return true;
                        }
                    }
                }
            }
            return false;
        },
    },
    onUpdate: ({ editor }) => {
        emit('update:modelValue', editor.getHTML());
    },
});

watch(() => props.modelValue, (value) => {
    if (editor.value && editor.value.getHTML() !== value) {
        editor.value.commands.setContent(value, false);
    }
});

onBeforeUnmount(() => {
    if (editor.value) {
        editor.value.destroy();
    }
});

const uploadImage = async (file) => {
    if (!file.type.startsWith('image/')) {
        emit('upload-error', 'Hanya file gambar yang diizinkan');
        return;
    }

    if (file.size > 5 * 1024 * 1024) {
        emit('upload-error', 'Ukuran file maksimal 5MB');
        return;
    }

    isUploading.value = true;
    uploadProgress.value = 0;

    const formData = new FormData();
    formData.append('image', file);
    formData.append('type', 'content');

    try {
        const response = await axios.post(route('admin.blog.upload-image'), formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
            onUploadProgress: (progressEvent) => {
                uploadProgress.value = Math.round(
                    (progressEvent.loaded * 100) / progressEvent.total
                );
            },
        });

        if (editor.value && response.data.url) {
            editor.value.chain().focus().setImage({ src: response.data.url }).run();
        }
    } catch (error) {
        console.error('Upload failed:', error);
        emit('upload-error', error.response?.data?.message || 'Gagal mengupload gambar');
    } finally {
        isUploading.value = false;
        uploadProgress.value = 0;
    }
};

const handleImageUpload = (event) => {
    const file = event.target.files?.[0];
    if (file) {
        uploadImage(file);
        event.target.value = '';
    }
};

const setLink = () => {
    const previousUrl = editor.value?.getAttributes('link').href;
    const url = window.prompt('Masukkan URL:', previousUrl);

    if (url === null) {
        return;
    }

    if (url === '') {
        editor.value?.chain().focus().extendMarkRange('link').unsetLink().run();
        return;
    }

    editor.value?.chain().focus().extendMarkRange('link').setLink({ href: url }).run();
};

const isActive = (name, attrs = {}) => {
    return editor.value?.isActive(name, attrs) || false;
};
</script>

<template>
    <div class="border border-oat-dark rounded-xl overflow-hidden bg-paper">
        <!-- Toolbar -->
        <div class="flex flex-wrap items-center gap-1 p-2 border-b border-oat-dark bg-oat/30">
            <!-- Text Formatting -->
            <div class="flex items-center gap-1 pr-2 border-r border-oat-dark">
                <button
                    type="button"
                    @click="editor?.chain().focus().toggleBold().run()"
                    :class="[
                        'p-2 rounded-lg transition-colors',
                        isActive('bold')
                            ? 'bg-terracotta text-cream'
                            : 'text-ink hover:bg-oat'
                    ]"
                    title="Bold (Ctrl+B)"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 4h8a4 4 0 014 4 4 4 0 01-4 4H6z M6 12h9a4 4 0 014 4 4 4 0 01-4 4H6z"/>
                    </svg>
                </button>
                <button
                    type="button"
                    @click="editor?.chain().focus().toggleItalic().run()"
                    :class="[
                        'p-2 rounded-lg transition-colors',
                        isActive('italic')
                            ? 'bg-terracotta text-cream'
                            : 'text-ink hover:bg-oat'
                    ]"
                    title="Italic (Ctrl+I)"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 4h4m-2 0v16m-4 0h8"/>
                    </svg>
                </button>
                <button
                    type="button"
                    @click="editor?.chain().focus().toggleStrike().run()"
                    :class="[
                        'p-2 rounded-lg transition-colors',
                        isActive('strike')
                            ? 'bg-terracotta text-cream'
                            : 'text-ink hover:bg-oat'
                    ]"
                    title="Strikethrough"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M12 5v14"/>
                    </svg>
                </button>
                <button
                    type="button"
                    @click="editor?.chain().focus().toggleCode().run()"
                    :class="[
                        'p-2 rounded-lg transition-colors',
                        isActive('code')
                            ? 'bg-terracotta text-cream'
                            : 'text-ink hover:bg-oat'
                    ]"
                    title="Inline Code"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                    </svg>
                </button>
            </div>

            <!-- Headings -->
            <div class="flex items-center gap-1 px-2 border-r border-oat-dark">
                <button
                    type="button"
                    @click="editor?.chain().focus().toggleHeading({ level: 2 }).run()"
                    :class="[
                        'p-2 rounded-lg transition-colors text-sm font-bold',
                        isActive('heading', { level: 2 })
                            ? 'bg-terracotta text-cream'
                            : 'text-ink hover:bg-oat'
                    ]"
                    title="Heading 2"
                >
                    H2
                </button>
                <button
                    type="button"
                    @click="editor?.chain().focus().toggleHeading({ level: 3 }).run()"
                    :class="[
                        'p-2 rounded-lg transition-colors text-sm font-bold',
                        isActive('heading', { level: 3 })
                            ? 'bg-terracotta text-cream'
                            : 'text-ink hover:bg-oat'
                    ]"
                    title="Heading 3"
                >
                    H3
                </button>
            </div>

            <!-- Lists -->
            <div class="flex items-center gap-1 px-2 border-r border-oat-dark">
                <button
                    type="button"
                    @click="editor?.chain().focus().toggleBulletList().run()"
                    :class="[
                        'p-2 rounded-lg transition-colors',
                        isActive('bulletList')
                            ? 'bg-terracotta text-cream'
                            : 'text-ink hover:bg-oat'
                    ]"
                    title="Bullet List"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <button
                    type="button"
                    @click="editor?.chain().focus().toggleOrderedList().run()"
                    :class="[
                        'p-2 rounded-lg transition-colors',
                        isActive('orderedList')
                            ? 'bg-terracotta text-cream'
                            : 'text-ink hover:bg-oat'
                    ]"
                    title="Numbered List"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                    </svg>
                </button>
            </div>

            <!-- Alignment & Quote -->
            <div class="flex items-center gap-1 px-2 border-r border-oat-dark">
                <button
                    type="button"
                    @click="editor?.chain().focus().toggleBlockquote().run()"
                    :class="[
                        'p-2 rounded-lg transition-colors',
                        isActive('blockquote')
                            ? 'bg-terracotta text-cream'
                            : 'text-ink hover:bg-oat'
                    ]"
                    title="Blockquote"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                </button>
                <button
                    type="button"
                    @click="editor?.chain().focus().toggleCodeBlock().run()"
                    :class="[
                        'p-2 rounded-lg transition-colors',
                        isActive('codeBlock')
                            ? 'bg-terracotta text-cream'
                            : 'text-ink hover:bg-oat'
                    ]"
                    title="Code Block"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                    </svg>
                </button>
            </div>

            <!-- Link & Image -->
            <div class="flex items-center gap-1 px-2 border-r border-oat-dark">
                <button
                    type="button"
                    @click="setLink"
                    :class="[
                        'p-2 rounded-lg transition-colors',
                        isActive('link')
                            ? 'bg-terracotta text-cream'
                            : 'text-ink hover:bg-oat'
                    ]"
                    title="Add Link"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                    </svg>
                </button>
                <label
                    :class="[
                        'p-2 rounded-lg transition-colors cursor-pointer',
                        'text-ink hover:bg-oat',
                        isUploading && 'opacity-50 cursor-not-allowed'
                    ]"
                    title="Upload Image"
                >
                    <input
                        type="file"
                        accept="image/*"
                        class="hidden"
                        :disabled="isUploading"
                        @change="handleImageUpload"
                    />
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </label>
            </div>

            <!-- Undo/Redo -->
            <div class="flex items-center gap-1 px-2">
                <button
                    type="button"
                    @click="editor?.chain().focus().undo().run()"
                    :disabled="!editor?.can().undo()"
                    class="p-2 rounded-lg text-ink hover:bg-oat disabled:opacity-30 disabled:cursor-not-allowed transition-colors"
                    title="Undo (Ctrl+Z)"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                    </svg>
                </button>
                <button
                    type="button"
                    @click="editor?.chain().focus().redo().run()"
                    :disabled="!editor?.can().redo()"
                    class="p-2 rounded-lg text-ink hover:bg-oat disabled:opacity-30 disabled:cursor-not-allowed transition-colors"
                    title="Redo (Ctrl+Y)"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 10h-10a8 8 0 00-8 8v2M21 10l-6 6m6-6l-6-6"/>
                    </svg>
                </button>
            </div>

            <!-- Upload Progress -->
            <div v-if="isUploading" class="ml-auto flex items-center gap-2 text-sm text-taupe">
                <div class="w-24 h-2 bg-oat rounded-full overflow-hidden">
                    <div
                        class="h-full bg-terracotta transition-all duration-300"
                        :style="{ width: `${uploadProgress}%` }"
                    ></div>
                </div>
                <span>{{ uploadProgress }}%</span>
            </div>
        </div>

        <!-- Editor Content -->
        <EditorContent :editor="editor" class="min-h-[300px]" />

        <!-- Drop Zone Indicator -->
        <div class="hidden peer-drop:flex absolute inset-0 bg-terracotta/10 border-2 border-dashed border-terracotta rounded-xl items-center justify-center pointer-events-none">
            <span class="text-terracotta font-medium">Drop gambar di sini</span>
        </div>
    </div>
</template>

<style>
.tiptap {
    @apply text-ink leading-relaxed;
}

.tiptap p {
    @apply mb-4;
}

.tiptap p:last-child {
    @apply mb-0;
}

.tiptap h2 {
    @apply text-2xl font-bold text-ink mb-4 mt-6;
}

.tiptap h3 {
    @apply text-xl font-bold text-ink mb-3 mt-5;
}

.tiptap ul,
.tiptap ol {
    @apply pl-6 mb-4;
}

.tiptap ul {
    @apply list-disc;
}

.tiptap ol {
    @apply list-decimal;
}

.tiptap li {
    @apply mb-1;
}

.tiptap blockquote {
    @apply border-l-4 border-terracotta pl-4 italic text-taupe my-4;
}

.tiptap pre {
    @apply bg-ink text-cream p-4 rounded-lg font-mono text-sm overflow-x-auto my-4;
}

.tiptap code {
    @apply bg-oat px-1.5 py-0.5 rounded text-sm font-mono text-ink;
}

.tiptap pre code {
    @apply bg-transparent p-0 text-cream;
}

.tiptap a {
    @apply text-terracotta underline hover:text-terracotta-dark;
}

.tiptap img {
    @apply max-w-full rounded-lg my-4;
}

.tiptap hr {
    @apply border-oat-dark my-8;
}

/* Placeholder */
.tiptap p.is-editor-empty:first-child::before {
    @apply text-taupe/50;
    content: attr(data-placeholder);
    float: left;
    height: 0;
    pointer-events: none;
}

/* Selection */
.tiptap ::selection {
    @apply bg-terracotta/20;
}
</style>
