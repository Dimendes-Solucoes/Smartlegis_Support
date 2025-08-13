<script setup lang="ts">
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import draggable from 'vuedraggable';
import { EyeIcon, Bars2Icon, ExclamationCircleIcon } from '@heroicons/vue/24/outline'; // Adicionado ExclamationCircleIcon
import PrimaryButton from '@/Components/PrimaryButton.vue';
import LinkButton from '@/Components/LinkButton.vue';

interface Session {
    id: number;
    name: string;
}

interface Document {
    id: number;
    name: string;
    attachment: string;
    // Se o backend estiver enviando `ordem_do_dia` aqui, você pode adicioná-lo
    // ordem_do_dia?: number;
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

    router.put(route('sessions.update_order', props.session.id), payload, {
        preserveScroll: true,
        onSuccess: () => {
            alert('Ordem salva com sucesso!');
        },
        onError: (errors) => {
            console.error('Erro ao salvar ordem:', errors);
            alert('Ocorreu um erro ao salvar a ordem. Verifique o console para mais detalhes.');
        },
        onFinish: () => isSaving.value = false,
    });
};
</script>

<template>

    <Head title="Reordenar Documentos" />

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

                    <p class="mb-4 text-gray-700 dark:text-gray-300">
                        Arraste e solte os documentos para reordená-los dentro de cada seção.
                    </p>

                    <div class="gap-8">
                        <div>
                            <div class="p-5 rounded-lg border dark:border-gray-600 mb-6">
                                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 flex items-center">
                                    Expediente do Dia
                                </h2>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Documentos que serão discutidos
                                    no início da sessão.</p>

                                <div v-if="expedienteList.length === 0"
                                    class="p-6 text-center text-gray-500 dark:text-gray-400 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg">
                                    <ExclamationCircleIcon class="h-8 w-8 mx-auto mb-2 text-gray-400" />
                                    <p>Nenhum documento no Expediente do Dia.</p>
                                </div>

                                <draggable v-model="expedienteList" item-key="id" tag="ul" handle=".drag-handle"
                                    ghost-class="ghost-item" class="space-y-3">
                                    <template #item="{ element }">
                                        <li
                                            class="flex items-center p-3 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 transition-all duration-200 ease-in-out">
                                            <button
                                                class="drag-handle p-2 text-gray-500 dark:text-gray-400 hover:text-indigo-600 rounded-full cursor-grab flex-shrink-0 transition-colors duration-200"
                                                title="Mover">
                                                <Bars2Icon class="h-5 w-5" />
                                            </button>

                                            <span class="flex-1 mx-4 font-medium text-gray-800 dark:text-gray-200">{{
                                                element.name }}</span>

                                            <LinkButton :link="element.attachment" title="Visualizar documento">
                                                <EyeIcon class="h-5 w-5 text-white" />
                                            </LinkButton>
                                        </li>
                                    </template>
                                </draggable>
                            </div>
                        </div>

                        <div>
                            <div class="p-5 rounded-lg border dark:border-gray-600 mb-6">
                                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 flex items-center">
                                    Ordem do Dia
                                </h2>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Documentos principais a serem
                                    votados ou discutidos em
                                    profundidade.</p>

                                <div v-if="ordemDoDiaList.length === 0"
                                    class="p-6 text-center text-gray-500 dark:text-gray-400 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg">
                                    <ExclamationCircleIcon class="h-8 w-8 mx-auto mb-2 text-gray-400" />
                                    <p>Nenhum documento na Ordem do Dia.</p>
                                </div>

                                <draggable v-model="ordemDoDiaList" item-key="id" tag="ul" handle=".drag-handle"
                                    ghost-class="ghost-item" class="space-y-3">
                                    <template #item="{ element }">
                                        <li
                                            class="flex items-center p-3 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 transition-all duration-200 ease-in-out">
                                            <button
                                                class="drag-handle p-2 text-gray-500 dark:text-gray-400 hover:text-indigo-600 rounded-full cursor-grab flex-shrink-0 transition-colors duration-200"
                                                title="Mover">
                                                <Bars2Icon class="h-5 w-5" />
                                            </button>

                                            <span class="flex-1 mx-4 font-medium text-gray-800 dark:text-gray-200">{{
                                                element.name }}</span>

                                            <LinkButton :link="element.attachment" title="Visualizar documento">
                                                <EyeIcon class="h-5 w-5 text-white" />
                                            </LinkButton>
                                        </li>
                                    </template>
                                </draggable>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style lang="postcss">
.ghost-item {
    @apply opacity-50 bg-blue-100 dark:bg-blue-900/50 rounded-lg;
    border: 2px dashed theme('colors.blue.400');
}

.sortable-chosen {
    @apply shadow-lg transform scale-100;
}

.sortable-drag {
    @apply opacity-75;
}
</style>