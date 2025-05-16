<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import { ref, watch } from 'vue';
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
    Legend
} from 'chart.js'
import { Pie, Line } from 'vue-chartjs'

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
    Legend
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
    alerts: Array<{ type: string, message: string }>
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' }
];

// Chart data refs
const fundChartData = ref({
    labels: [] as string[],
    datasets: [{
        data: [] as number[],
        backgroundColor: [
            '#4F46E5', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6'
        ],
        borderWidth: 1
    }]
});

const trendChartData = ref({
    labels: [] as string[],
    datasets: [
        {
            label: 'Credit',
            data: [] as number[],
            borderColor: '#10B981',
            backgroundColor: '#10B981',
            tension: 0.3,
            borderWidth: 2
        },
        {
            label: 'Debit',
            data: [] as number[],
            borderColor: '#EF4444',
            backgroundColor: '#EF4444',
            tension: 0.3,
            borderWidth: 2
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
        },
        tooltip: {
            callbacks: {
                label: function (context: any) {
                    return '৳' + context.raw.toLocaleString();
                }
            }
        }
    }
};

const lineChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    scales: {
        y: {
            beginAtZero: true,
            ticks: {
                callback: function (value: any) {
                    return '৳' + value;
                }
            }
        }
    },
    plugins: {
        tooltip: {
            callbacks: {
                label: function (context: any) {
                    return context.dataset.label + ': ৳' + context.raw.toLocaleString();
                }
            }
        }
    }
};

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
</script>

<template>
    <Head title="Dashboard" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4 space-y-6">
            <!-- Alerts Section -->
            <div v-if="alerts && alerts.length" class="space-y-2">
                <div v-for="(alert, index) in alerts" :key="index" :class="{
                    'bg-yellow-50 border-yellow-400 text-yellow-700': alert.type === 'warning',
                    'bg-blue-50 border-blue-400 text-blue-700': alert.type === 'info'
                }" class="border-l-4 p-4 rounded-md">
                    <p class="font-medium">{{ alert.message }}</p>
                </div>
            </div>

            <!-- Financial Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-[#FAFAFA] rounded-xl shadow p-6">
                    <h3 class="text-gray-500 text-sm font-medium">Total Balance</h3>
                    <p class="text-2xl font-bold mt-2">৳{{ financialSummary.balance.toLocaleString() }}</p>
                    <p class="text-sm text-gray-500 mt-1">Overall net balance</p>
                </div>

                <div class="bg-[#FAFAFA] rounded-xl shadow p-6">
                    <h3 class="text-gray-500 text-sm font-medium">Total Credit</h3>
                    <p class="text-2xl font-bold mt-2 text-green-600">+৳{{ financialSummary.total_credit.toLocaleString() }}
                    </p>
                    <p class="text-sm text-gray-500 mt-1">All time income</p>
                </div>

                <div class="bg-[#FAFAFA] rounded-xl shadow p-6">
                    <h3 class="text-gray-500 text-sm font-medium">Total Debit</h3>
                    <p class="text-2xl font-bold mt-2 text-red-600">-৳{{ financialSummary.total_debit.toLocaleString() }}
                    </p>
                    <p class="text-sm text-gray-500 mt-1">All time expenses</p>
                </div>

                <div class="bg-[#FAFAFA] rounded-xl shadow p-6">
                    <h3 class="text-gray-500 text-sm font-medium">This Month</h3>
                    <div class="flex justify-between mt-2">
                        <div>
                            <p class="text-green-600 font-medium">+৳{{ financialSummary.monthly_credit.toLocaleString() }}
                            </p>
                            <p class="text-sm text-gray-500">Income</p>
                        </div>
                        <div>
                            <p class="text-red-600 font-medium">-৳{{ financialSummary.monthly_debit.toLocaleString() }}</p>
                            <p class="text-sm text-gray-500">Expenses</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Fund Allocation Pie Chart -->
                <div class="bg-[#FAFAFA] rounded-xl shadow p-6">
                    <h3 class="text-lg font-semibold mb-4">Fund Allocation</h3>
                    <div class="h-64">
                        <Pie v-if="fundAllocation && fundAllocation.length" :data="fundChartData"
                            :options="pieChartOptions" />
                        <div v-else class="h-full flex items-center justify-center text-gray-500">
                            No fund allocation data available
                        </div>
                    </div>
                </div>

                <!-- Transaction Trends Line Chart -->
                <div class="bg-[#FAFAFA] rounded-xl shadow p-6">
                    <h3 class="text-lg font-semibold mb-4">Transaction Trends (Last 6 Months)</h3>
                    <div class="h-64">
                        <Line v-if="transactionTrends && transactionTrends.length" :data="trendChartData"
                            :options="lineChartOptions" />
                        <div v-else class="h-full flex items-center justify-center text-gray-500">
                            No transaction trends data available
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Transactions & Top Donors -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Recent Transactions -->
                <div class="bg-[#FAFAFA] rounded-xl shadow">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold">Recent Transactions</h3>
                            <Link href="/transactions" class="text-sm text-blue-600 hover:text-blue-800">
                            View All
                            </Link>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">TXN ID
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-[#FAFAFA] divide-y divide-gray-200">
                                    <tr v-for="txn in recentTransactions" :key="txn.id">
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ txn.txn_id }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm"
                                            :class="txn.type === 'credit' ? 'text-green-600' : 'text-red-600'">
                                            {{ txn.type === 'credit' ? '+' : '-' }}৳{{ txn.amount }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <span class="px-2 py-1 text-xs rounded-full capitalize" :class="{
                                                'bg-green-100 text-green-800': txn.type === 'credit',
                                                'bg-red-100 text-red-800': txn.type === 'debit'
                                            }">
                                                {{ txn.type }}
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Top Donors -->
                <div class="bg-[#FAFAFA] rounded-xl shadow">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Top Donors</h3>
                        <div v-if="topDonors && topDonors.length" class="space-y-4">
                            <div v-for="donor in topDonors" :key="donor.id" class="flex items-center">
                                <div
                                    class="flex-shrink-0 h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                    <span class="text-indigo-600 font-medium">
                                        {{ donor.name.charAt(0).toUpperCase() }}
                                    </span>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-900">{{ donor.name }}</p>
                                    <p class="text-sm text-gray-500">৳{{ donor.transactions_sum_amount }}
                                        donated</p>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-8 text-gray-500">
                            No donor data available
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
