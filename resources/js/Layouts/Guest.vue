<script setup>
import BreezeApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen flex flex-col justify-center items-center pt-6 sm:pt-0 bg-gray-50">
        <div class="w-full items-center sm:max-w-md mt-6 px-6 py-4 overflow-hidden sm:rounded-lg">
            <div class="flex flex-col justify-center items-center space-y-4">
                <Link href="/">
                    <BreezeApplicationLogo class=" w-20 fill-current text-gray-500" />
                </Link>
                <slot />
            </div>
        </div>
    </div>
</template>
