<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, useForm } from '@inertiajs/vue3';

import ImageUploadWithCropper from '@/Components/User/ImageUploadWithCropper.vue';
import UserForm from '@/Components/User/UserForm.vue';

interface Category {
    id: number;
    name: string;
}

const props = defineProps<{
    categories: Category[];
}>();

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    nickname: '',
    path_image: null as File | null,
    category_id: '',
});

const submit = () => {
    form.post(route('users.store'), {
        forceFormData: true,
        onFinish: () => {
            form.reset('password', 'password_confirmation');
        },
    });
};
</script>

<template>

    <Head title="Adicionar Usuário" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <form @submit.prevent="submit">
                            <div class="mb-4">
                                <ImageUploadWithCropper v-model="form.path_image" :error="form.errors.path_image" />
                            </div>

                            <UserForm :form="form" :categories="props.categories" :isCreating="true" />

                            <div class="flex items-center justify-end">
                                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                    Adicionar Usuário
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>