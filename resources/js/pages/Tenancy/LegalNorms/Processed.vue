<script setup lang="ts">
import { ref, watch, computed } from 'vue';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import TextButton from '@/components/Itens/TextButton.vue';
import IconButton from '@/components/Itens/IconButton.vue';
import Modal from '@/components/Common/Modal.vue';
import InputLabel from '@/components/Form/InputLabel.vue';
import TextInput from '@/components/Form/TextInput.vue';
import SelectInput from '@/components/Form/SelectInput.vue';
import InputError from '@/components/Form/InputError.vue';
import Pagination from '@/components/Table/Pagination.vue';
import LinkButton from '@/components/Common/LinkButton.vue';
import PrimaryButton from '@/components/Common/PrimaryButton.vue';
import SecondaryButton from '@/components/Common/SecondaryButton.vue';
import ConfirmDeletionModal from '@/components/Common/ConfirmDeletionModal.vue';
import { Head, router } from '@inertiajs/vue3';
import {
    PencilSquareIcon,
    EyeIcon,
    ArrowPathIcon,
    InboxArrowDownIcon,
    BuildingOffice2Icon,
    TrashIcon,
    MagnifyingGlassIcon,
    XMarkIcon,
} from '@heroicons/vue/24/outline';
import axios from 'axios';
import { getImageUrl } from '@/utils/image';

interface NormType {
    id: number;
    name: string;
    abbreviation: string;
}

interface NormSubject {
    id: number;
    name: string;
}

interface LegalNorm {
    id: number;
    object: string | null;
    number: string | null;
    publication_date: string | null;
    norm_type_id: number | null;
    norm_subject_id: number | null;
    is_active: boolean;
    attachment: string;
    norm_type: NormType | null;
    norm_subject: NormSubject | null;
}

interface PaginatedNorms {
    data: LegalNorm[];
    links: { url: string | null; label: string; active: boolean }[];
    total: number;
    per_page: number;
    current_page: number;
}

interface Session {
    id: number;
    name: string;
    datetime_start: string;
    year: number | null;
    session_status_id: number;
    has_ata: boolean;
}

interface Commission {
    id: number;
    comission_name: string;
    type: number;
}

interface Legislature {
    id: number;
    title: string;
    commissions: Commission[];
}

const props = defineProps<{
    norms: PaginatedNorms;
    normTypes: NormType[];
    normSubjects: NormSubject[];
    allNormTypes: NormType[];
    allNormSubjects: NormSubject[];
    availableYears: number[];
    filters: {
        search: string;
        year: number | null;
        norm_type_id: number | null;
        norm_subject_id: number | null;
    };
}>();

const norms = ref<LegalNorm[]>(props.norms.data);

watch(() => props.norms.data, (newData: LegalNorm[]) => {
    norms.value = newData;
});

// ── Busca ──────────────────────────────────────────────────────
const search = ref(props.filters.search ?? '');
const year = ref<number | null>(props.filters.year ?? null);
const selectedNormType = ref<number | null>(props.filters.norm_type_id ?? null);
const selectedNormSubject = ref<number | null>(props.filters.norm_subject_id ?? null);

function applySearch() {
    router.get(
        route('legal-norms.confirmed.index'),
        {
            search: search.value || undefined,
            year: year.value || undefined,
            norm_type_id: selectedNormType.value || undefined,
            norm_subject_id: selectedNormSubject.value || undefined,
        },
        { preserveState: true, replace: true },
    );
}

const hasActiveFilters = computed(() =>
    !!(search.value || year.value || selectedNormType.value || selectedNormSubject.value)
);

function clearSearch() {
    search.value = '';
    year.value = null;
    selectedNormType.value = null;
    selectedNormSubject.value = null;
    applySearch();
}

// ── Modal de edição ────────────────────────────────────────────
const editingNorm = ref<LegalNorm | null>(null);
const editForm = ref({
    object: '',
    number: '',
    publication_date: '',
    norm_type_id: null as number | null,
    norm_subject_id: null as number | null,
    is_active: true,
});
const editErrors = ref<Record<string, string>>({});
const editSaving = ref(false);

