<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Card, CardHeader, CardTitle, CardDescription, CardContent, CardFooter } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

const props = defineProps<{
    post: any;
    isVerified: boolean;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Board', href: '/' },
            { title: 'Post Details', href: '#' }
        ]
    }
});

const form = useForm({
    message: '',
});

const submitMessage = () => {
    form.post(`/posts/${props.post.id}/message`, {
        preserveScroll: true,
        onSuccess: () => form.reset('message'),
    });
};
</script>

<template>
    <Head :title="post.title" />

    <div class="max-w-4xl mx-auto p-4 py-8">
        <Link href="/" class="text-sm text-muted-foreground hover:underline mb-6 inline-block">&larr; Back to Board</Link>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="md:col-span-2 space-y-6">
                <Card>
                    <CardHeader>
                        <div class="flex justify-between items-start mb-2">
                            <Badge variant="outline">{{ post.category.name }}</Badge>
                            <span class="text-sm text-muted-foreground">{{ new Date(post.created_at).toLocaleDateString() }}</span>
                        </div>
                        <CardTitle class="text-3xl">{{ post.title }}</CardTitle>
                        <CardDescription>
                            Posted by {{ post.author_name }} in {{ post.city }}<span v-if="post.zip_code">, {{ post.zip_code }}</span>
                        </CardDescription>
                        
                        <div v-if="post.meta" class="flex gap-2 flex-wrap mt-4">
                            <Badge v-for="(value, key) in post.meta" :key="key" variant="secondary" class="capitalize">
                                {{ key.replace('_', ' ') }}: {{ value }}
                            </Badge>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div class="prose prose-sm dark:prose-invert max-w-none">
                            <p class="whitespace-pre-wrap">{{ post.description }}</p>
                        </div>
                        
                        <div v-if="post.images && post.images.length > 0" class="mt-8">
                            <h4 class="font-semibold mb-3">Images</h4>
                            <div class="flex gap-4 overflow-x-auto pb-2">
                                <img 
                                    v-for="(image, index) in post.images" 
                                    :key="index" 
                                    :src="image.url" 
                                    class="h-48 w-auto rounded-md object-cover border" 
                                />
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Contact / Action Sidebar -->
            <div class="space-y-6">
                <Card v-if="post.is_author" class="bg-muted/50">
                    <CardHeader>
                        <CardTitle class="text-lg">Your Post</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p class="text-sm text-muted-foreground">This is how your post appears to others on the board.</p>
                    </CardContent>
                </Card>

                <Card v-else>
                    <CardHeader>
                        <CardTitle class="text-xl">Contact Author</CardTitle>
                        <CardDescription v-if="!isVerified">
                            You must be a verified community member to contact others.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div v-if="!isVerified" class="text-center py-4 space-y-4">
                            <p class="text-sm text-muted-foreground">Get verified for $1.00 to unlock communication features and help keep our community spam-free.</p>
                            <Link href="/checkout">
                                <Button class="w-full">Get Verified Now</Button>
                            </Link>
                        </div>

                        <div v-else>
                            <!-- Author chose to display public contact info -->
                            <div v-if="post.has_public_contact" class="space-y-4">
                                <div v-if="post.contact_info?.phone" class="p-3 bg-muted rounded-md flex justify-between items-center">
                                    <span class="text-sm font-medium">Phone:</span>
                                    <span class="text-sm">{{ post.contact_info.phone }}</span>
                                </div>
                                <div v-if="post.contact_info?.email" class="p-3 bg-muted rounded-md flex justify-between items-center">
                                    <span class="text-sm font-medium">Email:</span>
                                    <span class="text-sm">{{ post.contact_info.email }}</span>
                                </div>
                            </div>

                            <!-- Author chose to hide info, use internal relay -->
                            <div v-else class="space-y-4">
                                <p class="text-sm text-muted-foreground">The author has chosen to keep their contact information private. You can send them a secure message below.</p>
                                
                                <form @submit.prevent="submitMessage" class="space-y-3">
                                    <div class="space-y-2">
                                        <Label for="message">Your Message</Label>
                                        <textarea 
                                            id="message" 
                                            v-model="form.message" 
                                            rows="4" 
                                            class="flex w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                                            placeholder="Write a message..."
                                            required
                                        ></textarea>
                                        <p v-if="form.errors.message" class="text-sm text-destructive">{{ form.errors.message }}</p>
                                    </div>
                                    <Button type="submit" class="w-full" :disabled="form.processing">Send Message</Button>
                                </form>
                                
                                <p v-if="$page.props.flash?.success" class="text-sm text-green-600 mt-2">
                                    {{ $page.props.flash.success }}
                                </p>
                                <p v-if="$page.props.flash?.error" class="text-sm text-destructive mt-2">
                                    {{ $page.props.flash.error }}
                                </p>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </div>
</template>
