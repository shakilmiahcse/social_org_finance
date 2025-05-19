<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import Swal from 'sweetalert2';
import { useToast } from 'vue-toastification';
import { ref, computed, onMounted } from 'vue';
import DonorCreateModal from '@/Components/DonorCreateModal.vue';
import axios from 'axios';
import vSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';

const toast = useToast();
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Transactions', href: '/transactions' },
    { title: 'Create Expense', href: '/expenses/create' },
];

// Reactive data
const donors = ref<{ id: number, name: string }[]>([]);
const funds = ref<{ id: number, name: string }[]>([]);
const donorSearch = ref('');
const fundSearch = ref('');

// Fetch dropdown data
const fetchData = async () => {
    try {
        const [donorsRes, fundsRes] = await Promise.all([
            axios.get('/donors/dropdown'),
            axios.get('/funds/dropdown')
        ]);

        if (donorsRes.data.success) {
            donors.value = donorsRes.data.donors;
        }
        if (fundsRes.data.success) {
            funds.value = fundsRes.data.funds;
        }
    } catch (error) {
        toast.error('Failed to load dropdown data');
    }
};

// Filtered options
const filteredDonors = computed(() => {
    if (!donorSearch.value) return donors.value;
    const searchTerm = donorSearch.value.toLowerCase();
    return donors.value.filter(donor =>
        donor.name.toLowerCase().includes(searchTerm) ||
        (donor.phone && donor.phone.includes(searchTerm))
    );
});

const filteredFunds = computed(() => {
    if (!fundSearch.value) return funds.value;
    return funds.value.filter(fund =>
        fund.name.toLowerCase().includes(fundSearch.value.toLowerCase())
    );
});

onMounted(() => {
    fetchData();
});

// Form handling
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
    form.post('/expenses', {
        onSuccess: () => {
            toast.success('Expense transaction created successfully!');
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
    <Head title="Create Expense Transaction" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <form @submit.prevent="submit" class="space-y-4">
            <div class="p-4 space-y-4">
                <div class="bg-[#FAFAFA] shadow rounded-xl p-6 space-y-6">
                    <h1 class="text-2xl font-bold mb-6">Create New Expense</h1>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <!-- Raiser Field (Searchable) -->
                        <div>
                            <label class="block font-semibold mb-1">Raiser</label>
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
                            <div v-if="form.errors.donor_id" class="text-red-500 text-sm">
                                {{ form.errors.donor_id }}
                            </div>
                        </div>

                        <!-- Fund Field (Searchable) -->
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
                            <input v-model="form.amount" type="number" step="0.01"
                                class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                :class="{ 'border-red-500': form.errors.amount }" placeholder="Enter Amount" required />
                            <div v-if="form.errors.amount" class="text-red-500 text-sm">{{ form.errors.amount }}</div>
                        </div>

                        <!-- Payment Method -->
                        <div>
                            <label class="block font-semibold mb-1">Payment Method <span
                                    class="text-red-500">*</span></label>
                            <select v-model="form.payment_method"
                                class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                :class="{ 'border-red-500': form.errors.payment_method }" required>
                                <option value="">Select Payment Method</option>
                                <option value="cash">Cash</option>
                                <option value="bank">Bank</option>
                                <option value="card">Card</option>
                                <option value="bkash">bkash</option>
                                <option value="nagad">Nagad</option>
                                <option value="rocket">Rocket</option>
                            </select>
                            <div v-if="form.errors.payment_method" class="text-red-500 text-sm">
                                {{ form.errors.payment_method }}
                            </div>
                        </div>

                        <!-- Purpose -->
                        <div>
                            <label class="block font-semibold mb-1">Purpose</label>
                            <input v-model="form.purpose" type="text"
                                class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                :class="{ 'border-red-500': form.errors.purpose }" placeholder="Enter purpose" />
                            <div v-if="form.errors.purpose" class="text-red-500 text-sm">{{ form.errors.purpose }}</div>
                        </div>

                        <!-- Reference -->
                        <div>
                            <label class="block font-semibold mb-1">Reference</label>
                            <input v-model="form.reference" type="text"
                                class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                :class="{ 'border-red-500': form.errors.reference }" placeholder="Enter reference" />
                            <div v-if="form.errors.reference" class="text-red-500 text-sm">{{ form.errors.reference }}</div>
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block font-semibold mb-1">Status</label>
                            <select v-model="form.status"
                                class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="completed">Completed</option>
                                <option value="pending">Pending</option>
                                <option value="canceled">Canceled</option>
                            </select>
                            <div v-if="form.errors.status" class="text-red-500 text-sm">{{ form.errors.status }}</div>
                        </div>
                    </div>

                    <!-- Note -->
                    <div>
                        <label class="block font-semibold mb-1">Note</label>
                        <textarea v-model="form.note"
                            class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            :class="{ 'border-red-500': form.errors.note }" placeholder="Enter note" rows="3"></textarea>
                        <div v-if="form.errors.note" class="text-red-500 text-sm">{{ form.errors.note }}</div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-between items-center mt-6">
                    <button type="button" @click="$inertia.visit('/transactions')"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-md transition-colors duration-200">
                        <font-awesome-icon :icon="['fas', 'arrow-left']" /> Back
                    </button>
                    <button type="submit"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md font-semibold transition-colors duration-200">
                        Save Expense
                    </button>
                </div>
            </div>
        </form>
    </AppLayout>
</template>
