<script setup>
import { useVModel } from "@vueuse/core";
import { cn } from "@/lib/utils";
import { defineProps, defineEmits, useAttrs } from 'vue';

const props = defineProps({
  class: { type: null, required: false },
  defaultValue: { type: [String, Number], required: false },
  modelValue: { type: [String, Number], required: false },
  disabled: { type: Boolean, required: false, default: false },
  error: { type: [String, Boolean] },
  rows: { type: [String, Number], default: 1 },
});

const emits = defineEmits(["update:modelValue"]);

const modelValue = useVModel(props, "modelValue", emits, {
  passive: true,
  defaultValue: props.defaultValue,
});
const attrs = useAttrs();
</script>

<template>
  <textarea
    v-bind="attrs"
    v-model="modelValue"
    :type="props.type"
    :rows="rows"
    :disabled="disabled"
    :class="
      cn(
        'flex w-full rounded-md border border-slate-200 bg-transparent px-3 py-2 text-sm shadow-sm placeholder:text-slate-500 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-slate-950 disabled:cursor-not-allowed disabled:bg-gray-50',
        props.class,
      )
    "
  />
  <p class="mt-0.5 text-xs text-red-500 font-semibold" v-if="error">
    This field is required.
  </p>
</template>
