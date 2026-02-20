<script setup lang="ts">
import AuthenticatedLayout from "@/layouts/AuthenticatedLayout.vue";
import PrimaryButton from "@/components/Common/PrimaryButton.vue";
import InputLabel from "@/components/Form/InputLabel.vue";
import TextInput from "@/components/Form/TextInput.vue";
import InputError from "@/components/Form/InputError.vue";
import { Head, useForm } from "@inertiajs/vue3";
import BackButtonRow from "@/components/Common/BackButtonRow.vue";

const form = useForm({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
});

const submit = () => {
    form.post(route("admin.store"), {
        forceFormData: true,
        onFinish: () => {
            form.reset("password", "password_confirmation");
        },
    });
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
                    <TextInput
                        id="name"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.name"
                        required
                        autofocus
                    />
                    <InputError class="mt-2" :message="form.errors.name" />
                </div>

                <div class="flex-1">
                    <InputLabel for="email" value="Email" />
                    <TextInput
                        id="email"
                        type="email"
                        class="mt-1 block w-full"
                        v-model="form.email"
                    />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>
            </div>

            <div class="md:flex md:space-x-4 mb-4">
                <div class="flex-1 mb-4 md:mb-0">
                    <InputLabel for="password" value="Senha" />
                    <TextInput
                        id="password"
                        type="password"
                        class="mt-1 block w-full"
                        v-model="form.password"
                        required
                        autocomplete="new-password"
                    />
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>
                <div class="flex-1">
                    <InputLabel for="password_confirmation" value="Confirmar Senha" />
                    <TextInput
                        id="password_confirmation"
                        type="password"
                        class="mt-1 block w-full"
                        v-model="form.password_confirmation"
                        required
                        autocomplete="new-password"
                    />
                    <InputError
                        class="mt-2"
                        :message="form.errors.password_confirmation"
                    />
                </div>
            </div>

            <div class="flex items-center justify-end mt-6">
                <PrimaryButton
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Salvar
                </PrimaryButton>
            </div>
        </form>
    </AuthenticatedLayout>
</template>
