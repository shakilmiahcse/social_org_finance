<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import EasyDataTable from 'vue3-easy-data-table';
import 'vue3-easy-data-table/dist/style.css';
import { ref, computed, watch, onMounted } from 'vue';
import { useToast } from 'vue-toastification';
import Datepicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';
import {
    Chart as ChartJS,
    ArcElement,
    LineElement,
    BarElement,
    PointElement,
    BarController,
    LineController,
    CategoryScale,
    LinearScale,
    Tooltip,
    Legend,
    Title
} from 'chart.js'
import { Pie, Line, Bar } from 'vue-chartjs'

// Register ChartJS components
ChartJS.register(
    ArcElement,
    LineElement,
    BarElement,
    PointElement,
    BarController,
    LineController,
    CategoryScale,
    LinearScale,
    Tooltip,
    Legend,
    Title
);

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
    permissions: {
        type: Object as () => {
            view: boolean;
            export: boolean;
            generate: boolean;
        },
        required: true
    }
});

// Date range picker setup - default to last 7 days
const dateRange = ref([
    props.filters.start_date ? new Date(props.filters.start_date) : new Date(new Date().setDate(new Date().getDate() - 7)),
    props.filters.end_date ? new Date(props.filters.end_date) : new Date()
]);

// Chart configuration
const chartType = ref(props.filters.chart_type || 'line');
const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: 'top' as const,
        },
        tooltip: {
            callbacks: {
                label: function(context: any) {
                    return `৳${context.raw.toLocaleString()}`;
                }
            }
        }
    },
    scales: {
        y: {
            beginAtZero: true,
            ticks: {
                callback: function(value: any) {
                    return '৳' + value.toLocaleString();
                }
            }
        }
    }
};

