<script setup lang="ts">
import { ref, computed } from 'vue';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import FileAttachment from '@/components/Form/FileAttachment.vue';
import TextButton from '@/components/Itens/TextButton.vue';
import IconButton from '@/components/Itens/IconButton.vue';
import Modal from '@/components/Common/Modal.vue';
import InputLabel from '@/components/Form/InputLabel.vue';
import TextInput from '@/components/Form/TextInput.vue';
import SelectInput from '@/components/Form/SelectInput.vue';
import InputError from '@/components/Form/InputError.vue';
import ConfirmDeletionModal from '@/components/Common/ConfirmDeletionModal.vue';
import { Head } from '@inertiajs/vue3';
import {
    PencilSquareIcon,
    CheckCircleIcon,
    XCircleIcon,
    CheckIcon,
    XMarkIcon,
    EyeIcon,
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

interface TempLegalNorm {
    id: number;
    original_filename: string;
    object: string | null;
    number: string | null;
    publication_date: string | null;
    norm_type_id: number | null;
    norm_subject_id: number | null;
    is_active: boolean;
    attachment: string;
    attachment_url: string | null;
    extraction_meta: {
        confidence: Record<string, number>;
        was_converted: boolean;
        used_ocr: boolean;
    } | null;
    norm_type: NormType | null;
    norm_subject: NormSubject | null;
}

interface UploadQueueItem {
    name: string;
    status: 'aguardando' | 'processando' | 'concluído' | 'erro';
    message?: string;
}

const props = defineProps<{
    norms: TempLegalNorm[];
    normTypes: NormType[];
    normSubjects: NormSubject[];
}>();

const norms = ref<TempLegalNorm[]>(props.norms);
const files = ref<File[]>([]);
const uploading = ref(false);
const uploadQueue = ref<UploadQueueItem[]>([]);

async function processFiles() {
    if (!files.value.length || uploading.value) return;

    uploading.value = true;
    uploadQueue.value = files.value.map((f) => ({ name: f.name, status: 'aguardando' }));

    for (let i = 0; i < files.value.length; i++) {
        uploadQueue.value[i].status = 'processando';

        const formData = new FormData();
        formData.append('attachment', files.value[i]);

        try {
            const response = await axios.post(route('legal-norms.upload'), formData, {
                headers: { 'Content-Type': 'multipart/form-data' },
            });
            norms.value.unshift(response.data.data);
            uploadQueue.value[i].status = 'concluído';
        } catch (err: any) {
            uploadQueue.value[i].status = 'erro';
            uploadQueue.value[i].message =
                err.response?.data?.message ??
                err.response?.data?.errors?.attachment?.[0] ??
                'Erro ao processar o arquivo.';
        }
    }

    files.value = [];
    uploading.value = false;
}

function clearQueue() {
    uploadQueue.value = [];
}

const getImage = (path: string): string => {
    return getImageUrl(path)
}

// --- Edit modal ---
const editingNorm = ref<TempLegalNorm | null>(null);
const editForm = ref({
    object: '',
    number: '',
    publication_date: '',
    norm_type_id: null as number | null,
    norm_subject_id: null as number | null,
});
const editErrors = ref<Record<string, string>>({});
const editSaving = ref(false);

function openEdit(norm: TempLegalNorm) {
    editingNorm.value = norm;
    editForm.value = {
        object: norm.object ?? '',
        number: norm.number ?? '',
        publication_date: norm.publication_date ?? '',
        norm_type_id: norm.norm_type_id,
        norm_subject_id: norm.norm_subject_id,
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
            route('legal-norms.update', { id: editingNorm.value.id }),
            editForm.value
        );

        const updated: TempLegalNorm = response.data.data;
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

// --- Confirm individual ---
const confirmingId = ref<number | null>(null);
const confirmLoading = ref(false);

async function confirmNorm(id: number) {
    confirmLoading.value = true;

    try {
        await axios.post(route('legal-norms.confirm', { id }));
        norms.value = norms.value.filter((n) => n.id !== id);
    } finally {
        confirmLoading.value = false;
        confirmingId.value = null;
    }
}

// --- Discard individual ---
const discardingNorm = ref<TempLegalNorm | null>(null);
const discardLoading = ref(false);

async function discardNorm() {
    if (!discardingNorm.value) return;

    discardLoading.value = true;

    try {
        await axios.delete(route('legal-norms.discard', { id: discardingNorm.value.id }));
        norms.value = norms.value.filter((n) => n.id !== discardingNorm.value!.id);
    } finally {
        discardLoading.value = false;
        discardingNorm.value = null;
    }
}

// --- Batch actions ---
const selectedIds = ref<number[]>([]);
const batchLoading = ref(false);

const allSelected = computed(
    () => norms.value.length > 0 && selectedIds.value.length === norms.value.length
);

function toggleAll() {
    selectedIds.value = allSelected.value ? [] : norms.value.map((n) => n.id);
}

function toggleOne(id: number) {
    const idx = selectedIds.value.indexOf(id);
    if (idx === -1) selectedIds.value.push(id);
    else selectedIds.value.splice(idx, 1);
}

const showConfirmBatchModal = ref(false);
const showDiscardBatchModal = ref(false);

async function confirmBatch() {
    batchLoading.value = true;
    showConfirmBatchModal.value = false;

    try {
        const ids = selectedIds.value.length ? selectedIds.value : [];
        await axios.post(route('legal-norms.confirm_batch'), { ids });
        const confirmed = new Set(ids.length ? ids : norms.value.map((n) => n.id));
        norms.value = norms.value.filter((n) => !confirmed.has(n.id));
        selectedIds.value = [];
    } finally {
        batchLoading.value = false;
    }
}

async function discardBatch() {
    batchLoading.value = true;
    showDiscardBatchModal.value = false;

    try {
        const ids = selectedIds.value.length ? selectedIds.value : [];
        await axios.delete(route('legal-norms.discard_batch'), { data: { ids } });
        const discarded = new Set(ids.length ? ids : norms.value.map((n) => n.id));
        norms.value = norms.value.filter((n) => !discarded.has(n.id));
        selectedIds.value = [];
    } finally {
        batchLoading.value = false;
    }
}

function confidenceColor(value: number | undefined): string {
    if (value === undefined) return 'text-gray-400';
    if (value >= 0.8) return 'text-green-600';
    if (value >= 0.5) return 'text-yellow-600';
    return 'text-red-500';
}

function confidenceLabel(value: number | undefined): string {
    if (value === undefined) return '—';
    return Math.round(value * 100) + '%';
}

const queueStatusLabel: Record<UploadQueueItem['status'], string> = {
    aguardando: 'Aguardando',
    processando: 'Processando...',
    concluído: 'Concluído',
    erro: 'Erro',
};
</script>

<template>
    <Head title="Normas Jurídicas" />

    <AuthenticatedLayout>
        <!-- Upload section -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">
                Upload em Lote
            </h2>

            <FileAttachment
                v-model="files"
                label="Arquivos (PDF, DOC, DOCX)"
                accept=".pdf,.doc,.docx"
                :max-files="20"
                :max-size="20"
                :multiple="true"
            />

            <!-- Upload queue progress -->
            <div v-if="uploadQueue.length > 0" class="mt-4 space-y-2">
                <div
                    v-for="item in uploadQueue"
                    :key="item.name"
                    class="flex items-center justify-between text-sm px-3 py-2 rounded-lg"
                    :class="{
                        'bg-gray-100 dark:bg-gray-700': item.status === 'aguardando',
                        'bg-blue-50 dark:bg-blue-900/20': item.status === 'processando',
                        'bg-green-50 dark:bg-green-900/20': item.status === 'concluído',
                        'bg-red-50 dark:bg-red-900/20': item.status === 'erro',
                    }"
                >
                    <span class="truncate text-gray-700 dark:text-gray-300 max-w-xs">
                        {{ item.name }}
                    </span>
                    <div class="flex items-center gap-2 ml-4 shrink-0">
                        <span
                            :class="{
                                'text-gray-500': item.status === 'aguardando',
                                'text-blue-600 dark:text-blue-400': item.status === 'processando',
                                'text-green-600 dark:text-green-400': item.status === 'concluído',
                                'text-red-600 dark:text-red-400': item.status === 'erro',
                            }"
                        >
                            {{ queueStatusLabel[item.status] }}
                            <span v-if="item.status === 'erro' && item.message">
                                — {{ item.message }}
                            </span>
                        </span>
                        <CheckCircleIcon v-if="item.status === 'concluído'" class="h-4 w-4 text-green-500" />
                        <XCircleIcon v-else-if="item.status === 'erro'" class="h-4 w-4 text-red-500" />
                    </div>
                </div>

                <div v-if="!uploading" class="flex justify-end pt-1">
                    <button
                        type="button"
                        class="text-xs text-gray-500 hover:text-gray-700 dark:hover:text-gray-300"
                        @click="clearQueue"
                    >
                        Limpar progresso
                    </button>
                </div>
            </div>

            <div class="mt-4 flex justify-end">
                <TextButton
                    @click="processFiles"
                    :disabled="uploading || !files.length"
                    class="p-4"
                    :class="{ 'opacity-50 cursor-not-allowed': uploading || !files.length }"
                >
                    {{ uploading ? 'Processando...' : `Processar ${files.length || ''} Arquivo(s)` }}
                </TextButton>
            </div>
        </div>

        <!-- Pending norms table -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                    Normas Pendentes
                    <span class="ml-1 text-sm font-normal text-gray-500">({{ norms.length }})</span>
                </h2>

                <div v-if="norms.length > 0" class="flex items-center gap-2 flex-wrap">
                    <span v-if="selectedIds.length > 0" class="text-sm text-gray-500 dark:text-gray-400">
                        {{ selectedIds.length }} selecionada(s)
                    </span>
                    <button
                        type="button"
                        class="px-3 py-1.5 text-sm rounded-lg bg-green-100 hover:bg-green-200 text-green-800 dark:bg-green-900/30 dark:hover:bg-green-900/50 dark:text-green-300 transition-colors"
                        :disabled="batchLoading"
                        @click="showConfirmBatchModal = true"
                    >
                        <CheckIcon class="inline h-4 w-4 mr-1" />
                        {{ selectedIds.length ? 'Confirmar Selecionados' : 'Confirmar Todos' }}
                    </button>
                    <button
                        type="button"
                        class="px-3 py-1.5 text-sm rounded-lg bg-red-100 hover:bg-red-200 text-red-800 dark:bg-red-900/30 dark:hover:bg-red-900/50 dark:text-red-300 transition-colors"
                        :disabled="batchLoading"
                        @click="showDiscardBatchModal = true"
                    >
                        <XMarkIcon class="inline h-4 w-4 mr-1" />
                        {{ selectedIds.length ? 'Descartar Selecionados' : 'Descartar Todos' }}
                    </button>
                </div>
            </div>

            <div v-if="norms.length === 0" class="p-10 text-center text-gray-400 dark:text-gray-500">
                Nenhuma norma pendente. Faça o upload de arquivos acima.
            </div>

            <div v-else class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-3 w-8">
                                <input
                                    type="checkbox"
                                    :checked="allSelected"
                                    @change="toggleAll"
                                    class="rounded border-gray-300 dark:border-gray-600"
                                />
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Arquivo
                            </th>
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
                                Confiança
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
                            <td class="px-4 py-3 w-8">
                                <input
                                    type="checkbox"
                                    :checked="selectedIds.includes(norm.id)"
                                    @change="toggleOne(norm.id)"
                                    class="rounded border-gray-300 dark:border-gray-600"
                                />
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap max-w-xs">
                                <span class="truncate block text-gray-800 dark:text-gray-200" :title="norm.original_filename">
                                    {{ norm.original_filename }}
                                </span>
                                <span v-if="norm.extraction_meta?.used_ocr" class="text-xs text-indigo-500">OCR</span>
                                <span v-if="norm.extraction_meta?.was_converted" class="text-xs text-purple-500 ml-1">Convertido</span>
                            </td>
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
                                {{ norm.publication_date ?? '—' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex flex-col gap-0.5 text-xs">
                                    <span :class="confidenceColor(norm.extraction_meta?.confidence?.norm_type_id)">
                                        Tipo: {{ confidenceLabel(norm.extraction_meta?.confidence?.norm_type_id) }}
                                    </span>
                                    <span :class="confidenceColor(norm.extraction_meta?.confidence?.number)">
                                        Num: {{ confidenceLabel(norm.extraction_meta?.confidence?.number) }}
                                    </span>
                                    <span :class="confidenceColor(norm.extraction_meta?.confidence?.publication_date)">
                                        Data: {{ confidenceLabel(norm.extraction_meta?.confidence?.publication_date) }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-right">
                                <IconButton
                                    color="blue"
                                    title="Visualizar arquivo"
                                    :href="norm.attachment ? getImage(norm.attachment) : ''"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                >
                                    <EyeIcon class="h-4 w-4" />
                                </IconButton>
                                <IconButton color="yellow" title="Editar" class="ml-1" @click="openEdit(norm)">
                                    <PencilSquareIcon class="h-4 w-4" />
                                </IconButton>
                                <IconButton
                                    color="green"
                                    title="Confirmar"
                                    class="ml-1"
                                    :disabled="confirmLoading && confirmingId === norm.id"
                                    @click="confirmingId = norm.id; confirmNorm(norm.id)"
                                >
                                    <CheckCircleIcon class="h-4 w-4" />
                                </IconButton>
                                <IconButton
                                    color="red"
                                    title="Descartar"
                                    class="ml-1"
                                    @click="discardingNorm = norm"
                                >
                                    <XCircleIcon class="h-4 w-4" />
                                </IconButton>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AuthenticatedLayout>

    <!-- Edit modal -->
    <Modal :show="editingNorm !== null" @close="closeEdit" max-width="lg">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">
                Editar Norma
            </h3>

            <div v-if="editingNorm" class="text-xs text-gray-500 dark:text-gray-400 mb-4 truncate">
                {{ editingNorm.original_filename }}
            </div>

            <div class="space-y-4">
                <div>
                    <InputLabel value="Tipo" />
                    <SelectInput
                        v-model="editForm.norm_type_id"
                        class="mt-1 block w-full"
                    >
                        <option :value="null">— Selecione —</option>
                        <option v-for="t in props.normTypes" :key="t.id" :value="t.id">
                            {{ t.abbreviation }} — {{ t.name }}
                        </option>
                    </SelectInput>
                    <InputError :message="editErrors.norm_type_id" class="mt-1" />
                </div>

                <div>
                    <InputLabel value="Assunto" />
                    <SelectInput
                        v-model="editForm.norm_subject_id"
                        class="mt-1 block w-full"
                    >
                        <option :value="null">— Selecione —</option>
                        <option v-for="s in props.normSubjects" :key="s.id" :value="s.id">
                            {{ s.name }}
                        </option>
                    </SelectInput>
                    <InputError :message="editErrors.norm_subject_id" class="mt-1" />
                </div>

                <div>
                    <InputLabel value="Número" />
                    <TextInput
                        v-model="editForm.number"
                        class="mt-1 block w-full"
                        placeholder="Ex: 123"
                    />
                    <InputError :message="editErrors.number" class="mt-1" />
                </div>

                <div>
                    <InputLabel value="Data de Publicação" />
                    <TextInput
                        v-model="editForm.publication_date"
                        type="date"
                        class="mt-1 block w-full"
                    />
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

    <!-- Discard single -->
    <ConfirmDeletionModal
        :show="discardingNorm !== null"
        title="Descartar norma"
        :message="`Deseja descartar '${discardingNorm?.original_filename}'? Ela será removida da fila de revisão.`"
        button-text="Descartar"
        @close="discardingNorm = null"
        @confirm="discardNorm"
    />

    <!-- Confirm batch -->
    <ConfirmDeletionModal
        :show="showConfirmBatchModal"
        title="Confirmar normas"
        :message="selectedIds.length
            ? `Confirmar ${selectedIds.length} norma(s) selecionada(s)? Elas serão criadas como normas jurídicas definitivas.`
            : `Confirmar TODAS as ${norms.length} normas pendentes?`"
        button-text="Confirmar"
        @close="showConfirmBatchModal = false"
        @confirm="confirmBatch"
    />

    <!-- Discard batch -->
    <ConfirmDeletionModal
        :show="showDiscardBatchModal"
        title="Descartar normas"
        :message="selectedIds.length
            ? `Descartar ${selectedIds.length} norma(s) selecionada(s)?`
            : `Descartar TODAS as ${norms.length} normas pendentes?`"
        button-text="Descartar"
        @close="showDiscardBatchModal = false"
        @confirm="discardBatch"
    />
</template>
