<script setup lang="ts">
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { useToast } from 'vue-toastification';
import axios from 'axios';

const toast = useToast();
const isOpen = ref(false);
const isLoading = ref(false);
const form = ref({
    name: '',
    email: '',
    phone: '',
    blood_group: '',
    address: '',
});

const emit = defineEmits(['close', 'donor-created']);

const submit = async () => {
    try {
        isLoading.value = true;
        
        // Use axios instead of Inertia's router.post
        const response = await axios.post('/donors', form.value);
        
        if (response.data.success) {
            toast.success(response.data.message || 'Donor added successfully');
            emit('donor-created', response.data.donor);
            closeModal();
            
            // Refresh the parent page to show the new donor
            router.reload({ only: ['donors'] });
        } else {
            throw new Error(response.data.message || 'Failed to create donor');
        }
    } catch (error) {
        if (error.response?.data?.errors) {
            // Handle validation errors
            Swal.fire({
                title: 'Validation Error',
                text: Object.values(error.response.data.errors).join('\n'),
                icon: 'error',
                confirmButtonText: 'OK'
            });
        } else {
            // Handle other errors
            Swal.fire({
                title: 'Error!',
                text: error.response?.data?.message || error.message || 'Something went wrong',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }
    } finally {
        isLoading.value = false;
    }
};

const closeModal = () => {
    isOpen.value = false;
    form.value = { name: '', email: '', phone: '', blood_group: '', address: '' };
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
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                        Add New Donor/Raiser
                    </h3>
                    <form @submit.prevent="submit">
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700">
                                Name <span class="text-red-500">*</span>
                            </label>
                            <input v-model="form.name" type="text" id="name" required
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                placeholder="Enter donor's Name">
                        </div>
                        <div class="mb-4">
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input v-model="form.email" type="email" id="email"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                placeholder="Enter donor's Email">
                        </div>
                        <div class="mb-4">
                            <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                            <input v-model="form.phone" type="tel" id="phone"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                placeholder="Enter donor's Phone">
                        </div>
                        <div class="mb-4">
                            <label for="blood_group" class="block text-sm font-medium text-gray-700">
                                Blood Group
                            </label>
                            <select v-model="form.blood_group" id="blood_group"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 sm:text-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Select Blood Group</option>
                                <option value="A+">A+</option>
                                <option value="A-">A−</option>
                                <option value="B+">B+</option>
                                <option value="B-">B−</option>
                                <option value="AB+">AB+</option>
                                <option value="AB-">AB−</option>
                                <option value="O+">O+</option>
                                <option value="O-">O−</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                            <textarea v-model="form.address" id="address" rows="3"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                placeholder="Enter donor's address"></textarea>
                        </div>
                    </form>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" @click="submit" :disabled="isLoading"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50">
                        <span v-if="isLoading">Saving...</span>
                        <span v-else>Save</span>
                    </button>
                    <button type="button" @click="closeModal" :disabled="isLoading"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>