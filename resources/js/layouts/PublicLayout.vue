<script setup lang="ts">
import PublicHeader from '@/components/PublicHeader.vue';
</script>

<template>
    <div class="min-h-screen bg-stone-50 font-sans text-stone-900 selection:bg-emerald-200 selection:text-emerald-900">
        <PublicHeader />
        
        <main>
            <slot />
        </main>
        
        <!-- Public Footer (Optional, simple version) -->
        <footer class="mt-20 border-t border-stone-200 bg-white py-12">
            <div class="mx-auto max-w-7xl px-4 text-center text-sm text-stone-500 sm:px-6 lg:px-8">
                &copy; {{ new Date().getFullYear() }} MasjidPanel. All rights reserved.
            </div>
        </footer>
    </div>
</template>
