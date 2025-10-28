<script setup lang="ts">
import { ref, computed } from "vue";
import { MagnifyingGlassIcon } from "@heroicons/vue/24/outline";
import PrimaryButton from "@/components/Common/PrimaryButton.vue";

interface TicketStatus {
    id: number;
    title: string;
    color: string;
}

interface TicketType {
    id: number;
    title: string;
}

interface Filters {
    search?: string;
    ticket_type_id?: number;
    ticket_status_id?: number;
}

const props = defineProps<{
    ticketTypes?: TicketType[];
    ticketStatuses?: TicketStatus[];
    filters?: Filters;
}>();

const emit = defineEmits<{
    apply: [filters: Filters];
    clear: [];
}>();

const searchQuery = ref(props.filters?.search || "");
const selectedType = ref(props.filters?.ticket_type_id || null);
const selectedStatus = ref(props.filters?.ticket_status_id || null);

const activeFiltersCount = computed(() => {
    let count = 0;
    if (searchQuery.value) count++;
    if (selectedType.value) count++;
    if (selectedStatus.value) count++;
    return count;
});

const applyFilters = () => {
    emit("apply", {
        search: searchQuery.value || undefined,
        ticket_type_id: selectedType.value || undefined,
        ticket_status_id: selectedStatus.value || undefined,
    });
};

const clearFilters = () => {
    searchQuery.value = "";
    selectedType.value = null;
    selectedStatus.value = null;
    emit("clear");
};
</script>

<template>
    <div class="bg-white dark:bg-gray-800 rounded-lg mb-6">
        <form @submit.prevent="applyFilters">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div v-if="ticketTypes && ticketTypes.length > 0">
                    <label
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                    >
                        Tipo de Ticket
                    </label>
                    <select
                        v-model="selectedType"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                    >
                        <option :value="null">Todos os tipos</option>
                        <option
                            v-for="type in ticketTypes"
                            :key="type.id"
                            :value="type.id"
                        >
                            {{ type.title }}
                        </option>
                    </select>
                </div>

                <div v-if="ticketStatuses && ticketStatuses.length > 0">
                    <label
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                    >
                        Status
                    </label>
                    <select
                        v-model="selectedStatus"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                    >
                        <option :value="null">Todos os status</option>
                        <option
                            v-for="status in ticketStatuses"
                            :key="status.id"
                            :value="status.id"
                        >
                            {{ status.title }}
                        </option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                    >
                        Pesquisar
                    </label>
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Buscar por título ou código..."
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                    />
                </div>
            </div>

            <div class="flex justify-end items-center space-x-4 mt-4">
                <PrimaryButton type="submit" class="h-9 flex items-center">
                    <MagnifyingGlassIcon class="h-5 w-5 mr-2" />
                    <span>Pesquisar</span>
                </PrimaryButton>

                <button
                    v-if="activeFiltersCount > 0"
                    type="button"
                    @click="clearFilters"
                    class="h-9 px-4 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg transition-colors"
                >
                    Limpar
                </button>
            </div>
        </form>
    </div>
</template>
