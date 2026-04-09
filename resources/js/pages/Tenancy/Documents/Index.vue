<script setup lang="ts">
import { ref } from "vue";
import AuthenticatedLayout from "@/layouts/AuthenticatedLayout.vue";
import { Head, router } from "@inertiajs/vue3";
import IconButton from "@/components/Itens/IconButton.vue";
import LinkButton from "@/components/Common/LinkButton.vue";
import ConfirmDeletionModal from "@/components/Common/ConfirmDeletionModal.vue";
import TextInput from "@/components/Form/TextInput.vue";
import PrimaryButton from "@/components/Common/PrimaryButton.vue";
import SecondaryButton from "@/components/Common/SecondaryButton.vue";
import SelectInput from "@/components/Form/SelectInput.vue";
import Pagination from "@/components/Table/Pagination.vue";
import {
    EyeIcon,
    PencilSquareIcon,
    TrashIcon,
    ChevronUpIcon,
    ChevronDownIcon,
    MagnifyingGlassIcon,
    UserGroupIcon,
    XMarkIcon,
    CheckCircleIcon,
    ClockIcon,
} from "@heroicons/vue/24/outline";

interface Author {
    id: number;
    name: string;
    email: string | null;
    status: number;
}

interface Document {
    id: number;
    name: string;
    protocol_number: string | null;
    attachment_url: string | null;
    document_status_vote_id: number;
    document_status_movement_id: number;
    status_sign: number;
    authors: Author[];
}

interface PaginatedDocuments {
    data: Document[];
    links: { url: string | null; label: string; active: boolean }[];
}

interface Category {
    id: number;
    name: string;
}

interface StatusOption {
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
        category_id: number | null;
        vote_status_id: number | null;
        movement_status_id: number | null;
        status_sign: number | null;
    };
}>();

// ── Status maps ────────────────────────────────────────────────
const voteStatusOptions: StatusOption[] = [
    { id: 1, name: "Pendente" },
    { id: 2, name: "Aguardando" },
    { id: 3, name: "Em vista" },
    { id: 4, name: "Em votação" },
    { id: 5, name: "Concluído" },
    { id: 6, name: "Leitura" },
];

const movementStatusOptions: StatusOption[] = [
    { id: 1, name: "Secretario" },
    { id: 2, name: "Em sessão" },
    { id: 3, name: "Procurador" },
    { id: 4, name: "Comissão Justiça" },
    { id: 5, name: "Comissões" },
    { id: 6, name: "Prefeitura" },
    { id: 7, name: "Em analise" },
    { id: 8, name: "Reprovado" },
];

const signatureStatusOptions: StatusOption[] = [
    { id: 0, name: "Pendente" },
    { id: 1, name: "Assinado Digitalmente" },
    { id: 2, name: "Assinado" },
    { id: 3, name: "Expirado" },
];

const getSignatureStatusText = (status: number) =>
    ({ 0: "Pendente", 1: "Assinado Digitalmente", 2: "Assinado", 3: "Expirado" } as Record<number, string>)[status] ?? "Desconhecido";

const getSignatureStatusColor = (status: number) =>
    ({
        0: "bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200",
        1: "bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200",
        2: "bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200",
    } as Record<number, string>)[status] ?? "bg-gray-100 text-gray-800";

const getVoteStatusText = (id: number) =>
    ({ 1: "Pendente", 2: "Aguardando", 3: "Em vista", 4: "Em votação", 5: "Concluído", 6: "Leitura" } as Record<number, string>)[id] ?? "N/A";

const getMovementStatusText = (id: number) =>
    ({ 1: "Secretario", 2: "Em sessão", 3: "Procurador", 4: "Comissão Justiça", 5: "Comissões", 6: "Prefeitura", 7: "Em analise", 8: "Reprovado" } as Record<number, string>)[id] ?? "N/A";

const getVoteStatusColor = (id: number) =>
    ({
        1: "bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200",
        2: "bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200",
        3: "bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200",
        4: "bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200",
        5: "bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200",
        6: "bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200",
    } as Record<number, string>)[id] ?? "bg-gray-100 text-gray-800";

const getMovementStatusColor = (id: number) =>
    ({
        1: "bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200",
        2: "bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200",
        3: "bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200",
        4: "bg-pink-100 text-pink-800 dark:bg-pink-900 dark:text-pink-200",
        5: "bg-pink-100 text-pink-800 dark:bg-pink-900 dark:text-pink-200",
        6: "bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200",
        7: "bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200",
        8: "bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200",
    } as Record<number, string>)[id] ?? "bg-gray-100 text-gray-800";

