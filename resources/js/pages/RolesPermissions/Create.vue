<script setup lang="ts">
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useToast } from 'vue-toastification';
import Swal from 'sweetalert2';

const toast = useToast();
const isOpen = ref(false);
const props = defineProps({
    permissions: {
        type: Object,
        required: true
    },
    availablePermissions: {
        type: Array,
        required: true
    }
});

const emit = defineEmits(['close']);

const form = useForm({
    name: '',
    permissions: [] as string[]
});

const submit = () => {
    form.post('/roles-permissions', {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Role created successfully');
            closeModal();
        },
        onError: (errors) => {
            Swal.fire({
                title: 'Error!',
                text: Object.values(errors).join('\n'),
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }
    });
};

const closeModal = () => {
    isOpen.value = false;
    form.reset();
    emit('close');
};

defineExpose({
    open: () => isOpen.value = true,
    close: closeModal
});
</script>

<template>
    <div v-if="isOpen" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75" @click="closeModal"></div>
            </div>

            <!-- Modal container -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-5xl sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                        Create New Role
                    </h3>
                    <form @submit.prevent="submit">
                        <div class="mb-4">
                            <label for="role-name" class="block text-sm font-medium text-gray-700">
                                Role Name <span class="text-red-500">*</span>
                            </label>
                            <input v-model="form.name" type="text" id="role-name" required
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                placeholder="Enter role name">
                            <span class="text-xs text-red-500" v-if="form.errors.name">{{ form.errors.name }}</span>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Permissions
                            </label>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <div v-for="(groupPermissions, resource) in permissions" :key="resource"
                                    class="border rounded-lg p-3">
                                    <h4 class="font-medium mb-2 capitalize">{{ resource }}</h4>
                                    <div class="space-y-2">
                                        <label v-for="permission in groupPermissions" :key="permission.id"
                                            class="flex items-center gap-2">
                                            <input type="checkbox" v-model="form.permissions" :value="permission.name"
                                                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                            <span class="text-sm">{{ permission.name.split('.')[1] }}</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <span class="text-xs text-red-500" v-if="form.errors.permissions">{{ form.errors.permissions
                            }}</span>
                        </div>
                    </form>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" @click="submit" :disabled="form.processing"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                        <span v-if="form.processing" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                                </circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                        </span>
                        {{ form.processing ? 'Creating...' : 'Create Role' }}
                    </button>
                    <button type="button" @click="closeModal"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
    </div>
</div></template>
