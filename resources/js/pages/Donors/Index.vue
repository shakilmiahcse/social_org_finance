<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'; // Import AppLayout for layout
import { type BreadcrumbItem } from '@/types';  // Import necessary types
import { Head } from '@inertiajs/vue3'; // Set the page title dynamically

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Donors',
        href: '/donors',
    },
];

defineProps({
    donors: Array, // Accept donors data as prop
});
</script>

<template>
    <Head title="Donor List" /> <!-- Set the page title dynamically -->

    <AppLayout :breadcrumbs="breadcrumbs"> <!-- Wrap the content inside the layout -->
        <div class="flex flex-col gap-4 p-4 rounded-xl">
            <h1 class="text-2xl font-bold mb-4">Donor List</h1>

            <table class="w-full table-auto border">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border px-4 py-2">ID</th>
                        <th class="border px-4 py-2">Name</th>
                        <th class="border px-4 py-2">Email</th>
                        <th class="border px-4 py-2">Phone</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="donor in donors" :key="donor.id">
                        <td class="border px-4 py-2">{{ donor.id }}</td>
                        <td class="border px-4 py-2">{{ donor.name }}</td>
                        <td class="border px-4 py-2">{{ donor.email }}</td>
                        <td class="border px-4 py-2">{{ donor.phone }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AppLayout>
</template>
