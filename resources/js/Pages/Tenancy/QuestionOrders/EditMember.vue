<script setup lang="ts">
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import IconButton from '@/Components/Itens/IconButton.vue';
import { TrashIcon } from '@heroicons/vue/24/solid';
import ConfirmDeletionModal from '@/Components/ConfirmDeletionModal.vue';
import TextButton from '@/Components/Itens/TextButton.vue';

interface User {
    id: number;
    name: string;
}

interface QuestionOrderUser {
    id: number;
    user: User;
}

interface QuestionOrder {
    id: number;
    quorum: {
        session: {
            id: number;
            name: string;
        }
    };
}

interface QuestionOrderData {
    question_order: QuestionOrder;
    users: QuestionOrderUser[];
}

const props = defineProps<{
    questionOrderData: QuestionOrderData;
}>();

const confirmingUserRemoval = ref(false);
const userToRemove = ref<QuestionOrderUser | null>(null);

const openConfirmRemoveModal = (user: QuestionOrderUser) => {
    userToRemove.value = user;
    confirmingUserRemoval.value = true;
};

const closeModal = () => {
    confirmingUserRemoval.value = false;
    userToRemove.value = null;
};

const removeUser = () => {
    if (!userToRemove.value) return;
    router.delete(route('question-orders.users.destroy', userToRemove.value.id), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
    });
};
</script>

<template>

    <Head :title="`${questionOrderData.question_order.quorum.session.name}`" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
                                <span>{{ questionOrderData.question_order.quorum.session.name}}</span>
                            </h2>

                            <TextButton
                                :href="route('sessions.talks', questionOrderData.question_order.quorum.session.id)"
                                color="gray">
                                Voltar
                            </TextButton>
                        </div>

                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                        Questões de Ordem    
                                    </th>
                                    <th class="relative px-6 py-3"><span class="sr-only">Ações</span></th>
                                </tr>
                            </thead>

                            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                <tr v-for="user in questionOrderData.users" :key="user.id">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ user.user.name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <IconButton as="button" color="red" title="Remover Inscrição"
                                            @click.stop="openConfirmRemoveModal(user)">
                                            <TrashIcon class="h-5 w-5" />
                                        </IconButton>
                                    </td>
                                </tr>

                                <tr v-if="questionOrderData.users.length === 0">
                                    <td colspan="2" class="px-6 py-4 whitespace-nowrap text-sm">
                                        Nenhum usuário inscrito.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>

    <ConfirmDeletionModal :show="confirmingUserRemoval" title="Remover Inscrição"
        :message="`Tem certeza que deseja remover a inscrição de '${userToRemove?.user.name}'?`" @close="closeModal"
        @confirm="removeUser" />
</template>