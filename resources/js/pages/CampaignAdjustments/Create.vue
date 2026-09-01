<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, useForm, Link } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import Swal from 'sweetalert2';
import { useToast } from 'vue-toastification';
import { ref, computed, watch } from 'vue';
import vSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';

const toast = useToast();
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Adjustments', href: '/adjustments' },
    { title: 'Create', href: '/adjustments/create' },
];

const props = defineProps<{
    mainFunds: { id: number, name: string, amount?: number }[];
    campaignFunds: { id: number, name: string, amount: number }[];
}>();

// Form initialization with Main Fund auto-selected
const form = useForm({
    amount: '',
    type: 'to_campaign',
    main_fund_id: props.mainFunds && props.mainFunds.length > 0 ? props.mainFunds[0].id : '',
    campaign_fund_id: '',
    note: '',
});

const selectedMainFund = computed(() => {
    return props.mainFunds.find(f => Number(f.id) === Number(form.main_fund_id));
});

const selectedCampaignFund = computed(() => {
    return props.campaignFunds.find(f => Number(f.id) === Number(form.campaign_fund_id));
});

// Format currency
const formatAmount = (num: number | undefined | null) => {
    if (num === undefined || num === null) return '৳0.00';
    return '৳' + Number(num).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
};

// Auto-fill amount when switching type or selecting campaign fund in 'to_main' mode
watch(() => form.campaign_fund_id, (newVal) => {
    const fund = props.campaignFunds.find(f => f.id === Number(newVal));
    if (form.type === 'to_main' && fund && fund.amount > 0) {
        form.amount = fund.amount.toString();
    }
});

watch(() => form.type, (newType) => {
    if (newType === 'to_main' && selectedCampaignFund.value && selectedCampaignFund.value.amount > 0) {
        form.amount = selectedCampaignFund.value.amount.toString();
    }
});

const fillMaxAmount = () => {
    if (form.type === 'to_main' && selectedCampaignFund.value) {
        form.amount = selectedCampaignFund.value.amount.toString();
    } else if (form.type === 'to_campaign' && selectedMainFund.value) {
        form.amount = (selectedMainFund.value.amount || 0).toString();
    }
};

