<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ShieldCheck, MapPin, Users } from 'lucide-vue-next';
</script>

<template>
    <div class="relative mb-8 overflow-hidden border-b border-emerald-900/10 bg-white">
        <!-- Background pattern -->
        <div class="absolute inset-0 bg-[linear-gradient(to_right,#064e3b0a_1px,transparent_1px),linear-gradient(to_bottom,#064e3b0a_1px,transparent_1px)] bg-[size:24px_24px]"></div>
        
        <div class="relative mx-auto max-w-7xl px-4 py-16 text-center sm:px-6 sm:py-24 lg:px-8">
            <h1 class="text-4xl font-extrabold tracking-tight text-emerald-950 sm:text-5xl lg:text-6xl">
                Your Local <span class="text-amber-500">Verified</span> Notice Board
            </h1>
            <p class="mx-auto mt-6 max-w-2xl text-xl text-stone-500">
                Free to browse. Connecting the community. A simple $1 lifetime security fee to post and connect.
            </p>
            
            <div class="mt-10 flex justify-center gap-4">
                <Link href="/register" class="inline-flex h-12 items-center justify-center rounded-full bg-emerald-800 px-8 text-base font-bold text-white transition-colors hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                    Get Started
                </Link>
                <Link href="/posts/create" class="inline-flex h-12 items-center justify-center rounded-full border border-stone-200 bg-white/50 px-8 text-base font-bold text-emerald-950 backdrop-blur-sm transition-colors hover:bg-stone-50 focus:outline-none focus:ring-2 focus:ring-stone-500 focus:ring-offset-2">
                    Post an Ad
                </Link>
            </div>

            <div class="mx-auto mt-20 grid max-w-4xl grid-cols-1 gap-8 text-left md:grid-cols-3">
                <div class="flex flex-col items-center rounded-3xl border border-emerald-900/10 bg-stone-50 p-6 text-center transition-all hover:bg-emerald-50/50">
                    <div class="mb-5 flex h-14 w-14 items-center justify-center rounded-full bg-emerald-100">
                        <MapPin class="h-7 w-7 text-emerald-800" />
                    </div>
                    <h3 class="text-xl font-bold text-emerald-950">Hyper-Local</h3>
                    <p class="mt-3 text-base text-stone-500">Everything is filtered strictly by your location. See what's relevant to you and your community.</p>
                </div>
                <div class="flex flex-col items-center rounded-3xl border border-emerald-900/10 bg-stone-50 p-6 text-center transition-all hover:bg-emerald-50/50">
                    <div class="mb-5 flex h-14 w-14 items-center justify-center rounded-full bg-emerald-100">
                        <ShieldCheck class="h-7 w-7 text-emerald-800" />
                    </div>
                    <h3 class="text-xl font-bold text-emerald-950">100% Verified</h3>
                    <p class="mt-3 text-base text-stone-500">The $1 gateway ensures every user is a real person with a valid card, drastically reducing spam.</p>
                </div>
                <div class="flex flex-col items-center rounded-3xl border border-emerald-900/10 bg-stone-50 p-6 text-center transition-all hover:bg-emerald-50/50">
                    <div class="mb-5 flex h-14 w-14 items-center justify-center rounded-full bg-emerald-100">
                        <Users class="h-7 w-7 text-emerald-800" />
                    </div>
                    <h3 class="text-xl font-bold text-emerald-950">Private & Secure</h3>
                    <p class="mt-3 text-base text-stone-500">Your contact info stays hidden. Responses are securely relayed directly to your email inbox.</p>
                </div>
            </div>
        </div>
    </div>
</template>
