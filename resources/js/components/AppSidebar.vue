<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { BookOpen, Folder, LayoutGrid, Users, Banknote, ReceiptText, Sliders, LineChart, Settings, HandCoins  } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
        icon: LayoutGrid,
    },
    {
        title: 'User Management',
        icon: Users,
        children: [
            {
                title: 'Users',
                href: '/users',
            },
            {
                title: 'Roles',
                href: '/roles-permissions',
            },
        ],
    },
    {
        title: 'Donors/Raisers',
        href: '/donors',
        icon: HandCoins,
    },
    {
        title: 'Funds',
        href: '/funds',
        icon: Banknote,
    },
    {
        title: 'Transactions',
        icon: ReceiptText,
        children: [
            {
                title: 'All Transactions',
                href: '/transactions',
            },
            {
                title: 'Add Transaction',
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
        children: [
            {
                title: 'All Adjustments',
                href: '/adjustments',
            },
            {
                title: 'Add Adjustment',
                href: '/adjustments/create',
            },
        ],
    },
    {
        title: 'Reports',
        icon: LineChart,
        children: [
            {
                title: 'Financial',
                href: '',
            },
            // Donor Reports
            {
                title: 'Donors',
                href: '',
            },
            // Campaign Reports
            {
                title: 'Campaigns',
                href: '',
            },
            // System Reports
            {
                title: 'System',
                href: '',
            },
        ],
    },
    {
        title: 'Settings',
        icon: Settings,
        href: '/settings/org',
    },
];

// const footerNavItems: NavItem[] = [
//     {
//         title: 'Resources',
//         href: '/resources',
//         icon: Folder,
//     },
//     {
//         title: 'Documentation',
//         href: '/docs',
//         icon: BookOpen,
//     },
// ];
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
