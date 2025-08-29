<script setup lang="ts">
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import IconButton from '@/Components/Itens/IconButton.vue';
import LinkButton from '@/Components/LinkButton.vue';
import ConfirmDeletionModal from '@/Components/ConfirmDeletionModal.vue';
import { EyeIcon, PencilSquareIcon, TrashIcon } from '@heroicons/vue/24/solid';

interface Document {
    id: number;
    name: string;
    attachment_url: string | null;
    document_status_vote_id: number;
    document_status_movement_id: number;
    status_sign: number;
}
interface PaginatedDocuments {
    data: Document[];
    links: { url: string | null; label: string; active: boolean; }[];
}

const props = defineProps<{
    documents: PaginatedDocuments;
}>();

const getSignatureStatusText = (status: number) => {
    const statuses: { [key: number]: string } = {
        0: 'Pendente',
        1: 'Assinado',
        2: 'Assinado',
    };
    return statuses[status] || 'Desconhecido';
};
const getSignatureStatusColor = (status: number) => {
    const colors: { [key: number]: string } = {
        0: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
        1: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        2: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
};

const getVoteStatusText = (statusId: number) => {
    const statuses: { [key: number]: string } = {
        1: 'Pendente',
        2: 'Aguardando',
        3: 'Em vista',
        4: 'Em votação',
        5: 'Concluído',
        6: 'Leitura',
    };
    return statuses[statusId] || 'N/A';
};

const getMovementStatusText = (statusId: number) => {
    const statuses: { [key: number]: string } = {
        1: 'Secretario',
        2: 'Em sessão',
        3: 'Procurador',
        4: 'Comissão Justiça',
        5: 'Comissões',
        6: 'Prefeitura',
        7: 'Em analise',
        8: 'Reprovado',
    };
    return statuses[statusId] || 'N/A';
};

const confirmingDeletion = ref(false);
const itemToDelete = ref<Document | null>(null);

const openConfirmDeleteModal = (item: Document) => {
    itemToDelete.value = item;
    confirmingDeletion.value = true;
};

const closeModal = () => {
    confirmingDeletion.value = false;
    itemToDelete.value = null;
};

const deleteItem = () => {
    if (!itemToDelete.value) return;
    router.delete(route('documents.destroy', itemToDelete.value.id), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
    });
};
</script>

<template>

    <Head title="Documentos" />

    <AuthenticatedLayout>
        <div v-if="props.documents.data.length > 0" class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                            Prot.</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                            Título</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                            Votação</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                            Movimentação</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                            Assinatura</th>
                        <th class="relative px-6 py-3"><span class="sr-only">Ações</span></th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                    <tr v-for="doc in props.documents.data" :key="doc.id">
                        <td class="px-6 py-4 whitespace-nowrap font-medium">{{ doc.id }}</td>
                        <td class="px-6 py-4 whitespace-normal font-medium">{{ doc.name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ getVoteStatusText(doc.document_status_vote_id) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">{{
                            getMovementStatusText(doc.document_status_movement_id) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                :class="getSignatureStatusColor(doc.status_sign)">
                                {{ getSignatureStatusText(doc.status_sign) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end space-x-1">
                                <LinkButton v-if="doc.attachment_url" :link="doc.attachment_url"
                                    title="Visualizar documento">
                                    <EyeIcon class="h-5 w-5 text-white" />
                                </LinkButton>
                                <IconButton :href="route('documents.edit', doc.id)" color="yellow" title="Editar">
                                    <PencilSquareIcon class="h-5 w-5" />
                                </IconButton>
                                <IconButton as="button" color="red" title="Excluir"
                                    @click.stop="openConfirmDeleteModal(doc)">
                                    <TrashIcon class="h-5 w-5" />
                                </IconButton>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div v-else>
            <p>Nenhum documento encontrado.</p>
        </div>

        <div v-if="props.documents.data.length > 0 && props.documents.links.length > 3"
            class="mt-6 flex justify-center">
            <Link v-for="(link, index) in props.documents.links" :key="index" :href="link.url || ''"
                class="px-4 py-2 text-sm"
                :class="{ 'bg-indigo-500 text-white rounded-md': link.active, 'text-gray-500 hover:text-gray-800': !link.active, 'cursor-not-allowed text-gray-400': !link.url }"
                v-html="link.label" />
        </div>
    </AuthenticatedLayout>

    <ConfirmDeletionModal :show="confirmingDeletion" title="Excluir Documento"
        :message="`Tem certeza que deseja mover o documento '${itemToDelete?.name}' para a lixeira?`"
        @close="closeModal" @confirm="deleteItem" />
</template>