<script setup lang="ts">
import { defineProps, defineEmits } from 'vue';
import { EyeIcon, ExclamationCircleIcon, ChevronDownIcon, ChevronUpIcon, ClipboardDocumentListIcon, TrashIcon } from '@heroicons/vue/24/outline';
import LinkButton from '@/Components/LinkButton.vue';
import IconButton from '@/Components/Itens/IconButton.vue';

interface Session {
    id: number;
    name: string;
}

interface Document {
    id: number;
    name: string;
    attachment: string;
}

const props = withDefaults(defineProps<{
    title: string;
    description: string;
    session: Session;
    documents: Document[];
}>(), {
    documents: () => [],
});

const emit = defineEmits(['update:documents', 'remove-document']);

const moveUp = (index: number) => {
    if (index > 0) {
        const newDocuments = [...props.documents];
        const itemToMove = newDocuments.splice(index, 1)[0];
        newDocuments.splice(index - 1, 0, itemToMove);
        emit('update:documents', newDocuments);
    }
};

const moveDown = (index: number) => {
    if (index < props.documents.length - 1) {
        const newDocuments = [...props.documents];
        const itemToMove = newDocuments.splice(index, 1)[0];
        newDocuments.splice(index + 1, 0, itemToMove);
        emit('update:documents', newDocuments);
    }
};

const removeDocument = (document: Document) => {
    emit('remove-document', document);
};
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
            <p>Nenhum documento em {{ title.toLowerCase() }}.</p>
        </div>

        <ul v-else class="space-y-3">
            <li v-for="(element, index) in documents" :key="element.id"
                class="flex items-center p-3 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 transition-all duration-200 ease-in-out">

                <div class="flex flex-col items-center mr-2">
                    <button @click="moveUp(index)" :disabled="index === 0"
                        class="px-1 text-gray-500 dark:text-gray-400 hover:text-indigo-600 rounded-full disabled:opacity-30 disabled:cursor-not-allowed transition-colors duration-200"
                        title="Mover para cima">
                        <ChevronUpIcon class="h-5 w-5" />
                    </button>
                    <button @click="moveDown(index)" :disabled="index === documents.length - 1"
                        class="px-1 text-gray-500 dark:text-gray-400 hover:text-indigo-600 rounded-full disabled:opacity-30 disabled:cursor-not-allowed transition-colors duration-200"
                        title="Mover para baixo">
                        <ChevronDownIcon class="h-5 w-5" />
                    </button>
                </div>

                <span class="flex-1 mx-2 font-medium text-gray-800 dark:text-gray-200">{{ element.name }}</span>

                <div class="flex flex-col space-y-1 md:flex-row md:space-y-0 md:space-x-1">
                    <LinkButton :link="element.attachment" title="Visualizar documento">
                        <EyeIcon class="h-5 w-5 text-white" />
                    </LinkButton>

                    <IconButton :href="route('sessions.documents.votes', { id: session.id, document_id: element.id })"
                        title="Votos" class="ml-0" color="green">
                        <ClipboardDocumentListIcon class="h-5 w-5 text-white" />
                    </IconButton>

                    <IconButton as="button" color="red" title="Remover da Pauta" @click="removeDocument(element)">
                        <TrashIcon class="h-5 w-5 text-white" />
                    </IconButton>
                </div>
            </li>
        </ul>
    </div>
</template>