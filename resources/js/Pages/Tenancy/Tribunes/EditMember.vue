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

interface TribuneUser {
    id: number;
    user: User;
}

interface ApartiamentoUser {
    id: number;
    user: User;
}

interface Tribune {
    id: number;
    quorum: {
        session: {
            id: number;
            name: string;
        }
    }
}

interface TribuneData {
    tribune: Tribune;
    tribune_users: TribuneUser[];
    apartiamento_users: ApartiamentoUser[];
}

const props = defineProps<{
    tribuneData: TribuneData;
}>();


const confirmingUserRemoval = ref(false);
const userToRemove = ref<TribuneUser | null>(null);

const openConfirmRemoveModal = (tribuneUser: TribuneUser) => {
    userToRemove.value = tribuneUser;
    confirmingUserRemoval.value = true;
};

const closeModal = () => {
    confirmingUserRemoval.value = false;
    userToRemove.value = null;
};

const removeUser = () => {
    if (!userToRemove.value) return;

    router.delete(route('tribunes.users.destroy', userToRemove.value.id), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
    });
};
</script>

<template>

    <Head :title="`${tribuneData.tribune.quorum.session.name}`" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="space-y-8">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900 dark:text-gray-100">
                            <div class="flex items-center justify-between mb-4">
                                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
                                    <span>{{ tribuneData.tribune.quorum.session.name }}</span>
                                </h2>

                                <TextButton :href="route('sessions.talks', tribuneData.tribune.quorum.session.id)"
                                    color="gray">
                                    Voltar
                                </TextButton>
                            </div>

                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 mb-6">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                            Inscritos na Tribuna</th>
                                        <th class="relative px-6 py-3"><span class="sr-only">Ações</span></th>
                                    </tr>
                                </thead>

                                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                    <tr v-for="tribuneUser in tribuneData.tribune_users" :key="tribuneUser.id">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ tribuneUser.user.name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <IconButton as="button" color="red" title="Remover Inscrição"
                                                @click.stop="openConfirmRemoveModal(tribuneUser)">
                                                <TrashIcon class="h-5 w-5" />
                                            </IconButton>
                                        </td>
                                    </tr>

                                    <tr v-if="tribuneData.tribune_users.length === 0">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            Nenhum usuário inscrito na tribuna
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                            Inscritos no Apartiamento
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                    <tr v-for="apartiamentoUser in tribuneData.apartiamento_users"
                                        :key="apartiamentoUser.id">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            {{ apartiamentoUser.user.name }}
                                        </td>
                                    </tr>
                                    <tr v-if="tribuneData.apartiamento_users.length === 0">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            Nenhum usuário inscrito para apartiamento
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>

    <ConfirmDeletionModal :show="confirmingUserRemoval" title="Remover Inscrição"
        :message="`Tem certeza que deseja remover a inscrição de '${userToRemove?.user.name}' desta tribuna?`"
        @close="closeModal" @confirm="removeUser" />
</template>