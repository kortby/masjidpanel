<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import {
    Search, MapPin, Briefcase, Home, Heart, Users,
    ShoppingBag, GraduationCap, Wrench, Activity, Car,
    Calendar, Globe, Plus, Hash, ArrowRight, FileText,
    ShieldCheck, Tag, Sparkles
} from 'lucide-vue-next';
import { ref, computed } from 'vue';
import GuestHero from '@/components/GuestHero.vue';

const props = defineProps<{
    categories: any[];
    recentPosts: any[];
    stats: { total_posts: number; verified_users: number; total_categories: number };
    popularTags: { id: number; name: string; slug: string; posts_count: number }[];
    filters: any;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Categories', href: '/' }
        ]
    }
});

const searchQuery = ref('');
const locationQuery = ref(props.filters.location || '');
const locationError = ref('');

const validateLocation = () => {
    const loc = locationQuery.value.trim();
    if (!loc) {
        locationError.value = '';
        return true;
    }
    const isValid = /^(?:\d{5}(?:-\d{4})?|.*[a-zA-Z].*)$/.test(loc);
    locationError.value = isValid ? '' : 'Please enter a valid 5-digit zip code or city name.';
    return isValid;
};

const clearLocationError = () => {
    if (locationError.value) {
        if (/^(?:\d{5}(?:-\d{4})?|.*[a-zA-Z].*)$/.test(locationQuery.value.trim())) {
            locationError.value = '';
        }
    }
};

const handleSearch = () => {
    if (!validateLocation()) return;

    router.get('/feed', {
        search: searchQuery.value,
        location: locationQuery.value
    });
};

const timeAgo = (date: string) => {
    const seconds = Math.floor((Date.now() - new Date(date).getTime()) / 1000);
    if (seconds < 60) return 'just now';
    const minutes = Math.floor(seconds / 60);
    if (minutes < 60) return `${minutes}m ago`;
    const hours = Math.floor(minutes / 60);
    if (hours < 24) return `${hours}h ago`;
    const days = Math.floor(hours / 24);
    if (days < 7) return `${days}d ago`;
    return new Date(date).toLocaleDateString();
};

const getCategoryIcon = (name: string) => {
    switch (name) {
        case 'Jobs & Hiring': return Briefcase;
        case 'Housing & Roommates': return Home;
        case 'Marriage & Matrimony': return Heart;
        case 'Professional Networking': return Users;
        case 'Buy, Sell & Give Away': return ShoppingBag;
        case 'Education & Tutors': return GraduationCap;
        case 'Local Services': return Wrench;
        case 'Sports & Activities': return Activity;
        case 'Rideshare & Carpool': return Car;
        case 'Community Events': return Calendar;
        case 'Websites & Apps': return Globe;
        case 'Other (Suggest a Category)': return Plus;
        default: return Hash;
    }
}
</script>

