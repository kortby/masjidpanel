<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ShieldCheck, Heart, MapPin, Users } from 'lucide-vue-next';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'About Us', href: '/about' }
        ]
    }
});
</script>

<template>
    <Head title="About Us" />

    <div class="mx-auto w-full max-w-5xl px-4 py-12">
        <!-- Hero Section -->
        <div class="mb-16 text-center">
            <h1 class="mb-6 text-4xl font-extrabold tracking-tight text-emerald-950 md:text-5xl">Building Stronger Local Communities</h1>
            <p class="mx-auto max-w-3xl text-xl leading-relaxed text-stone-600">
                MasjidPanel was created to bridge the gap between community members. We provide a safe, secure, and hyper-local platform for Muslims to network, trade, and support one another.
            </p>
        </div>

        <!-- Pillars -->
        <div class="mb-16 grid gap-8 md:grid-cols-3">
            <div class="rounded-3xl border border-emerald-900/5 bg-white p-8 text-center shadow-sm transition-all hover:shadow-md">
                <div class="mx-auto mb-6 flex h-16 w-16 items-center justify-center rounded-full bg-emerald-50 text-emerald-700">
                    <MapPin class="h-8 w-8" />
                </div>
                <h3 class="mb-3 text-xl font-bold text-emerald-900">Hyper-Local Focus</h3>
                <p class="text-stone-600">
                    Everything on MasjidPanel is filtered by your city or zip code. Find exactly what you need, right where you live, and support your local neighbors.
                </p>
            </div>

            <div class="rounded-3xl border border-emerald-900/5 bg-white p-8 text-center shadow-sm transition-all hover:shadow-md">
                <div class="mx-auto mb-6 flex h-16 w-16 items-center justify-center rounded-full bg-emerald-50 text-emerald-700">
                    <ShieldCheck class="h-8 w-8" />
                </div>
                <h3 class="mb-3 text-xl font-bold text-emerald-900">100% Verified Trust</h3>
                <p class="text-stone-600">
                    We use a strict $1 identity verification gate via Stripe. This effectively eliminates bots, scammers, and bad actors, ensuring a safe environment for everyone.
                </p>
            </div>

            <div class="rounded-3xl border border-emerald-900/5 bg-white p-8 text-center shadow-sm transition-all hover:shadow-md">
                <div class="mx-auto mb-6 flex h-16 w-16 items-center justify-center rounded-full bg-emerald-50 text-emerald-700">
                    <Heart class="h-8 w-8" />
                </div>
                <h3 class="mb-3 text-xl font-bold text-emerald-900">Community First</h3>
                <p class="text-stone-600">
                    Whether you are looking for a roommate, offering a local service, or networking professionally, our goal is to foster meaningful connections.
                </p>
            </div>
        </div>

        <!-- CTA -->
        <div class="rounded-3xl border border-emerald-900/10 bg-emerald-50 p-10 text-center">
            <Users class="mx-auto mb-4 h-12 w-12 text-emerald-700" />
            <h2 class="mb-4 text-3xl font-bold text-emerald-950">Ready to join your local board?</h2>
            <p class="mx-auto mb-8 max-w-xl text-stone-600">
                Discover opportunities, offer your services, or just see what's happening in your community today.
            </p>
            <div class="flex flex-col justify-center gap-4 sm:flex-row">
                <Link href="/register" class="inline-flex items-center justify-center rounded-full bg-emerald-800 px-8 py-3 text-sm font-semibold text-white transition-colors hover:bg-emerald-700">
                    Create an Account
                </Link>
                <Link href="/" class="inline-flex items-center justify-center rounded-full border border-emerald-800 bg-transparent px-8 py-3 text-sm font-semibold text-emerald-900 transition-colors hover:bg-emerald-800 hover:text-white">
                    Browse as Guest
                </Link>
            </div>
        </div>
    </div>
</template>
