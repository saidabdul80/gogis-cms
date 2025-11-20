<script setup lang="ts">
import { watch, onBeforeUnmount } from 'vue'
import { useEditor, EditorContent } from '@tiptap/vue-3'
import StarterKit from '@tiptap/starter-kit'
import Image from '@tiptap/extension-image'
import Link from '@tiptap/extension-link'
import TextAlign from '@tiptap/extension-text-align'
import Underline from '@tiptap/extension-underline'

interface Props {
    modelValue: string
    label?: string
    errorMessages?: string | string[]
}

const props = defineProps<Props>()
const emit = defineEmits<{
    'update:modelValue': [value: string]
}>()

const editor = useEditor({
    content: props.modelValue,
    extensions: [
        StarterKit,
        Underline,
        Link.configure({
            openOnClick: false,
        }),
        Image,
        TextAlign.configure({
            types: ['heading', 'paragraph'],
        }),
    ],
    onUpdate: ({ editor }) => {
        emit('update:modelValue', editor.getHTML())
    },
})

watch(() => props.modelValue, (value) => {
    const isSame = editor.value?.getHTML() === value
    if (!isSame && editor.value) {
        editor.value.commands.setContent(value, false)
    }
})

onBeforeUnmount(() => {
    editor.value?.destroy()
})

const addImage = () => {
    const url = window.prompt('Enter image URL:')
    if (url && editor.value) {
        editor.value.chain().focus().setImage({ src: url }).run()
    }
}

const setLink = () => {
    const url = window.prompt('Enter URL:')
    if (url && editor.value) {
        editor.value.chain().focus().setLink({ href: url }).run()
    }
}
</script>

<template>
    <div class="rich-text-editor">
        <label v-if="label" class="text-subtitle-2 mb-2 d-block">{{ label }}</label>
        
        <div v-if="editor" class="editor-toolbar">
            <v-btn-group density="compact" variant="outlined">
                <v-btn
                    size="small"
                    :class="{ 'is-active': editor.isActive('bold') }"
                    @click="editor.chain().focus().toggleBold().run()"
                >
                    <v-icon>mdi-format-bold</v-icon>
                </v-btn>
                <v-btn
                    size="small"
                    :class="{ 'is-active': editor.isActive('italic') }"
                    @click="editor.chain().focus().toggleItalic().run()"
                >
                    <v-icon>mdi-format-italic</v-icon>
                </v-btn>
                <v-btn
                    size="small"
                    :class="{ 'is-active': editor.isActive('underline') }"
                    @click="editor.chain().focus().toggleUnderline().run()"
                >
                    <v-icon>mdi-format-underline</v-icon>
                </v-btn>
            </v-btn-group>

            <v-btn-group density="compact" variant="outlined" class="ml-2">
                <v-btn
                    size="small"
                    :class="{ 'is-active': editor.isActive('heading', { level: 1 }) }"
                    @click="editor.chain().focus().toggleHeading({ level: 1 }).run()"
                >
                    H1
                </v-btn>
                <v-btn
                    size="small"
                    :class="{ 'is-active': editor.isActive('heading', { level: 2 }) }"
                    @click="editor.chain().focus().toggleHeading({ level: 2 }).run()"
                >
                    H2
                </v-btn>
                <v-btn
                    size="small"
                    :class="{ 'is-active': editor.isActive('heading', { level: 3 }) }"
                    @click="editor.chain().focus().toggleHeading({ level: 3 }).run()"
                >
                    H3
                </v-btn>
            </v-btn-group>

            <v-btn-group density="compact" variant="outlined" class="ml-2">
                <v-btn
                    size="small"
                    :class="{ 'is-active': editor.isActive('bulletList') }"
                    @click="editor.chain().focus().toggleBulletList().run()"
                >
                    <v-icon>mdi-format-list-bulleted</v-icon>
                </v-btn>
                <v-btn
                    size="small"
                    :class="{ 'is-active': editor.isActive('orderedList') }"
                    @click="editor.chain().focus().toggleOrderedList().run()"
                >
                    <v-icon>mdi-format-list-numbered</v-icon>
                </v-btn>
            </v-btn-group>

            <v-btn-group density="compact" variant="outlined" class="ml-2">
                <v-btn size="small" @click="setLink">
                    <v-icon>mdi-link</v-icon>
                </v-btn>
                <v-btn size="small" @click="addImage">
                    <v-icon>mdi-image</v-icon>
                </v-btn>
            </v-btn-group>
        </div>

        <EditorContent :editor="editor" class="editor-content" />
        
        <div v-if="errorMessages" class="text-error text-caption mt-1">
            {{ Array.isArray(errorMessages) ? errorMessages[0] : errorMessages }}
        </div>
    </div>
</template>

<style scoped>
.rich-text-editor {
    border: 1px solid rgba(0, 0, 0, 0.23);
    border-radius: 4px;
}

.editor-toolbar {
    padding: 8px;
    border-bottom: 1px solid rgba(0, 0, 0, 0.12);
    background-color: rgba(0, 0, 0, 0.02);
}

.editor-content {
    min-height: 300px;
}

.editor-content :deep(.ProseMirror) {
    padding: 16px;
    min-height: 300px;
    outline: none;
}

.editor-content :deep(.ProseMirror p) {
    margin-bottom: 1em;
}

.editor-content :deep(.ProseMirror h1),
.editor-content :deep(.ProseMirror h2),
.editor-content :deep(.ProseMirror h3) {
    margin-top: 1em;
    margin-bottom: 0.5em;
    font-weight: bold;
}

.editor-content :deep(.ProseMirror ul),
.editor-content :deep(.ProseMirror ol) {
    padding-left: 1.5em;
    margin-bottom: 1em;
}

.editor-content :deep(.ProseMirror img) {
    max-width: 100%;
    height: auto;
}

.is-active {
    background-color: rgba(0, 0, 0, 0.1);
}
</style>

