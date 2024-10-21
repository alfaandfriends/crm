<script setup>
import { AlertDialog, AlertDialogAction, AlertDialogCancel, AlertDialogContent, AlertDialogDescription, AlertDialogFooter, AlertDialogTitle, AlertDialogHeader, AlertDialogTrigger } from '@/Components/ui/alert-dialog'
</script>

<template>
  <AlertDialog :open="open">
    <AlertDialogContent>
      <AlertDialogHeader>
        <AlertDialogTitle v-if="$slots.title">
            <slot name="title"></slot>
        </AlertDialogTitle>
        <AlertDialogDescription>
            <slot name="description"></slot>
        </AlertDialogDescription>
      </AlertDialogHeader>
      <AlertDialogFooter>
        <AlertDialogCancel @click="closeModal()">Cancel</AlertDialogCancel>
        <AlertDialogAction class="bg-red-600 hover:bg-red-500" @click="handleRoute">Continue</AlertDialogAction>
      </AlertDialogFooter>
    </AlertDialogContent>
  </AlertDialog>
</template>

<script>
export default {
    props: {
        open: {
          type: Boolean,
          default: false
        },
        routeName: {
          type: String,
          default: ''
        },
        method: {
          type: String,
          default: 'delete'
        },
        id: {
          type: [String, Number],
          default: ''
        },
        params:{
          type: Object,
          default: {}
        },
    },
    emits: ['close', 'cancel'],
    methods: {
      handleRoute(){
        if (this.id && this.method == 'delete') {
          this.$inertia.delete(route(this.routeName, this.id), {
            preserveState: false,
            preserveScroll: true,
            onSuccess: () => {
              this.$emit('close', true); 
            }
          })
        }
        if (!this.id && this.method == 'post') {
          this.$inertia.post(route(this.routeName), this.params, {
            preserveState: false,
            preserveScroll: true,
            onSuccess: () => {
              this.$emit('close', true); 
            },
          })
        }
      },
      closeModal(){
        this.$emit('close', true)
        this.$emit('cancel', true)
      }
    }
}
</script>