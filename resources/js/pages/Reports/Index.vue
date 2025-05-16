<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import EasyDataTable from 'vue3-easy-data-table';
import 'vue3-easy-data-table/dist/style.css';
import { ref, computed, watch } from 'vue';
import { useToast } from 'vue-toastification';
import Datepicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';

const toast = useToast();

// Props
const props = defineProps({
    initialData: {
        type: Object,
        required: true,
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
});

// Date range picker setup
const dateRange = ref([
    props.filters.start_date ? new Date(props.filters.start_date) : null,
    props.filters.end_date ? new Date(props.filters.end_date) : null
]);

// Preset ranges for date picker
const presetRanges = ref([
    { label: 'Today', value: [new Date(), new Date()] },
    {
        label: 'Yesterday', value: [
            new Date(new Date().setDate(new Date().getDate() - 1)),
            new Date(new Date().setDate(new Date().getDate() - 1))
        ]
    },
    {
        label: 'This Week', value: [
            new Date(new Date().setDate(new Date().getDate() - 7)),
           new Date()
        ]
    },
    {
        label: 'Last Week', value: [
            new Date(new Date().setDate(new Date().getDate() - 14)),
            new Date(new Date().setDate(new Date().getDate() - 7))
        ]
    },
    {
        label: 'This Month', value: [
            new Date(new Date().setDate(1)),
            new Date()
        ]
    },
    {
        label: 'Last Month', value: [
            new Date(new Date().getFullYear(), new Date().getMonth() - 1, 1),
            new Date(new Date().getFullYear(), new Date().getMonth(), 0)
        ]
    },
    {
        label: 'This Year', value: [
            new Date(new Date().getFullYear(), 0, 1),
           new Date()
        ]
    },
    {
        label: 'Last Year', value: [
            new Date(new Date().getFullYear() - 1, 0, 1),
            new Date(new Date().getFullYear() - 1, 11, 31)
        ]
    },
]);

// Other refs
const activeTab = ref('financial_summary');
const searchTerm = ref('');
const months = ref(props.filters.months || 6);
const limit = ref(props.filters.limit || 5);
const isLoading = ref(false);

// Data refs initialized with props
const financialSummary = ref(props.initialData.financialSummary);
const fundAllocation = ref(props.initialData.fundAllocation);
const topDonors = ref(props.initialData.topDonors);
const transactionTrends = ref(props.initialData.transactionTrends);

// Computed
const filteredFundAllocation = computed(() =>
    fundAllocation.value.filter(fund =>
        fund.name.toLowerCase().includes(searchTerm.value.toLowerCase())
    )
);

const filteredTopDonors = computed(() =>
    topDonors.value.filter(donor =>
        donor.name.toLowerCase().includes(searchTerm.value.toLowerCase())
    )
);

// Constants
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Reports', href: '/reports' },
];

const fundHeaders = [
    { text: 'Fund Name', value: 'name', sortable: true },
    { text: 'Total Amount', value: 'amount', sortable: true },
];

const donorHeaders = [
    { text: 'Name', value: 'name', sortable: true },
    { text: 'Email', value: 'email', sortable: true },
    { text: 'Phone', value: 'phone', sortable: true },
    { text: 'Total Donated', value: 'amount', sortable: true },
];

const exportReport = async (type: 'excel' | 'pdf') => {
    try {
        const params = {
            type,
            report_type: activeTab.value,
            start_date: dateRange.value[0] ? dateRange.value[0].toISOString().split('T')[0] : null,
            end_date: dateRange.value[1] ? dateRange.value[1].toISOString().split('T')[0] : null,
            months: months.value,
            limit: limit.value,
        };

        const url = `/reports/export?${new URLSearchParams(params)}`;
        window.open(url, '_blank');
    } catch (error) {
        toast.error('Failed to export report');
    }
};

const resetDateRange = () => {
    dateRange.value = [null, null];
};

watch(activeTab, () => {
    searchTerm.value = ''; // Reset search when changing tabs
});
</script>

