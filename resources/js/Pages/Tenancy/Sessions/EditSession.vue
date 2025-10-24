<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InputLabel from "@/Components/Form/InputLabel.vue";
import InputError from "@/Components/Form/InputError.vue";
import PrimaryButton from "@/Components/Common/PrimaryButton.vue";
import { Head, useForm } from "@inertiajs/vue3";
import { format } from "date-fns";
import TextInput from "@/Components/Form/TextInput.vue";
import BackButtonRow from "@/Components/Common/BackButtonRow.vue";
import Talks from "./Talks.vue";
import { Quorum } from "@/types/inertia";
import SelectInput from "@/Components/Form/SelectInput.vue";

interface Session {
    id: number;
    name: string;
    quorums: Quorum[];
    datetime_start: string;
    session_status_id: number;
}

interface SessionStatus {
    id: number;
    name: string;
}

const props = defineProps<{
    session: Session;
    session_statuses: SessionStatus[];
}>();

const formatForInput = (dateString: string) => {
    if (!dateString) return "";
    const date = new Date(dateString);
    return format(date, "yyyy-MM-dd");
};

const form = useForm({
    name: props.session.name,
    datetime_start: formatForInput(props.session.datetime_start),
    session_status_id: props.session.session_status_id,
});

const submit = () => {
    form.put(route("sessions.update", props.session.id));
};
</script>

<template>
    <Head title="Editar Sessão" />

    <AuthenticatedLayout>
        <BackButtonRow :href="route('sessions.index')" />

        <div class="mb-5">
            <form @submit.prevent="submit">
                <div class="mb-4">
                    <InputLabel for="name" value="Nome da Sessão" />
                    <TextInput
                        id="name"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.name"
                        required
                        autofocus
                        autocomplete="name"
                    />
                    <InputError class="mt-2" :message="form.errors.name" />
                </div>

                <div class="flex gap-6">
                    <div class="w-1/2">
                        <InputLabel for="datetime_start" value="Data de Início" />
                        <input
                            id="datetime_start"
                            type="date"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                            v-model="form.datetime_start"
                            required
                        />
                        <InputError class="mt-2" :message="form.errors.datetime_start" />
                    </div>

                    <div class="w-1/2">
                        <InputLabel for="session_status_id" value="Status" />
                        <SelectInput
                            id="session_status_id"
                            v-model="form.session_status_id"
                            :options="session_statuses"
                            value-key="id"
                            label-key="name"
                            placeholder="Selecione o status"
                        />
                        <InputError
                            class="mt-2"
                            :message="form.errors.session_status_id"
                        />
                    </div>
                </div>

                <div class="flex items-center justify-end mt-6">
                    <PrimaryButton
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                    >
                        Salvar
                    </PrimaryButton>
                </div>
            </form>
        </div>

        <Talks :session="session" />
    </AuthenticatedLayout>
</template>