function openEdit(norm: LegalNorm) {
    editingNorm.value = norm;
    editForm.value = {
        object: norm.object ?? '',
        number: norm.number ?? '',
        publication_date: norm.publication_date ?? '',
        norm_type_id: norm.norm_type_id,
        norm_subject_id: norm.norm_subject_id,
        is_active: norm.is_active,
    };
    editErrors.value = {};
}

function closeEdit() {
    editingNorm.value = null;
}

async function saveEdit() {
    if (!editingNorm.value || editSaving.value) return;
    editSaving.value = true;
    editErrors.value = {};
    try {
        const response = await axios.put(
            route('legal-norms.confirmed.update', { id: editingNorm.value.id }),
            editForm.value,
        );
        const updated: LegalNorm = response.data.data;
        const idx = norms.value.findIndex((n) => n.id === updated.id);
        if (idx !== -1) norms.value[idx] = updated;
        closeEdit();
    } catch (err: any) {
        if (err.response?.status === 422) {
            const apiErrors = err.response.data.errors ?? {};
            Object.keys(apiErrors).forEach((k) => {
                editErrors.value[k] = apiErrors[k][0];
            });
        }
    } finally {
        editSaving.value = false;
    }
}

// ── Reprocessar ────────────────────────────────────────────────
const reprocessingId = ref<number | null>(null);
const reprocessError = ref('');

async function reprocess(norm: LegalNorm) {
    if (reprocessingId.value !== null) return;
    reprocessingId.value = norm.id;
    reprocessError.value = '';
    try {
        const response = await axios.post(
            route('legal-norms.confirmed.reprocess', { id: norm.id }),
        );
        const extracted = response.data.data;
        editingNorm.value = norm;
        editForm.value = {
            object: extracted.object ?? norm.object ?? '',
            number: extracted.number ?? norm.number ?? '',
            publication_date: extracted.publication_date ?? norm.publication_date ?? '',
            norm_type_id: extracted.norm_type_id ?? norm.norm_type_id,
            norm_subject_id: extracted.norm_subject_id ?? norm.norm_subject_id,
            is_active: norm.is_active,
        };
        editErrors.value = {};
    } catch (err: any) {
        reprocessError.value =
            err.response?.data?.message ?? 'Erro ao reprocessar o documento.';
    } finally {
        reprocessingId.value = null;
    }
}

// ── Excluir norma ──────────────────────────────────────────────
const confirmingDeletion = ref(false);
const normToDelete = ref<LegalNorm | null>(null);
const deleting = ref(false);
const deleteError = ref('');

function openDeleteModal(norm: LegalNorm) {
    normToDelete.value = norm;
    confirmingDeletion.value = true;
    deleteError.value = '';
}

function closeDeleteModal() {
    confirmingDeletion.value = false;
    normToDelete.value = null;
}

async function confirmDelete() {
    if (!normToDelete.value || deleting.value) return;
    deleting.value = true;
    deleteError.value = '';
    try {
        const csrfMeta = document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]');
        const response = await fetch(
            route('legal-norms.confirmed.destroy', { id: normToDelete.value.id }),
            {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfMeta?.content ?? '',
                    'Accept': 'application/json',
                },
            },
        );
        if (!response.ok) {
            const data = await response.json();
            deleteError.value = data.message ?? 'Erro ao excluir norma.';
            return;
        }
        const idx = norms.value.findIndex(n => n.id === normToDelete.value!.id);
        if (idx !== -1) norms.value.splice(idx, 1);
        closeDeleteModal();
    } catch {
        deleteError.value = 'Erro ao excluir norma.';
    } finally {
        deleting.value = false;
    }
}

