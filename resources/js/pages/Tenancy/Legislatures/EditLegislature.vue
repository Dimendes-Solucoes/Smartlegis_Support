<script setup lang="ts">
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/components/Common/PrimaryButton.vue';
import InputError from '@/components/Form/InputError.vue';
import InputLabel from '@/components/Form/InputLabel.vue';
import TextInput from '@/components/Form/TextInput.vue';
import BackButtonRow from '@/components/Common/BackButtonRow.vue';
import { Head, useForm } from '@inertiajs/vue3';

interface Legislature {
    id: number;
    title: string;
    start_at: string;
    end_at: string;
    is_current: boolean;
}

const props = defineProps<{
    legislature: Legislature;
}>();

const legislatureForm = useForm({
    title: props.legislature.title,
    start_at: props.legislature.start_at,
    end_at: props.legislature.end_at,
    is_current: props.legislature.is_current,
});

const submitDetails = () => {
    legislatureForm.put(route('legislatures.update', props.legislature.id));
};
</script>

<template>

    <Head title="Editar Legislatura" />
    <AuthenticatedLayout>
        <BackButtonRow :href="route('legislatures.index')" />

        <form @submit.prevent="submitDetails" class="mb-8 p-4 border rounded-lg">
            <h3 class="text-md font-semibold mb-4">Detalhes da Legislatura</h3>

            <div class="mb-4">
                <InputLabel for="title" value="Título" />
                <TextInput id="title" type="text" class="mt-1 block w-full" v-model="legislatureForm.title" required />
                <InputError class="mt-2" :message="legislatureForm.errors.title" />
            </div>

            <div class="mb-4 flex space-x-4">
                <div class="flex-1">
                    <InputLabel for="start_at" value="Início" />
                    <TextInput id="start_at" type="date" class="mt-1 block w-full" v-model="legislatureForm.start_at"
                        required />
                    <InputError class="mt-2" :message="legislatureForm.errors.start_at" />
                </div>
                <div class="flex-1">
                    <InputLabel for="end_at" value="Fim" />
                    <TextInput id="end_at" type="date" class="mt-1 block w-full" v-model="legislatureForm.end_at"
                        required />
                    <InputError class="mt-2" :message="legislatureForm.errors.end_at" />
                </div>
            </div>

            <div class="mb-4 flex items-center space-x-2">
                <input id="is_current" type="checkbox" v-model="legislatureForm.is_current"
                    class="rounded border-gray-300 dark:border-gray-700 text-indigo-600" />
                <InputLabel for="is_current" value="Legislatura atual?" />
            </div>

            <div class="flex justify-end">
                <PrimaryButton :class="{ 'opacity-25': legislatureForm.processing }"
                    :disabled="legislatureForm.processing">
                    Atualizar Detalhes
                </PrimaryButton>
            </div>
        </form>
    </AuthenticatedLayout>
</template>
