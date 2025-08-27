<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, useForm } from '@inertiajs/vue3';
import ImageUploadWithCropper from '@/Components/User/ImageUploadWithCropper.vue';
import UserForm from '@/Components/User/UserForm.vue';
import BackButtonRow from '@/Components/BackButtonRow.vue';

interface Category {
    id: number;
    name: string;
}

interface User {
    id: number;
    name: string;
    email: string;
    path_image: string | null;
    nickname: string | null;
    user_category_id: number | string;
}

const props = defineProps<{
    user: User;
    categories: Category[];
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
});

const submit = () => {
    form.post(route('users.update', props.user.id), {
        forceFormData: true,
        onSuccess: () => {
            form.reset('password', 'password_confirmation');
        },
    });
};
</script>

<template>

    <Head :title="`Editar Usuário: ${props.user.name}`" />

    <AuthenticatedLayout>
        <BackButtonRow :href="route('users.index')" />

        <form @submit.prevent="submit">
            <div class="mb-4">
                <ImageUploadWithCropper v-model="form.path_image" :error="form.errors.path_image"
                    :initial-image-url="form.existing_path_image" />
                <input type="hidden" name="existing_path_image" :value="form.existing_path_image" />
            </div>

            <UserForm :form="form" :categories="props.categories" />

            <div class="flex items-center justify-end mt-4">
                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Atualizar Usuário
                </PrimaryButton>
            </div>
        </form>
    </AuthenticatedLayout>
</template>