<script setup>
import { Head, Link } from '@inertiajs/vue3';
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import Card from '@/Components/Card.vue'
import { Table, TableBody, TableCaption, TableCell, TableHead, TableHeader, TableRow } from '@/Components/ui/table'
import Pagination from '@/Components/Pagination.vue'
import { debounce } from 'vue-debounce'
import { MagnifyingGlassIcon } from '@radix-icons/vue';
import { PlusCircle, PlusCircleIcon } from 'lucide-vue-next';
</script>

<template>
	<BreezeAuthenticatedLayout>
        <div class="flex items-center justify-between">
			<div class="relative">
				<MagnifyingGlassIcon class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" />
				<Input type="text" placeholder="Search" class="w-full rounded-lg bg-background pl-8 md:w-[200px] lg:w-[336px]" v-debounce:800ms="search" v-model="params.search"/>
			</div>
            <Button @click="$inertia.get(route('pipelines.create'))">
                <PlusCircleIcon class="h-4 w-4" />
                <span class="ml-1 hidden sm:block">New Pipeline</span>
            </Button>
		</div>
		<Card>
			<template #title>Pipelines</template>
			<template #content>
                <Table>
                    <TableHeader class="bg-gray-100">
                        <TableRow>
                        <TableHead>#</TableHead>
                        <TableHead>School Name</TableHead>
                        <TableHead>School Type</TableHead>
                        <TableHead>Progress</TableHead>
                        <TableHead>Principal Name</TableHead>
                        <TableHead>Phone Number</TableHead>
                        <TableHead>Address</TableHead>
                        <TableHead class="text-center">Action</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-if="$page.props.pipelines.data.length" v-for="pipeline, pipeline_index in $page.props.pipelines.data">
                            <TableCell>{{ $page.props.pipelines.from + pipeline_index }}</TableCell>
                            <TableCell>{{ pipeline.school_name }}</TableCell>
                            <TableCell>{{ pipeline.school_type }}</TableCell>
                            <TableCell>{{ pipeline.progress_percentage }}</TableCell>
                            <TableCell>{{ pipeline.principal_name }}</TableCell>
                            <TableCell>{{ pipeline.principal_phone_number }}</TableCell>
                            <TableCell>{{ pipeline.school_address }}</TableCell>
                            <TableCell class="text-center space-x-1">
								<Button variant="outline" @click="edit(pipeline.id)">
									Edit
								</Button>
								<Button variant="destructive" @click="deletePipeline(pipeline.id)">
									Delete
								</Button>
                            </TableCell>
                        </TableRow>
                        <TableRow v-else>
                            <TableCell class="text-center" colspan="10">
                                <div class="p-3">
                                    No Record Found
                                </div>
                            </TableCell>
                        </TableRow> 
                    </TableBody>
                </Table>
			</template>
		</Card>
		<Pagination :page_data="$page.props.pipelines" :params="params"/>
        <DeleteConfirmation :open="confirmation.is_open" @close="confirmation.is_open = false" :routeName="confirmation.route_name" :id="confirmation.id">
            <template #title>Delete Pipeline</template>
            <template #description>Are you sure want to delete this pipeline?</template>
        </DeleteConfirmation>
		<div id="delete-pipeline" class="hs-overlay hidden size-full fixed top-0 start-0 z-[80] overflow-x-hidden overflow-y-auto pointer-events-none">
			<div class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all sm:max-w-lg sm:w-full m-3 sm:mx-auto">
				<div class="flex flex-col bg-white border shadow-sm rounded-xl pointer-events-auto dark:bg-neutral-800 dark:border-neutral-700 dark:shadow-neutral-700/70">
					<div class="flex justify-between items-center py-3 px-4 border-b dark:border-neutral-700">
						<h3 class="font-bold text-gray-800 dark:text-white">
							Delete Pipeline
						</h3>
						<button type="button" class="flex justify-center items-center size-7 text-sm font-semibold rounded-full border border-transparent text-gray-800 hover:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:hover:bg-neutral-700" data-hs-overlay="#delete-pipeline">
							<span class="sr-only">Close</span>
							<svg class="flex-shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
								<path d="M18 6 6 18"></path>
								<path d="m6 6 12 12"></path>
							</svg>
						</button>
					</div>
					<div class="p-4 overflow-y-auto">
						<p class="mt-1 text-gray-800 dark:text-neutral-400">
							Are you sure you want to delete this pipeline?
						</p>
					</div>
					<div class="flex justify-end items-center gap-x-2 py-3 px-4 border-t dark:border-neutral-700">
						<button type="button" class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-white dark:hover:bg-neutral-800" data-hs-overlay="#delete-pipeline">
							Cancel
						</button>
						<button type="button" class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-red-600 text-white hover:bg-red-700 disabled:opacity-50 disabled:pointer-events-none">
							Delete
						</button>
					</div>
				</div>
			</div>
		</div>
	</BreezeAuthenticatedLayout>
</template>

<script>
import DeleteConfirmation from '@/Components/DeleteConfirmation.vue';

export default {
	Components: {
		Pagination
	},
    props: {
        filter: Object,
    },
	data(){
		return {
			pipeline_id_to_delete: '',
            params: {
                search: this.filter.search ? this.filter.search : '',
            },
            confirmation: {
                is_open: false,
                route_name: '',
                id: ''
            },
		}
	},
	methods: {
		edit(id){
			this.$inertia.get(route('pipelines.edit', id))
		},
		deletePipeline(pipeline_id){
            this.confirmation.route_name    = 'pipelines.destroy'
            this.confirmation.id            = pipeline_id
            this.confirmation.is_open       = true
		},
        search(){
            this.$inertia.get(this.route('pipelines'), this.params, { replace: true, preserveState: true});
        }
	}
}

</script>