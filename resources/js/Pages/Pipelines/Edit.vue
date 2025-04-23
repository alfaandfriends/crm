<script setup>
import { Head, Link, usePage, useForm } from '@inertiajs/vue3';
import { ref, defineProps, onMounted } from 'vue';
import { debounce } from 'vue-debounce';
import { format } from 'date-fns';
import { CopyIcon, MailIcon } from 'lucide-vue-next';
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import Card from '@/Components/Card.vue';
import Checkbox from '@/Components/ui/checkbox/Checkbox.vue';
import Dialog from '@/Components/DialogModal.vue';
import { Table, TableBody, TableCaption, TableCell, TableHead, TableHeader, TableRow } from '@/Components/ui/table';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { Textarea } from '@/Components/ui/textarea';
import ComboBox from '@/Components/ComboBox.vue';
import Datepicker from '@/Components/Datepicker.vue';
import { useToast } from '@/Components/ui/toast/use-toast'
import { Toaster } from '@/Components/ui/toast'
import Badge from '@/Components/ui/badge/Badge.vue';

const props = defineProps({
    pipeline_info: {
        type: Object,
        required: true  
    },
    pipeline_progress: {
        type: Array,
        default: () => []
    },
    programs: {
        type: Array,
        default: () => []
    }
});

const page = usePage();
const { toast } = useToast();

// Component state
const show_add_progress = ref(false);
const show_edit_progress = ref(false);
const showContractModal = ref(false);
const edit_progress_index = ref('');
const selected_program = ref('');
const searching_username_email = ref(false);
const progress_alert_message = ref('');
const edit_progress_alert_message = ref('');
const contract_alert_message = ref('');
const error_fields = ref({
    case_status: false,
    date: false,
    programs: false,
    student_numbers: []
});

const error_fields_edit = ref({
    case_status: false,
    date: false,
    programs: false,
    student_numbers: []
});

const contract_error_fields = ref({
    client_name: false,
    client_email: false,
    ssm_number: false,
    contract_date: false,
    address: false,
    right_to_use_fees: [],
    student_fees: [],
    teaching_aid_promo: false,
    lesson_plan_promo: false,
    centralise_training_promo: false,
    inhouse_training_kv_promo: false,
    inhouse_training_north_promo: false
});

// User list state
const user_list = ref({
    value: [],
    options: []
});

// Form state
const form = useForm({
    pipeline_id: props.pipeline_info.id ?? '',
    assign_to: props.pipeline_info.assignee_user_id ?? '',
    assignee_name: props.pipeline_info.assignee_name ?? '',
    date_start: props.pipeline_info.date_start ?? '',
    lead_source: props.pipeline_info.lead_source_id ?? '',
    school_name: props.pipeline_info.school_name ?? '',
    school_type: props.pipeline_info.school_type_id ?? '',
    branch_number: props.pipeline_info.branch_numbers ?? '',
    address: props.pipeline_info.school_address ?? '',
    principal_name: props.pipeline_info.principal_name ?? '',
    principal_phone_number: props.pipeline_info.principal_phone_number ?? '',
    pic_name: props.pipeline_info.pic_name ?? '',
    pic_position: props.pipeline_info.pic_position_id ?? '',
    pic_phone_number: props.pipeline_info.pic_phone_number ?? '',
    pic_email: props.pipeline_info.pic_email ?? '',
    progress_status: props.pipeline_progress ?? [],
    quotation: props.pipeline_info.quotation_file_name ?? '',
    quotation_path: props.pipeline_info.quotation_file_path ?? '',
    delete_quotation: false,
    contract_status: props.pipeline_info.contract_status ?? '',
});

// Contract form state
const contractForm = useForm({
    pipeline_id: props.pipeline_info.id ?? '',
    client_name: '',
    client_email: '',
    ssm_number: '',
    ssm_type: 'SSM',
    contract_date: '',
    address: '',
    right_to_use_fees: [],
    student_fees: [],
    teaching_aid_promo: '',
    lesson_plan_promo: '',
    centralise_training_promo: '',
    inhouse_training_kv_promo: '',
    inhouse_training_north_promo: '',
    link_token: '',
});

// Initialize form data when component is mounted
onMounted(() => {
    // Initialize arrays if they don't exist
    if (!contractForm.right_to_use_fees || contractForm.right_to_use_fees.length === 0) {
        contractForm.right_to_use_fees = [{
            program: '',
            price: '',
            promo_price: ''
        }];
    }

    if (!contractForm.student_fees || contractForm.student_fees.length === 0) {
        contractForm.student_fees = [{
            offering: '',
            price_per_year: '',
            promo_price: ''
        }];
    }
});

// Progress form state
const progress_form = ref({
    case_status: '',
    case_status_name: '',
    date: '',
    remark: '',
    signed_up_programs: []
});

const progress_form_edit = ref({
    case_status: '',
    case_status_name: '',
    date: '',
    remark: '',
    signed_up_programs: []
});

// Contract related methods
const fetchContractData = async () => {
    try {
        const response = await axios.get(route('contract.get_by_pipeline', { pipeline_id: props.pipeline_info.id }));
        if (response.data) {
            contractForm.client_name = response.data.name ?? '';
            contractForm.client_email = response.data.email ?? '';
            contractForm.ssm_number = response.data.ssm_ic ?? '';
            contractForm.ssm_type = response.data.id_type ?? 'SSM';
            contractForm.contract_date = response.data.date ?? new Date().toISOString().split('T')[0];
            contractForm.address = response.data.address ?? '';
            contractForm.right_to_use_fees = response.data.right_to_use_fee ? JSON.parse(response.data.right_to_use_fee) : [{
                program: '',
                price: '',
                promo_price: ''
            }];
            contractForm.student_fees = response.data.student_fee ? JSON.parse(response.data.student_fee) : [{
                offering: '',
                price_per_year: '',
                promo_price: ''
            }];
            contractForm.teaching_aid_promo = response.data.teaching_aid_promo ?? '';
            contractForm.lesson_plan_promo = response.data.lesson_plan_promo ?? '';
            contractForm.centralise_training_promo = response.data.centralise_training_promo ?? '';
            contractForm.inhouse_training_kv_promo = response.data.inhouse_training_kv_promo ?? '';
            contractForm.inhouse_training_north_promo = response.data.inhouse_training_north_promo ?? '';
            contractForm.link_token = response.data.link_token ?? '';
        } else {
            // Set default values for new contract
            contractForm.contract_date = new Date().toISOString().split('T')[0];
            contractForm.link_token = '';
            contractForm.ssm_type = 'SSM';
        }
    } catch (error) {
        console.error('Error fetching contract data:', error);
        // Set default values if there's an error
        contractForm.contract_date = new Date().toISOString().split('T')[0];
        contractForm.link_token = '';
        contractForm.ssm_type = 'SSM';
    }
};

