<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { Card, CardHeader, CardTitle, CardDescription, CardContent, CardFooter } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
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
    if (searchTimeout) clearTimeout(searchTimeout);
    
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
    if (!props.filters.category_id) return null;
    return props.categories.find(c => c.id == props.filters.category_id);
});
</script>

<template>
    <Head title="Feed" />

    <div class="px-4 py-8 max-w-4xl mx-auto w-full">
        <Link href="/" class="inline-flex items-center text-sm font-medium text-muted-foreground hover:text-foreground mb-6 transition-colors">
            <ChevronLeft class="h-4 w-4 mr-1" />
            Back to Categories
        </Link>

        <!-- Search & Feed Section -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <h1 class="text-2xl font-bold tracking-tight">
                <span v-if="currentCategory">{{ currentCategory.name }}</span>
                <span v-else-if="$page.props.location">{{ $page.props.location }} Feed</span>
                <span v-else>Local Feed</span>
            </h1>
            
            <div class="flex w-full sm:w-auto flex-col sm:flex-row items-center gap-3">
                <div class="relative w-full sm:w-48">
                    <MapPin class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                    <Input 
                        v-model="locationQuery" 
                        type="text" 
                        placeholder="City or Zip" 
                        class="pl-9 bg-card h-10 border-muted-foreground/20 rounded-full"
                    />
                </div>
                <div class="relative w-full sm:w-64">
                    <Search class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                    <Input 
                        v-model="searchQuery" 
                        type="search" 
                        placeholder="Search posts..." 
                        class="pl-9 bg-card h-10 border-muted-foreground/20 rounded-full"
                    />
                </div>
                <Link href="/posts/create" class="shrink-0 w-full sm:w-auto">
                    <Button class="w-full rounded-full h-10 px-6 shadow-sm">Create Post</Button>
                </Link>
            </div>
        </div>

        <div class="space-y-4">
            <Card v-for="post in posts.data" :key="post.id" class="overflow-hidden transition-all hover:shadow-md group">
                <CardHeader class="pb-2">
                    <div class="flex justify-between items-start">
                        <div>
                            <Badge variant="secondary" class="mb-2 bg-primary/10 text-primary hover:bg-primary/20">{{ post.category.name }}</Badge>
                            <CardTitle class="text-xl group-hover:text-primary transition-colors">{{ post.title }}</CardTitle>
                            <CardDescription class="mt-1">
                                Posted by <span class="font-medium text-foreground/80">{{ post.user.name }}</span> &bull; {{ new Date(post.created_at).toLocaleDateString() }}
                            </CardDescription>
                        </div>
                        <div class="flex gap-2 flex-wrap justify-end items-center">
                            <Badge v-if="post.meta?.job_type" variant="outline" class="font-normal">{{ post.meta.job_type }}</Badge>
                            <Badge v-if="post.meta?.price" variant="outline" class="font-normal border-primary/20 text-primary">${{ post.meta.price }}</Badge>
                            <span v-if="post.media && post.media.length > 0" class="text-xs text-muted-foreground flex items-center gap-1 bg-muted px-2 py-1 rounded-md" title="Has Images">
                                <ImageIcon class="h-3 w-3" />
                                {{ post.media.length }}
                            </span>
                        </div>
                    </div>
                </CardHeader>
                <CardContent>
                    <p class="text-muted-foreground whitespace-pre-wrap line-clamp-3">{{ post.description }}</p>
                </CardContent>
                <CardFooter class="pt-0">
                    <Link :href="`/posts/${post.id}`" class="w-full sm:w-auto">
                        <Button variant="outline" class="w-full">View Details</Button>
                    </Link>
                </CardFooter>
            </Card>

            <div v-if="posts.data.length === 0" class="flex flex-col items-center justify-center py-20 text-center bg-card rounded-2xl border border-dashed border-muted-foreground/20">
                <div class="h-16 w-16 bg-muted rounded-full flex items-center justify-center mb-4">
                    <Search class="h-8 w-8 text-muted-foreground/50" />
                </div>
                <h3 class="text-xl font-bold">No posts found</h3>
                <p class="text-muted-foreground max-w-sm mt-2">There are no posts matching your current filters or location.</p>
                <Button variant="outline" class="mt-6" @click="() => { searchQuery = ''; locationQuery = ''; }">Clear Filters</Button>
            </div>
        </div>
    </div>
</template>
