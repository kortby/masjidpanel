<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { Badge } from '@/components/ui/badge';
import { Input } from '@/components/ui/input';
import GuestHero from '@/components/GuestHero.vue';
import { 
    Search, MapPin, Briefcase, Home, Heart, Users, 
    ShoppingBag, GraduationCap, Wrench, Activity, Car, 
    Calendar, Globe, Plus, Hash
} from 'lucide-vue-next';
import { ref } from 'vue';

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

    <div class="px-4 py-12 max-w-7xl mx-auto w-full">
        <!-- Global Search Bar for Homepage -->
        <div class="max-w-3xl mx-auto mb-16">
            <h1 class="text-3xl md:text-4xl font-extrabold text-center mb-8 tracking-tight">What are you looking for locally?</h1>
            <form @submit.prevent="handleSearch" class="flex flex-col sm:flex-row gap-3">
                <div class="relative w-full sm:w-1/3">
                    <MapPin class="absolute left-4 top-1/2 -translate-y-1/2 h-5 w-5 text-muted-foreground" />
                    <Input 
                        v-model="locationQuery" 
                        type="text" 
                        placeholder="City or Zip" 
                        class="pl-12 bg-card h-14 border-muted-foreground/20 rounded-full text-base shadow-sm"
                    />
                </div>
                <div class="relative w-full sm:w-2/3">
                    <Search class="absolute left-4 top-1/2 -translate-y-1/2 h-5 w-5 text-muted-foreground" />
                    <Input 
                        v-model="searchQuery" 
                        type="search" 
                        placeholder="Search posts..." 
                        class="pl-12 bg-card h-14 border-muted-foreground/20 rounded-full text-base shadow-sm"
                    />
                </div>
                <button type="submit" class="hidden"></button>
            </form>
        </div>

        <!-- Categories Grid -->
        <div>
            <h2 class="text-xl font-bold tracking-tight mb-6 px-1">Browse Categories</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                <Link 
                    href="/feed" 
                    class="group relative flex flex-col items-center justify-center p-4 h-36 bg-card border border-muted-foreground/10 rounded-3xl hover:border-primary hover:shadow-lg transition-all text-center"
                >
                    <div class="p-4 rounded-full bg-primary/10 text-primary mb-3 group-hover:scale-110 transition-transform">
                        <Hash class="h-6 w-6" />
                    </div>
                    <span class="text-sm font-medium leading-tight">All Categories</span>
                </Link>
                
                <Link 
                    v-for="category in categories" 
                    :key="category.id"
                    :href="`/feed?category_id=${category.id}`"
                    class="group relative flex flex-col items-center justify-center p-4 h-36 bg-card border border-muted-foreground/10 rounded-3xl hover:border-primary hover:shadow-lg transition-all text-center"
                >
                    <div class="p-4 rounded-full bg-primary/10 text-primary mb-3 group-hover:scale-110 transition-transform">
                        <component :is="getCategoryIcon(category.name)" class="h-6 w-6" />
                    </div>
                    <span class="text-sm font-medium leading-tight">{{ category.name.replace(' (Suggest a Category)', '') }}</span>
                    <Badge variant="secondary" class="absolute top-3 right-3 px-1.5 min-w-[22px] h-5 flex items-center justify-center rounded-full text-[10px] shadow-sm">{{ category.posts_count }}</Badge>
                </Link>
            </div>
        </div>
    </div>
</template>
