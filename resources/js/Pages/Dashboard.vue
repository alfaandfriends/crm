<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import { Table, TableBody, TableCaption, TableCell, TableHead, TableHeader, TableRow } from '@/Components/ui/table'
import Card from '@/Components/Card.vue'
import axios from 'axios';
import { Button } from '@/Components/ui/button'
import { Sparkles } from 'lucide-vue-next'
import { useToast } from '@/Components/ui/toast/use-toast'
import { Textarea } from '@/Components/ui/textarea'
import { ref, nextTick, onMounted, watch } from 'vue'
import DOMPurify from 'dompurify'
import { VueMarkdownIt } from '@f3ve/vue-markdown-it';
import MarkdownIt from 'markdown-it';

const toast = useToast()
const page = usePage()
const aiPrompt = ref('')
const aiResponse = ref('')
const isGenerating = ref(false)
const conversationHistory = ref([])

// Update CSRF token whenever it changes in the shared data
watch(() => page.props.csrf_token, (newToken) => {
    if (newToken) {
        const metaTag = document.querySelector('meta[name="csrf-token"]');
        if (metaTag) {
            metaTag.setAttribute('content', newToken);
        }
    }
}, { immediate: true });

const md = new MarkdownIt({
  html: true,
  linkify: true
});

md.renderer.rules.table_open = function() {
  return '<div class="overflow-auto"><table class="ai_table">';
};

const renderMarkdown = (text) => {
    return DOMPurify.sanitize(md.render(text));
}

const handleKeyDown = (event) => {
    if (event.key === 'Enter' && !event.shiftKey) {
        event.preventDefault()
        handlePromptSubmit()
    }
}

const handlePromptSubmit = async () => {
    if (!aiPrompt.value.trim()) {
        toast.error('Please enter a prompt')
        return
    }

    try {
        isGenerating.value = true
        aiResponse.value = '' // Clear previous response

        // Add the current prompt to conversation history
        conversationHistory.value.push({
            role: 'user',
            content: aiPrompt.value
        })

        const response = await fetch('/dashboard/ai-prompt', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'text/event-stream',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                prompt: aiPrompt.value,
                conversation_history: conversationHistory.value
            })
        })

        const reader = response.body.getReader()
        const decoder = new TextDecoder()
        let newText = ''

        while (true) {
            const { done, value } = await reader.read()
            if (done) break

            const chunk = decoder.decode(value, { stream: true })
            aiResponse.value += chunk
        }

        // Add the complete response to conversation history
        conversationHistory.value.push({
            role: 'assistant',
            content: aiResponse.value
        })

        aiPrompt.value = '' // Clear the prompt after successful submission
    } catch (error) {
        console.error('Error:', error)
        toast.error('Failed to generate response. Please try again.')
    } finally {
        isGenerating.value = false
    }
}
</script>

