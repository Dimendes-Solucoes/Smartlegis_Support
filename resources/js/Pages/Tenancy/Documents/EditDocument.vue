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
}
interface EditData {
    document: Document;
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
});

const submit = () => {
    form.post(route('documents.update', props.data.document.id));
};
</script>

<template>

    <Head :title="`Editar Documento`" />

    <AuthenticatedLayout>
        <BackButtonRow :href="route('documents.index')" />

        <h2 class="text-xl font-semibold mb-6">Editar Documento: {{ data.document.name }}</h2>

        <form @submit.prevent="submit">
            <div class="space-y-6">

                <div>
                    <InputLabel for="name" value="Nome do Documento" />
                    <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" required />
                    <InputError class="mt-2" :message="form.errors.name" />
                </div>

                <div>
                    <InputLabel for="protocol_number" value="Número do Protocolo" />
                    <TextInput id="protocol_number" type="text" class="mt-1 block w-full"
                        v-model="form.protocol_number" />
                    <InputError class="mt-2" :message="form.errors.protocol_number" />
                </div>

                <div>
                    <InputLabel for="vote_status" value="Status de Votação" />
                    <select v-model="form.document_status_vote_id"
                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm">
                        <option v-for="status in voteStatuses" :key="status.id" :value="status.id">{{ status.name }}
                        </option>
                    </select>
                    <InputError class="mt-2" :message="form.errors.document_status_vote_id" />
                </div>

                <div>
                    <InputLabel for="movement_status" value="Status de Movimentação" />
                    <select v-model="form.document_status_movement_id"
                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm">
                        <option v-for="status in movementStatuses" :key="status.id" :value="status.id">{{ status.name }}
                        </option>
                    </select>
                    <InputError class="mt-2" :message="form.errors.document_status_movement_id" />
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