<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { BookOpen, Folder, LayoutGrid, Users, Banknote, ReceiptText, Sliders } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
        icon: LayoutGrid,
    },
    {
        title: 'Donors/Raisers',
        href: '/donors',
        icon: Users,
    },
    {
        title: 'Funds',
        href: '/funds',
        icon: Banknote,
    },
    {
        title: 'Transactions',
        icon: ReceiptText,
        // href: '/transactions',
        children: [
            {
                title: 'All Transactions',
                href: '/transactions',
            },
            {
                title: 'Add',
                href: '/transactions/create',
            },
            {
                title: 'Income',
                href: '/incomes/create',
            },
            {
                title: 'Expense',
                href: '/expenses/create',
            },
        ],
    },
    {
        title: 'Adjustments',
        icon: Sliders,
        // href: '/transactions',
        children: [
            {
                title: 'All Adjustments',
                href: '/adjustments',
            },
            {
                title: 'Add Adjustments',
                href: '/adjustments/create',
            },
        ],
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'Github Repo',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: Folder,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits',
        icon: BookOpen,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('dashboard')">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
