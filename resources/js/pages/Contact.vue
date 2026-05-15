<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Mail, MapPin, Phone } from 'lucide-vue-next';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Contact Us', href: '/contact' }
        ]
    }
});

const form = useForm({
    name: '',
    email: '',
    subject: '',
    message: '',
});

const submit = () => {
    form.post('/contact', {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <Head title="Contact Us" />

    <div class="px-4 py-12 max-w-5xl mx-auto w-full">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-extrabold tracking-tight mb-4">Get in Touch</h1>
            <p class="text-lg text-muted-foreground max-w-2xl mx-auto">
                Have a question, suggestion, or need help with your account? We're here for you. Fill out the form below and our team will get back to you shortly.
            </p>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            <!-- Contact Info -->
            <div class="md:col-span-1 space-y-6">
                <Card class="bg-card border-muted-foreground/10">
                    <CardContent class="p-6">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="h-10 w-10 bg-primary/10 text-primary rounded-full flex items-center justify-center shrink-0">
                                <Mail class="h-5 w-5" />
                            </div>
                            <div>
                                <h3 class="font-bold">Email Us</h3>
                                <p class="text-sm text-muted-foreground">support@masjidpanel.com</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-4 mb-4">
                            <div class="h-10 w-10 bg-primary/10 text-primary rounded-full flex items-center justify-center shrink-0">
                                <Phone class="h-5 w-5" />
                            </div>
                            <div>
                                <h3 class="font-bold">Call Us</h3>
                                <p class="text-sm text-muted-foreground">+1 (619) 742-7188</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <div class="h-10 w-10 bg-primary/10 text-primary rounded-full flex items-center justify-center shrink-0">
                                <MapPin class="h-5 w-5" />
                            </div>
                            <div>
                                <h3 class="font-bold">Location</h3>
                                <p class="text-sm text-muted-foreground">San Diego, CA</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Contact Form -->
            <div class="md:col-span-2">
                <Card class="bg-card border-muted-foreground/10 shadow-sm">
                    <CardHeader>
                        <CardTitle>Send a Message</CardTitle>
                        <CardDescription>We typically reply within 24 hours.</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <form @submit.prevent="submit" class="space-y-6">
                            <div class="grid sm:grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <Label for="name">Your Name</Label>
                                    <Input 
                                        id="name" 
                                        v-model="form.name" 
                                        placeholder="John Doe" 
                                        :disabled="form.processing"
                                        required 
                                    />
                                    <p v-if="form.errors.name" class="text-sm text-destructive">{{ form.errors.name }}</p>
                                </div>
                                <div class="space-y-2">
                                    <Label for="email">Email Address</Label>
                                    <Input 
                                        id="email" 
                                        type="email" 
                                        v-model="form.email" 
                                        placeholder="john@example.com" 
                                        :disabled="form.processing"
                                        required 
                                    />
                                    <p v-if="form.errors.email" class="text-sm text-destructive">{{ form.errors.email }}</p>
                                </div>
                            </div>
                            
                            <div class="space-y-2">
                                <Label for="subject">Subject</Label>
                                <Input 
                                    id="subject" 
                                    v-model="form.subject" 
                                    placeholder="How can we help you?" 
                                    :disabled="form.processing"
                                    required 
                                />
                                <p v-if="form.errors.subject" class="text-sm text-destructive">{{ form.errors.subject }}</p>
                            </div>

                            <div class="space-y-2">
                                <Label for="message">Message</Label>
                                <Textarea 
                                    id="message" 
                                    v-model="form.message" 
                                    placeholder="Write your message here..." 
                                    class="min-h-[150px]"
                                    :disabled="form.processing"
                                    required 
                                />
                                <p v-if="form.errors.message" class="text-sm text-destructive">{{ form.errors.message }}</p>
                            </div>

                            <Button type="submit" class="w-full sm:w-auto" :disabled="form.processing">
                                {{ form.processing ? 'Sending...' : 'Send Message' }}
                            </Button>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </div>
    </div>
</template>
