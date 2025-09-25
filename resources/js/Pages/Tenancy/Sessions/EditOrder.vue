<script setup lang="ts">
import { ref, watch } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DocumentList from './DocumentList.vue';
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

interface DocumentToRemove extends Document {
    listType: 'expediente' | 'ordemDoDia';
}

const props = defineProps<{
    session: Session;
    orderDocuments: Document[];
    expedientDocuments: Document[];
}>();

const expedientList = ref(props.expedientDocuments);
const orderList = ref(props.orderDocuments);

watch(() => props.expedientDocuments, (newExpedientDocuments) => {
    expedientList.value = newExpedientDocuments;
}, { deep: true });

watch(() => props.orderDocuments, (newOrderDocuments) => {
    orderList.value = newOrderDocuments;
}, { deep: true });

const isSaving = ref(false);
const isReseting = ref(false);

const confirmingDocRemoval = ref(false);
const docToRemove = ref<DocumentToRemove | null>(null);

const saveOrder = () => {
    isSaving.value = true;
    const payload = {
        expediente_documents: expedientList.value.map(doc => doc.id),
        ordem_do_dia_documents: orderList.value.map(doc => doc.id),
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

const handleRemoveDocument = (document: Document, listType: 'expediente' | 'ordemDoDia') => {
    docToRemove.value = { ...document, listType };
    confirmingDocRemoval.value = true;
};

const closeModal = () => {
    confirmingDocRemoval.value = false;
    docToRemove.value = null;
};

const deleteDocumentFromSession = () => {
    const doc = docToRemove.value;
    if (!doc) return;

    let routeName: string;

    if (doc.listType === 'expediente') {
        routeName = 'sessions.documents.destroy_expendient';
    } else if (doc.listType === 'ordemDoDia') {
        routeName = 'sessions.documents.destroy_order';
    } else {
        closeModal();
        return;
    }

    router.delete(route(routeName, {
        id: props.session.id,
        document_id: doc.id
    }), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
    });
};
</script>

<template>

    <Head title="Documentos" />

    <AuthenticatedLayout>
        <BackButtonRow :href="route('sessions.index')" />
        <div class="flex items-center justify-end mb-4">
            <PrimaryButton @click="resetOrder" :disabled="isReseting" :class="{ 'opacity-25': isReseting }">
                <span v-if="isReseting">Restaurando...</span>
                <span v-else>Restaurar Ordem</span>
            </PrimaryButton>

            <PrimaryButton @click="saveOrder" :disabled="isSaving" :class="{ 'opacity-25': isSaving, 'ml-1': true }">
                <span v-if="isSaving">Salvando...</span>
                <span v-else>Salvar Ordem</span>
            </PrimaryButton>
        </div>

        <div class="space-y-8">
            <DocumentList title="Expediente do Dia" description="Documentos que serão discutidos no início da sessão."
                :session="session" :documents="expedientList" @update:documents="expedientList = $event"
                @remove-document="doc => handleRemoveDocument(doc, 'expediente')" />

            <DocumentList title="Ordem do Dia"
                description="Documentos principais a serem votados ou discutidos em profundidade." :session="session"
                :documents="orderList" @update:documents="orderList = $event"
                @remove-document="doc => handleRemoveDocument(doc, 'ordemDoDia')" />
        </div>
    </AuthenticatedLayout>

    <ConfirmDeletionModal :show="confirmingDocRemoval" title="Remover Documento da Pauta"
        :message="`Tem certeza que deseja remover o documento '${docToRemove?.name}' da pauta de ${docToRemove?.listType === 'expediente' ? 'Expediente do Dia' : 'Ordem do Dia'} desta sessão?`"
        buttonText="Remover da Pauta" @close="closeModal" @confirm="deleteDocumentFromSession" />
</template>