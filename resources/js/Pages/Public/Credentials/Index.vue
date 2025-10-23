<script setup lang="ts">
import { ref } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router } from "@inertiajs/vue3";
import IconButton from "@/Components/Itens/IconButton.vue";
import { TrashIcon, PencilSquareIcon } from "@heroicons/vue/24/outline";
import ConfirmDeletionModal from "@/Components/ConfirmDeletionModal.vue";
import { getImageUrl } from "@/Utils/image";
import TextButton from "@/Components/Itens/TextButton.vue";
import RegularColumn from "@/Components/Table/RegularColumn.vue";

interface Credential {
    id: number;
    tenant_id: string;
    short_name: string;
    channel: string;
    host: string;
    key: string;
    city_name: string | null;
    city_shield: string | null;
}

const props = defineProps<{
    credentials: Credential[];
    can: {
        delete_credential: boolean;
    };
}>();

const itemToDelete = ref<Credential | null>(null);
const confirmingDeletion = ref(false);

const openConfirmDeleteModal = (credential: Credential) => {
    itemToDelete.value = credential;
    confirmingDeletion.value = true;
};

const closeModal = () => {
    confirmingDeletion.value = false;
    itemToDelete.value = null;
};

const deleteItem = () => {
    if (!itemToDelete.value) return;
    router.delete(route("credentials.destroy", itemToDelete.value.id), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
    });
};
</script>

<template>
    <div>
        <Head title="Credenciais" />
        <AuthenticatedLayout>
            <div class="flex justify-end items-center mb-6">
                <TextButton :href="route('credentials.create')">
                    Nova Credencial
                </TextButton>
            </div>
            <div
                class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg"
            >
                <div v-if="credentials.length > 0" class="overflow-x-auto">
                    <table
                        class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm"
                    >
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <RegularColumn>Brasão</RegularColumn>
                                <RegularColumn>Cidade</RegularColumn>
                                <RegularColumn>Nome curto</RegularColumn>
                                <RegularColumn>Canal</RegularColumn>
                                <RegularColumn>Host</RegularColumn>
                                <th class="relative px-6 py-3">
                                    <span class="sr-only">Ações</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody
                            class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700"
                        >
                            <tr
                                v-for="credential in credentials"
                                :key="credential.id"
                                class="hover:bg-gray-50 dark:hover:bg-gray-700/50"
                            >
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <img
                                        v-if="credential.city_shield"
                                        :src="getImageUrl(credential.city_shield)"
                                        alt="Brasão"
                                        class="h-10 w-10 object-contain rounded-md"
                                    />
                                    <div
                                        v-else
                                        class="h-10 w-10 bg-gray-200 dark:bg-gray-700 rounded-md flex items-center justify-center text-xs text-gray-500"
                                    >
                                        <span>N/A</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ credential.city_name || "N/A" }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ credential.short_name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ credential.channel }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ credential.host }}
                                </td>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium"
                                >
                                    <div class="flex items-center justify-end space-x-1">
                                        <IconButton
                                            :href="
                                                route('credentials.edit', credential.id)
                                            "
                                            color="yellow"
                                            title="Editar"
                                        >
                                            <PencilSquareIcon class="h-5 w-5" />
                                        </IconButton>
                                        <IconButton
                                            v-if="can.delete_credential"
                                            as="button"
                                            color="red"
                                            title="Excluir"
                                            @click="openConfirmDeleteModal(credential)"
                                        >
                                            <TrashIcon class="h-5 w-5" />
                                        </IconButton>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div v-else class="p-6 text-center text-gray-500">
                    <p>Nenhuma credencial encontrada.</p>
                </div>
            </div>
        </AuthenticatedLayout>

        <ConfirmDeletionModal
            :show="confirmingDeletion"
            title="Excluir Credencial"
            :message="`Tem certeza que deseja mover a credencial para '${itemToDelete?.city_name}' para a lixeira?`"
            buttonText="Excluir"
            @close="closeModal"
            @confirm="deleteItem"
        />
    </div>
</template>
