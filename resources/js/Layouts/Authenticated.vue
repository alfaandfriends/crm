<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3'
import { Bell, CircleUser, Home, LineChart, Menu, Package, Package2, Search, ShoppingCart, Users } from 'lucide-vue-next'
import { Badge } from '@/Components/ui/badge'
import { Button } from '@/Components/ui/button'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card'
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuLabel, DropdownMenuSeparator, DropdownMenuTrigger } from '@/Components/ui/dropdown-menu'
import { Input } from '@/Components/ui/input'
import { Sheet, SheetContent, SheetTrigger } from '@/Components/ui/sheet'
import { ScrollArea } from '@/Components/ui/scroll-area'
import { onMounted, watch, ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useToast } from '@/Components/ui/toast/use-toast'
import { Toaster } from '@/Components/ui/toast'

const { toast } = useToast();
const page = usePage();

const lastSuccess = ref(null);
const lastError = ref(null);

import { onUpdated } from 'vue';

onUpdated(() => {
    const flash = page.props.flash;
    if (flash?.success) {
        toast({
            title: "Success",
            description: flash.success,
            variant: "success",
            class: "bg-green-50 border-green-200 text-green-800"
        });
    }
    if (flash?.error) {
        toast({
            title: "Error",
            description: flash.error,
            variant: "destructive",
            class: "bg-red-50 border-red-200 text-red-800"
        });
    }
});

// Also show toasts on mount for initial flash messages
onMounted(() => {
    const flash = page.props.flash;
    if (flash?.success) {
        toast({
            title: "Success",
            description: flash.success,
            variant: "success",
            class: "bg-green-50 border-green-200 text-green-800"
        });
    }
    if (flash?.error) {
        toast({
            title: "Error",
            description: flash.error,
            variant: "destructive",
            class: "bg-red-50 border-red-200 text-red-800"
        });
    }
});
</script>

<template>
    <div class="min-h-screen w-full">
        <div class="fixed top-0 right-0 z-[9999]">
            <Toaster />
        </div>
        <div class="flex h-full min-h-screen">
            <!-- Sidebar -->
            <div class="hidden w-[220px] lg:w-[280px] border-r bg-white shadow md:block">
                <div class="flex h-full flex-col">
                    <div class="flex items-center px-4 my-3 py-1 lg:px-6">
                        <Link :href="route('dashboard')">
                            <div class="flex items-center space-x-4">
                                <ApplicationLogo
                                    class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200"
                                />
                                <span class="text-xs lg:text-sm font-semibold">ALFA and Friends CRM</span>
                            </div>
                        </Link>
                    </div>
                    <ScrollArea class="h-full px-2 bg-white border-t">
                        <div class="flex-1 bg-white">
                            <nav class="grid items-start p-2 text-sm font-medium lg:px-3 lg:py-4 gap-y-1 space-y-1">
                                <Link
                                    :href="route('dashboard')"
                                    class="flex items-center gap-3 rounded-lg px-3 py-2 text-slate-800 transition-all"
                                    :class="route().current('dashboard') ? 'bg-slate-800 text-white' : ''"
                                >
                                    <Home class="h-4 w-4" />
                                    Dashboard
                                </Link>
                                <Link
                                    :href="route('pipelines')"
                                    class="flex items-center gap-3 rounded-lg px-3 py-2 text-slate-800 transition-all"
                                    :class="route().current().startsWith('pipelines') || route().current('pipelines') ? 'bg-slate-800 text-white' : ''"
                                >
                                    <LineChart class="h-4 w-4" />
                                    Pipelines
                                </Link>
                            </nav>
                        </div>
                    </ScrollArea>
                </div>
            </div>

            <!-- Main Content -->
            <div class="flex-1 flex flex-col">
                <header class="flex justify-between md:justify-end h-14 md:h-[70px] items-center md:py-6 gap-4 border-b px-4 lg:px-6 bg-white">
                    <Sheet>
                        <SheetTrigger as-child>
                            <Button
                                variant="outline"
                                size="icon"
                                class="shrink-0 md:hidden"
                            >
                                <Menu class="h-5 w-5" />
                                <span class="sr-only">Toggle navigation menu</span>
                            </Button>
                        </SheetTrigger>
                        <SheetContent side="left" class="flex flex-col px-2">
                            <Link :href="route('dashboard')" class="px-2">
                                <div class="flex items-center space-x-4">
                                    <ApplicationLogo
                                        class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200"
                                    />
                                    <span class="text-xs lg:text-sm font-semibold">ALFA and Friends CRM</span>
                                </div>
                            </Link>
                            <nav class="grid items-start p-2 text-sm font-medium lg:px-3 lg:py-4 gap-y-1 space-y-1">
                                <Link
                                    :href="route('dashboard')"
                                    class="flex items-center gap-3 rounded-lg px-3 py-2 text-slate-800 transition-all"
                                    :class="route().current('dashboard') ? 'bg-slate-800 text-white' : ''"
                                >
                                    <Home class="h-4 w-4" />
                                    Dashboard
                                </Link>
                                <Link
                                    :href="route('pipelines')"
                                    class="flex items-center gap-3 rounded-lg px-3 py-2 text-slate-800 transition-all"
                                    :class="route().current('pipelines') ? 'bg-slate-800 text-white' : ''"
                                >
                                    <LineChart class="h-4 w-4" />
                                    Pipelines
                                </Link>
                            </nav>
                        </SheetContent>
                    </Sheet>
                    <DropdownMenu>
                        <DropdownMenuTrigger class="flex items-center space-x-1">
                            <Label class="cursor-pointer">{{ $page.props.auth.user.display_name }}</Label>
                            <Button variant="secondary" size="icon" class="flex rounded-full">
                                <CircleUser class="h-5 w-5" />
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end">
                            <DropdownMenuItem @click="$inertia.post(route('logout'))">Logout</DropdownMenuItem>
                        </DropdownMenuContent>
                    </DropdownMenu>
                </header>
                <main class="flex-1 overflow-auto bg-slate-50 p-4 lg:p-6">
                    <slot />
                </main>
            </div>
        </div>
    </div>
</template>