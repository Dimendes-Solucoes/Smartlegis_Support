<script setup lang="ts">
import { ref } from 'vue';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import BackButtonRow from '@/components/Common/BackButtonRow.vue';
import PrimaryButton from '@/components/Common/PrimaryButton.vue';
import IconButton from '@/components/Itens/IconButton.vue';
import { PencilSquareIcon, TrashIcon, PlusIcon } from '@heroicons/vue/24/outline';
import ConfirmDeletionModal from '@/components/Common/ConfirmDeletionModal.vue';
import TermModal from './TermModal.vue';

interface Legislature {
    id: number;
    title: string;
    start_at: string;
    end_at: string | null;
    is_current: boolean;
}

interface User {
    id: number;
    name: string;
}

interface Term {
    id: number;
    start_date: string;
    end_date: string | null;
    legislature: Legislature | null;
}

const props = defineProps<{
    data: {
        councilor: User;
        terms: Term[];
        legislatures: Legislature[];
    };
}>();

const formatDate = (dateString: string | null): string => {
    if (!dateString) return 'Em andamento';
    const normalized = /^\d{4}-\d{2}-\d{2}$/.test(dateString) ? `${dateString}T12:00:00` : dateString;
    const date = new Date(normalized);
    if (isNaN(date.getTime())) return 'Em andamento';
    return date.toLocaleDateString('pt-BR');
};

const form = useForm({
    id: null as number | null,
    legislature_id: null as number | null,
    start_date: '',
    end_date: null as string | null,
});

const showTermModal = ref(false);

const openCreateModal = () => {
    form.reset();
    showTermModal.value = true;
};

const openEditModal = (term: Term) => {
    form.id = term.id;
    form.legislature_id = term.legislature?.id ?? null;
    form.start_date = term.start_date;
    form.end_date = term.end_date || null;
    showTermModal.value = true;
};

const closeModal = () => {
    showTermModal.value = false;
    form.reset();
};

const submitTerm = () => {
    if (form.id) {
        form.put(route('councilors.terms.update', form.id), {
            onSuccess: () => closeModal(),
        });
    } else {
        form.post(route('councilors.terms.store', props.data.councilor.id), {
            onSuccess: () => closeModal(),
        });
    }
};

const termToDelete = ref<Term | null>(null);
const confirmingDeletion = ref(false);

const openConfirmDeleteModal = (term: Term) => {
    termToDelete.value = term;
    confirmingDeletion.value = true;
};

const closeDeleteModal = () => {
    confirmingDeletion.value = false;
    termToDelete.value = null;
};

const deleteTerm = () => {
    if (!termToDelete.value) return;
    useForm({}).delete(route('councilors.terms.destroy', termToDelete.value.id), {
        preserveScroll: true,
        onSuccess: () => closeDeleteModal(),
    });
};
</script>

<template>

    <Head :title="`Mandatos de ${data.councilor.name}`" />

    <AuthenticatedLayout>
        <BackButtonRow :href="route('councilors.index')" />

        <div class="flex justify-between items-center mb-6 mt-4">
            <h1 class="text-xl font-bold text-gray-800 dark:text-gray-200">
                Mandatos de: {{ data.councilor.name }}
            </h1>
            <PrimaryButton @click="openCreateModal" class="flex items-center">
                <PlusIcon class="h-5 w-5 mr-2" />
                Adicionar Mandato
            </PrimaryButton>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div v-if="data.terms.length > 0">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Legislatura
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Data de Início
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Data de Fim
                            </th>
                            <th class="relative px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <tr v-for="term in data.terms" :key="term.id">
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-2">
                                    <span class="font-medium text-gray-900 dark:text-gray-100">
                                        {{ term.legislature?.title ?? '-' }}
                                    </span>
                                    <span v-if="term.legislature?.is_current"
                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        Atual
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-700 dark:text-gray-300">
                                {{ formatDate(term.start_date) }}
                            </td>
                            <td class="px-6 py-4 text-gray-700 dark:text-gray-300">
                                {{ formatDate(term.end_date) }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end space-x-1">
                                    <IconButton @click="openEditModal(term)" color="yellow" title="Editar">
                                        <PencilSquareIcon class="h-5 w-5" />
                                    </IconButton>
                                    <IconButton @click="openConfirmDeleteModal(term)" color="red" title="Excluir">
                                        <TrashIcon class="h-5 w-5" />
                                    </IconButton>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-else class="p-6 text-center text-gray-500 dark:text-gray-400">
                <p>Nenhum mandato cadastrado para este vereador.</p>
            </div>
        </div>
    </AuthenticatedLayout>

    <TermModal :show="showTermModal" :form="form" :legislatures="data.legislatures" @close="closeModal"
        @submit="submitTerm" />

    <ConfirmDeletionModal :show="confirmingDeletion" title="Excluir Mandato"
        message="Tem certeza que deseja excluir este mandato?" buttonText="Excluir" @close="closeDeleteModal"
        @confirm="deleteTerm" />
</template>