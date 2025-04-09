<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import { ref } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Transactions', href: '/transactions' },
    { title: 'Create', href: '/transactions/create' },
];

const form = useForm({
    txn_id: '',
    donor_id: '',
    fund_id: '',
    amount: '',
    type: '',
    purpose: '',
    payment_method: '',
    reference: '',
    note: '',
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
        <div class="p-4 max-w-4xl mx-auto">
            <div class="bg-white shadow rounded-xl p-6">
                <h1 class="text-2xl font-bold mb-6">Create New Transaction</h1>

                <form @submit.prevent="submit" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-semibold mb-1">Transaction ID</label>
                            <input v-model="form.txn_id" type="text" class="w-full border rounded px-3 py-2"
                                :class="{ 'border-red-500': form.errors.txn_id }" />
                            <div v-if="form.errors.txn_id" class="text-red-500 text-sm">{{ form.errors.txn_id }}</div>
                        </div>
                        <div>
                            <label class="block font-semibold mb-1">Donor ID</label>
                            <input v-model="form.donor_id" type="text" class="w-full border rounded px-3 py-2"
                                :class="{ 'border-red-500': form.errors.donor_id }" />
                            <div v-if="form.errors.donor_id" class="text-red-500 text-sm">{{ form.errors.donor_id }}</div>
                        </div>
                        <div>
                            <label class="block font-semibold mb-1">Fund ID</label>
                            <input v-model="form.fund_id" type="text" class="w-full border rounded px-3 py-2"
                                :class="{ 'border-red-500': form.errors.fund_id }" />
                            <div v-if="form.errors.fund_id" class="text-red-500 text-sm">{{ form.errors.fund_id }}</div>
                        </div>
                        <div>
                            <label class="block font-semibold mb-1">Amount</label>
                            <input v-model="form.amount" type="number" step="0.01" class="w-full border rounded px-3 py-2"
                                :class="{ 'border-red-500': form.errors.amount }" />
                            <div v-if="form.errors.amount" class="text-red-500 text-sm">{{ form.errors.amount }}</div>
                        </div>
                        <div>
                            <label class="block font-semibold mb-1">Type</label>
                            <input v-model="form.type" type="text" class="w-full border rounded px-3 py-2"
                                :class="{ 'border-red-500': form.errors.type }" />
                            <div v-if="form.errors.type" class="text-red-500 text-sm">{{ form.errors.type }}</div>
                        </div>
                        <div>
                            <label class="block font-semibold mb-1">Purpose</label>
                            <input v-model="form.purpose" type="text" class="w-full border rounded px-3 py-2"
                                :class="{ 'border-red-500': form.errors.purpose }" />
                            <div v-if="form.errors.purpose" class="text-red-500 text-sm">{{ form.errors.purpose }}</div>
                        </div>
                        <div>
                            <label class="block font-semibold mb-1">Payment Method</label>
                            <input v-model="form.payment_method" type="text" class="w-full border rounded px-3 py-2"
                                :class="{ 'border-red-500': form.errors.payment_method }" />
                            <div v-if="form.errors.payment_method" class="text-red-500 text-sm">{{
                                form.errors.payment_method }}</div>
                        </div>
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
