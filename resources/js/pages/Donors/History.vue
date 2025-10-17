<!-- Donors/History.vue (new file) -->
<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import { ref, computed, onMounted } from 'vue';
import { useToast } from 'vue-toastification';
import vSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';
import ExcelJS from 'exceljs';
import jsPDF from 'jspdf';
import autoTable from 'jspdf-autotable';
import { saveAs } from 'file-saver';

const toast = useToast();

// Props
const props = defineProps({
    donors: {
        type: Array as () => Array<{
            id: number;
            name: string;
        }>,
        required: true
    },
    transactions: {
        type: Array as () => Array<{
            id: number;
            txn_no: string;
            type: 'credit' | 'debit';
            amount: number;
            fund?: { name: string };
            purpose: string;
            payment_method: string;
            reference: string;
            status: string;
            created_at: string;
            createdBy?: { name: string };
        }>,
        required: true
    },
    summary: {
        type: Object as () => {
            total_donated: number;
        },
        required: true
    },
    current_donor_id: {
        type: Number,
        required: true
    }
});

// Refs
const selectedDonorId = ref(props.current_donor_id);
const searchTerm = ref('');

// Computed properties
const filteredTransactions = computed(() => {
    if (!searchTerm.value) return props.transactions;

    const term = searchTerm.value.toLowerCase();
    return props.transactions.filter(txn =>
        txn.txn_no.toLowerCase().includes(term) ||
        (txn.fund?.name.toLowerCase().includes(term)) ||
        txn.purpose.toLowerCase().includes(term) ||
        txn.payment_method.toLowerCase().includes(term) ||
        txn.reference.toLowerCase().includes(term)
    );
});

// Methods
const handleDonorChange = (donorId: number) => {
    router.visit(`/donors/${donorId}/history`, {
        preserveState: true,
        preserveScroll: true
    });
};

const exportToExcel = () => {
    const workbook = new ExcelJS.Workbook();
    const worksheet = workbook.addWorksheet('Donor History');

    // Add headers
    worksheet.columns = [
        { header: 'Date', key: 'date', width: 20 },
        { header: 'TXN No', key: 'txn_no', width: 15 },
        { header: 'Type', key: 'type', width: 10 },
        { header: 'Amount', key: 'amount', width: 15 },
        { header: 'Fund', key: 'fund', width: 25 },
        { header: 'Purpose', key: 'purpose', width: 30 },
        { header: 'Payment Method', key: 'payment_method', width: 15 },
        { header: 'Reference', key: 'reference', width: 15 },
        { header: 'Status', key: 'status', width: 10 },
        { header: 'Created By', key: 'created_by', width: 20 }
    ];

    // Add data rows
    props.transactions.forEach(txn => {
        worksheet.addRow({
            date: new Date(txn.created_at).toLocaleString(),
            txn_no: txn.txn_no,
            type: txn.type,
            amount: txn.amount,
            fund: txn.fund?.name || 'N/A',
            purpose: txn.purpose,
            payment_method: txn.payment_method,
            reference: txn.reference,
            status: txn.status,
            created_by: txn.createdBy?.name || 'N/A'
        });
    });

    // Add summary section
    worksheet.addRow([]); // Empty row
    worksheet.addRow(['Summary', '', '', '', '', '', '', '', '']);
    worksheet.addRow(['Total Donated', props.summary.total_donated]);

    // Style summary section
    const summaryRows = [
        worksheet.rowCount - 1
    ];

    summaryRows.forEach(row => {
        worksheet.getRow(row).font = { bold: true };
    });

    // Generate Excel file
    workbook.xlsx.writeBuffer().then(buffer => {
        saveAs(new Blob([buffer]), `donor_history_${props.donors.find(d => d.id === selectedDonorId.value)?.name || 'all'}.xlsx`);
    });
};

