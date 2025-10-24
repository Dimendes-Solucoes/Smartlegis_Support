<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/Form/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';
import BackButtonRow from '@/Components/BackButtonRow.vue';

interface CommissionType {
    id: number;
    title: string;
}

const props = defineProps<{
    commissionTypes: CommissionType[];
}>();

const form = useForm({
    comission_name: '',
    type: '',
});

const submit = () => {
    form.post(route('commissions.store'));
};
</script>

<template>

    <Head title="Adicionar Comissão" />

    <AuthenticatedLayout>
        <BackButtonRow :href="route('commissions.index')" />

        <form @submit.prevent="submit">
            <div class="mb-4">
                <InputLabel for="comission_name" value="Nome da Comissão" />
                <TextInput id="comission_name" type="text" class="mt-1 block w-full" v-model="form.comission_name"
                    required autofocus autocomplete="off" />
                <InputError class="mt-2" :message="form.errors.comission_name" />
            </div>

            <div class="mb-4">
                <InputLabel value="Tipo de Comissão" />
                <div class="mt-2 flex space-x-4">
                    <div v-for="typeOption in props.commissionTypes" :key="typeOption.id" class="flex items-center">
                        <input :id="`type-${typeOption.id}`" type="radio" :value="typeOption.id" v-model="form.type"
                            class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800"
                            required />
                        <label :for="`type-${typeOption.id}`" class="ml-2 text-sm text-gray-600 dark:text-gray-400">
                            {{ typeOption.title }}
                        </label>
                    </div>
                </div>
                <InputError class="mt-2" :message="form.errors.type" />
            </div>

            <div class="flex items-center justify-end">
                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Salvar Comissão
                </PrimaryButton>
            </div>
        </form>
    </AuthenticatedLayout>
</template>