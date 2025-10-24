<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, usePage } from "@inertiajs/vue3";
import FeatureCard from "@/Components/Dashboard/FeatureCard.vue";
import TextButton from "@/Components/Itens/TextButton.vue";
import {
    ClipboardDocumentListIcon,
    UsersIcon,
    MegaphoneIcon,
    NewspaperIcon,
} from "@heroicons/vue/24/outline";

const authUser = usePage().props.auth.user;
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div class="mb-6 p-2 md:p-0">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-200">
                Bem-vindo(a) ao Smartlegis, {{ authUser.name }}!
            </h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                Este é o seu painel de controle. A partir daqui, você pode gerenciar as
                atividades legislativas.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <FeatureCard title="Sessões" :icon="ClipboardDocumentListIcon">
                <template #description>
                    <p>
                        Crie novas sessões e as gerencie, defina datas e controle a pauta
                        de documentos a serem discutidos e votados.
                    </p>
                </template>
                <template #actions>
                    <TextButton :href="route('sessions.index')">
                        Gerenciar Sessões
                    </TextButton>
                </template>
            </FeatureCard>

            <FeatureCard title="Participantes" :icon="UsersIcon">
                <template #description>
                    <p>
                        Gerencie os participantes do sistema, incluindo Vereadores e
                        Usuários. Cadastre novos membros, edite perfis e controle o status
                        de atividade.
                    </p>
                </template>
                <template #actions>
                    <div class="flex items-center space-x-2">
                        <TextButton :href="route('councilors.index')">
                            Vereadores
                        </TextButton>
                        <TextButton :href="route('users.index')"> Usuários </TextButton>
                    </div>
                </template>
            </FeatureCard>

            <FeatureCard title="Processo Legislativo" :icon="MegaphoneIcon">
                <template #description>
                    <p>
                        Controle as etapas de fala e debate durante uma sessão. Gerencie
                        os inscritos na Tribuna, nas Discussões, Quoruns e mais.
                    </p>
                </template>
                <template #actions>
                    <div class="flex items-center space-x-2">
                        <TextButton :href="route('sessions.index')">
                            Acessar Sessões
                        </TextButton>
                    </div>
                </template>
            </FeatureCard>

            <FeatureCard title="Documentos" :icon="NewspaperIcon">
                <template #description>
                    <p>
                        Organize todos os tipos de documentos legislativos. Gerencie os
                        Documentos e suas Categorias.
                    </p>
                </template>
                <template #actions>
                    <div class="flex items-center space-x-2">
                        <TextButton :href="route('document-categories.index')">
                            Categorias
                        </TextButton>
                        <TextButton :href="route('documents.index')">
                            Documentos
                        </TextButton>
                    </div>
                </template>
            </FeatureCard>
        </div>
    </AuthenticatedLayout>
</template>
