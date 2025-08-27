<script setup lang="ts">
import { defineProps } from 'vue';
import draggable from 'vuedraggable';
import { Bars2Icon, EyeIcon, ExclamationCircleIcon } from '@heroicons/vue/24/outline';
import LinkButton from '@/Components/LinkButton.vue';
import IconButton from '@/Components/Itens/IconButton.vue';
import { ClipboardDocumentListIcon } from '@heroicons/vue/24/solid';

interface Session {
    id: number;
    name: string;
}

interface Document {
    id: number;
    name: string;
    attachment: string;
}

const props = defineProps<{
    title: string;
    description: string;
    session: Session;
    documents: Document[];
}>();
</script>

<template>
    <div class="p-5 rounded-lg border dark:border-gray-600 mb-6">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 flex items-center">
            {{ title }}
        </h2>
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">{{ description }}</p>

        <div v-if="documents.length === 0"
            class="p-6 text-center text-gray-500 dark:text-gray-400 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg">
            <ExclamationCircleIcon class="h-8 w-8 mx-auto mb-2 text-gray-400" />
            <p>Nenhum documento no {{ title.toLowerCase() }}.</p>
        </div>

        <draggable :list="documents" item-key="id" tag="ul" handle=".drag-handle" ghost-class="ghost-item"
            class="space-y-3">
            <template #item="{ element }">
                <li
                    class="flex items-center p-3 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 transition-all duration-200 ease-in-out">
                    <button
                        class="drag-handle p-2 text-gray-500 dark:text-gray-400 hover:text-indigo-600 rounded-full cursor-grab flex-shrink-0 transition-colors duration-200"
                        title="Mover">
                        <Bars2Icon class="h-5 w-5" />
                    </button>

                    <span class="flex-1 mx-2 font-medium text-gray-800 dark:text-gray-200">{{ element.name }}</span>

                    <LinkButton :link="element.attachment" title="Visualizar documento">
                        <EyeIcon class="h-5 w-5 text-white" />
                    </LinkButton>

                    <IconButton :href="route('sessions.documents.votes', { id: session.id, document_id: element.id })"
                        title="Votos" class="ml-1" color="green">
                        <ClipboardDocumentListIcon class="h-5 w-5 text-white" />
                    </IconButton>
                </li>
            </template>
        </draggable>
    </div>
</template>