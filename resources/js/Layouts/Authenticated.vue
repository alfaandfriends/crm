<!-- <template>
    <header class="sticky top-0 inset-x-0 flex flex-wrap sm:justify-start sm:flex-nowrap z-[48] w-full bg-white shadow-sm border-b text-sm py-2.5 sm:py-4 lg:ps-64">
      <nav class="flex basis-full items-center w-full mx-auto px-4 sm:px-6" aria-label="Global">
        <div class="me-5 lg:me-0 lg:hidden">
			<Link :href="route('dashboard')">
				<BreezeApplicationLogo class="w-20 fill-current text-gray-500" />
			</Link>
        </div>
    
        <div class="w-full flex items-center justify-end ms-auto sm:justify-end sm:gap-x-3 sm:order-3">
          <div class="flex flex-row items-center justify-end gap-2">
            <div class="hs-dropdown [--placement:bottom-right] relative inline-flex">
              <button id="hs-dropdown-with-header" type="button" class="w-[2.375rem] h-[2.375rem] inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-full border border-transparent text-gray-800 hover:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none">
                <img class="inline-block size-[38px] rounded-full ring-2 ring-white" src="https://images.unsplash.com/photo-1568602471122-7832951cc4c5?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=facearea&facepad=2&w=320&h=320&q=80" alt="Image Description">
              </button>
    
              <div class="hs-dropdown-menu transition-[opacity,margin] duration hs-dropdown-open:opacity-100 opacity-0 hidden min-w-60 bg-white shadow-md rounded-lg p-2" aria-labelledby="hs-dropdown-with-header">
                <div class="py-3 px-5 -m-2 bg-gray-100 rounded-t-lg">
                  <p class="text-sm text-gray-500">Signed in as</p>
                  <p class="text-sm font-medium text-gray-800">{{$page.props.auth.user.display_name}}</p>
                </div>
                <div class="mt-2 py-2 first:pt-0 last:pb-0">
                  <Link :href="route('logout')" method="post" as="button" class="w-full flex space-x-2 items-center px-4 py-2 transition cursor-pointer group hover:bg-indigo-100 rounded">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" height="16" width="16"><path d="M377.9 105.9L500.7 228.7c7.2 7.2 11.3 17.1 11.3 27.3s-4.1 20.1-11.3 27.3L377.9 406.1c-6.4 6.4-15 9.9-24 9.9c-18.7 0-33.9-15.2-33.9-33.9l0-62.1-128 0c-17.7 0-32-14.3-32-32l0-64c0-17.7 14.3-32 32-32l128 0 0-62.1c0-18.7 15.2-33.9 33.9-33.9c9 0 17.6 3.6 24 9.9zM160 96L96 96c-17.7 0-32 14.3-32 32l0 256c0 17.7 14.3 32 32 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32l-64 0c-53 0-96-43-96-96L0 128C0 75 43 32 96 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32z"/></svg>
					<span>Logout</span>
                  </Link>
                </div>
              </div>
            </div>
          </div>
        </div>
      </nav>
    </header>
    <div class="sticky top-0 inset-x-0 z-20 bg-white border-y px-4 sm:px-6 md:px-8 lg:hidden">
      <div class="flex justify-between items-center py-2">
        <ol class="ms-3 flex items-center whitespace-nowrap">
          <li class="flex items-center text-sm text-gray-800">
            Dashboard
            <svg class="flex-shrink-0 mx-3 overflow-visible size-2.5 text-gray-400" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M5 1L10.6869 7.16086C10.8637 7.35239 10.8637 7.64761 10.6869 7.83914L5 14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
          </li>
          <li class="text-sm font-semibold text-gray-800 truncate" aria-current="page">
            Pipelines
          </li>
        </ol>
        <button type="button" class="py-2 px-3 flex justify-center items-center gap-x-1.5 text-xs rounded-lg border border-gray-200 text-gray-500 hover:text-gray-600" data-hs-overlay="#application-sidebar" aria-controls="application-sidebar" aria-label="Sidebar">
          <svg class="flex-shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 8L21 12L17 16M3 12H13M3 6H13M3 18H13"/></svg>
          <span class="sr-only">Sidebar</span>
        </button>
      </div>
    </div>
    <div id="application-sidebar" class="hs-overlay [--auto-close:lg]
      hs-overlay-open:translate-x-0
      -translate-x-full transition-all duration-300 transform
      w-[260px]
      hidden
      fixed inset-y-0 start-0 z-[60]
      bg-white border-e border-gray-200
      lg:block lg:translate-x-0 lg:end-auto lg:bottom-0
     
     ">
      <div class="px-6 py-4 bg-white border-b">
		<Link class="flex justify-between items-center space-x-2" :href="route('dashboard')">
			<BreezeApplicationLogo class="h-10 fill-current text-gray-500" />
			<span class="text-md text-slate-600 font-bold">ALFA and Friends</span>
		</Link>
      </div>
    
      <nav class="hs-accordion-group p-4 w-full flex flex-col flex-wrap" data-hs-accordion-always-open>
        <ul class="space-y-1.5">
          <li>
            <a class="flex items-center gap-x-3.5 py-2 px-2.5 text-sm text-neutral-700 rounded-lg hover:bg-indigo-100" :class="route().current('dashboard') || route().current('') ? 'bg-indigo-100' : ''" :href="route('dashboard')">
              <svg class="flex-shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" ><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
              Dashboard
            </a>
          </li>
          <li class="hs-accordion" :class="route().current('sales.*') ? 'active' : ''" id="projects-accordion">
            <button type="button" class="hs-accordion-toggle w-full text-start flex items-center gap-x-3.5 py-2 px-2.5 text-sm text-neutral-700 rounded-lg hover:text-indigo-600" :class="route().current('sales.*') ? 'hs-accordion-active:text-indigo-600 font-semibold' : ''">
              <svg class="flex-shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="7" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
              Sales
    
              <svg class="hs-accordion-active:block ms-auto hidden size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m18 15-6-6-6 6"/></svg>
    
              <svg class="hs-accordion-active:hidden ms-auto block size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
            </button>
    
            <div id="projects-accordion-child" class="hs-accordion-content w-full overflow-hidden transition-[height] duration-300" :class="route().current('sales.*') ? '' : 'hidden'">
              <ul class="pt-2 ps-2">
                <li>
                  <a class="flex items-center gap-x-3.5 py-2 px-6 text-sm text-neutral-700 rounded-lg hover:bg-indigo-100" :class="route().current('sales.pipelines') || route().current('sales.pipelines.*') ? 'bg-indigo-100' : ''" :href="route('sales.pipelines')">
                    Pipelines
                  </a>
                </li>
              </ul>
            </div>
          </li>
        </ul>
      </nav>
    </div>
    <div class="w-full h-screen lg:ps-64 flex flex-col justify-between">
      
        <div class="p-4 sm:py-6 sm:px-8 space-y-4 sm:space-y-6">
            <Toast :toastData="$page.props.flash"></Toast>
            <slot></slot>
        </div>
        <footer class="bg-white border-t shadow w-full z-50">
            <div class="w-full py-6 px-4 sm:px-6 lg:px-8">
                <div class="flex justify-center items-center">
                    <p class="text-sm font-semibold text-gray-800">© 2024 ALFA and Friends - CRM</p>
                </div>
            </div>
        </footer>
    </div>
</template> -->

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
                                :class="route().current('pipelines') ? 'bg-slate-800 text-white' : ''"
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