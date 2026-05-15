<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import { Card, CardHeader, CardTitle, CardDescription, CardContent, CardFooter } from '@/components/ui/card';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Checkout',
                href: '/checkout',
            },
        ],
    },
});

const form = useForm({});

const submit = () => {
    form.post('/checkout');
};
</script>

<template>
    <Head title="Identity Verification" />

    <div class="flex h-full flex-1 flex-col items-center justify-center p-4">
        <div class="w-full max-w-lg">
            <Alert v-if="$page.props.flash?.error" variant="destructive" class="mb-4">
                <AlertDescription>{{ $page.props.flash.error }}</AlertDescription>
            </Alert>
            
            <Card>
                <CardHeader>
                    <CardTitle>Verify Your Identity</CardTitle>
                    <CardDescription>
                        For security reasons and to prevent spam, we require a one-time $1 identity verification fee before you can post on the board.
                    </CardDescription>
                </CardHeader>
                
                <CardContent>
                    <ul class="list-disc list-inside space-y-2 text-muted-foreground text-sm">
                        <li>Post listings on the digital board</li>
                        <li>Contact other community members securely</li>
                        <li>Help us prevent bot spam and malicious actors</li>
                    </ul>
                    <div class="mt-6 p-4 bg-muted rounded-lg text-sm text-muted-foreground">
                        Payment is processed securely by Stripe. We do not store your credit card information.
                    </div>
                </CardContent>

                <CardFooter class="flex justify-end">
                    <form @submit.prevent="submit" class="w-full sm:w-auto">
                        <Button type="submit" class="w-full sm:w-auto" :disabled="form.processing">
                            Pay $1 & Verify
                        </Button>
                    </form>
                </CardFooter>
            </Card>
        </div>
    </div>
</template>
