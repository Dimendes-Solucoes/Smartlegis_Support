<script setup lang="ts">
import { ref, watch } from 'vue';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import TextButton from '@/components/Itens/TextButton.vue';
import { Head, router } from '@inertiajs/vue3';
import Checkbox from '@/components/Form/Checkbox.vue';
import ConfirmDeletionModal from '@/components/Common/ConfirmDeletionModal.vue';
import DocumentCategoryTable from './DocumentCategoryTable.vue';
import ProtocolTable from './ProtocolTable.vue';

interface DocumentCategory {
    id: number;
    name: string;
    abbreviation: string;
    order: number;
    min_protocol: number;
    highest_protocol: number | null;
    is_active: boolean;
    active_reserved_protocols: number[];
    next_available_protocol: number;
}

const props = defineProps<{
    categories: DocumentCategory[];
    filters: {
        show_inactive: boolean;
        show_protocols: boolean;
    }
}>();

const localFilters = ref({
    show_inactive: props.filters.show_inactive,
    show_protocols: props.filters.show_protocols
});

watch(
    () => localFilters.value,
    (newFilters) => {
        router.get(route('document-categories.index'), {
            show_inactive: newFilters.show_inactive,
            show_protocols: newFilters.show_protocols
        }, {
            preserveState: true,
            replace: true,
        });
    },
    { deep: true }
);

const changeStatus = (categoryId: number): void => {
    router.patch(route('document-categories.change_status', { id: categoryId }), {}, {
        preserveScroll: true,
        onSuccess: () => closeDeleteModal()
    });
};

const categoryToInactive = ref<DocumentCategory | null>(null);

const openConfirmDeleteModal = (category: DocumentCategory) => {
    categoryToInactive.value = category;
}

const closeDeleteModal = () => {
    categoryToInactive.value = null;
}

const inactiveCategory = () => {
    if (categoryToInactive.value?.id) {
        changeStatus(categoryToInactive.value?.id)
    }
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
            <div class="flex items-center space-x-4">
                <div class="flex items-center">
                    <Checkbox id="show_protocols" v-model:checked="localFilters.show_protocols" />
                    <label for="show_protocols" class="ml-2 text-sm text-gray-600 dark:text-gray-400">
                        Protocolos
                    </label>
                </div>

                <div class="flex items-center">
                    <Checkbox id="show_inactive" v-model:checked="localFilters.show_inactive" />
                    <label for="show_inactive" class="ml-2 text-sm text-gray-600 dark:text-gray-400">
                        Exibir inativas
                    </label>
                </div>
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

        <DocumentCategoryTable v-if="!props.filters.show_protocols" :categories="props.categories"
            @open-confirm-delete-modal="openConfirmDeleteModal" @change-status="changeStatus" />

        <ProtocolTable v-else :categories="props.categories" />
    </AuthenticatedLayout>

    <ConfirmDeletionModal :show="categoryToInactive !== null" title="Inativar categoria"
        :message="`Tem certeza que deseja desativar a categoria '${categoryToInactive?.name}'?`"
        @close="closeDeleteModal" @confirm="inactiveCategory" button-text="Desativar" />

    <ConfirmDeletionModal :show="confirmResetModal" title="Resetar Ordem"
        message="Tem certeza que deseja resetar a ordem de todas as categorias? Essa ação não pode ser desfeita."
        @close="closeResetModal" @confirm="resetOrder" button-text="Resetar" />
</template>