<script setup lang="ts">
import IconButton from '@/Components/Itens/IconButton.vue';
import { PencilSquareIcon } from '@heroicons/vue/24/outline';

interface DocumentCategory {
    id: number;
    name: string;
    min_protocol: number;
    active_reserved_protocols: number[];
    next_available_protocol: number;
}

const props = defineProps<{
    categories: DocumentCategory[];
}>();
</script>

<template>
    <div v-if="props.categories.length > 0" class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                        Nome</th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                        Mínimo</th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                        Em reserva</th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                        Próximo</th>
                    <th scope="col" class="relative px-6 py-3"><span class="sr-only">Ações</span></th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                <tr v-for="category in props.categories" :key="category.id">
                    <td class="px-6 py-4 whitespace-normal">{{ category.name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ category.min_protocol }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span v-if="category.active_reserved_protocols && category.active_reserved_protocols.length > 0"
                            class="bg-yellow-100 text-yellow-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-yellow-900 dark:text-yellow-300">
                            {{ category.active_reserved_protocols.join(', ') }}
                        </span>
                        <span v-else>-</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ category.next_available_protocol }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <IconButton :href="route('document-categories.edit', category.id)" color="yellow"
                            title="Editar">
                            <PencilSquareIcon class="h-5 w-5" />
                        </IconButton>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div v-else>
        <p>Nenhuma categoria de documento encontrada.</p>
    </div>
</template>
