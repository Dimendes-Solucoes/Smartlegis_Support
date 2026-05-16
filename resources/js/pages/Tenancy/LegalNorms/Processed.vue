<script setup lang="ts">
import { ref } from 'vue';
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
import { Head, router } from '@inertiajs/vue3';
import {
    PencilSquareIcon,
    EyeIcon,
    ArrowPathIcon,
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

const props = defineProps<{
    norms: PaginatedNorms;
    normTypes: NormType[];
    normSubjects: NormSubject[];
    availableYears: number[];
    filters: { search: string; year: number | null };
}>();

const norms = ref<LegalNorm[]>(props.norms.data);

// --- Search ---
const search = ref(props.filters.search ?? '');
const year = ref<number | null>(props.filters.year ?? null);

function applySearch() {
    router.get(
        route('legal-norms.confirmed.index'),
        {
            search: search.value || undefined,
            year: year.value || undefined,
        },
        { preserveState: true, replace: true },
    );
}

function clearSearch() {
    search.value = '';
    year.value = null;
    applySearch();
}

// --- Edit modal ---
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

// --- Reprocess ---
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

        // Pre-fill edit modal with extracted data so user can review before saving
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

// --- Helpers ---
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
            <div class="flex gap-2">
                <TextInput
                    v-model="search"
                    class="flex-1"
                    placeholder="Buscar por objeto ou número..."
                    @keydown.enter="applySearch"
                />
                <select
                    v-model="year"
                    class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    @change="applySearch"
                >
                    <option :value="null">Todos os anos</option>
                    <option v-for="y in props.availableYears" :key="y" :value="y">{{ y }}</option>
                </select>
                <TextButton @click="applySearch">Buscar</TextButton>
                <button
                    v-if="search || year"
                    type="button"
                    class="px-3 py-1.5 text-sm text-gray-500 hover:text-gray-700 dark:hover:text-gray-300"
                    @click="clearSearch"
                >
                    Limpar
                </button>
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
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Tipo
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Assunto
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Número
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Objeto
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Data Publicação
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Ativo
                            </th>
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
                                <span
                                    class="line-clamp-2 text-gray-700 dark:text-gray-300"
                                    :title="norm.object ?? ''"
                                >
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
                                    <LinkButton
                                        v-if="norm.attachment"
                                        class="ml-1"
                                        :link="getImageUrl(norm.attachment)"
                                        title="Visualizar documento"
                                    >
                                        <EyeIcon class="h-5 w-5 text-white" />
                                    </LinkButton>
                                    <IconButton
                                        color="yellow"
                                        title="Editar"
                                        class="ml-1"
                                        @click="openEdit(norm)"
                                    >
                                        <PencilSquareIcon class="h-5 w-5" />
                                    </IconButton>
                                    <IconButton
                                        color="indigo"
                                        title="Reprocessar com extrator"
                                        class="ml-1"
                                        :disabled="reprocessingId === norm.id"
                                        @click="reprocess(norm)"
                                    >
                                        <ArrowPathIcon
                                            class="h-5 w-5"
                                            :class="{ 'animate-spin': reprocessingId === norm.id }"
                                        />
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

    <!-- Edit modal -->
    <Modal :show="editingNorm !== null" @close="closeEdit" max-width="lg">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">
                Editar Norma
            </h3>

            <div class="space-y-4">
                <div>
                    <InputLabel value="Tipo" />
                    <SelectInput
                        v-model="editForm.norm_type_id"
                        class="mt-1 block w-full"
                        :options="props.normTypes"
                        :formatLabel="normTypeLabel"
                    />
                    <InputError :message="editErrors.norm_type_id" class="mt-1" />
                </div>

                <div>
                    <InputLabel value="Assunto" />
                    <SelectInput
                        v-model="editForm.norm_subject_id"
                        class="mt-1 block w-full"
                        :options="props.normSubjects"
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
                    <label for="is_active" class="text-sm text-gray-700 dark:text-gray-300">
                        Norma ativa
                    </label>
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
</template>
