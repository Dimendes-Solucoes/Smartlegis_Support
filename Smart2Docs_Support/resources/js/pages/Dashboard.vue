<script setup lang="ts">
import AuthenticatedLayout from "@/layouts/AuthenticatedLayout.vue";
import { Head, usePage } from "@inertiajs/vue3";

const authUser = usePage().props.auth.user;
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div class="mb-6 p-2 md:p-0">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-200">
                Bem-vindo(a) ao Smart2Docs Suporte, {{ authUser.name }}!
            </h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                Este é o seu painel de controle. A partir daqui, você pode gerenciar as
                atividades coorporativas.
            </p>
        </div>
    </AuthenticatedLayout>
</template>
