<script setup>
import { Head, Link } from '@inertiajs/vue3';
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import Card from '@/Components/Card.vue'
</script>

<template>
    <BreezeAuthenticatedLayout>
        <h1 class="text-xl font-medium px-1">New Pipeline</h1>

        <div class="bg-red-50 border border-red-200 text-sm text-red-800 rounded-lg p-4 mb-4" role="alert" v-if="$page.props.errors && Object.keys($page.props.errors).length > 0">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="flex-shrink-0 size-4 mt-0.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <path d="m15 9-6 6"></path>
                        <path d="m9 9 6 6"></path>
                    </svg>
                </div>
                <div class="ms-4">
                    <h3 class="text-sm font-semibold">
                        Error
                    </h3>
                    <div class="mt-2 text-sm text-red-700">
                        <ul class="list-disc space-y-1 ps-5">
                            <li v-for="(error, index) in $page.props.errors" :key="index">
                                {{ error }}
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Basic Information -->
        <Card>
            <template #title>Basic Information</template>
            <template #content>
                <div class="grid grid-cols-1 2xl:grid-cols-4 gap-4">
                    <div>
                        <Label> Assign To </Label>
                        <ComboBox :items="user_list.options" label-property="display_name" value-property="value" @search="findUsernameEmail" v-model="form.assign_to" select-placeholder="" search-placeholder="Search sales person name..." :loading="searching_username_email"> 
                            <template #label="{ item }">
                                <span class="font-medium">{{ item.display_name }}<br><small class="font-normal">{{ item.user_email ? item.user_email : 'Email not available' }}</small></span>
                            </template>
                        </ComboBox>
                    </div>
                    <div>
                        <Label> Start Date </Label>
                        <Datepicker mode="date" v-model="form.date_start" :format="'dd/MM/yyyy'"></Datepicker>
                    </div>
                    <div>
                        <Label> Lead Source </Label>
                        <ComboBox :items="$page.props.lead_sources" label-property="name" value-property="id" v-model="form.lead_source" select-placeholder="" search-placeholder="Search lead source..."></ComboBox>
                    </div>
                </div>
            </template>
        </Card>

        <!-- School Information -->
        <Card>
            <template #title>School Information</template>
            <template #content>
                <div class="grid grid-cols-1 2xl:grid-cols-4 gap-4">
                    <div>
                        <Label for="school_name"> Name </Label>
                        <Input type="text" name="school_name" id="school_name" v-model="form.school_name" autocomplete="off"></Input>
                    </div>
                    <div>
                        <Label for="school_type"> Type </Label>
                        <ComboBox id="school_type" :items="$page.props.school_types" label-property="name" value-property="id" v-model="form.school_type" select-placeholder="School Type" search-placeholder="Search school type..."></ComboBox>
                    </div>
                    <div>
                        <Label for="branch_number"> Branch Number </Label>
                        <Input type="number" min="1" name="branch_number" id="branch_number" v-model="form.branch_number" autocomplete="off"></Input>
                    </div>
                    <div>
                        <Label for="school_address"> Address </Label>
                        <Textarea id="school_address" rows="1" name="address" class="focus:ring-0 focus:border-gray-300 flex-1 block w-full rounded-md sm:text-sm border-gray-300" v-model="form.address" autocomplete="off"></Textarea>
                    </div>
                </div>
            </template>
        </Card>

        <!-- Principal Information -->
        <Card>
            <template #title>Principal Information</template>
            <template #content>
                <div class="grid grid-cols-1 2xl:grid-cols-4 gap-4">
                    <div>
                        <Label for="principal_name"> Name </Label>
                        <Input type="text" name="principal_name" id="principal_name" v-model="form.principal_name" autocomplete="off"></Input>
                    </div>
                    <div>
                        <Label for="principal_phone_number"> Phone Number </Label>
                        <Input type="text" name="principal_phone_number" id="principal_phone_number" v-model="form.principal_phone_number" autocomplete="off"></Input>
                    </div>
                </div>
            </template>
        </Card>
        
        <!-- PIC Information -->
        <Card>
            <template #title>Person-in-Charge Information</template>
            <template #content>
                <div class="grid grid-cols-1 2xl:grid-cols-4 gap-4">
                    <div>
                        <Label for="pic_name"> Name </Label>
                        <Input type="text" name="pic_name" id="pic_name" v-model="form.pic_name" autocomplete="off"></Input>
                    </div>
                    <div>
                        <Label for="pic_phone_number"> Phone Number </Label>
                        <Input type="number" name="pic_phone_number" id="pic_phone_number" v-model="form.pic_phone_number" autocomplete="off"></Input>
                    </div>
                    <div>
                        <Label for="pic_position"> Position </Label>
                        <ComboBox :items="$page.props.pic_positions" label-property="name" value-property="id" v-model="form.pic_position" select-placeholder="" search-placeholder="Search person-in-charge..."></ComboBox>
                    </div>
                    <div>
                        <Label for="pic_email"> Email </Label>
                        <Input type="email" name="pic_email" id="pic_email" v-model="form.pic_email" autocomplete="off"></Input>
                    </div>
                </div>
            </template>
        </Card>
        
        <!-- Status Information -->
        <Card>
            <template #title>Progress Status</template>
            <template #content>
                <div class="grid grid-cols-1 2xl:grid-cols-4 gap-4">
                    <div>
                        <Label> Progress Percentage</Label>
                        <ComboBox :items="$page.props.progress_percentage" label-property="name" value-property="id" v-model="form.progress_percentage" select-placeholder="" search-placeholder="Search status..."></ComboBox>
                    </div>
                </div>
            </template>
        </Card>
        
        <!-- Case Status Information -->
        <Card>
            <template #title>Case Status</template>
            <template #content>
                <div class="grid grid-cols-1 gap-4">
                    <div class="mb-2 col-span-4">
                        <ul class="mt-1 flex flex-col sm:flex-row sm:flex-wrap">
                            <li class="inline-flex items-center gap-x-2.5 py-3 px-4 text-sm font-medium bg-white border text-gray-800 -mt-px first:rounded-t-lg first:mt-0 last:rounded-b-lg sm:-ms-px sm:mt-0 sm:first:rounded-se-none sm:first:rounded-es-lg sm:last:rounded-es-none sm:last:rounded-se-lg" v-for="case_status, case_status_index in $page.props.case_status">
                                <div class="relative flex items-start w-full">
                                    <div class="flex items-center space-x-2">
                                        <Checkbox :id="'case_status_'+case_status.id" type="checkbox" :checked="isCaseStatusChecked(case_status.id)" @click.native="triggerCaseStatus(case_status.id)"></Checkbox>
                                        <Label class="cursor-pointer" :for="'case_status_'+case_status.id">{{ case_status.name }}</Label>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="mb-2 col-span-4">
                        <div class="flex flex-col">
                            <div class="-m-1.5 overflow-x-auto">
                                <div class="p-1.5 min-w-full inline-block align-middle">
                                    <div class="border rounded-lg overflow-hidden dark:border-neutral-700">
                                        <table class="w-full divide-y divide-gray-200">
                                            <thead>
                                                <tr>
                                                    <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase bg-gray-100">Case Status</th>
                                                    <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase bg-gray-100">Date</th>
                                                    <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase bg-gray-100">Remark</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-200">
                                                <tr v-for="case_status in form.case_status" v-if="form.case_status.length">
                                                    <td class="px-6 py-2 whitespace-nowrap text-sm font-medium text-gray-800">{{ case_status.name }}</td>
                                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-800">
                                                        <Datepicker 
                                                        mode="date"
                                                            v-model="case_status.date" 
                                                            :format="'dd/MM/yyyy'"
                                                        />
                                                    </td>
                                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-800">
                                                        <Input type="text" v-model="case_status.remark" autocomplete="off"></Input>
                                                    </td>
                                                </tr>
                                                <tr v-else>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center" colspan="3">No case status added</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </Card>
        
        <!-- Signed Up Program Information -->
        <Card>
            <template #title>Signed Up Program</template>
            <template #content>
                <div class="grid grid-cols-1 gap-4">
                    <div class="">
                        <ul class="mt-1 flex flex-col sm:flex-row sm:flex-wrap">
                            <li class="inline-flex items-center gap-x-2.5 py-3 px-4 text-sm font-medium bg-white border text-gray-800 -mt-px first:rounded-t-lg first:mt-0 last:rounded-b-lg sm:-ms-px sm:mt-0 sm:first:rounded-se-none sm:first:rounded-es-lg sm:last:rounded-es-none sm:last:rounded-se-lg" v-for="program, program_index in $page.props.programs" @change="triggerProgram($event, program.id)">
                                <div class="relative flex items-start w-full">
                                    <div class="flex items-center space-x-2">
                                        <Checkbox :id="'program_'+program.id" type="checkbox" :checked="isProgramChecked(program.id)" @click.native="triggerProgram(program.id)"></Checkbox>
                                        <Label class="cursor-pointer" :for="'program_'+program.id">{{ program.name }}</Label>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="">
                        <div class="flex flex-col">
                            <div class="-m-1.5 overflow-x-auto">
                                <div class="p-1.5 min-w-full inline-block align-middle">
                                    <div class="border rounded-lg overflow-hidden dark:border-neutral-700">
                                        <table class="w-full divide-y divide-gray-200">
                                            <thead>
                                                <tr>
                                                    <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase bg-gray-100">Program Name</th>
                                                    <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase bg-gray-100">Student Numbers</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-200">
                                                <tr v-for="program in form.signed_up_programs" v-if="form.signed_up_programs.length">
                                                    <td class="px-6 py-2 whitespace-nowrap text-sm font-medium text-gray-800">{{ program.name }}</td>
                                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-800">
                                                        <Input type="number" v-model="program.student_numbers" autocomplete="off"></Input>
                                                    </td>
                                                </tr>
                                                <tr v-else>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center" colspan="3">No signed up program added</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </Card>
        
        <!-- Attachments -->
        <Card>
            <template #title>Attachments</template>
            <template #content>
                <div class="grid grid-cols-1 md:grid-cols-2 2xl:grid-cols-4 gap-4">
                    <div class="mb-4" v-if="!form.quotation">
                        <Label class="block text-sm font-semibold text-gray-700"> Quotation </Label>
                        <form class="mt-1 max-w-sm">
                            <Label for="file-input" class="sr-only">Choose file</Label>
                            <input type="file" name="file-input" id="file-input" class="block w-full border border-gray-200 shadow-sm rounded-lg text-sm focus:z-10 focus:border-gray-300    focus:ring-0 disabled:opacity-50 disabled:pointer-events-none
                                file:bg-gray-50 file:border-0
                                file:me-4
                                file:py-3 file:px-4
                                " @change="handleQuotationFileUpload">
                        </form>
                    </div> 
                    <div v-else>
                        <Label class="mb-1 block text-sm font-semibold text-gray-700"> Quotation </Label>
                        <div class="mb-2 flex justify-between items-center shadow-border shadow rounded-lg shadow-gray-300 px-6 py-3">
                            <div class="flex items-center">
                                <div>
                                    <p class="text-sm font-medium text-gray-800 dark:text-white">{{ form.quotation.name }}</p>
                                </div>
                            </div>
                            <div class="items-center">
                                <a class="text-gray-500 hover:text-gray-800 cursor-pointer" @click="form.quotation = ''">
                                    <svg class="flex-shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M3 6h18"></path>
                                        <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                                        <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                                        <line x1="10" x2="10" y1="11" y2="17"></line>
                                        <line x1="14" x2="14" y1="11" y2="17"></line>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="mb-4" v-if="!form.contract">
                        <Label class="block text-sm font-semibold text-gray-700"> Contract </Label>
                        <form class="mt-1 max-w-sm">
                            <Label for="file-input" class="sr-only">Choose file</Label>
                            <input type="file" name="file-input" id="file-input" class="block w-full border border-gray-200 shadow-sm rounded-lg text-sm focus:z-10 focus:border-gray-300    focus:ring-0 disabled:opacity-50 disabled:pointer-events-none
                                file:bg-gray-50 file:border-0
                                file:me-4
                                file:py-3 file:px-4
                                " @change="handleContractFileUpload">
                            </form>
                    </div>
                    <div v-else>
                        <Label class="mb-1 block text-sm font-semibold text-gray-700"> Contract </Label>
                        <div class="mb-2 flex justify-between items-center shadow-border shadow rounded-lg shadow-gray-300 px-6 py-3">
                            <div class="flex items-center">
                                <div>
                                    <p class="text-sm font-medium text-gray-800 dark:text-white">{{ form.contract.name }}</p>
                                </div>
                            </div>
                            <div class="items-center">
                                <a class="text-gray-500 hover:text-gray-800 cursor-pointer" @click="form.contract = ''">
                                    <svg class="flex-shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M3 6h18"></path>
                                        <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                                        <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                                        <line x1="10" x2="10" y1="11" y2="17"></line>
                                        <line x1="14" x2="14" y1="11" y2="17"></line>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </Card>

        <Card>
            <template #content>
                <div class="flex justify-end space-x-2">
                    <Button variant="outline" @click="$inertia.get(route('pipelines'))">
                        Cancel
                    </Button>
                    <Button @click="submit">
                        Save
                    </Button>
                </div>
            </template>
        </Card>
    </BreezeAuthenticatedLayout>