const openContractModal = async () => {
    showContractModal.value = true;
    await fetchContractData();
};

const copyContractLink = () => {
    const contractUrl = route('contract.sign', { id: contractForm.link_token });
    navigator.clipboard.writeText(contractUrl).then(() => {
        toast({
            title: "Success",
            description: "Contract link copied to clipboard!",
        });
    }).catch(err => {
        console.error('Failed to copy link: ', err);
        toast({
            title: "Error",
            description: "Failed to copy link",
            variant: "destructive",
        });
    });
};

const viewContract = () => {
    const contractUrl = route('contract.view', { id: contractForm.link_token });
    window.open(contractUrl, '_blank');
};

// Progress status methods
const openAddProgress = () => {
    show_add_progress.value = true;
};

const editAddProgress = (index) => {
    progress_form_edit.value = JSON.parse(JSON.stringify(form.progress_status[index]));
    edit_progress_index.value = index;
    show_edit_progress.value = true;
};

const isProgramChecked = (program_id) => {
    return progress_form.value.signed_up_programs.some(program => program.id === program_id);
};

const triggerProgram = (program_id) => {
    const checked = progress_form.value.signed_up_programs.some(program => program.id === program_id);
    
    if (!checked) {
        progress_form.value.signed_up_programs.push({
            'id': program_id,
            'name': props.programs.find(program => program.id === program_id).name,
            'student_numbers': '',
        });
    } else {
        const index = progress_form.value.signed_up_programs.findIndex(program => program.id === program_id);
        progress_form.value.signed_up_programs.splice(index, 1);
    }
    progress_form.value.signed_up_programs.sort((a, b) => a.id - b.id);
};

const deleteProgram = (index) => {
    progress_form.value.signed_up_programs.splice(index, 1);
};

const isProgramCheckedEdit = (program_id) => {
    return progress_form_edit.value.signed_up_programs.some(program => program.id === program_id);
};

const triggerProgramEdit = (program_id) => {
    const checked = progress_form_edit.value.signed_up_programs.some(program => program.id === program_id);
    
    if (!checked) {
        progress_form_edit.value.signed_up_programs.push({
            'id': program_id,
            'name': props.programs.find(program => program.id === program_id).name,
            'student_numbers': '',
        });
    } else {
        const index = progress_form_edit.value.signed_up_programs.findIndex(program => program.id === program_id);
        progress_form_edit.value.signed_up_programs.splice(index, 1);
    }
    progress_form_edit.value.signed_up_programs.sort((a, b) => a.id - b.id);
};

const deleteProgramEdit = (index) => {
    progress_form_edit.value.signed_up_programs.splice(index, 1);
};

const addProgress = () => {
    // Reset error states
    error_fields.value = {
        case_status: false,
        date: false,
        programs: false,
        student_numbers: []
    };

    // Validate case status
    if (!progress_form.value.case_status) {
        progress_alert_message.value = 'Please select a case status';
        error_fields.value.case_status = true;
        return;
    }

    // Validate date
    if (!progress_form.value.date) {
        progress_alert_message.value = 'Please select a date';
        error_fields.value.date = true;
        return;
    }

    // Validate signed up programs
    if (!progress_form.value.signed_up_programs || progress_form.value.signed_up_programs.length === 0) {
        progress_alert_message.value = 'Please select at least one program';
        error_fields.value.programs = true;
        return;
    }

    // Validate student numbers for each program
    for (const program of progress_form.value.signed_up_programs) {
        if (!program.student_numbers) {
            progress_alert_message.value = `Please enter student numbers for ${program.name}`;
            error_fields.value.student_numbers.push(program.id);
            return;
        }
    }
    
    // Initialize progress_status as an array if it doesn't exist
    if (!form.progress_status) {
        form.progress_status = [];
    }
    
    form.progress_status.push(JSON.parse(JSON.stringify(progress_form.value)));
    clearForm();
    show_add_progress.value = false;
    progress_alert_message.value = '';
};

const editProgress = () => {
    // Reset error states
    error_fields_edit.value = {
        case_status: false,
        date: false,
        programs: false,
        student_numbers: []
    };

    // Validate case status
    if (!progress_form_edit.value.case_status) {
        edit_progress_alert_message.value = 'Please select a case status';
        error_fields_edit.value.case_status = true;
        return;
    }

    // Validate date
    if (!progress_form_edit.value.date) {
        edit_progress_alert_message.value = 'Please select a date';
        error_fields_edit.value.date = true;
        return;
    }

    // Validate signed up programs
    if (!progress_form_edit.value.signed_up_programs || progress_form_edit.value.signed_up_programs.length === 0) {
        edit_progress_alert_message.value = 'Please select at least one program';
        error_fields_edit.value.programs = true;
        return;
    }

    // Validate student numbers for each program
    for (const program of progress_form_edit.value.signed_up_programs) {
        if (!program.student_numbers) {
            edit_progress_alert_message.value = `Please enter student numbers for ${program.name}`;
            error_fields_edit.value.student_numbers.push(program.id);
            return;
        }
    }
    
    // Initialize progress_status as an array if it doesn't exist
    if (!form.progress_status) {
        form.progress_status = [];
    }
    
    form.progress_status[edit_progress_index.value] = JSON.parse(JSON.stringify(progress_form_edit.value));
    clearForm();
    show_edit_progress.value = false;
    edit_progress_alert_message.value = '';
};

const clearForm = () => {
    progress_form.value.case_status = '';
    progress_form.value.case_status_name = '';
    progress_form.value.date = '';
    progress_form.value.remark = '';
    progress_form.value.signed_up_programs = [];
};

const deleteProgressStatus = (index) => {
    // Initialize progress_status as an array if it doesn't exist
    if (!form.progress_status) {
        form.progress_status = [];
    }
    
    form.progress_status.splice(index, 1);
};

