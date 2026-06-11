<script setup lang="ts">
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/components/Common/PrimaryButton.vue';
import InputError from '@/components/Form/InputError.vue';
import InputLabel from '@/components/Form/InputLabel.vue';
import TextInput from '@/components/Form/TextInput.vue';
import IconButton from '@/components/Itens/IconButton.vue';
import BackButtonRow from '@/components/Common/BackButtonRow.vue';
import SelectInput from '@/components/Form/SelectInput.vue';
import Modal from '@/components/Common/Modal.vue';
import UserForm from '@/components/User/UserForm.vue';
import ImageUploadWithCropper from '@/components/User/ImageUploadWithCropper.vue';
import CouncilorForm from '@/pages/Tenancy/Councilors/CouncilorForm.vue';
import { TrashIcon, PencilIcon, CheckIcon, XMarkIcon } from '@heroicons/vue/24/outline';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

interface Mandate {
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
    category_party_id: number | null;
    start_date: string;
    end_date: string | null;
    has_conflict: boolean;
}

interface Councilor {
    id: number;
    name: string;
}

interface Party {
    id: number;
    name_party: string;
}

interface Category {
    id: number;
    name: string;
}

const props = defineProps<{
    mandate: Mandate;
    userTerms: UserTerm[];
    councilors: Councilor[];
    parties: Party[];
    categories: Category[];
}>();

const mandateForm = useForm({
    title: props.mandate.title,
    start_at: props.mandate.start_at,
    end_at: props.mandate.end_at,
    is_current: props.mandate.is_current,
});

const submitDetails = () => {
    mandateForm.put(route('mandates.update', props.mandate.id));
};

const usersForm = useForm({
    users: props.userTerms.map((t) => ({
        id: t.id,
        user_id: t.user_id,
        name: t.name,
        category_party_id: t.category_party_id,
        start_date: t.start_date,
        end_date: t.end_date ?? '',
        has_conflict: t.has_conflict,
    })),
});

watch(
    () => props.userTerms,
    (terms: UserTerm[]) => {
        usersForm.users = terms.map((t: UserTerm) => ({
            id: t.id,
            user_id: t.user_id,
            name: t.name,
            category_party_id: t.category_party_id,
            start_date: t.start_date,
            end_date: t.end_date ?? '',
            has_conflict: t.has_conflict,
        }));
    },
);

const sortUsers = () => {
    usersForm.users.sort((a, b) => a.name.localeCompare(b.name, 'pt-BR'));
};

const newTerm = ref({
    user_id: null as number | null,
    category_party_id: null as number | null,
    start_date: props.mandate.start_at ?? '',
    end_date: props.mandate.end_at ?? '',
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

watch(
    () => newTerm.value.user_id,
    (userId: number | null) => {
        if (!userId) return;
        const councilor = props.councilors.find((c: Councilor) => Number(c.id) === Number(userId)) as any;
        newTerm.value.category_party_id = councilor?.category_party_id ?? null;
    },
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
        category_party_id: newTerm.value.category_party_id,
        start_date: newTerm.value.start_date,
        end_date: newTerm.value.end_date ?? '',
        has_conflict: false,
    });

    sortUsers();
    submitUsers();
};

const removeTerm = (index: number) => {
    usersForm.users.splice(index, 1);
    submitUsers();
};

const editingIndex = ref<number | null>(null);
const editBuffer = ref({ category_party_id: null as number | null, start_date: '', end_date: '' });

const startEdit = (index: number) => {
    editingIndex.value = index;
    editBuffer.value = {
        category_party_id: usersForm.users[index].category_party_id,
        start_date: usersForm.users[index].start_date,
        end_date: usersForm.users[index].end_date ?? '',
    };
};

const confirmEdit = (index: number) => {
    usersForm.users[index].category_party_id = editBuffer.value.category_party_id;
    usersForm.users[index].start_date = editBuffer.value.start_date;
    usersForm.users[index].end_date = editBuffer.value.end_date;
    editingIndex.value = null;
    submitUsers();
};

const cancelEdit = () => {
    editingIndex.value = null;
};

const submitUsers = () => {
    usersForm.put(route('mandates.update_users', props.mandate.id));
};

const partyName = (partyId: number | null) => {
    if (!partyId) return '-';
    return props.parties.find((p: Party) => p.id === partyId)?.name_party ?? '-';
};

const showCreateModal = ref(false);

const createForm = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    nickname: '',
    path_image: null as File | null,
    category_id: '',
    party_id: '',
    mandate_id: props.mandate.id,
    is_vereador_active: props.mandate.is_current,
    is_leader: false,
    is_first_secretary: false,
    birthdate: '',
    summary: '',
});

