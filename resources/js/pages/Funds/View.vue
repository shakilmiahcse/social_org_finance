<script setup lang="ts">
import { ref, watch } from 'vue';

const props = defineProps({
    fund: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(['close']);

// Modal visibility flag
const isOpen = ref(false);

// Watch for changes in fund, if needed (optional)
watch(
    () => props.fund,
    (newVal) => {
        // You could perform operations when the fund changes
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
            <div class="absolute inset-0 bg-gray-500 opacity-75" aria-hidden="true" @click="closeModal"></div>

            <!-- Modal container with better animation -->
            <div
                class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all duration-300 w-full max-w-md">
                <!-- Header with gradient background -->
                <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-6 py-4">
                    <h3 class="text-xl font-semibold leading-6 text-white">Fund Details</h3>
                </div>

                <!-- Content area -->
                <div class="px-6 py-5 space-y-4">
                    <!-- Profile section -->
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0 h-12 w-12 rounded-full bg-indigo-100 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-lg font-medium text-gray-900">{{ fund.name }}</h4>
                            <p class="text-sm text-gray-500">{{ fund.type === 'main' ? 'Main Fund' : 'Campaign Fund' }}</p>
                        </div>
                    </div>

                    <!-- Details grid -->
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Amount</p>
                            <p class="mt-1 text-sm text-gray-900">{{ fund.total_amount }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Description</p>
                            <p class="mt-1 text-sm text-gray-900">{{ fund.description || 'No description provided' }}</p>
                        </div>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div v-if="fund.createdBy">
                                <p class="text-sm font-medium text-gray-500">Created By</p>
                                <p class="mt-1 text-sm text-gray-900">{{ fund.createdBy.name }}</p>
                            </div>
                            <div v-if="fund.updatedBy">
                                <p class="text-sm font-medium text-gray-500">Updated By</p>
                                <p class="mt-1 text-sm text-gray-900">{{ fund.updatedBy.name }}</p>
                            </div>
                            <div v-if="fund.created_at">
                                <p class="text-sm font-medium text-gray-500">Created At</p>
                                <p class="mt-1 text-sm text-gray-900">{{ fund.created_at }}</p>
                            </div>
                            <div v-if="fund.updated_at">
                                <p class="text-sm font-medium text-gray-500">Updated At</p>
                                <p class="mt-1 text-sm text-gray-900">{{ fund.updated_at }}</p>
                            </div>
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
