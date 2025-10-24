<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/Common/PrimaryButton.vue';
import InputLabel from '@/Components/Form/InputLabel.vue';
import TextInput from '@/Components/Form/TextInput.vue';
import InputError from '@/Components/Form/InputError.vue';
import { Head, useForm } from '@inertiajs/vue3';
import BackButtonRow from '@/Components/Common/BackButtonRow.vue';

interface Category {
    id: number;
    name: string;
    abbreviation: string | null;
    min_protocol: number;
    order: number;
}

const props = defineProps<{
    category: Category;
}>();

const form = useForm({
    name: props.category.name,
    abbreviation: props.category.abbreviation || '',
    min_protocol: props.category.min_protocol,
    order: props.category.order || 0
});

const submit = () => {
    form.put(route('document-categories.update', props.category.id));
};
</script>

<template>

    <Head :title="`Editar Categoria: ${props.category.name}`" />

    <AuthenticatedLayout>
        <BackButtonRow :href="route('document-categories.index')" />

        <form @submit.prevent="submit">
            <div class="space-y-6">
                <div>
                    <InputLabel for="name" value="Nome da Categoria" />
                    <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" required autofocus />
                    <InputError class="mt-2" :message="form.errors.name" />
                </div>

                <div class="flex flex-col sm:flex-row sm:gap-4">
                    <div class="w-full sm:w-1/3">
                        <InputLabel for="abbreviation" value="Abreviação" />
                        <TextInput id="abbreviation" type="text" class="mt-1 block w-full"
                            v-model="form.abbreviation" />
                        <InputError class="mt-2" :message="form.errors.abbreviation" />
                    </div>

                    <div class="mt-4 sm:mt-0 w-full sm:w-1/3">
                        <InputLabel for="order" value="Ordem" />
                        <TextInput id="order" type="number" class="mt-1 block w-full" v-model.number="form.order" />
                        <InputError class="mt-2" :message="form.errors.order" />
                    </div>

                    <div class="mt-4 sm:mt-0 w-full sm:w-1/3">
                        <InputLabel for="min_protocol" value="Protocolo Mínimo" />
                        <TextInput id="min_protocol" type="number" class="mt-1 block w-full"
                            v-model.number="form.min_protocol" />
                        <InputError class="mt-2" :message="form.errors.min_protocol" />
                    </div>
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