<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import TextButton from '@/Components/Itens/TextButton.vue';
import { Head } from '@inertiajs/vue3';
import IconButton from '@/Components/Itens/IconButton.vue';
import { PencilSquareIcon } from '@heroicons/vue/24/solid';

interface Commission {
    id: number;
    comission_name: string;
    type_description: string;
}

const props = defineProps<{
    commissions: Commission[];
    selectedTenantId: string | null;
}>();
</script>

<template>

    <Head title="Comissões" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="flex justify-end mb-4">
                            <TextButton :href="route('commissions.create')">
                                Nova Comissão
                            </TextButton>
                        </div>

                        <div v-if="props.commissions.length > 0" class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col"
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                            Comissão</th>
                                        <th scope="col"
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                            Tipo</th>
                                        <th scope="col"
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                            Ações</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                    <tr v-for="commission in props.commissions" :key="commission.id">
                                        <td class="px-4 py-4 whitespace-nowrap">{{ commission.comission_name }}</td>
                                        <td class="px-4 py-4 whitespace-nowrap">{{ commission.type_description }}</td>
                                        <td class="px-4 py-4 whitespace-nowrap text-left text-sm font-medium">
                                            <IconButton :href="route('commissions.edit', commission.id)" color="yellow"
                                                title="Editar">
                                                <PencilSquareIcon class="h-5 w-5" />
                                            </IconButton>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div v-else>
                            <p>Nenhuma comissão encontrada.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>