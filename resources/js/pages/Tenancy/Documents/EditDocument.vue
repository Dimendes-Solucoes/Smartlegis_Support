<script setup lang="ts">
import AuthenticatedLayout from "@/layouts/AuthenticatedLayout.vue";
import InputLabel from "@/components/Form/InputLabel.vue";
import TextInput from "@/components/Form/TextInput.vue";
import InputError from "@/components/Form/InputError.vue";
import PrimaryButton from "@/components/Common/PrimaryButton.vue";
import { Head, useForm } from "@inertiajs/vue3";
import BackButtonRow from "@/components/Common/BackButtonRow.vue";
import SelectInput from "@/components/Form/SelectInput.vue";
import { CheckCircleIcon, ClockIcon, UserGroupIcon } from "@heroicons/vue/24/outline";

interface Document {
    id: number;
    name: string;
    protocol_number: string | null;
    document_status_vote_id: number;
    document_status_movement_id: number;
    document_category_id: number;
}
interface Status {
    id: number;
    name: string;
}
interface Category {
    id: number;
    name: string;
}
interface Author {
    id: number;
    user_id: number;
    name: string;
    email: string | null;
    status: number;
}
interface EditData {
    document: Document;
    vote_statuses: Status[];
    movement_statuses: Status[];
    categories: Category[];
    authors: Author[];
}

const props = defineProps<{
    data: EditData;
}>();

const voteStatuses = [
    { id: 1, name: "Pendente" },
    { id: 2, name: "Aguardando" },
    { id: 3, name: "Em vista" },
    { id: 4, name: "Em votação" },
    { id: 5, name: "Concluído" },
    { id: 6, name: "Leitura" },
];

const movementStatuses = [
    { id: 1, name: "Secretario" },
    { id: 2, name: "Em sessão" },
    { id: 3, name: "Procurador" },
    { id: 4, name: "Comissão Justiça" },
    { id: 5, name: "Comissões" },
    { id: 6, name: "Prefeitura" },
    { id: 7, name: "Em analise" },
    { id: 8, name: "Reprovado" },
];

const getSignatureStatusText = (status: number) =>
    ({ 0: "Pendente", 1: "Assinado Digitalmente", 2: "Assinado", 3: "Expirado" } as Record<number, string>)[status] ?? "Desconhecido";

const getSignatureStatusColor = (status: number) =>
    ({
        0: "bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200",
        1: "bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200",
        2: "bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200",
        3: "bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200",
    } as Record<number, string>)[status] ?? "bg-gray-100 text-gray-800";

const form = useForm({
    _method: "put",
    name: props.data.document.name,
    protocol_number: props.data.document.protocol_number,
    document_status_vote_id: props.data.document.document_status_vote_id,
    document_status_movement_id: props.data.document.document_status_movement_id,
    document_category_id: props.data.document.document_category_id,
});

const submit = () => {
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
                        <TextInput id="protocol_number" type="text"
                            class="mt-1 block w-full bg-gray-100 dark:bg-gray-800" v-model="form.protocol_number" />
                        <InputError class="mt-2" :message="form.errors.protocol_number" />
                    </div>

                    <div class="flex-1">
                        <InputLabel for="category" value="Categoria do Documento" />
                        <SelectInput v-model="form.document_category_id" :options="data.categories" value-key="id"
                            label-key="name" placeholder="Selecione uma categoria" />
                        <InputError class="mt-2" :message="form.errors.document_category_id" />
                    </div>
                </div>

                <div>
                    <InputLabel for="name" value="Objeto" />
                    <textarea id="name"
                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm"
                        v-model="form.name" required rows="6"></textarea>
                    <InputError class="mt-2" :message="form.errors.name" />
                </div>

                <div class="flex flex-col md:flex-row md:space-x-4">
                    <div class="flex-1 mb-4 md:mb-0">
                        <InputLabel for="vote_status" value="Status de Votação" />
                        <SelectInput v-model="form.document_status_vote_id" :options="voteStatuses" value-key="id"
                            label-key="name" placeholder="Selecione um status" />
                        <InputError class="mt-2" :message="form.errors.document_status_vote_id" />
                    </div>

                    <div class="flex-1">
                        <InputLabel for="movement_status" value="Status de Movimentação" />
                        <SelectInput v-model="form.document_status_movement_id" :options="movementStatuses"
                            value-key="id" label-key="name" placeholder="Selecione um status" />
                        <InputError class="mt-2" :message="form.errors.document_status_movement_id" />
                    </div>
                </div>

                <!-- ── Tabela de Assinaturas ── -->
                <div>
                    <div class="flex items-center gap-2 mb-3">
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">
                            Autores e Assinaturas
                        </h3>
                    </div>

                    <div v-if="data.authors.length"
                        class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-700/50">
                                <tr>
                                    <th
                                        class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        ID
                                    </th>
                                    <th
                                        class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Autor
                                    </th>
                                    <th
                                        class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Status
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                                <tr v-for="author in data.authors" :key="author.id"
                                    class="hover:bg-gray-50 dark:hover:bg-gray-700/40">
                                    <td class="px-4 py-3 text-gray-500 dark:text-gray-400 font-mono text-xs">
                                        #{{ author.user_id }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <p class="font-medium text-gray-900 dark:text-gray-100">{{ author.name }}</p>
                                        <p v-if="author.email" class="text-xs text-gray-400 mt-0.5">{{ author.email }}
                                        </p>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span
                                            class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-semibold rounded-full"
                                            :class="getSignatureStatusColor(author.status)">
                                            <ExclamationCircleIcon v-if="author.status === 3" class="h-3.5 w-3.5" />
                                            <CheckCircleIcon v-else-if="author.status > 0" class="h-3.5 w-3.5" />
                                            <ClockIcon v-else class="h-3.5 w-3.5" />
                                            {{ getSignatureStatusText(author.status) }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div v-else
                        class="flex flex-col items-center justify-center py-8 rounded-lg border border-dashed border-gray-300 dark:border-gray-600 text-gray-400 dark:text-gray-500">
                        <UserGroupIcon class="h-8 w-8 mb-1 opacity-40" />
                        <p class="text-sm">Nenhum autor cadastrado neste documento.</p>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end mt-6">
                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Salvar Alterações
                </PrimaryButton>
            </div>
        </form>
    </AuthenticatedLayout>
</template>