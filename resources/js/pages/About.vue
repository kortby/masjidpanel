<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Card, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
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

    <div class="px-4 py-12 max-w-5xl mx-auto w-full">
        <!-- Hero Section -->
        <div class="text-center mb-16">
            <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight mb-6">Building Stronger Local Communities</h1>
            <p class="text-xl text-muted-foreground max-w-3xl mx-auto leading-relaxed">
                MasjidPanel was created to bridge the gap between community members. We provide a safe, secure, and hyper-local platform for Muslims to network, trade, and support one another.
            </p>
        </div>

        <!-- Pillars -->
        <div class="grid md:grid-cols-3 gap-8 mb-16">
            <Card class="bg-card border-muted-foreground/10 hover:shadow-md transition-all">
                <CardContent class="p-8 flex flex-col items-center text-center">
                    <div class="h-16 w-16 bg-primary/10 text-primary rounded-full flex items-center justify-center mb-6">
                        <MapPin class="h-8 w-8" />
                    </div>
                    <h3 class="text-xl font-bold mb-3">Hyper-Local Focus</h3>
                    <p class="text-muted-foreground">
                        Everything on MasjidPanel is filtered by your city or zip code. Find exactly what you need, right where you live, and support your local neighbors.
                    </p>
                </CardContent>
            </Card>

            <Card class="bg-card border-muted-foreground/10 hover:shadow-md transition-all">
                <CardContent class="p-8 flex flex-col items-center text-center">
                    <div class="h-16 w-16 bg-primary/10 text-primary rounded-full flex items-center justify-center mb-6">
                        <ShieldCheck class="h-8 w-8" />
                    </div>
                    <h3 class="text-xl font-bold mb-3">100% Verified Trust</h3>
                    <p class="text-muted-foreground">
                        We use a strict $1 identity verification gate via Stripe. This effectively eliminates bots, scammers, and bad actors, ensuring a safe environment for everyone.
                    </p>
                </CardContent>
            </Card>

            <Card class="bg-card border-muted-foreground/10 hover:shadow-md transition-all">
                <CardContent class="p-8 flex flex-col items-center text-center">
                    <div class="h-16 w-16 bg-primary/10 text-primary rounded-full flex items-center justify-center mb-6">
                        <Heart class="h-8 w-8" />
                    </div>
                    <h3 class="text-xl font-bold mb-3">Community First</h3>
                    <p class="text-muted-foreground">
                        Whether you are looking for a roommate, offering a local service, or networking professionally, our goal is to foster meaningful connections.
                    </p>
                </CardContent>
            </Card>
        </div>

        <!-- CTA -->
        <div class="bg-primary/5 rounded-3xl p-10 text-center border border-primary/10">
            <Users class="h-12 w-12 text-primary mx-auto mb-4" />
            <h2 class="text-3xl font-bold mb-4">Ready to join your local board?</h2>
            <p class="text-muted-foreground mb-8 max-w-xl mx-auto">
                Discover opportunities, offer your services, or just see what's happening in your community today.
            </p>
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <Link href="/register">
                    <Button size="lg" class="w-full sm:w-auto rounded-full px-8">Create an Account</Button>
                </Link>
                <Link href="/">
                    <Button variant="outline" size="lg" class="w-full sm:w-auto rounded-full px-8">Browse as Guest</Button>
                </Link>
            </div>
        </div>
    </div>
</template>
