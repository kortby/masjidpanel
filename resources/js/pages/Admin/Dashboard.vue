<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Activity, Users, CheckCircle, Clock } from 'lucide-vue-next';

defineProps<{
    metrics: {
        total_users: number;
        verified_users: number;
        active_posts: number;
        pending_suggestions: number;
    };
    suggestions: any[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Super Admin Dashboard', href: '/admin/dashboard' }
        ]
    }
});

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
</script>

<template>
    <Head title="Admin Dashboard" />

    <div class="p-6 max-w-7xl mx-auto space-y-6">
        <div>
            <h1 class="text-3xl font-bold tracking-tight">Dashboard</h1>
            <p class="text-muted-foreground">Platform metrics and moderation queue.</p>
        </div>

        <!-- Metrics -->
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
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
                <div v-if="suggestions.length === 0" class="text-center py-8 text-muted-foreground border rounded-lg bg-muted/20">
                    All caught up! No pending suggestions.
                </div>
                <Table v-else>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Suggested Name</TableHead>
                            <TableHead>User</TableHead>
                            <TableHead>Original Post</TableHead>
                            <TableHead>Date</TableHead>
                            <TableHead class="text-right">Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="suggestion in suggestions" :key="suggestion.id">
                            <TableCell class="font-medium">
                                <Badge variant="secondary">{{ suggestion.suggested_name }}</Badge>
                            </TableCell>
                            <TableCell>{{ suggestion.user_name }}</TableCell>
                            <TableCell>
                                <Link :href="`/posts/${suggestion.post_id}`" class="text-primary hover:underline">
                                    {{ suggestion.post_title }}
                                </Link>
                            </TableCell>
                            <TableCell>{{ new Date(suggestion.created_at).toLocaleDateString() }}</TableCell>
                            <TableCell class="text-right space-x-2 flex justify-end">
                                <Button size="sm" variant="outline" class="border-destructive text-destructive hover:bg-destructive/10" @click="reject(suggestion.id)">
                                    Reject
                                </Button>
                                <Button size="sm" @click="approve(suggestion.id)">
                                    Approve
                                </Button>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </CardContent>
        </Card>
    </div>
</template>
