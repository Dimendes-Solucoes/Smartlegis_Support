<script setup lang="ts">
import { computed, ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SessionStatusBadge from '@/Components/Session/SessionStatusBadge.vue'; 
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

const sortKey = ref<keyof Session>('datetime_start');
const sortOrder = ref<'asc' | 'desc'>('desc');

const sortBy = (key: keyof Session) => {
    if (sortKey.value === key) {
        sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortKey.value = key;
        sortOrder.value = 'asc';
    }
};

const sortedSessions = computed(() => {
    return [...props.sessions].sort((a, b) => {
        const key = sortKey.value;
        const order = sortOrder.value === 'asc' ? 1 : -1;

        if (a[key] < b[key]) return -1 * order;
        if (a[key] > b[key]) return 1 * order;
        return 0;
    });
});

const formatDate = (datetime: string) => {
    if (!datetime) return 'Não definida';
    const date = new Date(datetime);
    return date.toLocaleString('pt-BR', {
        day: '2-digit', month: '2-digit', year: 'numeric',
        hour: '2-digit', minute: '2-digit'
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
                        <div v-if="sortedSessions.length > 0" class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                            <button @click="sortBy('name')" class="flex items-center space-x-1">
                                                <span>NOME DA SESSÃO</span>
                                            </button>
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                            <button @click="sortBy('datetime_start')" class="flex items-center space-x-1">
                                                <span>DATA DE INÍCIO</span>
                                            </button>
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                            Status
                                        </th>
                                        </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                    <tr v-for="session in sortedSessions" :key="session.id">
                                        <td class="px-6 py-4 whitespace-nowrap">{{ session.name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ formatDate(session.datetime_start) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <SessionStatusBadge :status="session.session_status_id" />
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