<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, useForm } from '@inertiajs/vue3';
import BackButtonRow from '@/Components/BackButtonRow.vue';
import CredentialForm from './CredentialForm.vue';

interface Tenant {
    id: string;
    city_name: string;
}

const props = defineProps<{
    formData: {
        tenants: Tenant[];
    }
}>();

const form = useForm({
    tenant_id: '',
    short_name: '',
    channel: '',
    host: '',
    key: '',
    city_name: '',
    city_shield: null as File | null,
});

const submit = () => {
    form.post(route('credentials.store'));
};
</script>

<template>
    <Head title="Nova Credencial" />
    <AuthenticatedLayout>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <BackButtonRow :href="route('credentials.index')" />
                        <h2 class="text-xl font-semibold mt-4">Cadastrar Nova Credencial</h2>
                        <form @submit.prevent="submit" class="mt-6">
                            <CredentialForm :form="form" :tenants="props.formData.tenants" />
                            <div class="flex items-center justify-end mt-6">
                                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                    Salvar Credencial
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
    </AuthenticatedLayout>
</template>