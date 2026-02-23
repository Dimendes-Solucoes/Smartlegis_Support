<script setup lang="ts">
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/components/Common/PrimaryButton.vue';
import InputError from '@/components/Form/InputError.vue';
import InputLabel from '@/components/Form/InputLabel.vue';
import TextInput from '@/components/Form/TextInput.vue';
import IconButton from '@/components/Itens/IconButton.vue';
import BackButtonRow from '@/components/Common/BackButtonRow.vue';
import SelectInput from '@/components/Form/SelectInput.vue';
import { TrashIcon, PencilIcon, CheckIcon, XMarkIcon } from '@heroicons/vue/24/outline';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

interface Legislature {
    id: number;
    title: string;
    start_at: string;
    end_at: string;
    is_current: boolean;
}

interface UserTerm {
    id: number | null;
    user_id: number;
    name: string;
    start_date: string;
    end_date: string | null;
    has_conflict: boolean;
}

interface Councilor {
    id: number;
    name: string;
}

const props = defineProps<{
    legislature: Legislature;
    userTerms: UserTerm[];
    councilors: Councilor[];
}>();

const legislatureForm = useForm({
    title: props.legislature.title,
    start_at: props.legislature.start_at,
    end_at: props.legislature.end_at,
    is_current: props.legislature.is_current,
});

const submitDetails = () => {
    legislatureForm.put(route('legislatures.update', props.legislature.id));
};

const usersForm = useForm({
    users: props.userTerms.map((t) => ({
        id: t.id,
        user_id: t.user_id,
        name: t.name,
        start_date: t.start_date,
        end_date: t.end_date ?? '',
        has_conflict: t.has_conflict,
    })),
});

const sortUsers = () => {
    usersForm.users.sort((a, b) => a.name.localeCompare(b.name, 'pt-BR'));
};

const newTerm = ref({
    user_id: null as number | null,
    start_date: props.legislature.start_at ?? '',
    end_date: props.legislature.end_at ?? '',
});

const councilorsNotAdded = ref<Councilor[]>([]);

watch(
    () => props.councilors,
    () => {
        councilorsNotAdded.value = [...props.councilors].sort((a, b) =>
            a.name.localeCompare(b.name, 'pt-BR'),
        );
    },
    { immediate: true },
);

const addTerm = () => {
    if (!newTerm.value.user_id || !newTerm.value.start_date) {
        alert('Selecione um vereador e a data de início.');
        return;
    }

    const councilor = props.councilors.find(
        (c) => Number(c.id) === Number(newTerm.value.user_id),
    );

    usersForm.users.push({
        id: null,
        user_id: newTerm.value.user_id,
        name: councilor?.name ?? '',
        start_date: newTerm.value.start_date,
        end_date: newTerm.value.end_date ?? '',
        has_conflict: false,
    });

    sortUsers();

    newTerm.value = { user_id: null, start_date: '', end_date: '' };
    submitUsers();
};

const removeTerm = (index: number) => {
    usersForm.users.splice(index, 1);
    submitUsers();
};

const editingIndex = ref<number | null>(null);
const editBuffer = ref({ start_date: '', end_date: '' });

const startEdit = (index: number) => {
    editingIndex.value = index;
    editBuffer.value = {
        start_date: usersForm.users[index].start_date,
        end_date: usersForm.users[index].end_date ?? '',
    };
};

const confirmEdit = (index: number) => {
    usersForm.users[index].start_date = editBuffer.value.start_date;
    usersForm.users[index].end_date = editBuffer.value.end_date;
    editingIndex.value = null;
    submitUsers();
};

const cancelEdit = () => {
    editingIndex.value = null;
};

const submitUsers = () => {
    usersForm.put(route('legislatures.update_users', props.legislature.id));
};
</script>