// ── Exclusão ───────────────────────────────────────────────────
const confirmingDeletion = ref(false);
const itemToDelete = ref<Document | null>(null);

const openConfirmDeleteModal = (item: Document) => {
    itemToDelete.value = item;
    confirmingDeletion.value = true;
};

const closeDeleteModal = () => {
    confirmingDeletion.value = false;
    itemToDelete.value = null;
};

const deleteItem = () => {
    if (!itemToDelete.value) return;
    router.delete(route("documents.destroy", itemToDelete.value.id), {
        preserveScroll: true,
        onSuccess: () => closeDeleteModal(),
    });
};

// ── Modal de assinaturas ───────────────────────────────────────
const showAuthorsModal = ref(false);
const selectedDocumentAuthors = ref<{ name: string; authors: Author[] } | null>(null);

const openAuthorsModal = (doc: Document) => {
    selectedDocumentAuthors.value = { name: doc.name, authors: doc.authors };
    showAuthorsModal.value = true;
};

const closeAuthorsModal = () => {
    showAuthorsModal.value = false;
    selectedDocumentAuthors.value = null;
};

// ── Filtros ────────────────────────────────────────────────────
const search = ref(props.filters.search || "");
const selectedCategory = ref(props.filters.category_id ?? null);
const selectedVoteStatus = ref(props.filters.vote_status_id ?? null);
const selectedMovementStatus = ref(props.filters.movement_status_id ?? null);
const selectedSignatureStatus = ref(props.filters.status_sign ?? null);

const buildFilterParams = () => ({
    search: search.value,
    category_id: selectedCategory.value,
    vote_status_id: selectedVoteStatus.value,
    movement_status_id: selectedMovementStatus.value,
    status_sign: selectedSignatureStatus.value,
    sort: props.filters.sort,
    direction: props.filters.direction,
});

const submitFilters = () => {
    router.get(route("documents.index"), buildFilterParams(), { preserveState: true, replace: true });
};

const clearFilters = () => {
    search.value = "";
    selectedCategory.value = null;
    selectedVoteStatus.value = null;
    selectedMovementStatus.value = null;
    selectedSignatureStatus.value = null;
    router.get(route("documents.index"), { sort: props.filters.sort, direction: props.filters.direction }, { preserveState: true, replace: true });
};

const sortBy = (field: string) => {
    const direction = props.filters.sort === field && props.filters.direction === "asc" ? "desc" : "asc";
    router.get(route("documents.index"), { ...buildFilterParams(), sort: field, direction }, { preserveState: true, replace: true });
};
</script>

