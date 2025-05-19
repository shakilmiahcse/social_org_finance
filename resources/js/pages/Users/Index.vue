<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import EasyDataTable from 'vue3-easy-data-table';
import 'vue3-easy-data-table/dist/style.css';
import { ref, computed } from 'vue';
import Swal from 'sweetalert2';
import { useToast } from 'vue-toastification';
import AddUserModal from './create.vue';
import EditUserModal from './edit.vue';

const toast = useToast();
const searchTerm = ref('');
const addUserModal = ref();
const editUserModal = ref();
const selectedUser = ref(null);
const $refs = ref<Record<string, HTMLElement>>({});

const props = defineProps({
    users: {
        type: Array as () => Array<{
            id: number;
            name: string;
            email: string;
            roles: string[];
            created_at: string;
        }>,
        required: true
    },
    roles: {
        type: Array as () => Array<{
            id: number;
            name: string;
        }>,
        required: true
    },
    availableRoles: {
        type: Array,
        required: true
    }
});

const filteredUsers = computed(() =>
    props.users.filter(user =>
        user.name.toLowerCase().includes(searchTerm.value.toLowerCase()) ||
        user.email.toLowerCase().includes(searchTerm.value.toLowerCase())
    )
);

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'User Management', href: '/users' },
];

const headers = [
    { text: 'Name', value: 'name', sortable: true, class: 'font-bold' },
    { text: 'Email', value: 'email', sortable: true },
    { text: 'Roles', value: 'roles', sortable: false },
    { text: 'Created At', value: 'created_at', sortable: true },
    { text: 'Actions', value: 'actions', sortable: false, width: 120 },
];

const addUser = () => addUserModal.value.open();

const editUser = (user) => {
    selectedUser.value = user;
    editUserModal.value.open();
};

const deleteUser = (id: number, name: string) => {
    Swal.fire({
        title: 'Are you sure?',
        text: `You are about to delete the user "${name}". This action cannot be undone.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!',
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(`/users/${id}`, {
                preserveScroll: true,
                onSuccess: () => {
                    toast.success('User deleted successfully.');
                },
                onError: () => {
                    toast.error('Failed to delete user.');
                }
            });
        }
    });
};

const formatRoles = (roles) => {
    return roles.map(role =>
        `<span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">${role}</span>`
    ).join(' ');
};
const rowsPerPage = ref(20);
const rowsItems = ref([20, 30, 50, 100, 200]);
</script>

<template>
    <Head title="User Management" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4 space-y-4">
            <div class="bg-[#FAFAFA] shadow rounded-xl p-6 space-y-6">
                <div class="flex justify-between items-center">
                    <h1 class="text-2xl font-bold">User Management</h1>
                    <button @click="addUser"
                        class="bg-green-500 hover:bg-green-600 text-white text-sm px-3 py-1.5 rounded transition flex items-center">
                        <font-awesome-icon :icon="['fas', 'plus']" class="mr-1" />
                        Add
                    </button>
                </div>

                <div class="flex justify-end">
                    <input v-model="searchTerm" type="text" placeholder="Search users..."
                        class="border-10 rounded-lg px-3 py-2 w-full sm:w-64 focus:outline-none focus:ring focus:border-blue-100">
                </div>

                <div class="overflow-auto">
                    <EasyDataTable :headers="headers" :items="filteredUsers" header-text-direction="left" :rows-per-page="rowsPerPage" :rows-items="rowsItems" buttons-pagination class="custom-table min-w-[700px]">
                        <template #item-roles="{ roles }">
                            <div v-html="formatRoles(roles)" class="flex flex-wrap gap-1"></div>
                        </template>
                        <template #item-actions="{ id, name, roles }">
                            <div class="flex items-center space-x-3 my-1" v-if="!roles.includes('admin')">
                                <button @click.stop="editUser(filteredUsers.find(u => u.id === id))"
                                    class="w-9 h-9 flex items-center justify-center rounded-full bg-blue-100 text-blue-600 hover:bg-blue-200 hover:text-blue-800"
                                    title="Edit">
                                    <font-awesome-icon :icon="['fas', 'pen-to-square']" />
                                </button>
                                <button @click.stop="deleteUser(id, name)"
                                    class="w-9 h-9 flex items-center justify-center rounded-full bg-red-100 text-red-600 hover:bg-red-200 hover:text-red-800"
                                    title="Delete">
                                    <font-awesome-icon :icon="['fas', 'trash']" />
                                </button>
                            </div>
                        </template>
                    </EasyDataTable>
                </div>
            </div>
        </div>

        <AddUserModal ref="addUserModal" :roles="roles" :available-roles="availableRoles" />
        <EditUserModal ref="editUserModal" :user="selectedUser" :roles="roles" :available-roles="availableRoles" />
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
