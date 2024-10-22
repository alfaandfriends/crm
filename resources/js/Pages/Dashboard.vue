<script setup>
import { Head, Link } from '@inertiajs/vue3';
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import { Table, TableBody, TableCaption, TableCell, TableHead, TableHeader, TableRow } from '@/Components/ui/table'
import Card from '@/Components/Card.vue'
</script>

<template>
    <BreezeAuthenticatedLayout>
		<Card>
			<template #title>Statistics</template>
			<template #content>
                <Table>
                    <TableHeader class="bg-gray-100">
                        <TableRow>
                            <TableHead class="px-4"></TableHead>
                            <TableHead v-for="progress, progress_index in $page.props.progress_status">{{ progress.name }}</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="program, program_index in $page.props.programs">
                            <TableCell class="px-4">{{ program.name }}</TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
			</template>
		</Card>
    </BreezeAuthenticatedLayout>
</template>

<script>

export default {
}
</script>