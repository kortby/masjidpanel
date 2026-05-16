<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import TagsInput from '@/components/TagsInput.vue';

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
    tags: [] as string[],
    images: [] as File[],
});

const selectedCategory = computed(() => {
    return props.categories.find(c => c.id === form.category_id);
});

const isOtherCategory = computed(() => {
    return selectedCategory.value?.name === 'Other (Suggest a Category)';
});

const nextStep = () => {
    if (step.value === 1 && !form.category_id) {
return;
}

    if (step.value === 2 && (!form.title || !form.description)) {
return;
}

    step.value++;
};

const prevStep = () => {
    step.value--;
};

const zipError = ref('');

const validateZip = () => {
    if (!form.zip_code) {
        zipError.value = '';
        return true;
    }
    const isValid = /^\d{5}(-\d{4})?$/.test(form.zip_code);
    zipError.value = isValid ? '' : 'Please enter a valid 5-digit US zip code (e.g. 92123).';
    return isValid;
};

const clearZipError = () => {
    if (zipError.value || form.errors.zip_code) {
        if (/^\d{5}(-\d{4})?$/.test(form.zip_code)) {
            zipError.value = '';
            form.clearErrors('zip_code');
        }
    }
};

const submit = () => {
    if (!validateZip()) return;
    
    form.post('/posts', {
        onSuccess: () => {
            // Handled by redirect
        }
    });
};
</script>

