<script setup>
import { ref, watch, nextTick } from 'vue';
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'

const props = defineProps({
  modelValue: {
    type: [String, Number, Date, Object],
    default: new Date,
  },
  mode: {
    type: String,
    default: 'date'
  },
  format: {
    type: String,
    default: 'yyyy-MM-DD'
  },
  teleport: {
    type: [Boolean, String],
    default: 'body'
  },
  teleportCenter: {
    type: Boolean,
    default: false
  },
  disabled: {
    type: Boolean,
    default: false
  },
  uid: {
    type: String,
    default: '',
    required: false,
  },
  customPosition: {
    type: Function,
    default: null,
    required: false,
  },
  placeholder: {
    type: String,
    default: '',
    required: false,
  },
  error: {
    type: [Boolean, String],
    default: false
  },
});

const emit = defineEmits(['update:modelValue', 'select']);

const internalValue = ref(props.modelValue);
const isInitialized = ref(false);

watch(() => props.modelValue, (newValue) => {
  internalValue.value = newValue;
});

watch(internalValue, (newValue) => {
  emit('update:modelValue', newValue);
  emit('select', newValue);
});

isInitialized.value = true;

const handleSelect = (date) => {
  internalValue.value = date;
  emit('select', date);
};

</script>

<template>
  <VueDatePicker v-if="isInitialized"
    v-model="internalValue"
    :auto-apply="true"
    :clearable="false"
    :enable-time-picker="mode == 'time' ? true : false"
    :format="format"
    :time-picker="mode == 'time' ? true : false"
    :month-picker="mode == 'month' ? true : false"
    :year-picker="mode == 'year' ? true : false"
    :teleport="false"
    :teleport-center="false"
    :is24="false"
    :disabled="disabled"
    :uid="uid" 
    :alt-position="customPosition"
    :placeholder="placeholder"
    @update:model-value="handleSelect"
    :prevent-min-max-navigation="true"
    :position="'bottom'"
  />
  <p v-if="props.error" class="mt-0.5 text-xs text-red-500 font-semibold">
    This field is required.
  </p>
</template>

<style>
.dp__theme_light{
  @apply rounded-lg !important
}
.dp__overlay{
  @apply rounded-lg z-[9999] !important
}
.dp__input_wrap{
  @apply relative border border-gray-200 hover:border-gray-200 rounded-md h-[34px] shadow-sm placeholder:text-black !important
}
.dp__input{
  @apply text-base text-[14px] h-[34px] border border-transparent font-normal font-sans rounded-md disabled:bg-gray-50 disabled:cursor-not-allowed hover:border-transparent placeholder:text-black !important
}
.dp__active_date{
  @apply bg-primary border-primary !important
}
.dp__overlay_cell_active{
  @apply bg-black border-primary !important
}
.dp__instance_calendar{
  @apply py-3 px-4 w-full text-sm !important
}
.dp__today {
  @apply border-primary !important
}
.dp__month_year_row{
  @apply py-6 px-4 !important
}
.dp__month_year_col_nav{
  @apply border border-primary rounded-md px-1 py-0.5 hover:bg-gray-50 !important
}
.dp__inner_nav{
  @apply hover:bg-transparent !important
}
.dp__icon{
  @apply text-primary !important
}
.dp__month_year_wrap{
  @apply space-x-2 px-2 !important
}

.dp__selection_preview{
  @apply hidden !important
}
.dp__action_row{
  @apply hidden !important
}
.dp__action_buttons{
  @apply space-x-1 !important
}
.dp__cancel{
  @apply text-sm text-primary bg-secondary px-2 py-1.5 !important
}
.dp__select{
  @apply text-sm text-secondary bg-primary px-2 py-1.5 !important
}
.dp__pm_am_button{
  @apply text-sm text-secondary bg-primary px-2 py-1.5 !important
}

.dp__selection_grid_header{
  @apply px-3 py-2 !important
}
</style>