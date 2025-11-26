<script setup lang="ts">
import { ref, computed } from "vue";
import AuthenticatedLayout from "@/layouts/AuthenticatedLayout.vue";
import InputLabel from "@/components/Form/InputLabel.vue";
import TextInput from "@/components/Form/TextInput.vue";
import InputError from "@/components/Form/InputError.vue";
import PrimaryButton from "@/components/Common/PrimaryButton.vue";
import { Head, useForm, router } from "@inertiajs/vue3";
import BackButtonRow from "@/components/Common/BackButtonRow.vue";
import SelectInput from "@/components/Form/SelectInput.vue";
import CustomBadge from "@/components/Common/CustomBadge.vue";
import { User } from "@/types/inertia";
import IconButton from "@/components/Itens/IconButton.vue";
import {
    TrashIcon,
    CheckCircleIcon,
    XCircleIcon,
    PlusIcon,
} from "@heroicons/vue/24/outline";
import Modal from "@/components/Common/Modal.vue";
import ConfirmActionModal from "@/components/Common/ConfirmActionModal.vue";

interface Author {
    id: number;
    status_sign: 0 | 1 | 2;
    user_id: number;
    document_id: number;
    user: User;
    document: Document;
}

interface Document {
    id: number;
    name: string;
    protocol_number: string | null;
    document_status_vote_id: number;
    document_status_movement_id: number;
    document_category_id: number;
    authors: Author[];
}

interface Status {
    id: number;
    name: string;
}

interface Category {
    id: number;
    name: string;
}

interface EditData {
    document: Document;
    vote_statuses: Status[];
    movement_statuses: Status[];
    categories: Category[];
    all_available_authors: User[];
}

const props = defineProps<{
    data: EditData;
}>();

const voteStatuses = props.data.vote_statuses || [
    { id: 1, name: "Pendente" },
    { id: 2, name: "Aguardando" },
    { id: 3, name: "Em vista" },
    { id: 4, name: "Em votação" },
    { id: 5, name: "Concluído" },
    { id: 6, name: "Leitura" },
];

const movementStatuses = props.data.movement_statuses || [
    { id: 1, name: "Secretario" },
    { id: 2, name: "Em sessão" },
    { id: 3, name: "Procurador" },
    { id: 4, name: "Comissão Justiça" },
    { id: 5, name: "Comissões" },
    { id: 6, name: "Prefeitura" },
    { id: 7, name: "Em analise" },
    { id: 8, name: "Reprovado" },
];

const signatureStatusMap = {
    0: { title: "Pendente", color: "#f97316" },
    1: { title: "Assinado Clicksign", color: "#22c55e" },
    2: { title: "Assinado Manualmente", color: "#3b82f6" },
};

const currentAuthors = ref<Author[]>(props.data.document.authors);
const showAddAuthorModal = ref(false);
const newAuthorUserId = ref<number | null>(null);

const showConfirmSignature = ref(false);
const signatureActionType = ref<"sign" | "unsign" | null>(null);
const signatureAuthor = ref<Author | null>(null);

const form = useForm({
    _method: "put",
    name: props.data.document.name,
    protocol_number: props.data.document.protocol_number,
    document_status_vote_id: props.data.document.document_status_vote_id,
    document_status_movement_id: props.data.document.document_status_movement_id,
    document_category_id: props.data.document.document_category_id,
    authors: props.data.document.authors.map((author) => author.id),
});

const availableAuthorsOptions = computed(() => {
    const allAvailable = props.data.all_available_authors ?? [];

    const currentAuthorUserIds = currentAuthors.value.map((a) => a.user_id);

    return allAvailable.filter((user) => !currentAuthorUserIds.includes(user.id));
});

const openAddAuthorModal = () => {
    showAddAuthorModal.value = true;
};

const closeModal = () => {
    showAddAuthorModal.value = false;
    newAuthorUserId.value = null;
    showConfirmSignature.value = false;
    signatureActionType.value = null;
    signatureAuthor.value = null;
};

