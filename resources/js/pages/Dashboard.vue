<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import { ref, watch, computed } from 'vue';
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
import Datepicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';

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
)

const props = defineProps<{
    recentTransactions: Array<object>,
    financialSummary: {
        balance: number,
        total_credit: number,
        total_debit: number,
        monthly_credit: number,
        monthly_debit: number
    },
    fundAllocation: Array<object>,
    topDonors: Array<object>,
    transactionTrends: Array<object>,
    alerts: Array<{ type: string, message: string }>,
    permissions: {
        viewTransactions: boolean,
        createTransactions: boolean,
        viewDonors: boolean,
        viewFunds: boolean,
        viewDashboard: boolean
    },
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' }
];

// Date range for filtering
const dateRange = ref([new Date(new Date().setDate(new Date().getDate() - 30)), new Date()]);

// Active chart type for trends
const activeChartType = ref('line');

// Chart data refs
const fundChartData = ref({
    labels: [] as string[],
    datasets: [{
        data: [] as number[],
        backgroundColor: [
            '#4F46E5', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6',
            '#EC4899', '#06B6D4', '#84CC16', '#F97316', '#6366F1'
        ],
        borderWidth: 2,
        borderColor: '#ffffff'
    }]
});

const trendChartData = ref({
    labels: [] as string[],
    datasets: [
        {
            label: 'Credit',
            data: [] as number[],
            borderColor: '#10B981',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
            tension: 0.4,
            fill: true,
            borderWidth: 3
        },
        {
            label: 'Debit',
            data: [] as number[],
            borderColor: '#EF4444',
            backgroundColor: 'rgba(239, 68, 68, 0.1)',
            tension: 0.4,
            fill: true,
            borderWidth: 3
        }
    ]
});

// Chart options
const pieChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: 'right' as const,
            labels: {
                padding: 15,
                usePointStyle: true,
            }
        },
        tooltip: {
            callbacks: {
                label: function (context: any) {
                    const label = context.label || '';
                    const value = context.raw;
                    const total = context.dataset.data.reduce((a: number, b: number) => a + b, 0);
                    const percentage = Math.round((value / total) * 100);
                    return `${label}: ৳${value.toLocaleString()} (${percentage}%)`;
                }
            }
        }
    }
};

const lineChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    interaction: {
        intersect: false,
        mode: 'index' as const,
    },
    scales: {
        y: {
            beginAtZero: true,
            grid: {
                drawBorder: false,
            },
            ticks: {
                callback: function (value: any) {
                    return '৳' + value.toLocaleString();
                }
            }
        },
        x: {
            grid: {
                display: false
            }
        }
    },
    plugins: {
        legend: {
            display: true,
            position: 'top' as const,
        },
        tooltip: {
            callbacks: {
                label: function (context: any) {
                    return context.dataset.label + ': ৳' + context.raw.toLocaleString();
                }
            }
        }
    }
};

const barChartOptions = {
    ...lineChartOptions,
    scales: {
        ...lineChartOptions.scales,
        x: {
            grid: {
                display: false
            }
        }
    }
};

// Computed properties for additional metrics
const monthlyNet = computed(() => {
    return props.financialSummary.monthly_credit - props.financialSummary.monthly_debit;
});

const growthRate = computed(() => {
    const previousMonth = props.financialSummary.monthly_credit * 0.8; // Mock data
    const currentMonth = props.financialSummary.monthly_credit;
    return previousMonth > 0 ? ((currentMonth - previousMonth) / previousMonth) * 100 : 0;
});

// Define processChartData first
const processChartData = (fundAllocation: any[], transactionTrends: any[]) => {
    // Process Fund Allocation data
    if (fundAllocation && fundAllocation.length) {
        fundChartData.value.labels = fundAllocation.map(fund => fund.name);
        fundChartData.value.datasets[0].data = fundAllocation.map(fund =>
            parseFloat(fund.transactions_sum_amount) || 0
        );
    }

    // Process Transaction Trends data
    if (transactionTrends && transactionTrends.length) {
        const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
            'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        trendChartData.value.labels = transactionTrends.map(t =>
            `${monthNames[t.month - 1]} ${t.year}`
        );
        trendChartData.value.datasets[0].data = transactionTrends.map(t =>
            parseFloat(t.credit) || 0
        );
        trendChartData.value.datasets[1].data = transactionTrends.map(t =>
            parseFloat(t.debit) || 0
        );
    }
};

