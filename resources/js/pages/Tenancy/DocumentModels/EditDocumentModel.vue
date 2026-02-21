<script setup lang="ts">
import { ref } from 'vue';
import { useForm, Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/components/Common/PrimaryButton.vue';
import SecondaryButton from '@/components/Common/SecondaryButton.vue';
import InputLabel from '@/components/Form/InputLabel.vue';
import TextInput from '@/components/Form/TextInput.vue';
import SelectInput from '@/components/Form/SelectInput.vue';
import InputError from '@/components/Form/InputError.vue';
import BackButtonRow from '@/components/Common/BackButtonRow.vue';
import TextEditor from '@/components/Form/TextEditor.vue';
import DocumentUploadModal from './DocumentUploadModal.vue';

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

const showUploadModal = ref(false);

const submit = () => {
    form.put(route('document-models.update', props.data.documentModel.id), {
        preserveScroll: true,
    });
};

const handleConvertedHtml = (html: string) => {
    form.body = html;
};

const openUploadModal = () => {
    showUploadModal.value = true;
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

            <div>
                <div class="flex items-center justify-between">
                    <InputLabel for="body" value="Conteúdo do Modelo" />

                    <SecondaryButton type="button" @click="openUploadModal" class="text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                        Importar Word/PDF
                    </SecondaryButton>
                </div>

                <TextEditor v-model="form.body" id="body" :height="500"
                    placeholder="Digite aqui o texto padrão deste modelo..." class="mt-1" />

                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    Este texto servirá de base quando novos documentos forem criados a partir deste modelo.
                </p>
                <InputError class="mt-2" :message="form.errors.body" />
            </div>

            <div class="flex items-center justify-end gap-4">
                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Salvar Alterações
                </PrimaryButton>
            </div>
        </form>

        <DocumentUploadModal :show="showUploadModal" @close="showUploadModal = false"
            @converted="handleConvertedHtml" />
    </AuthenticatedLayout>
</template>