const addAuthorToDocument = () => {
    if (newAuthorUserId.value !== null) {
        const userToAdd = props.data.all_available_authors.find(
            (user) => user.id === newAuthorUserId.value
        );

        if (userToAdd) {
            const newAuthor: Author = {
                id: Date.now(),
                status_sign: 0,
                user_id: userToAdd.id,
                document_id: props.data.document.id,
                user: userToAdd,
                document: props.data.document,
            };
            currentAuthors.value.push(newAuthor);
            form.authors = currentAuthors.value.map((author) => author.id);
            closeModal();
        }
    }
};

const removeAuthor = (authorId: number) => {
    currentAuthors.value = currentAuthors.value.filter(
        (author) => author.id !== authorId
    );
    form.authors = currentAuthors.value.map((author) => author.id);
};

const prepareSignatureAction = (author: Author, type: "sign" | "unsign") => {
    signatureAuthor.value = author;
    signatureActionType.value = type;
    showConfirmSignature.value = true;
};

const confirmSignatureAction = () => {
    if (!signatureAuthor.value || !signatureActionType.value) {
        closeModal();
        return;
    }

    const authorId = signatureAuthor.value.id;
    const newStatus = signatureActionType.value === "sign" ? 2 : 0;

    const index = currentAuthors.value.findIndex((a) => a.id === authorId);
    if (index !== -1) {
        currentAuthors.value[index].status_sign = newStatus;
    }

    router.put(
        route("authors.update_signature_status", authorId),
        {
            status_sign: newStatus,
            _method: "put",
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                console.log(
                    `Status de assinatura do autor ${authorId} atualizado para ${newStatus}.`
                );
            },
            onError: (errors) => {
                console.error("Erro ao atualizar status de assinatura:", errors);
            },
        }
    );

    closeModal();
};

const submit = () => {
    form.authors = currentAuthors.value.map((author) => author.id);
    form.post(route("documents.update", props.data.document.id));
};
</script>

