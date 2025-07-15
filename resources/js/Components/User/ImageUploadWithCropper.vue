<script setup lang="ts">
import { ref, watch, onMounted } from 'vue'; // Importa onMounted
import { Cropper } from 'vue-advanced-cropper';
import 'vue-advanced-cropper/dist/style.css';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { getImageUrl } from '@/Utils/image'; // Importe sua função getImageUrl

const props = defineProps<{
    modelValue: File | null;
    error: string | undefined;
    initialImageUrl?: string | null; // Nova prop para a URL da imagem existente
}>();

const emit = defineEmits(['update:modelValue']);

const imageSource = ref<string | null>(null);
const showCropperModal = ref(false);
const cropperRef = ref<InstanceType<typeof Cropper> | null>(null);
const previewImageUrl = ref<string | null>(null);

// Lógica para pré-visualizar a imagem inicial ou a imagem recém-cortada
watch(() => props.modelValue, (newFile, oldFile) => {
    if (previewImageUrl.value && oldFile) {
        URL.revokeObjectURL(previewImageUrl.value);
    }
    if (newFile) {
        previewImageUrl.value = URL.createObjectURL(newFile);
    } else if (props.initialImageUrl) {
        // Se não há newFile, mas há uma imagem inicial, use-a para a pré-visualização
        // Importante: use sua função getImageUrl se o initialImageUrl for um caminho relativo!
        previewImageUrl.value = getImageUrl(props.initialImageUrl);
    } else {
        previewImageUrl.value = null;
    }
}, { immediate: true });

// Adiciona um watcher para initialImageUrl para lidar com casos onde ele pode mudar
watch(() => props.initialImageUrl, (newUrl) => {
    if (newUrl && !props.modelValue) { // Apenas atualiza se não houver uma nova imagem selecionada
        previewImageUrl.value = getImageUrl(newUrl);
    } else if (!newUrl && !props.modelValue) {
        previewImageUrl.value = null;
    }
}, { immediate: true });


const handleImageChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        const file = target.files[0];
        imageSource.value = URL.createObjectURL(file);
        showCropperModal.value = true;
    } else {
        emit('update:modelValue', null); // Limpa a imagem no pai se nada for selecionado
        imageSource.value = null;
        // Se o input foi limpo, mas havia uma imagem inicial, ela deve continuar a ser exibida
        // a menos que o usuário clique em um botão "remover imagem"
        previewImageUrl.value = props.initialImageUrl ? getImageUrl(props.initialImageUrl) : null;
    }
};

const cropImage = () => {
    if (cropperRef.value) {
        const { canvas } = cropperRef.value.getResult();
        if (canvas) {
            canvas.toBlob((blob) => {
                if (blob) {
                    const croppedFile = new File([blob], 'profile.png', { type: 'image/png' });
                    emit('update:modelValue', croppedFile);
                }
                showCropperModal.value = false;
                if (imageSource.value) {
                    URL.revokeObjectURL(imageSource.value);
                    imageSource.value = null;
                }
            }, 'image/png');
        }
    }
};

const cancelCrop = () => {
    showCropperModal.value = false;
    if (imageSource.value) {
        URL.revokeObjectURL(imageSource.value);
        imageSource.value = null;
    }
    // Ao cancelar o crop, a pré-visualização deve voltar para a imagem original ou nulo, se não houver.
    previewImageUrl.value = props.initialImageUrl ? getImageUrl(props.initialImageUrl) : null;
};
</script>

<template>
    <div>
        <InputLabel for="path_image" value="Foto de Perfil" />
        <input id="path_image" type="file" @change="handleImageChange"
            class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"
            accept="image/*" />
        <InputError class="mt-2" :message="error" />

        <div v-if="previewImageUrl && !showCropperModal" class="mt-4">
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Pré-visualização da Imagem:</p>
            <img :src="previewImageUrl" alt="Pré-visualização da imagem"
                class="max-w-xs h-auto rounded-lg shadow-md max-h-36" />
        </div>

        <div v-if="showCropperModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-xl max-w-lg w-full">
                <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Cortar Imagem</h3>
                <div class="max-h-96 overflow-hidden mb-4">
                    <Cropper ref="cropperRef" :src="imageSource" :stencil-props="{ aspectRatio: 1 / 1 }"
                        class="cropper" />
                </div>
                <div class="flex justify-end space-x-4">
                    <button @click="cancelCrop" type="button"
                        class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600 transition duration-150 ease-in-out">
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
    background: #DDD;
}
</style>