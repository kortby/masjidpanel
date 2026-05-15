<script setup lang="ts">
import { router } from '@inertiajs/vue3'
import { Search, Home, LayoutGrid, Info, Mail, SearchCode } from 'lucide-vue-next'
import { ref, onMounted, onUnmounted, watch } from 'vue'
import { Button } from '@/components/ui/button'
import {
  CommandDialog,
  CommandEmpty,
  CommandGroup,
  CommandInput,
  CommandItem,
  CommandList,
  CommandSeparator,
} from '@/components/ui/command'

const props = defineProps<{
    open: boolean;
}>();

const emit = defineEmits(['update:open']);

const searchQuery = ref('');
const isOpen = ref(props.open);
const searchResults = ref<any[]>([]);
const isSearching = ref(false);
let searchTimeout: ReturnType<typeof setTimeout> | null = null;

watch(() => props.open, (val) => {
    isOpen.value = val;
});

watch(isOpen, (val) => {
    emit('update:open', val);

    if (!val) {
        // Reset search query when closing
        setTimeout(() => {
 searchQuery.value = ''; 
}, 200);
    }
});

// Handle keyboard shortcut
const handleKeyDown = (e: KeyboardEvent) => {
    if (e.key === 'k' && (e.metaKey || e.ctrlKey)) {
        e.preventDefault()
        isOpen.value = !isOpen.value
    }
}

onMounted(() => {
    window.addEventListener('keydown', handleKeyDown)
})

onUnmounted(() => {
    window.removeEventListener('keydown', handleKeyDown)
})

watch(searchQuery, (newVal) => {
    if (!newVal.trim()) {
        searchResults.value = [];

        return;
    }
    
    if (searchTimeout) {
clearTimeout(searchTimeout);
}
    
    isSearching.value = true;
    searchTimeout = setTimeout(async () => {
        try {
            const res = await fetch(`/api/posts/search?q=${encodeURIComponent(newVal.trim())}`);

            if (res.ok) {
                searchResults.value = await res.json();
            }
        } catch (e) {
            console.error(e);
        } finally {
            isSearching.value = false;
        }
    }, 300);
});

const handleSelect = (route: string) => {
    isOpen.value = false;
    router.visit(route);
};

const handleSearch = () => {
    if (searchQuery.value.trim()) {
        isOpen.value = false;
        router.visit(`/feed?search=${encodeURIComponent(searchQuery.value.trim())}`);
    }
};
</script>

<template>
    <CommandDialog v-model:open="isOpen">
        <CommandInput 
            placeholder="Type a command or search..." 
            v-model="searchQuery" 
            @keydown.enter="handleSearch"
        />
        <CommandList>
            <CommandEmpty>
                <div class="flex flex-col items-center justify-center py-6 text-center">
                    <Search class="mb-4 h-10 w-10 text-muted-foreground/50" />
                    <p class="text-sm font-medium">No posts found.</p>
                    <p v-if="searchQuery.trim()" class="text-xs text-muted-foreground mt-1">Press Enter to view all results for "{{ searchQuery }}".</p>
                    <Button v-if="searchQuery.trim()" variant="outline" size="sm" class="mt-4" @click="handleSearch">View in Feed</Button>
                </div>
            </CommandEmpty>
            
            <CommandGroup heading="Posts" v-if="searchResults.length > 0">
                <CommandItem v-for="post in searchResults" :key="post.id" :value="post.title + ' ' + searchQuery" @select="handleSelect(`/posts/${post.id}`)">
                    <div class="flex flex-col">
                        <span class="font-medium">{{ post.title }}</span>
                        <span class="text-xs text-muted-foreground">{{ post.city }} &bull; {{ post.category?.name }}</span>
                    </div>
                </CommandItem>
            </CommandGroup>
            
            <CommandSeparator v-if="searchResults.length > 0" />
            
            <CommandGroup heading="Actions" v-if="searchQuery.trim()">
                <CommandItem :value="'search-posts ' + searchQuery" @select="handleSearch">
                    <SearchCode class="mr-2 h-4 w-4" />
                    <span>View all results for "{{ searchQuery }}"</span>
                </CommandItem>
            </CommandGroup>
            
            <CommandSeparator v-if="searchQuery.trim()" />
            
            <CommandGroup heading="Quick Links" v-if="!searchQuery.trim()">
                <CommandItem value="home feed board" @select="handleSelect('/')">
                    <Home class="mr-2 h-4 w-4" />
                    <span>Home Board</span>
                </CommandItem>
                <CommandItem v-if="$page.props.auth?.is_admin" value="dashboard admin settings" @select="handleSelect('/admin/dashboard')">
                    <LayoutGrid class="mr-2 h-4 w-4" />
                    <span>Admin Dashboard</span>
                </CommandItem>
                <CommandItem value="about company" @select="handleSelect('/about')">
                    <Info class="mr-2 h-4 w-4" />
                    <span>About Us</span>
                </CommandItem>
                <CommandItem value="contact support" @select="handleSelect('/contact')">
                    <Mail class="mr-2 h-4 w-4" />
                    <span>Contact</span>
                </CommandItem>
            </CommandGroup>
        </CommandList>
    </CommandDialog>
</template>
