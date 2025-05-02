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
        <div class="flex items-center justify-center min-h-screen px-4">
            <!-- Background overlay with smoother transition -->
            <div class="absolute inset-0 bg-gray-500 opacity-75" aria-hidden="true"
                @click="closeModal"></div>

            <!-- Modal container with better animation -->
            <div
                class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all duration-300 w-full max-w-md">
                <!-- Header with gradient background -->
                <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-6 py-4">
                    <h3 class="text-xl font-semibold leading-6 text-white">Donor/Raiser Details</h3>
                </div>

                <!-- Content area -->
                <div class="px-6 py-5 space-y-4">
                    <!-- Profile section -->
                    <div class="flex items-center space-x-4">
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
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Phone</p>
                            <p class="mt-1 text-sm text-gray-900">{{ donor.phone || 'Not provided' }}</p>
                        </div>
                        <div v-if="donor.address">
                            <p class="text-sm font-medium text-gray-500">Address</p>
                            <p class="mt-1 text-sm text-gray-900">{{ donor.address }}</p>
                        </div>
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

                <!-- Footer with action button -->
                <div class="bg-gray-50 px-6 py-4 flex justify-end">
                    <button type="button" @click="closeModal"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
