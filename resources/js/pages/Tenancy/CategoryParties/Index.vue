<script setup lang="ts">
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { getImageUrl } from '@/utils/image';
import { PencilSquareIcon, TrashIcon } from '@heroicons/vue/24/outline';
import TextButton from '@/components/Itens/TextButton.vue';
import IconButton from '@/components/Itens/IconButton.vue';

interface Party {
    id: number;
    name_party: string;
    logo: string | null;
}

const props = defineProps<{
    parties: Party[];
}>();

const getImage = (path: string): string => getImageUrl(path);

const destroy = (id: number): void => {
    if (confirm('Tem certeza que deseja remover este partido?')) {
        router.delete(route('category-parties.destroy', id), {
            preserveScroll: true,
        });
    }
};
</script>

<template>

    <Head title="Partidos Políticos" />

    <AuthenticatedLayout>
        <div class="flex justify-end items-center mb-4">
            <TextButton :href="route('category-parties.create')" class="p-4 justify-center text-center">
                Novo Partido
            </TextButton>
        </div>

        <div v-if="props.parties.length > 0" class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col"
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                            Logo
                        </th>
                        <th scope="col"
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                            Nome do Partido
                        </th>
                        <th class="relative px-4 py-3">
                            <span class="sr-only">Ações</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                    <tr v-for="party in props.parties" :key="party.id">
                        <td class="px-4 py-4 whitespace-nowrap">
                            <img v-if="party.logo" :src="getImage(party.logo)" :alt="party.name_party"
                                class="h-10 w-10 object-contain rounded" />
                            <div v-else
                                class="h-10 w-10 rounded bg-gray-300 flex items-center justify-center text-gray-600 text-xs">
                                N/A
                            </div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap font-medium text-gray-900 dark:text-white">
                            {{ party.name_party }}
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end space-x-1">
                                <IconButton :href="route('category-parties.edit', party.id)" color="yellow"
                                    title="Editar">
                                    <PencilSquareIcon class="h-5 w-5" />
                                </IconButton>

                                <IconButton @click="destroy(party.id)" color="red" title="Remover">
                                    <TrashIcon class="h-5 w-5" />
                                </IconButton>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div v-else>
            <p class="text-gray-500 dark:text-gray-400">Nenhum partido encontrado.</p>
        </div>
    </AuthenticatedLayout>
</template>