<script setup lang="ts">
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/components/Common/PrimaryButton.vue';
import { Head, useForm } from '@inertiajs/vue3';

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

interface Mandate {
    id: number;
    title: string;
    start_at: string;
    end_at: string;
    is_current: boolean;
}

const props = defineProps<{
    categories: Category[];
    parties: Party[];
    mandates: Mandate[];
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
    is_first_secretary: false,
    is_vereador_active: true,
    mandate_id: '' as string | number,
    birthdate: '',
    summary: '',
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
            <CouncilorForm :form="form" :parties="props.parties" :mandates="props.mandates" :isCreating="true" />

            <div class="flex items-center justify-end border-t border-gray-200 dark:border-gray-700 pt-6 mt-6">
                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Salvar
                </PrimaryButton>
            </div>
        </form>
    </AuthenticatedLayout>
</template>
