<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import EasyDataTable from 'vue3-easy-data-table';
import 'vue3-easy-data-table/dist/style.css';
import ExcelJS from 'exceljs';
import jsPDF from 'jspdf';
import autoTable from 'jspdf-autotable';
import { saveAs } from 'file-saver';
import { ref, computed, onMounted, onUnmounted } from 'vue';
import Swal from 'sweetalert2';
import AddDonorModal from './create.vue';
import EditDonorModal from './edit.vue';
import { useToast } from 'vue-toastification';
import ViewDonorModal from './view.vue';

// Refs
const toast = useToast();
const searchTerm = ref('');
const addDonorModal = ref();
const viewDonorModal = ref();
const editDonorModal = ref();
const selectedDonor = ref(null);
const $refs = ref<Record<string, HTMLElement>>({});

// Props
const props = defineProps({
    donors: {
        type: Array as () => Array<{
            id: number;
            name: string;
            email: string;
            phone: string;
            blood_group: string;
            createdBy?: { name: string };
            created_at: string;
        }>,
        required: true
    },
    permissions: {
        type: Object as () => {
            view: boolean;
            edit: boolean;
            delete: boolean;
            create: boolean;
        },
        required: true
    }
});

// Computed
const filteredDonors = computed(() =>
    props.donors.filter(d =>
        d.name.toLowerCase().includes(searchTerm.value.toLowerCase())
    )
);

// Constants
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Donors/Raisers', href: '/donors' },
];

const headers = [
    { text: 'Name', value: 'name', sortable: true, class: 'font-bold' },
    { text: 'Email', value: 'email', sortable: true },
    { text: 'Phone', value: 'phone', sortable: true },
    { text: 'Blood Group', value: 'blood_group', sortable: true },
    { text: 'Created By', value: 'createdBy.name', sortable: true },
    { text: 'Created At', value: 'created_at', sortable: true },
    { text: 'Actions', value: 'actions', sortable: false, width: 120 },
];

// Methods
const addDonor = () => addDonorModal.value.open();

// Add view method
const viewDonor = (id: number) => {
    const donor = props.donors.find(d => d.id === id);
    if (donor) {
        selectedDonor.value = donor;
        viewDonorModal.value.open(); // call exposed open() method on the view modal
    }
};

const editDonor = (id: number) => {
    const donor = props.donors.find(d => d.id === id);
    if (donor) {
        selectedDonor.value = donor;
        editDonorModal.value.open();
    }
};

const deleteDonor = (id: number) => {
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
            router.delete(`/donors/${id}`, {
                onSuccess: () => {
                    toast.success('The donor/raiser has been deleted.')
                }
            });
        }
    });
};

const handleClickOutside = (event: MouseEvent) => {
    props.donors.forEach(donor => {
        const dropdown = $refs.value[`dropdown-${donor.id}`];
        if (dropdown && !dropdown.contains(event.target as Node) &&
            !(event.target as Element).closest(`[data-dropdown-button="${donor.id}"]`)) {
            dropdown.classList.add('hidden');
        }
    });
};

const toggleDropdown = (id: number) => {
    const dropdown = $refs.value[`dropdown-${id}`];
    if (dropdown) {
        // Close all other dropdowns first
        props.donors.forEach(d => {
            if (d.id !== id && $refs.value[`dropdown-${d.id}`]) {
                $refs.value[`dropdown-${d.id}`].classList.add('hidden');
            }
        });
        dropdown.classList.toggle('hidden');
    }
};

// Export functions
const exportToExcel = () => {
    const workbook = new ExcelJS.Workbook();
    const worksheet = workbook.addWorksheet('Donors');
    worksheet.columns = [
        { header: 'Name', key: 'name' },
        { header: 'Email', key: 'email' },
        { header: 'Phone', key: 'phone' },
        { header: 'Blood Group', key: 'blood_group' },
    ];
    worksheet.addRows(props.donors);
    workbook.xlsx.writeBuffer().then((buffer) => {
        saveAs(new Blob([buffer]), 'donors.xlsx');
    });
};

const exportToPDF = () => {
    const doc = new jsPDF();
    autoTable(doc, {
        head: [['Name', 'Email', 'Phone', 'Blood Group']],
        body: props.donors.map(d => [d.name, d.email, d.phone, d.blood_group]),
    });
    doc.save('donors.pdf');
};

// Lifecycle hooks
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
    <Head title="Donor/Raiser List" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4 space-y-4">
            <div class="bg-[#FAFAFA] shadow rounded-xl p-6 space-y-6">
                <!-- Header with Add button -->
                <div class="flex justify-between items-center">
                    <h1 class="text-2xl font-bold">Donor List</h1>
                    <button v-if="props.permissions.create" @click="addDonor"
                        class="bg-green-500 hover:bg-green-600 text-white text-sm px-3 py-1.5 rounded transition flex items-center">
                        <font-awesome-icon :icon="['fas', 'plus']" class="mr-1" />
                        Add
                    </button>
                </div>

                <!-- Top bar -->
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
                        <input v-model="searchTerm" type="text" placeholder="Search donors..."
                            class="border-10 rounded-lg px-3 py-2 w-full sm:w-64 focus:outline-none focus:ring focus:border-blue-100">
                    </div>
                </div>

                <!-- Donor table -->
                <div class="overflow-auto">
                    <EasyDataTable :headers="headers" :items="filteredDonors" header-text-direction="left"
                        :rows-per-page="rowsPerPage" :rows-items="rowsItems" buttons-pagination
                        class="custom-table min-w-[700px]">
                        <!-- Blood Group column -->
                        <template #item-blood_group="{ blood_group }">
                            <span v-if="blood_group" class="px-2 py-1 rounded-full text-white text-xs font-semibold bg-red-500">
                                {{ blood_group }}
                            </span>
                        </template>
                        <template #item-actions="{ id, blood_group }">
                            <div class="relative inline-block text-left">
                                <button :data-dropdown-button="id"
                                    class="bg-blue-500 hover:bg-blue-700 px-2 py-1 text-white rounded"
                                    @click.stop="toggleDropdown(id)">
                                    Action <font-awesome-icon :icon="['fas', 'angle-down']" />
                                </button>
                                <div :ref="el => $refs[`dropdown-${id}`] = el"
                                    class="hidden absolute right-0 z-10 mt-2 w-28 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 divide-y divide-gray-100 origin-top-right">
                                    <div class="py-1">
                                        <!-- View Button -->
                                        <button @click.stop="viewDonor(id)"
                                            class="flex items-center gap-2 w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <font-awesome-icon :icon="['fas', 'eye']" />
                                            View
                                        </button>
                                        <button @click.stop="editDonor(id)"
                                            class="flex items-center gap-2 w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <font-awesome-icon :icon="['fas', 'pen-to-square']" />
                                            Edit
                                        </button>
                                        <button @click.stop="deleteDonor(id)"
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

        <AddDonorModal ref="addDonorModal" />
        <ViewDonorModal ref="viewDonorModal" :donor="selectedDonor" />
        <EditDonorModal ref="editDonorModal" :donor="selectedDonor" />
    </AppLayout>
</template>

<style>
/* If you want to style other parts of the table */
.custom-table {
    --easy-table-footer-background-color: #dfe0e1;
    --easy-table-footer-font-size: 14px;

    /* Header styling (optional) */
    --easy-table-header-background-color: #f1f3f5;
    --easy-table-header-font-size: 14px;
    --easy-table-header-font-color: #495057;
}

/* Make the Name column bold */
.custom-table .font-bold {
    font-weight: bold !important;
}
</style>
