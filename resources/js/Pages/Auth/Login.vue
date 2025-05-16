<script setup>
import BreezeGuestLayout from '@/Layouts/Guest.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import Card from '@/Components/Card.vue';
import { Alert, AlertDescription, AlertTitle } from '@/Components/ui/alert'
import { Label } from '@/Components/ui/label';
import { Input } from '@/Components/ui/input';
import { Button } from '@/Components/ui/button';
import { Checkbox } from '@/Components/ui/checkbox';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    username: '',
    password: '',
    remember: false
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};

const page = usePage();
</script>

<template>
    <BreezeGuestLayout>
        <Head title="Log in" />
            <Card>
                <template #title>
                    <h3>Admin Login</h3>
                </template>
                <template #description>
                    Please enter your credentials to log in.
                </template>
                <template #content>
                    <Alert variant="destructive" v-if="Object.keys($page.props.errors).length > 0">
                        <AlertDescription v-for="(error, key) in $page.props.errors" :key="key">
                            {{ error }}
                        </AlertDescription>
                    </Alert>
                    <div
                        v-if="page.props.flash.success"
                        class="mb-4 rounded border border-green-300 bg-green-50 px-4 py-3 text-green-800 text-sm flex items-center gap-2"
                    >
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                        </svg>
                        {{ page.props.flash.success }}
                    </div>
                    <form @submit.prevent="submit">
                        <div class="grid gap-4">
                            <div class="grid gap-2">
                                <Label for="email">Email / Username</Label>
                                <Input type="email" v-model="form.username" required autofocus autocomplete="off"/>
                            </div>
                            <div class="grid gap-2">
                                <Label for="password">Password</Label>
                                <Input type="password" v-model="form.password" required autocomplete="off"/>
                            </div>
                            <div class="flex justify-between">
                                <Label for="terms" class="flex space-x-2 items-center text-sm cursor-pointer">
                                    <Checkbox id="terms" v-model:checked="form.remember"/>
                                    <span>Remember me</span>
                                </Label>
                                <Link
                                    v-if="canResetPassword"
                                    :href="route('password.request')"
                                    class="text-sm text-gray-600 hover:text-gray-900"
                                >
                                    Forgot password?
                                </Link>
                            </div>
                            <Button class="w-full" @click="submit" :disabled="form.processing">Log in</Button>
                        </div>
                    </form>
                </template>
            </Card>
    </BreezeGuestLayout>
</template>