<template>

    <Head title="Documentos" />

    <AuthenticatedLayout>
        <!-- Filtros -->
        <form @submit.prevent="submitFilters">
            <div class="flex flex-col gap-4">
                <TextInput type="text" v-model="search" placeholder="Buscar por nome ou protocolo..." />
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
                    <SelectInput v-model="selectedCategory" :options="categories" value-key="id" label-key="name"
                        placeholder="Todas as categorias" />
                    <SelectInput v-model="selectedVoteStatus" :options="voteStatusOptions" value-key="id"
                        label-key="name" placeholder="Todos os status de votação" />
                    <SelectInput v-model="selectedMovementStatus" :options="movementStatusOptions" value-key="id"
                        label-key="name" placeholder="Todos os status de movimentação" />
                    <SelectInput v-model="selectedSignatureStatus" :options="signatureStatusOptions" value-key="id"
                        label-key="name" placeholder="Todos os status de assinatura" />
                </div>
            </div>
            <div class="flex justify-end items-center space-x-4 mt-4">
                <PrimaryButton type="submit" class="h-9 flex items-center">
                    <MagnifyingGlassIcon class="h-5 w-5 mr-2" />
                    Pesquisar
                </PrimaryButton>
                <SecondaryButton
                    v-if="search || selectedCategory !== null || selectedVoteStatus !== null || selectedMovementStatus !== null || selectedSignatureStatus !== null"
                    type="button" @click="clearFilters" class="h-9">
                    Limpar
                </SecondaryButton>
            </div>
        </form>

        <!-- Tabela -->
        <div class="overflow-x-auto mt-4">
            <table v-if="props.documents.data.length > 0"
                class="min-w-full divide-y divide-gray-200 dark:divide-gray-600 text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th
                            class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                            <button @click="sortBy('id')" class="flex items-center space-x-1">
                                <span>ID</span>
                                <ChevronUpIcon v-if="filters.sort === 'id' && filters.direction === 'asc'"
                                    class="h-4 w-4" />
                                <ChevronDownIcon v-if="filters.sort === 'id' && filters.direction === 'desc'"
                                    class="h-4 w-4" />
                            </button>
                        </th>
                        <th
                            class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                            <button @click="sortBy('name')" class="flex items-center space-x-1">
                                <span>TÍTULO</span>
                                <ChevronUpIcon v-if="filters.sort === 'name' && filters.direction === 'asc'"
                                    class="h-4 w-4" />
                                <ChevronDownIcon v-if="filters.sort === 'name' && filters.direction === 'desc'"
                                    class="h-4 w-4" />
                            </button>
                        </th>
                        <th
                            class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                            Votação</th>
                        <th
                            class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                            Movimentação</th>
                        <th
                            class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                            Assinatura</th>
                        <th class="relative px-6 py-2"><span class="sr-only">Ações</span></th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                    <tr v-for="doc in props.documents.data" :key="doc.id"
                        class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-6 py-2 whitespace-nowrap font-medium">{{ doc.id }}</td>
                        <td class="px-6 py-2 whitespace-normal font-medium">{{ doc.name }}</td>
                        <td class="px-6 py-2 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                :class="getVoteStatusColor(doc.document_status_vote_id)">
                                {{ getVoteStatusText(doc.document_status_vote_id) }}
                            </span>
                        </td>
                        <td class="px-6 py-2 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                :class="getMovementStatusColor(doc.document_status_movement_id)">
                                {{ getMovementStatusText(doc.document_status_movement_id) }}
                            </span>
                        </td>
                        <td class="px-6 py-2 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                :class="getSignatureStatusColor(doc.status_sign)">
                                {{ getSignatureStatusText(doc.status_sign) }}
                            </span>
                        </td>
                        <td class="px-6 py-2 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end space-x-1">
                                <!-- Botão autores/assinaturas -->
                                <button type="button" title="Ver autores e assinaturas" @click="openAuthorsModal(doc)"
                                    class="p-1.5 rounded bg-blue-500 hover:bg-blue-600 text-white">
                                    <UserGroupIcon class="h-5 w-5" />
                                </button>

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

        <Pagination :paginator="documents" />
    </AuthenticatedLayout>

    <!-- Modal de Assinaturas -->
    <Teleport to="body">
        <div v-if="showAuthorsModal" class="fixed inset-0 z-50 flex items-center justify-center p-4"
            @click.self="closeAuthorsModal">
            <!-- Overlay -->
            <div class="absolute inset-0 bg-black/50" />

            <!-- Painel -->
            <div class="relative z-10 w-full max-w-lg bg-white dark:bg-gray-800 rounded-xl shadow-xl">
                <!-- Header -->
                <div class="flex items-start justify-between p-5 border-b border-gray-200 dark:border-gray-700">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Autores e Assinaturas</h3>
                        <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400 line-clamp-2">
                            {{ selectedDocumentAuthors?.name }}
                        </p>
                    </div>
                    <button type="button" @click="closeAuthorsModal"
                        class="ml-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                        <XMarkIcon class="h-5 w-5" />
                    </button>
                </div>

                <!-- Body -->
                <div class="p-5">
                    <div v-if="selectedDocumentAuthors?.authors.length"
                        class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-700/50">
                                <tr>
                                    <th
                                        class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                        Autor</th>
                                    <th
                                        class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                        Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                                <tr v-for="author in selectedDocumentAuthors.authors" :key="author.id">
                                    <td class="px-4 py-3">
                                        <p class="font-medium text-gray-900 dark:text-gray-100">{{ author.name }}</p>
                                        <p v-if="author.email" class="text-xs text-gray-400">{{ author.email }}</p>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span
                                            class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-semibold rounded-full"
                                            :class="getSignatureStatusColor(author.status)">
                                            {{ getSignatureStatusText(author.status) }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div v-else class="text-center py-8 text-gray-400 dark:text-gray-500">
                        <UserGroupIcon class="h-10 w-10 mx-auto mb-2 opacity-40" />
                        <p class="text-sm">Nenhum autor cadastrado neste documento.</p>
                    </div>
                </div>

                <!-- Footer -->
                <div class="flex justify-end px-5 pb-5">
                    <SecondaryButton @click="closeAuthorsModal">Fechar</SecondaryButton>
                </div>
            </div>
        </div>
    </Teleport>

    <!-- Modal de exclusão -->
    <ConfirmDeletionModal :show="confirmingDeletion" title="Excluir Documento"
        :message="`Tem certeza que deseja mover o documento '${itemToDelete?.name}' para a lixeira?`"
        @close="closeDeleteModal" @confirm="deleteItem" />
</template>