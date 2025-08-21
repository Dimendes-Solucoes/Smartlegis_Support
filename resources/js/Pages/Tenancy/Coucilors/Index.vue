<script setup lang="ts">
import { ref, watch } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { getImageUrl } from '@/Utils/image';
import { ClipboardDocumentListIcon, PencilSquareIcon, UserMinusIcon, UserPlusIcon } from '@heroicons/vue/24/solid';
import TextButton from '@/Components/Itens/TextButton.vue';
import IconButton from '@/Components/Itens/IconButton.vue';
import UserStatusBadge from '@/Components/User/UserStatusBadge.vue';
import Checkbox from '@/Components/Checkbox.vue';

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
    filters: {
        show_inactive?: boolean;
    }
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

const copyAllUsersToClipboard = (): void => {
    const allUsersInfo = props.users.map(user => {
        const name = user.name;
        const category = user.category?.name || 'N/D';
        const party = user.party?.name_party || 'N/D';
        const email = user.email;

        return `${category} - ${party}\n${name}\n${email}`;
    });

    const combinedString = allUsersInfo.join('\n\n');

    navigator.clipboard.writeText(combinedString)
        .then(() => {
            alert('Informações de todos os vereadores copiadas para a área de transferência!');
        })
        .catch(err => {
            console.error('Erro ao copiar para a área de transferência:', err);
            const textarea = document.createElement('textarea');
            textarea.value = combinedString;
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand('copy');
            document.body.removeChild(textarea);
            alert('Informações copiadas. Seu navegador não suporta a API nativa de cópia.');
        });
};

const showInactive = ref(props.filters.show_inactive || false);
watch(showInactive, (value) => {
    router.get(route('councilors.index'), { show_inactive: value }, {
        preserveState: true,
        replace: true,
    });
});
</script>

<template>

    <Head title="Vereadores" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="flex justify-end items-center mb-4">
                            <div class="flex items-center space-x-2">
                                <TextButton @click="copyAllUsersToClipboard()" class="p-4" color="yellow"
                                    :disabled="!props.users.length">
                                    Copiar Vereadores
                                </TextButton>

                                <TextButton :href="route('councilors.create')" class="p-4">
                                    Novo Vereador
                                </TextButton>
                            </div>
                        </div>

                        <div class="flex justify-start items-center mb-4">
                            <div class="flex items-center">
                                <Checkbox id="show_inactive" v-model:checked="showInactive" />
                                <label for="show_inactive" class="ml-2 text-sm text-gray-600 dark:text-gray-400">
                                    Exibir inativos
                                </label>
                            </div>
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
                                            Email</th>
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
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <div class="flex flex-col">
                                                <span>{{ user.name }} ({{ user.party?.name_party || '-' }})</span>
                                                <span class="text-gray-500 dark:text-gray-300 text-sm">{{
                                                    user.category?.name || '-' }} - {{ user.nickname || '-' }}</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap">{{ user.email }}</td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <span v-if="user.status_lider"
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-gray-300 text-black mr-1">
                                                Líder
                                            </span>

                                            <span v-if="user.is_first_secretary"
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-gray-300 text-black">
                                                1º Sec.
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