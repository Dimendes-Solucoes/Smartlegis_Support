<script setup lang="ts">
import InputError from "@/components/Form/InputError.vue";
import InputLabel from "@/components/Form/InputLabel.vue";
import { DocumentArrowUpIcon, DocumentIcon, TrashIcon } from "@heroicons/vue/24/outline";
import { computed, ref, watch } from "vue";
import { getImageUrl } from "@/utils/image";

interface Props {
    modelValue: File | null;
    label?: string;
    error?: string;
    id?: string;
    accept?: string;
    maxSize?: number;
    initialFileUrl?: string | null;
    initialFileName?: string | null;
}

const props = withDefaults(defineProps<Props>(), {
    label: "Anexo do Documento",
    id: "document_attachment",
    accept: ".pdf,.doc,.docx,.xls,.xlsx,.txt",
    maxSize: 50,
});

const emit = defineEmits<{
    "update:modelValue": [file: File | null];
}>();

const file = computed({
    get: () => props.modelValue,
    set: (value) => emit("update:modelValue", value),
});

const currentFileName = ref<string | null>(props.initialFileName || null);
const currentFileUrl = ref<string | null>(props.initialFileUrl || null);

watch(
    () => props.modelValue,
    (newFile) => {
        if (newFile) {
            currentFileName.value = newFile.name;
        } else if (props.initialFileName) {
            currentFileName.value = props.initialFileName;
        } else {
            currentFileName.value = null;
        }
    },
    { immediate: true }
);

watch(
    () => props.initialFileName,
    (newName) => {
        if (newName && !props.modelValue) {
            currentFileName.value = newName;
        }
    },
    { immediate: true }
);

watch(
    () => props.initialFileUrl,
    (newUrl) => {
        currentFileUrl.value = newUrl ? getImageUrl(newUrl) : null;
    },
    { immediate: true }
);

const formatFileSize = (bytes: number): string => {
    if (bytes === 0) return "0 Bytes";
    const k = 1024;
    const sizes = ["Bytes", "KB", "MB", "GB"];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return Math.round((bytes / Math.pow(k, i)) * 100) / 100 + " " + sizes[i];
};

const getFileExtension = (filename: string): string => {
    return filename.split(".").pop()?.toUpperCase() || "FILE";
};

const handleFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (!target.files || !target.files[0]) {
        if (!props.initialFileName) {
            file.value = null;
            currentFileName.value = null;
        }
        return;
    }

    const selectedFile = target.files[0];
    const maxSizeBytes = props.maxSize * 1024 * 1024;

    if (selectedFile.size > maxSizeBytes) {
        alert(`O arquivo excede o tamanho máximo de ${props.maxSize}MB`);
        target.value = "";
        return;
    }

    file.value = selectedFile;
    currentFileName.value = selectedFile.name;
    target.value = "";
};

const removeFile = () => {
    file.value = null;
    currentFileName.value = props.initialFileName || null;
    currentFileUrl.value = props.initialFileUrl
        ? getImageUrl(props.initialFileUrl)
        : null;
};

const downloadFile = () => {
    if (currentFileUrl.value) {
        window.open(currentFileUrl.value, "_blank");
    }
};
</script>

<template>
    <div>
        <InputLabel :for="id" :value="label" />

        <div class="mt-2">
            <label
                :for="id"
                class="flex items-center justify-center w-full px-4 py-3 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:border-indigo-500 dark:hover:border-indigo-400 hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-150 ease-in-out"
            >
                <DocumentArrowUpIcon class="h-6 w-6 text-gray-400 mr-2" />
                <span class="text-sm text-gray-600 dark:text-gray-400">
                    {{
                        file
                            ? "Clique para substituir o arquivo"
                            : currentFileName
                            ? "Clique para substituir o arquivo"
                            : "Clique para selecionar um arquivo"
                    }}
                </span>
            </label>
            <input
                :id="id"
                type="file"
                :accept="accept"
                @change="handleFileChange"
                class="hidden"
            />
        </div>

        <InputError class="mt-2" :message="error" />

        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
            Tamanho máximo: {{ maxSize }}MB. Formatos aceitos: PDF, DOC, DOCX, XLS, XLSX,
            TXT
        </p>

        <!-- Preview do arquivo atual ou novo -->
        <div v-if="currentFileName" class="mt-4">
            <div class="flex items-center justify-between mb-2">
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ file ? "Novo arquivo:" : "Arquivo atual:" }}
                </p>
            </div>

            <div
                class="bg-white dark:bg-gray-800 p-4 rounded-lg border-2 border-gray-200 dark:border-gray-700 shadow-sm"
            >
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div
                            class="flex-shrink-0 w-12 h-12 bg-indigo-100 dark:bg-indigo-900 rounded-lg flex items-center justify-center"
                        >
                            <DocumentIcon
                                class="h-6 w-6 text-indigo-600 dark:text-indigo-400"
                            />
                        </div>
                        <div class="flex-1 min-w-0">
                            <p
                                class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate"
                            >
                                {{ currentFileName }}
                            </p>
                            <div class="flex items-center space-x-2 mt-1">
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300"
                                >
                                    {{ getFileExtension(currentFileName) }}
                                </span>
                                <span
                                    v-if="file"
                                    class="text-xs text-gray-500 dark:text-gray-400"
                                >
                                    {{ formatFileSize(file.size) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center space-x-2">
                        <button
                            v-if="currentFileUrl && !file"
                            type="button"
                            @click="downloadFile"
                            class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-xs font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out"
                        >
                            Visualizar
                        </button>
                        <button
                            v-if="file"
                            type="button"
                            @click="removeFile"
                            class="inline-flex items-center p-2 border border-transparent rounded-md text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-150 ease-in-out"
                            title="Remover arquivo"
                        >
                            <TrashIcon class="h-5 w-5" />
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estado vazio -->
        <div
            v-else
            class="mt-4 text-center py-8 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700"
        >
            <DocumentIcon class="mx-auto h-12 w-12 text-gray-400" />
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                Nenhum arquivo anexado
            </p>
        </div>
    </div>
</template>
