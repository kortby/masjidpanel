<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { MapPin, Phone, Mail, User2, ShieldCheck, Calendar, FileText, ChevronLeft } from 'lucide-vue-next';
import { getInitials } from '@/composables/useInitials';
import { computed } from 'vue';

const props = defineProps<{
    profile: {
        id: number;
        name: string;
        is_verified: boolean;
        joined_at: string;
        posts_count: number;
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

const memberSince = computed(() => {
    const date = new Date(props.profile.joined_at);
    return date.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
});

const hasContactInfo = computed(() => {
    return props.profile.age || props.profile.city || props.profile.zip_code || props.profile.address || props.profile.phone_number || props.profile.email;
});
</script>

<template>
    <Head :title="`${profile.name}'s Profile`" />

    <div class="min-h-[80vh]">
        <!-- Hero Banner -->
        <div class="relative h-48 overflow-hidden bg-gradient-to-br from-emerald-800 via-emerald-700 to-teal-600 sm:h-56">
            <!-- Decorative pattern -->
            <div class="absolute inset-0 bg-[linear-gradient(to_right,#ffffff08_1px,transparent_1px),linear-gradient(to_bottom,#ffffff08_1px,transparent_1px)] bg-[size:32px_32px]"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>

            <!-- Back button -->
            <div class="relative mx-auto max-w-5xl px-4 pt-6 sm:px-6 lg:px-8">
                <Link href="/" class="inline-flex items-center gap-1.5 rounded-full bg-white/15 px-4 py-2 text-sm font-medium text-white backdrop-blur-sm transition-all hover:bg-white/25">
                    <ChevronLeft class="h-4 w-4" />
                    Back
                </Link>
            </div>
        </div>

        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <!-- Profile Header Card -->
            <div class="-mt-20 relative z-10 mb-8">
                <div class="overflow-hidden rounded-3xl border border-emerald-900/5 bg-white shadow-lg">
                    <div class="p-6 sm:p-8">
                        <div class="flex flex-col items-center gap-6 sm:flex-row sm:items-end">
                            <!-- Avatar -->
                            <div class="relative shrink-0">
                                <div class="h-28 w-28 overflow-hidden rounded-2xl border-4 border-white bg-emerald-100 shadow-lg ring-4 ring-emerald-500/20 sm:h-32 sm:w-32">
                                    <img v-if="profile.avatar" :src="profile.avatar" :alt="profile.name" class="h-full w-full object-cover" />
                                    <div v-else class="flex h-full w-full items-center justify-center text-3xl font-bold text-emerald-800 sm:text-4xl">
                                        {{ getInitials(profile.name) }}
                                    </div>
                                </div>
                                <!-- Verification badge -->
                                <div v-if="profile.is_verified" class="absolute -bottom-1 -right-1 flex h-8 w-8 items-center justify-center rounded-full border-2 border-white bg-emerald-500 shadow-sm" title="Verified Member">
                                    <ShieldCheck class="h-4 w-4 text-white" />
                                </div>
                            </div>

                            <!-- Name & Meta -->
                            <div class="flex-1 text-center sm:text-left">
                                <div class="flex flex-col items-center gap-2 sm:flex-row sm:items-center">
                                    <h1 class="text-2xl font-extrabold tracking-tight text-emerald-950 sm:text-3xl">
                                        {{ profile.name }}
                                    </h1>
                                    <span v-if="profile.is_verified" class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-semibold text-emerald-700 ring-1 ring-emerald-600/10">
                                        <ShieldCheck class="h-3 w-3" />
                                        Verified
                                    </span>
                                </div>

                                <!-- Stats pills -->
                                <div class="mt-3 flex flex-wrap items-center justify-center gap-3 sm:justify-start">
                                    <div class="inline-flex items-center gap-1.5 rounded-full bg-stone-100 px-3 py-1.5 text-xs font-medium text-stone-600">
                                        <Calendar class="h-3.5 w-3.5 text-stone-400" />
                                        Member since {{ memberSince }}
                                    </div>
                                    <div class="inline-flex items-center gap-1.5 rounded-full bg-stone-100 px-3 py-1.5 text-xs font-medium text-stone-600">
                                        <FileText class="h-3.5 w-3.5 text-stone-400" />
                                        {{ profile.posts_count }} {{ profile.posts_count === 1 ? 'post' : 'posts' }}
                                    </div>
                                    <div v-if="profile.city || profile.zip_code" class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-medium text-emerald-700">
                                        <MapPin class="h-3.5 w-3.5 text-emerald-500" />
                                        {{ profile.city }}<span v-if="profile.city && profile.zip_code">, </span>{{ profile.zip_code }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Grid -->
            <div class="grid gap-8 pb-12 md:grid-cols-3">
                <!-- Left: Contact Details -->
                <div class="space-y-6 md:col-span-1">
                    <div class="overflow-hidden rounded-2xl border border-emerald-900/5 bg-white/80 shadow-sm backdrop-blur-sm">
                        <div class="border-b border-emerald-900/5 bg-gradient-to-r from-emerald-50/80 to-teal-50/80 px-6 py-4">
                            <h2 class="text-sm font-bold uppercase tracking-wider text-emerald-800">About</h2>
                        </div>

                        <div v-if="hasContactInfo" class="divide-y divide-stone-100 p-2">
                            <div v-if="profile.age" class="flex items-center gap-3 rounded-xl px-4 py-3 transition-colors hover:bg-stone-50">
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                                    <User2 class="h-4 w-4" />
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-stone-400">Age</p>
                                    <p class="text-sm font-semibold text-stone-800">{{ profile.age }} years old</p>
                                </div>
                            </div>

                            <div v-if="profile.address" class="flex items-center gap-3 rounded-xl px-4 py-3 transition-colors hover:bg-stone-50">
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                                    <MapPin class="h-4 w-4" />
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-stone-400">Address</p>
                                    <p class="text-sm font-semibold text-stone-800">{{ profile.address }}</p>
                                </div>
                            </div>

                            <div v-if="profile.phone_number" class="flex items-center gap-3 rounded-xl px-4 py-3 transition-colors hover:bg-stone-50">
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                                    <Phone class="h-4 w-4" />
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-stone-400">Phone</p>
                                    <a :href="`tel:${profile.phone_number}`" class="text-sm font-semibold text-emerald-700 transition-colors hover:text-emerald-900 hover:underline">
                                        {{ profile.phone_number }}
                                    </a>
                                </div>
                            </div>

                            <div v-if="profile.email" class="flex items-center gap-3 rounded-xl px-4 py-3 transition-colors hover:bg-stone-50">
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                                    <Mail class="h-4 w-4" />
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-stone-400">Email</p>
                                    <a :href="`mailto:${profile.email}`" class="text-sm font-semibold text-emerald-700 transition-colors hover:text-emerald-900 hover:underline">
                                        {{ profile.email }}
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div v-else class="px-6 py-10 text-center">
                            <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-stone-100">
                                <User2 class="h-5 w-5 text-stone-400" />
                            </div>
                            <p class="text-sm italic text-stone-400">This user has chosen to keep their details private.</p>
                        </div>
                    </div>
                </div>

                <!-- Right: Active Posts -->
                <div class="space-y-6 md:col-span-2">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-bold tracking-tight text-emerald-950">
                            Active Posts
                            <span class="ml-2 inline-flex min-w-[24px] items-center justify-center rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-bold text-emerald-800">{{ posts.length }}</span>
                        </h2>
                    </div>

                    <div v-if="posts.length === 0" class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-emerald-900/15 bg-gradient-to-br from-stone-50 to-emerald-50/30 py-16 text-center">
                        <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-white text-emerald-600 shadow-sm">
                            <FileText class="h-7 w-7" />
                        </div>
                        <h3 class="text-lg font-bold text-emerald-950">No active posts</h3>
                        <p class="mt-1 max-w-xs text-sm text-stone-500">This user currently has no active posts on the board.</p>
                    </div>

                    <div v-else class="grid gap-4 sm:grid-cols-2">
                        <Link
                            v-for="post in posts"
                            :key="post.id"
                            :href="`/posts/${post.id}`"
                            class="group relative block overflow-hidden rounded-2xl border border-emerald-900/5 bg-white shadow-sm transition-all duration-300 hover:-translate-y-0.5 hover:border-amber-400/60 hover:shadow-lg"
                        >
                            <!-- Thumbnail -->
                            <div v-if="post.thumb" class="aspect-[16/9] w-full overflow-hidden bg-stone-100">
                                <img :src="post.thumb" :alt="post.title" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" />
                            </div>
                            <div v-else class="flex aspect-[16/9] w-full items-center justify-center bg-gradient-to-br from-emerald-50 to-teal-50">
                                <FileText class="h-8 w-8 text-emerald-300" />
                            </div>

                            <!-- Content -->
                            <div class="space-y-2.5 p-5">
                                <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-[11px] font-semibold text-emerald-700 ring-1 ring-emerald-600/10 transition-colors group-hover:bg-amber-50 group-hover:text-amber-800 group-hover:ring-amber-400/20">
                                    {{ post.category?.name }}
                                </span>
                                <h3 class="line-clamp-2 text-[15px] font-bold leading-snug text-emerald-950 transition-colors group-hover:text-emerald-800">{{ post.title }}</h3>
                                <div class="flex items-center gap-2 text-xs text-stone-400">
                                    <span class="flex items-center gap-1">
                                        <MapPin class="h-3 w-3" />
                                        {{ post.city }}
                                    </span>
                                    <span class="h-1 w-1 rounded-full bg-stone-300"></span>
                                    <span>{{ new Date(post.created_at).toLocaleDateString() }}</span>
                                </div>
                            </div>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
