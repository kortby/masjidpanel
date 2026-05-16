<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { 
    Search, MapPin, Image as ImageIcon, ChevronLeft, X, SlidersHorizontal, Tag
} from 'lucide-vue-next';
import { ref, watch, computed } from 'vue';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';

const props = defineProps<{
    categories: any[];
    posts: { data: any[], [key: string]: any };
    popularTags: { id: number; name: string; slug: string; posts_count: number }[];
    filters: any;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Categories', href: '/' },
            { title: 'Feed', href: '/feed' }
        ]
    }
});

const searchQuery = ref(props.filters.search || '');
const locationQuery = ref(props.filters.location || '');
const locationError = ref('');
let searchTimeout: ReturnType<typeof setTimeout> | null = null;

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

const triggerSearch = () => {
    if (searchTimeout) {
        clearTimeout(searchTimeout);
    }

    const loc = locationQuery.value.trim();
    
    // Don't auto-search if user is halfway through typing an invalid zipcode
    if (!/^(?:\d{5}(?:-\d{4})?|.*[a-zA-Z].*)$/.test(loc) && loc !== '') {
        return; 
    }
    
    // Clear any error when auto-searching
    locationError.value = '';
    
    searchTimeout = setTimeout(() => {
        router.get('/feed', { 
            category_id: props.filters.category_id, 
            search: searchQuery.value,
            location: locationQuery.value
        }, { 
            preserveState: true, 
            preserveScroll: true,
            replace: true
        });
    }, 400);
};

watch(searchQuery, triggerSearch);
watch(locationQuery, triggerSearch);

const currentCategory = computed(() => {
    if (!props.filters.category_id) {
return null;
}

    return props.categories.find(c => c.id == props.filters.category_id);
});
</script>