// File handling methods
const handleQuotationFileUpload = (event) => {
    const file = event.target.files[0];
    if (file && file.type === 'application/pdf') {
        form.value.quotation = file;
    } else {
        alert('Please select a PDF file.');
        event.target.value = '';
    }
};

const handleContractFileUpload = (event) => {
    const file = event.target.files[0];
    if (file && file.type === 'application/pdf') {
        form.value.contract = file;
    } else {
        alert('Please select a PDF file.');
        event.target.value = '';
    }
};

const downloadQuotation = async () => {
    if (!form.value.quotation.name) {
        const response = await axios.get(form.value.quotation_path, {
            responseType: 'blob'
        });
        const blob = new Blob([response.data], { type: response.data.type });
        const url = URL.createObjectURL(blob);
        window.open(url, '_blank');
    } else {
        createBlob(form.value.quotation);
    }
};

const downloadContract = async () => {
    if (!form.value.contract.name) {
        const response = await axios.get(form.value.contract_path, {
            responseType: 'blob'
        });
        const blob = new Blob([response.data], { type: response.data.type });
        const url = URL.createObjectURL(blob);
        window.open(url, '_blank');
    } else {
        createBlob(form.value.contract);
    }
};

const createBlob = (file) => {
    if (file) {
        const blobUrl = URL.createObjectURL(file);
        window.open(blobUrl, '_blank');
        setTimeout(() => {
            URL.revokeObjectURL(blobUrl);
        }, 100);
    }
};

// User search method
const findUsernameEmail = debounce(function(query) {
    if (query) {
        searching_username_email.value = true;
        axios.get(route('users.find_username_email'), {
            params: {
                'keyword': query
            }
        })
        .then((res) => {
            user_list.value.options = res.data;
            searching_username_email.value = false;
        });
    }
}, 1000);

// Form submission
const submit = () => {
    // Ensure progress_status is properly formatted before submission
    const formattedForm = {
        ...form.data(),
        progress_status: form.progress_status.map(status => ({
            ...status,
            date: status.date ? new Date(status.date).toISOString() : null
        }))
    };

    form.post(route('pipelines.update'), {
        data: formattedForm,
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            // Show success message or handle success
        },
        onError: (errors) => {
            // Handle errors if needed
        }
    });
};

// Fee management methods
const addRightToUseFee = () => {
    if (!contractForm.right_to_use_fees) {
        contractForm.right_to_use_fees = [];
    }
    contractForm.right_to_use_fees.push({
        program: '',
        price: '',
        promo_price: ''
    });
};

const addStudentFee = () => {
    if (!contractForm.student_fees) {
        contractForm.student_fees = [];
    }
    contractForm.student_fees.push({
        offering: '',
        price_per_year: '',
        promo_price: ''
    });
};

const removeRightToUseFee = (index) => {
    contractForm.right_to_use_fees.splice(index, 1);
};

const removeStudentFee = (index) => {
    contractForm.student_fees.splice(index, 1);
};

const handleSave = () => {
    // Reset error states
    contract_error_fields.value = {
        client_name: false,
        client_email: false,
        ssm_number: false,
        contract_date: false,
        address: false,
        right_to_use_fees: [],
        student_fees: [],
        teaching_aid_promo: false,
        lesson_plan_promo: false,
        centralise_training_promo: false,
        inhouse_training_kv_promo: false,
        inhouse_training_north_promo: false
    };

    let hasErrors = false;
    let errorMessage = '';

    // Validate client name
    if (!contractForm.client_name) {
        contract_error_fields.value.client_name = true;
        hasErrors = true;
        errorMessage = 'Please enter client name';
    }

    // Validate client email
    if (!contractForm.client_email) {
        contract_error_fields.value.client_email = true;
        hasErrors = true;
        errorMessage = 'Please enter client email';
    }

    // Validate SSM number
    if (!contractForm.ssm_number) {
        contract_error_fields.value.ssm_number = true;
        hasErrors = true;
        errorMessage = 'Please enter SSM/IC number';
    }

    // Validate contract date
    if (!contractForm.contract_date) {
        contract_error_fields.value.contract_date = true;
        hasErrors = true;
        errorMessage = 'Please select contract date';
    }

    // Validate address
    if (!contractForm.address) {
        contract_error_fields.value.address = true;
        hasErrors = true;
        errorMessage = 'Please enter address';
    }

    // Validate right to use fees
    for (let i = 0; i < contractForm.right_to_use_fees.length; i++) {
        const fee = contractForm.right_to_use_fees[i];
        if (!fee.program || !fee.price || !fee.promo_price) {
            contract_error_fields.value.right_to_use_fees.push(i);
            hasErrors = true;
            errorMessage = 'Please fill in all required fields';
        } else if (isNaN(fee.price) || isNaN(fee.promo_price)) {
            contract_error_fields.value.right_to_use_fees.push(i);
            hasErrors = true;
            errorMessage = 'Please enter valid numbers for prices';
        }
    }

    // Validate student fees
    for (let i = 0; i < contractForm.student_fees.length; i++) {
        const fee = contractForm.student_fees[i];
        if (!fee.offering || !fee.price_per_year || !fee.promo_price) {
            contract_error_fields.value.student_fees.push(i);
            hasErrors = true;
            errorMessage = 'Please fill in all required fields';
        } else if (isNaN(fee.price_per_year) || isNaN(fee.promo_price)) {
            contract_error_fields.value.student_fees.push(i);
            hasErrors = true;
            errorMessage = 'Please enter valid numbers for prices';
        }
    }

    // Validate supplementary fees
    const validateNumericField = (value, fieldName) => {
        if (!value) {
            contract_error_fields.value[fieldName] = true;
            hasErrors = true;
            errorMessage = 'Please fill in all promo prices for supplementary fees';
            return false;
        }
        if (isNaN(value)) {
            contract_error_fields.value[fieldName] = true;
            hasErrors = true;
            errorMessage = 'Please enter valid numbers for all prices';
            return false;
        }
        return true;
    };

    validateNumericField(contractForm.teaching_aid_promo, 'teaching_aid_promo');
    validateNumericField(contractForm.lesson_plan_promo, 'lesson_plan_promo');
    validateNumericField(contractForm.centralise_training_promo, 'centralise_training_promo');
    validateNumericField(contractForm.inhouse_training_kv_promo, 'inhouse_training_kv_promo');
    validateNumericField(contractForm.inhouse_training_north_promo, 'inhouse_training_north_promo');

    if (hasErrors) {
        contract_alert_message.value = errorMessage;
        return;
    }

    if (!contractForm.link_token) {
        // Create new contract
        contractForm.post(route('contract.store'), {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                contract_alert_message.value = '';
                fetchContractData(); // Refresh the data after saving
            }
        });
    } else {
        // Update existing contract
        contractForm.post(route('contract.update', { id: contractForm.link_token }), {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                contract_alert_message.value = '';
                fetchContractData(); // Refresh the data after saving
            }
        });
    }
};

