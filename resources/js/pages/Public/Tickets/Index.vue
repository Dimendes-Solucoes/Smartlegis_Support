<script setup lang="ts">
import { ref } from "vue";
import AuthenticatedLayout from "@/layouts/AuthenticatedLayout.vue";
import { Head, router } from "@inertiajs/vue3";
import TextButton from "@/components/Itens/TextButton.vue";
import Pagination from "@/components/Table/Pagination.vue";
import TicketFilters from "./List/TicketFilters.vue";
import ViewModeToggle from "./List/ViewModeToggle.vue";
import TicketCard from "./List/TicketCard.vue";
import TicketTable from "./List/TicketTable.vue";
import EmptyState from "./List/EmptyState.vue";
import type {
    PaginatedTickets,
    TicketType,
    TicketStatus,
    TicketFilters as Filters,
    Author,
} from "@/types/ticket";

const props = defineProps<{
    tickets: PaginatedTickets;
    ticketTypes?: TicketType[];
    ticketStatuses?: TicketStatus[];
    authors?: Author[];
    filters?: Filters;
}>();

const viewMode = ref<"cards" | "table">("cards");

const applyFilters = (filters: Filters) => {
    router.get(
        route("tickets.index"),
        {
            search: filters.search,
            ticket_type_id: filters.ticket_type_id,
            status: filters.status,
            author_id: filters.author_id,
        },
        {
            preserveState: true,
            preserveScroll: true,
        }
    );
};

const clearFilters = () => {
    router.get(
        route("tickets.index"),
        {},
        {
            preserveState: true,
            preserveScroll: true,
        }
    );
};

const hasActiveFilters = () => {
    return !!(
        props.filters?.search ||
        props.filters?.ticket_type_id ||
        props.filters?.status ||
        props.filters?.author_id
    );
};
</script>

<template>
    <div>
        <Head title="Tickets" />

        <AuthenticatedLayout>
            <div
                class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6"
            >
                <ViewModeToggle v-model="viewMode" />

                <div class="flex gap-2">
                    <TextButton :href="route('tickets.create')"> Novo Ticket </TextButton>
                </div>
            </div>

            <TicketFilters
                :ticket-types="ticketTypes"
                :ticket-statuses="ticketStatuses"
                :authors="authors"
                :filters="filters"
                @apply="applyFilters"
                @clear="clearFilters"
            />

            <div v-if="viewMode === 'cards' && tickets.data.length > 0">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                    <TicketCard
                        v-for="ticket in tickets.data"
                        :key="ticket.id"
                        :ticket="ticket"
                        :view-route="route('tickets.view', ticket.code)"
                    />
                </div>
            </div>

            <TicketTable
                v-if="viewMode === 'table' && tickets.data.length > 0"
                :tickets="tickets.data"
            />

            <EmptyState
                v-if="tickets.data.length === 0"
                :has-active-filters="hasActiveFilters()"
                @clear-filters="clearFilters"
            />

            <Pagination :paginator="tickets" />
        </AuthenticatedLayout>
    </div>
</template>
