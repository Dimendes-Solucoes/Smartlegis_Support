<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/Common/PrimaryButton.vue';
import InputLabel from '@/Components/Form/InputLabel.vue';
import TextInput from '@/Components/Form/TextInput.vue';
import InputError from '@/Components/Form/InputError.vue';
import { Head, useForm } from '@inertiajs/vue3';
import BackButtonRow from '@/Components/Common/BackButtonRow.vue';

interface Admin {
    id: number;
    name: string;
    email: string;
}

const props = defineProps<{
    admin: Admin;
}>();

const form = useForm({
    name: props.admin.name,
    email: props.admin.email
});

const submit = () => {
    form.put(route('admin.update', props.admin.id));
};
</script>

<template>

    <Head title="Adicionar Administrador" />

    <AuthenticatedLayout>
        <BackButtonRow :href="route('users.index')" />

        <form @submit.prevent="submit">
            <div class="md:flex md:space-x-4 mb-4">
                <div class="flex-1 mb-4 md:mb-0">
                    <InputLabel for="name" value="Nome" />
                    <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" required autofocus />
                    <InputError class="mt-2" :message="form.errors.name" />
                </div>

                <div class="flex-1">
                    <InputLabel for="email" value="Email" />
                    <TextInput id="email" type="email" class="mt-1 block w-full" v-model="form.email" />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>
            </div>

            <div class="flex items-center justify-end mt-6">
                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Salvar
                </PrimaryButton>
            </div>
        </form>
    </AuthenticatedLayout>
</template>