<template>

    <Head title="Editar Legislatura" />
    <AuthenticatedLayout>
        <BackButtonRow :href="route('legislatures.index')" />

        <form @submit.prevent="submitDetails" class="mb-8 p-4 border rounded-lg">
            <h3 class="text-md font-semibold mb-4">Detalhes da Legislatura</h3>

            <div class="mb-4">
                <InputLabel for="title" value="Título" />
                <TextInput id="title" type="text" class="mt-1 block w-full" v-model="legislatureForm.title" required />
                <InputError class="mt-2" :message="legislatureForm.errors.title" />
            </div>

            <div class="mb-4 flex space-x-4">
                <div class="flex-1">
                    <InputLabel for="start_at" value="Início" />
                    <TextInput id="start_at" type="date" class="mt-1 block w-full" v-model="legislatureForm.start_at"
                        required />
                    <InputError class="mt-2" :message="legislatureForm.errors.start_at" />
                </div>
                <div class="flex-1">
                    <InputLabel for="end_at" value="Fim" />
                    <TextInput id="end_at" type="date" class="mt-1 block w-full" v-model="legislatureForm.end_at"
                        required />
                    <InputError class="mt-2" :message="legislatureForm.errors.end_at" />
                </div>
            </div>

            <div class="mb-4 flex items-center space-x-2">
                <input id="is_current" type="checkbox" v-model="legislatureForm.is_current"
                    class="rounded border-gray-300 dark:border-gray-700 text-indigo-600" />
                <InputLabel for="is_current" value="Legislatura atual?" />
            </div>

            <div class="flex justify-end">
                <PrimaryButton :class="{ 'opacity-25': legislatureForm.processing }"
                    :disabled="legislatureForm.processing">
                    Atualizar Detalhes
                </PrimaryButton>
            </div>
        </form>

        <div class="p-4 border rounded-lg">
            <h3 class="text-md font-semibold mb-4">Gerenciar Vereadores</h3>

            <div class="mb-4 flex flex-col sm:flex-row items-end sm:space-x-4 space-y-4 sm:space-y-0">
                <div class="flex-1 w-full">
                    <InputLabel for="select-councilor" value="Vereador" />
                    <SelectInput id="select-councilor" v-model="newTerm.user_id" :options="councilorsNotAdded"
                        value-key="id" label-key="name" placeholder="Selecione um vereador"
                        :disable-placeholder="true" />
                </div>
                <div class="flex-1 w-full">
                    <InputLabel for="term-start" value="Início do mandato" />
                    <TextInput id="term-start" type="date" class="mt-1 block w-full" v-model="newTerm.start_date" />
                </div>
                <div class="flex-1 w-full">
                    <InputLabel for="term-end" value="Fim do mandato (opcional)" />
                    <TextInput id="term-end" type="date" class="mt-1 block w-full" v-model="newTerm.end_date" />
                </div>
            </div>

            <div class="flex justify-end">
                <PrimaryButton type="button" @click="addTerm" class="mb-2"
                    :disabled="!newTerm.user_id || !newTerm.start_date || usersForm.processing">
                    Adicionar Vereador
                </PrimaryButton>
            </div>

            <div v-if="usersForm.users.length > 0" class="mt-4">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 border text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th
                                class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                Nome</th>
                            <th
                                class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                Início</th>
                            <th
                                class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                Fim</th>
                            <th
                                class="px-3 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        <tr v-for="(user, index) in usersForm.users" :key="`${user.user_id}-${index}`">
                            <td class="px-3 py-2 text-sm font-medium" :class="user.has_conflict
                                ? 'text-red-600 dark:text-red-400'
                                : 'text-gray-900 dark:text-gray-100'"
                                :title="user.has_conflict ? 'Conflito com outra legislatura' : ''">
                                {{ user.name }}
                                <span v-if="user.has_conflict" class="ml-1 text-xs">(conflito)</span>
                            </td>

                            <td class="px-3 py-2 text-sm text-gray-500 dark:text-gray-300">
                                <TextInput v-if="editingIndex === index" type="date" class="block w-full py-0.5 text-sm"
                                    v-model="editBuffer.start_date" />
                                <span v-else>{{ user.start_date }}</span>
                            </td>

                            <td class="px-3 py-2 text-sm text-gray-500 dark:text-gray-300">
                                <TextInput v-if="editingIndex === index" type="date" class="block w-full py-0.5 text-sm"
                                    v-model="editBuffer.end_date" />
                                <span v-else>{{ user.end_date || '-' }}</span>
                            </td>

                            <td class="px-3 py-2 text-right space-x-1">
                                <template v-if="editingIndex === index">
                                    <IconButton @click="confirmEdit(index)" color="green" title="Confirmar"
                                        :disabled="usersForm.processing">
                                        <CheckIcon class="h-5 w-5" />
                                    </IconButton>
                                    <IconButton @click="cancelEdit" color="gray" title="Cancelar">
                                        <XMarkIcon class="h-5 w-5" />
                                    </IconButton>
                                </template>

                                <template v-else>
                                    <IconButton @click="startEdit(index)" color="blue" title="Editar datas"
                                        :disabled="usersForm.processing">
                                        <PencilIcon class="h-5 w-5" />
                                    </IconButton>
                                    <IconButton @click="removeTerm(index)" color="red" title="Remover"
                                        :disabled="usersForm.processing">
                                        <TrashIcon class="h-5 w-5" />
                                    </IconButton>
                                </template>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <p v-else class="text-gray-500 dark:text-gray-400 mt-4">Nenhum vereador adicionado ainda.</p>

            <InputError class="mt-2" :message="usersForm.errors.users" />
        </div>
    </AuthenticatedLayout>
</template>