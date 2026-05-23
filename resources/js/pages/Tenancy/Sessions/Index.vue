<script setup lang="ts">
import { ref } from "vue";
import { Head, router } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/layouts/AuthenticatedLayout.vue";
import SessionStatusBadge from "@/components/Session/SessionStatusBadge.vue";
import IconButton from "@/components/Itens/IconButton.vue";
import ConfirmDeletionModal from "@/components/Common/ConfirmDeletionModal.vue";
import TextButton from "@/components/Itens/TextButton.vue";
import RegularColumn from "@/components/Table/RegularColumn.vue";
import Pagination from "@/components/Table/Pagination.vue";
import TextInput from "@/components/Form/TextInput.vue";
import SelectInput from "@/components/Form/SelectInput.vue";
import PrimaryButton from "@/components/Common/PrimaryButton.vue";
import SecondaryButton from "@/components/Common/SecondaryButton.vue";
import {
    TrashIcon,
    ChevronUpIcon,
    ChevronDownIcon,
    PencilSquareIcon,
    DocumentDuplicateIcon,
    ArrowPathIcon,
    ListBulletIcon,
    MagnifyingGlassIcon,
} from "@heroicons/vue/24/outline";

interface Session {
    id: number;
    name: string;
    datetime_start: string;
    session_status_id: number;
    has_ata: boolean;
}

interface PaginatedSessions {
    data: Session[];
    links: {
        url: string | null;
        label: string;
        active: boolean;
    }[];
}

interface StatusOption {
    id: string;
    name: string;
}

const props = defineProps<{
    sessions: PaginatedSessions;
    filters: {
        sort: string;
        direction: string;
        search?: string;
        year?: string;
        status_id?: string;
        has_ata?: string;
    };
}>();

const sessionStatusOptions: StatusOption[] = [
    { id: "1", name: "Aguardando Votação" },
    { id: "2", name: "Em Votação" },
    { id: "3", name: "Concluída" },
];

const ataOptions: StatusOption[] = [
    { id: "1", name: "Com ata" },
    { id: "0", name: "Sem ata" },
];

const search = ref(props.filters.search ?? "");
const year = ref(props.filters.year ?? "");
const selectedStatus = ref(props.filters.status_id ?? null);
const selectedAta = ref(props.filters.has_ata ?? null);

const buildFilterParams = () => ({
    search: search.value,
    year: year.value,
    status_id: selectedStatus.value,
    has_ata: selectedAta.value,
    sort: props.filters.sort,
    direction: props.filters.direction,
});

const submitFilters = () => {
    router.get(route("sessions.index"), buildFilterParams(), { preserveState: true, replace: true });
};

const clearFilters = () => {
    search.value = "";
    year.value = "";
    selectedStatus.value = null;
    selectedAta.value = null;
    router.get(route("sessions.index"), { sort: props.filters.sort, direction: props.filters.direction }, { preserveState: true, replace: true });
};

const sortBy = (field: string) => {
    const direction = props.filters.sort === field && props.filters.direction === "asc" ? "desc" : "asc";
    router.get(route("sessions.index"), { ...buildFilterParams(), sort: field, direction }, { preserveState: true, replace: true });
};

const formatDate = (datetime: string) => {
    if (!datetime) return "Não definida";
    const date = new Date(datetime);
    return date.toLocaleString("pt-BR", {
        day: "2-digit",
        month: "2-digit",
        year: "numeric",
    });
};

const confirmingSessionDeletion = ref(false);
const sessionToDelete = ref<Session | null>(null);

const openConfirmDeleteModal = (session: Session) => {
    sessionToDelete.value = session;
    confirmingSessionDeletion.value = true;
};

const deleteSession = () => {
    if (!sessionToDelete.value) return;

    router.delete(route("sessions.destroy", sessionToDelete.value.id), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
    });
};

const confirmingSessionReset = ref(false);
const sessionToReset = ref<Session | null>(null);

const openConfirmResetModal = (session: Session) => {
    sessionToReset.value = session;
    confirmingSessionReset.value = true;
};

const resetSession = () => {
    if (!sessionToReset.value) return;
    router.post(
        route("sessions.reset", sessionToReset.value.id),
        {},
        {
            preserveScroll: true,
            onSuccess: () => closeModal(),
        }
    );
};

const confirmingSessionDuplication = ref(false);
const sessionToDuplicate = ref<Session | null>(null);

const openConfirmDuplicateModal = (session: Session) => {
    sessionToDuplicate.value = session;
    confirmingSessionDuplication.value = true;
};

const duplicateSession = () => {
    if (!sessionToDuplicate.value) return;
    router.post(
        route("sessions.duplicate", sessionToDuplicate.value.id),
        {},
        {
            preserveScroll: true,
            onSuccess: () => closeModal(),
        }
    );
};

const closeModal = () => {
    confirmingSessionDeletion.value = false;
    sessionToDelete.value = null;
    confirmingSessionReset.value = false;
    sessionToReset.value = null;
    confirmingSessionDuplication.value = false;
    sessionToDuplicate.value = null;
};
</script>

