<script setup lang="ts">
import { ref, watch } from "vue";
import AuthenticatedLayout from "@/layouts/AuthenticatedLayout.vue";
import PrimaryButton from "@/components/Common/PrimaryButton.vue";
import InputLabel from "@/components/Form/InputLabel.vue";
import InputError from "@/components/Form/InputError.vue";
import { Head, useForm } from "@inertiajs/vue3";
import ImageUploadWithCropper from "@/components/User/ImageUploadWithCropper.vue";
import UserForm from "@/components/User/UserForm.vue";
import BackButtonRow from "@/components/Common/BackButtonRow.vue";
import SelectInput from "@/components/Form/SelectInput.vue";

interface Category {
    id: number;
    name: string;
}

interface Mayor {
    id: number;
    name: string;
    email: string;
}

const props = defineProps<{
    categories: Category[];
    mayors: Mayor[];
}>();

const selectedOption = ref<"new" | "existing">(props.mayors.length > 0 ? "new" : "new");

const newUserForm = useForm({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
    nickname: "",
    path_image: null as File | null,
    category_id: "",
});

const existingMayorForm = useForm({
    mayor_id: "",
});

watch(selectedOption, (newOption) => {
    if (newOption === "new") {
        existingMayorForm.reset();
    } else {
        newUserForm.reset();
        newUserForm.path_image = null;
    }
});

const submit = () => {
    if (selectedOption.value === "new") {
        newUserForm.post(route("users.store"), {
            forceFormData: true,
            onFinish: () => {
                newUserForm.reset("password", "password_confirmation");
            },
        });
    } else {
        existingMayorForm.post(route("users.save_mayor"), {
            onSuccess: () => {
                existingMayorForm.reset();
            },
        });
    }
};
</script>

<template>
    <Head title="Trocar Prefeito" />

    <AuthenticatedLayout>
        <BackButtonRow :href="route('users.index')" />

        <form @submit.prevent="submit">
            <div class="mb-6" v-if="props.mayors.length > 0">
                <div class="flex space-x-4">
                    <label class="inline-flex items-center">
                        <input
                            type="radio"
                            v-model="selectedOption"
                            value="new"
                            class="form-radio text-indigo-600 dark:text-indigo-400"
                        />
                        <span class="ml-2 text-gray-700 dark:text-gray-300"
                            >Novo Prefeito</span
                        >
                    </label>
                    <label class="inline-flex items-center">
                        <input
                            type="radio"
                            v-model="selectedOption"
                            value="existing"
                            class="form-radio text-indigo-600 dark:text-indigo-400"
                        />
                        <span class="ml-2 text-gray-700 dark:text-gray-300"
                            >Reativar Prefeito Existente</span
                        >
                    </label>
                </div>
            </div>
            <div class="mb-6 text-gray-700 dark:text-gray-300" v-else>
                <p>Informe os dados do novo prefeito</p>
            </div>

            <div v-if="selectedOption === 'new' || props.mayors.length === 0">
                <div class="mb-4">
                    <ImageUploadWithCropper
                        v-model="newUserForm.path_image"
                        :error="newUserForm.errors.path_image"
                    />
                </div>
                <UserForm
                    :form="newUserForm"
                    :categories="props.categories"
                    :isCreating="true"
                />
            </div>

            <div v-if="selectedOption === 'existing' && props.mayors.length > 0">
                <div class="mb-4">
                    <InputLabel for="mayor_id" value="Prefeito Inativo" />
                    <SelectInput
                        id="mayor_id"
                        v-model="existingMayorForm.mayor_id"
                        :options="props.mayors"
                        value-key="id"
                        :format-label="(mayor: Mayor) => `${mayor.name} (${mayor.email})`"
                        placeholder="Selecione um prefeito inativo"
                    />
                    <InputError
                        class="mt-2"
                        :message="existingMayorForm.errors.mayor_id"
                    />
                </div>
            </div>

            <div class="flex items-center justify-end mt-4">
                <PrimaryButton
                    :class="{
                        'opacity-25':
                            newUserForm.processing || existingMayorForm.processing,
                    }"
                    :disabled="newUserForm.processing || existingMayorForm.processing"
                >
                    {{
                        selectedOption === "new" || props.mayors.length === 0
                            ? "Salvar Novo Prefeito"
                            : "Ativar Prefeito"
                    }}
                </PrimaryButton>
            </div>
        </form>
    </AuthenticatedLayout>
</template>