const submit = () => {
    if (!form.main_fund_id) {
        toast.error('Please select a Main Fund');
        return;
    }
    if (!form.campaign_fund_id) {
        toast.error('Please select a Campaign Fund');
        return;
    }

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

                    <!-- No Main Fund Instruction Banner -->
                    <div v-if="props.mainFunds.length === 0" class="bg-amber-50 border-l-4 border-amber-500 p-4 rounded-md">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 text-amber-500">
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-semibold text-amber-800">No Main Fund Found</h3>
                                <p class="text-sm text-amber-700 mt-1">
                                    Your organization must have at least one <strong>Main Fund</strong> to perform campaign adjustments.
                                    Please navigate to the <Link href="/funds" class="font-bold underline hover:text-amber-900">Funds Page</Link>, click <strong>Add Fund</strong>, and set the Type to <strong>Main</strong>.
                                </p>
                                <div class="mt-2">
                                    <Link href="/funds" class="inline-flex items-center text-xs font-semibold px-3 py-1.5 bg-amber-600 hover:bg-amber-700 text-white rounded transition">
                                        + Go to Funds &amp; Create Main Fund
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-4">
                        <!-- Main Fund (Searchable v-select, Auto-Selected) -->
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="block font-semibold">
                                    Main Fund <span class="text-red-500">*</span>
                                </label>
                                <span v-if="props.mainFunds.length > 0" class="text-xs text-green-600 font-medium">
                                    (Auto-selected)
                                </span>
                            </div>
                            <v-select
                                v-model="form.main_fund_id"
                                :options="props.mainFunds"
                                label="name"
                                :reduce="fund => fund.id"
                                placeholder="Search or select main fund"
                                :clearable="false"
                                :class="['w-full bg-white', { 'border-red-500': form.errors.main_fund_id }]"
                            >
                                <template #option="{ name, amount }">
                                    <div class="flex items-center justify-between py-1">
                                        <span>{{ name }}</span>
                                        <span class="text-gray-500 text-xs ml-2 font-semibold">
                                            (Balance: {{ formatAmount(amount) }})
                                        </span>
                                    </div>
                                </template>
                                <template #selected-option="{ name, amount }">
                                    <div class="flex items-center justify-between w-full">
                                        <span>{{ name }}</span>
                                        <span class="text-gray-500 text-xs ml-2 font-semibold" v-if="amount !== undefined">
                                            (Balance: {{ formatAmount(amount) }})
                                        </span>
                                    </div>
                                </template>
                                <template #no-options>
                                    No main funds found
                                </template>
                            </v-select>
                            <div v-if="selectedMainFund" class="text-xs text-gray-500 mt-1">
                                Current Balance: <span class="font-semibold text-gray-700">{{ formatAmount(selectedMainFund.amount) }}</span>
                            </div>
                            <div v-if="form.errors.main_fund_id" class="text-red-500 text-sm">
                                {{ form.errors.main_fund_id }}
                            </div>
                        </div>

                        <!-- Campaign Fund (Searchable v-select) -->
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="block font-semibold">
                                    Campaign Fund <span class="text-red-500">*</span>
                                </label>
                                <span v-if="selectedCampaignFund" class="text-xs text-gray-500">
                                    Balance: <strong class="text-gray-700">{{ formatAmount(selectedCampaignFund.amount) }}</strong>
                                </span>
                            </div>
                            <v-select
                                v-model="form.campaign_fund_id"
                                :options="props.campaignFunds"
                                label="name"
                                :reduce="fund => fund.id"
                                placeholder="Search or select campaign fund"
                                :class="['w-full bg-white', { 'border-red-500': form.errors.campaign_fund_id }]"
                            >
                                <template #option="{ name, amount }">
                                    <div class="flex items-center justify-between py-1">
                                        <span>{{ name }}</span>
                                        <span class="text-gray-500 text-xs ml-2 font-semibold">
                                            (Balance: {{ formatAmount(amount) }})
                                        </span>
                                    </div>
                                </template>
                                <template #selected-option="{ name, amount }">
                                    <div class="flex items-center justify-between w-full">
                                        <span>{{ name }}</span>
                                        <span class="text-gray-500 text-xs ml-2 font-semibold" v-if="amount !== undefined">
                                            (Balance: {{ formatAmount(amount) }})
                                        </span>
                                    </div>
                                </template>
                                <template #no-options>
                                    No campaign funds found
                                </template>
                            </v-select>
                            <div v-if="props.campaignFunds.length === 0" class="text-xs text-amber-600 mt-1">
                                No active campaign funds found. <Link href="/funds" class="underline">Create a campaign fund</Link>.
                            </div>
                            <div v-if="form.errors.campaign_fund_id" class="text-red-500 text-sm">
                                {{ form.errors.campaign_fund_id }}
                            </div>
                        </div>

                        <!-- Type Radio Buttons with Explanations -->
                        <div>
                            <label class="block font-semibold mb-1">Type</label>
                            <div class="flex gap-2">
                                <label class="inline-flex items-center">
                                    <input type="radio" value="to_campaign" v-model="form.type" class="hidden peer" />
                                    <span class="px-4 py-2 rounded-full border border-gray-300 text-sm font-medium cursor-pointer
                                        transition-colors duration-200 peer-checked:bg-gray-800 peer-checked:text-white
                                        peer-checked:border-gray-800 hover:bg-gray-100">
                                        To Campaign
                                    </span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" value="to_main" v-model="form.type" class="hidden peer" />
                                    <span class="px-4 py-2 rounded-full border border-gray-300 text-sm font-medium cursor-pointer
                                        transition-colors duration-200 peer-checked:bg-gray-800 peer-checked:text-white
                                        peer-checked:border-gray-800 hover:bg-gray-100">
                                        To Main Fund
                                    </span>
                                </label>
                            </div>
                            <!-- Instruction / Helper Text for Type -->
                            <p class="text-xs text-gray-500 mt-2">
                                <span v-if="form.type === 'to_campaign'">
                                    <strong>To Campaign:</strong> Transfers money from Main Fund to Campaign Fund (e.g. allocating extra budget).
                                </span>
                                <span v-else>
                                    <strong>To Main Fund:</strong> Returns surplus / unused funds from Campaign Fund back to Main Fund.
                                </span>
                            </p>
                            <div v-if="form.errors.type" class="text-red-500 text-sm">{{ form.errors.type }}</div>
                        </div>

                        <!-- Amount -->
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="block font-semibold">Amount <span class="text-red-500">*</span></label>
                                <button 
                                    v-if="form.type === 'to_main' && selectedCampaignFund && selectedCampaignFund.amount > 0"
                                    type="button" 
                                    @click="fillMaxAmount"
                                    class="text-xs text-blue-600 hover:text-blue-800 underline font-medium"
                                >
                                    Use Campaign Balance ({{ formatAmount(selectedCampaignFund.amount) }})
                                </button>
                            </div>
                            <input 
                                v-model="form.amount" 
                                type="number" 
                                step="0.01" 
                                min="1"
                                :class="[
                                    'w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500',
                                    form.errors.amount ? 'border-red-500' : ''
                                ]" 
                                placeholder="Enter amount" 
                                required 
                            />
                            <p class="text-xs text-gray-400 mt-1">
                                Auto-filled from campaign fund in "To Main Fund" mode or enter manually.
                            </p>
                            <div v-if="form.errors.amount" class="text-red-500 text-sm">{{ form.errors.amount }}</div>
                        </div>
                    </div>

                    <!-- Note -->
                    <div>
                        <label class="block font-semibold mb-1">Note</label>
                        <textarea 
                            v-model="form.note" 
                            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            :class="{ 'border-red-500': form.errors.note }" 
                            placeholder="Enter adjustment note or purpose"
                        ></textarea>
                        <div v-if="form.errors.note" class="text-red-500 text-sm">{{ form.errors.note }}</div>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex justify-between items-center mt-6">
                    <button 
                        type="button" 
                        @click="$inertia.visit('/adjustments')"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-md transition-colors duration-200"
                    >
                        <font-awesome-icon :icon="['fas', 'arrow-left']" /> Back
                    </button>
                    <button 
                        type="submit" 
                        :disabled="form.processing || props.mainFunds.length === 0"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md font-semibold transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        Save Adjustment
                    </button>
                </div>
            </div>
        </form>
    </AppLayout>
</template>
