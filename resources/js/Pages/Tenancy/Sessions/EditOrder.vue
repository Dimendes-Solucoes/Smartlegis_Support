<script setup lang="ts">
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DocumentList from './Document/DocumentList.vue';

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
</script>

<template>

    <Head title="Documentos" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center justify-end">
                        <PrimaryButton @click="saveOrder" :disabled="isSaving" :class="{ 'opacity-25': isSaving }">
                            <span v-if="isSaving">Salvando...</span>
                            <span v-else>Salvar Ordem</span>
                        </PrimaryButton>
                    </div>

                    <p class="mt-4 mb-4 text-gray-700 dark:text-gray-300">
                        Arraste e solte os documentos para reordená-los dentro de cada seção.
                    </p>

                    <div class="gap-8">
                        <DocumentList title="Expediente do Dia"
                            description="Documentos que serão discutidos no início da sessão." :session="session"
                            :documents="expedienteList" />

                        <DocumentList title="Ordem do Dia"
                            description="Documentos principais a serem votados ou discutidos em profundidade."
                            :session="session" :documents="ordemDoDiaList" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>