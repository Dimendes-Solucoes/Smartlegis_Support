<script setup lang="ts">
import InputError from "@/components/Form/InputError.vue";
import InputLabel from "@/components/Form/InputLabel.vue";
import PrimaryButton from "@/components/Common/PrimaryButton.vue";
import { DocumentPlusIcon } from "@heroicons/vue/24/outline";
import { computed, ref, watch } from "vue";
import { Cropper } from "vue-advanced-cropper";
import "vue-advanced-cropper/dist/style.css";
import { getImageUrl } from "@/utils/image";

interface Props {
    modelValue: File | null;
    label?: string;
    error?: string;
    id?: string;
    accept?: string;
    maxSize?: number; // em MB
    initialImageUrl?: string | null;
}

const props = withDefaults(defineProps<Props>(), {
    label: "Foto do Usuário",
    id: "attachment",
    accept: "image/*",
    maxSize: 10,
});

const emit = defineEmits<{
    "update:modelValue": [file: File | null];
}>();

const file = computed({
    get: () => props.modelValue,
    set: (value) => emit("update:modelValue", value),
});

const imageSource = ref<string | null>(null);
const showCropperModal = ref(false);
const cropperRef = ref<InstanceType<typeof Cropper> | null>(null);
const previewImageUrl = ref<string | null>(null);

watch(
    () => props.modelValue,
    (newFile, oldFile) => {
        if (previewImageUrl.value && oldFile) {
            URL.revokeObjectURL(previewImageUrl.value);
        }
        if (newFile) {
            previewImageUrl.value = URL.createObjectURL(newFile);
        } else if (props.initialImageUrl) {
            previewImageUrl.value = getImageUrl(props.initialImageUrl);
        } else {
            previewImageUrl.value = null;
        }
    },
    { immediate: true }
);

watch(
    () => props.initialImageUrl,
    (newUrl) => {
        if (newUrl && !props.modelValue) {
            previewImageUrl.value = getImageUrl(newUrl);
        } else if (!newUrl && !props.modelValue) {
            previewImageUrl.value = null;
        }
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

const handleFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (!target.files || !target.files[0]) {
        file.value = null;
        imageSource.value = null;
        previewImageUrl.value = props.initialImageUrl
            ? getImageUrl(props.initialImageUrl)
            : null;
        return;
    }

    const selectedFile = target.files[0];
    const maxSizeBytes = props.maxSize * 1024 * 1024;

    if (selectedFile.size > maxSizeBytes) {
        alert(`O arquivo excede o tamanho máximo de ${props.maxSize}MB`);
        target.value = "";
        return;
    }

    imageSource.value = URL.createObjectURL(selectedFile);
    showCropperModal.value = true;
    target.value = "";
};

const cropImage = () => {
    if (cropperRef.value) {
        const { canvas } = cropperRef.value.getResult();
        if (canvas) {
            canvas.toBlob((blob) => {
                if (blob) {
                    const croppedFile = new File([blob], "image.png", {
                        type: "image/png",
                    });
                    file.value = croppedFile;
                }
                showCropperModal.value = false;
                if (imageSource.value) {
                    URL.revokeObjectURL(imageSource.value);
                    imageSource.value = null;
                }
            }, "image/png");
        }
    }
};

const cancelCrop = () => {
    showCropperModal.value = false;
    if (imageSource.value) {
        URL.revokeObjectURL(imageSource.value);
        imageSource.value = null;
    }
    previewImageUrl.value = props.initialImageUrl || null;
};

const removeFile = () => {
    if (previewImageUrl.value && file.value) {
        URL.revokeObjectURL(previewImageUrl.value);
    }
    file.value = null;
    previewImageUrl.value = props.initialImageUrl
        ? getImageUrl(props.initialImageUrl)
        : null;
};
</script>

<template>
    <div>
        <InputLabel :for="id" :value="label" />

        <input
            :id="id"
            type="file"
            :accept="accept"
            @change="handleFileChange"
            class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 dark:file:bg-indigo-900 file:text-indigo-700 dark:file:text-indigo-300 hover:file:bg-indigo-100 dark:hover:file:bg-indigo-800 cursor-pointer"
        />

        <InputError class="mt-2" :message="error" />

        <p v-if="maxSize" class="mt-2 text-sm text-gray-500 dark:text-gray-400">
            Tamanho máximo: {{ maxSize }}MB
        </p>

        <div v-if="previewImageUrl && !showCropperModal" class="mt-4">
            <div class="flex items-center justify-between mb-2">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Pré-visualização da Imagem:
                </p>
                <button
                    v-if="file"
                    type="button"
                    @click="removeFile"
                    class="text-xs text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 font-medium transition duration-150 ease-in-out"
                >
                    Remover
                </button>
            </div>

            <div
                class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border border-gray-300 dark:border-gray-600 shadow-sm"
            >
                <img
                    :src="previewImageUrl"
                    alt="Pré-visualização da imagem"
                    class="w-36 h-36 object-cover rounded-lg shadow-md mx-auto"
                />
                <p
                    v-if="file"
                    class="text-xs text-gray-500 dark:text-gray-400 text-center mt-2"
                >
                    {{ file.name }} ({{ formatFileSize(file.size) }})
                </p>
            </div>
        </div>

        <div
            v-else-if="!showCropperModal"
            class="mt-4 text-center py-8 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700"
        >
            <DocumentPlusIcon class="mx-auto h-12 w-12 text-gray-400" />
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                Nenhuma foto selecionada
            </p>
        </div>

        <!-- Modal de Cropper -->
        <div
            v-if="showCropperModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75"
        >
            <div
                class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-xl max-w-lg w-full"
            >
                <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">
                    Cortar Imagem
                </h3>
                <div class="max-h-96 overflow-hidden mb-4">
                    <Cropper
                        ref="cropperRef"
                        :src="imageSource"
                        :stencil-props="{ aspectRatio: 1 / 1 }"
                        class="cropper"
                    />
                </div>
                <div class="flex justify-end space-x-4">
                    <button
                        @click="cancelCrop"
                        type="button"
                        class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600 transition duration-150 ease-in-out"
                    >
                        Cancelar
                    </button>
                    <PrimaryButton @click="cropImage" type="button">
                        Cortar e Confirmar
                    </PrimaryButton>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
.cropper {
    height: 300px;
    background: #ddd;
}
</style>
