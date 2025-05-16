<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, Link, usePage } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import EasyDataTable from 'vue3-easy-data-table';
import 'vue3-easy-data-table/dist/style.css';
import ExcelJS from 'exceljs';
import jsPDF from 'jspdf';
import autoTable from 'jspdf-autotable';
import { saveAs } from 'file-saver';
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import Swal from 'sweetalert2';
import EditTransactionModal from './edit.vue';
import { useToast } from 'vue-toastification';
import ViewTransactionModal from './view.vue';
import ReceiptModal from './ReceiptModal.vue';

const toast = useToast();
const page = usePage();
const searchTerm = ref('');
const viewTransactionModal = ref();
const editTransactionModal = ref();
const receiptModal = ref();
const selectedTransaction = ref(null);
const $refs = ref<Record<string, HTMLElement>>({});

const props = defineProps({
    transactions: {
        type: Array as () => Array<{
            id: number;
            txn_id: string;
            status: string;
            donor?: { name: string };
            fund?: { name: string };
            amount: string;
            type: string;
            purpose: string;
            payment_method: string;
            reference: string;
            note: string;
            createdBy?: { name: string };
            created_at: string;
        }>,
        required: true
    },
    organization: Object,
});


const filteredTransactions = computed(() =>
    props.transactions.filter(t =>
        t.txn_id.toLowerCase().includes(searchTerm.value.toLowerCase()) ||
        (t.donor?.name?.toLowerCase().includes(searchTerm.value.toLowerCase())) ||
        (t.fund?.name?.toLowerCase().includes(searchTerm.value.toLowerCase()))
    )
);

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Transactions', href: '/transactions' },
];

const headers = [
    { text: 'TXN ID', value: 'txn_id', sortable: true, class: 'font-bold' },
    { text: 'Donor/Raiser', value: 'donor.name', sortable: true },
    { text: 'Fund', value: 'fund.name', sortable: true },
    { text: 'Amount', value: 'amount', sortable: true },
    { text: 'Type', value: 'type', sortable: true },
    { text: 'Status', value: 'status', sortable: true },
    { text: 'Payment Method', value: 'payment_method', sortable: true },
    { text: 'Created By', value: 'createdBy.name', sortable: true },
    { text: 'Created At', value: 'created_at', sortable: true },
    { text: 'Actions', value: 'actions', sortable: false, width: 120 },
];

const viewTransaction = (id: number) => {
    const transaction = props.transactions.find(t => t.id === id);
    if (transaction) {
        selectedTransaction.value = transaction;
        viewTransactionModal.value.open();
    }
};

const editTransaction = (id: number) => {
    const transaction = props.transactions.find(t => t.id === id);
    if (transaction) {
        selectedTransaction.value = transaction;
        editTransactionModal.value.open();
    }
};

const showReceipt = (id: number) => {
    const transaction = props.transactions.find(t => t.id === id);
    if (transaction) {
        selectedTransaction.value = transaction;
        receiptModal.value.open();
    }
};

const deleteTransaction = (id: number) => {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!',
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(`/transactions/${id}`, {
                onSuccess: () => {
                    toast.success('The transaction has been deleted.')
                }
            });
        }
    });
};

const handleClickOutside = (event: MouseEvent) => {
    props.transactions.forEach(transaction => {
        const dropdown = $refs.value[`dropdown-${transaction.id}`];
        if (dropdown && !dropdown.contains(event.target as Node) &&
            !(event.target as Element).closest(`[data-dropdown-button="${transaction.id}"]`)) {
            dropdown.classList.add('hidden');
        }
    });
};

const toggleDropdown = (id: number) => {
    const dropdown = $refs.value[`dropdown-${id}`];
    if (dropdown) {
        props.transactions.forEach(t => {
            if (t.id !== id && $refs.value[`dropdown-${t.id}`]) {
                $refs.value[`dropdown-${t.id}`].classList.add('hidden');
            }
        });
        dropdown.classList.toggle('hidden');
    }
};

const exportToExcel = () => {
    const workbook = new ExcelJS.Workbook();
    const worksheet = workbook.addWorksheet('Transactions');
    worksheet.columns = [
        { header: 'TXN ID', key: 'txn_id' },
        { header: 'Donor', key: 'donor.name' },
        { header: 'Fund', key: 'fund.name' },
        { header: 'Amount', key: 'amount' },
        { header: 'Type', key: 'type' },
        { header: 'Status', key: 'status' },
        { header: 'Payment Method', key: 'payment_method' },
    ];
    worksheet.addRows(props.transactions);
    workbook.xlsx.writeBuffer().then((buffer) => {
        saveAs(new Blob([buffer]), 'transactions.xlsx');
    });
};

