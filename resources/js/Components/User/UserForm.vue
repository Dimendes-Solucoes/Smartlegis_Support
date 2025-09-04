<script setup lang="ts">
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';

interface Category {
    id: number;
    name: string;
}

const props = defineProps<{
    form: any;
    categories: Category[];
    isCreating?: boolean;
}>();
</script>

<template>
    <div>
        <div class="md:flex md:space-x-4 mb-4">
            <div class="flex-1 mb-4 md:mb-0">
                <InputLabel for="category_id" value="Categoria" />
                <select id="category_id" v-model="form.category_id"
                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                    <option value="">Selecione uma categoria</option>
                    <option v-for="category in categories" :key="category.id" :value="category.id">
                        {{ category.name }}
                    </option>
                </select>
                <InputError class="mt-2" :message="form.errors.category_id" />

                <div v-if="form.category_id == 5" class="p-1 text-yellow-800 dark:text-yellow-200 text-sm">
                    <p>
                        Ao cadastrar um novo prefeito, o prefeito anterior será automaticamente inativado. Você pode
                        reativá-lo na seção "Trocar Prefeito".
                    </p>
                </div>

                <div v-if="form.category_id == 3" class="p-1 text-yellow-800 dark:text-yellow-200 text-sm">
                    <p>
                        Ao cadastrar um novo presidente, o presidente anterior será automaticamente convertido em
                        vereador.
                    </p>
                </div>
            </div>
        </div>

        <div class="md:flex md:space-x-4 mb-4">
            <div class="flex-1 mb-4 md:mb-0">
                <InputLabel for="name" value="Nome Completo" />
                <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" required autofocus
                    autocomplete="name" />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>
            <div class="flex-1">
                <InputLabel for="nickname" value="Apelido" />
                <TextInput id="nickname" type="text" class="mt-1 block w-full" v-model="form.nickname"
                    autocomplete="nickname" />
                <InputError class="mt-2" :message="form.errors.nickname" />
            </div>
        </div>

        <div class="mb-4">
            <InputLabel for="email" value="Email" />
            <TextInput id="email" type="email" class="mt-1 block w-full" v-model="form.email" required
                autocomplete="username" />
            <InputError class="mt-2" :message="form.errors.email" />

            <div v-if="(form.category_id == 2 || form.category_id == 3) && !props.isCreating"
                class="p-1 text-yellow-800 dark:text-yellow-200 text-sm">
                <p>
                    A alteração do nome ou e-mail do vereador exigirá uma nova assinatura eletrônica, invalidando
                    qualquer documento pendente para assinatura.
                </p>
            </div>
        </div>

        <div class="border-t border-gray-200 dark:border-gray-700 pt-6 mt-6">
            <p v-if="!props.isCreating" class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                Preencha os campos abaixo apenas se desejar alterar a senha.
            </p>
            <div class="md:flex md:space-x-4">
                <div class="flex-1 mb-4 md:mb-0">
                    <InputLabel for="password" :value="props.isCreating ? 'Senha' : 'Nova Senha'" />
                    <TextInput id="password" type="password" class="mt-1 block w-full" v-model="form.password"
                        :required="props.isCreating" autocomplete="new-password" />
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>
                <div class="flex-1">
                    <InputLabel for="password_confirmation" value="Confirmar Senha" />
                    <TextInput id="password_confirmation" type="password" class="mt-1 block w-full"
                        v-model="form.password_confirmation" :required="props.isCreating" autocomplete="new-password" />
                    <InputError class="mt-2" :message="form.errors.password_confirmation" />
                </div>
            </div>
        </div>
    </div>
</template>