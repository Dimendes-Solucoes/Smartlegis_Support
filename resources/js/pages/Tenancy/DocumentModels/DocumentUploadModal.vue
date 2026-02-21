<script setup lang="ts">
import { ref } from 'vue';
import Modal from '@/components/Common/Modal.vue';
import PrimaryButton from '@/components/Common/PrimaryButton.vue';
import SecondaryButton from '@/components/Common/SecondaryButton.vue';
import InputLabel from '@/components/Form/InputLabel.vue';
import InputError from '@/components/Form/InputError.vue';

const props = defineProps<{
    show: boolean;
}>();

const emit = defineEmits<{
    (e: 'close'): void;
    (e: 'converted', html: string): void;
}>();

const fileInput = ref<HTMLInputElement>();
const selectedFile = ref<File | null>(null);
const isConverting = ref(false);
const error = ref('');
const dragOver = ref(false);

const handleFileSelect = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];

    if (file) {
        validateAndSetFile(file);
    }
};

const handleDrop = (event: DragEvent) => {
    dragOver.value = false;
    const file = event.dataTransfer?.files[0];

    if (file) {
        validateAndSetFile(file);
    }
};

const validateAndSetFile = (file: File) => {
    error.value = '';

    const validTypes = [
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // .docx
        'application/msword', // .doc
        'application/pdf'
    ];

    const extension = file.name.split('.').pop()?.toLowerCase();

    if (!validTypes.includes(file.type) && !['docx', 'doc', 'pdf'].includes(extension || '')) {
        error.value = 'Por favor, selecione um arquivo Word (.doc, .docx) ou PDF (.pdf)';
        selectedFile.value = null;
        return;
    }

    if (file.size > 10 * 1024 * 1024) { // 10MB
        error.value = 'O arquivo é muito grande. Tamanho máximo: 10MB';
        selectedFile.value = null;
        return;
    }

    selectedFile.value = file;
};

const convertToHtml = async () => {
    if (!selectedFile.value) return;

    isConverting.value = true;
    error.value = '';

    try {
        const formData = new FormData();
        formData.append('file', selectedFile.value);

        const response = await fetch(route('document-models.convert'), {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Accept': 'application/json',
            },
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.message || 'Erro ao converter o arquivo');
        }

        emit('converted', data.html);
        closeModal();

    } catch (err: any) {
        error.value = err.message || 'Erro ao converter o arquivo. Tente novamente.';
    } finally {
        isConverting.value = false;
    }
};

const closeModal = () => {
    selectedFile.value = null;
    error.value = '';
    isConverting.value = false;

    if (fileInput.value) {
        fileInput.value.value = '';
    }

    emit('close');
};

const triggerFileInput = () => {
    fileInput.value?.click();
};
</script>

<template>
    <Modal :show="show" @close="closeModal" max-width="lg">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                Importar Documento
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Faça upload de um arquivo Word (.doc, .docx) ou PDF para converter em HTML e usar como modelo.
            </p>

            <div class="mt-6">
                <input ref="fileInput" type="file" accept=".doc,.docx,.pdf" class="hidden" @change="handleFileSelect" />

                <!-- Área de Drop -->
                <div @click="triggerFileInput" @dragover.prevent="dragOver = true" @dragleave.prevent="dragOver = false"
                    @drop.prevent="handleDrop" :class="[
                        'border-2 border-dashed rounded-lg p-8 text-center cursor-pointer transition-colors',
                        dragOver
                            ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20'
                            : 'border-gray-300 dark:border-gray-600 hover:border-indigo-400 dark:hover:border-indigo-500'
                    ]">
                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48"
                        aria-hidden="true">
                        <path
                            d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>

                    <div class="mt-4">
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                            <template v-if="selectedFile">
                                <svg class="inline w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                {{ selectedFile.name }}
                            </template>
                            <template v-else>
                                Clique para selecionar ou arraste o arquivo aqui
                            </template>
                        </p>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            Word (DOC, DOCX) ou PDF até 10MB
                        </p>
                    </div>
                </div>

                <InputError class="mt-2" :message="error" />
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <SecondaryButton @click="closeModal" :disabled="isConverting">
                    Cancelar
                </SecondaryButton>

                <PrimaryButton @click="convertToHtml" :disabled="!selectedFile || isConverting"
                    :class="{ 'opacity-25': isConverting }">
                    <svg v-if="isConverting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none"
                        viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                        </circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    <span v-if="isConverting">Convertendo...</span>
                    <span v-else>Converter e Importar</span>
                </PrimaryButton>
            </div>
        </div>
    </Modal>
</template>