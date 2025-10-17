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
import EditTransactionModal from './Edit.vue';
import { useToast } from 'vue-toastification';
import ViewTransactionModal from './View.vue';
import ReceiptModal from './ReceiptModal.vue';
import Datepicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';

const toast = useToast();
const page = usePage();
const searchTerm = ref('');
const viewTransactionModal = ref();
const editTransactionModal = ref();
const receiptModal = ref();
const selectedTransaction = ref(null);
const isFilterExpanded = ref(false);
const openDropdownId = ref<number | null>(null);

// Filters
const selectedTypes = ref<string[]>([]);
const selectedStatuses = ref<string[]>([]);
const selectedPaymentMethod = ref('');
const selectedCreatedBy = ref('');
const dateRange = ref<[Date | null, Date | null]>([null, null]);

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
    receiptSettings: Object,
    filters: {
        type: Object,
        default: () => ({}),
    },
});

// Computed properties for filter options
const types = computed(() => {
    return [...new Set(props.transactions.map(d => d.type))].filter(Boolean);
});

const statuses = computed(() => {
    return [...new Set(props.transactions.map(d => d.status))].filter(Boolean);
});

const paymentMethods = computed(() => {
    return [...new Set(props.transactions.map(d => d.payment_method))].filter(Boolean);
});

const createdBys = computed(() => {
    return [...new Set(props.transactions
        .filter(t => t.createdBy?.name)
        .map(t => t.createdBy?.name))] as string[];
});

// Update the watcher to use the new single-value filters
watch([dateRange, selectedTypes, selectedStatuses, selectedPaymentMethod, selectedCreatedBy], () => {
    const params: Record<string, any> = {};

    if (dateRange.value[0] && dateRange.value[1]) {
        params.start_date = dateRange.value[0].toISOString().split('T')[0];
        params.end_date = dateRange.value[1].toISOString().split('T')[0];
    }

    if (selectedTypes.value.length > 0) {
        params.types = selectedTypes.value;
    }

    if (selectedStatuses.value.length > 0) {
        params.statuses = selectedStatuses.value;
    }

    if (selectedPaymentMethod.value) {
        params.payment_method = selectedPaymentMethod.value;
    }

    if (selectedCreatedBy.value) {
        params.created_by = selectedCreatedBy.value;
    }

    router.get('/transactions', params, {
        preserveState: true,
        replace: true,
        preserveScroll: true,
    });
}, { deep: true });

// Update the reset function
const resetFilters = () => {
    dateRange.value = [null, null];
    selectedTypes.value = [];
    selectedStatuses.value = [];
    selectedPaymentMethod.value = '';
    selectedCreatedBy.value = '';
};

// Update the mounted hook to initialize dropdown values
onMounted(() => {
    document.addEventListener('click', handleDocumentClick);

    if (props.filters.types) {
        selectedTypes.value = Array.isArray(props.filters.types) ? props.filters.types : [props.filters.types];
    }
    if (props.filters.statuses) {
        selectedStatuses.value = Array.isArray(props.filters.statuses) ? props.filters.statuses : [props.filters.statuses];
    }
    if (props.filters.payment_method) {
        selectedPaymentMethod.value = props.filters.payment_method;
    }
    if (props.filters.created_by) {
        selectedCreatedBy.value = props.filters.created_by;
    }
    if (props.filters.start_date && props.filters.end_date) {
        dateRange.value = [
            new Date(props.filters.start_date),
            new Date(props.filters.end_date)
        ];
    }
});

const filteredTransactions = computed(() => {
    let result = props.transactions;

    if (searchTerm.value) {
        const term = searchTerm.value.toLowerCase();
        result = result.filter(t =>
            t.txn_id.toLowerCase().includes(term) ||
            (t.donor?.name?.toLowerCase().includes(term)) ||
            (t.fund?.name?.toLowerCase().includes(term))
        );
    }

    return result;
});

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
    openDropdownId.value = null;
    const transaction = props.transactions.find(t => t.id === id);
    if (transaction) {
        selectedTransaction.value = transaction;
        viewTransactionModal.value.open();
    }
};

const editTransaction = (id: number) => {
    openDropdownId.value = null;
    const transaction = props.transactions.find(t => t.id === id);
    if (transaction) {
        selectedTransaction.value = transaction;
        editTransactionModal.value.open();
    }
};

const showReceipt = (id: number) => {
    openDropdownId.value = null;
    const transaction = props.transactions.find(t => t.id === id);
    if (transaction) {
        selectedTransaction.value = transaction;
        receiptModal.value.open();
    }
};

