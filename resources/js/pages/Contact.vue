<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { Mail, MapPin, Phone, CheckCircle, AlertCircle } from 'lucide-vue-next';
import { computed } from 'vue';

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

const page = usePage();
const flash = computed(() => page.props.flash as Record<string, string> | undefined);
</script>

<template>
    <Head title="Contact Us" />

    <div class="mx-auto w-full max-w-5xl px-4 py-12 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-12 text-center">
            <h1 class="mb-4 text-4xl font-extrabold tracking-tight text-emerald-950">Get in Touch</h1>
            <p class="mx-auto max-w-2xl text-lg text-stone-600">
                Have a question, suggestion, or need help with your account? We're here for you. Fill out the form below and our team will get back to you shortly.
            </p>
        </div>

        <div class="grid gap-12 md:grid-cols-3">
            <!-- Contact Info -->
            <div class="space-y-6 md:col-span-1">
                <div class="rounded-3xl border border-emerald-900/5 bg-white p-6 shadow-sm">
                    <div class="space-y-6">
                        <div class="flex items-start space-x-4">
                            <div class="mt-1 flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-emerald-50 text-emerald-700">
                                <Mail class="h-5 w-5" />
                            </div>
                            <div>
                                <h3 class="font-semibold text-emerald-950">Email Us</h3>
                                <p class="text-sm text-stone-600">support@masjidpanel.com</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-4">
                            <div class="mt-1 flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-emerald-50 text-emerald-700">
                                <Phone class="h-5 w-5" />
                            </div>
                            <div>
                                <h3 class="font-semibold text-emerald-950">Call Us</h3>
                                <p class="text-sm text-stone-600">+1 (619) 742-7188</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-4">
                            <div class="mt-1 flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-emerald-50 text-emerald-700">
                                <MapPin class="h-5 w-5" />
                            </div>
                            <div>
                                <h3 class="font-semibold text-emerald-950">Location</h3>
                                <p class="text-sm text-stone-600">San Diego, CA</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="md:col-span-2">
                <div class="rounded-3xl border border-emerald-900/5 bg-white p-6 shadow-sm sm:p-8">
                    <div class="mb-6">
                        <h2 class="text-2xl font-bold tracking-tight text-emerald-950">Send a Message</h2>
                        <p class="text-sm text-stone-500">We typically reply within 24 hours.</p>
                    </div>
                    
                    <!-- Flash Messages -->
                    <div v-if="flash?.success" class="mb-6 flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-4">
                        <CheckCircle class="h-5 w-5 shrink-0 text-emerald-600" />
                        <p class="text-sm font-medium text-emerald-800">{{ flash.success }}</p>
                    </div>
                    <div v-if="flash?.error" class="mb-6 flex items-center gap-3 rounded-xl border border-red-200 bg-red-50 p-4">
                        <AlertCircle class="h-5 w-5 shrink-0 text-red-600" />
                        <p class="text-sm font-medium text-red-800">{{ flash.error }}</p>
                    </div>

                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="grid gap-6 sm:grid-cols-2">
                            <div class="space-y-2">
                                <label for="name" class="block text-sm font-medium leading-none text-emerald-950">Your Name</label>
                                <input 
                                    id="name" 
                                    v-model="form.name" 
                                    placeholder="John Doe" 
                                    :disabled="form.processing"
                                    :class="[
                                        'flex h-10 w-full rounded-md border bg-transparent px-3 py-2 text-sm placeholder:text-stone-400 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50',
                                        form.errors.name ? 'border-red-500 focus:ring-red-500' : 'border-stone-200 focus:ring-emerald-500'
                                    ]"
                                />
                                <p v-if="form.errors.name" class="text-sm font-medium text-red-500">{{ form.errors.name }}</p>
                            </div>
                            <div class="space-y-2">
                                <label for="email" class="block text-sm font-medium leading-none text-emerald-950">Email Address</label>
                                <input 
                                    id="email" 
                                    v-model="form.email" 
                                    placeholder="john@example.com" 
                                    :disabled="form.processing"
                                    :class="[
                                        'flex h-10 w-full rounded-md border bg-transparent px-3 py-2 text-sm placeholder:text-stone-400 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50',
                                        form.errors.email ? 'border-red-500 focus:ring-red-500' : 'border-stone-200 focus:ring-emerald-500'
                                    ]"
                                />
                                <p v-if="form.errors.email" class="text-sm font-medium text-red-500">{{ form.errors.email }}</p>
                            </div>
                        </div>
                        
                        <div class="space-y-2">
                            <label for="subject" class="block text-sm font-medium leading-none text-emerald-950">Subject</label>
                            <input 
                                id="subject" 
                                v-model="form.subject" 
                                placeholder="How can we help you?" 
                                :disabled="form.processing"
                                :class="[
                                    'flex h-10 w-full rounded-md border bg-transparent px-3 py-2 text-sm placeholder:text-stone-400 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50',
                                    form.errors.subject ? 'border-red-500 focus:ring-red-500' : 'border-stone-200 focus:ring-emerald-500'
                                ]"
                            />
                            <p v-if="form.errors.subject" class="text-sm font-medium text-red-500">{{ form.errors.subject }}</p>
                        </div>

                        <div class="space-y-2">
                            <label for="message" class="block text-sm font-medium leading-none text-emerald-950">Message</label>
                            <textarea 
                                id="message" 
                                v-model="form.message" 
                                placeholder="Write your message here..." 
                                :class="[
                                    'flex min-h-[150px] w-full rounded-md border bg-transparent px-3 py-2 text-sm placeholder:text-stone-400 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50',
                                    form.errors.message ? 'border-red-500 focus:ring-red-500' : 'border-stone-200 focus:ring-emerald-500'
                                ]"
                                :disabled="form.processing"
                            ></textarea>
                            <p v-if="form.errors.message" class="text-sm font-medium text-red-500">{{ form.errors.message }}</p>
                        </div>

                        <button 
                            type="submit" 
                            class="inline-flex w-full items-center justify-center rounded-md bg-emerald-800 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 sm:w-auto" 
                            :disabled="form.processing"
                        >
                            {{ form.processing ? 'Sending...' : 'Send Message' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