// ── Modal de sessão ────────────────────────────────────────────
const showSessionModal = ref(false);
const selectedNormForSession = ref<LegalNorm | null>(null);
const sessions = ref<Session[]>([]);
const sessionLoading = ref(false);
const sessionError = ref('');
const addingToSession = ref(false);
const selectedSessionId = ref<number | null>(null);

const sessionName = ref('');
const sessionStatusFilter = ref('0');
const sessionWithoutAta = ref(false);

const sessionStatusOptions = [
    { id: '0', name: 'Qualquer status' },
    { id: '1', name: 'Abertas / Em andamento' },
];

const sessionYearTabs = computed(() => {
    const years = new Set<number>();
    sessions.value.forEach((s: Session) => { if (s.year) years.add(s.year); });
    return [...years].sort((a: number, b: number) => b - a);
});
const activeSessionYearTab = ref<number | null>(null);
const visibleSessions = computed(() =>
    activeSessionYearTab.value === null
        ? sessions.value
        : sessions.value.filter((s: Session) => s.year === activeSessionYearTab.value)
);

watch(sessions, (newSessions: Session[]) => {
    const tabs = sessionYearTabs.value;
    activeSessionYearTab.value = (newSessions.length > 0 && tabs.length > 0) ? tabs[0] : null;
});

const getSessionStatusText = (id: number) =>
    ({ 1: 'Aguardando Votação', 2: 'Em Votação', 3: 'Concluída' } as Record<number, string>)[id] ?? 'N/A';

const getSessionStatusColor = (id: number) =>
    ({
        1: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
        2: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        3: 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
    } as Record<number, string>)[id] ?? 'bg-gray-100 text-gray-800';

function openSessionModal(norm: LegalNorm) {
    selectedNormForSession.value = norm;
    sessionError.value = '';
    sessions.value = [];
    activeSessionYearTab.value = null;
    showSessionModal.value = true;
    fetchSessions();
}

function closeSessionModal() {
    showSessionModal.value = false;
    selectedNormForSession.value = null;
    sessions.value = [];
    sessionError.value = '';
    addingToSession.value = false;
    selectedSessionId.value = null;
}

async function fetchSessions() {
    sessionLoading.value = true;
    sessionError.value = '';
    try {
        const params = new URLSearchParams();
        if (sessionName.value) params.append('name', sessionName.value);
        if (sessionStatusFilter.value === '1') params.append('only_open', '1');
        if (sessionWithoutAta.value) params.append('without_ata', '1');

        const response = await fetch(
            `${route('legal-norms.confirmed.available_sessions')}?${params.toString()}`,
            { headers: { 'Accept': 'application/json' } },
        );
        if (!response.ok) throw new Error();
        sessions.value = await response.json();
    } catch {
        sessionError.value = 'Erro ao carregar sessões.';
    } finally {
        sessionLoading.value = false;
    }
}

async function addNormToSession(sessionId: number) {
    if (!selectedNormForSession.value || addingToSession.value) return;
    addingToSession.value = true;
    selectedSessionId.value = sessionId;
    sessionError.value = '';
    try {
        const csrfMeta = document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]');
        const response = await fetch(
            route('legal-norms.confirmed.convert_to_session', { id: selectedNormForSession.value.id }),
            {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfMeta?.content ?? '',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ session_id: sessionId }),
            },
        );
        const data = await response.json();
        if (!response.ok) {
            sessionError.value = data.message ?? 'Erro ao converter norma.';
            return;
        }
        const idx = norms.value.findIndex(n => n.id === selectedNormForSession.value!.id);
        if (idx !== -1) norms.value.splice(idx, 1);
        closeSessionModal();
    } catch {
        sessionError.value = 'Erro ao enviar norma para a sessão.';
    } finally {
        addingToSession.value = false;
        selectedSessionId.value = null;
    }
}

