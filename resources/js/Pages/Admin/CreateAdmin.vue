<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('admin.store'), {
        forceFormData: true,
        onFinish: () => {
            form.reset('password', 'password_confirmation');
        },
    });
};
</script>

<template>

    <Head title="Adicionar Administrador" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <form @submit.prevent="submit">
                            <div class="md:flex md:space-x-4 mb-4">
                                <div class="flex-1 mb-4 md:mb-0">
                                    <InputLabel for="name" value="Nome" />
                                    <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name"
                                        required autofocus />
                                    <InputError class="mt-2" :message="form.errors.name" />
                                </div>

                                <div class="flex-1">
                                    <InputLabel for="email" value="Email" />
                                    <TextInput id="email" type="email" class="mt-1 block w-full" v-model="form.email" />
                                    <InputError class="mt-2" :message="form.errors.email" />
                                </div>
                            </div>

                            <div class="md:flex md:space-x-4 mb-4">
                                <div class="flex-1 mb-4 md:mb-0">
                                    <InputLabel for="password" value="Senha" />
                                    <TextInput id="password" type="password" class="mt-1 block w-full"
                                        v-model="form.password" required autocomplete="new-password" />
                                    <InputError class="mt-2" :message="form.errors.password" />
                                </div>
                                <div class="flex-1">
                                    <InputLabel for="password_confirmation" value="Confirmar Senha" />
                                    <TextInput id="password_confirmation" type="password" class="mt-1 block w-full"
                                        v-model="form.password_confirmation" required autocomplete="new-password" />
                                    <InputError class="mt-2" :message="form.errors.password_confirmation" />
                                </div>
                            </div>

                            <div class="flex items-center justify-end mt-6">
                                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                    Salvar
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>