// Preset ranges for date picker
const presetRanges = ref([
    {
        label: 'Last 7 Days',
        value: [
            new Date(new Date().setDate(new Date().getDate() - 7)),
            new Date()
        ]
    },
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
const monthlyComparison = ref(props.initialData.monthlyComparison);
const donationDistribution = ref(props.initialData.donationDistribution);

// Computed properties for charts
const fundAllocationChartData = computed(() => {
    return {
        labels: fundAllocation.value.map(fund => fund.name),
        datasets: [
            {
                data: fundAllocation.value.map(fund => fund.amount),
                backgroundColor: fundAllocation.value.map(fund => fund.color),
                borderWidth: 2,
                borderColor: '#fff'
            }
        ]
    };
});

const transactionTrendsChartData = computed(() => {
    return {
        labels: transactionTrends.value.map(trend => trend.period),
        datasets: [
            {
                label: 'Credit',
                data: transactionTrends.value.map(trend => trend.credit),
                borderColor: '#10B981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                tension: 0.4,
                fill: true
            },
            {
                label: 'Debit',
                data: transactionTrends.value.map(trend => trend.debit),
                borderColor: '#EF4444',
                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                tension: 0.4,
                fill: true
            },
            {
                label: 'Net Flow',
                data: transactionTrends.value.map(trend => trend.net_flow),
                borderColor: '#3B82F6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4,
                fill: true,
                borderDash: [5, 5]
            }
        ]
    };
});

const donationDistributionChartData = computed(() => {
    return {
        labels: donationDistribution.value.map(item => item.range),
        datasets: [
            {
                label: 'Donation Amount',
                data: donationDistribution.value.map(item => item.total_amount),
                backgroundColor: [
                    '#3B82F6',
                    '#10B981',
                    '#F59E0B',
                    '#EF4444',
                    '#8B5CF6'
                ],
                borderWidth: 2
            }
        ]
    };
});

const monthlyComparisonChartData = computed(() => {
    return {
        labels: ['Credit', 'Debit'],
        datasets: [
            {
                label: 'Current Month',
                data: [monthlyComparison.value.current.credit, monthlyComparison.value.current.debit],
                backgroundColor: 'rgba(59, 130, 246, 0.8)',
            },
            {
                label: 'Previous Month',
                data: [monthlyComparison.value.previous.credit, monthlyComparison.value.previous.debit],
                backgroundColor: 'rgba(107, 114, 128, 0.8)',
            }
        ]
    };
});

// Filter computed properties
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

// Watch for changes and reload data
watch([dateRange, months, limit, chartType], () => {
    if (dateRange.value[0] && dateRange.value[1]) {
        router.get('/reports', {
            start_date: dateRange.value[0].toISOString().split('T')[0],
            end_date: dateRange.value[1].toISOString().split('T')[0],
            months: months.value,
            limit: limit.value,
            chart_type: chartType.value,
        }, {
            preserveState: true,
            replace: true,
            preserveScroll: true,
        });
    }
}, { deep: true });

// Constants
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Reports', href: '/reports' },
];

const fundHeaders = [
    { text: 'Fund Name', value: 'name', sortable: true },
    { text: 'Amount', value: 'amount', sortable: true },
    { text: 'Percentage', value: 'percentage', sortable: true },
];

const donorHeaders = [
    { text: 'Name', value: 'name', sortable: true },
    { text: 'Email', value: 'email', sortable: true },
    { text: 'Phone', value: 'phone', sortable: true },
    { text: 'Total Donated', value: 'amount', sortable: true },
    { text: 'Transactions', value: 'transaction_count', sortable: true },
    { text: 'Avg Donation', value: 'avg_donation', sortable: true },
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
    dateRange.value = [new Date(new Date().setDate(new Date().getDate() - 7)), new Date()];
};

watch(activeTab, () => {
    searchTerm.value = ''; // Reset search when changing tabs
});

const rowsPerPage = ref(20);
const rowsItems = ref([20, 30, 50, 100, 200]);

// Helper function to format numbers with commas
const formatNumber = (num: number) => {
    return num.toLocaleString();
};

// Helper function to calculate percentage change
const calculateChange = (current: number, previous: number) => {
    if (previous === 0) return 0;
    return ((current - previous) / previous) * 100;
};
</script>

<template>
    <Head title="Reports" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4 space-y-6">
            <!-- Header Section -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Financial Reports</h1>
                        <p class="text-gray-600 mt-1">Comprehensive overview of your organization's financial health</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="text-right">
                            <p class="text-sm text-gray-500">Current Balance</p>
                            <p class="text-2xl font-bold" :class="financialSummary.balance < 0 ? 'text-red-600' : 'text-green-600'">
                                ৳{{ formatNumber(financialSummary.balance) }}
                            </p>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters Section -->
            <div v-if="props.permissions.generate" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="grid grid-cols-1 lg:grid-cols-5 gap-4 items-end">
                    <div class="lg:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Date Range</label>
                        <div class="flex items-center gap-2">
                            <Datepicker v-model="dateRange" range :enable-time-picker="false" auto-apply
                                :max-date="new Date()" placeholder="Select date range" :preset-ranges="presetRanges"
                                :format="'MMM dd, yyyy'" :preview-format="'MMM dd, yyyy'"
                                class="w-full border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <template #input-icon>
                                    <div class="pl-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400"
                                            viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                                clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                </template>
                            </Datepicker>
                            <button @click="resetDateRange" class="p-2.5 text-gray-500 hover:text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div v-if="activeTab === 'transaction_trends'">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Time Period</label>
                        <select v-model="months"
                            class="border border-gray-200 rounded-xl px-4 py-2.5 w-full focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="3">3 Months</option>
                            <option value="6">6 Months</option>
                            <option value="12">12 Months</option>
                            <option value="24">24 Months</option>
                        </select>
                    </div>

                    <div v-if="activeTab === 'top_donors'">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Top Donors</label>
                        <select v-model="limit"
                            class="border border-gray-200 rounded-xl px-4 py-2.5 w-full focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="5">Top 5</option>
                            <option value="10">Top 10</option>
                            <option value="20">Top 20</option>
                        </select>
                    </div>

                    <div v-if="activeTab === 'transaction_trends'">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Chart Type</label>
                        <select v-model="chartType"
                            class="border border-gray-200 rounded-xl px-4 py-2.5 w-full focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="line">Line Chart</option>
                            <option value="bar">Bar Chart</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="grid grid-cols-1 xl:grid-cols-4 gap-6">
                <!-- Sidebar Navigation -->
                <div class="xl:col-span-1">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-2 sticky top-6">
                        <button v-for="tab in [
                            { id: 'financial_summary', label: 'Financial Summary', icon: '📊' },
                            { id: 'fund_allocation', label: 'Fund Allocation', icon: '💰' },
                            { id: 'top_donors', label: 'Top Donors', icon: '👥' },
                            { id: 'transaction_trends', label: 'Transaction Trends', icon: '📈' }
                        ]" :key="tab.id" @click="activeTab = tab.id"
                            :class="[
                                activeTab === tab.id
                                    ? 'bg-blue-50 text-blue-700 border-blue-200'
                                    : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50 border-transparent',
                                'w-full text-left px-4 py-3 rounded-xl border transition-all duration-200 flex items-center gap-3 font-medium'
                            ]">
                            <span class="text-lg">{{ tab.icon }}</span>
                            {{ tab.label }}
                        </button>

                        <!-- Export Buttons -->
                        <div v-if="props.permissions.export" class="pt-4 mt-4 border-t border-gray-200">
                            <button @click="exportReport('excel')" :disabled="isLoading"
                                class="w-full bg-green-50 hover:bg-green-100 text-green-700 px-4 py-3 rounded-xl border border-green-200 transition-all duration-200 flex items-center justify-center gap-2 font-medium">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Export Excel
                            </button>
                            <button @click="exportReport('pdf')" :disabled="isLoading"
                                class="w-full bg-red-50 hover:bg-red-100 text-red-700 px-4 py-3 rounded-xl border border-red-200 transition-all duration-200 flex items-center justify-center gap-2 font-medium mt-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Export PDF
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Main Content Area -->
                <div class="xl:col-span-3 space-y-6">
                    <!-- Financial Summary -->
                    <div v-if="activeTab === 'financial_summary' && props.permissions.view" class="space-y-6">
                        <!-- Key Metrics Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            <div v-for="metric in [
                                {
                                    label: 'Total Credit',
                                    value: financialSummary.total_credit,
                                    change: calculateChange(financialSummary.total_credit, monthlyComparison.previous.credit),
                                    icon: '⬆️',
                                    color: 'green'
                                },
                                {
                                    label: 'Total Debit',
                                    value: financialSummary.total_debit,
                                    change: calculateChange(financialSummary.total_debit, monthlyComparison.previous.debit),
                                    icon: '⬇️',
                                    color: 'red'
                                },
                                {
                                    label: 'Transactions',
                                    value: financialSummary.transaction_count,
                                    change: 0,
                                    icon: '🔄',
                                    color: 'blue'
                                },
                                {
                                    label: 'Avg Transaction',
                                    value: financialSummary.avg_transaction,
                                    change: financialSummary.growth_rate,
                                    icon: '📊',
                                    color: 'purple'
                                }
                            ]" :key="metric.label"
                                class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-600">{{ metric.label }}</p>
                                        <p class="text-2xl font-bold mt-2" :class="`text-${metric.color}-600`">
                                            ৳{{ formatNumber(metric.value) }}
                                        </p>
                                        <p v-if="metric.change !== 0" class="text-sm mt-1" :class="metric.change >= 0 ? 'text-green-600' : 'text-red-600'">
                                            {{ metric.change >= 0 ? '↑' : '↓' }} {{ Math.abs(metric.change).toFixed(1) }}%
                                        </p>
                                    </div>
                                    <div class="text-2xl">{{ metric.icon }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Charts Row -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Monthly Comparison -->
                            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Monthly Comparison</h3>
                                <div class="h-64">
                                    <Bar :data="monthlyComparisonChartData" :options="chartOptions" />
                                </div>
                            </div>

                            <!-- Donation Distribution -->
                            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Donation Distribution</h3>
                                <div class="h-64">
                                    <Pie :data="donationDistributionChartData" :options="chartOptions" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Fund Allocation -->
                    <div v-if="activeTab === 'fund_allocation' && props.permissions.view" class="space-y-6">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Pie Chart -->
                            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Fund Allocation Distribution</h3>
                                <div class="h-80">
                                    <Pie :data="fundAllocationChartData" :options="chartOptions" />
                                </div>
                            </div>

                            <!-- Data Table -->
                            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                                <div class="flex items-center justify-between mb-6">
                                    <h3 class="text-lg font-semibold text-gray-900">Fund Details</h3>
                                    <input v-model="searchTerm" type="text" placeholder="Search funds..."
                                        class="border border-gray-200 rounded-xl px-4 py-2 w-64 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                                <EasyDataTable :headers="fundHeaders" :items="filteredFundAllocation"
                                    header-text-direction="left" :rows-per-page="rowsPerPage" :rows-items="rowsItems"
                                    buttons-pagination class="custom-table">
                                    <template #item-amount="{ amount }">
                                        <span class="font-semibold text-gray-900">৳{{ formatNumber(amount) }}</span>
                                    </template>
                                    <template #item-percentage="{ percentage }">
                                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ percentage }}%
                                        </span>
                                    </template>
                                </EasyDataTable>
                            </div>
                        </div>
                    </div>

                    <!-- Top Donors -->
                    <div v-if="activeTab === 'top_donors' && props.permissions.view" class="space-y-6">
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-lg font-semibold text-gray-900">Top Donors</h3>
                                <input v-model="searchTerm" type="text" placeholder="Search donors..."
                                    class="border border-gray-200 rounded-xl px-4 py-2 w-64 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <EasyDataTable :headers="donorHeaders" :items="filteredTopDonors"
                                header-text-direction="left" :rows-per-page="rowsPerPage" :rows-items="rowsItems"
                                buttons-pagination class="custom-table">
                                <template #item-amount="{ amount }">
                                    <span class="font-semibold text-green-600">৳{{ formatNumber(amount) }}</span>
                                </template>
                                <template #item-avg_donation="{ avg_donation }">
                                    <span class="text-sm text-gray-600">৳{{ formatNumber(avg_donation) }}</span>
                                </template>
                                <template #item-transaction_count="{ transaction_count }">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ transaction_count }}
                                    </span>
                                </template>
                            </EasyDataTable>
                        </div>
                    </div>

                    <!-- Transaction Trends -->
                    <div v-if="activeTab === 'transaction_trends' && props.permissions.view" class="space-y-6">
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-6">Transaction Trends Analysis</h3>
                            <div class="h-96 mb-6">
                                <component
                                    :is="chartType === 'line' ? Line : Bar"
                                    :data="transactionTrendsChartData"
                                    :options="chartOptions"
                                />
                            </div>

                            <!-- Detailed Table -->
                            <div class="mt-6">
                                <h4 class="text-md font-semibold text-gray-900 mb-4">Detailed Transaction Data</h4>
                                <div class="overflow-hidden rounded-xl border border-gray-200">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Period</th>
                                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Credit</th>
                                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Debit</th>
                                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Net Flow</th>
                                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Transactions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            <tr v-for="(trend, index) in transactionTrends" :key="index" class="hover:bg-gray-50">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                    {{ trend.period }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600 font-semibold">
                                                    ৳{{ formatNumber(trend.credit) }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600 font-semibold">
                                                    ৳{{ formatNumber(trend.debit) }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold" :class="trend.net_flow >= 0 ? 'text-green-600' : 'text-red-600'">
                                                    ৳{{ formatNumber(trend.net_flow) }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                                    {{ trend.transaction_count }}
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

<style scoped>
/* Custom table styles */
.custom-table {
    --easy-table-border: 1px solid #e5e7eb;
    --easy-table-row-border: 1px solid #e5e7eb;
    --easy-table-header-font-size: 0.875rem;
    --easy-table-header-height: 3rem;
    --easy-table-header-font-color: #6b7280;
    --easy-table-header-background-color: #f9fafb;
    --easy-table-header-item-padding: 0.75rem 1rem;

    --easy-table-body-row-font-size: 0.875rem;
    --easy-table-body-row-font-color: #6b7280;
    --easy-table-body-row-background-color: #ffffff;
    --easy-table-body-row-height: 3.5rem;
    --easy-table-body-row-item-padding: 0.75rem 1rem;

    --easy-table-body-row-hover-font-color: #374151;
    --easy-table-body-row-hover-background-color: #f9fafb;

    --easy-table-footer-background-color: #f9fafb;
    --easy-table-footer-font-color: #6b7280;
    --easy-table-footer-font-size: 0.875rem;
    --easy-table-footer-padding: 0.75rem 1rem;
    --easy-table-footer-height: 3rem;

    --easy-table-rows-per-page-selector-width: 4.5rem;
    --easy-table-rows-per-page-selector-option-padding: 0.5rem;
    --easy-table-border-radius: 0.75rem;
}

/* Datepicker custom styles */
.dp__main {
    font-family: inherit;
}

.dp__input {
    height: 3rem;
    border-radius: 0.75rem;
    border: 1px solid #e5e7eb;
    padding-left: 3rem;
    font-size: 0.875rem;
}

.dp__input:hover {
    border-color: #93c5fd;
}

.dp__input:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1);
}

.dp__menu {
    z-index: 50;
    border-radius: 0.75rem;
    border: 1px solid #e5e7eb;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}
</style>
