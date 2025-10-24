<script setup lang="ts">
import { ref, computed } from 'vue';
import { ChevronDownIcon } from '@heroicons/vue/24/outline';

interface Option {
    id: number;
    name: string;
}

const props = defineProps<{
    options: Option[];
    modelValue: number[];
    placeholder?: string;
}>();

const emit = defineEmits(['update:modelValue']);

const isOpen = ref(false);

const selectedOptions = computed({
    get: () => props.modelValue,
    set: (value) => {
        emit('update:modelValue', value);
    }
});

const buttonLabel = computed(() => {
    if (selectedOptions.value.length === 0) {
        return props.placeholder || 'Selecione';
    }
    if (selectedOptions.value.length === 1) {
        const selected = props.options.find(opt => opt.id === selectedOptions.value[0]);
        return selected?.name || '1 selecionada';
    }
    return `${selectedOptions.value.length} selecionadas`;
});
</script>

<template>
    <div class="relative">
        <button @click="isOpen = !isOpen"
            class="relative w-full cursor-default rounded-md bg-white dark:bg-gray-900 py-2 pl-3 pr-10 text-left text-gray-900 dark:text-gray-200 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 sm:text-sm sm:leading-6">
            <span class="block truncate">{{ buttonLabel }}</span>
            <span class="pointer-events-none absolute inset-y-0 right-0 ml-3 flex items-center pr-2">
                <ChevronDownIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
            </span>
        </button>

        <transition leave-active-class="transition ease-in duration-100" leave-from-class="opacity-100"
            leave-to-class="opacity-0">
            <div v-if="isOpen"
                class="absolute z-10 mt-1 max-h-56 w-full overflow-auto rounded-md bg-white dark:bg-gray-800 py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                <ul>
                    <li v-for="option in options" :key="option.id"
                        class="relative cursor-default select-none py-2 pl-3 pr-9 text-gray-900 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">
                        <label class="flex items-center">
                            <input type="checkbox" :value="option.id" v-model="selectedOptions"
                                class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                            <span class="ml-3 block truncate">{{ option.name }}</span>
                        </label>
                    </li>
                </ul>
            </div>
        </transition>
    </div>
</template>