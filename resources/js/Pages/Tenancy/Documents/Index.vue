<script setup lang="ts">
import { ref, watch } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import IconButton from '@/Components/Itens/IconButton.vue';
import LinkButton from '@/Components/LinkButton.vue';
import ConfirmDeletionModal from '@/Components/ConfirmDeletionModal.vue';
import TextInput from '@/Components/TextInput.vue';
import { EyeIcon, PencilSquareIcon, TrashIcon, ChevronUpIcon, ChevronDownIcon, ArrowPathIcon } from '@heroicons/vue/24/outline';
import { debounce } from 'lodash';
import TagMultiselectFilter from '@/Components/MultiselectFilter/TagMultiselectFilter.vue';

interface Document {
    id: number;
    name: string;
    protocol_number: string | null;
    attachment_url: string | null;
    document_status_vote_id: number;
    document_status_movement_id: number;
    status_sign: number;
}
interface PaginatedDocuments {
    data: Document[];
    links: { url: string | null; label: string; active: boolean; }[];
}
interface Category {
    id: number;
    name: string;
}

const props = defineProps<{
    documents: PaginatedDocuments;
    categories: Category[];
    filters: {
        search: string;
        sort: string;
        direction: string;
        categories?: number[];
    }
}>();

const getSignatureStatusText = (status: number) => {
    const statuses: { [key: number]: string } = { 0: 'Pendente', 1: 'Assinado', 2: 'Assinado', 3: 'Expirado' };
    return statuses[status] || 'Desconhecido';
};

const getSignatureStatusColor = (status: number) => {
    const colors: { [key: number]: string } = { 0: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200', 1: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200', 2: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200', 3: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' };
    return colors[status] || 'bg-gray-100 text-gray-800';
};

const getVoteStatusText = (statusId: number) => {
    const statuses: { [key: number]: string } = { 1: 'Pendente', 2: 'Aguardando', 3: 'Em vista', 4: 'Em votação', 5: 'Concluído', 6: 'Leitura' };
    return statuses[statusId] || 'N/A';
};

const getMovementStatusText = (statusId: number) => {
    const statuses: { [key: number]: string } = { 1: 'Secretario', 2: 'Em sessão', 3: 'Procurador', 4: 'Comissão Justiça', 5: 'Comissões', 6: 'Prefeitura', 7: 'Em analise', 8: 'Reprovado' };
    return statuses[statusId] || 'N/A';
};

const getVoteStatusColor = (statusId: number) => {
    const colors: { [key: number]: string } = { 1: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200', 2: 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200', 3: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200', 4: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200', 5: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200', 6: 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200' };
    return colors[statusId] || 'bg-gray-100 text-gray-800';
};

const getMovementStatusColor = (statusId: number) => {
    const colors: { [key: number]: string } = { 1: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200', 2: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200', 3: 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200', 4: 'bg-pink-100 text-pink-800 dark:bg-pink-900 dark:text-pink-200', 5: 'bg-pink-100 text-pink-800 dark:bg-pink-900 dark:text-pink-200', 6: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200', 7: 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200', 8: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' };
    return colors[statusId] || 'bg-gray-100 text-gray-800';
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

const search = ref(props.filters.search || '');
const selectedCategories = ref(props.filters.categories || []);

watch([search, selectedCategories], debounce(() => {
    router.get(route('documents.index'), {
        search: search.value,
        categories: selectedCategories.value,
        sort: props.filters.sort,
        direction: props.filters.direction,
    }, {
        preserveState: true,
        replace: true,
    });
}, 300));

const sortBy = (field: string) => {
    let direction = 'asc';
    if (props.filters.sort === field && props.filters.direction === 'asc') {
        direction = 'desc';
    }
    router.get(route('documents.index'), {
        sort: field,
        direction: direction,
        search: search.value,
        categories: selectedCategories.value,
    }, {
        preserveState: true,
        replace: true,
    });
};

const clearFilters = () => {
    search.value = '';
    selectedCategories.value = [];
};
</script>

<template>

    <Head title="Documentos" />

    <AuthenticatedLayout>
        <div class="flex flex-wrap items-end gap-4 mb-6">
            <TagMultiselectFilter :options="categories" v-model="selectedCategories" placeholder="Filtrar por Categoria"
                class="flex-1 min-w-[200px]" />
            <TextInput type="text" v-model="search" placeholder="Buscar por nome..."
                class="flex-1 h-10 min-w-[200px]" />

            <IconButton as="button" @click="clearFilters" color="indigo" title="Limpar Filtros" class="h-9 w-9 my-auto">
                <ArrowPathIcon class="h-6 w-6" />
            </IconButton>
        </div>

        <div class="overflow-x-auto">
            <table v-if="props.documents.data.length > 0"
                class="min-w-full divide-y divide-gray-200 dark:divide-gray-600 text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                            <button @click="sortBy('id')" class="flex items-center space-x-1">
                                <span>ID</span>
                                <ChevronUpIcon v-if="filters.sort === 'id' && filters.direction === 'asc'"
                                    class="h-4 w-4" />
                                <ChevronDownIcon v-if="filters.sort === 'id' && filters.direction === 'desc'"
                                    class="h-4 w-4" />
                            </button>
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                            <button @click="sortBy('name')" class="flex items-center space-x-1">
                                <span>TÍTULO</span>
                                <ChevronUpIcon v-if="filters.sort === 'name' && filters.direction === 'asc'"
                                    class="h-4 w-4" />
                                <ChevronDownIcon v-if="filters.sort === 'name' && filters.direction === 'desc'"
                                    class="h-4 w-4" />
                            </button>
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                            Votação</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                            Movimentação</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                            Assinatura</th>
                        <th class="relative px-6 py-3"><span class="sr-only">Ações</span></th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                    <tr v-for="doc in props.documents.data" :key="doc.id"
                        class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-6 py-4 whitespace-nowrap font-medium">{{ doc.id || 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-normal font-medium">{{ doc.name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                :class="getVoteStatusColor(doc.document_status_vote_id)">
                                {{ getVoteStatusText(doc.document_status_vote_id) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                :class="getMovementStatusColor(doc.document_status_movement_id)">
                                {{ getMovementStatusText(doc.document_status_movement_id) }}
                            </span>
                        </td>
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

            <div v-else class="text-center py-10 text-gray-500">
                <p>Nenhum documento encontrado com os filtros aplicados.</p>
            </div>
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