// ── Modal de comissão ──────────────────────────────────────────
const showCommissionModal = ref(false);
const selectedNormForCommission = ref<LegalNorm | null>(null);
const legislatures = ref<Legislature[]>([]);
const commissionLoading = ref(false);
const commissionError = ref('');
const addingToCommission = ref(false);
const selectedCommissionId = ref<number | null>(null);
const activeLegislatureTab = ref<number | null>(null);

const visibleCommissions = computed(() => {
    const tabId = activeLegislatureTab.value;
    return legislatures.value.find(l => l.id === tabId)?.commissions ?? [];
});

watch(legislatures, (newList: Legislature[]) => {
    activeLegislatureTab.value = newList[0]?.id ?? null;
});

function openCommissionModal(norm: LegalNorm) {
    selectedNormForCommission.value = norm;
    commissionError.value = '';
    legislatures.value = [];
    activeLegislatureTab.value = null;
    showCommissionModal.value = true;
    fetchCommissions();
}

function closeCommissionModal() {
    showCommissionModal.value = false;
    selectedNormForCommission.value = null;
    legislatures.value = [];
    commissionError.value = '';
    addingToCommission.value = false;
    selectedCommissionId.value = null;
}

async function fetchCommissions() {
    commissionLoading.value = true;
    commissionError.value = '';
    try {
        const response = await fetch(
            route('legal-norms.confirmed.available_commissions'),
            { headers: { 'Accept': 'application/json' } },
        );
        if (!response.ok) throw new Error();
        legislatures.value = await response.json();
    } catch {
        commissionError.value = 'Erro ao carregar comissões.';
    } finally {
        commissionLoading.value = false;
    }
}

async function addNormToCommission(commissionId: number) {
    if (!selectedNormForCommission.value || addingToCommission.value) return;
    addingToCommission.value = true;
    selectedCommissionId.value = commissionId;
    commissionError.value = '';
    try {
        const csrfMeta = document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]');
        const response = await fetch(
            route('legal-norms.confirmed.convert_to_commission', { id: selectedNormForCommission.value.id }),
            {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfMeta?.content ?? '',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ commission_id: commissionId }),
            },
        );
        const data = await response.json();
        if (!response.ok) {
            commissionError.value = data.message ?? 'Erro ao converter norma.';
            return;
        }
        const idx = norms.value.findIndex(n => n.id === selectedNormForCommission.value!.id);
        if (idx !== -1) norms.value.splice(idx, 1);
        closeCommissionModal();
    } catch {
        commissionError.value = 'Erro ao enviar norma para a comissão.';
    } finally {
        addingToCommission.value = false;
        selectedCommissionId.value = null;
    }
}

// ── Helpers ────────────────────────────────────────────────────
const normTypeLabel = (t: NormType) => `${t.abbreviation} — ${t.name}`;

function formatDate(date: string | null): string {
    if (!date) return '—';
    const [y, m, d] = date.split('-');
    return `${d}/${m}/${y}`;
}
</script>

