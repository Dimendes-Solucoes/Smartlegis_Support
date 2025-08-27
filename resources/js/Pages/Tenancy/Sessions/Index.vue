<script setup lang="ts">
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SessionStatusBadge from '@/Components/Session/SessionStatusBadge.vue';
import { Head, router, Link } from '@inertiajs/vue3';
import { TrashIcon, ChevronUpIcon, ChevronDownIcon, PencilSquareIcon, ArrowsUpDownIcon, ChatBubbleLeftRightIcon, DocumentArrowDownIcon, DocumentDuplicateIcon } from '@heroicons/vue/24/solid';
import IconButton from '@/Components/Itens/IconButton.vue';
import ConfirmDeletionModal from '@/Components/ConfirmDeletionModal.vue';
import TextButton from '@/Components/Itens/TextButton.vue';

interface Session {
    id: number;
    name: string;
    datetime_start: string;
    session_status_id: number;
}

interface PaginatedSessions {
    data: Session[];
    links: {
        url: string | null;
        label: string;
        active: boolean;
    }[];
}

const props = defineProps<{
    sessions: PaginatedSessions;
    filters: {
        sort: string;
        direction: string;
    }
}>();

const sortBy = (field: string) => {
    let direction = 'asc';
    if (props.filters.sort === field && props.filters.direction === 'asc') {
        direction = 'desc';
    }

    router.get(route('sessions.index'), {
        sort: field,
        direction: direction,
    }, {
        preserveState: true,
        replace: true,
    });
};

const formatDate = (datetime: string) => {
    if (!datetime) return 'Não definida';
    const date = new Date(datetime);
    return date.toLocaleString('pt-BR', {
        day: '2-digit', month: '2-digit', year: 'numeric',
    });
};

const confirmingSessionDeletion = ref(false);
const sessionToDelete = ref<Session | null>(null);

const openConfirmDeleteModal = (session: Session) => {
    sessionToDelete.value = session;
    confirmingSessionDeletion.value = true;
};

const closeModal = () => {
    confirmingSessionDeletion.value = false;
    sessionToDelete.value = null;
};

const deleteSession = () => {
    if (!sessionToDelete.value) return;

    router.delete(route('sessions.destroy', sessionToDelete.value.id), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
    });
};
</script>

<template>

    <Head title="Sessões" />

    <AuthenticatedLayout>
        <div class="flex justify-end items-center mb-4">
            <TextButton :href="route('sessions.create')" class="p-4">
                Nova Sessão
            </TextButton>
        </div>

        <div v-if="props.sessions.data.length > 0" class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                            <button @click="sortBy('name')" class="flex items-center space-x-1 uppercase">
                                <span>Nome da Sessão</span>
                                <ChevronUpIcon v-if="filters.sort === 'name' && filters.direction === 'asc'"
                                    class="h-4 w-4" />
                                <ChevronDownIcon v-if="filters.sort === 'name' && filters.direction === 'desc'"
                                    class="h-4 w-4" />
                            </button>
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                            <button @click="sortBy('datetime_start')" class="flex items-center space-x-1 uppercase">
                                <span>Data de Início</span>
                                <ChevronUpIcon v-if="filters.sort === 'datetime_start' && filters.direction === 'asc'"
                                    class="h-4 w-4" />
                                <ChevronDownIcon
                                    v-if="filters.sort === 'datetime_start' && filters.direction === 'desc'"
                                    class="h-4 w-4" />
                            </button>
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                            Status
                        </th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                    <tr v-for="session in props.sessions.data" :key="session.id">
                        <td class="px-6 py-4 whitespace-normal">{{ session.name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ formatDate(session.datetime_start) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <SessionStatusBadge :status="session.session_status_id" />
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end space-x-1">
                                <IconButton :href="route('sessions.documents', session.id)" color="green"
                                    title="Documentos">
                                    <DocumentDuplicateIcon class="h-5 w-5" />
                                </IconButton>

                                <IconButton :href="route('sessions.talks', session.id)" color="blue" title="Falas">
                                    <ChatBubbleLeftRightIcon class="h-5 w-5" />
                                </IconButton>

                                <IconButton :href="route('sessions.edit', session.id)" color="yellow" title="Editar">
                                    <PencilSquareIcon class="h-5 w-5" />
                                </IconButton>

                                <IconButton as="button" color="red" title="Excluir Sessão"
                                    @click.stop="openConfirmDeleteModal(session)">
                                    <TrashIcon class="h-5 w-5" />
                                </IconButton>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div v-else>
            <p>Nenhuma sessão encontrada.</p>
        </div>

        <div v-if="props.sessions.data.length > 0 && props.sessions.links.length > 3" class="mt-6 flex justify-center">
            <Link v-for="(link, index) in props.sessions.links" :key="index" :href="link.url || ''"
                class="px-4 py-2 text-sm" :class="{
                    'bg-indigo-500 text-white rounded-md': link.active,
                    'text-gray-500 hover:text-gray-800': !link.active,
                    'cursor-not-allowed text-gray-400': !link.url
                }" v-html="link.label" />
        </div>
    </AuthenticatedLayout>

    <ConfirmDeletionModal :show="confirmingSessionDeletion" title="Excluir Sessão"
        :message="`Tem certeza que deseja mover a sessão '${sessionToDelete?.name}' para a lixeira?`"
        @close="closeModal" @confirm="deleteSession" />
</template>