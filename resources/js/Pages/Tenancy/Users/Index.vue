<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { getImageUrl } from '@/Utils/image'
import TextButton from '@/Components/Itens/TextButton.vue';
import IconButton from '@/Components/Itens/IconButton.vue';
import { PencilSquareIcon, TrashIcon } from '@heroicons/vue/24/solid';

interface User {
    id: number;
    name: string;
    email: string;
    path_image: string | null;
    nickname: string | null;
    category: { id: number; name: string } | null;
}

const props = defineProps<{
    users: User[];
    selectedTenantId: string | null;
}>();

const getImage = (path: string): string => {
    return getImageUrl(path)
}

const form = useForm({});

const deleteUser = (userId: number) => {
    if (confirm('Tem certeza que deseja deletar este usuário?')) {
        form.delete(route('users.destroy', userId), {
            preserveScroll: true,
            onSuccess: () => { },
            onError: (errors) => {
                console.error('Erro ao deletar usuário:', errors);
                alert('Ocorreu um erro ao deletar o usuário.');
            }
        });
    }
};
</script>

<template>

    <Head title="Usuários" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="flex justify-end mb-4">
                            <TextButton :href="route('users.create')">
                                Novo Usuário
                            </TextButton>

                            <TextButton :href="route('users.replace_mayor')" color="orange" class="ml-2">
                                Trocar prefeito
                            </TextButton>
                        </div>

                        <div v-if="props.users.length > 0" class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                            Foto</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                            Nome</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                            Apelido</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                            Email</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                            Categoria</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                            Ações</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                    <tr v-for="user in props.users" :key="user.id">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <img v-if="user.path_image" :src="getImage(user.path_image)"
                                                alt="Foto do Usuário" class="h-10 w-10 rounded-full object-cover">
                                            <div v-else
                                                class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center text-gray-600 text-sm">
                                                <span>N/A</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ user.name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ user.nickname || '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ user.email }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ user.category?.name || '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-left text-sm font-medium">
                                            <IconButton :href="route('users.edit', user.id)" color="yellow" title="Editar">
                                                <PencilSquareIcon class="h-5 w-5" />
                                            </IconButton>

                                            <IconButton v-if="user.category.id !== 5" @click="deleteUser(user.id)" color="red" title="Deletar"
                                                class="ml-2">
                                                <TrashIcon class="h-5 w-5" />
                                            </IconButton>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div v-else>
                            <p>Nenhum usuário encontrado para esta cidade.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>