const exportToPDF = () => {
    const doc = new jsPDF();
    autoTable(doc, {
        head: [['TXN ID', 'Donor', 'Fund', 'Amount', 'Type', 'Status']],
        body: props.transactions.map(t => [
            t.txn_id,
            t.donor?.name || '',
            t.fund?.name || '',
            t.amount,
            t.type,
            t.status
        ]),
    });
    doc.save('transactions.pdf');
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});

const rowsPerPage = ref(20);
const rowsItems = ref([20, 30, 50, 100, 200]);
</script>

<template>
    <Head title="Transaction List" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4 space-y-4">
            <div class="bg-[#FAFAFA] shadow rounded-xl p-6 space-y-6">
                <div class="flex justify-between items-center">
                    <h1 class="text-2xl font-bold">Transaction List</h1>
                    <Link href="/transactions/create"
                        class="bg-green-500 hover:bg-green-600 text-white text-sm px-3 py-1.5 rounded transition flex items-center">
                    <font-awesome-icon :icon="['fas', 'plus']" class="mr-1" />
                    Add
                    </Link>
                </div>

                <div class="flex justify-between items-center flex-wrap gap-2">
                    <div class="flex gap-2">
                        <button @click="exportToExcel"
                            class="bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm px-3 py-1.5 rounded transition">
                            Export Excel
                        </button>
                        <button @click="exportToPDF"
                            class="bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm px-3 py-1.5 rounded transition">
                            Export PDF
                        </button>
                    </div>
                    <div>
                        <input v-model="searchTerm" type="text" placeholder="Search transactions..."
                            class="border-10 rounded-lg px-3 py-2 w-full sm:w-64 focus:outline-none focus:ring focus:border-blue-100">
                    </div>
                </div>
                <div class="overflow-auto">
                    <EasyDataTable :headers="headers" :items="filteredTransactions" header-text-direction="left"
                        :rows-per-page="rowsPerPage" :rows-items="rowsItems" buttons-pagination
                        class="custom-table min-w-[700px]">

                        <template #item-type="{ type }">
                            <span
                                :class="[
                                    'px-2 py-1 rounded-full text-xs font-semibold capitalize',
                                    type === 'credit' ? 'bg-blue-400 text-white' :
                                        type === 'debit' ? 'bg-red-400 text-white' :
                                            'bg-gray-200 text-gray-600'
                                ]">
                                {{ type || 'N/A' }}
                            </span>
                        </template>
                        <template #item-status="{ status }">
                            <span
                                :class="[
                                    'px-2 py-1 rounded-full text-xs font-semibold capitalize',
                                    status === 'completed' ? 'bg-green-200 text-green-700' :
                                        status === 'pending' ? 'bg-orange-200 text-orange-700' :
                                            status === 'canceled' ? 'bg-red-200 text-red-700' :
                                                'bg-gray-200 text-gray-600'
                                ]"
                            >
                                {{ status || 'N/A' }}
                            </span>
                        </template>
                        <template #item-actions="{ id }">
                            <div class="relative inline-block text-left">
                                <button :data-dropdown-button="id"
                                    class="bg-blue-500 hover:bg-blue-700 px-2 py-1 text-white rounded"
                                    @click.stop="toggleDropdown(id)">
                                    Action <font-awesome-icon :icon="['fas', 'angle-down']" />
                                </button>
                                <div :ref="el => $refs[`dropdown-${id}`] = el"
                                    class="hidden absolute right-0 z-10 mt-2 w-28 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 divide-y divide-gray-100 origin-top-right">
                                    <div class="py-1">
                                        <button @click.stop="viewTransaction(id)"
                                            class="flex items-center gap-2 w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <font-awesome-icon :icon="['fas', 'eye']" />
                                            View
                                        </button>
                                        <button @click.stop="editTransaction(id)"
                                            class="flex items-center gap-2 w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <font-awesome-icon :icon="['fas', 'pen-to-square']" />
                                            Edit
                                        </button>
                                        <button @click.stop="showReceipt(id)"
                                            class="flex items-center gap-2 w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <font-awesome-icon :icon="['fas', 'receipt']" />
                                            Receipt
                                        </button>
                                        <button @click.stop="deleteTransaction(id)"
                                            class="flex items-center gap-2 w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                            <font-awesome-icon :icon="['fas', 'trash']" />
                                            Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </EasyDataTable>
                </div>
            </div>
        </div>

        <ViewTransactionModal ref="viewTransactionModal" :transaction="selectedTransaction" />
        <EditTransactionModal ref="editTransactionModal" :transaction="selectedTransaction" />
        <ReceiptModal ref="receiptModal" :transaction="selectedTransaction" :organization="organization"/>
    </AppLayout>
</template>

<style>
.custom-table {
    --easy-table-footer-background-color: #dfe0e1;
    --easy-table-footer-font-size: 14px;
    --easy-table-header-background-color: #f1f3f5;
    --easy-table-header-font-size: 14px;
    --easy-table-header-font-color: #495057;
}

.custom-table .font-bold {
    font-weight: bold !important;
}
</style>
