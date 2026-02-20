<script setup lang="ts">
import { ChatBubbleLeftIcon, PaperClipIcon } from "@heroicons/vue/24/outline";
import CustomBadge from "@/components/Common/CustomBadge.vue";
import { Link } from "@inertiajs/vue3";
import { Ticket } from "@/types/ticket";

defineProps<{
    ticket: Ticket;
    viewRoute: string;
}>();

const formatDateTime = (date: string) => {
    return new Date(date).toLocaleDateString("pt-BR", {
        day: "2-digit",
        month: "2-digit",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
};

const truncate = (text: string, length: number = 80) => {
    return text.length > length ? text.substring(0, length) + "..." : text;
};
</script>

<template>
    <Link
        :href="viewRoute"
        class="block bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition-all border border-gray-200 dark:border-gray-700 overflow-hidden"
    >
        <div class="px-4 pt-4 pb-3 flex items-center justify-between">
            <div class="flex items-center gap-2">
                {{ console.log(ticket) }}
                <CustomBadge
                    :title="ticket.status_details.title"
                    :color="ticket.status_details.color"
                />
                <CustomBadge
                    :title="ticket.type.title"
                    color="#666666"
                    v-if="ticket.type"
                />
            </div>
        </div>

        <div class="px-4 pb-2">
            <span class="text-xs font-mono text-gray-500 dark:text-gray-400">
                {{ ticket.code }}
            </span>
        </div>

        <div class="px-4 pb-2">
            <h3
                class="text-base font-semibold text-gray-900 dark:text-white line-clamp-2"
            >
                {{ ticket.title }}
            </h3>
        </div>

        <div class="px-4 pb-4">
            <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2">
                {{ truncate(ticket.description) }}
            </p>
        </div>

        <div class="px-4 pb-4 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div
                    class="w-6 h-6 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white text-xs font-semibold"
                >
                    {{ ticket.author.name.charAt(0).toUpperCase() }}
                </div>
                <span class="text-sm text-gray-700 dark:text-gray-300">
                    {{ ticket.author.name }}
                </span>
            </div>

            <div class="flex items-center gap-3 text-gray-500 dark:text-gray-400">
                <div class="flex items-center gap-1" v-if="ticket.messages_count">
                    <ChatBubbleLeftIcon class="w-4 h-4" />
                    <span class="text-xs">{{ ticket.messages_count }}</span>
                </div>
                <div class="flex items-center gap-1" v-if="ticket.attachments_count">
                    <PaperClipIcon class="w-4 h-4" />
                    <span class="text-xs">{{ ticket.attachments_count }}</span>
                </div>
            </div>
        </div>

        <div
            class="px-4 pb-4 flex justify-between text-xs text-gray-500 dark:text-gray-400"
        >
            <div>
                <span class="font-medium">Criado:</span>
                <span class="ml-1">{{ formatDateTime(ticket.created_at) }}</span>
            </div>
            <div>
                <span class="font-medium">Atualizado:</span>
                <span class="ml-1">{{ formatDateTime(ticket.updated_at) }}</span>
            </div>
        </div>
    </Link>
</template>
