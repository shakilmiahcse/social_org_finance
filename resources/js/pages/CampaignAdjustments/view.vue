<script setup lang="ts">
import { ref, watch } from 'vue';

const props = defineProps({
    adjustment: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(['close']);
const isOpen = ref(false);

watch(
    () => props.adjustment,
    (newAdjustment) => {
        // Optional logic on adjustment update
    }
);

const closeModal = () => {
    isOpen.value = false;
    emit('close');
};

defineExpose({
    open: () => (isOpen.value = true),
    close: closeModal,
});
</script>

<template>
    <div v-if="isOpen" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <!-- Background overlay -->
            <div class="absolute inset-0 bg-gray-500 opacity-75" aria-hidden="true" @click="closeModal"></div>

            <!-- Modal box -->
            <div
                class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all duration-300 w-full max-w-lg">

                <!-- Header -->
                <div class="bg-gradient-to-r from-emerald-600 to-teal-700 px-6 py-4">
                    <h3 class="text-xl font-semibold leading-6 text-white">
                        Adjustment Details <small>(ID: {{ adjustment.id }})</small>
                    </h3>
                </div>

                <!-- Main content -->
                <div class="px-6 py-5 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Adjustment Amount</p>
                            <p class="mt-1 text-sm text-gray-900">{{ adjustment.adjustment_amount }}</p>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-gray-500">Type</p>
                            <p class="mt-1 text-xs font-semibold px-2 py-1 rounded-full capitalize" :class="[
                                adjustment.type === 'to_main' ? 'bg-red-100 text-red-700' :
                                    adjustment.type === 'to_campaign' ? 'bg-green-100 text-green-700' :
                                        'bg-gray-100 text-gray-700'
                            ]">
                                {{ (adjustment.type || 'N/A').replace(/_/g, ' ') }}
                            </p>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-gray-500">Campaign Fund</p>
                            <p class="mt-1 text-sm text-gray-900">{{ adjustment.campaign_fund_name || 'N/A' }}</p>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-gray-500">Main Fund</p>
                            <p class="mt-1 text-sm text-gray-900">{{ adjustment.main_name || 'N/A' }}</p>
                        </div>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-gray-500">Note</p>
                        <p class="mt-1 text-sm text-gray-900">{{ adjustment.note || 'N/A' }}</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div v-if="adjustment.createdBy">
                            <p class="text-sm font-medium text-gray-500">Created By</p>
                            <p class="mt-1 text-sm text-gray-900">{{ adjustment.createdBy.name }}</p>
                        </div>

                        <div v-if="adjustment.updatedBy">
                            <p class="text-sm font-medium text-gray-500">Updated By</p>
                            <p class="mt-1 text-sm text-gray-900">{{ adjustment.updatedBy.name }}</p>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-gray-500">Created At</p>
                            <p class="mt-1 text-sm text-gray-900">{{ adjustment.created_at }}</p>
                        </div>

                        <div v-if="adjustment.updated_at">
                            <p class="text-sm font-medium text-gray-500">Updated At</p>
                            <p class="mt-1 text-sm text-gray-900">{{ adjustment.updated_at }}</p>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="bg-gray-50 px-6 py-4 flex justify-end">
                    <button type="button" @click="closeModal"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
