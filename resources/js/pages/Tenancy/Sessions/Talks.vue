<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { UserGroupIcon, MicrophoneIcon, ChatBubbleOvalLeftEllipsisIcon, MegaphoneIcon, QuestionMarkCircleIcon, PencilSquareIcon, TrashIcon } from '@heroicons/vue/24/outline';
import IconButton from '@/components/Itens/IconButton.vue';
import ConfirmDeletionModal from '@/components/Common/ConfirmDeletionModal.vue';
import { Quorum } from '@/types/inertia';

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

const clearGeneric = (title: string, routeName: string, messagePart: string) => {
    modalTitle.value = `Limpar ${title}`;
    modalMessage.value = `Tem certeza que deseja limpar todas as ${messagePart}? Esta ação não pode ser desfeita.`;
    clearAction.value = () => {
        router.delete(route(routeName, props.session.id), {
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

const sections = computed(() => [
    {
        title: 'Quóruns',
        icon: UserGroupIcon,
        href: route('sessions.quorums', props.session.id),
        clearAction: () => clearGeneric('Quóruns', 'sessions.quorums.clear', 'quóruns'),
        hasContent: props.session.quorums?.length > 0
    },
    {
        title: 'Tribunas',
        icon: MicrophoneIcon,
        href: route('sessions.tribunes', props.session.id),
        clearAction: () => clearGeneric('Tribunas', 'sessions.tribunes.clear', 'tribunas'),
        hasContent: firstQuorum.value?.tribunes?.length > 0
    },
    {
        title: 'Discussões',
        icon: ChatBubbleOvalLeftEllipsisIcon,
        href: route('sessions.list_discussions', props.session.id),
        clearAction: () => clearGeneric('Discussões', 'sessions.discussions.clear', 'discussões'),
        hasContent: firstQuorum.value?.discussions?.length > 0
    },
    {
        title: 'Explanações Pessoais',
        icon: MegaphoneIcon,
        href: route('sessions.big_discussions', props.session.id),
        clearAction: () => clearGeneric('Explanações Pessoais', 'sessions.big_discussions.clear', 'explanações pessoais'),
        hasContent: firstQuorum.value?.big_discussions?.length > 0
    },
    {
        title: 'Questões de Ordem',
        icon: QuestionMarkCircleIcon,
        href: route('sessions.questions_orders', props.session.id),
        clearAction: () => clearGeneric('Questões de Ordem', 'sessions.questions_orders.clear', 'questões de ordem'),
        hasContent: firstQuorum.value?.question_orders?.length > 0
    },
]);
</script>

<template>
    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
        Gerenciar falas da sessão
    </p>

    <div class="space-y-4">
        <div v-for="section in sections" :key="section.title"
            class="block w-full p-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm hover:shadow-md transition-all duration-200 ease-in-out">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-full">
                        <component :is="section.icon" class="h-6 w-6 text-blue-600 dark:text-blue-300" />
                    </div>
                    <span class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ section.title }}</span>
                </div>
                <div v-if="section.hasContent" class="text-gray-500 dark:text-gray-400">
                    <IconButton :href="section.href" color="yellow" title="Editar">
                        <PencilSquareIcon class="h-5 w-5" />
                    </IconButton>

                    <IconButton @click="section.clearAction()" color="red" title="Limpar" class="ml-1">
                        <TrashIcon class="h-5 w-5" />
                    </IconButton>
                </div>
            </div>
        </div>
    </div>

    <ConfirmDeletionModal :show="showConfirmModal" :title="modalTitle" :message="modalMessage" @close="closeModal"
        @confirm="confirmClear" />
</template>