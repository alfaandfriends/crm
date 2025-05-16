<script setup>
import BreezeGuestLayout from '@/Layouts/Guest.vue';
import { Head, useForm } from '@inertiajs/vue3';
import Card from '@/Components/Card.vue';
import { Alert, AlertDescription } from '@/Components/ui/alert';
import { Label } from '@/Components/ui/label';
import { Input } from '@/Components/ui/input';
import { Button } from '@/Components/ui/button';

defineProps({
    status: String,
});

const form = useForm({
    user_email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <BreezeGuestLayout>
        <Head title="Forgot Password" />

        <Card>
            <template #title>
                <h3>Forgot Password</h3>
            </template>
            <template #description>
                Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.
            </template>
            <template #content>
                <Alert v-if="status" class="mb-4">
                    <AlertDescription>{{ status }}</AlertDescription>
                </Alert>

                <Alert variant="destructive" v-if="Object.keys($page.props.errors).length > 0">
                    <AlertDescription v-for="(error, key) in $page.props.errors" :key="key">
                        {{ error }}
                    </AlertDescription>
                </Alert>

                <form @submit.prevent="submit">
                    <div class="grid gap-4">
                        <div class="grid gap-2">
                            <Label for="user_email">Email</Label>
                            <Input
                                id="user_email"
                                type="email"
                                v-model="form.user_email"
                                required
                                autofocus
                                autocomplete="username"
                            />
                        </div>

                        <Button class="w-full" type="submit" :disabled="form.processing">
                            Email Password Reset Link
                        </Button>
                    </div>
                </form>
            </template>
        </Card>
    </BreezeGuestLayout>
</template>
