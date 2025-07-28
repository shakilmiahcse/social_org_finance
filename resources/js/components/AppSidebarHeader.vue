<script setup lang="ts">
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import { SidebarTrigger } from '@/components/ui/sidebar';
import type { BreadcrumbItemType } from '@/types';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { DropdownMenu, DropdownMenuContent, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { Bell, ChevronDown } from 'lucide-vue-next';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import UserMenuContent from '@/components/UserMenuContent.vue';
import { getInitials } from '@/composables/useInitials';

defineProps<{
    breadcrumbs?: BreadcrumbItemType[];
}>();

const page = usePage();
const auth = computed(() => page.props.auth);
const notifications = computed(() => page.props.notifications || []);
</script>

<template>
    <header
        class="flex h-16 shrink-0 items-center justify-between gap-2 border-b border-sidebar-border/70 px-6 transition-[width,height] ease-linear group-has-[[data-collapsible=icon]]/sidebar-wrapper:h-12 md:px-4"
    >
        <div class="flex items-center gap-2">
            <SidebarTrigger class="-ml-1" />
            <template v-if="breadcrumbs && breadcrumbs.length > 0">
                <Breadcrumbs :breadcrumbs="breadcrumbs" />
            </template>
        </div>

        <div class="flex items-center gap-2">
            <!-- Notification Dropdown -->
            <DropdownMenu>
                <DropdownMenuTrigger as-child>
                    <Button
                        variant="ghost"
                        size="icon"
                        class="relative size-10 w-auto rounded-full p-1 focus-within:ring-2 focus-within:ring-primary"
                    >
                        <Bell class="size-5" />
                        <span 
                            v-if="auth.user.unread_notifications_count > 0"
                            class="absolute right-0 top-0 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-xs text-white"
                        >
                            {{ auth.user.unread_notifications_count }}
                        </span>
                    </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent align="end" class="w-80 p-0">
                    <div class="p-2">
                        <div class="flex items-center justify-between px-2 py-1">
                            <h3 class="text-sm font-medium">Notifications</h3>
                            <Button variant="ghost" size="sm" class="text-xs">Mark all as read</Button>
                        </div>
                    </div>
                    <div class="max-h-80 overflow-y-auto">
                        <div v-if="notifications.length === 0" class="px-4 py-6 text-center text-sm text-gray-500">
                            No new notifications
                        </div>
                        <div v-else>
                            <div 
                                v-for="notification in notifications" 
                                :key="notification.id"
                                class="border-b border-gray-100 px-4 py-3 hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-gray-800"
                            >
                                <div class="flex items-start">
                                    <div class="flex-1">
                                        <p class="text-sm font-medium">{{ notification.data.message }}</p>
                                        <p class="text-xs text-gray-500">{{ notification.created_at }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="border-t border-gray-100 px-4 py-2 text-center dark:border-gray-800">
                        <a href="/notifications" class="text-sm font-medium text-primary hover:underline">
                            View all notifications
                        </a>
                    </div>
                </DropdownMenuContent>
            </DropdownMenu>

            <!-- User Dropdown with Name -->
            <DropdownMenu>
                <DropdownMenuTrigger as-child>
                    <Button
                        variant="ghost"
                        class="flex h-10 items-center gap-2 rounded-full px-2 focus-within:ring-2 focus-within:ring-primary"
                    >
                        <Avatar class="size-8 overflow-hidden rounded-full">
                            <AvatarImage v-if="auth.user.avatar" :src="auth.user.avatar" :alt="auth.user.name" />
                            <AvatarFallback class="rounded-lg bg-neutral-200 font-semibold text-black dark:bg-neutral-700 dark:text-white">
                                {{ getInitials(auth.user?.name) }}
                            </AvatarFallback>
                        </Avatar>
                        <span class="hidden text-sm font-medium sm:inline-flex">{{ auth.user.name }}</span>
                        <ChevronDown class="h-4 w-4 opacity-50" />
                    </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent align="end" class="w-56">
                    <UserMenuContent :user="auth.user" />
                </DropdownMenuContent>
            </DropdownMenu>
        </div>
    </header>
</template>