const sendContract = () => {
    contractForm.post(route('contract.send_contract', { id: contractForm.pipeline_id }));
};
</script>

<template>
    <BreezeAuthenticatedLayout>
        <div class="space-y-6">
            <!-- Header Section -->
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-medium">Edit Pipeline</h1>
                <Button @click="openContractModal" class="gap-2">
                    <svg class="flex-shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" x2="8" y1="13" y2="13"></line>
                        <line x1="16" x2="8" y1="17" y2="17"></line>
                        <line x1="10" x2="8" y1="9" y2="9"></line>
                    </svg>
                    <span class="hidden sm:inline">Online Contract</span>
                </Button>
            </div>

            <!-- Error Messages -->
            <div v-if="$page.props.errors && Object.keys($page.props.errors).length > 0" class="bg-red-50 border border-red-200 text-sm text-red-800 rounded-lg p-4" role="alert">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="flex-shrink-0 size-4 mt-0.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <path d="m15 9-6 6"></path>
                            <path d="m9 9 6 6"></path>
                        </svg>
                    </div>
                    <div class="ms-4">
                        <h3 class="text-sm font-semibold">Error</h3>
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

            <!-- Main Form Sections -->
            <Card>
                <template #title>Basic Information</template>
                <template #content>
                    <div class="grid grid-cols-1 2xl:grid-cols-3 gap-4">
                        <div>
                            <Label>Assign To</Label>
                            <ComboBox 
                                :items="user_list.options" 
                                label-property="display_name" 
                                value-property="value" 
                                @search="findUsernameEmail" 
                                v-model="form.assign_to" 
                                :selectPlaceholder="form.assignee_name" 
                                search-placeholder="Search sales person name..." 
                                :loading="searching_username_email"
                            > 
                                <template #label="{ item }">
                                    <span class="font-medium">{{ item.display_name }}<br><small class="font-normal">{{ item.user_email ? item.user_email : 'Email not available' }}</small></span>
                                </template>
                            </ComboBox>
                        </div>
                        <div>
                            <Label>Start Date</Label>
                            <Datepicker mode="date" v-model="form.date_start" :format="'dd/MM/yyyy'"></Datepicker>
                        </div>
                        <div>
                            <Label>Lead Source</Label>
                            <ComboBox 
                                :items="$page.props.lead_sources" 
                                label-property="name" 
                                value-property="id" 
                                v-model="form.lead_source" 
                                select-placeholder="" 
                                search-placeholder="Search lead source..."
                            ></ComboBox>
                        </div>
                    </div>
                </template>
            </Card>

            <!-- School Information Section -->
            <Card>
                <template #title>School Information</template>
                <template #content>
                    <div class="space-y-6">
                        <!-- School Details -->
                        <div class="grid grid-cols-1 2xl:grid-cols-4 gap-4">
                            <div>
                                <Label for="school_name">Name</Label>
                                <Input type="text" name="school_name" id="school_name" v-model="form.school_name" autocomplete="off"></Input>
                            </div>
                            <div>
                                <Label for="school_type">Type</Label>
                                <ComboBox 
                                    id="school_type" 
                                    :items="$page.props.school_types" 
                                    label-property="name" 
                                    value-property="id" 
                                    v-model="form.school_type" 
                                    select-placeholder="School Type" 
                                    search-placeholder="Search school type..."
                                ></ComboBox>
                            </div>
                            <div>
                                <Label for="branch_number">Branch Number</Label>
                                <Input type="number" min="1" name="branch_number" id="branch_number" v-model="form.branch_number" autocomplete="off"></Input>
                            </div>
                            <div>
                                <Label for="school_address">Address</Label>
                                <Textarea 
                                    id="school_address" 
                                    rows="1" 
                                    name="address" 
                                    class="focus:ring-0 focus:border-gray-300 flex-1 block w-full rounded-md sm:text-sm border-gray-300" 
                                    v-model="form.address" 
                                    autocomplete="off"
                                ></Textarea>
                            </div>
                        </div>

                        <!-- Principal Information -->
                        <div class="border-t border-dashed border-gray-400 pt-6">
                            <h3 class="text-sm font-semibold text-gray-700 mb-4">Principal Information</h3>
                            <div class="grid grid-cols-1 2xl:grid-cols-2 gap-4">
                                <div>
                                    <Label for="principal_name">Name</Label>
                                    <Input type="text" name="principal_name" id="principal_name" v-model="form.principal_name" autocomplete="off"></Input>
                                </div>
                                <div>
                                    <Label for="principal_phone_number">Phone Number</Label>
                                    <Input type="text" name="principal_phone_number" id="principal_phone_number" v-model="form.principal_phone_number" autocomplete="off"></Input>
                                </div>
                            </div>
                        </div>

                        <!-- PIC Information -->
                        <div class="border-t border-dashed border-gray-400 pt-6">
                            <h3 class="text-sm font-semibold text-gray-700 mb-4">Person-in-Charge Information</h3>
                            <div class="grid grid-cols-1 2xl:grid-cols-4 gap-4">
                                <div>
                                    <Label for="pic_name">Name</Label>
                                    <Input type="text" name="pic_name" id="pic_name" v-model="form.pic_name" autocomplete="off"></Input>
                                </div>
                                <div>
                                    <Label for="pic_phone_number">Phone Number</Label>
                                    <Input type="number" name="pic_phone_number" id="pic_phone_number" v-model="form.pic_phone_number" autocomplete="off"></Input>
                                </div>
                                <div>
                                    <Label for="pic_position">Position</Label>
                                    <ComboBox 
                                        :items="$page.props.pic_positions" 
                                        label-property="name" 
                                        value-property="id" 
                                        v-model="form.pic_position" 
                                        select-placeholder="" 
                                        search-placeholder="Search person-in-charge..."
                                    ></ComboBox>
                                </div>
                                <div>
                                    <Label for="pic_email">Email</Label>
                                    <Input type="email" name="pic_email" id="pic_email" v-model="form.pic_email" autocomplete="off"></Input>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </Card>

            <!-- Progress Status Section -->
            <Card>
                <template #title>
                    <div class="flex items-center justify-between">
                        <span>Progress Status</span>
                        <Button @click="openAddProgress" class="sm:gap-2">
                            <svg class="flex-shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M5 12h14"></path>
                                <path d="M12 5v14"></path>
                            </svg>
                            <span class="hidden sm:inline">Add Progress</span>
                        </Button>
                    </div>
                </template>
                <template #content>
                    <!-- Mobile List View -->
                    <div class="sm:hidden space-y-4">
                        <div v-if="form.progress_status.length" v-for="progress_status, progress_status_index in form.progress_status" class="bg-white p-4 rounded-lg border border-gray-200">
                            <div class="space-y-3">
                                <div class="flex justify-between items-start">
                                    <div class="text-sm font-medium text-gray-900">{{ progress_status.date ? format(new Date(progress_status.date), 'dd/MM/yyyy') : '' }}</div>
                                    <div class="flex space-x-2">
                                        <Button variant="outline" size="sm" @click="editAddProgress(progress_status_index)">Edit</Button>
                                        <Button variant="destructive" size="sm" @click="deleteProgressStatus(progress_status_index)">Delete</Button>
                                    </div>
                                </div>
                                <div>
                                    <div class="text-sm text-gray-500">Case Status</div>
                                    <div class="text-sm font-medium">{{ progress_status.case_status_name }}</div>
                                </div>
                                <div>
                                    <div class="text-sm text-gray-500">Remark</div>
                                    <div class="text-sm font-medium">{{ progress_status.remark }}</div>
                                </div>
                                <div>
                                    <div class="text-sm text-gray-500">Signed Up Programs</div>
                                    <div class="flex gap-1 flex-wrap mt-1">
                                        <Badge variant="outline" v-for="program in progress_status.signed_up_programs">
                                            <span>{{ program.name }} - <span class="font-normal">{{ program.student_numbers }} students</span></span>
                                        </Badge>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center p-4 bg-gray-50 rounded-lg">No Record Found</div>
                    </div>

                    <!-- Desktop Table View -->
                    <Table class="hidden sm:table">
                        <TableHeader class="bg-gray-100">
                            <TableRow>
                                <TableHead class="px-6">Date</TableHead>
                                <TableHead>Case Status</TableHead>
                                <TableHead>Remark</TableHead>
                                <TableHead>Signed Up Programs</TableHead>
                                <TableHead class="text-center">Action</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-if="form.progress_status.length" v-for="progress_status, progress_status_index in form.progress_status">
                                <TableCell class="px-6">{{ progress_status.date ? format(new Date(progress_status.date), 'dd/MM/yyyy') : '' }}</TableCell>
                                <TableCell>{{ progress_status.case_status_name }}</TableCell>
                                <TableCell>{{ progress_status.remark }}</TableCell>
                                <TableCell class="max-w-xs">
                                    <div class="flex gap-1 flex-wrap">
                                        <Badge variant="outline" v-for="program in progress_status.signed_up_programs">
                                            <span>{{ program.name }} - <span class="font-normal">{{ program.student_numbers }} students</span></span>
                                        </Badge>
                                    </div>
                                </TableCell>
                                <TableCell class="text-center space-x-1">
                                    <Button variant="outline" @click="editAddProgress(progress_status_index)">Edit</Button>
                                    <Button variant="destructive" @click="deleteProgressStatus(progress_status_index)">Delete</Button>
                                </TableCell>
                            </TableRow>
                            <TableRow v-else>
                                <TableCell class="text-center" colspan="10">
                                    <div class="p-3">No Record Found</div>
                                </TableCell>
                            </TableRow> 
                        </TableBody>
                    </Table>
                </template>
            </Card>

            <!-- Attachments Section -->
            <Card>
                <template #title>Attachments</template>
                <template #content>
                    <div class="grid grid-cols-1 2xl:grid-cols-2 gap-4">
                        <!-- Quotation -->
                        <div class="mb-4" v-if="!form.quotation">
                            <Label class="block text-sm font-semibold text-gray-700">Quotation</Label>
                            <form class="mt-1">
                                <Label for="file-input" class="sr-only">Choose file</Label>
                                <input type="file" name="file-input" id="file-input" class="block w-full border border-gray-200 shadow-sm rounded-lg text-sm focus:z-10 focus:border-gray-300 focus:ring-0 disabled:opacity-50 disabled:pointer-events-none file:bg-gray-50 file:border-0 file:me-4 file:py-3 file:px-4" @change="handleQuotationFileUpload">
                            </form>
                        </div> 
                        <div v-else>
                            <Label class="block text-sm font-semibold text-gray-700 mb-1">Quotation</Label>
                            <div class="mb-2 flex justify-between items-center shadow-border shadow rounded-lg shadow-gray-300 px-6 py-3">
                                <div class="flex items-center cursor-pointer" @click="downloadQuotation()">
                                    <div>
                                        <p class="text-sm font-medium text-gray-800 dark:text-white">{{ form.quotation.name || form.quotation }}</p>
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

                        <!-- Contract -->
                        <div class="mb-4" v-if="!form.contract">
                            <Label class="block text-sm font-semibold text-gray-700">Contract (If any)</Label>
                            <form class="mt-1">
                                <Label for="file-input" class="sr-only">Choose file</Label>
                                <input type="file" name="file-input" id="file-input" class="block w-full border border-gray-200 shadow-sm rounded-lg text-sm focus:z-10 focus:border-gray-300 focus:ring-0 disabled:opacity-50 disabled:pointer-events-none file:bg-gray-50 file:border-0 file:me-4 file:py-3 file:px-4" @change="handleContractFileUpload">
                            </form>
                        </div>
                        <div v-else>
                            <Label class="block text-sm font-semibold text-gray-700 mb-1">Contract (If any)</Label>
                            <div class="mb-2 flex justify-between items-center shadow-border shadow rounded-lg shadow-gray-300 px-6 py-3">
                                <div class="flex items-center cursor-pointer" @click="downloadContract()">
                                    <div>
                                        <p class="text-sm font-medium text-gray-800 dark:text-white">{{ form.contract.name || form.contract }}</p>
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

            <!-- Contract Status Section -->
            <Card>
                <template #title>Status</template>
                <template #content>
                    <div class="grid grid-cols-1 2xl:grid-cols-3 gap-4">
                        <div>
                            <Label>Contract Status</Label>
                            <ComboBox 
                                :items="$page.props.contract_status" 
                                label-property="name" 
                                value-property="id" 
                                v-model="form.contract_status" 
                                select-placeholder="" 
                                search-placeholder="Search contract status..."
                            ></ComboBox>
                        </div>
                    </div>
                </template>
            </Card>

            <!-- Action Buttons -->
            <Card>
                <template #content>
                    <div class="flex justify-end space-x-2">
                        <Button variant="outline" @click="$inertia.get(route('pipelines'))">Cancel</Button>
                        <Button @click.native="submit()">Save</Button>
                    </div>
                </template>
            </Card>
        </div>

        <!-- Progress Status Dialogs -->
        <Dialog v-model:open="show_add_progress" classProp="max-w-md">
            <template #title>Add Progress Status</template>
            <template #content>
                <div v-if="progress_alert_message" class="mb-4 bg-red-50 border border-red-200 text-sm text-red-800 rounded-lg p-4" role="alert">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="flex-shrink-0 size-4 mt-0.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="m15 9-6 6"></path>
                                <path d="m9 9 6 6"></path>
                            </svg>
                        </div>
                        <div class="ms-4">
                            <div class="text-sm text-red-700">
                                {{ progress_alert_message }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-1 gap-4 py-1">
                    <div>
                        <Label>Case Status</Label>
                        <ComboBox 
                            :items="$page.props.case_status" 
                            label-property="name" 
                            value-property="id" 
                            v-model="progress_form.case_status" 
                            select-placeholder="" 
                            search-placeholder="Search case status..." 
                            @select="progress_form.case_status_name = $page.props.case_status.find(item => item.id == progress_form.case_status)?.name"
                            :class="error_fields.case_status ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''"
                        ></ComboBox>
                    </div>
                    <div>
                        <Label>Date</Label>
                        <div class="" id="date"></div>
                        <Datepicker 
                            mode="date" 
                            v-model="progress_form.date" 
                            format="dd/MM/yyyy"
                            :class="error_fields.date ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''"
                        />
                    </div>
                    <div>
                        <Label>Remark</Label>
                        <Textarea rows="3" autocomplete="off" v-model="progress_form.remark"></Textarea>
                    </div>
                    <div>
                        <Label>Signed Up Programs</Label>
                        <ul class="mt-1 grid grid-cols-1 sm:grid-cols-2 w-full" :class="error_fields.programs ? 'ring-2 ring-red-500 rounded-lg p-2' : ''">
                            <li class="inline-flex items-center gap-x-2.5 py-3 px-4 text-sm font-medium bg-white border text-gray-800 -mt-px sm:mt-0 flex-grow w-full" 
                                v-for="program, program_index in $page.props.programs" 
                            >
                                <div class="relative flex items-start w-full">
                                    <div class="flex items-center space-x-2">
                                        <Checkbox :id="'program_'+program.id" type="checkbox" :checked="isProgramChecked(program.id)" @click.native="triggerProgram(program.id)"></Checkbox>
                                        <Label class="cursor-pointer" :for="'program_'+program.id">{{ program.name }}</Label>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div>
                        <Table>
                            <TableHeader class="bg-gray-100">
                                <TableRow>
                                    <TableHead class="px-6">Program</TableHead>
                                    <TableHead>Student Numbers</TableHead>
                                    <TableHead class="text-center">Action</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-if="progress_form.signed_up_programs.length" v-for="program, program_index in progress_form.signed_up_programs">
                                    <TableCell>{{ program.name }}</TableCell>
                                    <TableCell>
                                        <Input 
                                            v-model="program.student_numbers"
                                            :class="error_fields.student_numbers.includes(program.id) ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''"
                                        ></Input>
                                    </TableCell>
                                    <TableCell class="text-center space-x-1">
                                        <Button variant="destructive" @click="deleteProgram(program_index)">Delete</Button>
                                    </TableCell>
                                </TableRow>
                                <TableRow v-else>
                                    <TableCell class="text-center" colspan="10">
                                        <div class="p-3">No Record Found</div>
                                    </TableCell>
                                </TableRow> 
                            </TableBody>
                        </Table>
                    </div>
                </div>
            </template>
            <template #footer>
                <Button variant="outline" @click="show_add_progress = false">Cancel</Button>
                <Button @click="addProgress">Add</Button>
            </template>
        </Dialog>

        <Dialog v-model:open="show_edit_progress" classProp="max-w-md">
            <template #title>Edit Progress Status</template>
            <template #content>
                <div v-if="edit_progress_alert_message" class="mb-4 bg-red-50 border border-red-200 text-sm text-red-800 rounded-lg p-4" role="alert">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="flex-shrink-0 size-4 mt-0.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="m15 9-6 6"></path>
                                <path d="m9 9 6 6"></path>
                            </svg>
                        </div>
                        <div class="ms-4">
                            <div class="text-sm text-red-700">
                                {{ edit_progress_alert_message }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-1 gap-4 py-1">
                    <div>
                        <Label>Case Status</Label>
                        <ComboBox 
                            :items="$page.props.case_status" 
                            label-property="name" 
                            value-property="id" 
                            v-model="progress_form_edit.case_status" 
                            select-placeholder="" 
                            search-placeholder="Search case status..." 
                            @select="progress_form_edit.case_status_name = $page.props.case_status.find(item => item.id == progress_form_edit.case_status)?.name"
                            :class="error_fields_edit.case_status ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''"
                        ></ComboBox>
                    </div>
                    <div>
                        <Label>Date</Label>
                        <div class="" id="date"></div>
                        <Datepicker 
                            mode="date" 
                            v-model="progress_form_edit.date" 
                            format="dd/MM/y"
                            :class="error_fields_edit.date ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''"
                        />
                    </div>
                    <div>
                        <Label>Remark</Label>
                        <Textarea rows="3" autocomplete="off" v-model="progress_form_edit.remark"></Textarea>
                    </div>
                    <div>
                        <Label>Signed Up Programs</Label>
                        <ul class="mt-1 grid grid-cols-1 sm:grid-cols-2 w-full" :class="error_fields_edit.programs ? 'ring-2 ring-red-500 rounded-lg p-2' : ''">
                            <li class="inline-flex items-center gap-x-2.5 py-3 px-4 text-sm font-medium bg-white border text-gray-800 -mt-px sm:mt-0 flex-grow w-full" 
                                v-for="program, program_index in $page.props.programs" 
                            >
                                <div class="relative flex items-start w-full">
                                    <div class="flex items-center space-x-2">
                                        <Checkbox :id="'program_edit_'+program.id" type="checkbox" :checked="isProgramCheckedEdit(program.id)" @click.native="triggerProgramEdit(program.id)"></Checkbox>
                                        <Label class="cursor-pointer" :for="'program_edit_'+program.id">{{ program.name }}</Label>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div>
                        <Table>
                            <TableHeader class="bg-gray-100">
                                <TableRow>
                                    <TableHead class="px-6">Program</TableHead>
                                    <TableHead>Student Numbers</TableHead>
                                    <TableHead class="text-center">Action</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-if="progress_form_edit.signed_up_programs.length" v-for="progress_edit, progress_edit_index in progress_form_edit.signed_up_programs">
                                    <TableCell>{{ progress_edit.name }}</TableCell>
                                    <TableCell>
                                        <Input 
                                            v-model="progress_edit.student_numbers"
                                            :class="error_fields_edit.student_numbers.includes(progress_edit.id) ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''"
                                        ></Input>
                                    </TableCell>
                                    <TableCell class="text-center space-x-1">
                                        <Button variant="destructive" @click="deleteProgramEdit(progress_edit_index)">Delete</Button>
                                    </TableCell>
                                </TableRow>
                                <TableRow v-else>
                                    <TableCell class="text-center" colspan="10">
                                        <div class="p-3">No Record Found</div>
                                    </TableCell>
                                </TableRow> 
                            </TableBody>
                        </Table>
                    </div>
                </div>
            </template>
            <template #footer>
                <Button variant="outline" @click="show_edit_progress = false">Cancel</Button>
                <Button @click="editProgress">Save</Button>
            </template>
        </Dialog>

        <!-- Contract Information Modal -->
        <Dialog v-model:open="showContractModal" classProp="max-w-4xl">
            <template #title>Contract Information</template>
            <template #content>
                <div v-if="contract_alert_message" class="mb-4 bg-red-50 border border-red-200 text-sm text-red-800 rounded-lg p-4" role="alert">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="flex-shrink-0 size-4 mt-0.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="m15 9-6 6"></path>
                                <path d="m9 9 6 6"></path>
                            </svg>
                        </div>
                        <div class="ms-4">
                            <div class="text-sm text-red-700">
                                {{ contract_alert_message }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="space-y-6">
                    <!-- School Information Section -->
                    <div class="border-t border-dashed border-gray-400 pt-6">
                        <div class="space-y-4">
                            <div class="grid grid-cols-1 2xl:grid-cols-2 gap-4">
                                <div class="w-full">
                                    <Label class="block text-sm font-semibold text-gray-700 mb-1">Contract Date</Label>
                                    <Datepicker 
                                        mode="date" 
                                        v-model="contractForm.contract_date" 
                                        format="dd/MM/yyyy"
                                        :class="contract_error_fields.contract_date ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''"
                                    />
                                </div>
                            </div>
                            <div class="grid grid-cols-1 2xl:grid-cols-2 gap-4">
                                <div class="w-full">
                                    <Label class="block text-sm font-semibold text-gray-700 mb-1">Client Name</Label>
                                    <Input 
                                        type="text" 
                                        v-model="contractForm.client_name" 
                                        placeholder="Enter client name"
                                        :class="contract_error_fields.client_name ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''"
                                    />
                                </div>
                                <div class="w-full">
                                    <Label class="block text-sm font-semibold text-gray-700 mb-1">Client Email</Label>
                                    <Input 
                                        type="email" 
                                        v-model="contractForm.client_email" 
                                        placeholder="Enter client email"
                                        :class="contract_error_fields.client_email ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''"
                                    />
                                </div>
                                <div class="w-full">
                                    <Label class="block text-sm font-semibold text-gray-700 mb-1">ID Type</Label>
                                    <ComboBox 
                                        :items="[
                                            { id: 'SSM', name: 'SSM No.' },
                                            { id: 'IC', name: 'IC No.' }
                                        ]" 
                                        label-property="id" 
                                        value-property="id" 
                                        v-model="contractForm.ssm_type" 
                                        select-placeholder="Select type" 
                                        search-placeholder="Search type..."
                                    ></ComboBox>
                                </div>
                                <div class="w-full">
                                    <Label class="block text-sm font-semibold text-gray-700 mb-1">{{ contractForm.ssm_type === 'SSM' ? 'SSM No.' : 'IC No.' }}</Label>
                                    <div class="space-y-2">
                                        <Input 
                                            type="text" 
                                            v-model="contractForm.ssm_number" 
                                            :placeholder="'Enter ' + contractForm.ssm_type.toLowerCase()"
                                            :class="contract_error_fields.ssm_number ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''"
                                        />
                                    </div>
                                </div>
                            </div>
                            <div class="w-full">
                                <Label class="block text-sm font-semibold text-gray-700 mb-1">Address</Label>
                                <Textarea 
                                    rows="3" 
                                    v-model="contractForm.address" 
                                    placeholder="Enter address"
                                    :class="contract_error_fields.address ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Right to Use Fee Section -->
                    <div class="border-t border-dashed border-gray-400 pt-6">
                        <h3 class="text-sm font-semibold text-gray-700 mb-4">Right to Use Fee</h3>
                        <div class="space-y-2">
                            <div v-for="(fee, index) in contractForm.right_to_use_fees" :key="index" class="grid grid-cols-1 2xl:grid-cols-4 gap-4">
                                <div class="w-full">
                                    <Input 
                                        type="text" 
                                        v-model="fee.program" 
                                        placeholder="Enter offering"
                                        :class="contract_error_fields.right_to_use_fees.includes(index) ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''"
                                    />
                                </div>
                                <div class="w-full">
                                    <Input 
                                        type="number" 
                                        v-model="fee.price" 
                                        placeholder="Enter price"
                                        :class="contract_error_fields.right_to_use_fees.includes(index) ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''"
                                    />
                                </div>
                                <div class="w-full">
                                    <Input 
                                        type="number" 
                                        v-model="fee.promo_price" 
                                        placeholder="Enter promo price"
                                        :class="contract_error_fields.right_to_use_fees.includes(index) ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''"
                                    />
                                </div>
                                <div class="w-full flex items-end">
                                    <Button 
                                        variant="destructive" 
                                        @click="removeRightToUseFee(index)"
                                        v-if="contractForm.right_to_use_fees.length > 1"
                                    >Remove</Button>
                                </div>
                            </div>
                            <Button @click="addRightToUseFee">Add Item</Button>
                        </div>
                    </div>

                    <!-- Student Fee Section -->
                    <div class="border-t border-dashed border-gray-400 pt-6">
                        <h3 class="text-sm font-semibold text-gray-700 mb-4">Student Fee</h3>
                        <div class="space-y-2">
                            <div v-for="(fee, index) in contractForm.student_fees" :key="index" class="grid grid-cols-1 2xl:grid-cols-4 gap-4">
                                <div class="w-full">
                                    <Input 
                                        type="text" 
                                        v-model="fee.offering" 
                                        placeholder="Enter offering"
                                        :class="contract_error_fields.student_fees.includes(index) ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''"
                                    />
                                </div>
                                <div class="w-full">
                                    <Input 
                                        type="number" 
                                        v-model="fee.price_per_year" 
                                        placeholder="Enter price per year"
                                        :class="contract_error_fields.student_fees.includes(index) ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''"
                                    />
                                </div>
                                <div class="w-full">
                                    <Input 
                                        type="number" 
                                        v-model="fee.promo_price" 
                                        placeholder="Enter promo price"
                                        :class="contract_error_fields.student_fees.includes(index) ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''"
                                    />
                                </div>
                                <div class="w-full flex items-end">
                                    <Button 
                                        variant="destructive" 
                                        @click="removeStudentFee(index)"
                                        v-if="contractForm.student_fees.length > 1"
                                    >Remove</Button>
                                </div>
                            </div>
                            <Button @click="addStudentFee">Add Item</Button>
                        </div>
                    </div>

                    <!-- Supplementary Service and Product Fee Section -->
                    <div class="border-t border-dashed border-gray-400 pt-6">
                        <h3 class="text-sm font-semibold text-gray-700 mb-4">Supplementary Service and Product Fee</h3>
                        <div class="space-y-2">
                            <Table>
                                <TableHeader class="bg-gray-100">
                                    <TableRow>
                                        <TableHead class="px-6">Offering</TableHead>
                                        <TableHead>Promo Price</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    <TableRow>
                                        <TableCell class="px-6">Teaching Aid / Programme</TableCell>
                                        <TableCell>
                                            <Input 
                                                type="number" 
                                                v-model="contractForm.teaching_aid_promo" 
                                                placeholder="Enter promo price"
                                                :class="contract_error_fields.teaching_aid_promo ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''"
                                            />
                                        </TableCell>
                                    </TableRow>
                                    <TableRow>
                                        <TableCell class="px-6">Lesson Plan / Book</TableCell>
                                        <TableCell>
                                            <Input 
                                                type="number" 
                                                v-model="contractForm.lesson_plan_promo" 
                                                placeholder="Enter promo price"
                                                :class="contract_error_fields.lesson_plan_promo ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''"
                                            />
                                        </TableCell>
                                    </TableRow>
                                    <TableRow>
                                        <TableCell class="px-6">Centralise Training / Teacher / Session</TableCell>
                                        <TableCell>
                                            <Input 
                                                type="number" 
                                                v-model="contractForm.centralise_training_promo" 
                                                placeholder="Enter promo price"
                                                :class="contract_error_fields.centralise_training_promo ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''"
                                            />
                                        </TableCell>
                                    </TableRow>
                                    <TableRow>
                                        <TableCell class="px-6">In-House Training (Klang Valley) / Hour</TableCell>
                                        <TableCell>
                                            <Input 
                                                type="number" 
                                                v-model="contractForm.inhouse_training_kv_promo" 
                                                placeholder="Enter promo price"
                                                :class="contract_error_fields.inhouse_training_kv_promo ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''"
                                            />
                                        </TableCell>
                                    </TableRow>
                                    <TableRow>
                                        <TableCell class="px-6">In-House Training (North) / Hour</TableCell>
                                        <TableCell>
                                            <Input 
                                                type="number" 
                                                v-model="contractForm.inhouse_training_north_promo" 
                                                placeholder="Enter promo price"
                                                :class="contract_error_fields.inhouse_training_north_promo ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''"
                                            />
                                        </TableCell>
                                    </TableRow>
                                </TableBody>
                            </Table>
                        </div>
                    </div>

                    <!-- Actions Section -->
                    <div class="border-y border-dashed border-gray-400 py-6">
                        <h3 class="text-sm font-semibold text-gray-700 mb-4">Actions</h3>
                        <div class="grid grid-cols-1 2xl:grid-cols-2 gap-4">
                            <div class="flex space-x-2">
                                <Button variant="" @click="viewContract" v-if="contractForm.link_token">View Contract</Button>
                                <Button variant="outline" @click="copyContractLink" v-if="contractForm.link_token" class="gap-2">
                                    <CopyIcon class="size-4" />
                                    Copy Contract Link
                                </Button>
                                <Button variant="destructive" @click="sendContract" v-if="contractForm.link_token" class="gap-2">
                                    <MailIcon class="size-4" />
                                    Send Online Contract
                                </Button>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
            <template #footer>
                <div class="flex justify-between items-center">
                    <div class="flex space-x-2">
                        <Button variant="outline" @click="showContractModal = false">Cancel</Button>
                        <Button @click="handleSave">Save</Button>
                    </div>
                </div>
            </template>
        </Dialog>
    </BreezeAuthenticatedLayout>
</template>