<template>
    <Head title="Feed" />

    <div class="mx-auto w-full max-w-4xl px-4 py-8">
        <Link href="/" class="mb-6 inline-flex items-center text-sm font-medium text-stone-500 transition-colors hover:text-emerald-900">
            <ChevronLeft class="mr-1 h-4 w-4" />
            Back to Categories
        </Link>

        <!-- Search & Feed Section -->
        <div class="mb-8 flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
            <h1 class="text-2xl font-bold tracking-tight text-emerald-950">
                <span v-if="filters.tag" class="mr-2 inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-sm font-semibold text-emerald-800">
                    #{{ filters.tag }}
                    <button @click="() => router.get('/feed', { ...filters, tag: null })" class="ml-2 hover:text-emerald-950">
                        <X class="h-3 w-3" />
                    </button>
                </span>
                <span v-if="currentCategory">{{ currentCategory.name }}</span>
                <span v-else-if="$page.props.location">{{ $page.props.location }} Feed</span>
                <span v-else>Local Feed</span>
            </h1>
            
            <form @submit.prevent="triggerSearch" class="flex w-full flex-col items-center gap-3 sm:w-auto sm:flex-row">
                <div class="relative w-full sm:w-48">
                    <MapPin class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-emerald-600/60" />
                    <input 
                        v-model="locationQuery" 
                        @blur="validateLocation"
                        @input="clearLocationError"
                        type="text" 
                        placeholder="City or Zip" 
                        :class="[
                            'h-10 w-full rounded-full border bg-white pl-9 text-sm text-stone-900 shadow-sm transition-colors focus:outline-none focus:ring-1',
                            locationError 
                                ? 'border-red-500 focus:border-red-500 focus:ring-red-500 bg-red-50/30' 
                                : 'border-emerald-900/10 focus:border-emerald-500 focus:ring-emerald-500'
                        ]"
                    />
                    <p v-if="locationError" class="absolute -bottom-6 left-3 text-[10px] font-medium text-red-500 whitespace-nowrap">{{ locationError }}</p>
                </div>
                <div class="relative w-full sm:w-64">
                    <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-emerald-600/60" />
                    <input 
                        v-model="searchQuery" 
                        type="search" 
                        placeholder="Search posts..." 
                        class="h-10 w-full rounded-full border border-emerald-900/10 bg-white pl-9 text-sm text-stone-900 shadow-sm transition-colors focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                    />
                </div>
                <Link href="/posts/create" class="w-full shrink-0 sm:w-auto">
                    <button class="flex h-10 w-full items-center justify-center rounded-full bg-emerald-800 px-6 text-sm font-medium text-white shadow-sm transition-colors hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                        Create Post
                    </button>
                </Link>
            <button type="submit" class="hidden"></button>
            </form>
        </div>

        <!-- Filters Row -->
        <div class="mb-8 space-y-4">
            <!-- Category Dropdown -->
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-2 text-sm font-medium text-stone-500">
                    <SlidersHorizontal class="h-4 w-4" />
                    <span class="hidden sm:inline">Filter:</span>
                </div>
                <Select
                    :model-value="filters.category_id ? String(filters.category_id) : 'all'"
                    @update:model-value="(val: string) => {
                        const params: any = { search: filters.search, location: filters.location, tag: filters.tag };
                        if (val !== 'all') params.category_id = val;
                        router.get('/feed', params, { preserveState: true, preserveScroll: true, replace: true });
                    }"
                >
                    <SelectTrigger class="h-10 w-full rounded-full border-emerald-900/10 bg-white text-sm shadow-sm sm:w-64">
                        <SelectValue placeholder="All Categories" />
                    </SelectTrigger>
                    <SelectContent class="rounded-xl">
                        <SelectItem value="all">
                            All Categories
                        </SelectItem>
                        <SelectItem
                            v-for="category in categories"
                            :key="category.id"
                            :value="String(category.id)"
                        >
                            {{ category.name }} ({{ category.posts_count }})
                        </SelectItem>
                    </SelectContent>
                </Select>
            </div>

            <!-- Trending Tags -->
            <div v-if="popularTags && popularTags.length > 0" class="flex flex-wrap items-center gap-2">
                <div class="flex items-center gap-1.5 text-xs font-medium text-stone-400">
                    <Tag class="h-3.5 w-3.5" />
                    <span>Trending:</span>
                </div>
                <button
                    v-for="pTag in popularTags"
                    :key="pTag.id"
                    @click="router.get('/feed', { ...filters, tag: pTag.slug }, { preserveState: true, preserveScroll: true, replace: true })"
                    :class="[
                        'inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-medium transition-all duration-200',
                        filters.tag === pTag.slug
                            ? 'bg-emerald-800 text-white shadow-sm'
                            : 'border border-emerald-900/8 bg-white text-stone-500 hover:border-emerald-400/40 hover:bg-emerald-50 hover:text-emerald-700'
                    ]"
                >
                    #{{ pTag.name }}
                    <span v-if="filters.tag === pTag.slug" class="ml-0.5">
                        <X class="h-3 w-3" />
                    </span>
                </button>
            </div>
        </div>

        <div class="space-y-6">
            <template v-for="(post, index) in posts.data" :key="post.id">
                <!-- Divider for non-local posts -->
                <div v-if="!post.is_local && filters.location && (index === 0 || posts.data[index - 1].is_local)" class="my-8 rounded-2xl bg-amber-50 p-6 text-center border border-amber-200">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-amber-100 text-amber-700 mb-3">
                        <MapPin class="h-6 w-6" />
                    </div>
                    <h3 v-if="index === 0" class="text-lg font-bold text-amber-900">No posts found in your area</h3>
                    <h3 v-else class="text-lg font-bold text-amber-900">Posts from other locations</h3>
                    
                    <p v-if="index === 0" class="mt-1 text-sm text-amber-700">We couldn't find any posts matching your exact location, but here are some from other areas.</p>
                    <p v-else class="mt-1 text-sm text-amber-700">You've reached the end of the local posts. Here are some from other areas.</p>
                </div>

                <div 
                    class="group overflow-hidden rounded-2xl border border-emerald-900/10 bg-white shadow-sm transition-all hover:border-amber-400 hover:shadow-md"
                >
                <div class="p-6 pb-2">
                    <div class="flex items-start justify-between">
                        <div>
                            <span class="mb-3 inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-semibold text-emerald-700 transition-colors group-hover:bg-amber-100 group-hover:text-amber-800">
                                {{ post.category.name }}
                            </span>
                            <h3 class="text-xl font-bold text-emerald-950 transition-colors group-hover:text-emerald-800">{{ post.title }}</h3>
                            <p class="mt-1 text-sm text-stone-500">
                                Posted by <Link :href="`/users/${post.user.id}`" class="font-medium text-stone-700 transition-colors hover:text-emerald-800 hover:underline">{{ post.user.name }}</Link> &bull; {{ new Date(post.created_at).toLocaleDateString() }}
                            </p>
                        </div>
                        <div class="flex flex-wrap items-center justify-end gap-2">
                            <span class="inline-flex items-center rounded-full border border-stone-200 bg-stone-50 px-2.5 py-0.5 text-xs font-medium text-stone-700">
                                <MapPin class="mr-1 h-3 w-3" />
                                {{ post.city }}<template v-if="post.zip_code">, {{ post.zip_code }}</template>
                            </span>
                            <span v-if="post.meta?.job_type" class="inline-flex items-center rounded-full border border-stone-200 px-2.5 py-0.5 text-xs font-medium text-stone-700">
                                {{ post.meta.job_type }}
                            </span>
                            <span v-if="post.meta?.price" class="inline-flex items-center rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-0.5 text-xs font-semibold text-emerald-800">
                                ${{ post.meta.price }}
                            </span>
                            <span v-if="post.media && post.media.length > 0" class="flex items-center gap-1 rounded-md bg-stone-100 px-2 py-1 text-xs text-stone-600" title="Has Images">
                                <ImageIcon class="h-3 w-3" />
                                {{ post.media.length }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="p-6 py-4">
                    <p class="line-clamp-3 whitespace-pre-wrap text-stone-600 mb-4">{{ post.description }}</p>
                    
                <div v-if="post.tags && post.tags.length > 0" class="flex flex-wrap gap-2">
                        <button 
                            v-for="tag in post.tags" 
                            :key="tag.id"
                            @click.stop="router.get('/feed', { ...filters, tag: tag.slug })"
                            :class="[
                                'inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-medium transition-all duration-200',
                                filters.tag === tag.slug
                                    ? 'bg-emerald-700 text-white shadow-sm'
                                    : 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-600/10 hover:bg-emerald-100 hover:text-emerald-900'
                            ]"
                        >
                            <Tag class="h-3 w-3" />
                            {{ tag.name }}
                        </button>
                    </div>
                </div>
                <div class="p-6 pt-0">
                    <Link :href="`/posts/${post.id}`" class="inline-block w-full sm:w-auto">
                        <button class="flex w-full items-center justify-center rounded-full border border-emerald-800 bg-transparent px-6 py-2 text-sm font-medium text-emerald-900 transition-colors hover:bg-emerald-800 hover:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                            View Details
                        </button>
                    </Link>
                </div>
            </div>
            </template>

            <div v-if="posts.data.length === 0" class="flex flex-col items-center justify-center rounded-3xl border border-dashed border-emerald-900/20 bg-stone-50 py-20 text-center">
                <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-white text-emerald-700 shadow-sm">
                    <Search class="h-8 w-8 text-emerald-600/50" />
                </div>
                <h3 class="text-xl font-bold text-emerald-950">No posts found</h3>
                <p class="mt-2 max-w-sm text-stone-600">There are no posts matching your current filters or location.</p>
                <button 
                    @click="() => { searchQuery = ''; locationQuery = ''; }"
                    class="mt-6 inline-flex items-center justify-center rounded-full border border-emerald-800 bg-transparent px-6 py-2 text-sm font-medium text-emerald-900 transition-colors hover:bg-emerald-800 hover:text-white"
                >
                    Clear Filters
                </button>
            </div>
        </div>
    </div>
</template>
