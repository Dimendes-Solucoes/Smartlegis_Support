<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/Common/PrimaryButton.vue';
import { Head, useForm } from '@inertiajs/vue3';

import ImageUploadWithCropper from '@/Components/User/ImageUploadWithCropper.vue';
import UserForm from '@/Components/User/UserForm.vue';
import BackButtonRow from '@/Components/Common/BackButtonRow.vue';
import CouncilorForm from './CouncilorForm.vue';

interface Category {
    id: number;
    name: string;
}

interface Party {
    id: number;
    name_party: string;
}

const props = defineProps<{
    categories: Category[];
    parties: Party[];
}>();

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    nickname: '',
    path_image: null as File | null,
    category_id: '',
    party_id: '',
    is_leader: false,
    is_first_secretary: false
});

const submit = () => {
    form.post(route('councilors.store'), {
        forceFormData: true,
        onFinish: () => {
            form.reset('password', 'password_confirmation');
        },
    });
};
</script>

<template>

    <Head title="Adicionar Vereador" />

    <AuthenticatedLayout>
        <BackButtonRow :href="route('councilors.index')" />

        <form @submit.prevent="submit">
            <div class="mb-4">
                <ImageUploadWithCropper v-model="form.path_image" :error="form.errors.path_image" />
            </div>

            <UserForm :form="form" :categories="props.categories" :isCreating="true" />
            <CouncilorForm :form="form" :parties="props.parties" :isCreating="true" />

            <div class="flex items-center justify-end">
                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Salvar
                </PrimaryButton>
            </div>
        </form>
    </AuthenticatedLayout>
</template>