<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';

const sidebarNavItems: NavItem[] = [
    { title: 'Organization', href: '/settings/org' },
    { title: 'Fund Type', href: '/settings/fund-type' },
];

const page = usePage();
const currentPath = page.props.ziggy?.location ? new URL(page.props.ziggy.location).pathname : '';
</script>

<template>
    <div class="p-4 space-y-4">
            <div class="bg-[#FAFAFA] shadow rounded-xl p-6 space-y-6">
            <Heading title="Settings" description="Manage your organization settings." />

            <div class="flex flex-col lg:flex-row lg:space-x-10">
                <!-- Sidebar Navigation -->
                <aside class="w-full lg:w-56 flex-shrink-0">
                    <nav class="space-y-1">
                        <Link v-for="item in sidebarNavItems" :key="item.href" :href="item.href"
                            class="block px-3 py-2 rounded-md text-sm font-medium transition-colors" :class="[
                                currentPath === item.href
                                    ? 'bg-blue-100 text-blue-600 font-semibold'
                                    : 'text-gray-700 hover:bg-gray-100 hover:text-blue-600',
                            ]">
                        {{ item.title }}
                        </Link>
                    </nav>
                </aside>

                <!-- Separator (mobile only) -->
                <Separator class="my-6 block lg:hidden" />

                <!-- Main Content -->
                <main class="flex-1 max-w-full">
                    <div class="space-y-8">
                        <slot />
                    </div>
                </main>
            </div>
        </div>
    </div>
</template>
