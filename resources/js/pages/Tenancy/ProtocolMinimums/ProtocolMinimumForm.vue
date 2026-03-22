<script setup lang="ts">
import InputLabel from '@/components/Form/InputLabel.vue';
import TextInput from '@/components/Form/TextInput.vue';
import InputError from '@/components/Form/InputError.vue';
import SelectInput from '@/components/Form/SelectInput.vue';
import { useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

interface Category {
    id: number;
    name: string;
}

const props = defineProps<{
    form: ReturnType<typeof useForm>;
    categories: Category[];
    isEditing: boolean;
}>();

const selectedCategory = computed({
    get: () => props.form.document_category_id as number | null,
    set: (val) => { props.form.document_category_id = val; },
});
</script>

<template>
    <div class="space-y-4">
        <div v-if="!isEditing">
            <InputLabel for="document_category_id" value="Categoria" />
            <SelectInput id="document_category_id" v-model="selectedCategory" :options="categories" value-key="id"
                label-key="name" placeholder="Selecione uma categoria" />
            <InputError :message="form.errors.document_category_id" class="mt-2" />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <InputLabel for="year" value="Ano" />
                <TextInput id="year" type="number" v-model="form.year" class="mt-1 block w-full"
                    placeholder="Ex: 2025" />
                <InputError :message="form.errors.year" class="mt-2" />
            </div>
            <div>
                <InputLabel for="min_protocol" value="Protocolo Mínimo" />
                <TextInput id="min_protocol" type="number" v-model="form.min_protocol" class="mt-1 block w-full"
                    placeholder="Ex: 1" />
                <InputError :message="form.errors.min_protocol" class="mt-2" />
            </div>
        </div>
    </div>
</template>