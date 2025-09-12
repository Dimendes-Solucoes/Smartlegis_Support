<script setup lang="ts">
import { ref, watch } from 'vue';
import TextInput from '@/Components/TextInput.vue';
import { MagnifyingGlassIcon } from '@heroicons/vue/24/outline';

const props = defineProps<{
    modelValue: string;
    placeholder?: string;
}>();

const emit = defineEmits(['update:modelValue', 'search']);

const localValue = ref(props.modelValue);

watch(localValue, (newValue) => {
    emit('update:modelValue', newValue);
});
</script>

<template>
    <div class="flex items-center space-x-2 w-full">
        <TextInput
            type="text"
            v-model="localValue"
            :placeholder="placeholder"
            class="w-full"
            @keyup.enter="emit('search')"
        />
        <button
            class="p-3 text-white bg-blue-600 dark:bg-blue-500 rounded-md hover:bg-blue-700 dark:hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out"
            @click="emit('search')"
            aria-label="Buscar"
        >
            <MagnifyingGlassIcon class="h-5 w-5" />
        </button>
    </div>
</template>