<script setup lang="ts">
import IconButton from '@/Components/Itens/IconButton.vue';
import DocumentCategoryStatusBadge from '@/Components/DocumentCategory/DocumentCategoryStatusBadge.vue';
import { DocumentMinusIcon, DocumentPlusIcon, PencilSquareIcon } from '@heroicons/vue/24/outline';

interface DocumentCategory {
    id: number;
    name: string;
    abbreviation: string;
    order: number;
    is_active: boolean;
}

const props = defineProps<{
    categories: DocumentCategory[];
}>();

const emit = defineEmits(['open-confirm-delete-modal', 'change-status']);
</script>

<template>
    <div v-if="props.categories.length > 0" class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                        ID</th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                        Nome</th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                        Abreviação</th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                        Ordem</th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                        Status</th>
                    <th scope="col" class="relative px-6 py-3"><span class="sr-only">Ações</span></th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                <tr v-for="category in props.categories" :key="category.id">
                    <td class="px-6 py-4 whitespace-nowrap">{{ category.id }}</td>
                    <td class="px-6 py-4 whitespace-normal">{{ category.name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ category.abbreviation }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ category.order }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <DocumentCategoryStatusBadge :status="category.is_active ? 1 : 0" />
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <IconButton :href="route('document-categories.edit', category.id)" color="yellow"
                            title="Editar">
                            <PencilSquareIcon class="h-5 w-5" />
                        </IconButton>
                        <IconButton v-if="category.is_active" @click="$emit('open-confirm-delete-modal', category)"
                            color="red" title="Desativar" class="ml-1">
                            <DocumentMinusIcon class="h-5 w-5" />
                        </IconButton>
                        <IconButton v-else @click="$emit('change-status', category.id)" color="green" title="Ativar"
                            class="ml-1">
                            <DocumentPlusIcon class="h-5 w-5" />
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
