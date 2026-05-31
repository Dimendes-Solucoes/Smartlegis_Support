<script setup lang="ts">
import AuthenticatedLayout from "@/layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";

const refreshAllForm = useForm({});
const refreshTvForm = useForm({});

const submitRefreshAll = () => {
    refreshAllForm.post(route("maintenance.refresh-all"));
};

const submitRefreshTv = () => {
    refreshTvForm.post(route("maintenance.refresh-tv"));
};
</script>

<template>
    <Head title="Ferramentas" />

    <AuthenticatedLayout>
        <div class="space-y-6">
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-1">
                    Atualizar todas as páginas
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                    Envia um sinal para recarregar todas as páginas abertas desta cidade.
                </p>

                <form @submit.prevent="submitRefreshAll">
                    <button
                        type="submit"
                        :disabled="refreshAllForm.processing"
                        class="inline-flex items-center px-4 py-2 bg-orange-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-700 active:bg-orange-900 focus:outline-none focus:ring ring-orange-300 disabled:opacity-25 transition ease-in-out duration-150"
                    >
                        Atualizar todas as páginas
                    </button>
                </form>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-1">
                    Atualizar TV
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                    Envia um sinal para recarregar somente a TV desta cidade.
                </p>

                <form @submit.prevent="submitRefreshTv">
                    <button
                        type="submit"
                        :disabled="refreshTvForm.processing"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150"
                    >
                        Atualizar TV
                    </button>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
