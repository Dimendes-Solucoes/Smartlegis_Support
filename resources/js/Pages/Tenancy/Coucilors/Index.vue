<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { getImageUrl } from '@/Utils/image'
import { PencilSquareIcon, UserMinusIcon, UserPlusIcon } from '@heroicons/vue/24/solid';
import TextButton from '@/Components/Itens/TextButton.vue';
import IconButton from '@/Components/Itens/IconButton.vue';
import UserStatusBadge from '@/Components/User/UserStatusBadge.vue';

interface User {
    id: number;
    name: string;
    email: string;
    path_image: string | null;
    nickname: string | null;
    category: { id: number; name: string } | null;
    party: { id: number; name_party: string } | null;
    status_lider: string | null;
    status_user: number;
    is_first_secretary: boolean | null;
}

const props = defineProps<{
    users: User[];
    selectedTenantId: string | null;
}>();

const getImage = (path: string): string => {
    return getImageUrl(path)
}

const changeStatus = (userId: number): void => {
    if (confirm(`Tem certeza que deseja atualizar o status do usuário?`)) {
        router.patch(route('councilors.change_status', { id: userId }), {}, {
            preserveScroll: true
        });
    }
};
</script>

<template>

    <Head title="Vereadores" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="flex justify-end mb-4">
                            <TextButton :href="route('councilors.create')" class="p-4">
                                Novo Vereador
                            </TextButton>
                        </div>

                        <div v-if="props.users.length > 0" class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col"
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                            Foto</th>
                                        <th scope="col"
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                            Nome</th>
                                        <th scope="col"
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                            Apelido</th>
                                        <th scope="col"
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                            Email</th>
                                        <th scope="col"
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                            Categoria</th>
                                        <th scope="col"
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                            Partido</th>
                                        <th scope="col"
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                            Obs.</th>
                                        <th scope="col"
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                            Status</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                    <tr v-for="user in props.users" :key="user.id">
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <img v-if="user.path_image" :src="getImage(user.path_image)"
                                                alt="Foto do Usuário" class="h-10 w-10 rounded-full object-cover">
                                            <div v-else
                                                class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center text-gray-600 text-sm">
                                                <span>N/A</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap">{{ user.name }}</td>
                                        <td class="px-4 py-4 whitespace-nowrap">{{ user.nickname || '-' }}</td>
                                        <td class="px-4 py-4 whitespace-nowrap">{{ user.email }}</td>
                                        <td class="px-4 py-4 whitespace-nowrap">{{ user.category?.name || '-' }}</td>
                                        <td class="px-4 py-4 whitespace-nowrap">{{ user.party?.name_party || '-' }}</td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <span v-if="user.status_lider"
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-gray-300 text-black mr-1">
                                                Líder
                                            </span>

                                            <span v-if="user.is_first_secretary"
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-gray-300 text-black">
                                                1º Secretário
                                            </span>

                                            <span v-if="!user.status_lider && !user.is_first_secretary">
                                                -
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <UserStatusBadge :statusUser="user.status_user" />
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-left text-sm font-medium">
                                            <IconButton :href="route('councilors.edit', user.id)" color="yellow"
                                                title="Editar">
                                                <PencilSquareIcon class="h-5 w-5" />
                                            </IconButton>

                                            <IconButton @click="changeStatus(user.id)"
                                                :color="user.status_user === 1 ? 'red' : 'green'"
                                                :title="user.status_user === 1 ? 'Desativar usuário' : 'Ativar usuário'"
                                                href="#" class="ml-1" v-if="user.category?.id === 2">
                                                <template v-if="user.status_user === 1">
                                                    <UserMinusIcon class="h-5 w-5" />
                                                </template>
                                                <template v-else>
                                                    <UserPlusIcon class="h-5 w-5" />
                                                </template>
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