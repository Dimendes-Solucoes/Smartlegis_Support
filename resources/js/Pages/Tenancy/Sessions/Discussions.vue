<script setup lang="ts">
import { ref, watch } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, Link } from '@inertiajs/vue3';
import IconButton from '@/Components/Itens/IconButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { UsersIcon, TrashIcon } from '@heroicons/vue/24/solid';
import { debounce } from 'lodash';
import ConfirmDeletionModal from '@/Components/ConfirmDeletionModal.vue';
import TextButton from '@/Components/Itens/TextButton.vue';

interface Quorum {
    session_id: number
}

interface Discussion {
    id: number;
    session_name: string;
    document_name: string;
    quorum: Quorum
}

interface PaginatedDiscussions {
    data: Discussion[];
    links: { url: string | null; label: string; active: boolean; }[];
}

interface DiscussionData {
    session: {
        id: number;
        name: string;
    };
    discussions: PaginatedDiscussions;
}

const props = defineProps<{
    discussionData: DiscussionData;
    filters: {
        search: string;
        sort: string;
        direction: string;
    }
}>();

const search = ref(props.filters.search);
watch(search, debounce((value: string) => {
    router.get(route('discussions.index'), { search: value }, { preserveState: true, replace: true });
}, 300));

const confirmingDeletion = ref(false);
const itemToDelete = ref<Discussion | null>(null);

const openConfirmDeleteModal = (item: Discussion) => {
    itemToDelete.value = item;
    confirmingDeletion.value = true;
};

const closeModal = () => {
    confirmingDeletion.value = false;
    itemToDelete.value = null;
};

const deleteItem = () => {
    if (!itemToDelete.value) return;
    router.delete(route('discussions.destroy', itemToDelete.value.id), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
    });
};
</script>

<template>

    <Head title="Discussões de Documentos" />

    <AuthenticatedLayout>
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 px-2">
                <span>{{ discussionData.session.name }}</span>
            </h2>

            <TextButton :href="route('sessions.talks', discussionData.session.id)" color="gray">
                Voltar
            </TextButton>
        </div>

        <div class="mb-6">
            <TextInput type="text" v-model="search" placeholder="Buscar por documento..." class="w-full" />
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                            <span>DOCUMENTOS EM DISCUSSÃO</span>
                        </th>
                        <th scope="col" class="relative px-6 py-3">
                            <span class="sr-only">AÇÕES</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                    <tr v-for="discussion in discussionData.discussions.data" :key="discussion.id">
                        <td class="px-6 py-4 whitespace-normal font-medium">
                            {{ discussion.document_name }}
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end space-x-1">
                                <IconButton
                                    :href="route('sessions.discussions', [discussion.quorum.session_id, discussion.id])"
                                    color="indigo" title="Ver Inscritos">
                                    <UsersIcon class="h-5 w-5" />
                                </IconButton>
                                <IconButton as="button" color="red" title="Excluir"
                                    @click.stop="openConfirmDeleteModal(discussion)">
                                    <TrashIcon class="h-5 w-5" />
                                </IconButton>
                            </div>
                        </td>
                    </tr>

                    <tr v-if="discussionData.discussions.data.length === 0">
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            Nenhuma discussão encontrada
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div v-if="discussionData.discussions.data.length > 0 && discussionData.discussions.links.length > 3"
            class="mt-6 flex justify-center">
            <Link v-for="(link, index) in discussionData.discussions.links" :key="index" :href="link.url || ''"
                class="px-4 py-2 text-sm"
                :class="{ 'bg-indigo-500 text-white rounded-md': link.active, 'text-gray-500 hover:text-gray-800': !link.active, 'cursor-not-allowed text-gray-400': !link.url }"
                v-html="link.label" />
        </div>
    </AuthenticatedLayout>

    <ConfirmDeletionModal :show="confirmingDeletion" title="Excluir Discussão"
        :message="`Tem certeza que deseja mover a discussão do documento '${itemToDelete?.document_name}' para a lixeira?`"
        @close="closeModal" @confirm="deleteItem" />
</template>