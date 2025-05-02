<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import Swal from 'sweetalert2';
import { useToast } from 'vue-toastification';

const toast = useToast();
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Transactions', href: '/transactions' },
    { title: 'Create Income', href: '/incomes/create' },
];

// Receiving dropdown data for donors and funds from the controller
defineProps<{
    donors: { id: number, name: string }[];
    funds: { id: number, name: string }[];
}>();

const form = useForm({
    donor_id: '',
    fund_id: '',
    amount: '',
    purpose: '',
    payment_method: '',
    reference: '',
    note: '',
    status: 'completed',
});

const submit = () => {
    form.post('/incomes', {
        onSuccess: () => {
            toast.success('Income transaction created successfully!');
            router.visit('/transactions');
        },
        onError: (errors) => {
            Swal.fire({
                title: 'Validation Error',
                text: Object.values(errors).join('\n'),
                icon: 'error',
                confirmButtonText: 'OK',
            });
        },
    });
};
</script>

<template>
    <Head title="Create Income Transaction" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <form @submit.prevent="submit" class="space-y-4">
            <div class="p-4 space-y-4">
                <div class="bg-[#FAFAFA] shadow rounded-xl p-6 space-y-6">
                    <h1 class="text-2xl font-bold mb-6">Create New Income</h1>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
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
                            <label class="block font-semibold mb-1">Fund <span class="text-red-500">*</span></label>
                            <select v-model="form.fund_id" class="w-full border rounded px-3 py-2"
                                :class="{ 'border-red-500': form.errors.fund_id }" required>
                                <option value="">Select Fund</option>
                                <option v-for="fund in funds" :key="fund.id" :value="fund.id">
                                    {{ fund.name }}
                                </option>
                            </select>
                            <div v-if="form.errors.fund_id" class="text-red-500 text-sm">{{ form.errors.fund_id }}</div>
                        </div>

                        <!-- Amount -->
                        <div>
                            <label class="block font-semibold mb-1">Amount <span class="text-red-500">*</span></label>
                            <input v-model="form.amount" type="number" step="0.01" class="w-full border rounded px-3 py-2"
                                :class="{ 'border-red-500': form.errors.amount }" placeholder="Enter Amount" required />
                            <div v-if="form.errors.amount" class="text-red-500 text-sm">{{ form.errors.amount }}</div>
                        </div>

                        <!-- Payment Method -->
                        <div>
                            <label class="block font-semibold mb-1">Payment Method <span
                                    class="text-red-500">*</span></label>
                            <select v-model="form.payment_method" class="w-full border rounded px-3 py-2"
                                :class="{ 'border-red-500': form.errors.payment_method }" required>
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
                                :class="{ 'border-red-500': form.errors.purpose }" placeholder="Enter purpose" />
                            <div v-if="form.errors.purpose" class="text-red-500 text-sm">{{ form.errors.purpose }}</div>
                        </div>

                        <!-- Reference -->
                        <div>
                            <label class="block font-semibold mb-1">Reference</label>
                            <input v-model="form.reference" type="text" class="w-full border rounded px-3 py-2"
                                :class="{ 'border-red-500': form.errors.reference }" placeholder="Enter reference" />
                            <div v-if="form.errors.reference" class="text-red-500 text-sm">{{ form.errors.reference }}</div>
                        </div>

                        <div>
                            <label for="status" class="block font-semibold mb-1">
                                Status
                            </label>
                            <select v-model="form.status" id="status" class="w-full border rounded px-3 py-2">
                                <option value="completed">Completed</option>
                                <option value="pending">Pending</option>
                            </select>
                            <div v-if="form.errors.status" class="text-red-500 text-sm">{{ form.errors.status }}</div>
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold mb-1">Note</label>
                        <textarea v-model="form.note" class="w-full border rounded px-3 py-2"
                            :class="{ 'border-red-500': form.errors.note }" placeholder="Enter note"></textarea>
                        <div v-if="form.errors.note" class="text-red-500 text-sm">{{ form.errors.note }}</div>
                    </div>
                </div>
                <div class="flex justify-between items-center mt-6">
                    <button type="button" @click="$inertia.visit('/transactions')"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded">
                        <font-awesome-icon :icon="['fas', 'arrow-left']" /> Back
                    </button>
                    <button type="submit"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded font-semibold">
                        Save Income
                    </button>
                </div>
            </div>
        </form>
    </AppLayout>
</template>
