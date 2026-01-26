<script setup lang="ts">
import { useForm, Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';

import PrimaryButton from '@/components/Common/PrimaryButton.vue';
import InputLabel from '@/components/Form/InputLabel.vue';
import TextInput from '@/components/Form/TextInput.vue';
import SelectInput from '@/components/Form/SelectInput.vue';
import InputError from '@/components/Form/InputError.vue';
import BackButtonRow from '@/components/Common/BackButtonRow.vue';

interface DocumentModel {
    id: number;
    title: string;
    body: string | null;
    user_id: number;
    document_category_id: number;
}

interface Category {
    id: number;
    name: string;
}

interface EditData {
    documentModel: DocumentModel;
    categories: Category[];
}

const props = defineProps<{
    data: EditData;
}>();

const form = useForm({
    title: props.data.documentModel.title,
    document_category_id: props.data.documentModel.document_category_id,
    user_id: props.data.documentModel.user_id,
    body: props.data.documentModel.body || '',
});

const submit = () => {
    form.put(route('document-models.update', props.data.documentModel.id), {
        preserveScroll: true,
    });
};
</script>

<template>

    <Head title="Editar Modelo de Documento" />

    <AuthenticatedLayout>
        <BackButtonRow :href="route('document-models.index')" />

        <form @submit.prevent="submit" class="space-y-6">
            <div>
                <InputLabel for="title" value="Título do Modelo" />
                <TextInput id="title" type="text" class="mt-1 block w-full" v-model="form.title" required autofocus />
                <InputError class="mt-2" :message="form.errors.title" />
            </div>

            <div>
                <InputLabel for="category" value="Categoria do Documento" />
                <SelectInput v-model="form.document_category_id" :options="data.categories" value-key="id"
                    label-key="name" placeholder="Selecione uma categoria" />
                <InputError class="mt-2" :message="form.errors.document_category_id" />
            </div>

            <div class="flex items-center justify-end gap-4">
                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Salvar Alterações
                </PrimaryButton>
            </div>
        </form>
    </AuthenticatedLayout>
</template>