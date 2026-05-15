<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { MapPin, Phone, Mail, User2, ShieldCheck } from 'lucide-vue-next';
import { getInitials } from '@/composables/useInitials';

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

    <div class="mx-auto max-w-4xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="flex flex-col gap-8 md:flex-row">
            <!-- Left Column: Profile Card -->
            <div class="w-full md:w-1/3">
                <div class="overflow-hidden rounded-3xl border border-emerald-900/10 bg-white shadow-sm">
                    <div class="flex flex-col items-center p-6 text-center">
                        <div class="relative mb-4 h-24 w-24 overflow-hidden rounded-full bg-emerald-100 shadow-sm">
                            <img v-if="profile.avatar" :src="profile.avatar" :alt="profile.name" class="h-full w-full object-cover" />
                            <div v-else class="flex h-full w-full items-center justify-center text-2xl font-bold text-emerald-800">
                                {{ getInitials(profile.name) }}
                            </div>
                        </div>

                        <h1 class="mb-1 flex items-center justify-center gap-2 text-2xl font-bold text-emerald-950">
                            {{ profile.name }}
                            <ShieldCheck v-if="profile.is_verified" class="h-5 w-5 text-emerald-500" />
                        </h1>
                        
                        <p class="mb-4 text-sm text-stone-500">
                            Joined {{ new Date(profile.joined_at).toLocaleDateString() }}
                        </p>

                        <div class="mt-4 w-full space-y-3 border-t border-emerald-900/10 pt-4 text-left text-sm">
                            <!-- Privacy Controlled Fields -->
                            <div v-if="profile.age" class="flex items-center gap-3">
                                <User2 class="h-4 w-4 shrink-0 text-emerald-600/60" />
                                <span class="text-stone-700">{{ profile.age }} years old</span>
                            </div>

                            <div v-if="profile.city || profile.zip_code" class="flex items-center gap-3">
                                <MapPin class="h-4 w-4 shrink-0 text-emerald-600/60" />
                                <span class="text-stone-700">
                                    {{ profile.city }}<span v-if="profile.city && profile.zip_code">, </span>{{ profile.zip_code }}
                                </span>
                            </div>

                            <div v-if="profile.address" class="flex items-center gap-3">
                                <MapPin class="h-4 w-4 shrink-0 text-emerald-600/60" />
                                <span class="text-stone-700">{{ profile.address }}</span>
                            </div>

                            <div v-if="profile.phone_number" class="flex items-center gap-3">
                                <Phone class="h-4 w-4 shrink-0 text-emerald-600/60" />
                                <a :href="`tel:${profile.phone_number}`" class="text-emerald-700 transition-colors hover:text-emerald-800 hover:underline">
                                    {{ profile.phone_number }}
                                </a>
                            </div>

                            <div v-if="profile.email" class="flex items-center gap-3">
                                <Mail class="h-4 w-4 shrink-0 text-emerald-600/60" />
                                <a :href="`mailto:${profile.email}`" class="text-emerald-700 transition-colors hover:text-emerald-800 hover:underline">
                                    {{ profile.email }}
                                </a>
                            </div>

                            <div v-if="!profile.age && !profile.city && !profile.address && !profile.phone_number && !profile.email" class="py-2 text-center text-sm italic text-stone-500">
                                This user has chosen to keep their details private.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Active Posts -->
            <div class="w-full space-y-6 md:w-2/3">
                <h2 class="text-xl font-bold tracking-tight text-emerald-950">Active Posts ({{ posts.length }})</h2>
                
                <div v-if="posts.length === 0" class="rounded-3xl border border-dashed border-emerald-900/20 bg-stone-50 p-12 text-center text-stone-500">
                    This user currently has no active posts.
                </div>

                <div v-else class="grid gap-4 sm:grid-cols-2">
                    <Link
                        v-for="post in posts"
                        :key="post.id"
                        :href="`/posts/${post.id}`"
                        class="group relative block overflow-hidden rounded-2xl border border-emerald-900/10 bg-white shadow-sm transition-all hover:border-amber-400 hover:shadow-md"
                    >
                        <div v-if="post.thumb" class="aspect-[16/9] w-full overflow-hidden bg-stone-100">
                            <img :src="post.thumb" :alt="post.title" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" />
                        </div>
                        <div class="space-y-2 p-5">
                            <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-semibold text-emerald-700 transition-colors group-hover:bg-amber-100 group-hover:text-amber-800">
                                {{ post.category?.name }}
                            </span>
                            <h3 class="line-clamp-2 font-bold text-emerald-950 transition-colors group-hover:text-emerald-800">{{ post.title }}</h3>
                            <div class="mt-2 flex items-center text-xs text-stone-500">
                                <span class="flex items-center"><MapPin class="mr-1 h-3 w-3" /> {{ post.city }}</span>
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
