<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps<{
    transaction: any;
    donors: { id: number; name: string }[];
    funds: { id: number; name: string }[];
}>();

const breadcrumbs = [
    { title: 'Transactions', href: '/transactions' },
    { title: 'Edit', href: `/transactions/${props.transaction.id}/edit` },
];

const form = useForm({
    donor_id: props.transaction.donor_id,
    fund_id: props.transaction.fund_id,
    amount: props.transaction.amount,
    type: props.transaction.type,
    purpose: props.transaction.purpose,
    payment_method: props.transaction.payment_method,
    reference: props.transaction.reference,
    note: props.transaction.note,
    status: props.transaction.status || 'pending',
});

const submit = () => {
    form.put(`/transactions/${props.transaction.id}`, {
        onSuccess: () => router.visit('/transactions'),
    });
};
</script>

<template>
    <Head title="Edit Transaction" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <form @submit.prevent="submit" class="space-y-4">
            <div class="p-4 space-y-4">
                <div class="bg-[#FAFAFA] shadow rounded-xl p-6 space-y-6">
                    <h1 class="text-2xl font-bold mb-6">Edit Transaction #{{ props.transaction.txn_id }}</h1>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <!-- Donor -->
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

                        <!-- Fund -->
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

                        <!-- Payment Method -->
                        <div>
                            <label class="block font-semibold mb-1">Payment Method</label>
                            <select v-model="form.payment_method" class="w-full border rounded px-3 py-2"
                                :class="{ 'border-red-500': form.errors.payment_method }">
                                <option value="">Select Payment Method</option>
                                <option value="cash">Cash</option>
                                <option value="bkash">bkash</option>
                                <option value="bank">Bank</option>
                            </select>
                            <div v-if="form.errors.payment_method" class="text-red-500 text-sm">{{
                                form.errors.payment_method }}</div>
                        </div>

                        <!-- Purpose -->
                        <div>
                            <label class="block font-semibold mb-1">Purpose</label>
                            <input v-model="form.purpose" type="text" class="w-full border rounded px-3 py-2"
                                :class="{ 'border-red-500': form.errors.purpose }" />
                            <div v-if="form.errors.purpose" class="text-red-500 text-sm">{{ form.errors.purpose }}</div>
                        </div>

                        <!-- Reference -->
                        <div>
                            <label class="block font-semibold mb-1">Reference</label>
                            <input v-model="form.reference" type="text" class="w-full border rounded px-3 py-2"
                                :class="{ 'border-red-500': form.errors.reference }" />
                            <div v-if="form.errors.reference" class="text-red-500 text-sm">{{ form.errors.reference }}</div>
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block font-semibold mb-1">Status</label>
                            <select v-model="form.status" class="w-full border rounded px-3 py-2">
                                <option value="completed">Completed</option>
                                <option value="pending">Pending</option>
                                <option value="canceled">Canceled</option>
                            </select>
                            <div v-if="form.errors.status" class="text-red-500 text-sm">{{ form.errors.status }}</div>
                        </div>

                        <!-- Type (Credit / Debit) -->
                        <div>
                            <label class="block font-semibold mb-1">Type</label>
                            <div class="flex gap-2">
                                <label class="inline-flex items-center">
                                    <input type="radio" value="credit" v-model="form.type" class="hidden peer" />
                                    <span
                                        class="px-4 py-2 rounded-full border border-gray-300 text-sm font-medium cursor-pointer peer-checked:bg-gray-800 peer-checked:text-white">Credit</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" value="debit" v-model="form.type" class="hidden peer" />
                                    <span
                                        class="px-4 py-2 rounded-full border border-gray-300 text-sm font-medium cursor-pointer peer-checked:bg-gray-800 peer-checked:text-white">Debit</span>
                                </label>
                            </div>
                            <div v-if="form.errors.type" class="text-red-500 text-sm">{{ form.errors.type }}</div>
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold mb-1">Note</label>
                        <textarea v-model="form.note" class="w-full border rounded px-3 py-2"
                            :class="{ 'border-red-500': form.errors.note }" placeholder="Enter note"></textarea>
                        <div v-if="form.errors.note" class="text-red-500 text-sm">{{ form.errors.note }}</div>
                    </div>

                    <div class="flex justify-between items-center mt-6">
                        <button type="button" @click="$inertia.visit('/transactions')"
                            class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded">
                            Back
                        </button>
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded font-semibold">
                            Update Transaction
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </AppLayout></template>