</template>

<script>
import { debounce } from 'vue-debounce'
import Checkbox from '@/Components/ui/checkbox/Checkbox.vue';

export default {
    data(){
        return {
            selected_program: '',
            searching_username_email: false,
            user_list: {
                value: [],
                options: []
            },
            form: {
                assign_to: !!this.$page.props.auth.user.is_admin ? '' : this.$page.props.auth.user.ID,
                date_start: '',
                lead_source: '',
                school_name: '',
                school_type: '',
                branch_number: '',
                address: '',
                principal_name: '',
                principal_phone_number: '',
                pic_name: '',
                pic_position: '',
                pic_phone_number: '',
                pic_email: '',
                progress_percentage: '',
                case_status: [],
                signed_up_programs: [],
                quotation: '',
                contract: ''
            }
        }
    },
    methods: {
        isCaseStatusChecked(case_status_id) {
            return this.form.case_status.some(status => status.id === case_status_id);
        },
        isProgramChecked(program_id) {
            return this.form.signed_up_programs.some(program => program.id === program_id);
        },
        triggerCaseStatus(case_status_id){
            const checked = this.form.case_status.some(status => status.id === case_status_id)
            
            if(!checked){
                this.form.case_status.push({
                    'id': case_status_id,
                    'name': this.$page.props.case_status.find(case_status => case_status.id === case_status_id).name,
                    'date': '',
                    'remark': '',
                })
            }
            else{
                const index = this.form.case_status.findIndex(case_status => case_status.id === case_status_id)
                this.form.case_status.splice(index, 1)
            }
            this.form.case_status.sort((a, b) => a.id - b.id);

        },
        triggerProgram(program_id){
            const checked = this.form.signed_up_programs.some(program => program.id === program_id)
            
            if(!checked){
                this.form.signed_up_programs.push({
                    'id': program_id,
                    'name': this.$page.props.programs.find(program => program.id === program_id).name,
                    'student_numbers': '',
                })
            }
            else{
                const index = this.form.signed_up_programs.findIndex(program => program.id === program_id)
                this.form.signed_up_programs.splice(index, 1)
            }
            this.form.signed_up_programs.sort((a, b) => a.id - b.id);

        },
        handleQuotationFileUpload(event) {
            const file = event.target.files[0];
            if (file && file.type === 'application/pdf') {
                this.form.quotation = file
            } else {
                alert('Please select a PDF file.');
                event.target.value = '';
            }
        },
        handleContractFileUpload(event) {
            const file = event.target.files[0];
            if (file && file.type === 'application/pdf') {
                this.form.contract = file
            } else {
                alert('Please select a PDF file.');
                event.target.value = '';
            }
        },
        findUsernameEmail: debounce(function(query) {
            if(query){
                this.searching_username_email = true
                axios.get(route('users.find_username_email'), {
                    params: {
                        'keyword': query
                    }
                })
                .then((res) => {
                    this.user_list.options = res.data
                    this.searching_username_email = false
                });
            }
        }, 1000),
        submit(){
            this.$inertia.post(route('pipelines.store'), this.form)
        }
    },
}
</script>