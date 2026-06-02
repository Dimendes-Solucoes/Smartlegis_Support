<script setup lang="ts">
import IconButton from '@/components/Itens/IconButton.vue';
import TextButton from '@/components/Itens/TextButton.vue';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import { PencilSquareIcon } from '@heroicons/vue/24/outline';
import { Head } from '@inertiajs/vue3';

interface Mandate {
    id: number;
    title: string;
    start_at: string;
    end_at: string;
    is_current: boolean;
}

const props = defineProps<{ mandates: Mandate[] }>();
</script>

<template>

    <Head title="Mandatos" />
    <AuthenticatedLayout>
        <div class="flex justify-end items-center mb-4">
            <TextButton :href="route('mandates.create')" class="p-4 justify-center text-center">
                Novo Mandato
            </TextButton>
        </div>

        <div v-if="props.mandates.length > 0" class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                            Título</th>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                            Início</th>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                            Fim</th>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                            Atual</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                    <tr v-for="mandate in props.mandates" :key="mandate.id">
                        <td class="px-4 py-4 whitespace-nowrap">{{ mandate.title }}</td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            {{ new Date(mandate.start_at).toLocaleDateString('pt-BR') }}
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            {{ new Date(mandate.end_at).toLocaleDateString('pt-BR') }}
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <span :class="mandate.is_current
                                ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300'
                                : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'"
                                class="px-2 py-1 rounded-full text-xs font-medium">
                                {{ mandate.is_current ? 'Sim' : 'Não' }}
                            </span>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-left text-sm font-medium">
                            <IconButton :href="route('mandates.edit', mandate.id)" color="yellow" title="Editar">
                                <PencilSquareIcon class="h-5 w-5" />
                            </IconButton>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div v-else>
            <p>Nenhum mandato encontrado.</p>
        </div>
    </AuthenticatedLayout>
</template>
