<script setup lang="ts">
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import draggable from 'vuedraggable';
import { EyeIcon, Bars3Icon } from '@heroicons/vue/24/solid';
import PrimaryButton from '@/Components/PrimaryButton.vue';

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

    router.put(route('sessions.update', props.session.id), payload, {
        preserveScroll: true,
        onSuccess: () => alert('Ordem salva com sucesso!'),
        onError: () => alert('Ocorreu um erro ao salvar a ordem.'),
        onFinish: () => isSaving.value = false,
    });
};
</script>

<template>
    <Head :title="`${session.name}`" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-200">
                        Pauta da Sessão: {{ session.name }}
                    </h1>
                    <PrimaryButton @click="saveOrder" :disabled="isSaving" :class="{ 'opacity-25': isSaving }">
                        Salvar Ordem
                    </PrimaryButton>
                </div>

                <div class="space-y-8">
                    
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900 dark:text-gray-100">
                            <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-gray-200">EXPEDIENTE DO DIA</h2>
                            <table class="min-w-full">
                                <thead class="border-b border-gray-200 dark:border-gray-700">
                                    <tr>
                                        <th class="px-1 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">DOCUMENTOS</th>
                                    </tr>
                                </thead>
                                <draggable 
                                    v-model="expedienteList" 
                                    item-key="id"
                                    tag="tbody"
                                    handle=".drag-handle"
                                    ghost-class="ghost-item"
                                >
                                    <template #item="{ element }">
                                        <tr>
                                            <td class="pt-2" colspan="100%">
                                                <div class="flex items-center p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg text-gray-800 dark:text-gray-200">
                                                    <button class="drag-handle p-2 text-gray-500 hover:text-gray-400 rounded-full cursor-grab flex-shrink-0" title="Mover">
                                                        <Bars3Icon class="h-5 w-5" />
                                                    </button>
                                                    
                                                    <span class="flex-1 mx-4 font-medium">{{ element.name }}</span>
                                                    
                                                    <a :href="element.attachment" target="_blank" rel="noopener noreferrer" 
                                                       class="flex items-center justify-center p-1 bg-indigo-600 hover:bg-indigo-700 rounded-md border-2 border-indigo-600 flex-shrink-0" 
                                                       title="Visualizar">
                                                        <EyeIcon class="h-5 w-5 text-white" />
                                                    </a>
                                                </div>
                                                </td>
                                        </tr>
                                    </template>
                                </draggable>
                            </table>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900 dark:text-gray-100">
                            <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-gray-200">ORDEM DO DIA</h2>
                             <table class="min-w-full">
                                <thead class="border-b border-gray-200 dark:border-gray-700">
                                    <tr>
                                        <th class="px-1 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">DOCUMENTOS</th>
                                    </tr>
                                </thead>
                                <draggable 
                                    v-model="ordemDoDiaList" 
                                    item-key="id"
                                    tag="tbody"
                                    handle=".drag-handle"
                                    ghost-class="ghost-item"
                                >
                                    <template #item="{ element }">
                                        <tr>
                                            <td class="pt-2" colspan="100%">
                                                 <div class="flex items-center p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg text-gray-800 dark:text-gray-200">
                                                    <button class="drag-handle p-2 text-gray-500 hover:text-gray-400 rounded-full cursor-grab flex-shrink-0" title="Mover">
                                                        <Bars3Icon class="h-5 w-5" />
                                                    </button>
                                                    
                                                    <span class="flex-1 mx-4 font-medium">{{ element.name }}</span>
                                                    
                                                    <a :href="element.attachment" target="_blank" rel="noopener noreferrer" 
                                                       class="flex items-center justify-center p-1 bg-indigo-600 hover:bg-indigo-700 rounded-md border-2 border-indigo-600 flex-shrink-0" 
                                                       title="Visualizar">
                                                        <EyeIcon class="h-5 w-5 text-white" />
                                                    </a>
                                                </div>
                                                </td>
                                        </tr>
                                    </template>
                                </draggable>
                            </table>
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
}
.ghost-item td {
    @apply border-2 border-dashed border-blue-400 rounded-lg;
}
</style>