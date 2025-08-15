<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { format } from 'date-fns';
import TextInput from '@/Components/TextInput.vue';

interface Session {
    id: number;
    name: string;
    datetime_start: string;
    session_status_id: number;
}

interface SessionStatus {
    id: number;
    name: string;
}

const props = defineProps<{
    session: Session;
    session_statuses: SessionStatus[];
}>();

const formatForInput = (dateString: string) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    date.setMinutes(date.getMinutes() - date.getTimezoneOffset());
    return format(date, "yyyy-MM-dd");
};

const form = useForm({
    name: props.session.name,
    datetime_start: formatForInput(props.session.datetime_start),
    session_status_id: props.session.session_status_id
});

const submit = () => {
    form.put(route('sessions.update', props.session.id));
};
</script>

<template>

    <Head title="Editar Sessão" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <form @submit.prevent="submit">
                            <div class="mb-4">
                                <InputLabel for="name" value="Nome da Sessão" />
                                <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" required
                                    autofocus autocomplete="name" />
                                <InputError class="mt-2" :message="form.errors.name" />
                            </div>

                            <div class="flex gap-6">
                                <div class="w-1/2">
                                    <InputLabel for="datetime_start" value="Data de Início" />
                                    <input id="datetime_start" type="date"
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                        v-model="form.datetime_start" required />
                                    <InputError class="mt-2" :message="form.errors.datetime_start" />
                                </div>

                                <div class="w-1/2">
                                    <InputLabel for="session_status_id" value="Status" />
                                    <select id="session_status_id" v-model="form.session_status_id"
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                        <option value="">Selecione o status</option>
                                        <option v-for="session_status in session_statuses" :key="session_status.id" :value="session_status.id">
                                            {{ session_status.name }}
                                        </option>
                                    </select>
                                    <InputError class="mt-2" :message="form.errors.session_status_id" />
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