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
</script>

<template>
    <div class="grid min-h-screen w-full md:grid-cols-[220px_1fr] lg:grid-cols-[280px_1fr]">
        <div class="hidden border-r bg-white shadow md:block">
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
        <div class="flex flex-col">
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
                    <!-- <DropdownMenuLabel>Hi</DropdownMenuLabel> -->
                    <!-- <DropdownMenuSeparator /> -->
                    <!-- <DropdownMenuItem>Settings</DropdownMenuItem> -->
                    <!-- <DropdownMenuItem>Support</DropdownMenuItem> -->
                    <!-- <DropdownMenuSeparator /> -->
                    <DropdownMenuItem @click="$inertia.post(route('logout'))">Logout</DropdownMenuItem>
                </DropdownMenuContent>
                </DropdownMenu>
            </header>
            <main class="flex flex-1 flex-col gap-4 p-4 lg:gap-6 lg:p-6 bg-slate-50">
            <slot />
                <!-- <div class="flex items-center">
                <h1 class="text-lg font-semibold md:text-2xl">
                    Inventory
                </h1>
                </div>
                <div class="flex flex-1 items-center justify-center rounded-lg border border-dashed shadow-sm">
                <div class="flex flex-col items-center gap-1 text-center">
                </div>
                </div> -->
            </main>
        </div>
    </div>
</template>