<template>
    <Head title="Reports" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4 space-y-4">
            <div class="bg-[#FAFAFA] shadow rounded-xl p-6 space-y-6">
                <!-- Header -->
                <div class="flex justify-between items-center">
                    <h1 class="text-2xl font-bold">Reports</h1>
                </div>

                <!-- Filters -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date Range</label>
                        <div class="flex items-center gap-2">
                            <Datepicker v-model="dateRange" range :enable-time-picker="false" auto-apply
                                :max-date="new Date()" placeholder="Select date range" :preset-ranges="presetRanges"
                                :format="'MMM dd, yyyy'" :preview-format="'MMM dd, yyyy'"
                                class="w-full border rounded-lg focus:outline-none focus:ring focus:border-blue-100">
                                <template #input-icon>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </template>
                            </Datepicker>
                            <button @click="resetDateRange"
                                class="bg-gray-200 hover:bg-gray-300 text-gray-800 p-2 rounded transition"
                                title="Clear date range">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div v-if="activeTab === 'transaction_trends'">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Months</label>
                        <select v-model="months"
                            class="border rounded-lg px-3 py-2 w-full focus:outline-none focus:ring focus:border-blue-100">
                            <option value="3">3 Months</option>
                            <option value="6">6 Months</option>
                            <option value="12">12 Months</option>
                            <option value="24">24 Months</option>
                        </select>
                    </div>

                    <div v-if="activeTab === 'top_donors'">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Top Donors Limit</label>
                        <select v-model="limit"
                            class="border rounded-lg px-3 py-2 w-full focus:outline-none focus:ring focus:border-blue-100">
                            <option value="5">Top 5</option>
                            <option value="10">Top 10</option>
                            <option value="20">Top 20</option>
                        </select>
                    </div>
                </div>

                <!-- Action buttons -->
                <div class="flex justify-between items-center flex-wrap gap-2">
                    <div class="flex gap-2">
                        <button @click="exportReport('excel')" :disabled="isLoading"
                            class="bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm px-3 py-1.5 rounded transition">
                            Export Excel
                        </button>
                        <button @click="exportReport('pdf')" :disabled="isLoading"
                            class="bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm px-3 py-1.5 rounded transition">
                            Export PDF
                        </button>
                    </div>
                </div>

                <!-- Tabs -->
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8">
                        <button @click="activeTab = 'financial_summary'"
                            :class="[activeTab === 'financial_summary' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300', 'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm']">
                            Financial Summary
                        </button>
                        <button @click="activeTab = 'fund_allocation'"
                            :class="[activeTab === 'fund_allocation' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300', 'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm']">
                            Fund Allocation
                        </button>
                        <button @click="activeTab = 'top_donors'"
                            :class="[activeTab === 'top_donors' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300', 'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm']">
                            Top Donors
                        </button>
                        <button @click="activeTab = 'transaction_trends'"
                            :class="[activeTab === 'transaction_trends' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300', 'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm']">
                            Transaction Trends
                        </button>
                    </nav>
                </div>

                <!-- Tab content -->
                <div class="pt-4">
                    <!-- Financial Summary -->
                    <div v-if="activeTab === 'financial_summary'" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-white p-4 rounded-lg shadow">
                            <h3 class="text-lg font-medium text-gray-900">Current Balance</h3>
                            <p class="mt-2 text-3xl font-semibold"
                                :class="financialSummary.balance < 0 ? 'text-red-600' : 'text-green-600'">
                                ৳{{ financialSummary.balance.toLocaleString() }}
                            </p>
                        </div>
                        <div class="bg-white p-4 rounded-lg shadow">
                            <h3 class="text-lg font-medium text-gray-900">Total Credit</h3>
                            <p class="mt-2 text-3xl font-semibold text-green-600">
                                ৳{{ financialSummary.total_credit.toLocaleString() }}
                            </p>
                        </div>
                        <div class="bg-white p-4 rounded-lg shadow">
                            <h3 class="text-lg font-medium text-gray-900">Total Debit</h3>
                            <p class="mt-2 text-3xl font-semibold text-red-600">
                                ৳{{ financialSummary.total_debit.toLocaleString() }}
                            </p>
                        </div>
                    </div>

                    <!-- Fund Allocation -->
                    <div v-if="activeTab === 'fund_allocation'">
                        <div class="mb-4">
                            <input v-model="searchTerm" type="text" placeholder="Search funds..."
                                class="border rounded-lg px-3 py-2 w-full sm:w-64 focus:outline-none focus:ring focus:border-blue-100">
                        </div>
                        <EasyDataTable :headers="fundHeaders" :items="filteredFundAllocation" header-text-direction="left"
                            rows-per-page="20" :rows-items="[30, 50, 100, 200]" buttons-pagination class="custom-table">
                            <template #item-amount="{ amount }">
                                <span class="font-semibold">৳{{ amount.toLocaleString() }}</span>
                            </template>
                        </EasyDataTable>
                    </div>

                    <!-- Top Donors -->
                    <div v-if="activeTab === 'top_donors'">
                        <div class="mb-4">
                            <input v-model="searchTerm" type="text" placeholder="Search donors..."
                                class="border rounded-lg px-3 py-2 w-full sm:w-64 focus:outline-none focus:ring focus:border-blue-100">
                        </div>
                        <EasyDataTable :headers="donorHeaders" :items="filteredTopDonors" header-text-direction="left"
                            rows-per-page="20" :rows-items="[30, 50, 100, 200]" buttons-pagination class="custom-table">
                            <template #item-amount="{ amount }">
                                <span class="font-semibold">৳{{ amount.toLocaleString() }}</span>
                            </template>
                        </EasyDataTable>
                    </div>

                    <!-- Transaction Trends -->
                    <div v-if="activeTab === 'transaction_trends'">
                        <div class="bg-white p-4 rounded-lg shadow">
                            <div class="h-64">
                                <!-- Chart would go here - you can use Chart.js or any other library -->
                                <p class="text-center text-gray-500">Transaction trends chart would be displayed here</p>
                                <div class="mt-4">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Period</th>
                                                <th
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Credit</th>
                                                <th
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Debit</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            <tr v-for="(trend, index) in transactionTrends" :key="index">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ new Date(trend.year, trend.month - 1).toLocaleDateString('default', {
                                                        month: 'short', year: 'numeric'
                                                    }) }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">
                                                    ৳{{ trend.credit.toLocaleString() }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">
                                                    ৳{{ trend.debit.toLocaleString() }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style>
/* Custom table styles */
.custom-table {
    --easy-table-footer-background-color: #dfe0e1;
    --easy-table-footer-font-size: 14px;
    --easy-table-header-background-color: #f1f3f5;
    --easy-table-header-font-size: 14px;
    --easy-table-header-font-color: #495057;
}

/* Datepicker custom styles */
.dp__mai
n {
    font-family: inherit;
}

.dp__input {
    height: 42px;
    border-radius: 0.5rem;
    border: 1px solid #d1d5db;
    padding-left: 2.5rem;
}

.dp__input:hover {
    border-color: #93c5fd;
}

.dp__input:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 1px #3b82f6;
}

.dp__menu {
    z-index: 50;
}</style>
