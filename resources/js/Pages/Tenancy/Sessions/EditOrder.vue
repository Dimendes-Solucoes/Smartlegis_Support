<script setup lang="ts">
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DocumentList from './Document/DocumentList.vue';
import BackButtonRow from '@/Components/BackButtonRow.vue';
import ConfirmDeletionModal from '@/Components/ConfirmDeletionModal.vue';

interface Session {
    id: number;
    name: string;
}

interface Document {
    id: number;
    name: string;
    attachment: string;
}

const props = defineProps<{
    session: Session;
    agendaDocuments: Document[];
    extraDocuments: Document[];
}>();

const expedienteList = ref(props.extraDocuments);
const ordemDoDiaList = ref(props.agendaDocuments);

const isSaving = ref(false);
const isReseting = ref(false);

const saveOrder = () => {
    isSaving.value = true;
    const payload = {
        expediente_documents: expedienteList.value.map(doc => doc.id),
        ordem_do_dia_documents: ordemDoDiaList.value.map(doc => doc.id),
    };
    router.put(route('sessions.update_documents', props.session.id), payload, {
        preserveScroll: true,
        onFinish: () => isSaving.value = false,
    });
};

const resetOrder = () => {
    isReseting.value = true;
    router.put(route('sessions.reset_documents', props.session.id), {}, {
        preserveScroll: true,
        onFinish: () => isReseting.value = false,
    });
};

const confirmingDocRemoval = ref(false);
const docToRemove = ref<Document | null>(null);

const handleRemoveDocument = (document: Document) => {
    docToRemove.value = document;
    confirmingDocRemoval.value = true;
};

const closeModal = () => {
    confirmingDocRemoval.value = false;
    docToRemove.value = null;
};

const deleteDocumentFromSession = () => {
    if (!docToRemove.value) return;
    router.delete(route('sessions.documents.destroy', { 
        id: props.session.id, 
        document_id: docToRemove.value.id 
    }), {
        preserveScroll: true,
        onSuccess: () => {
            if (docToRemove.value) {
                expedienteList.value = expedienteList.value.filter(
                    doc => doc.id !== docToRemove.value?.id
                );
                ordemDoDiaList.value = ordemDoDiaList.value.filter(
                    doc => doc.id !== docToRemove.value?.id
                );
            }
            closeModal();
        },
    });
};
</script>

<template>
    <Head title="Documentos" />
    <AuthenticatedLayout>
        <BackButtonRow :href="route('sessions.index')" />
        <div class="flex items-center justify-end mb-4">
            <PrimaryButton @click="resetOrder" :disabled="isReseting" :class="{ 'opacity-25': isReseting }">
                <span v-if="isReseting">Salvando...</span>
                <span v-else>Restaurar Ordem</span>
            </PrimaryButton>
            <PrimaryButton @click="saveOrder" :disabled="isSaving" :class="{ 'opacity-25': isSaving, 'ml-1': true }">
                <span v-if="isSaving">Salvando...</span>
                <span v-else>Salvar Ordem</span>
            </PrimaryButton>
        </div>
        <div class="gap-8">
            <DocumentList 
                title="Expediente do Dia" 
                description="Documentos que serão discutidos no início da sessão."
                :session="session" 
                :documents="expedienteList" 
                @update:documents="expedienteList = $event"
                @remove-document="handleRemoveDocument" 
            />
            <DocumentList 
                title="Ordem do Dia"
                description="Documentos principais a serem votados ou discutidos em profundidade." 
                :session="session"
                :documents="ordemDoDiaList" 
                @update:documents="ordemDoDiaList = $event"
                @remove-document="handleRemoveDocument" 
            />
        </div>
    </AuthenticatedLayout>
    <ConfirmDeletionModal 
        :show="confirmingDocRemoval" 
        title="Remover Documento da Pauta"
        :message="`Tem certeza que deseja remover o documento '${docToRemove?.name}' desta sessão?`"
        buttonText="Remover da Pauta"
        @close="closeModal" 
        @confirm="deleteDocumentFromSession" 
    />
</template>