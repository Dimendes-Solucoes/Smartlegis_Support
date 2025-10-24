<script setup lang="ts">
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import PrimaryButton from '@/components/Common/PrimaryButton.vue';
import ImageUploadWithCropper from '@/components/User/ImageUploadWithCropper.vue';
import UserForm from '@/components/User/UserForm.vue';
import BackButtonRow from '@/components/Common/BackButtonRow.vue';
import CouncilorForm from './CouncilorForm.vue';

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
        <BackButtonRow :href="route('councilors.index')" />

        <form @submit.prevent="submit">
            <div class="mb-4">
                <ImageUploadWithCropper v-model="form.path_image" :error="form.errors.path_image"
                    :initial-image-url="form.existing_path_image" />
                <input type="hidden" name="existing_path_image" :value="form.existing_path_image" />
            </div>

            <UserForm :form="form" :categories="props.categories" />
            <CouncilorForm :form="form" :parties="props.parties" />

            <div class="flex items-center justify-end">
                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Salvar
                </PrimaryButton>
            </div>
        </form>
    </AuthenticatedLayout>
</template>