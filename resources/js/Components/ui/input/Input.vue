<script setup>
import { useVModel } from "@vueuse/core";
import { cn } from "@/lib/utils";
import { defineProps, defineEmits, useAttrs } from 'vue';

const props = defineProps({
  defaultValue: { type: [String, Number], required: false },
  modelValue: { type: [String, Number], required: false },
  class: { type: null, required: false },
  error: { type: [String, Boolean] },
  disabled: { type: Boolean, required: false, default: false },
  inputGroup: { type: String, default: '', required: false },
});

const emits = defineEmits(["update:modelValue"]);

const modelValue = useVModel(props, "modelValue", emits, {
  passive: true,
  defaultValue: props.defaultValue,
});

const attrs = useAttrs();
</script>

<template>
  <div class="flex flex-col flex-nowrap items-stretch">
    <span v-if="inputGroup"
      class="flex items-center whitespace-nowrap rounded-md border px-3 shadow-sm bg-gray-50 pt-0.5 text-sm font-medium rounded-r-none border-r-0"
      id="addon-wrapping"
      >{{ props.inputGroup }}
    </span>
    <input
      v-model="modelValue"
      :disabled="disabled"
      :class="
        cn(
          'flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:bg-gray-50',
          props.class,
          {
            'rounded-l-none': props.inputGroup, 
          },
        )
      "
      v-bind="attrs"
    />
    <p class="mt-0.5 text-xs text-red-500 font-semibold" v-if="error">
      This field is required.
    </p>
  </div>
</template>
