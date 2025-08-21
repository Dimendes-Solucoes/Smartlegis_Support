<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import {
    UserGroupIcon,
    MicrophoneIcon,
    ChatBubbleOvalLeftEllipsisIcon,
    MegaphoneIcon,
    QuestionMarkCircleIcon,
    PencilSquareIcon,
    TrashIcon
} from '@heroicons/vue/24/solid';
import TextButton from '@/Components/Itens/TextButton.vue';
import IconButton from '@/Components/Itens/IconButton.vue';
import ConfirmDeletionModal from '@/Components/ConfirmDeletionModal.vue';

interface Tribune {
    id: number;
}

interface Discussion {
    id: number;
}

interface BigDiscussion {
    id: number;
}

interface QuestionOrder {
    id: number;
}

interface Quorum {
    id: number;
    tribunes: Tribune[];
    discussions: Discussion[];
    big_discussions: BigDiscussion[];
    question_orders: QuestionOrder[];
}

const props = defineProps<{
    session: {
        id: number;
        name: string;
        quorums: Quorum[];
    };
}>();

const showConfirmModal = ref(false);
const clearAction = ref<(() => void) | null>(null);
const modalTitle = ref('');
const modalMessage = ref('');
const firstQuorum = computed(() => props.session.quorums[0] || null);

const clearTribunes = () => {
    modalTitle.value = 'Limpar Tribunas';
    modalMessage.value = 'Tem certeza que deseja limpar todas as tribunas? Esta ação não pode ser desfeita.';
    clearAction.value = () => {
        router.delete(route('sessions.tribunes.clear', props.session.id), {
            preserveScroll: true,
            onSuccess: () => {
                closeModal();
            },
        });
    };
    showConfirmModal.value = true;
};

const clearDiscussions = () => {
    modalTitle.value = 'Limpar Discussões';
    modalMessage.value = 'Tem certeza que deseja limpar todas as discussões? Esta ação não pode ser desfeita.';
    clearAction.value = () => {
        router.delete(route('sessions.discussions.clear', props.session.id), {
            preserveScroll: true,
            onSuccess: () => {
                closeModal();
            },
        });
    };
    showConfirmModal.value = true;
};

const clearBigDiscussions = () => {
    modalTitle.value = 'Limpar Explanações Pessoais';
    modalMessage.value = 'Tem certeza que deseja limpar todas as explanações pessoais? Esta ação não pode ser desfeita.';
    clearAction.value = () => {
        router.delete(route('sessions.big_discussions.clear', props.session.id), {
            preserveScroll: true,
            onSuccess: () => {
                closeModal();
            },
        });
    };
    showConfirmModal.value = true;
};

const clearQuestionOrders = () => {
    modalTitle.value = 'Limpar Questões de Ordem';
    modalMessage.value = 'Tem certeza que deseja limpar todas as questões de ordem? Esta ação não pode ser desfeita.';
    clearAction.value = () => {
        router.delete(route('sessions.questions_orders.clear', props.session.id), {
            preserveScroll: true,
            onSuccess: () => {
                closeModal();
            },
        });
    };
    showConfirmModal.value = true;
};

const clearQuorums = () => {
    modalTitle.value = 'Limpar Quóruns';
    modalMessage.value = 'Tem certeza que deseja limpar todos os quóruns? Esta ação não pode ser desfeita.';
    clearAction.value = () => {
        router.delete(route('sessions.quorums.clear', props.session.id), {
            preserveScroll: true,
            onSuccess: () => {
                closeModal();
            },
        });
    };
    showConfirmModal.value = true;
};

const confirmClear = () => {
    if (clearAction.value) {
        clearAction.value();
    }
};

const closeModal = () => {
    showConfirmModal.value = false;
};
</script>

