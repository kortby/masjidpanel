<script setup lang="ts">
import { Head, Link, useForm, router } from '@inertiajs/vue3';

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

const deletePost = () => {
    if (confirm('Are you sure you want to delete this post? This cannot be undone.')) {
        router.delete(`/posts/${props.post.id}`);
    }
};
</script>

<template>
    <Head :title="post.title" />

    <div class="mx-auto w-full max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
        <Link href="/" class="mb-6 inline-block text-sm text-stone-500 transition-colors hover:text-emerald-800 hover:underline">&larr; Back to Board</Link>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
            <!-- Main Content -->
            <div class="space-y-6 md:col-span-2">
                <div class="overflow-hidden rounded-3xl border border-emerald-900/10 bg-white shadow-sm">
                    <div class="p-6 sm:p-8">
                        <div class="mb-4 flex items-start justify-between">
                            <span class="inline-flex items-center rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-0.5 text-xs font-semibold text-emerald-800">
                                {{ post.category.name }}
                            </span>
                            <span class="text-sm text-stone-500">{{ new Date(post.created_at).toLocaleDateString() }}</span>
                        </div>
                        
                        <h1 class="mb-2 text-3xl font-extrabold tracking-tight text-emerald-950">{{ post.title }}</h1>
                        
                        <p class="mb-6 text-stone-500">
                            Posted by <Link :href="`/users/${post.author_id}`" class="font-medium text-emerald-700 transition-colors hover:text-emerald-800 hover:underline">{{ post.author_name }}</Link> in {{ post.city }}<span v-if="post.zip_code">, {{ post.zip_code }}</span>
                        </p>
                        
                        <div v-if="post.meta" class="mb-8 flex flex-wrap gap-2">
                            <span v-for="(value, key) in post.meta" :key="key" class="inline-flex items-center rounded-full bg-stone-100 px-3 py-1 text-xs font-medium capitalize text-stone-700">
                                {{ key.replace('_', ' ') }}: {{ value }}
                            </span>
                        </div>

                        <div class="prose prose-stone max-w-none prose-p:leading-relaxed prose-a:text-emerald-700 hover:prose-a:text-emerald-800">
                            <p class="whitespace-pre-wrap text-stone-700">{{ post.description }}</p>
                        </div>
                        
                        <div v-if="post.images && post.images.length > 0" class="mt-8">
                            <h4 class="mb-4 font-bold text-emerald-950">Images</h4>
                            <div class="flex gap-4 overflow-x-auto pb-4">
                                <img 
                                    v-for="(image, index) in post.images" 
                                    :key="index" 
                                    :src="image.url" 
                                    class="h-48 w-auto rounded-xl border border-stone-200 object-cover shadow-sm" 
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact / Action Sidebar -->
            <div class="space-y-6">
                <div v-if="post.is_author" class="rounded-3xl border border-emerald-900/10 bg-emerald-50 p-6 shadow-sm">
                    <h3 class="mb-2 text-lg font-bold text-emerald-950">Your Post</h3>
                    <p class="mb-6 text-sm text-stone-600">This is how your post appears to others on the board.</p>
                    
                    <div class="flex flex-col gap-3">
                        <Link :href="`/posts/${post.id}/edit`" class="flex w-full items-center justify-center rounded-full border border-emerald-800 bg-transparent px-4 py-2 text-sm font-medium text-emerald-900 transition-colors hover:bg-emerald-800 hover:text-white">
                            Edit Post
                        </Link>
                        <button @click="deletePost" class="flex w-full items-center justify-center rounded-full bg-red-500 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                            Delete Post
                        </button>
                    </div>
                </div>

                <div v-else class="rounded-3xl border border-emerald-900/10 bg-white p-6 shadow-sm">
                    <h3 class="mb-2 text-xl font-bold text-emerald-950">Contact Author</h3>
                    <p v-if="!isVerified" class="mb-6 text-sm text-stone-500">
                        You must be a verified community member to contact others.
                    </p>

                    <div v-if="!isVerified" class="space-y-4 text-center">
                        <p class="text-sm text-stone-600">Get verified for $1.00 to unlock communication features and help keep our community spam-free.</p>
                        <Link :href="$page.props.auth.user ? '/checkout' : '/register'" class="inline-flex w-full items-center justify-center rounded-full bg-emerald-800 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                            Get Verified Now
                        </Link>
                    </div>

                    <div v-else>
                        <!-- Author chose to display public contact info -->
                        <div v-if="post.has_public_contact" class="space-y-4">
                            <div v-if="post.contact_info?.phone" class="flex items-center justify-between rounded-xl bg-stone-50 p-4">
                                <span class="text-sm font-medium text-stone-500">Phone:</span>
                                <span class="font-semibold text-emerald-950">{{ post.contact_info.phone }}</span>
                            </div>
                            <div v-if="post.contact_info?.email" class="flex items-center justify-between rounded-xl bg-stone-50 p-4">
                                <span class="text-sm font-medium text-stone-500">Email:</span>
                                <span class="font-semibold text-emerald-950">{{ post.contact_info.email }}</span>
                            </div>
                        </div>

                        <!-- Author chose to hide info, use internal relay -->
                        <div v-else class="space-y-4">
                            <p class="mb-4 text-sm text-stone-600">The author has chosen to keep their contact information private. You can send them a secure message below.</p>
                            
                            <form @submit.prevent="submitMessage" class="space-y-4">
                                <div class="space-y-2">
                                    <label for="message" class="block text-sm font-medium leading-none text-emerald-950">Your Message</label>
                                    <textarea 
                                        id="message" 
                                        v-model="form.message" 
                                        rows="4" 
                                        class="flex w-full rounded-md border border-stone-200 bg-transparent px-3 py-2 text-sm placeholder:text-stone-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                        placeholder="Write a message..."
                                        required
                                    ></textarea>
                                    <p v-if="form.errors.message" class="text-sm font-medium text-red-500">{{ form.errors.message }}</p>
                                </div>
                                <button type="submit" :disabled="form.processing" class="inline-flex w-full items-center justify-center rounded-full bg-emerald-800 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 disabled:pointer-events-none disabled:opacity-50">
                                    Send Message
                                </button>
                            </form>
                            
                            <p v-if="$page.props.flash?.success" class="mt-2 text-sm font-medium text-emerald-600">
                                {{ $page.props.flash.success }}
                            </p>
                            <p v-if="$page.props.flash?.error" class="mt-2 text-sm font-medium text-red-500">
                                {{ $page.props.flash.error }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
