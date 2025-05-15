<script setup lang="ts">
import { ref, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useToast } from 'vue-toastification';
import Swal from 'sweetalert2';

const toast = useToast();
const isOpen = ref(false);
const props = defineProps({
    user: {
        type: Object as () => {
            id: number;
            name: string;
            email: string;
            roles: string[];
        },
        default: null
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

const emit = defineEmits(['close']);

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    roles: [] as string[]
});

watch(() => props.user, (newUser) => {
    if (newUser) {
        form.name = newUser.name;
        form.email = newUser.email;
        form.roles = [...newUser.roles];
    }
}, { immediate: true });

const submit = () => {
    form.put(`/users/${props.user.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('User updated successfully');
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
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75" @click="closeModal"></div>
            </div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                        Edit User: {{ user?.name }}
                    </h3>
                    <form @submit.prevent="submit">
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700">
                                Name <span class="text-red-500">*</span>
                            </label>
                            <input v-model="form.name" type="text" id="name" required
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <span class="text-xs text-red-500" v-if="form.errors.name">{{ form.errors.name }}</span>
                        </div>

                        <div class="mb-4">
                            <label for="email" class="block text-sm font-medium text-gray-700">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input v-model="form.email" type="email" id="email" required
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <span class="text-xs text-red-500" v-if="form.errors.email">{{ form.errors.email }}</span>
                        </div>

                        <div class="mb-4">
                            <label for="password" class="block text-sm font-medium text-gray-700">
                                Password
                            </label>
                            <input v-model="form.password" type="password" id="password"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <span class="text-xs text-red-500" v-if="form.errors.password">{{ form.errors.password }}</span>
                        </div>

                        <div class="mb-4">
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                                Confirm Password
                            </label>
                            <input v-model="form.password_confirmation" type="password" id="password_confirmation"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Roles
                            </label>
                            <div class="space-y-2">
                                <label v-for="role in roles" :key="role.id" class="flex items-center gap-2">
                                    <input type="checkbox" v-model="form.roles" :value="role.name"
                                        class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    <span class="text-sm">{{ role.name }}</span>
                                </label>
                            </div>
                            <span class="text-xs text-red-500" v-if="form.errors.roles">{{ form.errors.roles }}</span>
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
                        {{ form.processing ? 'Updating...' : 'Update User' }}
                    </button>
                    <button type="button" @click="closeModal"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
</div></template>
