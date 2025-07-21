<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import IconButton from '@/Components/Itens/IconButton.vue';
import TextButton from '@/Components/Itens/TextButton.vue';
import SessionStatusBadge from '@/Components/Session/SessionStatusBadge.vue'; 
import { PencilSquareIcon } from '@heroicons/vue/24/solid';
import { Head } from '@inertiajs/vue3';

interface Session {
    id: number;
    name: string;
    datetime_start: string; 
    session_status_id: number;
}

const props = defineProps<{
    sessions: Session[];
}>();

const formatDate = (datetime: string) => {
    if (!datetime) return 'Não definida';
    const date = new Date(datetime);
    return date.toLocaleString('pt-BR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

</script>

<template>

    <Head title="Sessões" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="flex justify-end mb-4">
                        </div>

                        <div v-if="props.sessions.length > 0" class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                            Nome da Sessão</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                            Data de Início</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                            Status</th>
                                        <th scope="col" class="relative px-6 py-3">
                                            <span class="sr-only">Ações</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                    <tr v-for="session in props.sessions" :key="session.id">
                                        <td class="px-6 py-4 whitespace-nowrap">{{ session.name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ formatDate(session.datetime_start) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <SessionStatusBadge :status="session.session_status_id" />
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <IconButton :href="route('sessions.edit', session.id)"
                                                color="yellow" title="Editar">
                                                <PencilSquareIcon class="h-5 w-5" />
                                            </IconButton>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div v-else>
                            <p>Nenhuma sessão encontrada.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>