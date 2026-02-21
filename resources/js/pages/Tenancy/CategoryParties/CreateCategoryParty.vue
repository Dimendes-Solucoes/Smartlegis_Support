<script setup lang="ts">
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import PrimaryButton from '@/components/Common/PrimaryButton.vue';
import BackButtonRow from '@/components/Common/BackButtonRow.vue';
import CategoryPartyForm from './CategoryPartyForm.vue';

const form = useForm({
    name_party: '',
    logo: null as File | null,
});

const submit = () => {
    form.post(route('category-parties.store'), {
        forceFormData: true,
    });
};
</script>

<template>

    <Head title="Novo Partido Político" />

    <AuthenticatedLayout>
        <BackButtonRow :href="route('category-parties.index')" />

        <form @submit.prevent="submit" class="max-w-lg">
            <CategoryPartyForm :form="form" />

            <div class="flex items-center justify-end mt-6">
                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Salvar
                </PrimaryButton>
            </div>
        </form>
    </AuthenticatedLayout>
</template>