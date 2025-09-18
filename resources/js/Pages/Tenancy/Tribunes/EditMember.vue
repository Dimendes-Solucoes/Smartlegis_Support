<script setup lang="ts">
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import IconButton from '@/Components/Itens/IconButton.vue';
import { TrashIcon, ExclamationCircleIcon, ChevronDownIcon, ChevronUpIcon } from '@heroicons/vue/24/outline';
import ConfirmDeletionModal from '@/Components/ConfirmDeletionModal.vue';
import BackButtonRow from '@/Components/BackButtonRow.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

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

const tribuneUsersList = ref(props.tribuneData.tribune_users);
const isSavingOrder = ref(false);

const moveUp = (index: number) => {
    if (index > 0) {
        const itemToMove = tribuneUsersList.value.splice(index, 1)[0];
        tribuneUsersList.value.splice(index - 1, 0, itemToMove);
    }
};

const moveDown = (index: number) => {
    if (index < tribuneUsersList.value.length - 1) {
        const itemToMove = tribuneUsersList.value.splice(index, 1)[0];
        tribuneUsersList.value.splice(index + 1, 0, itemToMove);
    }
};

const saveOrder = () => {
    isSavingOrder.value = true;
    const userIds = tribuneUsersList.value.map(item => item.id);

    router.put(route('tribunes.users.reorder', { id: props.tribuneData.tribune.quorum.session.id, tribune: props.tribuneData.tribune.id }), {
        user_ids: userIds,
    }, {
        preserveScroll: true,
        onFinish: () => isSavingOrder.value = false,
    });
};

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

    <Head :title="`Inscritos na Tribuna - ${tribuneData.tribune.quorum.session.name}`" />

    <AuthenticatedLayout>
        <BackButtonRow :href="route('sessions.edit', tribuneData.tribune.quorum.session.id)" />

        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mx-2">
                <span>{{ tribuneData.tribune.quorum.session.name }}</span>
            </h2>
            <PrimaryButton @click="saveOrder" :disabled="isSavingOrder" :class="{ 'opacity-25': isSavingOrder }">
                Salvar Ordem
            </PrimaryButton>
        </div>

        <div class="space-y-6">
            <div class="p-5 rounded-lg border bg-white dark:bg-gray-800 dark:border-gray-700">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Inscritos na Tribuna</h2>

                <div v-if="tribuneUsersList.length === 0"
                    class="mt-4 p-6 text-center text-gray-500 dark:text-gray-400 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg">
                    <ExclamationCircleIcon class="h-8 w-8 mx-auto mb-2 text-gray-400" />
                    <p>Nenhum usuário inscrito na tribuna.</p>
                </div>

                <ul v-else class="mt-4 space-y-3">
                    <li v-for="(tribuneUser, index) in tribuneUsersList" :key="tribuneUser.id"
                        class="flex items-center p-3 bg-gray-50 dark:bg-gray-900/50 rounded-lg border border-gray-200 dark:border-gray-700">

                        <div class="flex flex-col items-center mr-2">
                            <button @click="moveUp(index)" :disabled="index === 0"
                                class="px-1 text-gray-400 hover:text-indigo-600 disabled:opacity-30">
                                <ChevronUpIcon class="h-5 w-5" />
                            </button>
                            <button @click="moveDown(index)" :disabled="index === tribuneUsersList.length - 1"
                                class="px-1 text-gray-400 hover:text-indigo-600 disabled:opacity-30">
                                <ChevronDownIcon class="h-5 w-5" />
                            </button>
                        </div>

                        <span class="flex-1 font-medium text-gray-800 dark:text-gray-200">{{ tribuneUser.user.name
                        }}</span>

                        <IconButton as="button" color="red" title="Remover Inscrição"
                            @click.stop="openConfirmRemoveModal(tribuneUser)">
                            <TrashIcon class="h-5 w-5" />
                        </IconButton>
                    </li>
                </ul>
            </div>

            <div class="p-5 rounded-lg border bg-white dark:bg-gray-800 dark:border-gray-700">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Inscritos no Apartiamento</h2>
                <div v-if="tribuneData.apartiamento_users.length === 0"
                    class="mt-4 p-6 text-center text-gray-500 dark:text-gray-400 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg">
                    <ExclamationCircleIcon class="h-8 w-8 mx-auto mb-2 text-gray-400" />
                    <p>Nenhum usuário inscrito para apartiamento.</p>
                </div>
                <ul v-else class="mt-4 space-y-3">
                    <li v-for="apartiamentoUser in tribuneData.apartiamento_users" :key="apartiamentoUser.id"
                        class="flex items-center p-3 bg-gray-50 dark:bg-gray-900/50 rounded-lg border border-gray-200 dark:border-gray-700">
                        <span class="flex-1 font-medium text-gray-800 dark:text-gray-200">{{ apartiamentoUser.user.name
                        }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </AuthenticatedLayout>

    <ConfirmDeletionModal :show="confirmingUserRemoval" title="Remover Inscrição"
        :message="`Tem certeza que deseja remover a inscrição de '${userToRemove?.user.name}' desta tribuna?`"
        buttonText="Remover Inscrição" @close="closeModal" @confirm="removeUser" />
</template>