<script setup lang="ts">
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DocumentList from './Document/DocumentList.vue';
import BackButtonRow from '@/Components/BackButtonRow.vue';

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
    isSaving.value = true;

    router.put(route('sessions.reset_documents', props.session.id), {}, {
        preserveScroll: true,
        onFinish: () => isSaving.value = false,
    });
}
</script>

<template>

    <Head title="Documentos" />

    <AuthenticatedLayout>
        <BackButtonRow :href="route('sessions.index')" />

        <div class="flex items-center justify-end mb-4">
            <PrimaryButton @click="resetOrder" :disabled="isSaving" :class="{ 'opacity-25': isSaving }">
                <span v-if="isSaving">Salvando...</span>
                <span v-else>Resetar</span>
            </PrimaryButton>

            <PrimaryButton @click="saveOrder" :disabled="isSaving" :class="{ 'opacity-25': isSaving, 'ml-1': true }">
                <span v-if="isSaving">Salvando...</span>
                <span v-else>Salvar Ordem</span>
            </PrimaryButton>
        </div>

        <div class="gap-8">
            <DocumentList title="Expediente do Dia" description="Documentos que serão discutidos no início da sessão."
                :session="session" :documents="expedienteList" @update:documents="expedienteList = $event" />

            <DocumentList title="Ordem do Dia"
                description="Documentos principais a serem votados ou discutidos em profundidade." :session="session"
                :documents="ordemDoDiaList" @update:documents="ordemDoDiaList = $event" />
        </div>
    </AuthenticatedLayout>
</template>