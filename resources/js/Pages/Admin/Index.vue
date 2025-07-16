<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import TextButton from '@/Components/Itens/TextButton.vue';
import IconButton from '@/Components/Itens/IconButton.vue';
import { PencilSquareIcon, TrashIcon } from '@heroicons/vue/24/solid';

interface Admin {
    id: number;
    name: string;
    email: string;
}

const props = defineProps<{
    admins: Admin[];
    selectedTenantId: string | null;
}>();

const form = useForm({});

const deleteAdmin = (adminId: number) => {
    if (confirm('Tem certeza que deseja deletar este administrador?')) {
        form.delete(route('admin.destroy', adminId), {
            preserveScroll: true,
            onSuccess: () => { },
            onError: (errors) => {
                console.error('Erro ao deletar administrador:', errors);
                alert('Ocorreu um erro ao deletar o administrador.');
            }
        });
    }
};
</script>

<template>

    <Head title="Administradores" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
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
                                        <td class="px-6 py-4 whitespace-nowrap">{{ admin.name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ admin.email }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <IconButton :href="route('admin.edit', admin.id)" color="yellow"
                                                title="Editar">
                                                <PencilSquareIcon class="h-5 w-5" />
                                            </IconButton>

                                            <IconButton @click="deleteAdmin(admin.id)" color="red" title="Deletar"
                                                class="ml-2">
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
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>