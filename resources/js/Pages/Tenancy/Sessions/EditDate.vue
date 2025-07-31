<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { format } from 'date-fns';

interface Session {
    id: number;
    name: string;
    datetime_start: string;
}

const props = defineProps<{
    session: Session;
}>();

const formatForInput = (dateString: string) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    date.setMinutes(date.getMinutes() - date.getTimezoneOffset());
    return format(date, "yyyy-MM-dd'T'HH:mm");
};

const form = useForm({
    datetime_start: formatForInput(props.session.datetime_start),
});

const submit = () => {
    form.put(route('sessions.update_date', props.session.id));
};
</script>

<template>
    <Head title="Editar Data da Sessão" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h2 class="text-xl font-semibold mb-4">Editar Data da Sessão: {{ props.session.name }}</h2>
                        <form @submit.prevent="submit">
                            <div>
                                <InputLabel for="datetime_start" value="Nova Data e Hora de Início" />
                                <input 
                                    id="datetime_start" 
                                    type="datetime-local"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                    v-model="form.datetime_start" 
                                    required />
                                <InputError class="mt-2" :message="form.errors.datetime_start" />
                            </div>

                            <div class="flex items-center justify-end mt-6">
                                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                    Salvar Data
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>