<script setup lang="ts">
import { EyeIcon } from "@heroicons/vue/24/outline";
import RegularColumn from "@/components/Table/RegularColumn.vue";
import CustomBadge from "@/components/Common/CustomBadge.vue";
import IconButton from "@/components/Itens/IconButton.vue";

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
    tickets: Ticket[];
}>();

const emit = defineEmits<{
    view: [code: string];
}>();

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString("pt-BR", {
        day: "2-digit",
        month: "2-digit",
        year: "numeric",
    });
};
</script>

<template>
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
        <div class="overflow-x-auto">
            <table
                class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm"
            >
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <RegularColumn>Código</RegularColumn>
                        <RegularColumn>Ticket</RegularColumn>
                        <RegularColumn>Tipo</RegularColumn>
                        <RegularColumn>Status</RegularColumn>
                        <RegularColumn>Data</RegularColumn>
                        <th class="relative px-6 py-3">
                            <span class="sr-only">Ações</span>
                        </th>
                    </tr>
                </thead>
                <tbody
                    class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700"
                >
                    <tr
                        v-for="ticket in tickets"
                        :key="ticket.id"
                        class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors"
                    >
                        <td class="px-6 py-4">
                            {{ ticket.code }}
                        </td>
                        <td class="px-6 py-4">
                            {{ ticket.title }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ ticket.type.title }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <CustomBadge
                                :title="ticket.status.title"
                                :color="ticket.status.color"
                            />
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ formatDate(ticket.created_at) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <IconButton
                                :href="route('tickets.view', ticket.code)"
                                title="Visualizar"
                            >
                                <EyeIcon class="h-5 w-5" />
                            </IconButton>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
