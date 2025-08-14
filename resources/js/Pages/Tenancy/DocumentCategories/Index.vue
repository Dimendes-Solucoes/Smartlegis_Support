<script setup lang="ts">
import { ref, watch } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import IconButton from '@/Components/Itens/IconButton.vue';
import TextButton from '@/Components/Itens/TextButton.vue';
import DocumentCategoryStatusBadge from '@/Components/DocumentCategory/DocumentCategoryStatusBadge.vue';
import { DocumentMinusIcon, DocumentPlusIcon, PencilSquareIcon } from '@heroicons/vue/24/solid';
import { Head, router } from '@inertiajs/vue3';
import Checkbox from '@/Components/Checkbox.vue';

interface DocumentCategory {
    id: number;
    name: string;
    abbreviation: string;
    min_protocol: number;
    highest_protocol: number | null;
    is_active: boolean;
}

const props = defineProps<{
    categories: DocumentCategory[];
    filters: {
        show_inactive?: boolean;
    }
}>();

const changeStatus = (categoryId: number): void => {
    const category = props.categories.find(c => c.id === categoryId);
    if (!category) return;
    const action = category.is_active ? 'desativar' : 'ativar';
    if (confirm(`Tem certeza que deseja ${action} esta categoria?`)) {
        router.patch(route('document-categories.change_status', { id: categoryId }), {}, {
            preserveScroll: true
        });
    }
};


const showInactive = ref(props.filters?.show_inactive || false);

watch(showInactive, (value) => {
    router.get(route('document-categories.index'), { show_inactive: value }, {
        preserveState: true,
        replace: true,
    });
});
</script>

<template>

    <Head title="Categorias de Documentos" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="flex justify-between items-center mb-4">
                            <div class="flex items-center">
                                <Checkbox id="show_inactive" v-model:checked="showInactive" />
                                <label for="show_inactive" class="ml-2 text-sm text-gray-600 dark:text-gray-400">
                                    Exibir inativas
                                </label>
                            </div>

                            <TextButton :href="route('document-categories.create')" class="p-4">
                                Nova Categoria
                            </TextButton>
                        </div>

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
                                            Protocolo Mínimo</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                            Protocolo Usado</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                            Status</th>
                                        <th scope="col" class="relative px-6 py-3"><span class="sr-only">Ações</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                    <tr v-for="category in props.categories" :key="category.id">
                                        <td class="px-6 py-4 whitespace-nowrap">{{ category.id }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ category.name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ category.abbreviation }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ category.min_protocol }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ category.highest_protocol ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <DocumentCategoryStatusBadge :status="category.is_active ? 1 : 0" />
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <IconButton :href="route('document-categories.edit', category.id)"
                                                color="yellow" title="Editar">
                                                <PencilSquareIcon class="h-5 w-5" />
                                            </IconButton>
                                            <IconButton @click="changeStatus(category.id)"
                                                :color="category.is_active ? 'red' : 'green'"
                                                :title="category.is_active ? 'Desativar Categoria' : 'Ativar Categoria'"
                                                href="#" class="ml-1">
                                                <template v-if="category.is_active">
                                                    <DocumentMinusIcon class="h-5 w-5" />
                                                </template>
                                                <template v-else>
                                                    <DocumentPlusIcon class="h-5 w-5" />
                                                </template>
                                            </IconButton>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div v-else>
                            <p>Nenhuma categoria de documento encontrada.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>