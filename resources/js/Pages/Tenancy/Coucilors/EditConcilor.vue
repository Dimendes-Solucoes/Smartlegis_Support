<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

import PrimaryButton from '@/Components/PrimaryButton.vue';
import ImageUploadWithCropper from '@/Components/User/ImageUploadWithCropper.vue';
import UserForm from '@/Components/User/UserForm.vue';
import ConcilorForm from '@/Components/User/ConcilorForm.vue';

interface Category {
    id: number;
    name: string;
}

interface Party {
    id: number;
    name_party: string;
}

interface User {
    id: number;
    name: string;
    email: string;
    path_image: string | null;
    nickname: string | null;
    user_category_id: number;
    category_party_id: number;
    status_lider: boolean | null;
    is_first_secretary: boolean | null;
}

const props = defineProps<{
    user: User;
    categories: Category[];
    parties: Party[];
}>();

const form = useForm({
    _method: 'put',
    name: props.user.name,
    email: props.user.email,
    password: '',
    password_confirmation: '',
    nickname: props.user.nickname,
    path_image: null as File | null,
    existing_path_image: props.user.path_image,
    category_id: props.user.user_category_id,
    party_id: props.user.category_party_id,
    is_leader: props.user.status_lider ?? null,
    is_first_secretary: props.user.is_first_secretary ?? false
});

const submit = () => {
    form.post(route('councilors.update', props.user.id), {
        forceFormData: true,
        onFinish: () => {
            form.reset('password', 'password_confirmation');
        },
    });
};
</script>

<template>

    <Head title="Editar Vereador" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <form @submit.prevent="submit">
                            <div class="mb-4">
                                <ImageUploadWithCropper v-model="form.path_image" :error="form.errors.path_image"
                                    :initial-image-url="form.existing_path_image" />
                                <input type="hidden" name="existing_path_image" :value="form.existing_path_image" />
                            </div>

                            <UserForm :form="form" :categories="props.categories" />
                            <ConcilorForm :form="form" :parties="props.parties" />

                            <div class="flex items-center justify-end">
                                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                    Salvar
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>