<script setup lang="ts">
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import IconButton from '@/Components/Itens/IconButton.vue';
import { TrashIcon } from '@heroicons/vue/24/outline';
import ConfirmDeletionModal from '@/Components/ConfirmDeletionModal.vue';

interface Tenant {
    id: string;
    city_name: string;
}

interface Credential {
    id: number;
    tenant_id: string;
    short_name: string;
    channel: string;
    host: string;
    key: string;
    city_name: string | null;
    tenant: Tenant | null;
}

const props = defineProps<{
    credentials: Credential[];
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
    router.delete(route('credentials.destroy', itemToDelete.value.id), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
    });
};
</script>

<template>
    <Head title="Credenciais" />
    <AuthenticatedLayout>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div v-if="credentials.length > 0" class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-700/50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">Cidade</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">Nome Curto</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">Canal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">Host</th>
                                    <th class="relative px-6 py-3"><span class="sr-only">Ações</span></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                <tr v-for="credential in credentials" :key="credential.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="px-6 py-4 whitespace-nowrap">{{ credential.tenant?.city_name || credential.city_name || 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ credential.short_name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ credential.channel }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ credential.host }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end space-x-1">
                                            <IconButton as="button" color="red" title="Excluir Permanentemente" @click="openConfirmDeleteModal(credential)">
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
        :message="`Tem certeza que deseja EXCLUIR PERMANENTEMENTE a credencial para '${itemToDelete?.tenant?.city_name || itemToDelete?.city_name}'? Esta ação não pode ser desfeita.`"
        buttonText="Excluir"
        @close="closeModal" 
        @confirm="deleteItem" 
    />
</template>