const submitCreate = () => {
    createForm.post(route('councilors.store'), {
        forceFormData: true,
        onSuccess: () => {
            showCreateModal.value = false;
            createForm.reset();
        },
        onFinish: () => createForm.reset('password', 'password_confirmation'),
    });
};
</script>

<template>

    <Head title="Editar Mandato" />
    <AuthenticatedLayout>
        <BackButtonRow :href="route('mandates.index')" />

        <form @submit.prevent="submitDetails" class="mb-8 p-4 border rounded-lg">
            <h3 class="text-md font-semibold mb-4">Detalhes do Mandato</h3>

            <div class="mb-4">
                <InputLabel for="title" value="Título" />
                <TextInput id="title" type="text" class="mt-1 block w-full" v-model="mandateForm.title" required />
                <InputError class="mt-2" :message="mandateForm.errors.title" />
            </div>

            <div class="mb-4 flex space-x-4">
                <div class="flex-1">
                    <InputLabel for="start_at" value="Início" />
                    <TextInput id="start_at" type="date" class="mt-1 block w-full" v-model="mandateForm.start_at"
                        required />
                    <InputError class="mt-2" :message="mandateForm.errors.start_at" />
                </div>
                <div class="flex-1">
                    <InputLabel for="end_at" value="Fim" />
                    <TextInput id="end_at" type="date" class="mt-1 block w-full" v-model="mandateForm.end_at"
                        required />
                    <InputError class="mt-2" :message="mandateForm.errors.end_at" />
                </div>
            </div>

            <div class="mb-4 flex items-center space-x-2">
                <input id="is_current" type="checkbox" v-model="mandateForm.is_current"
                    class="rounded border-gray-300 dark:border-gray-700 text-indigo-600" />
                <InputLabel for="is_current" value="Mandato atual?" />
            </div>

            <div class="flex justify-end">
                <PrimaryButton :class="{ 'opacity-25': mandateForm.processing }"
                    :disabled="mandateForm.processing">
                    Atualizar Detalhes
                </PrimaryButton>
            </div>
        </form>

        <div class="p-4 border rounded-lg">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-md font-semibold">Gerenciar Vereadores</h3>
                <button type="button" @click="showCreateModal = true"
                    class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                    + Cadastrar novo vereador
                </button>
            </div>

            <div class="mb-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
                <div class="lg:col-span-2">
                    <InputLabel for="select-councilor" value="Vereador" />
                    <SelectInput id="select-councilor" v-model="newTerm.user_id" :options="councilorsNotAdded"
                        value-key="id" label-key="name" placeholder="Selecione um vereador"
                        :disable-placeholder="true" />
                </div>
                <div>
                    <InputLabel for="new-party" value="Partido" />
                    <SelectInput id="new-party" v-model="newTerm.category_party_id" :options="parties"
                        value-key="id" label-key="name_party" placeholder="Selecione um partido"
                        :disable-placeholder="false" />
                </div>
                <div>
                    <InputLabel for="term-start" value="Início" />
                    <TextInput id="term-start" type="date" class="mt-1 block w-full" v-model="newTerm.start_date" />
                </div>
                <div>
                    <InputLabel for="term-end" value="Fim (opcional)" />
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
                                Partido</th>
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
                                :title="user.has_conflict ? 'Conflito com outro mandato' : ''">
                                {{ user.name }}
                                <span v-if="user.has_conflict" class="ml-1 text-xs">(conflito)</span>
                            </td>

                            <td class="px-3 py-2 text-sm text-gray-500 dark:text-gray-300">
                                <SelectInput v-if="editingIndex === index" v-model="editBuffer.category_party_id"
                                    :options="parties" value-key="id" label-key="name_party"
                                    placeholder="Sem partido" :disable-placeholder="false"
                                    class="py-0.5 text-sm" />
                                <span v-else>{{ partyName(user.category_party_id) }}</span>
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
                                    <IconButton @click="startEdit(index)" color="blue" title="Editar"
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
        <Modal :show="showCreateModal" max-width="2xl" @close="showCreateModal = false">
            <div class="p-6">
                <h2 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Cadastrar Vereador</h2>

                <form @submit.prevent="submitCreate">
                    <div class="mb-4">
                        <ImageUploadWithCropper v-model="createForm.path_image"
                            :error="createForm.errors.path_image" />
                    </div>

                    <UserForm :form="createForm" :categories="props.categories" :isCreating="true" />
                    <CouncilorForm :form="createForm" :parties="props.parties"
                        :mandates="[props.mandate]" :isCreating="true" />

                    <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <button type="button" @click="showCreateModal = false"
                            class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:underline">
                            Cancelar
                        </button>
                        <PrimaryButton :class="{ 'opacity-25': createForm.processing }"
                            :disabled="createForm.processing">
                            Salvar
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
