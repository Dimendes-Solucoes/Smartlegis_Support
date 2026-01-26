<script setup lang="ts">
import { ref } from 'vue';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import ConfirmDeletionModal from '@/components/Common/ConfirmDeletionModal.vue';
import DocumentModelTable from './DocumentModelTable.vue';

// Interface baseada na sua Model
interface DocumentModel {
    id: number;
    title: string;
    body: string;
    user_id: number;
    document_category_id: number;
    user?: { name: string };
    category?: { name: string };
    created_at: string;
    updated_at: string;
}

const props = defineProps<{
    documentModels: DocumentModel[]; 
}>();

const modelToDelete = ref<DocumentModel | null>(null);

const openConfirmDeleteModal = (model: DocumentModel) => {
    modelToDelete.value = model;
}

const closeDeleteModal = () => {
    modelToDelete.value = null;
}

const deleteModel = () => {
    if (modelToDelete.value?.id) {
        router.delete(route('document-models.destroy', { id: modelToDelete.value.id }), {
            preserveScroll: true,
            onSuccess: () => closeDeleteModal()
        });
    }
}
</script>

<template>
    <Head title="Modelos de Documentos" />

    <AuthenticatedLayout>
        <div class="flex justify-between items-center mb-4">
            <div class="flex items-center space-x-4">
                <span class="text-gray-500 text-sm">
                    {{ props.documentModels.length }} modelos encontrados
                </span>
            </div>

            <div class="flex items-center space-x-2">
                </div>
        </div>

        <DocumentModelTable 
            :models="props.documentModels"
            @open-confirm-delete-modal="openConfirmDeleteModal" 
        />
    </AuthenticatedLayout>

    <ConfirmDeletionModal 
        :show="modelToDelete !== null" 
        title="Excluir Modelo"
        :message="`Tem certeza que deseja excluir o modelo '${modelToDelete?.title}'? Essa ação não pode ser desfeita.`"
        @close="closeDeleteModal" 
        @confirm="deleteModel" 
        button-text="Excluir" 
    />
</template>