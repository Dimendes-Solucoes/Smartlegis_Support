<script setup lang="ts">
import { computed } from 'vue';
import { useForm, Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';

// Componentes Comuns
import BackButton from '@/components/Common/BackButton.vue';
import PrimaryButton from '@/components/Common/PrimaryButton.vue';

// Componentes de Formulário
import InputLabel from '@/components/Form/InputLabel.vue';
import TextInput from '@/components/Form/TextInput.vue';
import TextareaInput from '@/components/Form/TextareaInput.vue';
import SelectInput from '@/components/Form/SelectInput.vue';
import InputError from '@/components/Form/InputError.vue';

interface Props {
    documentModel: {
        id: number;
        title: string;
        body: string | null;
        user_id: number;
        document_category_id: number;
    };
    categories: { id: number; name: string }[];
    users: { id: number; name: string }[];
}

const props = defineProps<Props>();

const form = useForm({
    title: props.documentModel.title,
    document_category_id: props.documentModel.document_category_id,
    user_id: props.documentModel.user_id,
    body: props.documentModel.body || '',
});

// Transformando os dados para o formato que o SelectInput geralmente espera (label/value)
const categoryOptions = computed(() => 
    props.categories.map(c => ({ label: c.name, value: c.id }))
);

const userOptions = computed(() => 
    props.users.map(u => ({ label: u.name, value: u.id }))
);

const submit = () => {
    form.put(route('document-models.update', props.documentModel.id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Editar Modelo de Documento" />

    <AuthenticatedLayout>     
            <div class="flex justify-between items-center mb-6">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Editar Modelo
                </h2>
                
                <BackButton :href="route('document-models.index')">
                    Voltar
                </BackButton>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    <form @submit.prevent="submit" class="space-y-6">
                        
                        <div>
                            <InputLabel for="title" value="Título do Modelo" />
                            <TextInput
                                id="title"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.title"
                                required
                                autofocus
                            />
                            <InputError class="mt-2" :message="form.errors.title" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <InputLabel for="document_category_id" value="Categoria" />
                                <SelectInput
                                    id="document_category_id"
                                    v-model="form.document_category_id"
                                    :options="categoryOptions"
                                    class="mt-1 block w-full"
                                    placeholder="Selecione uma categoria"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.document_category_id" />
                            </div>

                            <div>
                                <InputLabel for="user_id" value="Autor / Usuário" />
                                <SelectInput
                                    id="user_id"
                                    v-model="form.user_id"
                                    :options="userOptions"
                                    class="mt-1 block w-full"
                                    placeholder="Selecione um usuário"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.user_id" />
                            </div>
                        </div>

                        <div>
                            <InputLabel for="body" value="Conteúdo do Modelo" />
                            <TextareaInput
                                id="body"
                                v-model="form.body"
                                rows="15"
                                class="mt-1 block w-full"
                                placeholder="Digite aqui o texto padrão deste modelo..."
                            />
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Este texto servirá de base quando novos documentos forem criados a partir deste modelo.
                            </p>
                            <InputError class="mt-2" :message="form.errors.body" />
                        </div>

                        <div class="flex items-center justify-end gap-4">
                            <BackButton :href="route('document-models.index')" class="bg-transparent border-transparent text-gray-600 hover:bg-gray-100 shadow-none">
                                Cancelar
                            </BackButton>
                            
                            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                Salvar Alterações
                            </PrimaryButton>
                        </div>

                    </form>
                </div>
            </div>
    </AuthenticatedLayout>
</template>