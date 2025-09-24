<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, useForm } from '@inertiajs/vue3';
import BackButtonRow from '@/Components/BackButtonRow.vue';

interface Document {
    id: number;
    name: string;
    protocol_number: string | null;
    document_status_vote_id: number;
    document_status_movement_id: number;
    document_category_id: number; 
}
interface Status {
    id: number;
    name: string;
}
interface Category {
    id: number;
    name: string;
}
interface EditData {
    document: Document;
    vote_statuses: Status[];
    movement_statuses: Status[];
    categories: Category[]; 
}

const props = defineProps<{
    data: EditData;
}>();

const voteStatuses = [
    { id: 1, name: 'Pendente' },
    { id: 2, name: 'Aguardando' },
    { id: 3, name: 'Em vista' },
    { id: 4, name: 'Em votação' },
    { id: 5, name: 'Concluído' },
    { id: 6, name: 'Leitura' },
];

const movementStatuses = [
    { id: 1, name: 'Secretario' },
    { id: 2, name: 'Em sessão' },
    { id: 3, name: 'Procurador' },
    { id: 4, name: 'Comissão Justiça' },
    { id: 5, name: 'Comissões' },
    { id: 6, name: 'Prefeitura' },
    { id: 7, name: 'Em analise' },
    { id: 8, name: 'Reprovado' },
];

const form = useForm({
    _method: 'put',
    name: props.data.document.name,
    protocol_number: props.data.document.protocol_number,
    document_status_vote_id: props.data.document.document_status_vote_id,
    document_status_movement_id: props.data.document.document_status_movement_id,
    document_category_id: props.data.document.document_category_id, 
});

const submit = () => {
    form.post(route('documents.update', props.data.document.id));
};
</script>

<template>
    <Head :title="`Editar Documento`" />
    <AuthenticatedLayout>
        <BackButtonRow :href="route('documents.index')" />

        <form @submit.prevent="submit">
            <div class="space-y-6">

                <div class="flex flex-col md:flex-row md:space-x-4">
                    <div class="flex-1 mb-4 md:mb-0">
                        <InputLabel for="protocol_number" value="Protocolo" />
                        <TextInput id="protocol_number" type="text" class="mt-1 block w-full bg-gray-100 dark:bg-gray-800"
                            v-model="form.protocol_number" readonly />
                        <InputError class="mt-2" :message="form.errors.protocol_number" />
                    </div>

                    <div class="flex-1">
                        <InputLabel for="category" value="Categoria do Documento" />
                        <select v-model="form.document_category_id"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm">
                            <option v-for="category in data.categories" :key="category.id" :value="category.id">{{ category.name }}</option>
                        </select>
                        <InputError class="mt-2" :message="form.errors.document_category_id" />
                    </div>
                </div>
                <div>
                    <InputLabel for="name" value="Objeto" />
                    <textarea id="name"
                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm"
                        v-model="form.name" required rows="6"></textarea>
                    <InputError class="mt-2" :message="form.errors.name" />
                </div>
                <div class="flex flex-col md:flex-row md:space-x-4">
                    <div class="flex-1 mb-4 md:mb-0">
                        <InputLabel for="vote_status" value="Status de Votação" />
                        <select v-model="form.document_status_vote_id"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm">
                            <option v-for="status in voteStatuses" :key="status.id" :value="status.id">{{ status.name }}
                            </option>
                        </select>
                        <InputError class="mt-2" :message="form.errors.document_status_vote_id" />
                    </div>

                    <div class="flex-1">
                        <InputLabel for="movement_status" value="Status de Movimentação" />
                        <select v-model="form.document_status_movement_id"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm">
                            <option v-for="status in movementStatuses" :key="status.id" :value="status.id">
                                {{ status.name }}
                            </option>
                        </select>
                        <InputError class="mt-2" :message="form.errors.document_status_movement_id" />
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end mt-6">
                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Salvar Alterações
                </PrimaryButton>
            </div>
        </form>
    </AuthenticatedLayout>
</template>