<template>
    <Head title="Create Post" />

    <div class="mx-auto flex min-h-[calc(100vh-4rem)] w-full items-center justify-center p-4 sm:p-6 lg:p-8">
        <div class="w-full max-w-2xl">
            <form @submit.prevent="submit" class="overflow-hidden rounded-3xl border border-emerald-900/10 bg-white shadow-sm">
                <div class="border-b border-emerald-900/10 px-6 py-8 sm:px-8">
                    <h2 class="text-2xl font-bold tracking-tight text-emerald-950">Create a New Post</h2>
                    <p class="mt-2 text-sm text-stone-500">Step {{ step }} of 3: {{ step === 1 ? 'Select Category' : (step === 2 ? 'Post Details' : 'Location & Review') }}</p>
                </div>
                
                <div class="px-6 py-8 sm:px-8">
                    <!-- Step 1: Category -->
                    <div v-if="step === 1" class="space-y-4">
                        <label class="block text-sm font-bold text-emerald-950">Select a Category</label>
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

                    <!-- Step 2: Details -->
                    <div v-if="step === 2" class="space-y-6">
                        <div v-if="isOtherCategory" class="space-y-2">
                            <label for="suggested_name" class="block text-sm font-bold text-emerald-950">Suggest Category Name <span class="text-red-500">*</span></label>
                            <input id="suggested_name" v-model="form.suggested_category_name" placeholder="e.g. Lost & Found" required class="h-10 w-full rounded-xl border border-stone-200 bg-transparent px-3 py-2 text-sm text-stone-900 placeholder:text-stone-400 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500" />
                            <p class="text-xs text-stone-500">This suggestion will be reviewed by admins.</p>
                        </div>

                        <div class="space-y-2">
                            <label for="title" class="block text-sm font-bold text-emerald-950">Title <span class="text-red-500">*</span></label>
                            <input id="title" v-model="form.title" placeholder="Give your post a clear title" required class="h-10 w-full rounded-xl border border-stone-200 bg-transparent px-3 py-2 text-sm text-stone-900 placeholder:text-stone-400 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500" />
                            <p v-if="form.errors.title" class="text-sm font-medium text-red-500">{{ form.errors.title }}</p>
                        </div>

                        <div class="space-y-2">
                            <label for="description" class="block text-sm font-bold text-emerald-950">Description <span class="text-red-500">*</span></label>
                            <textarea 
                                id="description" 
                                v-model="form.description" 
                                rows="5" 
                                class="flex w-full rounded-xl border border-stone-200 bg-transparent px-3 py-2 text-sm text-stone-900 placeholder:text-stone-400 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500 disabled:cursor-not-allowed disabled:opacity-50"
                                placeholder="Describe what you're offering or looking for..."
                                required
                            ></textarea>
                            <p v-if="form.errors.description" class="text-sm font-medium text-red-500">{{ form.errors.description }}</p>
                        </div>

                        <!-- Tags -->
                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-emerald-950">Tags</label>
                            <TagsInput v-model="form.tags" placeholder="e.g. urgent, discount" />
                            <p v-if="form.errors.tags" class="text-sm font-medium text-red-500">{{ form.errors.tags }}</p>
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

                        <!-- Images Upload -->
                        <div class="space-y-2">
                            <label for="images" class="block text-sm font-bold text-emerald-950">Images (Max 3)</label>
                            <input 
                                id="images" 
                                type="file" 
                                multiple 
                                accept="image/*" 
                                @change="e => { form.images = Array.from((e.target as HTMLInputElement).files || []).slice(0, 3) }" 
                                class="flex h-10 w-full rounded-xl border border-stone-200 bg-transparent px-3 py-2 text-sm text-stone-900 file:mr-4 file:rounded-full file:border-0 file:bg-emerald-50 file:px-4 file:py-1 file:text-sm file:font-semibold file:text-emerald-700 hover:file:bg-emerald-100 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
                            />
                            <p class="text-xs text-stone-500">You can upload up to 3 images to showcase your post.</p>
                            <p v-if="form.errors.images" class="text-sm font-medium text-red-500">{{ form.errors.images }}</p>
                        </div>
                    </div>

                    <!-- Step 3: Location -->
                    <div v-if="step === 3" class="space-y-6">
                        <div class="space-y-2">
                            <label for="city" class="block text-sm font-bold text-emerald-950">City <span class="text-red-500">*</span></label>
                            <input id="city" v-model="form.city" placeholder="e.g. Seattle" required class="h-10 w-full rounded-xl border border-stone-200 bg-transparent px-3 py-2 text-sm text-stone-900 placeholder:text-stone-400 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500" />
                            <p class="text-xs text-stone-500">This dictates where your post will appear on the local board.</p>
                            <p v-if="form.errors.city" class="text-sm font-medium text-red-500">{{ form.errors.city }}</p>
                        </div>
                        <div class="space-y-2">
                            <label for="zip_code" class="block text-sm font-bold text-emerald-950">Zip Code (Optional)</label>
                            <input 
                                id="zip_code" 
                                v-model="form.zip_code" 
                                @blur="validateZip"
                                @input="clearZipError"
                                placeholder="e.g. 98101" 
                                :class="[
                                    'h-10 w-full rounded-xl border bg-transparent px-3 py-2 text-sm text-stone-900 placeholder:text-stone-400 focus:outline-none focus:ring-1 transition-colors',
                                    (zipError || form.errors.zip_code)
                                        ? 'border-red-500 focus:border-red-500 focus:ring-red-500 bg-red-50/50'
                                        : 'border-stone-200 focus:border-emerald-500 focus:ring-emerald-500'
                                ]"
                            />
                            <p v-if="zipError || form.errors.zip_code" class="text-sm font-medium text-red-500">
                                {{ zipError || form.errors.zip_code }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between border-t border-emerald-900/10 bg-stone-50 px-6 py-4 sm:px-8">
                    <button type="button" @click="prevStep" :disabled="step === 1" class="rounded-full px-6 py-2 text-sm font-medium text-stone-600 transition-colors hover:bg-stone-200 hover:text-stone-900 disabled:pointer-events-none disabled:opacity-50">Back</button>
                    <button type="button" v-if="step < 3" @click="nextStep" class="rounded-full bg-emerald-800 px-8 py-2 text-sm font-medium text-white transition-colors hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">Next</button>
                    <button type="submit" v-if="step === 3" :disabled="form.processing" class="rounded-full bg-amber-500 px-8 py-2 text-sm font-bold text-amber-950 transition-colors hover:bg-amber-400 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 disabled:pointer-events-none disabled:opacity-50">Post to Board</button>
                </div>
            </form>
        </div>
    </div>
</template>
