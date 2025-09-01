<script setup lang="ts">
import { ref, watch } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import IconButton from '@/Components/Itens/IconButton.vue';
import TextButton from '@/Components/Itens/TextButton.vue';
import DocumentCategoryStatusBadge from '@/Components/DocumentCategory/DocumentCategoryStatusBadge.vue';
import { DocumentMinusIcon, DocumentPlusIcon, PencilSquareIcon } from '@heroicons/vue/24/solid';
import { Head, router } from '@inertiajs/vue3';
import Checkbox from '@/Components/Checkbox.vue';
import ConfirmDeletionModal from '@/Components/ConfirmDeletionModal.vue';

interface DocumentCategory {
    id: number;
    name: string;
    abbreviation: string;
    order: number;
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

    router.patch(route('document-categories.change_status', { id: categoryId }), {}, {
        preserveScroll: true,
        onSuccess: () => closeDeleteModal()
    });
};

const inactiveCategory = () => {
    if (categoryToInactive.value?.id) {
        changeStatus(categoryToInactive.value?.id)
    }
}

const showInactive = ref(props.filters?.show_inactive || false);

watch(showInactive, (value) => {
    router.get(route('document-categories.index'), { show_inactive: value }, {
        preserveState: true,
        replace: true,
    });
});

const categoryToInactive = ref<DocumentCategory | null>(null);

const openConfirmDeleteModal = (category: DocumentCategory) => {
    categoryToInactive.value = category;
}

const closeDeleteModal = () => {
    categoryToInactive.value = null;
}

const confirmResetModal = ref(false);

const openConfirmResetModal = () => {
    confirmResetModal.value = true;
};

const closeResetModal = () => {
    confirmResetModal.value = false;
};

const resetOrder = () => {
    router.put(route('document-categories.reset_order'), {}, {
        preserveScroll: true,
        onSuccess: () => closeResetModal()
    });
};

</script>

<template>

    <Head title="Categorias de Documentos" />

    <AuthenticatedLayout>
        <div class="flex justify-between items-center mb-4">
            <div class="flex items-center">
                <Checkbox id="show_inactive" v-model:checked="showInactive" />
                <label for="show_inactive" class="ml-2 text-sm text-gray-600 dark:text-gray-400">
                    Exibir inativas
                </label>
            </div>

            <div class="flex items-center space-x-2">
                <TextButton @click="openConfirmResetModal"
                    class="p-4 bg-gray-200 hover:bg-gray-300 text-gray-800 dark:bg-gray-600 dark:hover:bg-gray-500 dark:text-gray-200">
                    Resetar Ordem
                </TextButton>

                <TextButton :href="route('document-categories.create')" class="p-4">
                    Nova Categoria
                </TextButton>
            </div>
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
                            Ordem</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                            Prox Prot.</th>
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
                        <td class="px-6 py-4 whitespace-normal">{{ category.name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ category.abbreviation }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ category.order }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ Math.max(Number(category.min_protocol), (Number(category.highest_protocol) ?? 0) + 1) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <DocumentCategoryStatusBadge :status="category.is_active ? 1 : 0" />
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <IconButton :href="route('document-categories.edit', category.id)" color="yellow"
                                title="Editar">
                                <PencilSquareIcon class="h-5 w-5" />
                            </IconButton>

                            <IconButton v-if="category.is_active" @click="openConfirmDeleteModal(category)" color="red"
                                title="Desativar" class="ml-1">
                                <DocumentMinusIcon class="h-5 w-5" />
                            </IconButton>

                            <IconButton v-else @click="changeStatus(category.id)" color="green" title="Ativar"
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
    </AuthenticatedLayout>

    <ConfirmDeletionModal :show="categoryToInactive !== null" title="Inativar categoria"
        :message="`Tem certeza que deseja desativar a categoria '${categoryToInactive?.name}'?`"
        @close="closeDeleteModal" @confirm="inactiveCategory" button-text="Desativar" />

    <ConfirmDeletionModal :show="confirmResetModal" title="Resetar Ordem"
        message="Tem certeza que deseja resetar a ordem de todas as categorias? Essa ação não pode ser desfeita."
        @close="closeResetModal" @confirm="resetOrder" button-text="Resetar" />
</template>