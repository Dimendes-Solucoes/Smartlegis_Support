<script setup lang="ts">
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import IconButton from '@/Components/Itens/IconButton.vue';
import { TrashIcon } from '@heroicons/vue/24/solid';
import ConfirmDeletionModal from '@/Components/ConfirmDeletionModal.vue';

interface User {
    id: number;
    name: string;
}

interface QuorumUser {
    id: number; 
    user: User;
}

interface Quorum {
    id: number;
    session: {
        name: string;
    };
}

interface QuorumData {
    quorum: Quorum;
    quorum_users: QuorumUser[];
}


const props = defineProps<{
    quorumData: QuorumData;
}>();


const confirmingUserRemoval = ref(false);
const userToRemove = ref<QuorumUser | null>(null);

const openConfirmRemoveModal = (quorumUser: QuorumUser) => {
    userToRemove.value = quorumUser;
    confirmingUserRemoval.value = true;
};

const closeModal = () => {
    confirmingUserRemoval.value = false;
    userToRemove.value = null;
};

const removeUser = () => {
    if (!userToRemove.value) return;
    
    // A rota para deletar o usuário do quorum será 'quorums.users.destroy'
    router.delete(route('quorums.users.destroy', userToRemove.value.id), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
    });
};
</script>

<template>
    <Head :title="`${quorumData.quorum.session.name}`" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-6">
                    {{ quorumData.quorum.session.name }}
                </h1>

                <div class="space-y-8">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900 dark:text-gray-100">
                            <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-gray-200">Inscritos no Quorum</h2>
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Vereador</th>
                                        <th class="relative px-6 py-3"><span class="sr-only">Ações</span></th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                    <tr v-for="quorumUser in quorumData.quorum_users" :key="quorumUser.id">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ quorumUser.user.name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <IconButton as="button" color="red" title="Remover Inscrição" @click.stop="openConfirmRemoveModal(quorumUser)">
                                                <TrashIcon class="h-5 w-5" />
                                            </IconButton>
                                        </td>
                                    </tr>
                                    <tr v-if="quorumData.quorum_users.length === 0">
                                        <td colspan="2" class="px-6 py-10 text-center text-gray-500">Nenhum usuário inscrito no quorum.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>

    <ConfirmDeletionModal 
        :show="confirmingUserRemoval"
        title="Remover Inscrição"
        :message="`Tem certeza que deseja remover a inscrição de '${userToRemove?.user.name}' deste quorum?`"
        @close="closeModal"
        @confirm="removeUser"
    />
</template>