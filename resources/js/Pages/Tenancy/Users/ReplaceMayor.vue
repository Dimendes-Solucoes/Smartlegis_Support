<script setup lang="ts">
import { ref, watch } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import { Head, useForm } from '@inertiajs/vue3';

import ImageUploadWithCropper from '@/Components/User/ImageUploadWithCropper.vue';
import UserForm from '@/Components/User/UserForm.vue';

interface Category {
    id: number;
    name: string;
}

interface Mayor {
    id: number;
    name: string;
    email: string;
}

const props = defineProps<{
    categories: Category[];
    mayors: Mayor[];
}>();

const selectedOption = ref<'new' | 'existing'>(props.mayors.length > 0 ? 'new' : 'new');

const newUserForm = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    nickname: '',
    path_image: null as File | null,
    category_id: '',
});

const existingMayorForm = useForm({
    mayor_id: '',
});

watch(selectedOption, (newOption) => {
    if (newOption === 'new') {
        existingMayorForm.reset();
    } else {
        newUserForm.reset();
        newUserForm.path_image = null;
    }
});

const submit = () => {
    if (selectedOption.value === 'new') {
        newUserForm.post(route('users.store'), {
            forceFormData: true,
            onFinish: () => {
                newUserForm.reset('password', 'password_confirmation');
            },
        });
    } else {
        existingMayorForm.post(route('users.save_mayor'), {
            onSuccess: () => {
                existingMayorForm.reset();
            },
        });
    }
};
</script>

<template>

    <Head title="Trocar Prefeito" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Trocar Prefeito</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <form @submit.prevent="submit">
                            <div class="mb-6" v-if="props.mayors.length > 0">
                                <div class="flex space-x-4">
                                    <label class="inline-flex items-center">
                                        <input type="radio" v-model="selectedOption" value="new"
                                            class="form-radio text-indigo-600 dark:text-indigo-400" />
                                        <span class="ml-2 text-gray-700 dark:text-gray-300">Novo Prefeito</span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input type="radio" v-model="selectedOption" value="existing"
                                            class="form-radio text-indigo-600 dark:text-indigo-400" />
                                        <span class="ml-2 text-gray-700 dark:text-gray-300">Reativar Prefeito
                                            Existente</span>
                                    </label>
                                </div>
                            </div>
                            <div class="mb-6 text-gray-700 dark:text-gray-300" v-else>
                                <p>Informe os dados do novo prefeito</p>
                            </div>

                            <div v-if="selectedOption === 'new' || props.mayors.length === 0">
                                <div class="mb-4">
                                    <ImageUploadWithCropper v-model="newUserForm.path_image"
                                        :error="newUserForm.errors.path_image" />
                                </div>
                                <UserForm :form="newUserForm" :categories="props.categories" :isCreating="true" />
                            </div>

                            <div v-if="selectedOption === 'existing' && props.mayors.length > 0">
                                <div class="mb-4">
                                    <InputLabel for="mayor_id" value="Prefeito Inativo" />
                                    <select id="mayor_id" v-model="existingMayorForm.mayor_id"
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                        <option value="">Selecione um prefeito inativo</option>
                                        <option v-for="mayor in props.mayors" :key="mayor.id" :value="mayor.id">
                                            {{ mayor.name }} ({{ mayor.email }})
                                        </option>
                                    </select>
                                    <InputError class="mt-2" :message="existingMayorForm.errors.mayor_id" />
                                </div>
                            </div>

                            <div class="flex items-center justify-end mt-4">
                                <PrimaryButton
                                    :class="{ 'opacity-25': newUserForm.processing || existingMayorForm.processing }"
                                    :disabled="newUserForm.processing || existingMayorForm.processing">
                                    {{ selectedOption === 'new' || props.mayors.length === 0 ? 'Salvar Novo Prefeito'
                                        :
                                        'Ativar Prefeito' }}
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>