<script setup>
import { defineEmits, defineProps } from 'vue';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/Components/ui/dialog';
import { CaretSortIcon, CheckIcon } from '@radix-icons/vue'

const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false
  },
  classProp: {
    type: String,
    default: ''
  },
  size: {
    type: String,
    default: 'sm'
  },
  interactOutside: {
    type: Function,
    required: false,
    default: () => {}, 
  },
  hideClose: {
    type: Boolean,
    default: false
  },
});

const emit = defineEmits(['update:modelValue', 'close']);
</script>

<template>
  <Dialog :open="modelValue">
    <DialogContent :class="['max-h-[90dvh] p-0 flex flex-col', classProp]" @interactOutside="interactOutside" :hideClose="hideClose">
      <DialogHeader class="p-6 pb-0">
        <DialogTitle class="text-lg font-semibold" v-if="$slots.title">
          <slot name="title"></slot>
        </DialogTitle>
        <DialogDescription>
          <slot name="description"></slot>
        </DialogDescription>
      </DialogHeader>
      <div class="flex-1 overflow-y-auto px-6">
        <slot name="content"></slot>
      </div>
      <DialogFooter class="p-6 pt-0" v-if="$slots.footer">
        <slot name="footer"></slot>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>
