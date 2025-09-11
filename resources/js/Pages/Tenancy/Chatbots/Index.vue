<script setup lang="ts">
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { TrashIcon, ChatBubbleLeftRightIcon } from '@heroicons/vue/24/solid';
import TextButton from '@/Components/Itens/TextButton.vue';
import CircleIconButton from '@/Components/Itens/CircleIconButton.vue';

interface Chat {
    id: number;
    title: string;
    lastMessage: string;
    timestamp: string;
}

const chats = ref<Chat[]>([
    { id: 1, title: 'Dúvida sobre o Produto X', lastMessage: 'Certo, me diga mais sobre sua dúvida.', timestamp: '10:03' },
    { id: 2, title: 'Suporte Técnico', lastMessage: 'Amanhã o técnico irá entrar em contato.', timestamp: 'Ontem' },
    { id: 3, title: 'Configuração de Conta', lastMessage: 'Obrigado por ajudar!', timestamp: 'Há 3 dias' },
]);
</script>

<template>

    <Head title="Histórico de Chats" />

    <AuthenticatedLayout>
        <div class="flex justify-end items-center mb-4">
            <TextButton :href="route('chats.new')">
                Novo Chat
            </TextButton>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <ul v-if="chats.length > 0">
                <li v-for="chat in chats" :key="chat.id"
                    class="flex items-center justify-between py-4 border-b border-gray-200 dark:border-gray-700 last:border-b-0">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center space-x-2">
                            <ChatBubbleLeftRightIcon class="w-5 h-5 text-gray-500 dark:text-gray-400" />
                            <span class="font-medium text-lg truncate">{{ chat.title }}</span>
                        </div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 truncate">
                            {{ chat.lastMessage }}
                        </p>
                    </div>
                    <div class="flex space-x-2 ml-4">
                        <CircleIconButton :href="route('chats.show', chat.id)">
                            <ChatBubbleLeftRightIcon class="w-5 h-5" />
                        </CircleIconButton>

                        <CircleIconButton :href="route('chats.show', chat.id)" color="red">
                            <TrashIcon class="w-5 h-5" />
                        </CircleIconButton>
                    </div>
                </li>
            </ul>
            <div v-else class="text-center text-gray-500 dark:text-gray-400">
                Nenhum chat encontrado. Crie um novo para começar!
            </div>
        </div>
    </AuthenticatedLayout>
</template>