<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import Swal from 'sweetalert2';
import { useToast } from 'vue-toastification';
import { ref } from 'vue';

const toast = useToast();
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Adjustments', href: '/adjustments' },
    { title: 'Create', href: '/adjustments/create' },
];

// Props from backend
defineProps<{
    mainFunds: { id: number, name: string }[];
    campaignFunds: { id: number, name: string }[];
}>();

const form = useForm({
    amount: '',
    type: 'to_campaign',
    main_fund_id: '',
    campaign_fund_id: '',
    note: '',
});

// Submit handler
const submit = () => {
    form.post('/adjustments', {
        onSuccess: () => {
            toast.success('Adjustment created successfully!');
            router.visit('/adjustments');
        },
        onError: (errors) => {
            Swal.fire({
                title: 'Validation Error',
                text: Object.values(errors).join('\n'),
                icon: 'error',
                confirmButtonText: 'OK',
            });
        },
    });
};
</script>

<template>
    <Head title="Create Campaign Adjustment" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <form @submit.prevent="submit" class="space-y-4">
            <div class="p-4 space-y-4">
                <div class="bg-[#FAFAFA] shadow rounded-xl p-6 space-y-6">
                    <h1 class="text-2xl font-bold mb-6">Create Campaign Adjustment</h1>

                    <!-- Three-column grid layout -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-4">
                        <!-- Main Fund -->
                        <div>
                            <label class="block font-semibold mb-1">Main Fund <span class="text-red-500">*</span></label>
                            <select v-model="form.main_fund_id" class="w-full border rounded px-3 py-2"
                                :class="{ 'border-red-500': form.errors.main_fund_id }" required>
                                <option value="">Select Main Fund</option>
                                <option v-for="main in mainFunds" :key="main.id" :value="main.id">
                                    {{ main.name }}
                                </option>
                            </select>
                            <div v-if="form.errors.main_fund_id" class="text-red-500 text-sm">
                                {{ form.errors.main_fund_id }}
                            </div>
                        </div>

                        <!-- Campaign Fund -->
                        <div>
                            <label class="block font-semibold mb-1">Campaign Fund <span
                                    class="text-red-500">*</span></label>
                            <select v-model="form.campaign_fund_id" class="w-full border rounded px-3 py-2"
                                :class="{ 'border-red-500': form.errors.campaign_fund_id }" required>
                                <option value="">Select Campaign Fund</option>
                                <option v-for="campaign in campaignFunds" :key="campaign.id" :value="campaign.id">
                                    {{ campaign.name }}
                                </option>
                            </select>
                            <div v-if="form.errors.campaign_fund_id" class="text-red-500 text-sm">
                                {{ form.errors.campaign_fund_id }}
                            </div>
                        </div>


                        <!-- Type (Smart Radio Buttons) -->
                        <div>
                            <label class="block font-semibold mb-1">Type</label>
                            <div class="flex gap-2">
                                <!-- To Campaign Option -->
                                <label class="inline-flex items-center">
                                    <input type="radio" value="to_campaign" v-model="form.type" class="hidden peer" />
                                    <span class="px-4 py-2 rounded-full border border-gray-300 text-sm font-medium cursor-pointer transition-colors duration-200
                                        peer-checked:bg-gray-800 peer-checked:text-white peer-checked:border-gray-800
                                        hover:bg-gray-100">
                                        To Campaign
                                    </span>
                                </label>
                                <!-- To Main Fund Option -->
                                <label class="inline-flex items-center">
                                    <input type="radio" value="to_main" v-model="form.type" class="hidden peer" />
                                    <span class="px-4 py-2 rounded-full border border-gray-300 text-sm font-medium cursor-pointer transition-colors duration-200
                                        peer-checked:bg-gray-800 peer-checked:text-white peer-checked:border-gray-800
                                        hover:bg-gray-100">
                                        To Main Fund
                                    </span>
                                </label>
                            </div>
                            <div v-if="form.errors.type" class="text-red-500 text-sm">{{ form.errors.type }}</div>
                        </div>

                         <!-- Amount -->
                        <div>
                            <label class="block font-semibold mb-1">Amount <span class="text-red-500">*</span></label>
                            <input v-model="form.amount" type="number" step="0.01" class="w-full border rounded px-3 py-2"
                                :class="{ 'border-red-500': form.errors.amount }" placeholder="Enter amount" required />
                            <div v-if="form.errors.amount" class="text-red-500 text-sm">{{ form.errors.amount }}</div>
                        </div>
                    </div>

                    <!-- Note -->
                    <div>
                        <label class="block font-semibold mb-1">Note</label>
                        <textarea v-model="form.note" class="w-full border rounded px-3 py-2"
                            :class="{ 'border-red-500': form.errors.note }" placeholder="Enter note"></textarea>
                        <div v-if="form.errors.note" class="text-red-500 text-sm">{{ form.errors.note }}</div>
                    </div>
                </div>

                <!-- Submit buttons -->
                <div class="flex justify-between items-center mt-6">
                    <button type="button" @click="$inertia.visit('/adjustments')"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded">
                        <font-awesome-icon :icon="['fas', 'arrow-left']" /> Back
                    </button>
                    <button type="submit" :disabled="form.processing"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded font-semibold">
                        Save Adjustment
                    </button>
                </div>
            </div>
        </form>
    </AppLayout>
</template>
