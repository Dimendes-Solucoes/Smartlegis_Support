<script setup lang="ts">
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/components/Common/PrimaryButton.vue';
import InputError from '@/components/Form/InputError.vue';
import InputLabel from '@/components/Form/InputLabel.vue';
import TextInput from '@/components/Form/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';
import BackButtonRow from '@/components/Common/BackButtonRow.vue';

const form = useForm({
    title: '',
    start_at: '',
    end_at: '',
    is_current: false,
});

const submit = () => form.post(route('legislatures.store'));
</script>

<template>

    <Head title="Adicionar Legislatura" />
    <AuthenticatedLayout>
        <BackButtonRow :href="route('legislatures.index')" />

        <form @submit.prevent="submit">
            <div class="mb-4">
                <InputLabel for="title" value="Título" />
                <TextInput id="title" type="text" class="mt-1 block w-full" v-model="form.title" required autofocus />
                <InputError class="mt-2" :message="form.errors.title" />
            </div>

            <div class="mb-4 flex space-x-4">
                <div class="flex-1">
                    <InputLabel for="start_at" value="Início" />
                    <TextInput id="start_at" type="date" class="mt-1 block w-full" v-model="form.start_at" required />
                    <InputError class="mt-2" :message="form.errors.start_at" />
                </div>
                <div class="flex-1">
                    <InputLabel for="end_at" value="Fim" />
                    <TextInput id="end_at" type="date" class="mt-1 block w-full" v-model="form.end_at" required />
                    <InputError class="mt-2" :message="form.errors.end_at" />
                </div>
            </div>

            <div class="mb-4 flex items-center space-x-2">
                <input id="is_current" type="checkbox" v-model="form.is_current"
                    class="rounded border-gray-300 dark:border-gray-700 text-indigo-600" />
                <InputLabel for="is_current" value="Legislatura atual?" />
            </div>

            <div class="flex items-center justify-end">
                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Salvar Legislatura
                </PrimaryButton>
            </div>
        </form>
    </AuthenticatedLayout>
</template>