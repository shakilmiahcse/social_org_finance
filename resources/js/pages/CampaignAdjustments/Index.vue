<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useToast } from 'vue-toastification';
import EasyDataTable from 'vue3-easy-data-table';
import 'vue3-easy-data-table/dist/style.css';
import Swal from 'sweetalert2';
import { Head, router, Link } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import AppLayout from '@/layouts/AppLayout.vue';

const toast = useToast();
const searchTerm = ref('');
const selectedAdjustment = ref(null);
const $refs = ref<Record<string, HTMLElement>>({});

const props = defineProps({
    adjustments: {
        type: Array as () => Array<{
            id: number;
            adjustment_amount: string;
            note: string;
            fund_name: string;
            fund_type: string;
            createdBy?: { name: string };
            created_at: string;
        }>,
        required: true,
    },
    funds: {
        type: Array,
        required: true
    }
});

const filteredAdjustments = computed(() =>
    props.adjustments.filter(a =>
        a.fund_name.toLowerCase().includes(searchTerm.value.toLowerCase()) ||
        a.note.toLowerCase().includes(searchTerm.value.toLowerCase())
    )
);

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Campaign Adjustments', href: '/adjustments' },
];

const headers = [
    { text: 'Adjustment Amount', value: 'adjustment_amount', sortable: true, class: 'font-bold' },
    { text: 'Fund Name', value: 'fund_name', sortable: true },
    { text: 'Fund Type', value: 'fund_type', sortable: true },
    { text: 'Note', value: 'note', sortable: true },
    { text: 'Created By', value: 'createdBy.name', sortable: true },
    { text: 'Created At', value: 'created_at', sortable: true },
    { text: 'Actions', value: 'actions', sortable: false, width: 120 },
];

const viewAdjustment = (id: number) => {
    const adjustment = props.adjustments.find(a => a.id === id);
    if (adjustment) {
        selectedAdjustment.value = adjustment;
        // Open modal logic for view
    }
};

const deleteAdjustment = (id: number) => {
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
            router.delete(`/adjustments/${id}`, {
                onSuccess: () => {
                    toast.success('Adjustment has been deleted.');
                }
            });
        }
    });
};

const handleClickOutside = (event: MouseEvent) => {
    props.adjustments.forEach(adjustment => {
        const dropdown = $refs.value[`dropdown-${adjustment.id}`];
        if (dropdown && !dropdown.contains(event.target as Node) &&
            !(event.target as Element).closest(`[data-dropdown-button="${adjustment.id}"]`)) {
            dropdown.classList.add('hidden');
        }
    });
};

const toggleDropdown = (id: number) => {
    const dropdown = $refs.value[`dropdown-${id}`];
    if (dropdown) {
        props.adjustments.forEach(a => {
            if (a.id !== id && $refs.value[`dropdown-${a.id}`]) {
                $refs.value[`dropdown-${a.id}`].classList.add('hidden');
            }
        });
        dropdown.classList.toggle('hidden');
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});
</script>

<template>
    <Head title="Campaign Adjustments List" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4 space-y-4">
            <div class="bg-[#FAFAFA] shadow rounded-xl p-6 space-y-6">
                <div class="flex justify-between items-center">
                    <h1 class="text-2xl font-bold">Campaign Adjustments List</h1>
                    <Link href="/adjustments/create"
                            class="bg-green-500 hover:bg-green-600 text-white text-sm px-3 py-1.5 rounded adjustment flex items-center">
                        <font-awesome-icon :icon="['fas', 'plus']" class="mr-1" />
                        Add Adjustment
                    </Link>
                </div>

                <div class="flex justify-between items-center flex-wrap gap-2">
                    <div>
                        <input v-model="searchTerm" type="text" placeholder="Search adjustments..."
                            class="border-10 rounded-lg px-3 py-2 w-full sm:w-64 focus:outline-none focus:ring focus:border-blue-100">
                    </div>
                </div>
                <div class="overflow-auto">
                    <EasyDataTable :headers="headers" :items="filteredAdjustments" header-text-direction="left"
                        rows-per-page="20" :rows-items="[30, 50, 100, 200]" buttons-pagination
                        class="custom-table min-w-[700px]">
                        <template #item-actions="{ id }">
                            <div class="relative inline-block text-left">
                                <button :data-dropdown-button="id"
                                    class="bg-blue-500 hover:bg-blue-700 px-2 text-white rounded"
                                    @click.stop="toggleDropdown(id)">
                                    Action <font-awesome-icon :icon="['fas', 'angle-down']" />
                                </button>
                                <div :ref="el => $refs[`dropdown-${id}`] = el"
                                    class="hidden absolute right-0 z-10 mt-2 w-28 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 divide-y divide-gray-100 origin-top-right">
                                    <div class="py-1">
                                        <button @click.stop="viewAdjustment(id)"
                                            class="flex items-center gap-2 w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <font-awesome-icon :icon="['fas', 'eye']" />
                                            View
                                        </button>
                                        <button @click.stop="deleteAdjustment(id)"
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
    </AppLayout>
</template>

<style scoped>
.custom-table {
    --easy-table-footer-background-color: #dfe0e1;
    --easy-table-footer-font-size: 14px;
    --easy-table-header-background-color: #f1f3f5;
    --easy-table-header-font-size: 14px;
    --easy-table-header-font-color: #495057;
}
</style>
