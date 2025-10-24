<script setup lang="ts">
import { ref, computed } from "vue";
import { Head, router } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/layouts/AuthenticatedLayout.vue";
import PrimaryButton from "@/components/Common/PrimaryButton.vue";
import BackButtonRow from "@/components/Common/BackButtonRow.vue";
import SelectInput from "@/components/Form/SelectInput.vue";

interface User {
    id: number;
    name: string;
}

interface Session {
    id: number;
    name: string;
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
const notVotedUsersArray = Array.isArray(props.notVotedUsers)
    ? props.notVotedUsers
    : Object.values(props.notVotedUsers);

const unifiedVotes = computed(() => {
    const votedItems = votesArray.map((vote: any) => ({
        id: vote.id,
        vote_category_id: vote.vote_category_id,
        user: vote.user,
    }));

    const notVotedItems = notVotedUsersArray.map((user: any) => ({
        id: null,
        vote_category_id: null,
        user: user,
    }));

    return [...votedItems, ...notVotedItems];
});

const editableUnifiedVotes = ref(unifiedVotes.value);

const saveVotes = () => {
    isSaving.value = true;

    const votesToSave = editableUnifiedVotes.value.map((item: any) => ({
        id: item.id,
        user_id: item.user.id,
        vote_category_id: item.vote_category_id,
    }));

    const payload = {
        votes: votesToSave,
    };

    router.put(
        route("sessions.documents.update_votes", {
            id: props.session.id,
            document_id: props.document.id,
        }),
        payload,
        {
            onFinish: () => (isSaving.value = false),
        }
    );
};
</script>

<template>
    <Head title="Votos" />

    <AuthenticatedLayout>
        <BackButtonRow :href="route('sessions.index')" />

        <div class="flex items-center justify-end mb-4">
            <PrimaryButton
                @click="saveVotes"
                :disabled="isSaving"
                :class="{ 'opacity-25': isSaving }"
            >
                <span v-if="isSaving">Salvando...</span>
                <span v-else>Salvar Votos</span>
            </PrimaryButton>
        </div>

        <div v-if="editableUnifiedVotes.length > 0" class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th
                            scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                        >
                            Membro
                        </th>
                        <th
                            scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                        >
                            Voto
                        </th>
                    </tr>
                </thead>
                <tbody
                    class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700"
                >
                    <tr
                        v-for="item in editableUnifiedVotes"
                        :key="item.user.id"
                        class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200"
                    >
                        <td
                            class="px-6 py-4 whitespace-norma text-sm font-medium text-gray-900 dark:text-gray-100"
                        >
                            {{ item.user.name }}
                        </td>
                        <td
                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300 w-1/3 md:w-1/3 lg:w-1/5"
                        >
                            <SelectInput
                                v-model="item.vote_category_id"
                                :options="voteCategories"
                                value-key="id"
                                label-key="name"
                                placeholder="Nenhum"
                                custom-class="mt-1 block px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100"
                            />
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div
            v-else
            class="text-center p-10 text-gray-500 dark:text-gray-400 bg-gray-200 dark:bg-gray-700 border-4 border-dashed border-gray-400 dark:border-gray-600 rounded-lg"
        >
            <p class="font-bold text-xl mb-2">Nenhum membro para votar.</p>
            <p>A lista de quórum para este documento está vazia.</p>
        </div>
    </AuthenticatedLayout>
</template>
