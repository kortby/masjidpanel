<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { Menu, X } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import AppLogoIcon from '@/components/AppLogoIcon.vue';

const page = usePage();
const auth = computed(() => page.props.auth);
const isMobileMenuOpen = ref(false);

const mainNavItems = [
    { title: 'Home', href: '/' },
    { title: 'About Us', href: '/about' },
    { title: 'Contact', href: '/contact' },
];
</script>

<template>
    <header class="relative z-50 bg-emerald-900 text-emerald-50 shadow-md">
        <div class="mx-auto flex h-20 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
            <!-- Logo -->
            <Link href="/" class="flex items-center gap-x-2 text-xl font-bold tracking-tight text-white transition-colors hover:text-emerald-200">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-800 text-emerald-100 shadow-inner">
                    <AppLogoIcon class="h-6 w-6" />
                </span>
                MasjidPanel
            </Link>

            <!-- Desktop Nav -->
            <nav class="hidden md:flex md:items-center md:gap-8">
                <Link
                    v-for="item in mainNavItems"
                    :key="item.title"
                    :href="item.href"
                    class="text-sm font-medium text-emerald-100 transition-colors hover:text-amber-400"
                >
                    {{ item.title }}
                </Link>
            </nav>

            <!-- Desktop Auth -->
            <div class="hidden items-center gap-4 md:flex">
                <template v-if="auth.user">
                    <Link
                        href="/settings/profile"
                        class="text-sm font-medium text-emerald-100 transition-colors hover:text-amber-400"
                    >
                        Profile
                    </Link>
                    <Link
                        v-if="auth.is_admin"
                        href="/admin/dashboard"
                        class="text-sm font-medium text-emerald-100 transition-colors hover:text-amber-400"
                    >
                        Dashboard
                    </Link>
                </template>
                <template v-else>
                    <Link
                        href="/login"
                        class="text-sm font-medium text-emerald-100 transition-colors hover:text-amber-400"
                    >
                        Log in
                    </Link>
                    <Link
                        href="/register"
                        class="rounded-full bg-amber-500 px-5 py-2 text-sm font-semibold text-emerald-950 transition-all hover:bg-amber-400 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 focus:ring-offset-emerald-900"
                    >
                        Sign up
                    </Link>
                </template>
            </div>

            <!-- Mobile Menu Toggle -->
            <div class="flex items-center md:hidden">
                <button
                    @click="isMobileMenuOpen = !isMobileMenuOpen"
                    type="button"
                    class="inline-flex items-center justify-center rounded-md p-2 text-emerald-200 hover:bg-emerald-800 hover:text-white focus:outline-none"
                >
                    <span class="sr-only">Open main menu</span>
                    <Menu v-if="!isMobileMenuOpen" class="h-6 w-6" aria-hidden="true" />
                    <X v-else class="h-6 w-6" aria-hidden="true" />
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div v-show="isMobileMenuOpen" class="md:hidden">
            <div class="space-y-1 px-2 pb-3 pt-2 sm:px-3">
                <Link
                    v-for="item in mainNavItems"
                    :key="item.title"
                    :href="item.href"
                    class="block rounded-md px-3 py-2 text-base font-medium text-emerald-100 hover:bg-emerald-800 hover:text-white"
                >
                    {{ item.title }}
                </Link>
            </div>
            <div class="border-t border-emerald-800 pb-3 pt-4">
                <div class="flex items-center px-5">
                    <template v-if="auth.user">
                        <Link
                            href="/settings/profile"
                            class="block rounded-md px-3 py-2 text-base font-medium text-emerald-100 hover:bg-emerald-800 hover:text-white"
                        >
                            Profile
                        </Link>
                        <Link
                            v-if="auth.is_admin"
                            href="/admin/dashboard"
                            class="block rounded-md px-3 py-2 text-base font-medium text-emerald-100 hover:bg-emerald-800 hover:text-white"
                        >
                            Dashboard
                        </Link>
                    </template>
                    <template v-else>
                        <div class="mt-3 flex flex-col space-y-2 px-2">
                            <Link
                                href="/login"
                                class="block rounded-md px-3 py-2 text-base font-medium text-emerald-100 hover:bg-emerald-800 hover:text-white"
                            >
                                Log in
                            </Link>
                            <Link
                                href="/register"
                                class="block rounded-full bg-amber-500 px-3 py-2 text-center text-base font-medium text-emerald-950 hover:bg-amber-400"
                            >
                                Sign up
                            </Link>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </header>
</template>
