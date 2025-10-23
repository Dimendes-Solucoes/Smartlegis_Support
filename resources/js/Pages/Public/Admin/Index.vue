<script setup lang="ts">
import { PencilSquareIcon, TrashIcon } from '@heroicons/vue/24/outline';
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import TextButton from '@/Components/Itens/TextButton.vue';
import IconButton from '@/Components/Itens/IconButton.vue';
import ConfirmDeletionModal from '@/Components/ConfirmDeletionModal.vue';

interface Admin {
    id: number;
    name: string;
    email: string;
}

const props = defineProps<{
    admins: Admin[];
}>();

const adminToDelete = ref<Admin | null>(null);

const openConfirmDeleteModal = (admin: Admin) => {
    adminToDelete.value = admin;
}

const closeDeleteModal = () => {
    adminToDelete.value = null;
}

const form = useForm({});

const deleteAdmin = () => {
    form.delete(route('admin.destroy', adminToDelete.value?.id), {
        preserveScroll: true,
        onSuccess: () => closeDeleteModal()
    });
};
</script>

<template>

    <Head title="Administradores" />

    <AuthenticatedLayout>
        <div class="flex justify-end mb-4">
            <TextButton :href="route('admin.create')">
                Novo Administrador
            </TextButton>
        </div>

        <div v-if="props.admins.length > 0" class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                            Nome</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                            Email</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                    <tr v-for="admin in props.admins" :key="admin.id">
                        <td class="px-6 py-4 whitespace-normal">{{ admin.name }}</td>
                        <td class="px-6 py-4 whitespace-normal">{{ admin.email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <IconButton :href="route('admin.edit', admin.id)" color="yellow" title="Editar">
                                <PencilSquareIcon class="h-5 w-5" />
                            </IconButton>

                            <IconButton @click="openConfirmDeleteModal(admin)" color="red" title="Deletar" class="ml-1">
                                <TrashIcon class="h-5 w-5" />
                            </IconButton>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div v-else>
            <p>Nenhum administrador encontrado.</p>
        </div>
    </AuthenticatedLayout>

    <ConfirmDeletionModal :show="adminToDelete !== null" title="Deletar administrador"
        :message="`Tem certeza que deseja deletar o administrador '${adminToDelete?.name}'?`" @close="closeDeleteModal"
        @confirm="deleteAdmin" />
</template>