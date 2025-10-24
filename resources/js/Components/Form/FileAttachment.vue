<script setup lang="ts">
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import { XMarkIcon, DocumentPlusIcon } from "@heroicons/vue/24/outline";
import { computed } from "vue";

interface Props {
    modelValue: File[];
    label?: string;
    error?: string;
    id?: string;
    multiple?: boolean;
    accept?: string;
    maxSize?: number; // em MB
    maxFiles?: number;
}

const props = withDefaults(defineProps<Props>(), {
    label: "Anexos",
    id: "attachments",
    multiple: true,
    accept: "*",
    maxSize: 10,
    maxFiles: 10,
});

const emit = defineEmits<{
    "update:modelValue": [files: File[]];
}>();

const files = computed({
    get: () => props.modelValue,
    set: (value) => emit("update:modelValue", value),
});

const formatFileSize = (bytes: number): string => {
    if (bytes === 0) return "0 Bytes";
    const k = 1024;
    const sizes = ["Bytes", "KB", "MB", "GB"];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return Math.round((bytes / Math.pow(k, i)) * 100) / 100 + " " + sizes[i];
};

const handleFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (!target.files) return;

    const newFiles = Array.from(target.files);
    const maxSizeBytes = props.maxSize * 1024 * 1024;

    const invalidFiles = newFiles.filter((file) => file.size > maxSizeBytes);
    if (invalidFiles.length > 0) {
        alert(
            `Os seguintes arquivos excedem o tamanho máximo de ${
                props.maxSize
            }MB:\n${invalidFiles.map((f) => f.name).join("\n")}`
        );
        target.value = "";
        return;
    }

    const totalFiles = files.value.length + newFiles.length;
    if (totalFiles > props.maxFiles) {
        alert(`Você pode anexar no máximo ${props.maxFiles} arquivo(s)`);
        target.value = "";
        return;
    }

    files.value = [...files.value, ...newFiles];
    target.value = "";
};

const removeFile = (index: number) => {
    files.value = files.value.filter((_, i) => i !== index);
};

const clearAll = () => {
    files.value = [];
};
</script>

<template>
    <div class="space-y-3">
        <div class="flex items-center justify-between">
            <InputLabel :for="id" :value="label" />
            <button
                v-if="files.length > 0"
                type="button"
                @click="clearAll"
                class="text-xs text-red-600 hover:text-red-800 font-medium"
            >
                Remover todos
            </button>
        </div>

        <input
            :id="id"
            type="file"
            :multiple="multiple"
            :accept="accept"
            @change="handleFileChange"
            class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 dark:file:bg-indigo-900 file:text-indigo-700 dark:file:text-indigo-300 hover:file:bg-indigo-100 dark:hover:file:bg-indigo-800 cursor-pointer"
        />

        <p v-if="maxSize || maxFiles" class="text-xs text-gray-500 dark:text-gray-400">
            <span v-if="maxSize">Tamanho máximo por arquivo: {{ maxSize }}MB</span>
            <span v-if="maxSize && maxFiles"> • </span>
            <span v-if="maxFiles">Máximo de {{ maxFiles }} arquivo(s)</span>
        </p>

        <InputError :message="error" class="mt-2" />

        <div v-if="files.length > 0" class="space-y-2">
            <div
                v-for="(file, index) in files"
                :key="`${file.name}-${index}`"
                class="flex items-center justify-between bg-gray-50 dark:bg-gray-800 p-3 rounded-lg border border-gray-200 dark:border-gray-700"
            >
                <div class="flex-1 min-w-0 mr-4">
                    <p
                        class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate"
                    >
                        {{ file.name }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        {{ formatFileSize(file.size) }}
                    </p>
                </div>
                <button
                    type="button"
                    @click="removeFile(index)"
                    class="flex-shrink-0 text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 transition-colors p-1 rounded-md hover:bg-red-50 dark:hover:bg-red-900/20"
                    aria-label="Remover arquivo"
                >
                    <XMarkIcon class="h-5 w-5" />
                </button>
            </div>
        </div>

        <div
            v-else
            class="text-center py-8 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg"
        >
            <DocumentPlusIcon class="mx-auto h-12 w-12 text-gray-400" />
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                Nenhum arquivo anexado
            </p>
        </div>
    </div>
</template>