<template>
    <Head title="Sessões" />

    <AuthenticatedLayout>
        <!-- Filtros -->
        <form @submit.prevent="submitFilters" class="mb-4">
            <div class="flex flex-col gap-4">
                <TextInput type="text" v-model="search" placeholder="Buscar por nome da sessão..." />
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <TextInput type="number" v-model="year" placeholder="Ano (ex: 2024)" />
                    <SelectInput v-model="selectedStatus" :options="sessionStatusOptions" value-key="id"
                        label-key="name" placeholder="Todos os status" />
                    <SelectInput v-model="selectedAta" :options="ataOptions" value-key="id"
                        label-key="name" placeholder="Com ou sem ata" />
                </div>
            </div>
            <div class="flex justify-end items-center space-x-4 mt-4">
                <PrimaryButton type="submit" class="h-9 flex items-center">
                    <MagnifyingGlassIcon class="h-5 w-5 mr-2" />
                    Pesquisar
                </PrimaryButton>
                <SecondaryButton
                    v-if="search || year || selectedStatus !== null || selectedAta !== null"
                    type="button" @click="clearFilters" class="h-9">
                    Limpar
                </SecondaryButton>
                <TextButton :href="route('sessions.create')">Nova Sessão</TextButton>
            </div>
        </form>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="overflow-x-auto">
                <table
                    v-if="props.sessions.data.length > 0"
                    class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm"
                >
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <RegularColumn>
                                <button
                                    @click="sortBy('name')"
                                    class="flex items-center space-x-1 uppercase"
                                >
                                    <span>Nome da Sessão</span>
                                    <ChevronUpIcon
                                        v-if="
                                            filters.sort === 'name' &&
                                            filters.direction === 'asc'
                                        "
                                        class="h-4 w-4"
                                    />
                                    <ChevronDownIcon
                                        v-if="
                                            filters.sort === 'name' &&
                                            filters.direction === 'desc'
                                        "
                                        class="h-4 w-4"
                                    />
                                </button>
                            </RegularColumn>

                            <RegularColumn>
                                <button
                                    @click="sortBy('datetime_start')"
                                    class="flex items-center space-x-1 uppercase"
                                >
                                    <span>Data de Início</span>
                                    <ChevronUpIcon
                                        v-if="
                                            filters.sort === 'datetime_start' &&
                                            filters.direction === 'asc'
                                        "
                                        class="h-4 w-4"
                                    />
                                    <ChevronDownIcon
                                        v-if="
                                            filters.sort === 'datetime_start' &&
                                            filters.direction === 'desc'
                                        "
                                        class="h-4 w-4"
                                    />
                                </button>
                            </RegularColumn>
                            <RegularColumn>Status</RegularColumn>
                            <RegularColumn>Ata</RegularColumn>
                            <th
                                scope="col"
                                class="relative px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400"
                            >
                                Ações
                            </th>
                        </tr>
                    </thead>
                    <tbody
                        class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700"
                    >
                        <tr
                            v-for="session in props.sessions.data"
                            :key="session.id"
                            class="hover:bg-gray-50 dark:hover:bg-gray-700/50"
                        >
                            <td class="px-6 py-4 whitespace-normal">
                                {{ session.name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ formatDate(session.datetime_start) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <SessionStatusBadge :status="session.session_status_id" />
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                    :class="session.has_ata
                                        ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                                        : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'">
                                    {{ session.has_ata ? 'SIM' : 'NÃO' }}
                                </span>
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium"
                            >
                                <div class="flex items-center justify-end space-x-1">
                                    <IconButton
                                        as="button"
                                        color="purple"
                                        title="Duplicar Sessão"
                                        @click.stop="openConfirmDuplicateModal(session)"
                                    >
                                        <DocumentDuplicateIcon class="h-5 w-5" />
                                    </IconButton>

                                    <IconButton
                                        as="button"
                                        color="blue"
                                        title="Resetar Sessão"
                                        @click.stop="openConfirmResetModal(session)"
                                    >
                                        <ArrowPathIcon class="h-5 w-5" />
                                    </IconButton>

                                    <IconButton
                                        :href="route('sessions.documents', session.id)"
                                        color="green"
                                        title="Documentos da Sessão"
                                    >
                                        <ListBulletIcon class="h-5 w-5" />
                                    </IconButton>

                                    <IconButton
                                        :href="route('sessions.edit', session.id)"
                                        color="yellow"
                                        title="Editar Sesssão"
                                    >
                                        <PencilSquareIcon class="h-5 w-5" />
                                    </IconButton>

                                    <IconButton
                                        as="button"
                                        color="red"
                                        title="Excluir Sessão"
                                        @click.stop="openConfirmDeleteModal(session)"
                                    >
                                        <TrashIcon class="h-5 w-5" />
                                    </IconButton>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div
                v-if="props.sessions.data.length === 0"
                class="bg-white dark:bg-gray-800 rounded-lg p-6 text-center text-gray-500"
            >
                <p>Nenhuma sessão encontrada.</p>
            </div>
        </div>

        <Pagination :paginator="sessions" />
    </AuthenticatedLayout>

    <ConfirmDeletionModal
        :show="confirmingSessionDeletion"
        title="Excluir Sessão"
        :message="`Tem certeza que deseja mover a sessão '${sessionToDelete?.name}' para a lixeira?`"
        :buttonText="'Excluir'"
        @close="closeModal"
        @confirm="deleteSession"
    />

    <ConfirmDeletionModal
        :show="confirmingSessionReset"
        title="Resetar Sessão"
        :message="`Tem certeza que deseja restaurar a sessão '${sessionToReset?.name}' para o estado original? <strong>ESTA AÇÃO LIMPARÁ QUÓRUNS, VOTOS E FALAS; E NÃO PODERÁ SER DESFEITA</strong>`"
        :buttonText="'Confirmar'"
        @close="closeModal"
        @confirm="resetSession"
    />

    <ConfirmDeletionModal
        :show="confirmingSessionDuplication"
        title="Duplicar Sessão"
        :message="`Tem certeza que deseja criar uma réplica da sessão '${sessionToDuplicate?.name}' e de toda a sua pauta?`"
        :buttonText="'Confirmar'"
        @close="closeModal"
        @confirm="duplicateSession"
    />
</template>
