<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps<{
    categories: any[];
    post: any;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Board', href: '/' },
            { title: 'Edit Post', href: '#' }
        ]
    }
});

const form = useForm({
    category_id: props.post.category_id,
    title: props.post.title,
    description: props.post.description,
    city: props.post.city,
    zip_code: props.post.zip_code || '',
    meta: props.post.meta || {} as Record<string, string>,
});

const selectedCategory = computed(() => {
    return props.categories.find(c => c.id === form.category_id);
});

const submit = () => {
    form.put(`/posts/${props.post.id}`);
};
</script>

<template>
    <Head title="Edit Post" />

    <div class="mx-auto flex min-h-[calc(100vh-4rem)] w-full items-center justify-center p-4 sm:p-6 lg:p-8">
        <div class="w-full max-w-2xl">
            <div class="overflow-hidden rounded-3xl border border-emerald-900/10 bg-white shadow-sm">
                <div class="border-b border-emerald-900/10 px-6 py-8 sm:px-8">
                    <h2 class="text-2xl font-bold tracking-tight text-emerald-950">Edit Post</h2>
                    <p class="mt-2 text-sm text-stone-500">Update your post details and location.</p>
                </div>
                
                <div class="px-6 py-8 sm:px-8">
                    <div class="space-y-6">
                        <div class="space-y-4">
                            <label class="block text-sm font-bold text-emerald-950">Category</label>
                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                <div 
                                    v-for="category in categories" 
                                    :key="category.id"
                                    @click="form.category_id = category.id"
                                    class="cursor-pointer rounded-2xl border p-4 transition-all"
                                    :class="form.category_id === category.id ? 'border-emerald-500 bg-emerald-50 shadow-sm ring-1 ring-emerald-500' : 'border-stone-200 hover:border-emerald-300 hover:bg-stone-50'"
                                >
                                    <span class="text-sm font-semibold" :class="form.category_id === category.id ? 'text-emerald-900' : 'text-stone-700'">{{ category.name }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label for="title" class="block text-sm font-bold text-emerald-950">Title <span class="text-red-500">*</span></label>
                            <input id="title" v-model="form.title" required class="h-10 w-full rounded-xl border border-stone-200 bg-transparent px-3 py-2 text-sm text-stone-900 placeholder:text-stone-400 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500" />
                            <p v-if="form.errors.title" class="text-sm font-medium text-red-500">{{ form.errors.title }}</p>
                        </div>

                        <div class="space-y-2">
                            <label for="description" class="block text-sm font-bold text-emerald-950">Description <span class="text-red-500">*</span></label>
                            <textarea 
                                id="description" 
                                v-model="form.description" 
                                rows="5" 
                                class="flex w-full rounded-xl border border-stone-200 bg-transparent px-3 py-2 text-sm text-stone-900 placeholder:text-stone-400 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500 disabled:cursor-not-allowed disabled:opacity-50"
                                required
                            ></textarea>
                            <p v-if="form.errors.description" class="text-sm font-medium text-red-500">{{ form.errors.description }}</p>
                        </div>

                        <!-- Dynamic Meta Fields -->
                        <div v-if="selectedCategory?.name === 'Jobs & Hiring'" class="space-y-2">
                            <label for="job_type" class="block text-sm font-bold text-emerald-950">Job Type</label>
                            <select id="job_type" v-model="form.meta.job_type" class="flex h-10 w-full rounded-xl border border-stone-200 bg-transparent px-3 py-1 text-sm text-stone-900 transition-colors focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                                <option value="" disabled>Select type...</option>
                                <option value="Full-Time">Full-Time</option>
                                <option value="Part-Time">Part-Time</option>
                                <option value="Contract">Contract</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label for="city" class="block text-sm font-bold text-emerald-950">City <span class="text-red-500">*</span></label>
                            <input id="city" v-model="form.city" required class="h-10 w-full rounded-xl border border-stone-200 bg-transparent px-3 py-2 text-sm text-stone-900 placeholder:text-stone-400 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500" />
                            <p v-if="form.errors.city" class="text-sm font-medium text-red-500">{{ form.errors.city }}</p>
                        </div>

                        <div class="space-y-2">
                            <label for="zip_code" class="block text-sm font-bold text-emerald-950">Zip Code (Optional)</label>
                            <input id="zip_code" v-model="form.zip_code" class="h-10 w-full rounded-xl border border-stone-200 bg-transparent px-3 py-2 text-sm text-stone-900 placeholder:text-stone-400 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500" />
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between border-t border-emerald-900/10 bg-stone-50 px-6 py-4 sm:px-8">
                    <button @click="() => router.visit(`/posts/${post.id}`)" class="rounded-full px-6 py-2 text-sm font-medium text-stone-600 transition-colors hover:bg-stone-200 hover:text-stone-900">Cancel</button>
                    <button @click="submit" :disabled="form.processing" class="rounded-full bg-emerald-800 px-8 py-2 text-sm font-bold text-white transition-colors hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 disabled:pointer-events-none disabled:opacity-50">Save Changes</button>
                </div>
            </div>
        </div>
    </div>
</template>
