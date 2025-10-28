<script setup lang="ts">
interface Ticket {
    code: string;
    created_at: string;
}

defineProps<{
    ticket: Ticket;
}>();

const formatDate = (date: string) => {
    return new Date(date).toLocaleString("pt-BR", {
        day: "2-digit",
        month: "2-digit",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
};
</script>

<template>
    <div
        class="bg-gray-100 dark:bg-gray-800 border dark:border-gray-500 rounded-lg shadow-lg p-6 text-gray-900 dark:text-gray-100"
    >
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium opacity-90">Código do Ticket</p>
                <h1 class="text-xl font-bold mt-1">#{{ ticket.code }}</h1>
            </div>
            <div class="text-right">
                <p class="text-sm opacity-90">Criado em</p>
                <p class="text-md font-semibold">
                    {{ formatDate(ticket.created_at) }}
                </p>
            </div>
        </div>
    </div>
</template>
