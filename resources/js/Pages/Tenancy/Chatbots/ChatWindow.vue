<script setup lang="ts">
import { ref, nextTick } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import ChatMessage from './ChatMessage.vue';
import ChatInput from './ChatInput.vue';

interface Message {
    id: number;
    text?: string;
    imageUrl?: string;
    sender: 'me' | 'other';
    timestamp: string;
}

const messages = ref<Message[]>([
    { id: 1, text: 'Olá! Como posso ajudar?', sender: 'other', timestamp: '10:00' },
    { id: 2, text: 'Oi! Estou com uma dúvida sobre o produto X.', sender: 'me', timestamp: '10:01' },
    { id: 3, imageUrl: 'https://via.placeholder.com/150', sender: 'other', timestamp: '10:02' },
    { id: 4, text: 'Certo, me diga mais sobre sua dúvida.', sender: 'other', timestamp: '10:03' },
]);

const chatContainer = ref<HTMLElement | null>(null);

const addMessage = async ({ text, imageUrl }: { text?: string; imageUrl?: string }) => {
    if (!text && !imageUrl) return;

    const newMessage: Message = {
        id: messages.value.length + 1,
        text,
        imageUrl,
        sender: 'me',
        timestamp: new Date().toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' }),
    };
    messages.value.push(newMessage);

    await nextTick(() => {
        if (chatContainer.value) {
            chatContainer.value.scrollTop = chatContainer.value.scrollHeight;
        }
    });
};
</script>

<template>

    <Head title="Chatbot" />

    <AuthenticatedLayout>

        <div class="flex flex-col h-[82vh] bg-gray-100 dark:bg-gray-700 rounded-lg shadow-md">
            <div ref="chatContainer" class="flex-1 p-2 overflow-y-auto space-y-2">
                <ChatMessage v-for="(message, index) in messages" :key="message.id" :message="message"
                    :is-mine="message.sender === 'me'"
                    :is-previous-same-sender="index > 0 && messages[index - 1].sender === message.sender" />
            </div>

            <ChatInput @sendMessage="addMessage" />
        </div>
    </AuthenticatedLayout>
</template>