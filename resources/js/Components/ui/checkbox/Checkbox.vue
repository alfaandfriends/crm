<script setup>
import { computed } from "vue";
import {
  CheckboxIndicator,
  CheckboxRoot,
  useForwardPropsEmits,
} from "radix-vue";
import { CheckIcon } from "@radix-icons/vue";
import { cn } from "@/lib/utils";

const props = defineProps({
  modelValue: { type: Array, required: false, default: () => [] },
  defaultChecked: { type: Boolean, required: false },
  checked: { type: [Boolean, String, Number], required: false },
  disabled: { type: Boolean, required: false },
  required: { type: Boolean, required: false },
  name: { type: String, required: false },
  value: { type: [Number, String, Boolean], required: false },
  id: { type: [Number, String], required: false },
  asChild: { type: Boolean, required: false },
  as: { type: null, required: false },
  class: { type: null, required: false },
});
const emits = defineEmits(["update:checked", "update:modelValue"]);

const delegatedProps = computed(() => {
  const { class: _, ...delegated } = props;

  return delegated;
});

const forwarded = useForwardPropsEmits(delegatedProps, emits);

const handleClick = () => {
  const newValue = props.value;
  const isChecked = props.modelValue.includes(newValue);

  const newModelValue = isChecked
    ? props.modelValue.filter(item => item !== newValue)
    : [...props.modelValue, newValue];

  emits('update:modelValue', newModelValue);
};
</script>

<template>
  <CheckboxRoot
    @click="handleClick"
    v-bind="forwarded"
    :class="
      cn(
        'peer h-4 w-4 shrink-0 rounded-sm border border-slate-200 border-slate-900 shadow focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-slate-950 disabled:cursor-not-allowed disabled:opacity-50 data-[state=checked]:bg-slate-900 data-[state=checked]:text-slate-50 dark:border-slate-800 dark:border-slate-50 dark:focus-visible:ring-slate-300 dark:data-[state=checked]:bg-slate-50 dark:data-[state=checked]:text-slate-900',
        props.class,
      )
    "
  >
    <CheckboxIndicator
      class="flex h-full w-full items-center justify-center text-current"
    >
      <slot>
        <CheckIcon class="h-4 w-4" />
      </slot>
    </CheckboxIndicator>
  </CheckboxRoot>
</template>
