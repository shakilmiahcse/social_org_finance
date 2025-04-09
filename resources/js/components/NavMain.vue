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
import { ref } from 'vue';

defineProps<{
    items: NavItem[];
}>();

const page = usePage<SharedData>();
const expanded = ref<string | null>(null); // currently expanded menu

function toggleMenu(title: string) {
    expanded.value = expanded.value === title ? null : title;
}
</script>

<template>
    <SidebarGroup class="px-2 py-0">
        <SidebarGroupLabel>Platform</SidebarGroupLabel>
        <SidebarMenu>
            <SidebarMenuItem v-for="item in items" :key="item.title">
                <!-- Has Children -->
                <div v-if="item.children">
                    <SidebarMenuButton as-child :tooltip="item.title" @click="toggleMenu(item.title)">
                        <div class="flex items-center gap-2 w-full px-2 py-2 rounded-md cursor-pointer hover:bg-muted transition"
                            :class="{ 'bg-muted text-primary': expanded === item.title }">
                            <component :is="item.icon" class="w-4 h-4" />
                            <span class="flex-1 text-sm">{{ item.title }}</span>
                            <span class="text-xs">
                                <span v-if="expanded === item.title">▾</span>
                                <span v-else>▸</span>
                            </span>
                        </div>
                    </SidebarMenuButton>

                    <!-- Child Items -->
                    <div v-show="expanded === item.title" class="ml-6 mt-1 space-y-1 transition-all duration-300">
                        <Link v-for="child in item.children" :key="child.href" :href="child.href"
                            class="flex items-center gap-2 text-sm px-2 py-1 rounded hover:bg-gray-100 transition"
                            :class="{ 'font-semibold text-blue-600': page.url === child.href }">
                        <span class="text-xs">→</span>
                        <span>{{ child.title }}</span>
                        </Link>
                    </div>

                </div>


                <!-- No Children -->
                <SidebarMenuButton v-else as-child :is-active="item.href === page.url" :tooltip="item.title"
                    @click="expanded = null">
                    <Link :href="item.href">
                    <component :is="item.icon" />
                    <span>{{ item.title }}</span>
                    </Link>
                </SidebarMenuButton>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>
</template>
