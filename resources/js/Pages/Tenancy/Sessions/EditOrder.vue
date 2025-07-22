<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { EyeIcon } from '@heroicons/vue/24/solid';

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

</script>

<template>
    <Head :title="`Pauta - ${session.name}`" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="mb-6">
                    <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-200">
                        Pauta da Sessão: {{ session.name }}
                    </h1>
                </div>

                <div class="space-y-8">
                    
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900 dark:text-gray-100">
                            <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-gray-200">EXPEDIENTE DO DIA</h2>
                            <div class="space-y-3">
                                <div v-for="document in props.extraDocuments" :key="document.id"
                                     class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg text-gray-800 dark:text-gray-200">
                                    
                                    <span class="flex-1 mr-4">{{ document.name }}</span>
                                    
                                    <a :href="document.attachment" target="_blank" rel="noopener noreferrer"
                                       class="p-2 text-blue-500 hover:bg-blue-100 dark:hover:bg-blue-900/50 rounded-full"
                                       title="Visualizar Documento">
                                        <EyeIcon class="h-5 w-5" />
                                    </a>
                                </div>
                                <div v-if="props.extraDocuments.length === 0" class="text-center text-gray-500 py-4">
                                    Nenhum documento no expediente.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900 dark:text-gray-100">
                             <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-gray-200">ORDEM DO DIA</h2>
                            <div class="space-y-3">
                                <div v-for="document in props.agendaDocuments" :key="document.id" 
                                     class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg text-gray-800 dark:text-gray-200">
                                    
                                    <span class="flex-1 mr-4">{{ document.name }}</span>
                                    
                                    <a :href="document.attachment" target="_blank" rel="noopener noreferrer"
                                       class="p-2 text-blue-500 hover:bg-blue-100 dark:hover:bg-blue-900/50 rounded-full"
                                       title="Visualizar Documento">
                                        <EyeIcon class="h-5 w-5" />
                                    </a>
                                </div>
                                <div v-if="props.agendaDocuments.length === 0" class="text-center text-gray-500 py-4">
                                    Nenhum documento na ordem do dia.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>