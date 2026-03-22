<script setup lang="ts">
import { ref } from 'vue';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { PencilSquareIcon, TrashIcon, PlusIcon } from '@heroicons/vue/24/outline';
import IconButton from '@/components/Itens/IconButton.vue';
import PrimaryButton from '@/components/Common/PrimaryButton.vue';
import ConfirmDeletionModal from '@/components/Common/ConfirmDeletionModal.vue';
import ProtocolMinimumModal from './ProtocolMinimumModal.vue';

interface ProtocolMinimum {
    id: number;
    year: number;
    min_protocol: number;
    document_category_id: number;
}

interface Category {
    id: number;
    name: string;
    protocol_minimums: ProtocolMinimum[];
}

const props = defineProps<{
    data: {
        categories: Category[];
        current_year: number;
    };
}>();

const form = useForm({
    id: null as number | null,
    document_category_id: null as number | null,
    year: props.data.current_year,
    min_protocol: 1,
});

const showModal = ref(false);

const openCreateModal = (categoryId?: number) => {
    form.reset();
    form.year = props.data.current_year;
    form.min_protocol = 1;
    if (categoryId) form.document_category_id = categoryId;
    showModal.value = true;
};

const openEditModal = (minimum: ProtocolMinimum) => {
    form.id = minimum.id;
    form.document_category_id = minimum.document_category_id;
    form.year = minimum.year;
    form.min_protocol = minimum.min_protocol;
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    form.reset();
};

const submitForm = () => {
    if (form.id) {
        form.put(route('protocol-minimums.update', form.id), {
            onSuccess: () => closeModal(),
        });
    } else {
        form.post(route('protocol-minimums.store'), {
            onSuccess: () => closeModal(),
        });
    }
};

const itemToDelete = ref<ProtocolMinimum | null>(null);
const confirmingDeletion = ref(false);

const openConfirmDeleteModal = (minimum: ProtocolMinimum) => {
    itemToDelete.value = minimum;
    confirmingDeletion.value = true;
};

const closeDeleteModal = () => {
    confirmingDeletion.value = false;
    itemToDelete.value = null;
};

const deleteItem = () => {
    if (!itemToDelete.value) return;
    useForm({}).delete(route('protocol-minimums.destroy', itemToDelete.value.id), {
        preserveScroll: true,
        onSuccess: () => closeDeleteModal(),
    });
};
</script>

<template>

    <Head title="Protocolos Mínimos" />

    <AuthenticatedLayout>
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-xl font-bold text-gray-800 dark:text-gray-200">
                Protocolos Mínimos por Categoria
            </h1>
            <PrimaryButton @click="openCreateModal()" class="flex items-center">
                <PlusIcon class="h-5 w-5 mr-2" />
                Adicionar
            </PrimaryButton>
        </div>

        <div class="space-y-6">
            <div v-for="category in data.categories" :key="category.id"
                class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-base font-semibold text-gray-800 dark:text-gray-200">
                        {{ category.name }}
                    </h2>
                    <button @click="openCreateModal(category.id)"
                        class="text-sm text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-200 flex items-center gap-1">
                        <PlusIcon class="h-4 w-4" />
                        Adicionar ano
                    </button>
                </div>

                <div v-if="category.protocol_minimums.length > 0">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Ano
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Protocolo Mínimo
                                </th>
                                <th class="relative px-6 py-3" />
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-for="minimum in category.protocol_minimums" :key="minimum.id"
                                class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-6 py-3 font-medium text-gray-900 dark:text-gray-100">
                                    {{ minimum.year }}
                                    <span v-if="minimum.year === data.current_year"
                                        class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        Atual
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-gray-700 dark:text-gray-300">
                                    {{ minimum.min_protocol }}
                                </td>
                                <td class="px-6 py-3 text-right">
                                    <div class="flex items-center justify-end space-x-1">
                                        <IconButton
                                            @click="openEditModal({ ...minimum, document_category_id: category.id })"
                                            color="yellow" title="Editar">
                                            <PencilSquareIcon class="h-5 w-5" />
                                        </IconButton>
                                        <IconButton @click="openConfirmDeleteModal(minimum)" color="red"
                                            title="Excluir">
                                            <TrashIcon class="h-5 w-5" />
                                        </IconButton>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-else class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                    Nenhum protocolo mínimo cadastrado para esta categoria.
                </div>
            </div>

            <div v-if="data.categories.length === 0" class="text-center py-10 text-gray-500">
                Nenhuma categoria encontrada.
            </div>
        </div>
    </AuthenticatedLayout>

    <ProtocolMinimumModal :show="showModal" :form="form" :categories="data.categories" @close="closeModal"
        @submit="submitForm" />

    <ConfirmDeletionModal :show="confirmingDeletion" title="Excluir Protocolo Mínimo"
        message="Tem certeza que deseja excluir este protocolo mínimo?" @close="closeDeleteModal"
        @confirm="deleteItem" />
</template>