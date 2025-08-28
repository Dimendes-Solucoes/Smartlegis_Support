<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, useForm } from '@inertiajs/vue3';

interface Document {
    id: number;
    name: string;
    protocol_number: string | null;
    document_status_vote_id: number;
    document_status_movement_id: number;
}
interface Status {
    id: number;
    name: string;
}
interface EditData {
    document: Document;
    vote_statuses: Status[];
    movement_statuses: Status[];
}

const props = defineProps<{
    data: EditData;
}>();

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
        <div class="py-12">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
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
                                    <TextInput id="protocol_number" type="text" class="mt-1 block w-full" v-model="form.protocol_number" />
                                    <InputError class="mt-2" :message="form.errors.protocol_number" />
                                </div>

                                <div>
                                    <InputLabel for="vote_status" value="Status de Votação" />
                                    <select v-model="form.document_status_vote_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm">
                                        <option v-for="status in data.vote_statuses" :key="status.id" :value="status.id">{{ status.name }}</option>
                                    </select>
                                    <InputError class="mt-2" :message="form.errors.document_status_vote_id" />
                                </div>
                                
                                <div>
                                    <InputLabel for="movement_status" value="Status de Movimentação" />
                                    <select v-model="form.document_status_movement_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm">
                                        <option v-for="status in data.movement_statuses" :key="status.id" :value="status.id">{{ status.name }}</option>
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
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>