<template>
    <Head title="Normas Jurídicas — Confirmadas" />

    <AuthenticatedLayout>
        <!-- Header bar -->
        <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
            <h1 class="text-xl font-semibold text-gray-800 dark:text-gray-100">
                Normas Jurídicas Confirmadas
            </h1>
            <TextButton :href="route('legal-norms.index')" color="gray">
                Pendentes
            </TextButton>
        </div>

        <!-- Search -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 mb-6">
            <div class="flex flex-col gap-3">
                <TextInput
                    v-model="search"
                    class="w-full"
                    placeholder="Buscar por objeto ou número..."
                    @keydown.enter="applySearch"
                />
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <SelectInput
                        v-model="selectedNormType"
                        :options="props.normTypes"
                        value-key="id"
                        label-key="name"
                        placeholder="Todos os tipos"
                    />
                    <SelectInput
                        v-model="selectedNormSubject"
                        :options="props.normSubjects"
                        value-key="id"
                        label-key="name"
                        placeholder="Todos os assuntos"
                    />
                    <select
                        v-model="year"
                        class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        <option :value="null">Todos os anos</option>
                        <option v-for="y in props.availableYears" :key="y" :value="y">{{ y }}</option>
                    </select>
                </div>
                <div class="flex justify-end gap-2">
                    <button
                        v-if="hasActiveFilters"
                        type="button"
                        class="px-3 py-1.5 text-sm text-gray-500 hover:text-gray-700 dark:hover:text-gray-300"
                        @click="clearSearch"
                    >
                        Limpar
                    </button>
                    <TextButton @click="applySearch">Buscar</TextButton>
                </div>
            </div>
        </div>

        <!-- Error toast -->
        <div
            v-if="reprocessError"
            class="mb-4 px-4 py-3 rounded-lg bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300 text-sm"
        >
            {{ reprocessError }}
            <button class="ml-3 text-red-500 hover:text-red-700" @click="reprocessError = ''">✕</button>
        </div>

        <!-- Table -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h2 class="text-base font-semibold text-gray-800 dark:text-gray-100">
                    Normas
                    <span class="ml-1 text-sm font-normal text-gray-500">({{ props.norms.total }})</span>
                </h2>
            </div>

            <div v-if="norms.length === 0" class="p-10 text-center text-gray-400 dark:text-gray-500">
                Nenhuma norma confirmada encontrada.
            </div>

            <div v-else class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tipo</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Assunto</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Número</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Objeto</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Data Publicação</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Ativo</th>
                            <th class="px-4 py-3 relative"><span class="sr-only">Ações</span></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        <tr
                            v-for="norm in norms"
                            :key="norm.id"
                            class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors"
                        >
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span v-if="norm.norm_type" class="font-medium text-gray-800 dark:text-gray-200">
                                    {{ norm.norm_type.abbreviation }}
                                </span>
                                <span v-else class="text-gray-400">—</span>
                            </td>
                            <td class="px-4 py-3 max-w-xs">
                                <span v-if="norm.norm_subject" class="text-gray-700 dark:text-gray-300">
                                    {{ norm.norm_subject.name }}
                                </span>
                                <span v-else class="text-gray-400">—</span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-gray-700 dark:text-gray-300">
                                {{ norm.number ?? '—' }}
                            </td>
                            <td class="px-4 py-3 max-w-xs">
                                <span class="line-clamp-2 text-gray-700 dark:text-gray-300" :title="norm.object ?? ''">
                                    {{ norm.object ?? '—' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-gray-700 dark:text-gray-300">
                                {{ formatDate(norm.publication_date) }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span
                                    class="inline-block px-2 py-0.5 rounded-full text-xs font-medium"
                                    :class="norm.is_active
                                        ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300'
                                        : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400'"
                                >
                                    {{ norm.is_active ? 'Sim' : 'Não' }}
                                </span>
                            </td>
                            <td class="px-6 py-2 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-1">
                                    <!-- Visualizar -->
                                    <LinkButton
                                        v-if="norm.attachment"
                                        color="blue"
                                        :link="getImageUrl(norm.attachment)"
                                        title="Visualizar documento"
                                    >
                                        <EyeIcon class="h-5 w-5 text-white" />
                                    </LinkButton>
                                    <!-- Enviar para sessão -->
                                    <IconButton
                                        as="button"
                                        color="green"
                                        title="Converter para ata de sessão"
                                        @click="openSessionModal(norm)"
                                    >
                                        <InboxArrowDownIcon class="h-5 w-5" />
                                    </IconButton>
                                    <!-- Enviar para comissão -->
                                    <IconButton
                                        as="button"
                                        color="orange"
                                        title="Converter para documento de comissão"
                                        @click="openCommissionModal(norm)"
                                    >
                                        <BuildingOffice2Icon class="h-5 w-5" />
                                    </IconButton>
                                    <!-- Editar -->
                                    <IconButton
                                        color="yellow"
                                        title="Editar"
                                        @click="openEdit(norm)"
                                    >
                                        <PencilSquareIcon class="h-5 w-5" />
                                    </IconButton>
                                    <!-- Reprocessar -->
                                    <IconButton
                                        color="indigo"
                                        title="Reprocessar com extrator"
                                        :disabled="reprocessingId === norm.id"
                                        @click="reprocess(norm)"
                                    >
                                        <ArrowPathIcon
                                            class="h-5 w-5"
                                            :class="{ 'animate-spin': reprocessingId === norm.id }"
                                        />
                                    </IconButton>
                                    <!-- Excluir -->
                                    <IconButton
                                        as="button"
                                        color="red"
                                        title="Excluir norma"
                                        @click="openDeleteModal(norm)"
                                    >
                                        <TrashIcon class="h-5 w-5" />
                                    </IconButton>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="px-4 pb-4">
                <Pagination :paginator="props.norms" />
            </div>
        </div>
    </AuthenticatedLayout>

    <!-- Modal de edição -->
    <Modal :show="editingNorm !== null" @close="closeEdit" max-width="lg">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Editar Norma</h3>
            <div class="space-y-4">
                <div>
                    <InputLabel value="Tipo" />
                    <SelectInput
                        v-model="editForm.norm_type_id"
                        class="mt-1 block w-full"
                        :options="props.allNormTypes"
                        :formatLabel="normTypeLabel"
                    />
                    <InputError :message="editErrors.norm_type_id" class="mt-1" />
                </div>
                <div>
                    <InputLabel value="Assunto" />
                    <SelectInput
                        v-model="editForm.norm_subject_id"
                        class="mt-1 block w-full"
                        :options="props.allNormSubjects"
                        labelKey="name"
                    />
                    <InputError :message="editErrors.norm_subject_id" class="mt-1" />
                </div>
                <div>
                    <InputLabel value="Número" />
                    <TextInput v-model="editForm.number" class="mt-1 block w-full" placeholder="Ex: 123" />
                    <InputError :message="editErrors.number" class="mt-1" />
                </div>
                <div>
                    <InputLabel value="Data de Publicação" />
                    <TextInput v-model="editForm.publication_date" type="date" class="mt-1 block w-full" />
                    <InputError :message="editErrors.publication_date" class="mt-1" />
                </div>
                <div>
                    <InputLabel value="Objeto / Ementa" />
                    <textarea
                        v-model="editForm.object"
                        rows="3"
                        maxlength="120"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                        placeholder="Descrição resumida do conteúdo da norma"
                    />
                    <div class="text-xs text-right text-gray-400 mt-0.5">
                        {{ (editForm.object ?? '').length }}/120
                    </div>
                    <InputError :message="editErrors.object" class="mt-1" />
                </div>
                <div class="flex items-center gap-2">
                    <input
                        id="is_active"
                        v-model="editForm.is_active"
                        type="checkbox"
                        class="rounded border-gray-300 dark:border-gray-600 text-indigo-600"
                    />
                    <label for="is_active" class="text-sm text-gray-700 dark:text-gray-300">Norma ativa</label>
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <button
                    type="button"
                    class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white"
                    @click="closeEdit"
                >
                    Cancelar
                </button>
                <TextButton class="px-4 py-2" :disabled="editSaving" @click="saveEdit">
                    {{ editSaving ? 'Salvando...' : 'Salvar' }}
                </TextButton>
            </div>
        </div>
    </Modal>

    <!-- Modal de exclusão -->
    <ConfirmDeletionModal
        :show="confirmingDeletion"
        title="Excluir Norma"
        :message="`Tem certeza que deseja excluir a norma '${normToDelete?.object ?? normToDelete?.number ?? ''}'?`"
        @close="closeDeleteModal"
        @confirm="confirmDelete"
    />

    <!-- Modal enviar para sessão -->
    <Teleport to="body">
        <div v-if="showSessionModal" class="fixed inset-0 z-50 flex items-center justify-center p-4"
            @click.self="closeSessionModal">
            <div class="absolute inset-0 bg-black/50" />

            <div class="relative z-10 w-full max-w-2xl bg-white dark:bg-gray-800 rounded-xl shadow-xl flex flex-col max-h-[90vh]">
                <!-- Header -->
                <div class="flex items-start justify-between p-5 border-b border-gray-200 dark:border-gray-700">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Converter para Ata de Sessão</h3>
                        <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400 line-clamp-2">
                            {{ selectedNormForSession?.object ?? selectedNormForSession?.number }}
                        </p>
                    </div>
                    <button type="button" @click="closeSessionModal"
                        class="ml-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                        <XMarkIcon class="h-5 w-5" />
                    </button>
                </div>

                <!-- Filtros -->
                <div class="p-5 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex flex-col gap-3">
                        <TextInput type="text" v-model="sessionName" placeholder="Nome da sessão..." />
                        <div class="grid grid-cols-2 gap-3">
                            <SelectInput v-model="sessionStatusFilter" :options="sessionStatusOptions"
                                value-key="id" label-key="name" :disable-placeholder="true" />
                            <label class="flex items-center gap-2 cursor-pointer select-none">
                                <input type="checkbox" v-model="sessionWithoutAta"
                                    class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500 dark:bg-gray-900" />
                                <span class="text-sm text-gray-700 dark:text-gray-300">Sem ata</span>
                            </label>
                        </div>
                    </div>
                    <div class="flex justify-end mt-3">
                        <PrimaryButton type="button" @click="fetchSessions" :disabled="sessionLoading"
                            class="h-9 flex items-center">
                            <MagnifyingGlassIcon class="h-5 w-5 mr-2" />
                            Buscar
                        </PrimaryButton>
                    </div>
                </div>

                <!-- Lista de sessões -->
                <div class="flex-1 overflow-y-auto p-5 min-h-[12rem]">
                    <div v-if="sessionLoading" class="text-center py-10 text-gray-500 dark:text-gray-400">
                        Carregando sessões...
                    </div>
                    <div v-else-if="sessionError" class="text-center py-4 text-sm text-red-600 dark:text-red-400">
                        {{ sessionError }}
                    </div>
                    <div v-else-if="sessions.length === 0"
                        class="text-center py-10 text-gray-400 dark:text-gray-500 text-sm">
                        Nenhuma sessão encontrada.
                    </div>
                    <template v-else>
                        <!-- Abas de ano -->
                        <div v-if="sessionYearTabs.length > 1" class="flex gap-1 flex-wrap mb-3">
                            <button v-for="tabYear in sessionYearTabs" :key="tabYear" type="button"
                                @click="activeSessionYearTab = tabYear"
                                class="px-3 py-1 text-xs font-medium rounded-full transition-colors"
                                :class="activeSessionYearTab === tabYear
                                    ? 'bg-indigo-600 text-white'
                                    : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600'">
                                {{ tabYear }}
                            </button>
                        </div>
                        <div class="space-y-2">
                            <button v-for="session in visibleSessions" :key="session.id" type="button"
                                :disabled="addingToSession"
                                @click="addNormToSession(session.id)"
                                class="w-full flex items-center justify-between p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 text-left transition-colors disabled:opacity-60 disabled:cursor-not-allowed"
                                :class="{ 'opacity-60': addingToSession && selectedSessionId !== session.id }">
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-gray-100 text-sm">{{ session.name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ session.datetime_start }}</p>
                                </div>
                                <div class="flex items-center gap-2 ml-3 shrink-0">
                                    <span v-if="session.has_ata"
                                        class="px-2 py-0.5 text-xs font-semibold rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-200">
                                        Tem ata
                                    </span>
                                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full"
                                        :class="getSessionStatusColor(session.session_status_id)">
                                        {{ getSessionStatusText(session.session_status_id) }}
                                    </span>
                                    <span v-if="addingToSession && selectedSessionId === session.id"
                                        class="text-xs text-gray-400">Enviando...</span>
                                </div>
                            </button>
                        </div>
                    </template>
                </div>

                <!-- Footer -->
                <div class="flex justify-end px-5 py-4 border-t border-gray-200 dark:border-gray-700">
                    <SecondaryButton @click="closeSessionModal">Fechar</SecondaryButton>
                </div>
            </div>
        </div>
    </Teleport>

    <!-- Modal enviar para comissão -->
    <Teleport to="body">
        <div v-if="showCommissionModal" class="fixed inset-0 z-50 flex items-center justify-center p-4"
            @click.self="closeCommissionModal">
            <div class="absolute inset-0 bg-black/50" />

            <div class="relative z-10 w-full max-w-2xl bg-white dark:bg-gray-800 rounded-xl shadow-xl flex flex-col max-h-[90vh]">
                <!-- Header -->
                <div class="flex items-start justify-between p-5 border-b border-gray-200 dark:border-gray-700">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Converter para Documento de Comissão</h3>
                        <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400 line-clamp-2">
                            {{ selectedNormForCommission?.object ?? selectedNormForCommission?.number }}
                        </p>
                    </div>
                    <button type="button" @click="closeCommissionModal"
                        class="ml-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                        <XMarkIcon class="h-5 w-5" />
                    </button>
                </div>

                <!-- Abas de legislatura -->
                <div v-if="!commissionLoading && legislatures.length > 0"
                    class="px-5 pt-4 flex gap-1 flex-wrap border-b border-gray-200 dark:border-gray-700 pb-3">
                    <button v-for="leg in legislatures" :key="leg.id" type="button"
                        @click="activeLegislatureTab = leg.id"
                        class="px-3 py-1 text-xs font-medium rounded-full transition-colors"
                        :class="activeLegislatureTab === leg.id
                            ? 'bg-indigo-600 text-white'
                            : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600'">
                        {{ leg.title }}
                    </button>
                </div>

                <!-- Lista de comissões -->
                <div class="flex-1 overflow-y-auto p-5 min-h-[12rem]">
                    <div v-if="commissionLoading" class="text-center py-10 text-gray-500 dark:text-gray-400">
                        Carregando comissões...
                    </div>
                    <div v-else-if="commissionError" class="text-center py-4 text-sm text-red-600 dark:text-red-400">
                        {{ commissionError }}
                    </div>
                    <div v-else-if="legislatures.length === 0"
                        class="text-center py-10 text-gray-400 dark:text-gray-500 text-sm">
                        Nenhuma comissão encontrada.
                    </div>
                    <div v-else-if="visibleCommissions.length === 0"
                        class="text-center py-10 text-gray-400 dark:text-gray-500 text-sm">
                        Nenhuma comissão ativa nesta legislatura.
                    </div>
                    <div v-else class="space-y-2">
                        <button v-for="commission in visibleCommissions" :key="commission.id" type="button"
                            :disabled="addingToCommission"
                            @click="addNormToCommission(commission.id)"
                            class="w-full flex items-center justify-between p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 text-left transition-colors disabled:opacity-60 disabled:cursor-not-allowed"
                            :class="{ 'opacity-60': addingToCommission && selectedCommissionId !== commission.id }">
                            <div class="flex items-center gap-3">
                                <BuildingOffice2Icon class="h-5 w-5 text-gray-400 shrink-0" />
                                <p class="font-medium text-gray-900 dark:text-gray-100 text-sm">
                                    {{ commission.comission_name }}
                                </p>
                            </div>
                            <span v-if="addingToCommission && selectedCommissionId === commission.id"
                                class="text-xs text-gray-400 ml-3 shrink-0">Enviando...</span>
                        </button>
                    </div>
                </div>

                <!-- Footer -->
                <div class="flex justify-end px-5 py-4 border-t border-gray-200 dark:border-gray-700">
                    <SecondaryButton @click="closeCommissionModal">Fechar</SecondaryButton>
                </div>
            </div>
        </div>
    </Teleport>
</template>