const deleteTransaction = (id: number) => {
    openDropdownId.value = null;
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

const handleDocumentClick = (event: MouseEvent) => {
  const target = event.target as Element;

  // Check if click is on dropdown button
  const dropdownButton = target.closest('[data-dropdown-button]');
  if (dropdownButton) {
    const id = Number(dropdownButton.getAttribute('data-dropdown-button'));
    toggleDropdown(id);
    return;
  }

  // Check if click is inside dropdown menu
  const dropdownMenu = target.closest('[data-dropdown-menu]');
  if (dropdownMenu) {
    return; // Don't close if clicking inside menu
  }

  // Close dropdown if clicking outside
  openDropdownId.value = null;
};

const toggleDropdown = (id: number) => {
  openDropdownId.value = openDropdownId.value === id ? null : id;
};

const isDropdownOpen = (id: number) => {
  return openDropdownId.value === id;
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

onUnmounted(() => {
    document.removeEventListener('click', handleDocumentClick);
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
                    <div class="space-x-2">
                        <Link v-for="link in [
                            { title: 'Income', href: '/incomes/create' },
                            { title: 'Expense', href: '/expenses/create' },
                        ]" :key="link.href" :href="link.href"
                            class="bg-green-500 hover:bg-green-600 text-white text-sm px-3 py-1.5 rounded transition items-center">
                            <font-awesome-icon :icon="['fas', 'plus']" class="mr-1" />
                            {{ link.title }}
                        </Link>
                    </div>
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
                    <div class="flex gap-2">
                        <input v-model="searchTerm" type="text" placeholder="Search transactions..."
                            class="border-10 rounded-lg px-3 py-2 w-full sm:w-64 focus:outline-none focus:ring focus:border-blue-100">
                        <button @click="isFilterExpanded = !isFilterExpanded"
                            class="bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm px-3 py-1.5 rounded transition flex items-center">
                            <font-awesome-icon :icon="['fas', 'filter']" class="mr-1" />
                            Filter
                        </button>
                    </div>
                </div>

                <!-- Filter panel -->
                <div v-if="isFilterExpanded" class="bg-white p-4 rounded-lg shadow border border-gray-200 transition-all duration-300">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-medium text-gray-700">Filter Options</h3>
                        <button @click="resetFilters" class="text-sm text-red-500 hover:text-red-700">
                            Reset All Filters
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
                        <!-- Date Range Filter -->
                        <div>
                            <h4 class="font-semibold mb-2 text-gray-700">Date Range</h4>
                            <Datepicker
                                v-model="dateRange"
                                range
                                :enable-time-picker="false"
                                auto-apply
                                placeholder="Select date range"
                                class="w-full"
                            />
                        </div>

                        <!-- Payment Method Filter -->
                        <div>
                            <h4 class="font-semibold mb-2 text-gray-700">Payment Method</h4>
                            <select
                                v-model="selectedPaymentMethod"
                                class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring focus:border-blue-100"
                            >
                                <option value="">All Payment Methods</option>
                                <option
                                    v-for="method in paymentMethods"
                                    :key="method"
                                    :value="method"
                                >
                                    {{ method }}
                                </option>
                            </select>
                        </div>

                        <!-- Created By Filter -->
                        <div>
                            <h4 class="font-semibold mb-2 text-gray-700">Created By</h4>
                            <select
                                v-model="selectedCreatedBy"
                                class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring focus:border-blue-100"
                            >
                                <option value="">All Creators</option>
                                <option
                                    v-for="creator in createdBys"
                                    :key="creator"
                                    :value="creator"
                                >
                                    {{ creator }}
                                </option>
                            </select>
                        </div>

                        <!-- Type Filter -->
                        <div>
                            <h4 class="font-semibold mb-2 text-gray-700">Type</h4>
                            <div class="space-y-2">
                                <label v-for="type in types" :key="type" class="flex items-center">
                                    <input
                                        type="checkbox"
                                        v-model="selectedTypes"
                                        :value="type"
                                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 h-4 w-4"
                                    >
                                    <span class="ml-2 text-gray-800 capitalize">{{ type }}</span>
                                </label>
                            </div>
                        </div>

                        <!-- Status Filter -->
                        <div>
                            <h4 class="font-semibold mb-2 text-gray-700">Status</h4>
                            <div class="space-y-2">
                                <label v-for="status in statuses" :key="status" class="flex items-center">
                                    <input
                                        type="checkbox"
                                        v-model="selectedStatuses"
                                        :value="status"
                                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 h-4 w-4"
                                    >
                                    <span class="ml-2 text-gray-800 capitalize">{{ status }}</span>
                                </label>
                            </div>
                        </div>
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
                                    class="bg-blue-500 hover:bg-blue-700 px-2 py-1 text-white rounded flex items-center gap-1"
                                    @click.stop="toggleDropdown(id)">
                                    Action <font-awesome-icon :icon="['fas', 'angle-down']" />
                                </button>
                                <div v-show="isDropdownOpen(id)" data-dropdown-menu
                                    class="absolute right-0 z-50 mt-2 w-32 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 divide-y divide-gray-100 origin-top-right">
                                    <div class="py-1">
                                        <button @click="viewTransaction(id)"
                                            class="flex items-center gap-2 w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                            <font-awesome-icon :icon="['fas', 'eye']" class="w-4 h-4" />
                                            View
                                        </button>
                                        <button @click="editTransaction(id)"
                                            class="flex items-center gap-2 w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                            <font-awesome-icon :icon="['fas', 'pen-to-square']" class="w-4 h-4" />
                                            Edit
                                        </button>
                                        <button @click="showReceipt(id)"
                                            class="flex items-center gap-2 w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                            <font-awesome-icon :icon="['fas', 'receipt']" class="w-4 h-4" />
                                            Receipt
                                        </button>
                                        <button @click="deleteTransaction(id)"
                                            class="flex items-center gap-2 w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100 transition-colors">
                                            <font-awesome-icon :icon="['fas', 'trash']" class="w-4 h-4" />
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
        <ReceiptModal ref="receiptModal" :transaction="selectedTransaction" :organization="organization" :receiptSettings="receiptSettings" />
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

.dp__main {
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
}

/* Ensure dropdowns appear above table */
:deep(.easy-data-table) {
  position: relative;
  z-index: 1;
}

:deep([data-dropdown-menu]) {
  z-index: 1000 !important;
}
</style>