const exportToPDF = () => {
    const doc = new jsPDF();
    const donorName = props.donors.find(d => d.id === selectedDonorId.value)?.name || 'All Donors';

    // Add title
    doc.setFontSize(16);
    doc.text(`Donor History - ${donorName}`, 14, 15);

    // Add summary section
    doc.setFontSize(12);
    doc.text('Summary', 14, 25);

    const summaryData = [
        ['Total Donated', props.summary.total_donated]
    ];

    autoTable(doc, {
        startY: 30,
        head: [['Category', 'Amount']],
        body: summaryData,
        styles: { fontSize: 10 },
        headStyles: { fillColor: [41, 128, 185], textColor: 255, fontStyle: 'bold' }
    });

    // Add transactions section
    doc.text('Transaction History', 14, (doc as any).lastAutoTable.finalY + 15);

    const transactionsData = props.transactions.map(txn => [
        new Date(txn.created_at).toLocaleDateString(),
        txn.txn_no,
        txn.type.toUpperCase(),
        txn.amount,
        txn.fund?.name || 'N/A',
        txn.purpose,
        txn.payment_method,
        txn.reference,
        txn.status,
        txn.createdBy?.name || 'N/A'
    ]);

    autoTable(doc, {
        startY: (doc as any).lastAutoTable.finalY + 20,
        head: [
            ['Date', 'TXN No', 'Type', 'Amount', 'Fund', 'Purpose', 'Payment', 'Reference', 'Status', 'Created By']
        ],
        body: transactionsData,
        styles: { fontSize: 8 },
        headStyles: { fillColor: [41, 128, 185], textColor: 255, fontStyle: 'bold' },
        columnStyles: {
            0: { cellWidth: 15 },
            1: { cellWidth: 12 },
            2: { cellWidth: 8 },
            3: { cellWidth: 10 },
            4: { cellWidth: 20 },
            5: { cellWidth: 25 },
            6: { cellWidth: 10 },
            7: { cellWidth: 12 },
            8: { cellWidth: 10 },
            9: { cellWidth: 15 }
        }
    });

    doc.save(`donor_history_${donorName.replace(' ', '_')}.pdf`);
};

// Breadcrumbs
const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    { title: 'Donors/Raisers', href: '/donors' },
    { title: 'History', href: `/donors/${selectedDonorId.value}/history` }
]);

// Format currency
const formatCurrency = (amount: number) => {
    return '৳' + new Intl.NumberFormat('en-US', {
        style: 'decimal',
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(amount);
};
</script>

<template>
    <Head :title="`Donor History - ${donors.find(d => d.id === selectedDonorId)?.name || ''}`" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4 space-y-4">
            <div class="bg-[#FAFAFA] shadow rounded-xl p-6 space-y-6">
                <!-- Header with Donor Selector -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                    <h1 class="text-3xl font-bold">Donor/Raiser History</h1>

                    <div class="w-full md:w-80">
                        <label class="block text-base font-medium text-gray-800 mb-2">Select Donor/Raiser</label>
                        <v-select
                            v-model="selectedDonorId"
                            :options="donors"
                            label="name"
                            :reduce="donor => donor.id"
                            @update:modelValue="handleDonorChange"
                            placeholder="Select a donor/raiser"
                            class="w-full border border-gray-300 rounded-md shadow-sm"
                        ></v-select>
                    </div>
                </div>

                <!-- Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-green-800">Total Donated</h3>
                        <p class="text-2xl font-semibold text-green-600">{{ formatCurrency(summary.total_donated) }}</p>
                    </div>
                </div>

                <!-- Search and Export -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div class="flex gap-2">
                        <button
                            @click="exportToExcel"
                            class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-3 py-2 rounded-md text-sm flex items-center gap-1"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Export Excel
                        </button>
                        <button
                            @click="exportToPDF"
                            class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-3 py-2 rounded-md text-sm flex items-center gap-1"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Export PDF
                        </button>
                    </div>

                    <div class="w-full sm:w-64">
                        <input
                            v-model="searchTerm"
                            type="text"
                            placeholder="Search transactions..."
                            class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        />
                    </div>
                </div>

                <!-- Transaction Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">TXN No</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fund</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Purpose</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created By</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="txn in filteredTransactions" :key="txn.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ new Date(txn.created_at).toLocaleString() }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ txn.txn_no }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span
                                        :class="{
                                            'px-2 py-1 rounded-full text-xs font-semibold': true,
                                            'bg-green-100 text-green-800': txn.type === 'credit',
                                            'bg-red-100 text-red-800': txn.type === 'debit'
                                        }"
                                    >
                                        {{ txn.type.toUpperCase() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm" :class="{
                                    'text-green-600 font-medium': txn.type === 'credit',
                                    'text-red-600 font-medium': txn.type === 'debit'
                                }">
                                    {{ formatCurrency(txn.amount) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ txn.fund?.name || 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ txn.purpose || 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ txn.payment_method || 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ txn.reference || 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span
                                        :class="{
                                            'px-2 py-1 rounded-full text-xs font-semibold': true,
                                            'bg-green-100 text-green-800': txn.status === 'completed',
                                            'bg-yellow-100 text-yellow-800': txn.status === 'pending',
                                            'bg-red-100 text-red-800': txn.status === 'failed'
                                        }"
                                    >
                                        {{ txn.status.toUpperCase() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ txn.createdBy?.name || 'N/A' }}
                                </td>
                            </tr>
                            <tr v-if="filteredTransactions.length === 0">
                                <td colspan="10" class="px-6 py-4 text-center text-sm text-gray-500">
                                    No transactions found
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
