<template>
    <Pagination v-slot="{ page }" :total="page_data.total" :itemsPerPage="10" :siblingCount="2" :default-page="page_data.current_page" v-if="page_data.data.length">
        <PaginationList v-slot="{ items }" class="flex justify-center items-center gap-1">
            <PaginationFirst @click="$inertia.get(generatePageUrl(page_data.first_page_url))" />
            <!-- <PaginationPrev @click="$inertia.get(generatePageUrl(page_data.prev_page_url))" /> -->
        
            <template v-for="(item, index) in items" :key="index">
                <PaginationListItem v-if="item.type === 'page'" :value="item.value" as-child @click="$inertia.get(generatePageUrl(page_data.path + '?page=' + item.value))">
                    <Button class="w-10 h-10 p-0" :variant="item.value === page_data.current_page ? 'default' : 'outline'">
                        {{ item.value }}
                    </Button>
                </PaginationListItem>
                <PaginationEllipsis v-else :key="item.type" :index="index" />
            </template>
        
            <!-- <PaginationNext @click="$inertia.get(generatePageUrl(page_data.next_page_url))" /> -->
            <PaginationLast @click="$inertia.get(generatePageUrl(page_data.last_page_url))" />
        </PaginationList>
    </Pagination>
</template>

<script>
import { Pagination, PaginationEllipsis, PaginationFirst, PaginationLast, PaginationList, PaginationListItem, PaginationNext, PaginationPrev, } from '@/Components/ui/pagination'
import { Button, } from '@/Components/ui/button'

export default {
    components: {
        Pagination,
        PaginationEllipsis,
        PaginationFirst,
        PaginationLast,
        PaginationList,
        PaginationListItem,
        PaginationNext,
        PaginationPrev, 
        Button
    },
    data() {
        return {
            url: ''
        }
    },  
    props: {
        page_data: Object,
        params: Object
    },
    methods: {
        objectToQueryString(obj, parentKey = '') {
            const queryString = [];

            for (const key in obj) {
                if (obj.hasOwnProperty(key)) {
                    const value = obj[key];
                    const paramKey = parentKey ? `${parentKey}[${key}]` : key;

                    if (typeof value === 'object' && !Array.isArray(value)) {
                        // If the value is an object, recursively convert it to a query string
                        queryString.push(this.objectToQueryString(value, paramKey));
                    } else {
                        // Otherwise, encode the key and value and add to the query string
                        queryString.push(`${encodeURIComponent(paramKey)}=${encodeURIComponent(value)}`);
                    }
                }
            }

            return queryString.join('&');
        },
        generateQueryString() {
            return this.objectToQueryString(this.params);
        },
        generatePageUrl(baseUrl) {
            const queryString = this.generateQueryString();
            return queryString ? `${baseUrl}&${queryString}` : baseUrl;
        }
    },
    mounted() {
        // Generate the initial query string when the component is mounted
        this.generateQueryString();
    },
    updated() {
        // Regenerate the query string if parameters change
        this.generateQueryString();
    }
}
</script>
