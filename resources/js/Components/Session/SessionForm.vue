<script setup lang="ts">
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import { useForm } from '@inertiajs/vue3';

interface Status {
    id: number;
    name: string;
}

defineProps<{
    form: ReturnType<typeof useForm>;
    statuses: Status[];
}>();
</script>

<template>
    <div class="space-y-6">
        <div>
            <InputLabel for="name" value="Nome da Sessão" />
            <TextInput 
                id="name" 
                type="text" 
                class="mt-1 block w-full" 
                v-model="form.name"
                required 
                autofocus />
            <InputError class="mt-2" :message="form.errors.name" />
        </div>

        <div>
            <InputLabel for="datetime_start" value="Data e Hora de Início" />
            <input 
                id="datetime_start" 
                type="datetime-local"
                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                v-model="form.datetime_start" 
                required />
            <InputError class="mt-2" :message="form.errors.datetime_start" />
        </div>

        <div>
            <InputLabel for="session_status_id" value="Status da Sessão" />
            <select 
                id="session_status_id"
                v-model="form.session_status_id"
                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
            >
                <option v-for="status in statuses" :key="status.id" :value="status.id">
                    {{ status.name }}
                </option>
            </select>
            <InputError class="mt-2" :message="form.errors.session_status_id" />
        </div>
    </div>
</template>