<template>

    <Head :title="`${session.name}`" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
                                <span>{{ session.name }}</span>
                            </h2>

                            <TextButton :href="route('sessions.index')" color="gray">
                                Voltar
                            </TextButton>
                        </div>

                        <div class="space-y-4">
                            <div
                                class="block w-full p-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm hover:shadow-md transition-all duration-200 ease-in-out">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-full">
                                            <UserGroupIcon class="h-6 w-6 text-blue-600 dark:text-blue-300" />
                                        </div>
                                        <span
                                            class="text-lg font-semibold text-gray-900 dark:text-gray-100">Quóruns</span>
                                    </div>
                                    <div v-if="props.session.quorums?.length > 0"
                                        class="text-gray-500 dark:text-gray-400">
                                        <IconButton :href="route('sessions.quorums', session.id)" color="yellow"
                                            title="Editar">
                                            <PencilSquareIcon class="h-5 w-5" />
                                        </IconButton>
                                        <IconButton @click="clearQuorums" color="red" title="Limpar" class="ml-1">
                                            <TrashIcon class="h-5 w-5" />
                                        </IconButton>
                                    </div>
                                </div>
                            </div>
                            <div
                                class="block w-full p-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm hover:shadow-md transition-all duration-200 ease-in-out">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-full">
                                            <MicrophoneIcon class="h-6 w-6 text-blue-600 dark:text-blue-300" />
                                        </div>
                                        <span
                                            class="text-lg font-semibold text-gray-900 dark:text-gray-100">Tribunas</span>
                                    </div>
                                    <div v-if="firstQuorum?.tribunes?.length > 0"
                                        class="text-gray-500 dark:text-gray-400">
                                        <IconButton :href="route('sessions.tribunes', session.id)" color="yellow"
                                            title="Editar">
                                            <PencilSquareIcon class="h-5 w-5" />
                                        </IconButton>
                                        <IconButton @click="clearTribunes" color="red" title="Limpar" class="ml-1">
                                            <TrashIcon class="h-5 w-5" />
                                        </IconButton>
                                    </div>
                                </div>
                            </div>
                            <div
                                class="block w-full p-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm hover:shadow-md transition-all duration-200 ease-in-out">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-full">
                                            <ChatBubbleOvalLeftEllipsisIcon
                                                class="h-6 w-6 text-blue-600 dark:text-blue-300" />
                                        </div>
                                        <span
                                            class="text-lg font-semibold text-gray-900 dark:text-gray-100">Discussões</span>
                                    </div>
                                    <div v-if="firstQuorum?.discussions?.length > 0"
                                        class="text-gray-500 dark:text-gray-400">
                                        <IconButton :href="route('sessions.list_discussions', session.id)"
                                            color="yellow" title="Editar">
                                            <PencilSquareIcon class="h-5 w-5" />
                                        </IconButton>
                                        <IconButton @click="clearDiscussions" color="red" title="Limpar" class="ml-1">
                                            <TrashIcon class="h-5 w-5" />
                                        </IconButton>
                                    </div>
                                </div>
                            </div>
                            <div
                                class="block w-full p-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm hover:shadow-md transition-all duration-200 ease-in-out">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-full">
                                            <MegaphoneIcon class="h-6 w-6 text-blue-600 dark:text-blue-300" />
                                        </div>
                                        <span class="text-lg font-semibold text-gray-900 dark:text-gray-100">Explanações
                                            Pessoais</span>
                                    </div>
                                    <div v-if="firstQuorum?.big_discussions?.length > 0"
                                        class="text-gray-500 dark:text-gray-400">
                                        <IconButton :href="route('sessions.big_discussions', session.id)" color="yellow"
                                            title="Editar">
                                            <PencilSquareIcon class="h-5 w-5" />
                                        </IconButton>
                                        <IconButton @click="clearBigDiscussions" color="red" title="Limpar"
                                            class="ml-1">
                                            <TrashIcon class="h-5 w-5" />
                                        </IconButton>
                                    </div>
                                </div>
                            </div>
                            <div
                                class="block w-full p-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm hover:shadow-md transition-all duration-200 ease-in-out">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-full">
                                            <QuestionMarkCircleIcon class="h-6 w-6 text-blue-600 dark:text-blue-300" />
                                        </div>
                                        <span class="text-lg font-semibold text-gray-900 dark:text-gray-100">Questões de
                                            Ordem</span>
                                    </div>
                                    <div v-if="firstQuorum?.question_orders?.length > 0"
                                        class="text-gray-500 dark:text-gray-400">
                                        <IconButton :href="route('sessions.questions_orders', session.id)"
                                            color="yellow" title="Editar">
                                            <PencilSquareIcon class="h-5 w-5" />
                                        </IconButton>
                                        <IconButton @click="clearQuestionOrders" color="red" title="Limpar"
                                            class="ml-1">
                                            <TrashIcon class="h-5 w-5" />
                                        </IconButton>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <ConfirmDeletionModal :show="showConfirmModal" :title="modalTitle" :message="modalMessage" @close="closeModal"
            @confirm="confirmClear" />

    </AuthenticatedLayout>
</template>