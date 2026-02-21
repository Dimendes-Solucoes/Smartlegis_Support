<script setup lang="ts">
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import PrimaryButton from '@/components/Common/PrimaryButton.vue';
import BackButtonRow from '@/components/Common/BackButtonRow.vue';
import CategoryPartyForm from './CategoryPartyForm.vue';
import { getImageUrl } from '@/utils/image';

interface Party {
    id: number;
    name_party: string;
    logo: string | null;
}

const props = defineProps<{
    party: Party;
}>();

const form = useForm({
    _method: 'put',
    name_party: props.party.name_party,
    logo: null as File | null,
});

const existingLogoUrl = props.party.logo ? getImageUrl(props.party.logo) : null;

const submit = () => {
    form.post(route('category-parties.update', props.party.id), {
        forceFormData: true,
    });
};
</script>

<template>

    <Head title="Editar Partido Político" />

    <AuthenticatedLayout>
        <BackButtonRow :href="route('category-parties.index')" />

        <form @submit.prevent="submit" class="max-w-lg">
            <CategoryPartyForm :form="form" :existing-logo-url="existingLogoUrl" />

            <div class="flex items-center justify-end mt-6">
                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Salvar
                </PrimaryButton>
            </div>
        </form>
    </AuthenticatedLayout>
</template>