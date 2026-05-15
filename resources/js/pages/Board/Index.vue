<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { 
    Search, MapPin, Briefcase, Home, Heart, Users, 
    ShoppingBag, GraduationCap, Wrench, Activity, Car, 
    Calendar, Globe, Plus, Hash
} from 'lucide-vue-next';
import { ref } from 'vue';
import GuestHero from '@/components/GuestHero.vue';

const props = defineProps<{
    categories: any[];
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

const handleSearch = () => {
    router.get('/feed', { 
        search: searchQuery.value,
        location: locationQuery.value
    });
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
        <!-- Global Search Bar for Homepage -->
        <div class="mx-auto mb-16 max-w-3xl">
            <h1 class="mb-8 text-center text-3xl font-extrabold tracking-tight text-emerald-950 md:text-4xl">What are you looking for locally?</h1>
            <form @submit.prevent="handleSearch" class="flex flex-col gap-3 sm:flex-row">
                <div class="relative w-full sm:w-1/3">
                    <MapPin class="absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-emerald-600/60" />
                    <input 
                        v-model="locationQuery" 
                        type="text" 
                        placeholder="City or Zip" 
                        class="h-14 w-full rounded-full border border-emerald-900/10 bg-white pl-12 text-base text-stone-900 shadow-sm transition-colors focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                    />
                </div>
                <div class="relative w-full sm:w-2/3">
                    <Search class="absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-emerald-600/60" />
                    <input 
                        v-model="searchQuery" 
                        type="search" 
                        placeholder="Search posts..." 
                        class="h-14 w-full rounded-full border border-emerald-900/10 bg-white pl-12 text-base text-stone-900 shadow-sm transition-colors focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                    />
                </div>
                <button type="submit" class="hidden"></button>
            </form>
        </div>

        <!-- Categories Grid -->
        <div>
            <h2 class="mb-6 px-1 text-xl font-bold tracking-tight text-emerald-900">Browse Categories</h2>
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6">
                <Link 
                    href="/feed" 
                    class="group relative flex h-36 flex-col items-center justify-center rounded-3xl border border-emerald-900/10 bg-white p-4 text-center text-stone-700 shadow-sm transition-all hover:border-amber-400 hover:shadow-md"
                >
                    <div class="mb-3 rounded-full bg-emerald-50 p-4 text-emerald-700 transition-transform group-hover:scale-110">
                        <Hash class="h-6 w-6" />
                    </div>
                    <span class="text-sm font-medium leading-tight">All Categories</span>
                </Link>
                
                <Link 
                    v-for="category in categories" 
                    :key="category.id"
                    :href="`/feed?category_id=${category.id}`"
                    class="group relative flex h-36 flex-col items-center justify-center rounded-3xl border border-emerald-900/10 bg-white p-4 text-center text-stone-700 shadow-sm transition-all hover:border-amber-400 hover:shadow-md"
                >
                    <div class="mb-3 rounded-full bg-emerald-50 p-4 text-emerald-700 transition-transform group-hover:scale-110">
                        <component :is="getCategoryIcon(category.name)" class="h-6 w-6" />
                    </div>
                    <span class="text-sm font-medium leading-tight">{{ category.name.replace(' (Suggest a Category)', '') }}</span>
                    <span class="absolute right-3 top-3 flex min-w-[22px] items-center justify-center rounded-full bg-stone-100 px-1.5 py-0.5 text-[10px] font-bold text-stone-600 shadow-sm group-hover:bg-amber-100 group-hover:text-amber-800">{{ category.posts_count }}</span>
                </Link>
            </div>
        </div>
    </div>
</template>
