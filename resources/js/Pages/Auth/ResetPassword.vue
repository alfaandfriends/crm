<script setup>
import BreezeGuestLayout from '@/Layouts/Guest.vue';
import { Head, useForm } from '@inertiajs/vue3';
import Card from '@/Components/Card.vue';
import { Alert, AlertDescription } from '@/Components/ui/alert';
import { Label } from '@/Components/ui/label';
import { Input } from '@/Components/ui/input';
import { Button } from '@/Components/ui/button';

const props = defineProps({
    user_email: String,
    token: String,
});

const form = useForm({
    token: props.token,
    user_email: props.user_email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.store'));
};
</script>

<template>
    <BreezeGuestLayout>
        <Head title="Reset Password" />

        <Card>
            <template #title>
                <h3>Reset Password</h3>
            </template>
            <template #description>
                Please enter your new password below.
            </template>
            <template #content>
                <Alert variant="destructive" v-if="Object.keys($page.props.errors).length > 0">
                    <AlertDescription v-for="(error, key) in $page.props.errors" :key="key">
                        {{ error }}
                    </AlertDescription>
                </Alert>

                <form @submit.prevent="submit">
                    <div class="grid gap-4">
                        <div class="grid gap-2">
                            <Label for="password">New Password</Label>
                            <Input
                                id="password"
                                type="password"
                                v-model="form.password"
                                required
                                autofocus
                                autocomplete="new-password"
                            />
                        </div>

                        <div class="grid gap-2">
                            <Label for="password_confirmation">Confirm Password</Label>
                            <Input
                                id="password_confirmation"
                                type="password"
                                v-model="form.password_confirmation"
                                required
                                autocomplete="new-password"
                            />
                        </div>

                        <Button class="w-full" type="submit" :disabled="form.processing">
                            Reset Password
                        </Button>
                    </div>
                </form>
            </template>
        </Card>
    </BreezeGuestLayout>
</template>