<template>
    <Head title="Categories" />

    <GuestHero v-if="!$page.props.auth.user" />

    <div class="mx-auto w-full max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        <!-- Search Section -->
        <div class="mx-auto mb-16 max-w-3xl">
            <div class="text-center">
                <h1 class="text-3xl font-extrabold tracking-tight text-emerald-950 md:text-4xl">What are you looking for locally?</h1>
                <p class="mt-3 text-base text-stone-500">Search across all categories or browse below</p>
            </div>
            <form @submit.prevent="handleSearch" class="mt-8 flex flex-col gap-3 sm:flex-row">
                <div class="relative w-full sm:w-1/3">
                    <MapPin class="absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-emerald-600/60" />
                    <input
                        v-model="locationQuery"
                        @blur="validateLocation"
                        @input="clearLocationError"
                        type="text"
                        placeholder="City or Zip"
                        :class="[
                            'h-14 w-full rounded-full border bg-white pl-12 text-base text-stone-900 shadow-sm transition-colors focus:outline-none focus:ring-1',
                            locationError
                                ? 'border-red-500 focus:border-red-500 focus:ring-red-500 bg-red-50/30'
                                : 'border-emerald-900/10 focus:border-emerald-500 focus:ring-emerald-500'
                        ]"
                    />
                    <p v-if="locationError" class="absolute -bottom-6 left-4 whitespace-nowrap text-xs font-medium text-red-500">{{ locationError }}</p>
                </div>
                <div class="relative w-full sm:flex-1">
                    <Search class="absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-emerald-600/60" />
                    <input
                        v-model="searchQuery"
                        type="search"
                        placeholder="Search posts..."
                        class="h-14 w-full rounded-full border border-emerald-900/10 bg-white pl-12 pr-32 text-base text-stone-900 shadow-sm transition-colors focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                    />
                    <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 rounded-full bg-emerald-800 px-6 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-emerald-700">
                        Search
                    </button>
                </div>
            </form>

            <!-- Popular Tags -->
            <div v-if="popularTags.length > 0" class="mt-6 flex flex-wrap items-center justify-center gap-2">
                <span class="flex items-center gap-1 text-xs font-medium text-stone-400">
                    <Sparkles class="h-3.5 w-3.5" />
                    Trending:
                </span>
                <Link
                    v-for="pTag in popularTags"
                    :key="pTag.id"
                    :href="`/feed?tag=${pTag.slug}`"
                    class="inline-flex items-center gap-1 rounded-full border border-emerald-900/8 bg-white px-3 py-1 text-xs font-medium text-stone-500 transition-all hover:border-emerald-400/40 hover:bg-emerald-50 hover:text-emerald-700"
                >
                    <Tag class="h-3 w-3" />
                    {{ pTag.name }}
                </Link>
            </div>
        </div>

        <!-- Stats Bar -->
        <div class="mb-12 grid grid-cols-3 divide-x divide-emerald-900/10 overflow-hidden rounded-2xl border border-emerald-900/10 bg-white shadow-sm">
            <div class="flex flex-col items-center gap-1 px-4 py-6 text-center sm:flex-row sm:justify-center sm:gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700">
                    <FileText class="h-5 w-5" />
                </div>
                <div>
                    <p class="text-xl font-bold text-emerald-950 sm:text-2xl">{{ stats.total_posts }}</p>
                    <p class="text-[11px] font-medium uppercase tracking-wider text-stone-400 sm:text-xs">Posts</p>
                </div>
            </div>
            <div class="flex flex-col items-center gap-1 px-4 py-6 text-center sm:flex-row sm:justify-center sm:gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700">
                    <ShieldCheck class="h-5 w-5" />
                </div>
                <div>
                    <p class="text-xl font-bold text-emerald-950 sm:text-2xl">{{ stats.verified_users }}</p>
                    <p class="text-[11px] font-medium uppercase tracking-wider text-stone-400 sm:text-xs">Verified</p>
                </div>
            </div>
            <div class="flex flex-col items-center gap-1 px-4 py-6 text-center sm:flex-row sm:justify-center sm:gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700">
                    <Hash class="h-5 w-5" />
                </div>
                <div>
                    <p class="text-xl font-bold text-emerald-950 sm:text-2xl">{{ stats.total_categories }}</p>
                    <p class="text-[11px] font-medium uppercase tracking-wider text-stone-400 sm:text-xs">Categories</p>
                </div>
            </div>
        </div>

        <!-- Categories Grid -->
        <div class="mb-16">
            <div class="mb-6 flex items-center justify-between">
                <h2 class="text-xl font-bold tracking-tight text-emerald-900">Browse Categories</h2>
                <Link href="/feed" class="flex items-center gap-1 text-sm font-medium text-emerald-700 transition-colors hover:text-emerald-900">
                    View All
                    <ArrowRight class="h-4 w-4" />
                </Link>
            </div>
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6">
                <Link
                    href="/feed"
                    class="group relative flex h-36 flex-col items-center justify-center rounded-2xl border border-emerald-900/10 bg-gradient-to-br from-emerald-50 to-teal-50 p-4 text-center text-stone-700 shadow-sm transition-all duration-300 hover:-translate-y-0.5 hover:border-emerald-500/30 hover:shadow-lg"
                >
                    <div class="mb-3 rounded-xl bg-white p-3.5 text-emerald-700 shadow-sm ring-1 ring-emerald-900/5 transition-transform duration-300 group-hover:scale-110">
                        <Hash class="h-5 w-5" />
                    </div>
                    <span class="text-sm font-semibold leading-tight text-emerald-900">All Posts</span>
                </Link>

                <Link
                    v-for="category in categories"
                    :key="category.id"
                    :href="`/feed?category_id=${category.id}`"
                    class="group relative flex h-36 flex-col items-center justify-center rounded-2xl border border-emerald-900/10 bg-white p-4 text-center text-stone-700 shadow-sm transition-all duration-300 hover:-translate-y-0.5 hover:border-amber-400/60 hover:shadow-lg"
                >
                    <div class="mb-3 rounded-xl bg-emerald-50 p-3.5 text-emerald-700 ring-1 ring-emerald-900/5 transition-all duration-300 group-hover:scale-110 group-hover:bg-amber-50 group-hover:text-amber-700 group-hover:ring-amber-400/20">
                        <component :is="getCategoryIcon(category.name)" class="h-5 w-5" />
                    </div>
                    <span class="text-sm font-semibold leading-tight transition-colors group-hover:text-emerald-900">{{ category.name.replace(' (Suggest a Category)', '') }}</span>
                    <span class="absolute right-2.5 top-2.5 flex min-w-[22px] items-center justify-center rounded-full bg-stone-100 px-1.5 py-0.5 text-[10px] font-bold text-stone-500 transition-colors group-hover:bg-amber-100 group-hover:text-amber-800">{{ category.posts_count }}</span>
                </Link>
            </div>
        </div>

        <!-- Recent Posts -->
        <div v-if="recentPosts.length > 0">
            <div class="mb-6 flex items-center justify-between">
                <h2 class="text-xl font-bold tracking-tight text-emerald-900">Recently Posted</h2>
                <Link href="/feed" class="flex items-center gap-1 text-sm font-medium text-emerald-700 transition-colors hover:text-emerald-900">
                    See All
                    <ArrowRight class="h-4 w-4" />
                </Link>
            </div>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <Link
                    v-for="post in recentPosts"
                    :key="post.id"
                    :href="`/posts/${post.id}`"
                    class="group flex flex-col overflow-hidden rounded-2xl border border-emerald-900/5 bg-white shadow-sm transition-all duration-300 hover:-translate-y-0.5 hover:border-amber-400/60 hover:shadow-lg"
                >
                    <!-- Thumbnail or Placeholder -->
                    <div v-if="post.thumb" class="aspect-[2/1] w-full overflow-hidden bg-stone-100">
                        <img :src="post.thumb" :alt="post.title" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" />
                    </div>
                    <div v-else class="flex aspect-[2/1] w-full items-center justify-center bg-gradient-to-br from-emerald-50 to-teal-50/80">
                        <FileText class="h-8 w-8 text-emerald-200" />
                    </div>

                    <!-- Content -->
                    <div class="flex flex-1 flex-col p-5">
                        <div class="mb-2 flex items-center gap-2">
                            <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-[11px] font-semibold text-emerald-700 ring-1 ring-emerald-600/10 transition-colors group-hover:bg-amber-50 group-hover:text-amber-800 group-hover:ring-amber-400/20">
                                {{ post.category_name }}
                            </span>
                            <span class="text-[11px] text-stone-400">{{ timeAgo(post.created_at) }}</span>
                        </div>
                        <h3 class="line-clamp-2 text-[15px] font-bold leading-snug text-emerald-950 transition-colors group-hover:text-emerald-800">{{ post.title }}</h3>
                        <div class="mt-auto flex items-center gap-2 pt-3 text-xs text-stone-400">
                            <span class="font-medium text-stone-500">{{ post.author_name }}</span>
                            <span class="h-1 w-1 rounded-full bg-stone-300"></span>
                            <span class="flex items-center gap-0.5">
                                <MapPin class="h-3 w-3" />
                                {{ post.city }}
                            </span>
                        </div>
                    </div>
                </Link>
            </div>
        </div>

        <!-- CTA Section for guests -->
        <div v-if="!$page.props.auth.user" class="mt-16 overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-800 via-emerald-700 to-teal-600 p-8 text-center sm:p-12">
            <div class="absolute inset-0 bg-[linear-gradient(to_right,#ffffff08_1px,transparent_1px),linear-gradient(to_bottom,#ffffff08_1px,transparent_1px)] bg-[size:32px_32px]"></div>
            <h2 class="relative text-2xl font-extrabold text-white sm:text-3xl">Ready to join your local community?</h2>
            <p class="relative mx-auto mt-3 max-w-lg text-base text-emerald-100/80">Get verified for just $1 and start posting, connecting, and discovering what's happening around you.</p>
            <div class="relative mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row">
                <Link href="/register" class="inline-flex h-12 items-center justify-center rounded-full bg-white px-8 text-base font-bold text-emerald-900 shadow-lg transition-all hover:bg-emerald-50 hover:shadow-xl">
                    Get Started — It's Free
                </Link>
                <Link href="/about" class="inline-flex h-12 items-center justify-center rounded-full border border-white/25 px-8 text-base font-medium text-white transition-all hover:border-white/50 hover:bg-white/10">
                    Learn More
                </Link>
            </div>
        </div>
    </div>
</template>
