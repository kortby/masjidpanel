<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { ShieldCheck, MapPin, Users } from 'lucide-vue-next';
</script>

<template>
    <div class="relative overflow-hidden bg-background border-b mb-8">
        <!-- Background pattern -->
        <div class="absolute inset-0 bg-[linear-gradient(to_right,#80808012_1px,transparent_1px),linear-gradient(to_bottom,#80808012_1px,transparent_1px)] bg-[size:24px_24px]"></div>
        
        <div class="relative max-w-7xl mx-auto px-4 py-16 sm:px-6 sm:py-24 lg:px-8 text-center">
            <h1 class="text-4xl font-extrabold tracking-tight sm:text-5xl lg:text-6xl text-foreground">
                Your Local <span class="text-primary">Verified</span> Notice Board
            </h1>
            <p class="mt-6 max-w-2xl mx-auto text-xl text-muted-foreground">
                Connecting the community without the spam. A simple $1 lifetime verification fee keeps scammers out and trust high.
            </p>
            
            <div class="mt-10 flex justify-center gap-4">
                <Link href="/login">
                    <Button size="lg" class="h-12 px-8 text-base">Get Started</Button>
                </Link>
                <Link href="/posts/create">
                    <Button size="lg" variant="outline" class="h-12 px-8 text-base bg-background/50 backdrop-blur-sm">Post an Ad</Button>
                </Link>
            </div>

            <div class="mt-20 grid grid-cols-1 md:grid-cols-3 gap-8 max-w-4xl mx-auto text-left">
                <div class="flex flex-col items-center text-center p-6 rounded-2xl bg-muted/30 border border-muted/50 transition-all hover:bg-muted/50">
                    <div class="flex items-center justify-center h-14 w-14 rounded-full bg-primary/10 mb-5">
                        <MapPin class="h-7 w-7 text-primary" />
                    </div>
                    <h3 class="text-xl font-semibold">Hyper-Local</h3>
                    <p class="mt-3 text-base text-muted-foreground">Everything is filtered strictly by your location. See what's relevant to you and your community.</p>
                </div>
                <div class="flex flex-col items-center text-center p-6 rounded-2xl bg-muted/30 border border-muted/50 transition-all hover:bg-muted/50">
                    <div class="flex items-center justify-center h-14 w-14 rounded-full bg-primary/10 mb-5">
                        <ShieldCheck class="h-7 w-7 text-primary" />
                    </div>
                    <h3 class="text-xl font-semibold">100% Verified</h3>
                    <p class="mt-3 text-base text-muted-foreground">The $1 gateway ensures every user is a real person with a valid card, drastically reducing spam.</p>
                </div>
                <div class="flex flex-col items-center text-center p-6 rounded-2xl bg-muted/30 border border-muted/50 transition-all hover:bg-muted/50">
                    <div class="flex items-center justify-center h-14 w-14 rounded-full bg-primary/10 mb-5">
                        <Users class="h-7 w-7 text-primary" />
                    </div>
                    <h3 class="text-xl font-semibold">Private & Secure</h3>
                    <p class="mt-3 text-base text-muted-foreground">Your contact info stays hidden. Responses are securely relayed directly to your email inbox.</p>
                </div>
            </div>
        </div>
    </div>
</template>
