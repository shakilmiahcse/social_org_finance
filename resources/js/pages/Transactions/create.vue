<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import Swal from 'sweetalert2';
import { useToast } from 'vue-toastification';
import { ref, onMounted, computed } from 'vue';
import DonorCreateModal from '@/Components/DonorCreateModal.vue';
import axios from 'axios';
import vSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';

const toast = useToast();
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Transactions', href: '/transactions' },
    { title: 'Create', href: '/transactions/create' },
];

// Donor create modal reference
const donorCreateModal = ref();
const isDonorButtonHovered = ref(false);
const donors = ref<{ id: number, name: string }[]>([]);
const funds = ref<{ id: number, name: string }[]>([]);

// Search queries
const donorSearch = ref('');
const fundSearch = ref('');

// Fetch initial data
const fetchData = async () => {
    try {
        const [donorsRes, fundsRes] = await Promise.all([
            axios.get('/donors/dropdown'),
            axios.get('/funds/dropdown')
        ]);

        if (donorsRes.data.success) {
            donors.value = donorsRes.data.donors;
        } else {
            throw new Error(donorsRes.data.message);
        }

        if (fundsRes.data.success) {
            funds.value = fundsRes.data.funds;
        } else {
            throw new Error(fundsRes.data.message);
        }
    } catch (error) {
        Swal.fire({
            title: 'Error!',
            text: error.response?.data?.message || error.message || 'Failed to load data',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    }
};

// Filter donors based on search
const filteredDonors = computed(() => {
    if (!donorSearch.value) return donors.value;
    const searchTerm = donorSearch.value.toLowerCase();
    return donors.value.filter(donor =>
        donor.name.toLowerCase().includes(searchTerm) ||
        (donor.phone && donor.phone.includes(searchTerm))
    );
});

// Filter funds based on search
const filteredFunds = computed(() => {
    if (!fundSearch.value) return funds.value;
    return funds.value.filter(fund =>
        fund.name.toLowerCase().includes(fundSearch.value.toLowerCase())
    );
});

onMounted(() => {
    fetchData();
});

// Handle new donor creation
const handleDonorCreated = async (newDonorId: number) => {
    try {
        const response = await axios.get('/donors/dropdown');

        if (response.data.success) {
            donors.value = response.data.donors;
            form.donor_id = newDonorId;
            donorCreateModal.value.close();
        } else {
            throw new Error(response.data.message);
        }
    } catch (error) {
        Swal.fire({
            title: 'Error!',
            text: error.response?.data?.message || error.message || 'Failed to refresh donor list',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    }
};

const form = useForm({
    txn_id: '',
    donor_id: '',
    fund_id: '',
    amount: '',
    type: 'credit',
    purpose: '',
    payment_method: '',
    reference: '',
    note: '',
    status: 'completed',
    date: new Date().toISOString().split('T')[0],
});

const submit = () => {
    form.post('/transactions', {
        onSuccess: () => {
            toast.success('Transaction created successfully!');
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
    <Head title="Create Transaction" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <!-- Donor Create Modal -->
        <DonorCreateModal ref="donorCreateModal" @donor-created="handleDonorCreated" />

        <form @submit.prevent="submit" class="space-y-4">
            <div class="p-4 space-y-4">
                <div class="bg-[#FAFAFA] shadow rounded-xl p-6 space-y-6">
                    <h1 class="text-2xl font-bold mb-6">Create New Transaction</h1>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <!-- Improved Donor Field -->
                        <div>
                            <label class="block font-semibold mb-1">Donor/Raiser</label>
                            <div class="relative flex items-center">
                                <v-select v-model="form.donor_id" :options="filteredDonors" label="name"
                                    :reduce="donor => donor.id" placeholder="Search by name or phone" :filterable="false"
                                    @search="donorSearch = $event"
                                    :class="['w-full', { 'border-red-500': form.errors.donor_id }]">
                                    <template #option="{ name, phone }">
                                        <div class="">
                                            <span class="truncate">{{ name }}</span>
                                            <span class="text-gray-500 ml-2 whitespace-nowrap" v-if="phone">
                                                ({{ phone }})
                                            </span>
                                        </div>
                                    </template>
                                    <template #selected-option="{ name, phone }">
                                        <div class="">
                                            <span>{{ name }}</span>
                                            <span class="text-gray-500 ml-2" v-if="phone">
                                                ({{ phone }})
                                            </span>
                                        </div>
                                    </template>
                                    <template #no-options>
                                        No raisers found
                                    </template>
                                </v-select>
                                <button type="button" @click.stop="donorCreateModal.open()"
                                    class="ml-1 bg-blue-600 hover:bg-blue-700 text-white p-1 rounded-full transition-colors duration-200"
                                    title="Quick add donor">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                            <div v-if="form.errors.donor_id" class="text-red-500 text-sm">{{ form.errors.donor_id }}</div>
                        </div>

                        <!-- Fund Field with Consistent Styling -->
                        <div>
                            <label class="block font-semibold mb-1">Fund <span class="text-red-500">*</span></label>
                            <v-select v-model="form.fund_id" :options="filteredFunds" label="name" :reduce="fund => fund.id"
                                placeholder="Search or select fund" :filterable="false" @search="fundSearch = $event"
                                :class="['w-full', { 'border-red-500': form.errors.fund_id }]" required></v-select>
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
                                <option value="bank">Bank</option>
                                <option value="card">Card</option>
                                <option value="bkash">bkash</option>
                                <option value="nagad">Nagad</option>
                                <option value="rocket">Rocket</option>
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

                        <!-- Type (Smart Radio Buttons) -->
                        <div>
                            <label class="block font-semibold mb-1">Type</label>
                            <div class="flex gap-2">
                                <!-- Credit Option -->
                                <label class="inline-flex items-center">
                                    <input type="radio" value="credit" v-model="form.type" class="hidden peer" />
                                    <span class="px-4 py-2 rounded-full border border-gray-300 text-sm font-medium cursor-pointer transition-colors duration-200
                peer-checked:bg-gray-800 peer-checked:text-white peer-checked:border-gray-800
                hover:bg-gray-100">
                                        Credit
                                    </span>
                                </label>
                                <!-- Debit Option -->
                                <label class="inline-flex items-center">
                                    <input type="radio" value="debit" v-model="form.type" class="hidden peer" />
                                    <span class="px-4 py-2 rounded-full border border-gray-300 text-sm font-medium cursor-pointer transition-colors duration-200
                peer-checked:bg-gray-800 peer-checked:text-white peer-checked:border-gray-800
                hover:bg-gray-100">
                                        Debit
                                    </span>
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


                </div>
                <div class="flex justify-between items-center mt-6">
                    <button type="button" @click="$inertia.visit('/transactions')"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded">
                        <font-awesome-icon :icon="['fas', 'arrow-left']" /> Back
                    </button>
                    <button type="submit"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded font-semibold">
                        Save Transaction
                    </button>
                </div>
            </div>

        </form>
    </AppLayout>
</template>
