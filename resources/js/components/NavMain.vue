<script setup lang="ts">
import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { type NavItem, type SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';

defineProps<{
    items: NavItem[];
}>();

const page = usePage<SharedData>();
</script>

<template>
    <SidebarGroup class="px-2 py-0">
        <SidebarGroupLabel>Platform</SidebarGroupLabel>
        <SidebarMenu>
            <SidebarMenuItem v-for="item in items" :key="item.title">
                <!-- Parent with Children -->
                <div v-if="item.children">
                    <SidebarMenuButton as-child :tooltip="item.title">
                        <div class="flex items-center gap-2">
                            <component :is="item.icon" />
                            <span>{{ item.title }}</span>
                        </div>
                    </SidebarMenuButton>
                    <div class="ml-6 mt-1 space-y-1">
                        <Link v-for="child in item.children" :key="child.href" :href="child.href"
                            class="block text-sm px-2 py-1 rounded hover:bg-gray-100"
                            :class="{ 'font-semibold text-blue-600': page.url === child.href }">
                        {{ child.title }}
                        </Link>
                    </div>
                </div>

                <!-- Top-Level Link -->
                <SidebarMenuButton v-else as-child :is-active="item.href === page.url" :tooltip="item.title">
                    <Link :href="item.href">
                    <component :is="item.icon" />
                    <span>{{ item.title }}</span>
                    </Link>
                </SidebarMenuButton>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>
</template>