// Then set up the watcher
watch(() => [props.fundAllocation, props.transactionTrends], () => {
    processChartData(props.fundAllocation, props.transactionTrends);
}, { immediate: true });

// Format number helper
const formatNumber = (num: number) => {
    return num.toLocaleString();
};
</script>

<template>
    <Head title="Dashboard" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6 space-y-6">
            <!-- Header Section -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Financial Dashboard</h1>
                        <p class="text-gray-600 mt-1">Welcome to your organization's financial overview</p>
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

            <!-- Alerts Section -->
            <div v-if="alerts && alerts.length && props.permissions.viewDashboard" class="space-y-3">
                <div v-for="(alert, index) in alerts" :key="index"
                     :class="{
                         'bg-yellow-50 border-yellow-400': alert.type === 'warning',
                         'bg-blue-50 border-blue-400': alert.type === 'info',
                         'bg-red-50 border-red-400': alert.type === 'error'
                     }"
                     class="border-l-4 p-4 rounded-xl shadow-sm">
                    <div class="flex items-center">
                        <div :class="{
                            'text-yellow-400': alert.type === 'warning',
                            'text-blue-400': alert.type === 'info',
                            'text-red-400': alert.type === 'error'
                        }">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <p class="ml-3 font-medium" :class="{
                            'text-yellow-700': alert.type === 'warning',
                            'text-blue-700': alert.type === 'info',
                            'text-red-700': alert.type === 'error'
                        }">
                            {{ alert.message }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Financial Summary Cards -->
            <div v-if="props.permissions.viewDashboard" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Total Balance -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Balance</p>
                            <p class="text-2xl font-bold mt-2" :class="financialSummary.balance < 0 ? 'text-red-600' : 'text-gray-900'">
                                ৳{{ formatNumber(financialSummary.balance) }}
                            </p>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-gray-500 to-gray-600 flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-sm text-gray-500 mt-3">Overall net balance</p>
                </div>

                <!-- Total Credit -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Credit</p>
                            <p class="text-2xl font-bold mt-2 text-green-600">
                                +৳{{ formatNumber(financialSummary.total_credit) }}
                            </p>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-green-500 to-green-600 flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-sm text-gray-500 mt-3">All time income</p>
                </div>

                <!-- Total Debit -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Debit</p>
                            <p class="text-2xl font-bold mt-2 text-red-600">
                                -৳{{ formatNumber(financialSummary.total_debit) }}
                            </p>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-red-500 to-red-600 flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-sm text-gray-500 mt-3">All time expenses</p>
                </div>

                <!-- Monthly Performance -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">This Month</p>
                            <p class="text-2xl font-bold mt-2" :class="monthlyNet >= 0 ? 'text-green-600' : 'text-red-600'">
                                {{ monthlyNet >= 0 ? '+' : '' }}৳{{ formatNumber(monthlyNet) }}
                            </p>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="flex justify-between mt-3 text-sm">
                        <div>
                            <p class="text-green-600 font-medium">+৳{{ formatNumber(financialSummary.monthly_credit) }}</p>
                            <p class="text-gray-500">Income</p>
                        </div>
                        <div>
                            <p class="text-red-600 font-medium">-৳{{ formatNumber(financialSummary.monthly_debit) }}</p>
                            <p class="text-gray-500">Expenses</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                <!-- Fund Allocation Pie Chart -->
                <div v-if="props.permissions.viewFunds" class="xl:col-span-1 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900">Fund Allocation</h3>
                        <Link href="/funds" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                            View All
                        </Link>
                    </div>
                    <div class="h-80">
                        <Pie v-if="fundAllocation && fundAllocation.length" :data="fundChartData" :options="pieChartOptions" />
                        <div v-else class="h-full flex flex-col items-center justify-center text-gray-500">
                            <svg class="w-16 h-16 mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            <p>No fund allocation data available</p>
                        </div>
                    </div>
                </div>

                <!-- Transaction Trends Chart -->
                <div v-if="props.permissions.viewTransactions" class="xl:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900">Transaction Trends</h3>
                        <div class="flex items-center gap-4">
                            <div class="flex bg-gray-100 rounded-lg p-1">
                                <button @click="activeChartType = 'line'"
                                        :class="activeChartType === 'line' ? 'bg-white shadow-sm' : 'text-gray-600'"
                                        class="px-3 py-1 rounded-md text-sm font-medium transition-all">
                                    Line
                                </button>
                                <button @click="activeChartType = 'bar'"
                                        :class="activeChartType === 'bar' ? 'bg-white shadow-sm' : 'text-gray-600'"
                                        class="px-3 py-1 rounded-md text-sm font-medium transition-all">
                                    Bar
                                </button>
                            </div>
                            <Link href="/reports" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                                View Reports
                            </Link>
                        </div>
                    </div>
                    <div class="h-80">
                        <component
                            :is="activeChartType === 'line' ? Line : Bar"
                            v-if="transactionTrends && transactionTrends.length"
                            :data="trendChartData"
                            :options="activeChartType === 'line' ? lineChartOptions : barChartOptions"
                        />
                        <div v-else class="h-full flex flex-col items-center justify-center text-gray-500">
                            <svg class="w-16 h-16 mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                            </svg>
                            <p>No transaction trends data available</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Transactions & Top Donors -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Recent Transactions -->
                <div v-if="props.permissions.viewTransactions" class="bg-white rounded-2xl shadow-sm border border-gray-100">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-semibold text-gray-900">Recent Transactions</h3>
                            <Link href="/transactions" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                                View All
                            </Link>
                        </div>
                        <div class="space-y-4">
                            <div v-for="txn in recentTransactions" :key="txn.id"
                                 class="flex items-center justify-between p-4 rounded-xl border border-gray-100 hover:border-gray-200 transition-colors">
                                <div class="flex items-center gap-4">
                                    <div :class="{
                                        'bg-green-100 text-green-600': txn.type === 'credit',
                                        'bg-red-100 text-red-600': txn.type === 'debit'
                                    }" class="w-10 h-10 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path v-if="txn.type === 'credit'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                            <path v-if="txn.type === 'debit'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">{{ txn.txn_id }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold" :class="txn.type === 'credit' ? 'text-green-600' : 'text-red-600'">
                                        {{ txn.type === 'credit' ? '+' : '-' }}৳{{ formatNumber(txn.amount) }}
                                    </p>
                                    <p class="text-sm text-gray-500">{{ new Date(txn.created_at).toLocaleDateString() }}</p>
                                </div>
                            </div>
                        </div>
                        <div v-if="!recentTransactions || recentTransactions.length === 0" class="text-center py-8 text-gray-500">
                            <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p>No recent transactions</p>
                        </div>
                    </div>
                </div>

                <!-- Top Donors -->
                <div v-if="props.permissions.viewDonors" class="bg-white rounded-2xl shadow-sm border border-gray-100">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-semibold text-gray-900">Top Donors</h3>
                            <Link href="/donors" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                                View All
                            </Link>
                        </div>
                        <div v-if="topDonors && topDonors.length" class="space-y-4">
                            <div v-for="(donor, index) in topDonors" :key="donor.id"
                                 class="flex items-center justify-between p-4 rounded-xl border border-gray-100 hover:border-gray-200 transition-colors">
                                <div class="flex items-center gap-4">
                                    <div class="relative">
                                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-semibold">
                                            {{ donor.name.charAt(0).toUpperCase() }}
                                        </div>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">{{ donor.name }}</p>
                                        <p class="text-sm text-gray-500">{{ donor.email || 'No email' }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-green-600">৳{{ formatNumber(donor.transactions_sum_amount) }}</p>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-8 text-gray-500">
                            <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <p>No donor data available</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
/* Custom styles for better visual hierarchy */
.bg-gradient-to-br {
    background-size: 200% 200%;
    animation: gradient 15s ease infinite;
}

@keyframes gradient {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

.hover-lift:hover {
    transform: translateY(-2px);
    transition: transform 0.2s ease;
}
</style>
