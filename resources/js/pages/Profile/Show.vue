<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Card, CardContent } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { getInitials } from '@/composables/useInitials';
import { MapPin, Phone, Mail, User2, ShieldCheck } from 'lucide-vue-next';

defineProps<{
    profile: {
        id: number;
        name: string;
        is_verified: boolean;
        joined_at: string;
        age: number | null;
        city: string | null;
        zip_code: string | null;
        address: string | null;
        phone_number: string | null;
        email: string | null;
        avatar?: string;
    };
    posts: any[];
}>();
</script>

<template>
    <Head :title="`${profile.name}'s Profile`" />

    <div class="max-w-4xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row gap-8">
            <!-- Left Column: Profile Card -->
            <div class="w-full md:w-1/3">
                <Card>
                    <CardContent class="p-6 flex flex-col items-center text-center">
                        <Avatar class="h-24 w-24 mb-4">
                            <AvatarImage v-if="profile.avatar" :src="profile.avatar" :alt="profile.name" />
                            <AvatarFallback class="text-2xl bg-primary/10 text-primary">
                                {{ getInitials(profile.name) }}
                            </AvatarFallback>
                        </Avatar>

                        <h1 class="text-2xl font-bold mb-1 flex items-center justify-center gap-2">
                            {{ profile.name }}
                            <ShieldCheck v-if="profile.is_verified" class="h-5 w-5 text-green-500" />
                        </h1>
                        
                        <p class="text-sm text-muted-foreground mb-4">
                            Joined {{ new Date(profile.joined_at).toLocaleDateString() }}
                        </p>

                        <div class="w-full space-y-3 text-sm text-left mt-4 border-t pt-4">
                            <!-- Privacy Controlled Fields -->
                            <div v-if="profile.age" class="flex items-center gap-3">
                                <User2 class="h-4 w-4 text-muted-foreground shrink-0" />
                                <span>{{ profile.age }} years old</span>
                            </div>

                            <div v-if="profile.city || profile.zip_code" class="flex items-center gap-3">
                                <MapPin class="h-4 w-4 text-muted-foreground shrink-0" />
                                <span>
                                    {{ profile.city }}<span v-if="profile.city && profile.zip_code">, </span>{{ profile.zip_code }}
                                </span>
                            </div>

                            <div v-if="profile.address" class="flex items-center gap-3">
                                <MapPin class="h-4 w-4 text-muted-foreground shrink-0" />
                                <span>{{ profile.address }}</span>
                            </div>

                            <div v-if="profile.phone_number" class="flex items-center gap-3">
                                <Phone class="h-4 w-4 text-muted-foreground shrink-0" />
                                <a :href="`tel:${profile.phone_number}`" class="text-primary hover:underline">
                                    {{ profile.phone_number }}
                                </a>
                            </div>

                            <div v-if="profile.email" class="flex items-center gap-3">
                                <Mail class="h-4 w-4 text-muted-foreground shrink-0" />
                                <a :href="`mailto:${profile.email}`" class="text-primary hover:underline">
                                    {{ profile.email }}
                                </a>
                            </div>

                            <div v-if="!profile.age && !profile.city && !profile.address && !profile.phone_number && !profile.email" class="text-center text-muted-foreground italic py-2">
                                This user has chosen to keep their details private.
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Right Column: Active Posts -->
            <div class="w-full md:w-2/3 space-y-6">
                <h2 class="text-xl font-bold tracking-tight">Active Posts ({{ posts.length }})</h2>
                
                <div v-if="posts.length === 0" class="border rounded-lg border-dashed p-12 text-center text-muted-foreground">
                    This user currently has no active posts.
                </div>

                <div v-else class="grid gap-4 sm:grid-cols-2">
                    <Link
                        v-for="post in posts"
                        :key="post.id"
                        :href="`/posts/${post.id}`"
                        class="block group relative rounded-lg border bg-card text-card-foreground shadow-sm hover:shadow-md transition-all overflow-hidden"
                    >
                        <div v-if="post.thumb" class="aspect-[16/9] w-full overflow-hidden bg-muted">
                            <img :src="post.thumb" :alt="post.title" class="object-cover w-full h-full group-hover:scale-105 transition-transform duration-300" />
                        </div>
                        <div class="p-4 space-y-2">
                            <Badge variant="secondary" class="font-normal">{{ post.category?.name }}</Badge>
                            <h3 class="font-semibold line-clamp-2 group-hover:text-primary transition-colors">{{ post.title }}</h3>
                            <div class="flex items-center text-xs text-muted-foreground mt-2">
                                <span class="flex items-center"><MapPin class="h-3 w-3 mr-1" /> {{ post.city }}</span>
                                <span class="mx-2">•</span>
                                <span>{{ new Date(post.created_at).toLocaleDateString() }}</span>
                            </div>
                        </div>
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>
