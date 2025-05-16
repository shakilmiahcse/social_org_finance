<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import EasyDataTable from 'vue3-easy-data-table';
import 'vue3-easy-data-table/dist/style.css';
import { ref, computed } from 'vue';
import Swal from 'sweetalert2';
import { useToast } from 'vue-toastification';
import AddRoleModal from './create.vue';
import EditRoleModal from './edit.vue';

// Refs
const toast = useToast();
const searchTerm = ref('');
const addRoleModal = ref();
const editRoleModal = ref();
const selectedRole = ref(null);
const $refs = ref<Record<string, HTMLElement>>({});

// Props
const props = defineProps({
    roles: {
        type: Array as () => Array<{
            id: number;
            name: string;
            permissions: string[];
            created_at: string;
        }>,
        required: true
    },
    permissions: {
        type: Object,
        required: true
    },
    availablePermissions: {
        type: Array,
        required: true
    }
});

// Computed
const filteredRoles = computed(() =>
    props.roles.filter(role =>
        role.name.toLowerCase().includes(searchTerm.value.toLowerCase())
    )
);

// Constants
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Role & Permission Management', href: '/roles-permissions' },
];

const headers = [
    { text: 'Role Name', value: 'name', sortable: true, class: 'font-bold' },
    { text: 'Created At', value: 'created_at', sortable: true },
    { text: 'Actions', value: 'actions', sortable: false, width: 120 },
];

// Methods
const addRole = () => addRoleModal.value.open();

const editRole = (role) => {
    selectedRole.value = role;
    editRoleModal.value.open();
};

const deleteRole = (id, name) => {
    Swal.fire({
        title: 'Are you sure?',
        text: `You are about to delete the role "${name}". This action cannot be undone.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!',
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(`/roles-permissions/${id}`, {
                preserveScroll: true,
                onSuccess: () => {
                    toast.success('The role has been deleted.');
                },
                onError: (errors) => {
                    Swal.fire({
                        title: 'Error!',
                        text: errors.message || 'Failed to delete role',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        }
    });
};

const formatPermissions = (permissions) => {
    return permissions.map(p => {
        const parts = p.split('.');
        return `<span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">${parts[0]}.${parts[1]}</span>`;
    }).join(' ');
};
const rowsPerPage = ref(20);
const rowsItems = ref([20, 30, 50, 100, 200]);
</script>

<template>
    <Head title="Role & Permission Management" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4 space-y-4">
            <div class="bg-[#FAFAFA] shadow rounded-xl p-6 space-y-6">
                <!-- Header with Add button -->
                <div class="flex justify-between items-center">
                    <h1 class="text-2xl font-bold">Role Management</h1>
                    <button @click="addRole"
                        class="bg-green-500 hover:bg-green-600 text-white text-sm px-3 py-1.5 rounded transition flex items-center">
                        <font-awesome-icon :icon="['fas', 'plus']" class="mr-1" />
                        Add
                    </button>
                </div>

                <!-- Search bar -->
                <div class="flex justify-end">
                    <input v-model="searchTerm" type="text" placeholder="Search roles..."
                        class="border-10 rounded-lg px-3 py-2 w-full sm:w-64 focus:outline-none focus:ring focus:border-blue-100">
                </div>

                <!-- Roles table -->
                <div class="overflow-auto">
                    <EasyDataTable :headers="headers" :items="filteredRoles" header-text-direction="left" :rows-per-page="rowsPerPage" :rows-items="rowsItems" buttons-pagination class="custom-table min-w-[700px]">
                        <template #item-actions="{ id, name }">
                            <div v-if="name !== 'admin'" class="flex items-center space-x-3 my-1">
                                <!-- Edit Icon Button -->
                                <button
                                    @click.stop="editRole(filteredRoles.find(r => r.id === id))"
                                    class="w-9 h-9 flex items-center justify-center rounded-full bg-blue-100 text-blue-600 hover:bg-blue-200 hover:text-blue-800"
                                    title="Edit"
                                >
                                    <font-awesome-icon :icon="['fas', 'pen-to-square']" />
                                </button>

                                <!-- Delete Icon Button -->
                                <button
                                    @click.stop="deleteRole(id, name)"
                                    class="w-9 h-9 flex items-center justify-center rounded-full bg-red-100 text-red-600 hover:bg-red-200 hover:text-red-800"
                                    title="Delete"
                                >
                                    <font-awesome-icon :icon="['fas', 'trash']" />
                                </button>
                            </div>
                        </template>
                    </EasyDataTable>
                </div>
            </div>
        </div>

        <AddRoleModal ref="addRoleModal" :permissions="permissions" :available-permissions="availablePermissions" />
        <EditRoleModal ref="editRoleModal" :role="selectedRole" :permissions="permissions"
            :available-permissions="availablePermissions" />
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
