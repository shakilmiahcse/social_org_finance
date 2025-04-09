<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import { ref } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Transactions', href: '/transactions' },
    { title: 'Create', href: '/transactions/create' },
];

// Receiving dropdown data for donors and funds from the controller
defineProps<{
    donors: { id: number, name: string }[];
    funds: { id: number, name: string }[];
}>();

const form = useForm({
    txn_id: '', // Transaction ID
    donor_id: '', // Donor ID
    fund_id: '', // Fund ID
    amount: '', // Amount
    type: '', // Transaction type (credit or debit)
    purpose: '', // Purpose
    payment_method: '', // Payment method (e.g., cash, bank, etc.)
    reference: '', // Transaction reference
    note: '', // Additional notes
    status: 'pending', // Default status
});

const submit = () => {
    form.post('/transactions', {
        onSuccess: () => {
            router.visit('/transactions');
        },
    });
};
</script>

<template>
    <Head title="Create Transaction" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4 space-y-4">
            <div class="bg-[#FAFAFA] shadow rounded-xl p-6 space-y-6">
                <h1 class="text-2xl font-bold mb-6">Create New Transaction</h1>

                <form @submit.prevent="submit" class="space-y-4">
                    <!-- Three-column grid layout -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <!-- Transaction ID -->
                        <div>
                            <label class="block font-semibold mb-1">Transaction ID</label>
                            <input v-model="form.txn_id" type="text" class="w-full border rounded px-3 py-2"
                                :class="{ 'border-red-500': form.errors.txn_id }" />
                            <div v-if="form.errors.txn_id" class="text-red-500 text-sm">{{ form.errors.txn_id }}</div>
                        </div>

                        <!-- Donor ID (Dropdown) -->
                        <div>
                            <label class="block font-semibold mb-1">Donor</label>
                            <select v-model="form.donor_id" class="w-full border rounded px-3 py-2"
                                :class="{ 'border-red-500': form.errors.donor_id }">
                                <option value="">Select Donor</option>
                                <option v-for="donor in donors" :key="donor.id" :value="donor.id">
                                    {{ donor.name }}
                                </option>
                            </select>
                            <div v-if="form.errors.donor_id" class="text-red-500 text-sm">{{ form.errors.donor_id }}</div>
                        </div>

                        <!-- Fund ID (Dropdown) -->
                        <div>
                            <label class="block font-semibold mb-1">Fund</label>
                            <select v-model="form.fund_id" class="w-full border rounded px-3 py-2"
                                :class="{ 'border-red-500': form.errors.fund_id }">
                                <option value="">Select Fund</option>
                                <option v-for="fund in funds" :key="fund.id" :value="fund.id">
                                    {{ fund.name }}
                                </option>
                            </select>
                            <div v-if="form.errors.fund_id" class="text-red-500 text-sm">{{ form.errors.fund_id }}</div>
                        </div>

                        <!-- Amount -->
                        <div>
                            <label class="block font-semibold mb-1">Amount</label>
                            <input v-model="form.amount" type="number" step="0.01" class="w-full border rounded px-3 py-2"
                                :class="{ 'border-red-500': form.errors.amount }" />
                            <div v-if="form.errors.amount" class="text-red-500 text-sm">{{ form.errors.amount }}</div>
                        </div>

                        <!-- Type -->
                        <div>
                            <label class="block font-semibold mb-1">Type</label>
                            <select v-model="form.type" class="w-full border rounded px-3 py-2"
                                :class="{ 'border-red-500': form.errors.type }">
                                <option value="">Select Type</option>
                                <option value="credit">Credit</option>
                                <option value="debit">Debit</option>
                            </select>
                            <div v-if="form.errors.type" class="text-red-500 text-sm">{{ form.errors.type }}</div>
                        </div>

                        <!-- Purpose -->
                        <div>
                            <label class="block font-semibold mb-1">Purpose</label>
                            <input v-model="form.purpose" type="text" class="w-full border rounded px-3 py-2"
                                :class="{ 'border-red-500': form.errors.purpose }" />
                            <div v-if="form.errors.purpose" class="text-red-500 text-sm">{{ form.errors.purpose }}</div>
                        </div>

                        <!-- Payment Method -->
                        <div>
                            <label class="block font-semibold mb-1">Payment Method</label>
                            <select v-model="form.payment_method" class="w-full border rounded px-3 py-2"
                                :class="{ 'border-red-500': form.errors.payment_method }">
                                <option value="">Select Payment Method</option>
                                <option value="cash">Cash</option>
                                <option value="bkash">bkash</option>
                                <option value="card">Card</option>
                                <option value="bank">Bank</option>
                            </select>
                            <div v-if="form.errors.payment_method" class="text-red-500 text-sm">{{ form.errors.payment_method }}</div>
                        </div>

                        <!-- Reference -->
                        <div>
                            <label class="block font-semibold mb-1">Reference</label>
                            <input v-model="form.reference" type="text" class="w-full border rounded px-3 py-2"
                                :class="{ 'border-red-500': form.errors.reference }" />
                            <div v-if="form.errors.reference" class="text-red-500 text-sm">{{ form.errors.reference }}</div>
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold mb-1">Note</label>
                        <textarea v-model="form.note" class="w-full border rounded px-3 py-2"
                            :class="{ 'border-red-500': form.errors.note }"></textarea>
                        <div v-if="form.errors.note" class="text-red-500 text-sm">{{ form.errors.note }}</div>
                    </div>

                    <div class="flex justify-between items-center mt-6">
                        <button type="button" @click="$inertia.visit('/transactions')"
                            class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded">
                            Back
                        </button>
                        <button type="submit"
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded font-semibold">
                            Save Transaction
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
