<script setup lang="ts">
import { ref, computed } from 'vue';
import { ChevronDownIcon, XMarkIcon } from '@heroicons/vue/24/solid';

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

const selectedIds = computed({
    get: () => props.modelValue,
    set: (value) => {
        emit('update:modelValue', value);
    }
});

const selectedOptions = computed(() => {
    return props.options.filter(opt => selectedIds.value.includes(opt.id));
});

const removeOption = (optionId: number) => {
    selectedIds.value = selectedIds.value.filter(id => id !== optionId);
};

</script>

<template>
    <div class="relative">
        <div @click="isOpen = !isOpen"
            class="relative w-full cursor-pointer rounded-md bg-white dark:bg-gray-900 pl-3 pr-10 text-left text-gray-900 dark:text-gray-200 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-700 focus-within:ring-2 focus-within:ring-indigo-500 min-h-[2.625rem] flex items-center flex-wrap gap-1 py-1.5">
            
            <span v-for="option in selectedOptions" :key="`selected-${option.id}`"
                class="inline-flex items-center rounded-md bg-indigo-50 dark:bg-indigo-900/50 px-2 py-1 text-xs font-medium text-indigo-700 dark:text-indigo-300 ring-1 ring-inset ring-indigo-600/20">
                {{ option.name }}
                <button @click.stop="removeOption(option.id)" class="ml-1.5 h-4 w-4 rounded-full inline-flex items-center justify-center text-indigo-400 hover:bg-indigo-200 dark:hover:bg-indigo-800 hover:text-indigo-500">
                    <XMarkIcon class="h-3 w-3" />
                </button>
            </span>
            
            <span v-if="selectedOptions.length === 0" class="text-gray-400">{{ placeholder || 'Selecione' }}</span>
            
            <span class="pointer-events-none absolute inset-y-0 right-0 ml-3 flex items-center pr-2">
                <ChevronDownIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
            </span>
        </div>

        <transition leave-active-class="transition ease-in duration-100" leave-from-class="opacity-100" leave-to-class="opacity-0">
            <div v-if="isOpen"
                class="absolute z-10 mt-1 max-h-56 w-full overflow-auto rounded-md bg-white dark:bg-gray-800 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                <ul class="py-1">
                    <li v-for="option in options" :key="option.id"
                        class="relative cursor-default select-none py-2 pl-3 pr-9 text-gray-900 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">
                        <label class="flex items-center">
                            <input type="checkbox" :value="option.id" v-model="selectedIds"
                                class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                            <span class="ml-3 block truncate">{{ option.name }}</span>
                        </label>
                    </li>
                </ul>
            </div>
        </transition>
    </div>
</template>