<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import { Head, useForm } from '@inertiajs/vue3';
import BackButtonRow from '@/Components/BackButtonRow.vue';

interface Category {
    id: number;
    name: string;
    abbreviation: string | null;
    min_protocol: number;
}

const props = defineProps<{
    category: Category;
}>();

const form = useForm({
    name: props.category.name,
    abbreviation: props.category.abbreviation || '',
    min_protocol: props.category.min_protocol
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
                <div class="flex gap-6">
                    <div class="w-1/2">
                        <InputLabel for="abbreviation" value="Abreviação" />
                        <TextInput id="abbreviation" type="text" class="mt-1 block w-full"
                            v-model="form.abbreviation" />
                        <InputError class="mt-2" :message="form.errors.abbreviation" />
                    </div>
                    <div class="w-1/2">
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