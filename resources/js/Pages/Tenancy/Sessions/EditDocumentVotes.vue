<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import BackButton from '@/Components/BackButton.vue';
import IconButton from '@/Components/Itens/IconButton.vue';
import { TrashIcon } from '@heroicons/vue/24/solid';

interface User {
    id: number;
    name: string;
}

interface Session {
    id: number;
    name: string;
}

interface Vote {
    id: number | null;
    vote_category_id: number | null;
    user: User;
}

interface VoteCategory {
    id: number;
    name: string;
}

interface Document {
    id: number;
    name: string;
    attachment: string;
}

const props = defineProps<{
    session: Session;
    document: Document;
    voteCategories: VoteCategory[];
    votes: any;
    notVotedUsers: any;
}>();

const isSaving = ref(false);

const votesArray = Array.isArray(props.votes) ? props.votes : Object.values(props.votes);
const notVotedUsersArray = Array.isArray(props.notVotedUsers) ? props.notVotedUsers : Object.values(props.notVotedUsers);

const unifiedVotes = computed(() => {
    const votedItems = votesArray.map(vote => ({
        id: vote.id,
        vote_category_id: vote.vote_category_id,
        user: vote.user,
    }));

    const notVotedItems = notVotedUsersArray.map(user => ({
        id: null,
        vote_category_id: null,
        user: user,
    }));

    return [...votedItems, ...notVotedItems];
});

const editableUnifiedVotes = ref(unifiedVotes.value);

const saveVotes = () => {
    isSaving.value = true;

    const votesToSave = editableUnifiedVotes.value.map(item => ({
        id: item.id,
        user_id: item.user.id,
        vote_category_id: item.vote_category_id,
    }));

    const payload = {
        votes: votesToSave
    };

    router.put(route('sessions.documents.update_votes', {
        id: props.session.id,
        document_id: props.document.id
    }), payload, {
        onSuccess: () => alert('Votos atualizados com sucesso!'),
        onError: (errors) => {
            console.error('Erro ao atualizar votos:', errors);
            alert('Ocorreu um erro ao atualizar os votos.');
        },
        onFinish: () => isSaving.value = false,
    });
};

const deleteVote = (item: Vote) => {
    item.vote_category_id = null
}
</script>

<template>

    <Head title="Votos" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <BackButton :href="route('sessions.documents', session.id)" />
                        <PrimaryButton @click="saveVotes" :disabled="isSaving" :class="{ 'opacity-25': isSaving }">
                            <span v-if="isSaving">Salvando...</span>
                            <span v-else>Salvar Votos</span>
                        </PrimaryButton>
                    </div>

                    <p class="mb-4 text-gray-700 dark:text-gray-300">
                        Edite os votos registrados para este documento.
                    </p>

                    <div v-if="editableUnifiedVotes.length > 0" class="space-y-4">
                        <div v-for="item in editableUnifiedVotes" :key="item.user.id"
                            class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg shadow-sm">
                            <div class="flex flex-col md:flex-row md:items-center justify-between">
                                <span class="font-medium text-gray-800 dark:text-gray-200">{{ item.user.name }}</span>
                                <select v-model="item.vote_category_id"
                                    class="mt-2 md:mt-0 px-3 py-2 border rounded-md text-sm dark:bg-gray-900 dark:text-gray-300 dark:border-gray-600">
                                    <option :value="null" disabled>Selecione um voto</option>
                                    <option v-for="category in voteCategories" :key="category.id" :value="category.id">
                                        {{ category.name }}
                                    </option>
                                </select>

                                <IconButton v-if="item.id" @click="deleteVote(item)" color="red" title="Deletar"
                                    class="ml-2">
                                    <TrashIcon class="h-5 w-5" />
                                </IconButton>
                            </div>
                        </div>
                    </div>
                    <div v-else
                        class="text-center text-gray-500 dark:text-gray-400 p-6 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg">
                        Nenhum membro no quórum para este documento.
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>