<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, useForm } from '@inertiajs/vue3';
import BackButtonRow from '@/Components/BackButtonRow.vue';
import CredentialForm from './CredentialForm.vue';

interface Credential {
    id: number;
    tenant_id: string;
    short_name: string;
    channel: string;
    host: string;
    city_name: string | null;
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
                        <h2 class="text-xl font-semibold mt-4">Editar Credencial: {{ data.credential.tenant?.city_name || data.credential.city_name }}</h2>
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