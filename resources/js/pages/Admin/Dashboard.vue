<script setup lang="ts">
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { Activity, Users, CheckCircle, Clock, LayoutDashboard, Tags, Menu, X as XIcon, Plus, Pencil, Trash2, MessageSquare, Eye, EyeOff } from 'lucide-vue-next';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { ref, computed } from 'vue';

const props = defineProps<{
    metrics: {
        total_users: number;
        verified_users: number;
        active_posts: number;
        pending_suggestions: number;
    };
    suggestions: any[];
    users: any;
    posts: any;
    categories: any[];
    messages: any[];
}>();

const unreadCount = computed(() => props.messages.filter(m => !m.is_read).length);

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Super Admin Dashboard', href: '/admin/dashboard' }
        ]
    }
});

// Initialize active section from URL query param if present
const urlParams = typeof window !== 'undefined' ? new URLSearchParams(window.location.search) : null;
const activeSection = ref(urlParams ? (urlParams.get('tab') || 'overview') : 'overview');
const mobileMenuOpen = ref(false);

const menuItems = [
    { key: 'overview', label: 'Overview', icon: LayoutDashboard },
    { key: 'users', label: 'Users', icon: Users },
    { key: 'posts', label: 'Posts', icon: Activity },
    { key: 'categories', label: 'Categories', icon: Tags },
    { key: 'messages', label: 'Messages', icon: MessageSquare },
];

const setSection = (key: string) => {
    activeSection.value = key;
    mobileMenuOpen.value = false;
    
    if (typeof window !== 'undefined') {
        const url = new URL(window.location.href);
        url.searchParams.set('tab', key);
        url.searchParams.delete('users_page');
        url.searchParams.delete('posts_page');
        window.history.pushState({}, '', url);
    }
};

const approve = (id: number) => {
    if (confirm('Are you sure you want to approve this category and reassign the post?')) {
        router.post(`/admin/suggestions/${id}/approve`, {}, { preserveScroll: true });
    }
};

const reject = (id: number) => {
    if (confirm('Are you sure you want to reject this suggestion?')) {
        router.post(`/admin/suggestions/${id}/reject`, {}, { preserveScroll: true });
    }
};

const verifyUser = (id: number) => {
    if (confirm('Are you sure you want to manually verify this user?')) {
        router.post(`/admin/users/${id}/verify`, {}, { preserveScroll: true });
    }
};

const deleteUser = (id: number) => {
    if (confirm('Are you sure you want to delete this user? This cannot be undone.')) {
        router.delete(`/admin/users/${id}`, { preserveScroll: true });
    }
};

const blockUser = (id: number) => {
    if (confirm('Are you sure you want to permanently block this user? They will not be able to register again.')) {
        router.post(`/admin/users/${id}/block`, {}, { preserveScroll: true });
    }
};

const unblockUser = (id: number) => {
    if (confirm('Are you sure you want to unblock this user?')) {
        router.post(`/admin/users/${id}/unblock`, {}, { preserveScroll: true });
    }
};

const deletePost = (id: number) => {
    if (confirm('Are you sure you want to delete this post? This cannot be undone.')) {
        router.delete(`/admin/posts/${id}`, { preserveScroll: true });
    }
};

// Category CRUD
const categoryForm = useForm({ name: '' });
const editingCategoryId = ref<number | null>(null);
const editForm = useForm({ name: '' });
const showAddForm = ref(false);

const createCategory = () => {
    categoryForm.post('/admin/categories', {
        preserveScroll: true,
        onSuccess: () => {
            categoryForm.reset();
            showAddForm.value = false;
        },
    });
};

const startEdit = (category: any) => {
    editingCategoryId.value = category.id;
    editForm.name = category.name;
};

const cancelEdit = () => {
    editingCategoryId.value = null;
    editForm.reset();
};

