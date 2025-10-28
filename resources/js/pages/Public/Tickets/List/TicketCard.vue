<script setup lang="ts">
import { EyeIcon } from "@heroicons/vue/24/outline";
import CustomBadge from "@/components/Common/CustomBadge.vue";
import IconButton from "@/components/Itens/IconButton.vue";
import { Link } from "@inertiajs/vue3";

interface TicketStatus {
    id: number;
    title: string;
    color: string;
}

interface TicketType {
    id: number;
    title: string;
}

interface Author {
    id: number;
    name: string;
}

interface Ticket {
    id: number;
    code: string;
    title: string;
    description: string;
    ticket_status_id: number;
    ticket_type_id: number;
    author_id: number;
    status: TicketStatus;
    type: TicketType;
    author: Author;
    created_at: string;
}

defineProps<{
    ticket: Ticket;
    viewRoute: string;
}>();

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString("pt-BR", {
        day: "2-digit",
        month: "2-digit",
        year: "numeric",
    });
};

const truncate = (text: string, length: number = 100) => {
    return text.length > length ? text.substring(0, length) + "..." : text;
};
</script>

<template>
    <Link
        :href="viewRoute"
        class="bg-white dark:bg-gray-800 rounded-lg shadow-sm transition-shadow border border-gray-200 dark:border-gray-700 overflow-hidden group"
    >
        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex justify-between items-start">
                <span
                    class="text-xs font-mono text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded"
                >
                    {{ ticket.code }}
                </span>
                <CustomBadge :title="ticket.status.title" :color="ticket.status.color" />
            </div>
        </div>

        <div class="p-4">
            <div class="flex items-center justify-between text-sm">
                <div>
                    <h3
                        class="text-lg font-semibold text-gray-900 dark:text-white transition-colors"
                    >
                        {{ ticket.title }}
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                        <span class="font-medium">Descrição:</span>
                        {{ ticket.description }}
                    </p>
                    <p class="text-gray-500 dark:text-gray-400 mt-1">
                        <span class="font-medium">Autor:</span>
                        {{ ticket.author.name }}
                    </p>
                    <p class="text-gray-500 dark:text-gray-400 mt-1">
                        <span class="font-medium">Tipo:</span>
                        {{ ticket.type.title }}
                    </p>
                    <p class="text-gray-500 dark:text-gray-400 mt-1">
                        <span class="font-medium">Criado em:</span>
                        {{ formatDate(ticket.created_at) }}
                    </p>
                </div>
            </div>
        </div>
    </Link>
</template>
