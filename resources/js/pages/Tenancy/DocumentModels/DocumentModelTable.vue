<script setup lang="ts">
import { PencilSquareIcon, TrashIcon } from "@heroicons/vue/24/outline";
import IconButton from "@/components/Itens/IconButton.vue";

interface DocumentModel {
    id: number;
    title: string;
    user?: { name: string };
    category?: { name: string };
}

defineProps<{
    models: DocumentModel[];
}>();

const emit = defineEmits(["open-confirm-delete-modal"]);
</script>

<template>
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
                <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    Título
                </th>
                <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    Categoria
                </th>
                <th scope="col"
                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    Ações
                </th>
            </tr>
        </thead>

        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            <tr v-for="model in models" :key="model.id"
                class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                        {{ model.title }}
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span
                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                        {{ model.category?.name || "Sem categoria" }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <IconButton :href="route('document-models.edit', model.id)" color="yellow" title="Editar">
                        <PencilSquareIcon class="h-5 w-5" />
                    </IconButton>

                    <IconButton @click="emit('open-confirm-delete-modal', model)"
                        color="red" title="Desativar" class="ml-1">
                        <TrashIcon class="h-5 w-5" />
                    </IconButton>
                </td>
            </tr>
            <tr v-if="models.length === 0">
                <td colspan="4" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400 text-sm">
                    Nenhum modelo de documento encontrado.
                </td>
            </tr>
        </tbody>
    </table>
</template>
