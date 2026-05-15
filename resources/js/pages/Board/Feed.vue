<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { 
    Search, MapPin, Image as ImageIcon, ChevronLeft
} from 'lucide-vue-next';
import { ref, watch, computed } from 'vue';

const props = defineProps<{
    categories: any[];
    posts: any;
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
let searchTimeout: ReturnType<typeof setTimeout> | null = null;

const triggerSearch = () => {
    if (searchTimeout) {
clearTimeout(searchTimeout);
}
    
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
                <span v-if="currentCategory">{{ currentCategory.name }}</span>
                <span v-else-if="$page.props.location">{{ $page.props.location }} Feed</span>
                <span v-else>Local Feed</span>
            </h1>
            
            <div class="flex w-full flex-col items-center gap-3 sm:w-auto sm:flex-row">
                <div class="relative w-full sm:w-48">
                    <MapPin class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-emerald-600/60" />
                    <input 
                        v-model="locationQuery" 
                        type="text" 
                        placeholder="City or Zip" 
                        class="h-10 w-full rounded-full border border-emerald-900/10 bg-white pl-9 text-sm text-stone-900 shadow-sm transition-colors focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                    />
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
            </div>
        </div>

        <div class="space-y-6">
            <div 
                v-for="post in posts.data" 
                :key="post.id" 
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
                    <p class="line-clamp-3 whitespace-pre-wrap text-stone-600">{{ post.description }}</p>
                </div>
                <div class="p-6 pt-0">
                    <Link :href="`/posts/${post.id}`" class="inline-block w-full sm:w-auto">
                        <button class="flex w-full items-center justify-center rounded-full border border-emerald-800 bg-transparent px-6 py-2 text-sm font-medium text-emerald-900 transition-colors hover:bg-emerald-800 hover:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                            View Details
                        </button>
                    </Link>
                </div>
            </div>

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
