<script setup lang="ts">
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { Activity, Users, CheckCircle, Clock, LayoutDashboard, Tags, Menu, X as XIcon, Plus, Pencil, Trash2 } from 'lucide-vue-next';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { ref } from 'vue';

defineProps<{
    metrics: {
        total_users: number;
        verified_users: number;
        active_posts: number;
        pending_suggestions: number;
    };
    suggestions: any[];
    users: any;
    categories: any[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Super Admin Dashboard', href: '/admin/dashboard' }
        ]
    }
});

const activeSection = ref('overview');
const mobileMenuOpen = ref(false);

const menuItems = [
    { key: 'overview', label: 'Overview', icon: LayoutDashboard },
    { key: 'users', label: 'Users', icon: Users },
    { key: 'categories', label: 'Categories', icon: Tags },
];

const setSection = (key: string) => {
    activeSection.value = key;
    mobileMenuOpen.value = false;
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
                                            <Badge v-if="user.is_verified" variant="default" class="bg-green-600 hover:bg-green-700">Verified</Badge>
                                            <Badge v-else variant="outline">Unverified</Badge>
                                        </TableCell>
                                        <TableCell class="hidden md:table-cell">{{ user.posts_count }}</TableCell>
                                        <TableCell class="hidden lg:table-cell">{{ new Date(user.created_at).toLocaleDateString() }}</TableCell>
                                        <TableCell class="text-right">
                                            <div class="flex items-center justify-end gap-2">
                                                <Button v-if="!user.is_verified" size="sm" variant="outline" @click="verifyUser(user.id)">
                                                    Verify
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
        </main>
    </div>
</template>
