<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import IconButton from '@/Components/Itens/IconButton.vue';
import { TrashIcon } from '@heroicons/vue/24/solid';

interface CommissionType {
    id: number;
    title: string;
}

interface User {
    id: number;
    name: string;
    function: number;
}

interface CommissionUser {
    id: number;
    name: string;
    function: number | null;
}

interface UserFunction {
    id: number;
    title: string;
}

interface FormErrors {
    comission_name?: string;
    type?: string;
    users?: string;
    'users.id'?: string;
    'users.function'?: string;
    [key: string]: string | undefined;
}

const props = defineProps<{
    comission: {
        id: number;
        comission_name: string;
        type: number;
    };
    commissionTypes: CommissionType[];
    counciliors: User[];
    comissionUsers: CommissionUser[];
    functions: UserFunction[];
}>();

const commissionForm = useForm({
    comission_name: props.comission.comission_name,
    type: props.comission.type,
}) as {
    comission_name: string;
    type: number;
    errors: FormErrors;
    processing: boolean;
    put: (url: string, options?: any) => void;
};

const submitCommissionDetails = () => {
    commissionForm.put(route('commissions.update', props.comission.id));
};

const usersForm = useForm({
    users: props.comissionUsers.map(user => ({
        id: user.id,
        function: Number(user.function),
    })),
}) as {
    users: { id: number; function: number | null }[];
    errors: FormErrors;
    processing: boolean;
    put: (url: string, options?: any) => void;
};


const newUserToAdd = ref<{ id: number | null; function: number | null }>({
    id: null,
    function: null,
});

const usersNotYetAdded = ref<User[]>([]);

watch([() => props.counciliors, () => usersForm.users], () => {
    usersNotYetAdded.value = props.counciliors.filter(
        availableUser => !usersForm.users.some(commissionUser => commissionUser.id === availableUser.id)
    );
}, { immediate: true });


const addUser = () => {
    if (newUserToAdd.value.id && newUserToAdd.value.function) {
        const isUserAlreadyAdded = usersForm.users.some(
            (user) => user.id === newUserToAdd.value.id
        );

        if (!isUserAlreadyAdded) {
            usersForm.users.push({
                id: newUserToAdd.value.id,
                function: newUserToAdd.value.function,
            });
            newUserToAdd.value.id = null;
            newUserToAdd.value.function = null;
            submitUsers();
        } else {
            alert('Este usuário já foi adicionado à comissão.');
        }
    } else {
        alert('Selecione um usuário e uma função para adicionar.');
    }
};

const removeUser = (userId: number) => {
    usersForm.users = usersForm.users.filter(user => user.id !== userId);
    submitUsers();
};

const submitUsers = () => {
    usersForm.put(route('commissions.update_users', props.comission.id));
};
</script>

<template>

    <Head title="Editar Comissão" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <form @submit.prevent="submitCommissionDetails" class="mb-8 p-4 border rounded-lg">
                            <h3 class="text-md font-semibold mb-4">Detalhes da Comissão</h3>
                            <div class="mb-4">
                                <InputLabel for="comission_name" value="Nome da Comissão" />
                                <TextInput id="comission_name" type="text" class="mt-1 block w-full"
                                    v-model="commissionForm.comission_name" required autofocus autocomplete="off" />
                                <InputError class="mt-2" :message="commissionForm.errors.comission_name" />
                            </div>

                            <div class="mb-4">
                                <InputLabel value="Tipo de Comissão" />
                                <div class="mt-2 flex space-x-4">
                                    <div v-for="typeOption in props.commissionTypes" :key="typeOption.id"
                                        class="flex items-center">
                                        <input :id="`type-${typeOption.id}`" type="radio" :value="typeOption.id"
                                            v-model="commissionForm.type"
                                            class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800"
                                            required />
                                        <label :for="`type-${typeOption.id}`"
                                            class="ml-2 text-sm text-gray-600 dark:text-gray-400">
                                            {{ typeOption.title }}
                                        </label>
                                    </div>
                                </div>
                                <InputError class="mt-2" :message="commissionForm.errors.type" />
                            </div>

                            <div class="flex items-center justify-end">
                                <PrimaryButton :class="{ 'opacity-25': commissionForm.processing }"
                                    :disabled="commissionForm.processing">
                                    Atualizar Detalhes
                                </PrimaryButton>
                            </div>
                        </form>

                        <div class="p-4 border rounded-lg">
                            <h3 class="text-md font-semibold mb-4">Gerenciar Membros da Comissão</h3>

                            <div class="mb-4 flex flex-col sm:flex-row items-end sm:space-x-4 space-y-4 sm:space-y-0">
                                <div class="flex-1 w-full">
                                    <InputLabel for="select-user" value="Selecionar Vereador" />
                                    <select id="select-user" v-model="newUserToAdd.id"
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                        <option :value="null" disabled>Selecione um vereador</option>
                                        <option v-for="user in usersNotYetAdded" :key="user.id" :value="user.id">
                                            {{ user.name }}
                                        </option>
                                    </select>
                                    <InputError class="mt-2" :message="usersForm.errors['users.id']" />
                                </div>

                                <div class="flex-1 w-full">
                                    <InputLabel for="select-function" value="Selecionar Função" />
                                    <select id="select-function" v-model="newUserToAdd.function"
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                        <option :value="null" disabled>Selecione uma função</option>
                                        <option v-for="role in props.functions" :key="role.id" :value="role.id">
                                            {{ role.title }}
                                        </option>
                                    </select>
                                    <InputError class="mt-2" :message="usersForm.errors['users.function']" />
                                </div>
                            </div>

                            <div class="flex justify-end">
                                <PrimaryButton type="button" @click="addUser" class="w-full sm:w-auto mt-4 sm:mt-0"
                                    :disabled="!newUserToAdd.id || !newUserToAdd.function || usersForm.processing">
                                    Adicionar Membro
                                </PrimaryButton>
                            </div>

                            <div v-if="usersForm.users.length > 0" class="mt-4">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 table-sm border">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th scope="col"
                                                class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Nome
                                            </th>
                                            <th scope="col"
                                                class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Função
                                            </th>
                                            <th scope="col"
                                                class="px-3 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Ações
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody
                                        class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        <tr v-for="(user, index) in usersForm.users" :key="user.id">
                                            <td
                                                class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{props.counciliors.find(u => u.id === user.id)?.name}}
                                            </td>
                                            <td
                                                class="px-3 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                                {{props.functions.find(r => r.id === user.function)?.title}}
                                            </td>
                                            <td class="px-3 py-2 whitespace-nowrap text-right text-sm font-medium">
                                                <IconButton @click="removeUser(user.id)" color="red" title="Deletar"
                                                    :disabled="usersForm.processing">
                                                    <TrashIcon class="h-5 w-5" />
                                                </IconButton>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <p v-else class="text-gray-500 dark:text-gray-400 mt-4">Nenhum membro adicionado ainda.</p>

                            <InputError class="mt-2" :message="usersForm.errors.users" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>