<template>
    <Head :title="`Editar Documento`" />
    <AuthenticatedLayout>
        <BackButtonRow :href="route('documents.index')" />

        <form @submit.prevent="submit">
            <div class="space-y-6">
                <div class="flex flex-col md:flex-row md:space-x-4">
                    <div class="flex-1 mb-4 md:mb-0">
                        <InputLabel for="protocol_number" value="Protocolo" />
                        <TextInput
                            id="protocol_number"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.protocol_number"
                        />
                        <InputError class="mt-2" :message="form.errors.protocol_number" />
                    </div>

                    <div class="flex-1">
                        <InputLabel for="category" value="Categoria do Documento" />
                        <SelectInput
                            v-model="form.document_category_id"
                            :options="data.categories"
                            value-key="id"
                            label-key="name"
                            placeholder="Selecione uma categoria"
                        />
                        <InputError
                            class="mt-2"
                            :message="form.errors.document_category_id"
                        />
                    </div>
                </div>

                <div>
                    <InputLabel for="name" value="Objeto" />
                    <textarea
                        id="name"
                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm"
                        v-model="form.name"
                        required
                        rows="6"
                    ></textarea>
                    <InputError class="mt-2" :message="form.errors.name" />
                </div>

                <div class="flex flex-col md:flex-row md:space-x-4">
                    <div class="flex-1 mb-4 md:mb-0">
                        <InputLabel for="vote_status" value="Status de Votação" />
                        <SelectInput
                            v-model="form.document_status_vote_id"
                            :options="voteStatuses"
                            value-key="id"
                            label-key="name"
                            placeholder="Selecione um status"
                        />
                        <InputError
                            class="mt-2"
                            :message="form.errors.document_status_vote_id"
                        />
                    </div>

                    <div class="flex-1">
                        <InputLabel
                            for="movement_status"
                            value="Status de Movimentação"
                        />
                        <SelectInput
                            v-model="form.document_status_movement_id"
                            :options="movementStatuses"
                            value-key="id"
                            label-key="name"
                            placeholder="Selecione um status"
                        />
                        <InputError
                            class="mt-2"
                            :message="form.errors.document_status_movement_id"
                        />
                    </div>
                </div>
                <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            Autores do Documento
                        </h3>
                        <PrimaryButton @click.prevent="openAddAuthorModal">
                            <PlusIcon class="h-4 w-4 mr-2" />
                            Adicionar Autor
                        </PrimaryButton>
                    </div>

                    <div class="space-y-4">
                        <div
                            v-if="currentAuthors.length === 0"
                            class="text-gray-500 dark:text-gray-400"
                        >
                            Nenhum autor associado a este documento.
                        </div>
                        <div
                            v-for="author in currentAuthors"
                            :key="author.id"
                            class="flex items-center justify-between p-3 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm"
                        >
                            <div class="flex items-center space-x-4">
                                <img
                                    :src="
                                        author.user.path_image ??
                                        'https://smartlegis-production.s3.us-east-1.amazonaws.com/public/default_user.webp'
                                    "
                                    :alt="author.user.name"
                                    class="w-10 h-10 rounded-full object-cover"
                                />

                                <div>
                                    <p
                                        class="text-sm font-semibold text-gray-900 dark:text-gray-100"
                                    >
                                        {{ author.user.name }}
                                    </p>
                                    <CustomBadge
                                        :title="
                                            signatureStatusMap[author.status_sign].title
                                        "
                                        :color="
                                            signatureStatusMap[author.status_sign].color
                                        "
                                    />
                                </div>
                            </div>

                            <div class="flex space-x-2">
                                <IconButton
                                    v-if="author.status_sign !== 2"
                                    @click.prevent="
                                        prepareSignatureAction(author, 'sign')
                                    "
                                    color="blue"
                                    title="Marcar como Assinado Manualmente"
                                    as="button"
                                >
                                    <CheckCircleIcon class="h-5 w-5" />
                                </IconButton>

                                <IconButton
                                    v-if="author.status_sign === 2"
                                    @click.prevent="
                                        prepareSignatureAction(author, 'unsign')
                                    "
                                    color="gray"
                                    title="Remover Assinatura Manual"
                                    as="button"
                                >
                                    <XCircleIcon class="h-5 w-5" />
                                </IconButton>

                                <IconButton
                                    @click.prevent="removeAuthor(author.id)"
                                    color="red"
                                    title="Excluir Autor"
                                    as="button"
                                >
                                    <TrashIcon class="h-5 w-5" />
                                </IconButton>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end mt-6">
                <PrimaryButton
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Salvar Alterações
                </PrimaryButton>
            </div>
        </form>

        <Modal :show="showAddAuthorModal" :max-width="'sm'" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                    Adicionar Novo Autor
                </h2>

                <div class="mt-4">
                    <InputLabel for="new_author" value="Selecionar Usuário" />
                    <SelectInput
                        id="new_author"
                        v-model="newAuthorUserId"
                        :options="availableAuthorsOptions"
                        value-key="id"
                        label-key="name"
                        placeholder="Selecione um usuário"
                        class="mt-1 block w-full"
                    />
                </div>

                <div class="mt-6 flex justify-end">
                    <PrimaryButton
                        @click="addAuthorToDocument"
                        :disabled="!newAuthorUserId"
                        class="ml-3"
                    >
                        Adicionar
                    </PrimaryButton>
                    <button
                        @click="closeModal"
                        class="ml-3 inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150"
                    >
                        Cancelar
                    </button>
                </div>
            </div>
        </Modal>

        <ConfirmActionModal
            :show="showConfirmSignature"
            :title="
                signatureActionType === 'sign'
                    ? 'Confirmar Assinatura Manual'
                    : 'Remover Assinatura Manual'
            "
            :message="
                signatureActionType === 'sign'
                    ? `Tem certeza que deseja marcar '${signatureAuthor?.user.name}' como Assinado Manualmente?`
                    : `Tem certeza que deseja remover a assinatura manual de '${signatureAuthor?.user.name}'? O status voltará para Pendente.`
            "
            confirm-button-text="Confirmar"
            cancel-button-text="Cancelar"
            @close="closeModal"
            @confirm="confirmSignatureAction"
        />
    </AuthenticatedLayout>
</template>
