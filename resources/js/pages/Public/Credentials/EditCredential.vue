<script setup lang="ts">
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/components/Common/PrimaryButton.vue';
import { Head, useForm } from '@inertiajs/vue3';
import BackButtonRow from '@/components/Common/BackButtonRow.vue';
import CredentialForm from './CredentialForm.vue';

interface Credential {
    id: number;
    tenant_id: string;
    short_name: string;
    channel: string;
    host: string;
    city_name: string | null;
    state_name: string | null;
    address: string | null;
    zip_code: string | null;
    phone: string | null;
    cnpj: string | null;
    city_shield: string | null;
    tenant: { city_name: string } | null;
}

const props = defineProps<{
    data: {
        credential: Credential;
    }
}>();

const form = useForm({
    _method: 'put',
    short_name: props.data.credential.short_name,
    channel: props.data.credential.channel,
    host: props.data.credential.host,
    key: '',
    city_name: props.data.credential.city_name,
    state_name: props.data.credential.state_name,
    address: props.data.credential.address,
    zip_code: props.data.credential.zip_code,
    phone: props.data.credential.phone,
    cnpj: props.data.credential.cnpj,
    city_shield: null as File | null,
    existing_city_shield: props.data.credential.city_shield,
    tenant: props.data.credential.tenant,
});

const submit = () => {
    form.post(route('credentials.update', props.data.credential.id));
};
</script>

<template>

    <Head title="Editar Credencial" />

    <AuthenticatedLayout>
        <BackButtonRow :href="route('credentials.index')" />

        <form @submit.prevent="submit" class="mt-6">
            <CredentialForm :form="form" :is-editing="true" />
            <div class="flex items-center justify-end mt-6">
                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Salvar Alterações
                </PrimaryButton>
            </div>
        </form>
    </AuthenticatedLayout>
</template>