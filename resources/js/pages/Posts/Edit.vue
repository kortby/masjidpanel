<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardHeader, CardTitle, CardDescription, CardContent, CardFooter } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

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

    <div class="flex items-center justify-center p-4 min-h-full">
        <div class="w-full max-w-2xl">
            <Card>
                <CardHeader>
                    <CardTitle>Edit Post</CardTitle>
                    <CardDescription>Update your post details and location.</CardDescription>
                </CardHeader>
                
                <CardContent class="space-y-6">
                    <div class="space-y-4">
                        <Label>Category</Label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div 
                                v-for="category in categories" 
                                :key="category.id"
                                @click="form.category_id = category.id"
                                class="p-4 rounded-xl border cursor-pointer transition-all"
                                :class="form.category_id === category.id ? 'border-primary bg-primary/5 ring-2 ring-primary ring-offset-2' : 'hover:border-primary/50 hover:bg-muted'"
                            >
                                <span class="font-medium text-sm">{{ category.name }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <Label for="title">Title <span class="text-destructive">*</span></Label>
                        <Input id="title" v-model="form.title" required />
                        <p v-if="form.errors.title" class="text-sm text-destructive">{{ form.errors.title }}</p>
                    </div>

                    <div class="space-y-2">
                        <Label for="description">Description <span class="text-destructive">*</span></Label>
                        <textarea 
                            id="description" 
                            v-model="form.description" 
                            rows="5" 
                            class="flex w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                            required
                        ></textarea>
                        <p v-if="form.errors.description" class="text-sm text-destructive">{{ form.errors.description }}</p>
                    </div>

                    <!-- Dynamic Meta Fields -->
                    <div v-if="selectedCategory?.name === 'Jobs & Hiring'" class="space-y-2">
                        <Label for="job_type">Job Type</Label>
                        <select id="job_type" v-model="form.meta.job_type" class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                            <option value="" disabled>Select type...</option>
                            <option value="Full-Time">Full-Time</option>
                            <option value="Part-Time">Part-Time</option>
                            <option value="Contract">Contract</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <Label for="city">City <span class="text-destructive">*</span></Label>
                        <Input id="city" v-model="form.city" required />
                        <p v-if="form.errors.city" class="text-sm text-destructive">{{ form.errors.city }}</p>
                    </div>

                    <div class="space-y-2">
                        <Label for="zip_code">Zip Code (Optional)</Label>
                        <Input id="zip_code" v-model="form.zip_code" />
                    </div>
                </CardContent>

                <CardFooter class="flex justify-between">
                    <Button variant="outline" @click="() => router.visit(`/posts/${post.id}`)">Cancel</Button>
                    <Button @click="submit" :disabled="form.processing">Save Changes</Button>
                </CardFooter>
            </Card>
        </div>
    </div>
</template>
