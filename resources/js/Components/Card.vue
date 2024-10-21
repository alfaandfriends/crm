<template>
    <Card class="w-full">
        <CardHeader v-if="$slots.title">
            <CardTitle>
                <h3 class="font-semibold leading-none tracking-tight">
                    <slot name="title"></slot>
                </h3>
            </CardTitle>
            <CardDescription v-if="$slots.description">
                <slot name="description"></slot>
            </CardDescription>
        </CardHeader>
        <CardHeader class="py-3" v-else></CardHeader>
        <CardContent class="grid gap-2" v-if="$slots.content">
            <slot name="content">
            </slot>
        </CardContent>
        <template v-if="$slots.footer">
            <CardFooter class="flex justify-end space-x-2">
                <slot name="footer">
                </slot>
            </CardFooter>
        </template>
    </Card>
  </template>

<script setup>
    import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/Components/ui/card'
</script>