const updateCategory = (id: number) => {
    editForm.put(`/admin/categories/${id}`, {
        preserveScroll: true,
        onSuccess: () => {
            editingCategoryId.value = null;
            editForm.reset();
        },
    });
};

const deleteCategory = (id: number) => {
    if (confirm('Are you sure you want to delete this category?')) {
        router.delete(`/admin/categories/${id}`, { preserveScroll: true });
    }
};

// Messages
const toggleRead = (id: number) => {
    router.post(`/admin/messages/${id}/toggle-read`, {}, { preserveScroll: true });
};

const deleteMessage = (id: number) => {
    if (confirm('Are you sure you want to delete this message?')) {
        router.delete(`/admin/messages/${id}`, { preserveScroll: true });
    }
};
</script>

<template>
    <Head title="Admin Dashboard" />

    <div class="mx-auto flex w-full max-w-7xl flex-col gap-6 px-4 py-6 md:flex-row md:px-6">
        <!-- Mobile menu toggle -->
        <div class="flex items-center justify-between md:hidden">
            <h1 class="text-xl font-bold tracking-tight">Dashboard</h1>
            <Button variant="ghost" size="icon" @click="mobileMenuOpen = !mobileMenuOpen">
                <XIcon v-if="mobileMenuOpen" class="h-5 w-5" />
                <Menu v-else class="h-5 w-5" />
            </Button>
        </div>

        <!-- Sidebar Navigation -->
        <aside :class="[
            'shrink-0 md:block md:w-56',
            mobileMenuOpen ? 'block' : 'hidden'
        ]">
            <nav class="sticky top-20 space-y-1">
                <h2 class="mb-4 hidden text-xs font-semibold uppercase tracking-wider text-muted-foreground md:block">Administration</h2>
                <button
                    v-for="item in menuItems"
                    :key="item.key"
                    @click="setSection(item.key)"
                    :class="[
                        'flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors',
                        activeSection === item.key
                            ? 'bg-primary text-primary-foreground shadow-sm'
                            : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground'
                    ]"
                >
                    <component :is="item.icon" class="h-4 w-4" />
                    {{ item.label }}
                    <Badge v-if="item.key === 'messages' && unreadCount > 0" variant="destructive" class="ml-auto h-5 min-w-[20px] px-1.5 text-[10px]">{{ unreadCount }}</Badge>
                </button>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="min-w-0 flex-1 space-y-6">
            <!-- Overview Section -->
            <template v-if="activeSection === 'overview'">
                <div>
                    <h1 class="hidden text-2xl font-bold tracking-tight md:block">Overview</h1>
                    <p class="text-sm text-muted-foreground">Platform metrics and moderation queue.</p>
                </div>

                <!-- Metrics Grid -->
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <Card>
                        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle class="text-sm font-medium">Total Users</CardTitle>
                            <Users class="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div class="text-2xl font-bold">{{ metrics.total_users }}</div>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle class="text-sm font-medium">Verified Users</CardTitle>
                            <CheckCircle class="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div class="text-2xl font-bold">{{ metrics.verified_users }}</div>
                            <p class="text-xs text-muted-foreground">${{ metrics.verified_users }} revenue</p>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle class="text-sm font-medium">Active Posts</CardTitle>
                            <Activity class="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div class="text-2xl font-bold">{{ metrics.active_posts }}</div>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle class="text-sm font-medium">Pending Suggestions</CardTitle>
                            <Clock class="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div class="text-2xl font-bold">{{ metrics.pending_suggestions }}</div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Moderation Queue -->
                <Card>
                    <CardHeader>
                        <CardTitle>Category Suggestions Review</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div v-if="suggestions.length === 0" class="rounded-lg border bg-muted/20 py-8 text-center text-muted-foreground">
                            All caught up! No pending suggestions.
                        </div>
                        <div v-else class="overflow-x-auto">
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Suggested Name</TableHead>
                                        <TableHead>User</TableHead>
                                        <TableHead class="hidden sm:table-cell">Original Post</TableHead>
                                        <TableHead class="hidden md:table-cell">Date</TableHead>
                                        <TableHead class="text-right">Actions</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    <TableRow v-for="suggestion in suggestions" :key="suggestion.id">
                                        <TableCell class="font-medium">
                                            <Badge variant="secondary">{{ suggestion.suggested_name }}</Badge>
                                        </TableCell>
                                        <TableCell>{{ suggestion.user_name }}</TableCell>
                                        <TableCell class="hidden sm:table-cell">
                                            <Link :href="`/posts/${suggestion.post_id}`" class="text-primary hover:underline">
                                                {{ suggestion.post_title }}
                                            </Link>
                                        </TableCell>
                                        <TableCell class="hidden md:table-cell">{{ new Date(suggestion.created_at).toLocaleDateString() }}</TableCell>
                                        <TableCell class="text-right">
                                            <div class="flex items-center justify-end gap-2">
                                                <Button size="sm" variant="outline" class="border-destructive text-destructive hover:bg-destructive/10" @click="reject(suggestion.id)">
                                                    Reject
                                                </Button>
                                                <Button size="sm" @click="approve(suggestion.id)">
                                                    Approve
                                                </Button>
                                            </div>
                                        </TableCell>
                                    </TableRow>
                                </TableBody>
                            </Table>
                        </div>
                    </CardContent>
                </Card>
            </template>

            <!-- Users Section -->
            <template v-if="activeSection === 'users'">
                <div>
                    <h1 class="hidden text-2xl font-bold tracking-tight md:block">User Management</h1>
                    <p class="text-sm text-muted-foreground">Manage user accounts and verification status.</p>
                </div>

                <Card>
                    <CardContent class="p-0">
                        <div class="overflow-x-auto">
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Name</TableHead>
                                        <TableHead class="hidden sm:table-cell">Email</TableHead>
                                        <TableHead>Status</TableHead>
                                        <TableHead class="hidden md:table-cell">Posts</TableHead>
                                        <TableHead class="hidden lg:table-cell">Joined</TableHead>
                                        <TableHead class="text-right">Actions</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    <TableRow v-for="user in users.data" :key="user.id">
                                        <TableCell class="font-medium">{{ user.name }}</TableCell>
                                        <TableCell class="hidden sm:table-cell">{{ user.email }}</TableCell>
                                        <TableCell>
                                            <div class="flex flex-col gap-1">
                                                <Badge v-if="user.banned_at" variant="destructive" class="w-fit">Blocked</Badge>
                                                <Badge v-else-if="user.is_verified" variant="default" class="w-fit bg-green-600 hover:bg-green-700">Verified</Badge>
                                                <Badge v-else variant="outline" class="w-fit">Unverified</Badge>
                                            </div>
                                        </TableCell>
                                        <TableCell class="hidden md:table-cell">{{ user.posts_count }}</TableCell>
                                        <TableCell class="hidden lg:table-cell">{{ new Date(user.created_at).toLocaleDateString() }}</TableCell>
                                        <TableCell class="text-right">
                                            <div class="flex items-center justify-end gap-2">
                                                <Button v-if="!user.is_verified && !user.banned_at" size="sm" variant="outline" @click="verifyUser(user.id)">
                                                    Verify
                                                </Button>
                                                <Button v-if="user.banned_at" size="sm" variant="outline" @click="unblockUser(user.id)">
                                                    Unblock
                                                </Button>
                                                <Button v-else size="sm" variant="outline" class="border-destructive text-destructive hover:bg-destructive/10" @click="blockUser(user.id)">
                                                    Block
                                                </Button>
                                                <Button size="sm" variant="destructive" @click="deleteUser(user.id)">
                                                    Delete
                                                </Button>
                                            </div>
                                        </TableCell>
                                    </TableRow>
                                </TableBody>
                            </Table>
                        </div>
                    </CardContent>
                    
                    <!-- Pagination for Users -->
                    <div v-if="users.last_page > 1" class="border-t border-stone-100 p-4">
                        <div class="flex items-center justify-between">
                            <div class="flex flex-1 justify-between sm:hidden">
                                <Link 
                                    v-if="users.prev_page_url" 
                                    :href="users.prev_page_url" 
                                    class="relative inline-flex items-center rounded-md border border-stone-300 bg-white px-4 py-2 text-sm font-medium text-stone-700 hover:bg-stone-50"
                                    preserve-scroll
                                >
                                    Previous
                                </Link>
                                <span v-else class="relative inline-flex items-center rounded-md border border-stone-200 bg-stone-50 px-4 py-2 text-sm font-medium text-stone-400 cursor-not-allowed">
                                    Previous
                                </span>
                                <Link 
                                    v-if="users.next_page_url" 
                                    :href="users.next_page_url" 
                                    class="relative ml-3 inline-flex items-center rounded-md border border-stone-300 bg-white px-4 py-2 text-sm font-medium text-stone-700 hover:bg-stone-50"
                                    preserve-scroll
                                >
                                    Next
                                </Link>
                                <span v-else class="relative ml-3 inline-flex items-center rounded-md border border-stone-200 bg-stone-50 px-4 py-2 text-sm font-medium text-stone-400 cursor-not-allowed">
                                    Next
                                </span>
                            </div>
                            <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm text-stone-500">
                                        Showing <span class="font-medium">{{ users.from }}</span> to <span class="font-medium">{{ users.to }}</span> of <span class="font-medium">{{ users.total }}</span> users
                                    </p>
                                </div>
                                <div>
                                    <nav class="isolate inline-flex -space-x-px rounded-md shadow-xs" aria-label="Pagination">
                                        <template v-for="(link, i) in users.links" :key="i">
                                            <Link
                                                v-if="link.url"
                                                :href="link.url"
                                                :class="[
                                                    link.active 
                                                        ? 'relative z-10 inline-flex items-center bg-primary px-3.5 py-2 text-sm font-semibold text-primary-foreground focus:z-20 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary' 
                                                        : 'relative inline-flex items-center px-3.5 py-2 text-sm font-semibold text-stone-900 ring-1 ring-inset ring-stone-300 hover:bg-stone-50 focus:z-20 focus:outline-offset-0',
                                                    i === 0 ? 'rounded-l-md' : '',
                                                    i === users.links.length - 1 ? 'rounded-r-md' : ''
                                                ]"
                                                v-html="link.label"
                                                preserve-scroll
                                            />
                                            <span
                                                v-else
                                                :class="[
                                                    'relative inline-flex items-center px-3.5 py-2 text-sm font-semibold text-stone-400 ring-1 ring-inset ring-stone-200 cursor-not-allowed',
                                                    i === 0 ? 'rounded-l-md' : '',
                                                    i === users.links.length - 1 ? 'rounded-r-md' : ''
                                                ]"
                                                v-html="link.label"
                                            />
                                        </template>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </Card>
            </template>

            <!-- Posts Section -->
            <template v-if="activeSection === 'posts'">
                <div>
                    <h1 class="hidden text-2xl font-bold tracking-tight md:block">Post Management</h1>
                    <p class="text-sm text-muted-foreground">Monitor and manage all user posts and view author information.</p>
                </div>

                <Card>
                    <CardContent class="p-0">
                        <div class="overflow-x-auto">
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Post Title</TableHead>
                                        <TableHead>Category</TableHead>
                                        <TableHead>Author Details</TableHead>
                                        <TableHead>Status</TableHead>
                                        <TableHead class="hidden md:table-cell">Created At</TableHead>
                                        <TableHead class="text-right">Actions</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    <TableRow v-for="post in posts.data" :key="post.id">
                                        <TableCell class="font-medium">
                                            <div class="flex flex-col">
                                                <Link :href="`/posts/${post.id}`" class="text-primary hover:underline font-bold">
                                                    {{ post.title }}
                                                </Link>
                                            </div>
                                        </TableCell>
                                        <TableCell>
                                            <Badge variant="secondary">{{ post.category_name || 'N/A' }}</Badge>
                                        </TableCell>
                                        <TableCell>
                                            <div v-if="post.user" class="flex flex-col gap-0.5">
                                                <div class="flex items-center gap-1.5">
                                                    <span class="font-semibold text-stone-900">{{ post.user.name }}</span>
                                                    <Badge v-if="post.user.is_verified" variant="default" class="h-4 px-1.5 text-[9px] bg-green-600 hover:bg-green-700">Verified</Badge>
                                                </div>
                                                <span class="text-xs text-muted-foreground">{{ post.user.email }}</span>
                                            </div>
                                            <span v-else class="text-xs text-muted-foreground">Unknown User</span>
                                        </TableCell>
                                        <TableCell>
                                            <Badge v-if="post.is_expired" variant="outline" class="text-stone-500 border-stone-300">Expired</Badge>
                                            <Badge v-else variant="default" class="bg-emerald-600 hover:bg-emerald-700">Active</Badge>
                                        </TableCell>
                                        <TableCell class="hidden md:table-cell text-sm text-stone-500">
                                            {{ new Date(post.created_at).toLocaleDateString() }}
                                        </TableCell>
                                        <TableCell class="text-right">
                                            <div class="flex items-center justify-end gap-2">
                                                <Link :href="`/posts/${post.id}/edit`">
                                                    <Button size="sm" variant="outline" class="gap-1">
                                                        <Pencil class="h-3.5 w-3.5" />
                                                        Edit
                                                    </Button>
                                                </Link>
                                                <Button size="sm" variant="destructive" class="gap-1" @click="deletePost(post.id)">
                                                    <Trash2 class="h-3.5 w-3.5" />
                                                    Delete
                                                </Button>
                                            </div>
                                        </TableCell>
                                    </TableRow>
                                    <TableRow v-if="posts.data.length === 0">
                                        <TableCell colspan="6" class="py-8 text-center text-muted-foreground">
                                            No posts found.
                                        </TableCell>
                                    </TableRow>
                                </TableBody>
                            </Table>
                        </div>
                    </CardContent>
                    
                    <!-- Pagination for Posts -->
                    <div v-if="posts.last_page > 1" class="border-t border-stone-100 p-4">
                        <div class="flex items-center justify-between">
                            <div class="flex flex-1 justify-between sm:hidden">
                                <Link 
                                    v-if="posts.prev_page_url" 
                                    :href="posts.prev_page_url" 
                                    class="relative inline-flex items-center rounded-md border border-stone-300 bg-white px-4 py-2 text-sm font-medium text-stone-700 hover:bg-gray-50"
                                    preserve-scroll
                                >
                                    Previous
                                </Link>
                                <span v-else class="relative inline-flex items-center rounded-md border border-stone-200 bg-stone-50 px-4 py-2 text-sm font-medium text-stone-400 cursor-not-allowed">
                                    Previous
                                </span>
                                <Link 
                                    v-if="posts.next_page_url" 
                                    :href="posts.next_page_url" 
                                    class="relative ml-3 inline-flex items-center rounded-md border border-stone-300 bg-white px-4 py-2 text-sm font-medium text-stone-700 hover:bg-gray-50"
                                    preserve-scroll
                                >
                                    Next
                                </Link>
                                <span v-else class="relative ml-3 inline-flex items-center rounded-md border border-stone-200 bg-stone-50 px-4 py-2 text-sm font-medium text-stone-400 cursor-not-allowed">
                                    Next
                                </span>
                            </div>
                            <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm text-stone-500">
                                        Showing <span class="font-medium">{{ posts.from }}</span> to <span class="font-medium">{{ posts.to }}</span> of <span class="font-medium">{{ posts.total }}</span> posts
                                    </p>
                                </div>
                                <div>
                                    <nav class="isolate inline-flex -space-x-px rounded-md shadow-xs" aria-label="Pagination">
                                        <template v-for="(link, i) in posts.links" :key="i">
                                            <Link
                                                v-if="link.url"
                                                :href="link.url"
                                                :class="[
                                                    link.active 
                                                        ? 'relative z-10 inline-flex items-center bg-primary px-3.5 py-2 text-sm font-semibold text-primary-foreground focus:z-20 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary' 
                                                        : 'relative inline-flex items-center px-3.5 py-2 text-sm font-semibold text-stone-900 ring-1 ring-inset ring-stone-300 hover:bg-stone-50 focus:z-20 focus:outline-offset-0',
                                                    i === 0 ? 'rounded-l-md' : '',
                                                    i === posts.links.length - 1 ? 'rounded-r-md' : ''
                                                ]"
                                                v-html="link.label"
                                                preserve-scroll
                                            />
                                            <span
                                                v-else
                                                :class="[
                                                    'relative inline-flex items-center px-3.5 py-2 text-sm font-semibold text-stone-400 ring-1 ring-inset ring-stone-200 cursor-not-allowed',
                                                    i === 0 ? 'rounded-l-md' : '',
                                                    i === posts.links.length - 1 ? 'rounded-r-md' : ''
                                                ]"
                                                v-html="link.label"
                                            />
                                        </template>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </Card>
            </template>

            <!-- Categories Section -->
            <template v-if="activeSection === 'categories'">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="hidden text-2xl font-bold tracking-tight md:block">Categories</h1>
                        <p class="text-sm text-muted-foreground">Manage categories and view post distribution.</p>
                    </div>
                    <Button v-if="!showAddForm" size="sm" @click="showAddForm = true" class="gap-1.5">
                        <Plus class="h-4 w-4" />
                        Add Category
                    </Button>
                </div>

                <!-- Add Category Form -->
                <Card v-if="showAddForm">
                    <CardContent class="pt-6">
                        <form @submit.prevent="createCategory" class="flex flex-col gap-3 sm:flex-row sm:items-end">
                            <div class="flex-1 space-y-1.5">
                                <label class="text-sm font-medium">Category Name</label>
                                <Input v-model="categoryForm.name" placeholder="e.g. Electronics" :class="{ 'border-destructive': categoryForm.errors.name }" />
                                <p v-if="categoryForm.errors.name" class="text-xs text-destructive">{{ categoryForm.errors.name }}</p>
                            </div>
                            <div class="flex gap-2">
                                <Button type="submit" :disabled="categoryForm.processing" size="sm">
                                    {{ categoryForm.processing ? 'Creating...' : 'Create' }}
                                </Button>
                                <Button type="button" variant="outline" size="sm" @click="showAddForm = false; categoryForm.reset()">
                                    Cancel
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>

                <Card>
                    <CardContent class="p-0">
                        <div class="overflow-x-auto">
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Name</TableHead>
                                        <TableHead class="hidden sm:table-cell">Slug</TableHead>
                                        <TableHead>Posts</TableHead>
                                        <TableHead class="text-right">Actions</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    <TableRow v-for="category in categories" :key="category.id">
                                        <TableCell>
                                            <!-- Edit mode -->
                                            <form v-if="editingCategoryId === category.id" @submit.prevent="updateCategory(category.id)" class="flex items-center gap-2">
                                                <Input v-model="editForm.name" class="h-8 w-40" :class="{ 'border-destructive': editForm.errors.name }" />
                                                <Button type="submit" size="sm" variant="default" class="h-7 px-2 text-xs" :disabled="editForm.processing">
                                                    Save
                                                </Button>
                                                <Button type="button" size="sm" variant="ghost" class="h-7 px-2 text-xs" @click="cancelEdit">
                                                    Cancel
                                                </Button>
                                            </form>
                                            <span v-else class="font-medium">{{ category.name }}</span>
                                            <p v-if="editingCategoryId === category.id && editForm.errors.name" class="mt-1 text-xs text-destructive">{{ editForm.errors.name }}</p>
                                        </TableCell>
                                        <TableCell class="hidden text-muted-foreground sm:table-cell">{{ category.slug }}</TableCell>
                                        <TableCell>
                                            <Badge variant="secondary">{{ category.posts_count }}</Badge>
                                        </TableCell>
                                        <TableCell class="text-right">
                                            <div v-if="editingCategoryId !== category.id" class="flex items-center justify-end gap-1">
                                                <Button size="sm" variant="ghost" class="h-8 w-8 p-0" @click="startEdit(category)" title="Edit">
                                                    <Pencil class="h-3.5 w-3.5" />
                                                </Button>
                                                <Button
                                                    size="sm"
                                                    variant="ghost"
                                                    class="h-8 w-8 p-0 text-destructive hover:text-destructive"
                                                    :disabled="category.posts_count > 0"
                                                    :title="category.posts_count > 0 ? 'Cannot delete: has posts' : 'Delete'"
                                                    @click="deleteCategory(category.id)"
                                                >
                                                    <Trash2 class="h-3.5 w-3.5" />
                                                </Button>
                                            </div>
                                        </TableCell>
                                    </TableRow>
                                </TableBody>
                            </Table>
                        </div>
                    </CardContent>
                </Card>
            </template>

            <!-- Messages Section -->
            <template v-if="activeSection === 'messages'">
                <div>
                    <h1 class="hidden text-2xl font-bold tracking-tight md:block">Contact Messages</h1>
                    <p class="text-sm text-muted-foreground">Messages submitted through the contact form.</p>
                </div>

                <Card v-if="messages.length === 0">
                    <CardContent class="py-12 text-center">
                        <MessageSquare class="mx-auto h-10 w-10 text-stone-300" />
                        <p class="mt-3 text-sm text-muted-foreground">No messages yet.</p>
                    </CardContent>
                </Card>

                <div v-else class="space-y-3">
                    <Card 
                        v-for="msg in messages" 
                        :key="msg.id" 
                        :class="[
                            'transition-all',
                            msg.is_read ? 'opacity-70' : 'border-emerald-500/30 shadow-sm'
                        ]"
                    >
                        <CardContent class="p-5">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                <div class="min-w-0 flex-1">
                                    <div class="mb-1 flex flex-wrap items-center gap-2">
                                        <h3 class="text-sm font-bold text-foreground">{{ msg.subject }}</h3>
                                        <Badge v-if="!msg.is_read" variant="default" class="text-[10px]">New</Badge>
                                    </div>
                                    <p class="mb-3 text-xs text-muted-foreground">
                                        From <span class="font-medium text-foreground">{{ msg.name }}</span>
                                        &lt;{{ msg.email }}&gt;
                                        · {{ new Date(msg.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit' }) }}
                                    </p>
                                    <p class="whitespace-pre-wrap text-sm text-muted-foreground">{{ msg.message }}</p>
                                </div>
                                <div class="flex shrink-0 items-center gap-1">
                                    <Button size="sm" variant="ghost" class="h-8 w-8 p-0" @click="toggleRead(msg.id)" :title="msg.is_read ? 'Mark as unread' : 'Mark as read'">
                                        <EyeOff v-if="msg.is_read" class="h-3.5 w-3.5" />
                                        <Eye v-else class="h-3.5 w-3.5" />
                                    </Button>
                                    <Button size="sm" variant="ghost" class="h-8 w-8 p-0 text-destructive hover:text-destructive" @click="deleteMessage(msg.id)" title="Delete">
                                        <Trash2 class="h-3.5 w-3.5" />
                                    </Button>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </template>
        </main>
    </div>
</template>
