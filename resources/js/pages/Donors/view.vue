<script setup lang="ts">
import { ref, watch } from 'vue';

const props = defineProps({
    donor: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(['close']);

// Modal visibility flag
const isOpen = ref(false);

// Watch for changes in donor, if needed (optional)
watch(
    () => props.donor,
    (newVal) => {
        // You could perform operations when the donor changes
    }
);

const closeModal = () => {
    isOpen.value = false;
    emit('close');
};

// Expose functions for the parent to control the modal
defineExpose({
    open: () => (isOpen.value = true),
    close: closeModal,
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
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Donor/Raiser Details</h3>
                    
                    <!-- Profile section -->
                    <div class="flex items-center space-x-4 mb-6">
                        <div class="flex-shrink-0 h-12 w-12 rounded-full bg-indigo-100 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-lg font-medium text-gray-900">{{ donor.name }}</h4>
                            <p class="text-sm text-gray-500">{{ donor.email }}</p>
                        </div>
                    </div>

                    <!-- Details grid -->
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Phone</p>
                            <p class="mt-1 text-sm text-gray-900">{{ donor.phone || 'Not provided' }}</p>
                        </div>
                        
                        <div v-if="donor.blood_group">
                            <p class="text-sm font-medium text-gray-500">Blood Group</p>
                            <p class="mt-1 text-sm">
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold text-white bg-red-500">
                                    {{ donor.blood_group }}
                                </span>
                            </p>
                        </div>
                        
                        <div v-if="donor.address">
                            <p class="text-sm font-medium text-gray-500">Address</p>
                            <p class="mt-1 text-sm text-gray-900">{{ donor.address }}</p>
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div v-if="donor.createdBy">
                                <p class="text-sm font-medium text-gray-500">Created By</p>
                                <p class="mt-1 text-sm text-gray-900">{{ donor.createdBy.name }}</p>
                            </div>
                            <div v-if="donor.updatedBy">
                                <p class="text-sm font-medium text-gray-500">Updated By</p>
                                <p class="mt-1 text-sm text-gray-900">{{ donor.updatedBy.name }}</p>
                            </div>
                            <div v-if="donor.created_at">
                                <p class="text-sm font-medium text-gray-500">Created At</p>
                                <p class="mt-1 text-sm text-gray-900">{{ donor.created_at }}</p>
                            </div>
                            <div v-if="donor.updated_at">
                                <p class="text-sm font-medium text-gray-500">Updated At</p>
                                <p class="mt-1 text-sm text-gray-900">{{ donor.updated_at }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer with action button -->
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" @click="closeModal"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>