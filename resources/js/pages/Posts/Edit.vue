<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import TagsInput from '@/components/TagsInput.vue';

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
    tags: props.post.tags || [] as string[],
    images: [] as File[],
    deleted_images: [] as number[],
    _method: 'put',
});

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

const selectedCategory = computed(() => {
    return props.categories.find(c => c.id === form.category_id);
});

const toggleDeleteImage = (id: number) => {
    if (form.deleted_images.includes(id)) {
        form.deleted_images = form.deleted_images.filter(i => i !== id);
    } else {
        form.deleted_images.push(id);
    }
};

const currentImageCount = computed(() => {
    const existing = props.post.images ? props.post.images.filter((img: any) => !form.deleted_images.includes(img.id)).length : 0;
    return existing + form.images.length;
});

const submit = () => {
    if (!validateZip()) return;

    if (currentImageCount.value > 3) {
        alert('You can only have up to 3 images total.');
        return;
    }
    form.post(`/posts/${props.post.id}`);
};
</script>

<template>
    <Head title="Edit Post" />

    <div class="mx-auto flex min-h-[calc(100vh-4rem)] w-full items-center justify-center p-4 sm:p-6 lg:p-8">
        <div class="w-full max-w-2xl">
            <form @submit.prevent="submit" class="overflow-hidden rounded-3xl border border-emerald-900/10 bg-white shadow-sm">
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

                        <div class="space-y-2">
                            <label for="city" class="block text-sm font-bold text-emerald-950">City <span class="text-red-500">*</span></label>
                            <input id="city" v-model="form.city" required class="h-10 w-full rounded-xl border border-stone-200 bg-transparent px-3 py-2 text-sm text-stone-900 placeholder:text-stone-400 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500" />
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

                        <!-- Images Management -->
                        <div class="space-y-4">
                            <label class="block text-sm font-bold text-emerald-950">Images (Max 3 Total)</label>
                            
                            <!-- Existing Images -->
                            <div v-if="props.post.images && props.post.images.length > 0" class="flex flex-wrap gap-4">
                                <div v-for="image in props.post.images" :key="image.id" class="relative group">
                                    <img :src="image.url" class="h-32 w-auto rounded-xl border border-stone-200 object-cover shadow-sm transition-all" :class="{'opacity-30 grayscale': form.deleted_images.includes(image.id)}" />
                                    <button 
                                        type="button" 
                                        @click="toggleDeleteImage(image.id)" 
                                        class="absolute -top-2 -right-2 flex h-6 w-6 items-center justify-center rounded-full bg-white shadow-md transition-colors hover:bg-stone-100 focus:outline-none"
                                        :class="form.deleted_images.includes(image.id) ? 'text-emerald-600' : 'text-red-500'"
                                        :title="form.deleted_images.includes(image.id) ? 'Undo Remove' : 'Remove Image'"
                                    >
                                        <svg v-if="!form.deleted_images.includes(image.id)" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                        <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" /></svg>
                                    </button>
                                </div>
                            </div>

                            <!-- New Images -->
                            <div class="space-y-2">
                                <label for="images" class="block text-xs font-medium text-stone-500">Upload New Images</label>
                                <input 
                                    id="images" 
                                    type="file" 
                                    multiple 
                                    accept="image/*" 
                                    @change="e => { form.images = Array.from((e.target as HTMLInputElement).files || []) }" 
                                    class="flex h-10 w-full rounded-xl border border-stone-200 bg-transparent px-3 py-2 text-sm text-stone-900 file:mr-4 file:rounded-full file:border-0 file:bg-emerald-50 file:px-4 file:py-1 file:text-sm file:font-semibold file:text-emerald-700 hover:file:bg-emerald-100 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
                                />
                                <p class="text-xs font-medium" :class="currentImageCount > 3 ? 'text-red-500' : 'text-stone-500'">
                                    You have selected {{ currentImageCount }} image(s) total.
                                </p>
                                <p v-if="form.errors.images" class="text-sm font-medium text-red-500">{{ form.errors.images }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between border-t border-emerald-900/10 bg-stone-50 px-6 py-4 sm:px-8">
                    <button type="button" @click="() => router.visit(`/posts/${post.id}`)" class="rounded-full px-6 py-2 text-sm font-medium text-stone-600 transition-colors hover:bg-stone-200 hover:text-stone-900">Cancel</button>
                    <button type="submit" :disabled="form.processing" class="rounded-full bg-emerald-800 px-8 py-2 text-sm font-bold text-white transition-colors hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 disabled:pointer-events-none disabled:opacity-50">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</template>
