<script setup lang="ts">
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import IconButton from '@/Components/Itens/IconButton.vue';
import { TrashIcon } from '@heroicons/vue/24/solid';
import ConfirmDeletionModal from '@/Components/ConfirmDeletionModal.vue';
import BackButtonRow from '@/Components/BackButtonRow.vue';

interface User {
    id: number;
    name: string;
}

interface BigDiscussionUser {
    id: number;
    user: User;
}

interface Discussion {
    id: number;
    quorum: {
        session: {
            id: number;
            name: string;
        }
    }
}

interface DiscussionData {
    discussion: Discussion;
    users: BigDiscussionUser[];
}

const props = defineProps<{
    discussionData: DiscussionData;
}>();

const confirmingUserRemoval = ref(false);
const userToRemove = ref<BigDiscussionUser | null>(null);

const openConfirmRemoveModal = (user: BigDiscussionUser) => {
    userToRemove.value = user;
    confirmingUserRemoval.value = true;
};

const closeModal = () => {
    confirmingUserRemoval.value = false;
    userToRemove.value = null;
};

const removeUser = () => {
    if (!userToRemove.value) return;
    router.delete(route('big-discussions.users.destroy', userToRemove.value.id), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
    });
};
</script>

<template>

    <Head :title="`${discussionData.discussion.quorum.session.name}`" />

    <AuthenticatedLayout>
        <BackButtonRow :href="route('sessions.talks', discussionData.discussion.quorum.session.id)" />

        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 px-2">
                <span>{{ discussionData.discussion.quorum.session.name }}</span>
            </h2>
        </div>

        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                        Explanações Pessoais
                    </th>
                    <th class="relative px-6 py-3"><span class="sr-only">Ações</span></th>
                </tr>
            </thead>

            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                <tr v-for="user in discussionData.users" :key="user.id">
                    <td class="px-6 py-4 whitespace-normal text-sm">{{ user.user.name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <IconButton as="button" color="red" title="Remover Inscrição"
                            @click.stop="openConfirmRemoveModal(user)">
                            <TrashIcon class="h-5 w-5" />
                        </IconButton>
                    </td>
                </tr>

                <tr v-if="discussionData.users.length === 0">
                    <td colspan="3" class="px-6 py-4 whitespace-nowrap text-sm">
                        Nenhum usuário inscrito
                    </td>
                </tr>
            </tbody>
        </table>
    </AuthenticatedLayout>

    <ConfirmDeletionModal :show="confirmingUserRemoval" title="Remover Inscrição"
        :message="`Tem certeza que deseja remover a inscrição de '${userToRemove?.user.name}'?`" @close="closeModal"
        @confirm="removeUser" />
</template>