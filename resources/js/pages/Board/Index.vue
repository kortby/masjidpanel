<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Card, CardHeader, CardTitle, CardDescription, CardContent, CardFooter } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';

defineProps<{
    categories: any[];
    posts: any;
    filters: any;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Board', href: '/' }
        ]
    }
});
</script>

<template>
    <Head title="Digital Board" />

    <div class="flex flex-col md:flex-row gap-6 p-4 h-full max-w-7xl mx-auto">
        <!-- Sidebar: Categories -->
        <div class="w-full md:w-64 shrink-0">
            <div class="sticky top-4">
                <h3 class="font-semibold text-lg mb-4">Categories</h3>
                <div class="flex flex-col gap-1">
                    <Link 
                        href="/" 
                        class="px-3 py-2 rounded-md text-sm transition-colors hover:bg-muted"
                        :class="{ 'bg-muted font-medium text-foreground': !filters.category_id, 'text-muted-foreground': filters.category_id }"
                    >
                        All Categories
                    </Link>
                    <Link 
                        v-for="category in categories" 
                        :key="category.id"
                        :href="`/?category_id=${category.id}`"
                        class="px-3 py-2 rounded-md text-sm transition-colors hover:bg-muted flex justify-between items-center"
                        :class="{ 'bg-muted font-medium text-foreground': filters.category_id == category.id, 'text-muted-foreground': filters.category_id != category.id }"
                    >
                        <span>{{ category.name }}</span>
                        <Badge variant="secondary" class="ml-2 font-normal">{{ category.posts_count }}</Badge>
                    </Link>
                </div>
            </div>
        </div>

        <!-- Main Feed -->
        <div class="flex-1 min-w-0">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold tracking-tight">
                    <span v-if="$page.props.location">{{ $page.props.location }} Board</span>
                    <span v-else>Digital Board</span>
                </h1>
                <Link href="/posts/create">
                    <Button>Create Post</Button>
                </Link>
            </div>

            <div class="space-y-4">
                <Card v-for="post in posts.data" :key="post.id" class="overflow-hidden transition-all hover:shadow-md">
                    <CardHeader class="pb-2">
                        <div class="flex justify-between items-start">
                            <div>
                                <Badge variant="outline" class="mb-2">{{ post.category.name }}</Badge>
                                <CardTitle class="text-xl">{{ post.title }}</CardTitle>
                                <CardDescription class="mt-1">
                                    Posted by {{ post.user.name }} &bull; {{ new Date(post.created_at).toLocaleDateString() }}
                                </CardDescription>
                            </div>
                            <!-- Render dynamic meta tags if they exist -->
                            <div class="flex gap-2 flex-wrap justify-end items-center">
                                <Badge v-if="post.meta?.job_type" variant="secondary">{{ post.meta.job_type }}</Badge>
                                <Badge v-if="post.meta?.price" variant="secondary">${{ post.meta.price }}</Badge>
                                <span v-if="post.media && post.media.length > 0" class="text-xs text-muted-foreground flex items-center gap-1" title="Has Images">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                                    {{ post.media.length }}
                                </span>
                            </div>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <p class="text-muted-foreground whitespace-pre-wrap line-clamp-3">{{ post.description }}</p>
                    </CardContent>
                    <CardFooter class="pt-0">
                        <Link :href="`/posts/${post.id}`">
                            <Button variant="outline" size="sm">View Details</Button>
                        </Link>
                    </CardFooter>
                </Card>

                <div v-if="posts.data.length === 0" class="text-center py-12 text-muted-foreground bg-muted/30 rounded-xl border border-dashed">
                    <p>No posts found in this category for your location.</p>
                </div>
            </div>
        </div>
    </div>
</template>
