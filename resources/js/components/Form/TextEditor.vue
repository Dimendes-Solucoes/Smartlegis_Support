<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount, watch } from 'vue';

interface Props {
    modelValue: string;
    id?: string;
    height?: number;
    disabled?: boolean;
    placeholder?: string;
}

const props = withDefaults(defineProps<Props>(), {
    id: 'rich-text-editor',
    height: 500,
    disabled: false,
    placeholder: 'Digite aqui...',
});

const emit = defineEmits<{
    'update:modelValue': [value: string];
}>();

let editor: any = null;
const editorId = ref(props.id);
const isEditorReady = ref(false);

onMounted(() => {
    loadTinyMCE();
});

const loadTinyMCE = () => {
    if (window.tinymce) {
        initTinyMCE();
    } else {
        const script = document.createElement('script');
        script.src = 'https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js';
        script.referrerPolicy = 'origin';
        script.onload = () => initTinyMCE();
        document.head.appendChild(script);
    }
};

const initTinyMCE = () => {
    window.tinymce.init({
        selector: `#${editorId.value}`,
        height: props.height,
        menubar: false,
        language: 'pt_BR',
        plugins: [
            'advlist',
            'autolink',
            'lists',
            'link',
            'image',
            'charmap',
            'preview',
            'anchor',
            'searchreplace',
            'visualblocks',
            'code',
            'fullscreen',
            'insertdatetime',
            'media',
            'table',
            'help',
            'wordcount',
        ],
        toolbar:
            'undo redo | blocks | ' +
            'bold italic forecolor backcolor | alignleft aligncenter ' +
            'alignright alignjustify | bullist numlist outdent indent | ' +
            'removeformat | table | link image | code | help',
        content_style: `
            body { 
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
                font-size: 14px;
                line-height: 1.6;
                padding: 1rem;
            }
        `,
        placeholder: props.placeholder,
        readonly: props.disabled,
        setup: (ed: any) => {
            editor = ed;

            ed.on('init', () => {
                isEditorReady.value = true;
                ed.setContent(props.modelValue || '');

                if (props.disabled) {
                    ed.mode.set('readonly');
                }
            });

            ed.on('change keyup', () => {
                const content = ed.getContent();
                emit('update:modelValue', content);
            });

            ed.on('blur', () => {
                const content = ed.getContent();
                emit('update:modelValue', content);
            });
        },
    });
};

// Watch for external changes to modelValue
watch(
    () => props.modelValue,
    (newValue) => {
        if (editor && isEditorReady.value) {
            const currentContent = editor.getContent();
            if (currentContent !== newValue) {
                editor.setContent(newValue || '');
            }
        }
    }
);

// Watch for disabled state changes
watch(
    () => props.disabled,
    (newValue) => {
        if (editor && isEditorReady.value) {
            if (newValue) {
                editor.mode.set('readonly');
            } else {
                editor.mode.set('design');
            }
        }
    }
);

onBeforeUnmount(() => {
    if (window.tinymce && editor) {
        window.tinymce.remove(`#${editorId.value}`);
    }
});

// Expose methods for parent component
defineExpose({
    getContent: () => editor?.getContent() || '',
    setContent: (content: string) => editor?.setContent(content),
    focus: () => editor?.focus(),
    clear: () => editor?.setContent(''),
});
</script>

<template>
    <div class="rich-text-editor-wrapper">
        <textarea :id="editorId" class="hidden"></textarea>
    </div>
</template>

<style scoped>
.rich-text-editor-wrapper {
    @apply w-full;
}

:deep(.tox-tinymce) {
    border-radius: 0.375rem;
    border-color: rgb(209 213 219);
}

:deep(.tox-toolbar),
:deep(.tox-statusbar) {
    background-color: rgb(249 250 251) !important;
}

@media (prefers-color-scheme: dark) {
    :deep(.tox-tinymce) {
        border-color: rgb(55 65 81);
    }

    :deep(.tox-toolbar),
    :deep(.tox-statusbar) {
        background-color: rgb(31 41 55) !important;
    }
}
</style>