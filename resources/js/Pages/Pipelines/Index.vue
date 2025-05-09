<script setup>
import { Head, Link } from '@inertiajs/vue3';
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import Card from '@/Components/Card.vue'
import { Table, TableBody, TableCaption, TableCell, TableHead, TableHeader, TableRow } from '@/Components/ui/table'
import { Badge } from '@/Components/ui/badge'
import Pagination from '@/Components/Pagination.vue'
import { debounce } from 'vue-debounce'
import { MagnifyingGlassIcon } from '@radix-icons/vue';
import { PlusCircle, PlusCircleIcon } from 'lucide-vue-next';
import ComboBox from '@/Components/ComboBox.vue'
import axios from 'axios'
import { ref } from 'vue'

const user_list = ref({
    options: []
})

const searching_username_email = ref(false)

const findUsernameEmail = debounce(function(query) {
    if(query){
        searching_username_email.value = true
        axios.get(route('users.find_username_email'), {
            params: {
                'keyword': query
            }
        })
        .then((res) => {
            user_list.value.options = res.data
            searching_username_email.value = false
        });
    }
}, 1000)

const handleUserSearch = debounce((query) => {
    params.user_filter = query;
    search();
}, 300);
</script>

<template>
	<BreezeAuthenticatedLayout>
        <div class="flex items-center justify-between mb-3">
			<div class="grid grid-cols-1 2xl:grid-cols-4 gap-2">
				<div class="relative">
					<MagnifyingGlassIcon class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" />
					<Input type="text" placeholder="Search" class="w-full rounded-lg bg-background pl-8 md:w-[200px] lg:w-[336px]" v-debounce:800ms="search" v-model="params.search"/>
				</div>
				<ComboBox 
					v-if="$page.props.is_admin"
					:items="user_list.options" 
					label-property="display_name" 
					value-property="value" 
					v-model="params.user_filter" 
					select-placeholder="" 
					search-placeholder="Search sales person name..."
					@update:modelValue="search"
					@search="findUsernameEmail"
					:loading="searching_username_email"
                    class="w-full"
				>
					<template #label="{ item }">
						<span class="font-medium">{{ item.display_name }}<br><small class="font-normal">{{ item.user_email ? item.user_email : 'Email not available' }}</small></span>
					</template>
				</ComboBox>
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
                        <TableHead width="1%">#</TableHead>
                        <TableHead width="30%">School Name</TableHead>
                        <!-- <TableHead>School Type</TableHead> -->
                        <TableHead>Principal Name</TableHead>
                        <TableHead>Phone Number</TableHead>
                        <!-- <TableHead>Address</TableHead> -->
                        <TableHead class="text-center">Contract Status</TableHead>
                        <TableHead class="text-center">Action</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-if="$page.props.pipelines.data.length" v-for="pipeline, pipeline_index in $page.props.pipelines.data">
                            <TableCell>{{ $page.props.pipelines.from + pipeline_index }}</TableCell>
                            <TableCell>
                                <div class="mb-1">
                                    <span>{{ pipeline.school_name }}</span>
                                </div>
                                <div class="">
                                    <Badge variant="">
                                        {{ pipeline.school_type }}
                                    </Badge>
                                </div>
                            </TableCell>
                            <!-- <TableCell></TableCell> -->
                            <TableCell>{{ pipeline.principal_name }}</TableCell>
                            <TableCell>{{ pipeline.principal_phone_number }}</TableCell>
                            <!-- <TableCell class="max-w-[200px] truncate">{{ pipeline.school_address }}</TableCell> -->
                            <TableCell class="text-center">
                                <Badge :variant="pipeline.contract_status === 'Pending' ? 'secondary' : 'default'" 
                                       :class="pipeline.contract_status === 'Pending' ? 'bg-yellow-100 text-yellow-800 hover:bg-yellow-200' : 'bg-green-100 text-green-800 hover:bg-green-200'">
                                    {{ pipeline.contract_status }}
                                </Badge>
                            </TableCell>
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
                user_filter: this.filter.user_filter ? this.filter.user_filter : '',
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