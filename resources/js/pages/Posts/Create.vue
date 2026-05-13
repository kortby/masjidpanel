<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardHeader, CardTitle, CardDescription, CardContent, CardFooter } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

const props = defineProps<{
    categories: any[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Board', href: '/' },
            { title: 'Create Post', href: '/posts/create' }
        ]
    }
});

const step = ref(1);
const page = usePage();

const form = useForm({
    category_id: '',
    title: '',
    description: '',
    city: page.props.location || '',
    zip_code: '',
    suggested_category_name: '',
    meta: {} as Record<string, string>,
    images: [] as File[],
});

const selectedCategory = computed(() => {
    return props.categories.find(c => c.id === form.category_id);
});

const isOtherCategory = computed(() => {
    return selectedCategory.value?.name === 'Other (Suggest a Category)';
});

const nextStep = () => {
    if (step.value === 1 && !form.category_id) return;
    if (step.value === 2 && (!form.title || !form.description)) return;
    step.value++;
};

const prevStep = () => {
    step.value--;
};

const submit = () => {
    form.post('/posts', {
        onSuccess: () => {
            // Handled by redirect
        }
    });
};
</script>

<template>
    <Head title="Create Post" />

    <div class="flex items-center justify-center p-4 min-h-full">
        <div class="w-full max-w-2xl">
            <Card>
                <CardHeader>
                    <CardTitle>Create a New Post</CardTitle>
                    <CardDescription>Step {{ step }} of 3: {{ step === 1 ? 'Select Category' : (step === 2 ? 'Post Details' : 'Location & Review') }}</CardDescription>
                </CardHeader>
                
                <CardContent>
                    <!-- Step 1: Category -->
                    <div v-if="step === 1" class="space-y-4">
                        <Label>Select a Category</Label>
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

                    <!-- Step 2: Details -->
                    <div v-if="step === 2" class="space-y-6">
                        <div v-if="isOtherCategory" class="space-y-2">
                            <Label for="suggested_name">Suggest Category Name <span class="text-destructive">*</span></Label>
                            <Input id="suggested_name" v-model="form.suggested_category_name" placeholder="e.g. Lost & Found" required />
                            <p class="text-xs text-muted-foreground">This suggestion will be reviewed by admins.</p>
                        </div>

                        <div class="space-y-2">
                            <Label for="title">Title <span class="text-destructive">*</span></Label>
                            <Input id="title" v-model="form.title" placeholder="Give your post a clear title" required />
                            <p v-if="form.errors.title" class="text-sm text-destructive">{{ form.errors.title }}</p>
                        </div>

                        <div class="space-y-2">
                            <Label for="description">Description <span class="text-destructive">*</span></Label>
                            <textarea 
                                id="description" 
                                v-model="form.description" 
                                rows="5" 
                                class="flex w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                                placeholder="Describe what you're offering or looking for..."
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

                        <!-- Images Upload -->
                        <div class="space-y-2">
                            <Label for="images">Images (Max 3)</Label>
                            <Input 
                                id="images" 
                                type="file" 
                                multiple 
                                accept="image/*" 
                                @change="e => { form.images = Array.from((e.target as HTMLInputElement).files || []).slice(0, 3) }" 
                            />
                            <p class="text-xs text-muted-foreground">You can upload up to 3 images to showcase your post.</p>
                            <p v-if="form.errors.images" class="text-sm text-destructive">{{ form.errors.images }}</p>
                        </div>
                    </div>

                    <!-- Step 3: Location -->
                    <div v-if="step === 3" class="space-y-6">
                        <div class="space-y-2">
                            <Label for="city">City <span class="text-destructive">*</span></Label>
                            <Input id="city" v-model="form.city" placeholder="e.g. Seattle" required />
                            <p class="text-xs text-muted-foreground">This dictates where your post will appear on the local board.</p>
                            <p v-if="form.errors.city" class="text-sm text-destructive">{{ form.errors.city }}</p>
                        </div>
                        <div class="space-y-2">
                            <Label for="zip_code">Zip Code (Optional)</Label>
                            <Input id="zip_code" v-model="form.zip_code" placeholder="e.g. 98101" />
                        </div>
                    </div>
                </CardContent>

                <CardFooter class="flex justify-between">
                    <Button variant="outline" @click="prevStep" :disabled="step === 1">Back</Button>
                    <Button v-if="step < 3" @click="nextStep">Next</Button>
                    <Button v-if="step === 3" @click="submit" :disabled="form.processing">Post to Board</Button>
                </CardFooter>
            </Card>
        </div>
    </div>
</template>
