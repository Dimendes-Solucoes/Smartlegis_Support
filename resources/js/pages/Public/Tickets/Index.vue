<script setup lang="ts">
import AuthenticatedLayout from "@/layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import TextButton from "@/components/Itens/TextButton.vue";
import RegularColumn from "@/components/Table/RegularColumn.vue";
import CustomBadge from "@/components/Common/CustomBadge.vue";
import Pagination from "@/components/Table/Pagination.vue";

interface TicketStatus {
    title: string;
    color: string;
}

interface TicketType {
    title: string;
}

interface Author {
    name: string;
}

interface Ticket {
    id: number;
    title: string;
    description: string;
    ticket_status_id: number;
    ticket_type_id: number;
    author_id: number;
    status: TicketStatus;
    type: TicketType;
    author: Author;
}

interface PaginatedTickets {
    data: Ticket[];
    links: {
        url: string | null;
        label: string;
        active: boolean;
    }[];
}

const props = defineProps<{
    tickets: PaginatedTickets;
    links: {
        url: string | null;
        label: string;
        active: boolean;
    }[];
}>();
</script>

<template>
    <div>
        <Head title="Tickets" />

        <AuthenticatedLayout>
            <div class="flex justify-end items-center mb-6">
                <TextButton :href="route('tickets.create')">Novo Ticket</TextButton>
            </div>
            <div
                class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg"
            >
                <div v-if="tickets.data.length > 0" class="overflow-x-auto">
                    <table
                        class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm"
                    >
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <RegularColumn>Título</RegularColumn>
                                <RegularColumn>Autor</RegularColumn>
                                <RegularColumn>Tipo</RegularColumn>
                                <RegularColumn>Status</RegularColumn>
                                <th class="relative px-6 py-3">
                                    <span class="sr-only">Ações</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody
                            class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700"
                        >
                            <tr
                                v-for="ticket in props.tickets.data"
                                :key="ticket.id"
                                class="hover:bg-gray-50 dark:hover:bg-gray-700/50"
                            >
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ ticket.title }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ ticket.author.name }}
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
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-else class="p-6 text-center text-gray-500">
                    <p>Nenhum ticket encontrado.</p>
                </div>
            </div>

            <Pagination :paginator="tickets" />
        </AuthenticatedLayout>
    </div>
</template>
