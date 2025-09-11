<script setup lang="ts">
import { defineProps } from 'vue';

interface Message {
    id: number;
    text?: string;
    imageUrl?: string;
    sender: 'me' | 'other';
    timestamp: string;
}

const props = defineProps<{
    message: Message;
    isMine: boolean;
    isPreviousSameSender: boolean;
}>();
</script>

<template>
    <div class="flex" :class="{
        'justify-end': isMine,
        'justify-start': !isMine,
        'mt-1': isPreviousSameSender,
        'mt-2': !isPreviousSameSender
    }">
        <div class="flex items-end gap-2 max-w-xs lg:max-w-md p-2 rounded-lg shadow-md relative" :class="{
            'bg-blue-500 text-white dark:bg-blue-600': isMine,
            'bg-gray-300 text-gray-800 dark:bg-gray-600 dark:text-gray-100': !isMine
        }">
            <div class="flex flex-col">
                <p v-if="message.text" class="text-sm break-words">{{ message.text }}</p>
                <img v-if="message.imageUrl" :src="message.imageUrl" alt="Imagem anexada"
                    class="mt-2 rounded-md max-w-full h-auto object-cover" style="max-height: 200px;" />
            </div>
            <span class="text-xs opacity-75 whitespace-nowrap" :class="{
                'text-blue-100 dark:text-blue-200': isMine,
                'text-gray-600 dark:text-gray-300': !isMine
            }">
                {{ message.timestamp }}
            </span>
            <div class="absolute w-0 h-0 border-solid" :class="{
                'border-l-[10px] border-l-blue-500 border-t-[10px] border-t-transparent border-b-[10px] border-b-transparent top-1/2 -right-2 transform -translate-y-1/2 dark:border-l-blue-600': isMine,
                'border-r-[10px] border-r-gray-300 border-t-[10px] border-t-transparent border-b-[10px] border-b-transparent top-1/2 -left-2 transform -translate-y-1/2 dark:border-r-gray-600': !isMine
            }"></div>
        </div>
    </div>
</template>

<style scoped>
.justify-end>div {
    margin-right: 10px;
}

.justify-start>div {
    margin-left: 10px;
}
</style>