<script setup>
import { computed } from "vue";
import InputLabel from "./InputLabel.vue";

const props = defineProps({
    id: {
        type: String,
        default: "",
    },
    modelValue: {
        type: [String, Number, Object, null],
        default: "",
    },
    options: {
        type: Array,
        required: true,
        default: () => [],
    },
    valueKey: {
        type: String,
        default: "id",
    },
    label: {
        type: String,
        default: null,
    },
    labelKey: {
        type: String,
        default: "title",
    },
    placeholder: {
        type: String,
        default: "Selecione uma opção",
    },
    required: {
        type: Boolean,
        default: false,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    disablePlaceholder: {
        type: Boolean,
        default: false,
    },
    customClass: {
        type: String,
        default: "",
    },
    formatLabel: {
        type: Function,
        default: null,
    },
});

defineEmits(["update:modelValue"]);

const selectClasses = computed(() => {
    const baseClasses =
        "mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm";
    return props.customClass || baseClasses;
});

const getOptionLabel = (option) => {
    if (props.formatLabel) {
        return props.formatLabel(option);
    }
    return option[props.labelKey];
};
</script>

<template>
    <InputLabel v-if="label" :for="id" :value="label" />

    <select
        :id="id"
        :value="modelValue"
        :class="selectClasses"
        :required="required"
        :disabled="disabled"
        @change="$emit('update:modelValue', $event.target.value)"
    >
        <option value="" :disabled="disablePlaceholder">
            {{ placeholder }}
        </option>
        <option
            v-for="option in options"
            :key="option[valueKey]"
            :value="option[valueKey]"
        >
            <slot name="option" :option="option">
                {{ getOptionLabel(option) }}
            </slot>
        </option>
    </select>
</template>