<template>
    <BreezeAuthenticatedLayout>
        <Card class="mb-6">
            <template #title>
                <div class="flex items-center gap-2">
                    <Sparkles class="w-5 h-5 text-yellow-500" />
                    <span>CRM AI Assistant</span>
                </div>
            </template>
            <template #content>
                <div class="space-y-4">
                    <div class="flex flex-col gap-2">
                        <Textarea 
                            v-model="aiPrompt" 
                            placeholder="Ask me anything about your data."
                            class="min-h-[100px] resize-none"
                            @keydown="handleKeyDown"
                        />
                        <div class="flex justify-end">
                            <Button 
                                @click="handlePromptSubmit"
                                :disabled="!aiPrompt.trim() || isGenerating"
                                class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700"
                            >
                                <Sparkles class="w-4 h-4 mr-2" />
                                {{ isGenerating ? 'Processing...' : 'Generate' }}
                            </Button>
                        </div>
                    </div>
                </div>
            </template>
        </Card>

        
        <Card class="mb-6" v-if="aiResponse || isGenerating">
            <template #title>
                <div class="flex items-center gap-2">
                    <Sparkles class="w-5 h-5 text-yellow-500" />
                    <span>Response</span>
                </div>
            </template>
            <template #content>
                <div class="flex flex-col gap-2">
                    <div class="flex-1">
                        <div v-html="aiResponse ? renderMarkdown(aiResponse) : isGenerating ? 'Thinking...' : ''"></div>
                    </div>
                </div>
            </template>
        </Card>
		<!-- <Card>
			<template #title>Monthly Report</template>
			<template #content>
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-5 gap-2">
                    <div class="">
                        <Label>Year / Month</Label>
                        <Datepicker mode="month" :format="'MMM yyyy'" v-model="monthly_report.params.date" @select="getMonthlyReport" placeholder="This Month"></Datepicker>
                    </div>
                    <div class="">
                        <Label>Sales Person</Label>
                        <ComboBox :canClear="true" :items="monthly_report.user.list.options" label-property="display_name" value-property="value" @search="findUserMonthly" v-model="monthly_report.params.sales_person" select-placeholder="All Records" search-placeholder="Search sales person..." :loading="monthly_report.user.searching" @select="getMonthlyReport"> 
                            <template #label="{ item }">
                                <span class="font-medium">{{ item.display_name }}<br><small class="font-normal">{{ item.user_email ? item.user_email : 'Email not available' }}</small></span>
                            </template>
                        </ComboBox>
                    </div>
                </div>
                <div class="animate-pulse duration-700" v-if="monthly_report.searching">
                    <ul class="mt-5 space-y-3">
                        <li class="w-full h-4 bg-gray-200 rounded-full dark:bg-neutral-700"></li>
                        <li class="w-full h-4 bg-gray-200 rounded-full dark:bg-neutral-700"></li>
                        <li class="w-full h-4 bg-gray-200 rounded-full dark:bg-neutral-700"></li>
                        <li class="w-full h-4 bg-gray-200 rounded-full dark:bg-neutral-700"></li>
                        <li class="w-full h-4 bg-gray-200 rounded-full dark:bg-neutral-700"></li>
                        <li class="w-full h-4 bg-gray-200 rounded-full dark:bg-neutral-700"></li>
                        <li class="w-full h-4 bg-gray-200 rounded-full dark:bg-neutral-700"></li>
                        <li class="w-full h-4 bg-gray-200 rounded-full dark:bg-neutral-700"></li>
                        <li class="w-full h-4 bg-gray-200 rounded-full dark:bg-neutral-700"></li>
                    </ul>
                </div>
                <div class="overflow-auto" v-else>
                    <table class="min-w-full divide-y divide-gray-400 border border-slate-400">
                        <thead>
                            <tr>
                                <th class="bg-slate-200"></th>
                                <th colspan="10" class="whitespace-nowrap py-2 px-2 text-xs font-medium text-center text-white bg-slate-800 uppercase">
                                    Case Status
                                </th>
                            </tr>
                            <tr>
                                <th class="whitespace-nowrap py-2 px-4 text-xs font-medium text-left text-white bg-slate-800 uppercase">Program</th>
                                <th class="py-2 px-4 border-l border-r border-slate-400 whitespace-nowrap text-xs font-bold text-center text-gray-800 uppercase bg-slate-200" v-for="case_status in $page.props.case_status">
                                    {{ case_status.name }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-400">
                            <tr v-for="program in $page.props.programs">
                                <td class="py-2 px-4 text-sm font-semibold text-gray-900 whitespace-nowrap text-left bg-slate-200">{{ program.name }}</td>
                                <td v-for="case_status in $page.props.case_status" class="py-2 px-2 text-sm font-semibold text-gray-900 whitespace-nowrap text-center border-l border-r border-slate-400">
                                    {{ monthly_report.data.find(item => item.program_id == program.id && item.case_status_id == case_status.id)?.centre_names.reduce((acc, centre) => acc + parseInt(centre.student_numbers), 0) ?? 0 }} students / 
                                    {{ monthly_report.data.find(item => item.program_id == program.id && item.case_status_id == case_status.id)?.centre_names.length ?? 0 }} centres
                                </td>
                            </tr>
                            <tr>
                                <td class="py-2 px-4 text-sm font-bold text-gray-900 whitespace-nowrap text-left">Total</td>
                                <td v-for="case_status in $page.props.case_status" class="py-2 px-2 text-sm font-bold text-gray-900 whitespace-nowrap text-center border-l border-r border-slate-400">
                                    <div class="flex flex-col">
                                        <span>
                                        {{ totalStudentsMonthly(case_status.id) }} students / 
                                        {{ totalCentresMonthly(case_status.id) }} centres
                                        </span>
                                        <Popover>
                                            <PopoverTrigger>
                                                <span class="text-xs underline text-slate-700">
                                                    More Details
                                                </span>
                                            </PopoverTrigger>
                                            <PopoverContent>
                                                <div class="flex flex-col" v-if="listCentresWithStudentNumber(case_status.id)">
                                                    <span class="text-xs" v-for="centre in listCentresWithStudentNumber(case_status.id)">{{ centre.name }} / {{ centre.total_student_numbers }} students</span>
                                                </div>
                                                <div class="flex flex-col items-center" v-else>
                                                    <span class="text-xs">Data Unavailable</span>
                                                </div>
                                            </PopoverContent>
                                        </Popover>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
			</template>
		</Card>
		<Card>
			<template #title>Yearly Report</template>
			<template #content>
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-5 gap-2">
                    <div class="">
                        <Label>Program</Label>
                        <ComboBox :canClear="true" :items="$page.props.programs" label-property="name" value-property="id" v-model="yearly_report.params.program" select-placeholder="All Programs" search-placeholder="Search program..." @select="getYearlyReport"></ComboBox>
                    </div>
                    <div class="">
                        <Label>Year</Label>
                        <Datepicker mode="year" :format="'yyyy'" v-model="yearly_report.params.date" @select="getYearlyReport" placeholder="This Year"></Datepicker>
                    </div>
                    <div class="">
                        <Label>Sales Person</Label>
                        <ComboBox :canClear="true" :items="yearly_report.user.list.options" label-property="display_name" value-property="value" @search="findUserYearly" v-model="yearly_report.params.sales_person" select-placeholder="All Records" search-placeholder="Search sales person..." :loading="yearly_report.user.searching" @select="getYearlyReport"> 
                            <template #label="{ item }">
                                <span class="font-medium">{{ item.display_name }}<br><small class="font-normal">{{ item.user_email ? item.user_email : 'Email not available' }}</small></span>
                            </template>
                        </ComboBox>
                    </div>
                </div>
                <div class="animate-pulse duration-700" v-if="yearly_report.searching">
                    <ul class="mt-5 space-y-3">
                        <li class="w-full h-4 bg-gray-200 rounded-full dark:bg-neutral-700"></li>
                        <li class="w-full h-4 bg-gray-200 rounded-full dark:bg-neutral-700"></li>
                        <li class="w-full h-4 bg-gray-200 rounded-full dark:bg-neutral-700"></li>
                        <li class="w-full h-4 bg-gray-200 rounded-full dark:bg-neutral-700"></li>
                        <li class="w-full h-4 bg-gray-200 rounded-full dark:bg-neutral-700"></li>
                        <li class="w-full h-4 bg-gray-200 rounded-full dark:bg-neutral-700"></li>
                        <li class="w-full h-4 bg-gray-200 rounded-full dark:bg-neutral-700"></li>
                        <li class="w-full h-4 bg-gray-200 rounded-full dark:bg-neutral-700"></li>
                        <li class="w-full h-4 bg-gray-200 rounded-full dark:bg-neutral-700"></li>
                    </ul>
                </div>
                <div class="overflow-auto" v-else>
                    <table class="min-w-full divide-y divide-gray-400 border border-slate-400">
                        <thead>
                            <tr>
                                <th class="bg-slate-200"></th>
                                <th colspan="10" class="whitespace-nowrap py-2 px-2 text-xs font-medium text-center text-white bg-slate-800 uppercase">
                                    Case Status
                                </th>
                            </tr>
                            <tr>
                                <th class="whitespace-nowrap py-2 px-4 text-xs font-medium text-left text-white bg-slate-800 uppercase">Month</th>
                                <th class="py-2 px-4 border-l border-r border-slate-400 whitespace-nowrap text-xs font-bold text-center text-gray-800 uppercase bg-slate-200" v-for="case_status in $page.props.case_status">
                                    {{ case_status.name }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-400">
                            <tr v-for="month in months">
                                <td class="py-2 px-4 text-sm font-semibold text-gray-900 whitespace-nowrap text-left bg-slate-200">{{ month.name }}</td>
                                <td v-for="case_status in $page.props.case_status" class="py-2 px-2 text-sm font-semibold text-gray-900 whitespace-nowrap text-center border-l border-r border-slate-400">
                                    {{ totalStudentsYearly(month.number, case_status.id) }} students / 
                                    {{ totalCentresYearly(month.number, case_status.id) }} centres
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
			</template>
		</Card> -->
    </BreezeAuthenticatedLayout>
</template>

<script>
import { format } from 'date-fns'
import { debounce } from 'vue-debounce'
import { Popover, PopoverContent, PopoverTrigger } from '@/Components/ui/popover'

export default {
    data(){
        return {
            months: [],
            monthly_report: {
                user: {
                    searching: false,
                    list: {
                        value: [],
                        options: []
                    },
                },
                searching: false,
                data: [],
                params: {
                    date : {
                        month: new Date().getMonth(), 
                        year: new Date().getFullYear()
                    },
                    sales_person: ''
                }
            },
            yearly_report: {
                user: {
                    searching: false,
                    list: {
                        value: [],
                        options: []
                    },
                },
                searching: false,
                data: [],
                params: {
                    program: '',
                    date : new Date().getFullYear(),
                    sales_person: ''
                }
            }
        }
    },
    computed: {
        totalStudentsMonthly() {
            return (caseStatusId) => {
                const case_status_exist = this.monthly_report.data.some(item => item.case_status_id == caseStatusId)
                if (!case_status_exist) {
                    return 0;
                }
                const matchedItems = this.monthly_report.data.filter(item => item.case_status_id == caseStatusId) 
                const sum = matchedItems.reduce((total, item) => {
                    return total + (item.centre_names.reduce((centreTotal, centre) => {
                        return centreTotal + (Number(centre.student_numbers) || 0);
                    }, 0));
                }, 0);

                return sum; 
            }
        },
        totalCentresMonthly() {
            return (caseStatusId) => {
                const matchedItem = this.monthly_report.data.find(item => item.case_status_id == caseStatusId);

                return matchedItem ? (matchedItem.centre_names ? matchedItem.centre_names.length : 0) : 0;
            };
        },
        listCentresWithStudentNumber() {
            return (caseStatusId) => {
                const case_status_exist = this.monthly_report.data.some(item => item.case_status_id == caseStatusId)

                if(case_status_exist){
                    const matchedItems = this.monthly_report.data.filter(item => item.case_status_id == caseStatusId) 
                    const centerMap = {};

                    matchedItems.forEach(item => {
                        item.centre_names.forEach(centre => {
                            const centreName = centre.name;
                            const studentCount = Number(centre.student_numbers) || 0;

                            if (!centerMap[centreName]) {
                                centerMap[centreName] = {
                                    name: centreName,
                                    total_student_numbers: 0
                                };
                            }

                            centerMap[centreName].total_student_numbers += studentCount;
                        });
                    });

                    return Object.values(centerMap);
                }
            };
        },
        totalStudentsYearly(){
            return (month, caseStatusId) => {
                const data_by_month = this.yearly_report.data.filter(item => item.month === month);
                
                if (data_by_month.length > 0) {
                    const data_by_case_status = data_by_month.flatMap(item => item.case_statuses)
                                                            .filter(item => item.case_status_id === caseStatusId);
                    
                    const sum = data_by_case_status.reduce((total, item) => {
                        const centreTotal = item.centre_names.reduce((centreSum, centre) => {
                            return centreSum + (Number(centre.student_numbers) || 0);
                        }, 0);
                        return total + centreTotal;
                    }, 0);

                    return sum;
                }
                
                return 0;
            };
        },
        totalCentresYearly(){
            return (month, caseStatusId) => {
                const data_by_month = this.yearly_report.data.filter(item => item.month === month);

                if (data_by_month.length > 0) {
                    const uniqueCentres = data_by_month
                        .flatMap(item => item.case_statuses)
                        .filter(item => item.case_status_id === caseStatusId)
                        .reduce((uniqueSet, item) => {
                            item.centre_names.forEach(centre => uniqueSet.add(centre.name));
                            return uniqueSet;
                        }, new Set());

                    return uniqueCentres.size;
                }

                return 0;
            };
        }
    },
    methods: {
        findUserMonthly: debounce(function(query) {
            if(query){
                this.monthly_report.user.searching = true
                axios.get(route('users.find_username_email'), {
                    params: {
                        'keyword': query
                    }
                })
                .then((res) => {
                    this.monthly_report.user.list.options = res.data
                    this.monthly_report.user.searching = false
                });
            }
        }, 1000),
        findUserYearly: debounce(function(query) {
            if(query){
                this.yearly_report.user.searching = true
                axios.get(route('users.find_username_email'), {
                    params: {
                        'keyword': query
                    }
                })
                .then((res) => {
                    this.yearly_report.user.list.options = res.data
                    this.yearly_report.user.searching = false
                });
            }
        }, 1000),
        getMonthlyReport(){
            this.monthly_report.searching = true
            axios.get(route('dashboard.get_monthly_report'), {
                params: this.monthly_report.params
            })
            .then((response) => {
                this.monthly_report.data = response.data
                this.monthly_report.searching = false
            })
        },
        getYearlyReport(){
            this.yearly_report.searching = true
            axios.get(route('dashboard.get_yearly_report'), {
                params: this.yearly_report.params
            })
            .then((response) => {
                this.yearly_report.data = response.data
                this.yearly_report.searching = false
            })
        },
        getMonthName(monthNumber) {
            const monthNames = [
                "January",
                "February",
                "March",
                "April",
                "May",
                "June",
                "July",
                "August",
                "September",
                "October",
                "November",
                "December"
            ];
            return monthNames[monthNumber - 1];
        },
        getMonth(){
            const year = new Date().getFullYear();
            for (let i = 1; i <= 12; i++) {
                const month = {
                    name: this.getMonthName(i),
                    number: i,
                    year: year
                };
                this.months.push(month);
            }
        }
    },
    mounted(){
        this.getMonth()
        this.getMonthlyReport()
        this.getYearlyReport()
    }
}
</script>

<style>
.ai_table {
    @apply w-full border-collapse border border-gray-300 mb-4 text-[12px];
}

.ai_table th,
.ai_table td {
    @apply border border-gray-300 px-4 py-2 text-xs;
}

.ai_table th {
    @apply bg-gray-100 font-semibold;
}

.ai_table tr:nth-child(even) {
    @apply bg-gray-50;
}

.ai_table tr:hover {
    @apply bg-gray-100;
}
</style>