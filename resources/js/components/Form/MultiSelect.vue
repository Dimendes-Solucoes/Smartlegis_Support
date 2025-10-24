<script setup lang="ts">
import { ref, computed } from "vue";
import InputLabel from "@/components/Form/InputLabel.vue";
import InputError from "@/components/Form/InputError.vue";

interface Option {
    value: string | number;
    label: string;
}

interface Props {
    modelValue: (string | number)[];
    options: Option[];
    label?: string;
    placeholder?: string;
    error?: string;
    id?: string;
}

const props = withDefaults(defineProps<Props>(), {
    placeholder: "Selecione os itens",
    id: "multiselect",
});

const emit = defineEmits<{
    "update:modelValue": [value: (string | number)[]];
}>();

const isOpen = ref(false);

const toggleDropdown = () => {
    isOpen.value = !isOpen.value;
};

const closeDropdown = () => {
    isOpen.value = false;
};

const toggleOption = (value: string | number) => {
    const currentValues = [...props.modelValue];
    const index = currentValues.indexOf(value);

    if (index > -1) {
        currentValues.splice(index, 1);
    } else {
        currentValues.push(value);
    }

    emit("update:modelValue", currentValues);
};

const isSelected = (value: string | number) => {
    return props.modelValue.includes(value);
};

const displayText = computed(() => {
    if (props.modelValue.length === 0) {
        return props.placeholder;
    }

    const selectedOptions = props.options
        .filter((option) => props.modelValue.includes(option.value))
        .map((option) => option.label);

    if (selectedOptions.length <= 2) {
        return selectedOptions.join(", ");
    }

    return `${selectedOptions.length} itens selecionados`;
});
</script>

<template>
    <div class="w-full">
        <InputLabel v-if="label" :for="id" :value="label" />

        <div class="relative">
            <button
                type="button"
                :id="id"
                @click="toggleDropdown"
                @blur="closeDropdown"
                class="mt-1 w-full flex items-center justify-between px-3 py-2 border border-gray-300 dark:border-gray-700 dark:bg-gray-900 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
            >
                <span
                    :class="
                        modelValue.length === 0
                            ? 'text-gray-400 dark:text-gray-500'
                            : 'text-gray-900 dark:text-gray-300'
                    "
                >
                    {{ displayText }}
                </span>
                <svg
                    class="h-5 w-5 text-gray-400 transition-transform"
                    :class="{ 'rotate-180': isOpen }"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                >
                    <path
                        fill-rule="evenodd"
                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                        clip-rule="evenodd"
                    />
                </svg>
            </button>

            <Transition
                enter-active-class="transition ease-out duration-100"
                enter-from-class="transform opacity-0 scale-95"
                enter-to-class="transform opacity-100 scale-100"
                leave-active-class="transition ease-in duration-75"
                leave-from-class="transform opacity-100 scale-100"
                leave-to-class="transform opacity-0 scale-95"
            >
                <div
                    v-if="isOpen"
                    class="absolute z-10 mt-1 w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-md shadow-lg max-h-60 overflow-y-auto"
                >
                    <div
                        v-for="option in options"
                        :key="option.value"
                        @mousedown.prevent="toggleOption(option.value)"
                        class="flex items-center px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer transition-colors"
                    >
                        <input
                            type="checkbox"
                            :checked="isSelected(option.value)"
                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 pointer-events-none"
                            readonly
                        />
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                            {{ option.label }}
                        </span>
                    </div>

                    <div
                        v-if="options.length === 0"
                        class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400 text-center"
                    >
                        Nenhuma opção disponível
                    </div>
                </div>
            </Transition>
        </div>

        <InputError v-if="error" :message="error" class="mt-2" />
    </div>
</template>
