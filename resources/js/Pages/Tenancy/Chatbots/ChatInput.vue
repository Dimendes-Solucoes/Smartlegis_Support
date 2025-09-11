<script setup lang="ts">
import { ref, defineEmits } from 'vue';
import { PaperClipIcon, PaperAirplaneIcon } from '@heroicons/vue/24/solid';

const emits = defineEmits(['sendMessage']);

const messageText = ref('');
const selectedImage = ref<File | null>(null);
const previewImageUrl = ref<string | null>(null);
const fileInput = ref<HTMLInputElement | null>(null);

const handleFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        selectedImage.value = target.files[0];
        previewImageUrl.value = URL.createObjectURL(target.files[0]);
    }
};

const triggerFileInput = () => {
    fileInput.value?.click();
};

const removeImage = () => {
    selectedImage.value = null;
    previewImageUrl.value = null;
    if (fileInput.value) {
        fileInput.value.value = '';
    }
};

const handleKeyDown = (event: KeyboardEvent) => {
    if (event.key === 'Enter' && !event.shiftKey) {
        event.preventDefault();
        sendMessage();
    }
};

const sendMessage = () => {
    if (messageText.value.trim() === '' && !selectedImage.value) {
        return;
    }

    emits('sendMessage', {
        text: messageText.value.trim(),
        imageUrl: previewImageUrl.value
    });

    messageText.value = '';
    removeImage();
};
</script>

<template>
    <div
        class="p-4 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-600 flex flex-col space-y-2 rounded-b-lg">
        <div v-if="previewImageUrl" class="relative">
            <img :src="previewImageUrl"
                class="w-24 h-24 object-cover rounded-md border border-gray-300 dark:border-gray-500"
                alt="Preview da imagem" />
            <button @click="removeImage" class="absolute -top-2 bg-red-500 text-white rounded-full p-1 text-xs">
                &times;
            </button>
        </div>

        <div class="flex items-end space-x-2">
            <button @click="triggerFileInput"
                class="p-2 rounded-full bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-300 dark:hover:bg-gray-500 transition duration-150"
                title="Anexar imagem">
                <PaperClipIcon class="w-6 h-6" />
            </button>
            <input type="file" ref="fileInput" @change="handleFileChange" accept="image/*" class="hidden" />

            <textarea v-model="messageText" @keydown="handleKeyDown" rows="1"
                class="flex-1 resize-none overflow-hidden p-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-100"
                placeholder="Digite sua mensagem..."></textarea>

            <button @click="sendMessage" :disabled="messageText.trim() === '' && !selectedImage"
                class="p-2 rounded-full bg-blue-600 text-white dark:bg-blue-700 hover:bg-blue-700 dark:hover:bg-blue-800 transition duration-150 disabled:opacity-50 disabled:cursor-not-allowed"
                title="Enviar mensagem">
                <PaperAirplaneIcon class="w-6 h-6" />
            </button>
        </div>
    </div>
</template>

<style scoped>
textarea {
    min-height: 40px;
    max-height: 120px;
}
</style>