<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/org/Layout.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { useForm, Head } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps<{
    receiptSettings: {
        credit: {
            header: { title: string; subtitle: string; color: string; icon: string };
            body: { watermark_text: string; watermark_color: string; background_color: string; transaction_style: string };
            footer: { message: string; note: string };
            labels: { amount: string; date: string; method: string; donor: string; fund: string; purpose: string };
        };
        debit: {
            header: { title: string; subtitle: string; color: string; icon: string };
            body: { watermark_text: string; watermark_color: string; background_color: string; transaction_style: string };
            footer: { message: string; note: string };
            labels: { amount: string; date: string; method: string; donor: string; fund: string; purpose: string };
        };
    };
    can: {
        view: boolean;
        update: boolean;
    };
}>();

const form = useForm({
    _method: 'PUT',
    receipt: props.receiptSettings
});

const iconOptions = [
    'hand-holding-heart',
    'receipt',
    'donate',
    'hands-helping',
    'gift',
    'building',
    'university',
    'church',
    'money-bill-wave',
    'credit-card',
    'wallet',
    'handshake',
];

const submit = () => {
    form.post(route('receipt.update'), {
        preserveScroll: true,
    });
};

// Preview functionality
const activeTab = ref('credit');
const previewData = {
    credit: {
        type: 'credit',
        txn_id: 'TXN-' + Math.random().toString(36).substring(2, 10).toUpperCase(),
        amount: '1000.00',
        status: 'completed',
        payment_method: 'cash',
        created_at: new Date().toISOString(),
        donor: { name: 'John Doe' },
        fund: { name: 'General Fund' },
        purpose: 'Donation for charity'
    },
    debit: {
        type: 'debit',
        txn_id: 'TXN-' + Math.random().toString(36).substring(2, 10).toUpperCase(),
        amount: '500.00',
        status: 'completed',
        payment_method: 'bank',
        created_at: new Date().toISOString(),
        donor: { name: 'Organization' },
        fund: { name: 'Operations Fund' },
        purpose: 'Office supplies purchase'
    }
};

const formatCurrency = (amount: string) => {
    return '৳ ' + parseFloat(amount).toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
};

const formatDate = (dateString: string) => {
    const options: Intl.DateTimeFormatOptions = {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    };
    return new Date(dateString).toLocaleDateString(undefined, options);
};
</script>

<template>
    <AppLayout>
        <Head v-if="props.can.view" title="Receipt Settings" />
        <SettingsLayout>
            <div class="space-y-6">
                <HeadingSmall title="Receipt Settings" description="Customize the appearance and content of your receipts." />
                <Tabs v-model="activeTab" class="w-full">
                    <TabsList class="grid w-full grid-cols-2">
                        <TabsTrigger value="credit">Donation Receipt</TabsTrigger>
                        <TabsTrigger value="debit">Payment Receipt</TabsTrigger>
                    </TabsList>
                    <form @submit.prevent="submit" class="space-y-6">
                        <TabsContent value="credit" class="space-y-6">
                            <!-- Credit Receipt Settings -->
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <!-- Settings Column -->
                                <div class="space-y-6">
                                    <!-- Header Settings -->
                                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                                        <h3 class="text-lg font-medium mb-4">Header Settings</h3>
                                        <div class="space-y-4">
                                            <div class="grid gap-2">
                                                <Label for="credit-header-title">Title</Label>
                                                <Input id="credit-header-title" v-model="form.receipt.credit.header.title" type="text" />
                                                <InputError :message="form.errors['receipt.credit.header.title']" />
                                            </div>
                                            <div class="grid gap-2">
                                                <Label for="credit-header-subtitle">Subtitle</Label>
                                                <Input id="credit-header-subtitle" v-model="form.receipt.credit.header.subtitle" type="text" />
                                                <InputError :message="form.errors['receipt.credit.header.subtitle']" />
                                            </div>
                                            <div class="grid gap-2">
                                                <Label for="credit-header-color">Header Color</Label>
                                                <div class="flex items-center gap-2">
                                                    <Input id="credit-header-color" v-model="form.receipt.credit.header.color" type="color" class="w-12 h-10 p-1 rounded" />
                                                    <Input v-model="form.receipt.credit.header.color" type="text" class="flex-1" placeholder="#000000" />
                                                </div>
                                                <InputError :message="form.errors['receipt.credit.header.color']" />
                                            </div>
                                            <div class="grid gap-2">
                                                <Label for="credit-header-icon">Icon</Label>
                                                <Select v-model="form.receipt.credit.header.icon">
                                                    <SelectTrigger>
                                                        <SelectValue placeholder="Select icon" />
                                                    </SelectTrigger>
                                                    <SelectContent>
                                                        <SelectItem v-for="icon in iconOptions" :key="icon" :value="icon">
                                                            {{ icon }}
                                                        </SelectItem>
                                                    </SelectContent>
                                                </Select>
                                                <InputError :message="form.errors['receipt.credit.header.icon']" />
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Body Settings -->
                                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                                        <h3 class="text-lg font-medium mb-4">Body Settings</h3>
                                        <div class="space-y-4">
                                            <div class="grid gap-2">
                                                <Label for="credit-watermark-text">Watermark Text</Label>
                                                <Input id="credit-watermark-text" v-model="form.receipt.credit.body.watermark_text" type="text" />
                                                <InputError :message="form.errors['receipt.credit.body.watermark_text']" />
                                            </div>
                                            <div class="grid gap-2">
                                                <Label for="credit-watermark-color">Watermark Color</Label>
                                                <div class="flex items-center gap-2">
                                                    <Input id="credit-watermark-color" v-model="form.receipt.credit.body.watermark_color" type="color" class="w-12 h-10 p-1 rounded" />
                                                    <Input v-model="form.receipt.credit.body.watermark_color" type="text" class="flex-1" placeholder="#000000" />
                                                </div>
                                                <InputError :message="form.errors['receipt.credit.body.watermark_color']" />
                                            </div>
                                            <div class="grid gap-2">
                                                <Label for="credit-background-color">Background Color</Label>
                                                <div class="flex items-center gap-2">
                                                    <Input id="credit-background-color" v-model="form.receipt.credit.body.background_color" type="color" class="w-12 h-10 p-1 rounded" />
                                                    <Input v-model="form.receipt.credit.body.background_color" type="text" class="flex-1" placeholder="#000000" />
                                                </div>
                                                <InputError :message="form.errors['receipt.credit.body.background_color']" />
                                            </div>
                                            <div class="grid gap-2">
                                                <Label for="credit-transaction-style">Transaction Style</Label>
                                                <div class="flex items-center gap-2">
                                                    <Input id="credit-transaction-style" v-model="form.receipt.credit.body.transaction_style" type="color" class="w-12 h-10 p-1 rounded" />
                                                    <Input v-model="form.receipt.credit.body.transaction_style" type="text" class="flex-1" placeholder="#000000" />
                                                </div>
                                                <InputError :message="form.errors['receipt.credit.body.transaction_style']" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Preview Column -->
                                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                                    <h3 class="text-lg font-medium mb-4">Preview</h3>
                                    <div class="relative overflow-hidden p-0" :style="{ backgroundColor: form.receipt.credit.body.background_color }">
                                        <!-- Watermark -->
                                        <div class="absolute -left-20 -top-20 text-9xl font-bold transform -rotate-30 select-none pointer-events-none"
                                            :style="{ color: form.receipt.credit.body.watermark_color, opacity: '0.1' }">
                                            {{ form.receipt.credit.body.watermark_text }}
                                        </div>
                                        <!-- Header -->
                                        <div class="p-6 text-white relative" :style="{ backgroundColor: form.receipt.credit.header.color }">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center">
                                                    <div class="w-14 h-14 bg-[#FAFAFA]/20 rounded-full flex items-center justify-center">
                                                        <font-awesome-icon :icon="['fas', form.receipt.credit.header.icon]" class="text-2xl" />
                                                    </div>
                                                    <div class="ml-4">
                                                        <h1 class="text-2xl font-bold">{{ form.receipt.credit.header.title }}</h1>
                                                        <p class="text-white/80">{{ form.receipt.credit.header.subtitle }}</p>
                                                    </div>
                                                </div>
                                                <div class="text-right">
                                                    <p class="text-white/80 text-sm">Transaction #</p>
                                                    <p class="font-mono font-semibold">{{ previewData.credit.txn_id }}</p>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Body -->
                                        <div class="p-6 relative">
                                            <!-- Organization Info -->
                                            <div class="mb-8 text-center">
                                                <div class="w-20 h-20 mx-auto mb-4 rounded-full border-4 border-gray-100/50 overflow-hidden bg-white flex items-center justify-center">
                                                    <font-awesome-icon :icon="['fas', 'building']" class="text-gray-600 text-3xl" />
                                                </div>
                                                <h2 class="text-xl font-bold text-gray-800">Your Organization</h2>
                                                <p class="text-gray-600">Helping communities grow</p>
                                            </div>
                                            <!-- Transaction Details -->
                                            <div class="rounded-lg p-5 mb-6" :style="{ backgroundColor: form.receipt.credit.body.transaction_style }">
                                                <div class="flex justify-between items-center mb-4 pb-4" :style="{ borderBottomColor: form.receipt.credit.body.transaction_style }">
                                                    <div>
                                                        <p class="text-gray-600 text-sm">{{ form.receipt.credit.labels.amount }}</p>
                                                        <p class="text-3xl font-bold text-gray-800">
                                                            {{ formatCurrency(previewData.credit.amount) }}
                                                        </p>
                                                    </div>
                                                    <div class="px-3 py-2 rounded-lg flex items-center bg-green-200 text-green-600">
                                                        <font-awesome-icon :icon="['fas', 'check-circle']" class="mr-1" />
                                                        <span class="font-semibold capitalize">Completed</span>
                                                    </div>
                                                </div>
                                                <div class="space-y-3">
                                                    <div class="flex justify-between">
                                                        <span class="text-gray-600">{{ form.receipt.credit.labels.date }}</span>
                                                        <span class="font-semibold text-gray-800">{{ formatDate(previewData.credit.created_at) }}</span>
                                                    </div>
                                                    <div class="flex justify-between">
                                                        <span class="text-gray-600">{{ form.receipt.credit.labels.method }}</span>
                                                        <span class="font-semibold text-gray-800 capitalize">{{ previewData.credit.payment_method }}</span>
                                                    </div>
                                                    <div class="flex justify-between">
                                                        <span class="text-gray-600">{{ form.receipt.credit.labels.donor }}</span>
                                                        <span class="font-semibold text-gray-800">{{ previewData.credit.donor.name }}</span>
                                                    </div>
                                                    <div class="flex justify-between">
                                                        <span class="text-gray-600">{{ form.receipt.credit.labels.fund }}</span>
                                                        <span class="font-semibold text-gray-800">{{ previewData.credit.fund.name }}</span>
                                                    </div>
                                                    <div class="flex justify-between">
                                                        <span class="text-gray-600">{{ form.receipt.credit.labels.purpose }}</span>
                                                        <span class="font-semibold text-gray-800">{{ previewData.credit.purpose }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Footer -->
                                            <div class="text-center">
                                                <p class="text-gray-600 text-sm mb-1">
                                                    {{ form.receipt.credit.footer.message }}
                                                </p>
                                                <p class="text-gray-500 text-xs">
                                                    {{ form.receipt.credit.footer.note }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Label Settings -->
                            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                                <h3 class="text-lg font-medium mb-4">Label Settings</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                    <div class="grid gap-2">
                                        <Label for="credit-label-amount">Amount Label</Label>
                                        <Input id="credit-label-amount" v-model="form.receipt.credit.labels.amount" type="text" />
                                        <InputError :message="form.errors['receipt.credit.labels.amount']" />
                                    </div>
                                    <div class="grid gap-2">
                                        <Label for="credit-label-date">Date Label</Label>
                                        <Input id="credit-label-date" v-model="form.receipt.credit.labels.date" type="text" />
                                        <InputError :message="form.errors['receipt.credit.labels.date']" />
                                    </div>
                                    <div class="grid gap-2">
                                        <Label for="credit-label-method">Method Label</Label>
                                        <Input id="credit-label-method" v-model="form.receipt.credit.labels.method" type="text" />
                                        <InputError :message="form.errors['receipt.credit.labels.method']" />
                                    </div>
                                    <div class="grid gap-2">
                                        <Label for="credit-label-donor">Donor Label</Label>
                                        <Input id="credit-label-donor" v-model="form.receipt.credit.labels.donor" type="text" />
                                        <InputError :message="form.errors['receipt.credit.labels.donor']" />
                                    </div>
                                    <div class="grid gap-2">
                                        <Label for="credit-label-fund">Fund Label</Label>
                                        <Input id="credit-label-fund" v-model="form.receipt.credit.labels.fund" type="text" />
                                        <InputError :message="form.errors['receipt.credit.labels.fund']" />
                                    </div>
                                    <div class="grid gap-2">
                                        <Label for="credit-label-purpose">Purpose Label</Label>
                                        <Input id="credit-label-purpose" v-model="form.receipt.credit.labels.purpose" type="text" />
                                        <InputError :message="form.errors['receipt.credit.labels.purpose']" />
                                    </div>
                                </div>
                            </div>
                            <!-- Footer Settings -->
                            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                                <h3 class="text-lg font-medium mb-4">Footer Settings</h3>
                                <div class="space-y-4">
                                    <div class="grid gap-2">
                                        <Label for="credit-footer-message">Footer Message</Label>
                                        <Input id="credit-footer-message" v-model="form.receipt.credit.footer.message" type="text" />
                                        <InputError :message="form.errors['receipt.credit.footer.message']" />
                                    </div>
                                    <div class="grid gap-2">
                                        <Label for="credit-footer-note">Footer Note</Label>
                                        <textarea id="credit-footer-note" v-model="form.receipt.credit.footer.note" rows="3"
                                            class="w-full border rounded px-3 py-2"></textarea>
                                        <InputError :message="form.errors['receipt.credit.footer.note']" />
                                    </div>
                                </div>
                            </div>
                        </TabsContent>
                        <TabsContent value="debit" class="space-y-6">
                            <!-- Debit Receipt Settings -->
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <!-- Settings Column -->
                                <div class="space-y-6">
                                    <!-- Header Settings -->
                                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                                        <h3 class="text-lg font-medium mb-4">Header Settings</h3>
                                        <div class="space-y-4">
                                            <div class="grid gap-2">
                                                <Label for="debit-header-title">Title</Label>
                                                <Input id="debit-header-title" v-model="form.receipt.debit.header.title" type="text" />
                                                <InputError :message="form.errors['receipt.debit.header.title']" />
                                            </div>
                                            <div class="grid gap-2">
                                                <Label for="debit-header-subtitle">Subtitle</Label>
                                                <Input id="debit-header-subtitle" v-model="form.receipt.debit.header.subtitle" type="text" />
                                                <InputError :message="form.errors['receipt.debit.header.subtitle']" />
                                            </div>
                                            <div class="grid gap-2">
                                                <Label for="debit-header-color">Header Color</Label>
                                                <div class="flex items-center gap-2">
                                                    <Input id="debit-header-color" v-model="form.receipt.debit.header.color" type="color" class="w-12 h-10 p-1 rounded" />
                                                    <Input v-model="form.receipt.debit.header.color" type="text" class="flex-1" placeholder="#000000" />
                                                </div>
                                                <InputError :message="form.errors['receipt.debit.header.color']" />
                                            </div>
                                            <div class="grid gap-2">
                                                <Label for="debit-header-icon">Icon</Label>
                                                <Select v-model="form.receipt.debit.header.icon">
                                                    <SelectTrigger>
                                                        <SelectValue placeholder="Select icon" />
                                                    </SelectTrigger>
                                                    <SelectContent>
                                                        <SelectItem v-for="icon in iconOptions" :key="icon" :value="icon">
                                                            {{ icon }}
                                                        </SelectItem>
                                                    </SelectContent>
                                                </Select>
                                                <InputError :message="form.errors['receipt.debit.header.icon']" />
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Body Settings -->
                                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                                        <h3 class="text-lg font-medium mb-4">Body Settings</h3>
                                        <div class="space-y-4">
                                            <div class="grid gap-2">
                                                <Label for="debit-watermark-text">Watermark Text</Label>
                                                <Input id="debit-watermark-text" v-model="form.receipt.debit.body.watermark_text" type="text" />
                                                <InputError :message="form.errors['receipt.debit.body.watermark_text']" />
                                            </div>
                                            <div class="grid gap-2">
                                                <Label for="debit-watermark-color">Watermark Color</Label>
                                                <div class="flex items-center gap-2">
                                                    <Input id="debit-watermark-color" v-model="form.receipt.debit.body.watermark_color" type="color" class="w-12 h-10 p-1 rounded" />
                                                    <Input v-model="form.receipt.debit.body.watermark_color" type="text" class="flex-1" placeholder="#000000" />
                                                </div>
                                                <InputError :message="form.errors['receipt.debit.body.watermark_color']" />
                                            </div>
                                            <div class="grid gap-2">
                                                <Label for="debit-background-color">Background Color</Label>
                                                <div class="flex items-center gap-2">
                                                    <Input id="debit-background-color" v-model="form.receipt.debit.body.background_color" type="color" class="w-12 h-10 p-1 rounded" />
                                                    <Input v-model="form.receipt.debit.body.background_color" type="text" class="flex-1" placeholder="#000000" />
                                                </div>
                                                <InputError :message="form.errors['receipt.debit.body.background_color']" />
                                            </div>
                                            <div class="grid gap-2">
                                                <Label for="debit-transaction-style">Transaction Style</Label>
                                                <div class="flex items-center gap-2">
                                                    <Input id="debit-transaction-style" v-model="form.receipt.debit.body.transaction_style" type="color" class="w-12 h-10 p-1 rounded" />
                                                    <Input v-model="form.receipt.debit.body.transaction_style" type="text" class="flex-1" placeholder="#000000" />
                                                </div>
                                                <InputError :message="form.errors['receipt.debit.body.transaction_style']" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Preview Column -->
                                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                                    <h3 class="text-lg font-medium mb-4">Preview</h3>
                                    <div class="relative overflow-hidden p-0" :style="{ backgroundColor: form.receipt.debit.body.background_color }">
                                        <!-- Watermark -->
                                        <div class="absolute -left-20 -top-20 text-9xl font-bold transform -rotate-30 select-none pointer-events-none"
                                            :style="{ color: form.receipt.debit.body.watermark_color, opacity: '0.1' }">
                                            {{ form.receipt.debit.body.watermark_text }}
                                        </div>
                                        <!-- Header -->
                                        <div class="p-6 text-white relative" :style="{ backgroundColor: form.receipt.debit.header.color }">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center">
                                                    <div class="w-14 h-14 bg-[#FAFAFA]/20 rounded-full flex items-center justify-center">
                                                        <font-awesome-icon :icon="['fas', form.receipt.debit.header.icon]" class="text-2xl" />
                                                    </div>
                                                    <div class="ml-4">
                                                        <h1 class="text-2xl font-bold">{{ form.receipt.debit.header.title }}</h1>
                                                        <p class="text-white/80">{{ form.receipt.debit.header.subtitle }}</p>
                                                    </div>
                                                </div>
                                                <div class="text-right">
                                                    <p class="text-white/80 text-sm">Transaction #</p>
                                                    <p class="font-mono font-semibold">{{ previewData.debit.txn_id }}</p>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Body -->
                                        <div class="p-6 relative">
                                            <!-- Organization Info -->
                                            <div class="mb-8 text-center">
                                                <div class="w-20 h-20 mx-auto mb-4 rounded-full border-4 border-gray-100/50 overflow-hidden bg-white flex items-center justify-center">
                                                    <font-awesome-icon :icon="['fas', 'building']" class="text-gray-600 text-3xl" />
                                                </div>
                                                <h2 class="text-xl font-bold text-gray-800">Your Organization</h2>
                                                <p class="text-gray-600">Helping communities grow</p>
                                            </div>
                                            <!-- Transaction Details -->
                                            <div class="rounded-lg p-5 mb-6" :style="{ backgroundColor: form.receipt.debit.body.transaction_style }">
                                                <div class="flex justify-between items-center mb-4 pb-4" :style="{ borderBottomColor: form.receipt.debit.body.transaction_style }">
                                                    <div>
                                                        <p class="text-gray-600 text-sm">{{ form.receipt.debit.labels.amount }}</p>
                                                        <p class="text-3xl font-bold text-gray-800">
                                                            {{ formatCurrency(previewData.debit.amount) }}
                                                        </p>
                                                    </div>
                                                    <div class="px-3 py-2 rounded-lg flex items-center bg-blue-200 text-blue-600">
                                                        <font-awesome-icon :icon="['fas', 'check-circle']" class="mr-1" />
                                                        <span class="font-semibold capitalize">Completed</span>
                                                    </div>
                                                </div>
                                                <div class="space-y-3">
                                                    <div class="flex justify-between">
                                                        <span class="text-gray-600">{{ form.receipt.debit.labels.date }}</span>
                                                        <span class="font-semibold text-gray-800">{{ formatDate(previewData.debit.created_at) }}</span>
                                                    </div>
                                                    <div class="flex justify-between">
                                                        <span class="text-gray-600">{{ form.receipt.debit.labels.method }}</span>
                                                        <span class="font-semibold text-gray-800 capitalize">{{ previewData.debit.payment_method }}</span>
                                                    </div>
                                                    <div class="flex justify-between">
                                                        <span class="text-gray-600">{{ form.receipt.debit.labels.donor }}</span>
                                                        <span class="font-semibold text-gray-800">{{ previewData.debit.donor.name }}</span>
                                                    </div>
                                                    <div class="flex justify-between">
                                                        <span class="text-gray-600">{{ form.receipt.debit.labels.fund }}</span>
                                                        <span class="font-semibold text-gray-800">{{ previewData.debit.fund.name }}</span>
                                                    </div>
                                                    <div class="flex justify-between">
                                                        <span class="text-gray-600">{{ form.receipt.debit.labels.purpose }}</span>
                                                        <span class="font-semibold text-gray-800">{{ previewData.debit.purpose }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Footer -->
                                            <div class="text-center">
                                                <p class="text-gray-600 text-sm mb-1">
                                                    {{ form.receipt.debit.footer.message }}
                                                </p>
                                                <p class="text-gray-500 text-xs">
                                                    {{ form.receipt.debit.footer.note }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Label Settings -->
                            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                                <h3 class="text-lg font-medium mb-4">Label Settings</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                    <div class="grid gap-2">
                                        <Label for="debit-label-amount">Amount Label</Label>
                                        <Input id="debit-label-amount" v-model="form.receipt.debit.labels.amount" type="text" />
                                        <InputError :message="form.errors['receipt.debit.labels.amount']" />
                                    </div>
                                    <div class="grid gap-2">
                                        <Label for="debit-label-date">Date Label</Label>
                                        <Input id="debit-label-date" v-model="form.receipt.debit.labels.date" type="text" />
                                        <InputError :message="form.errors['receipt.debit.labels.date']" />
                                    </div>
                                    <div class="grid gap-2">
                                        <Label for="debit-label-method">Method Label</Label>
                                        <Input id="debit-label-method" v-model="form.receipt.debit.labels.method" type="text" />
                                        <InputError :message="form.errors['receipt.debit.labels.method']" />
                                    </div>
                                    <div class="grid gap-2">
                                        <Label for="debit-label-donor">Recipient Label</Label>
                                        <Input id="debit-label-donor" v-model="form.receipt.debit.labels.donor" type="text" />
                                        <InputError :message="form.errors['receipt.debit.labels.donor']" />
                                    </div>
                                    <div class="grid gap-2">
                                        <Label for="debit-label-fund">Fund Label</Label>
                                        <Input id="debit-label-fund" v-model="form.receipt.debit.labels.fund" type="text" />
                                        <InputError :message="form.errors['receipt.debit.labels.fund']" />
                                    </div>
                                    <div class="grid gap-2">
                                        <Label for="debit-label-purpose">Purpose Label</Label>
                                        <Input id="debit-label-purpose" v-model="form.receipt.debit.labels.purpose" type="text" />
                                        <InputError :message="form.errors['receipt.debit.labels.purpose']" />
                                    </div>
                                </div>
                            </div>
                            <!-- Footer Settings -->
                            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                                <h3 class="text-lg font-medium mb-4">Footer Settings</h3>
                                <div class="space-y-4">
                                    <div class="grid gap-2">
                                        <Label for="debit-footer-message">Footer Message</Label>
                                        <Input id="debit-footer-message" v-model="form.receipt.debit.footer.message" type="text" />
                                        <InputError :message="form.errors['receipt.debit.footer.message']" />
                                    </div>
                                    <div class="grid gap-2">
                                        <Label for="debit-footer-note">Footer Note</Label>
                                        <textarea id="debit-footer-note" v-model="form.receipt.debit.footer.note" rows="3"
                                            class="w-full border rounded px-3 py-2"></textarea>
                                        <InputError :message="form.errors['receipt.debit.footer.note']" />
                                    </div>
                                </div>
                            </div>
                        </TabsContent>
                        <!-- Submit Button -->
                        <div v-if="props.can.update" class="flex items-center gap-4 justify-center">
                            <Button type="submit" :disabled="form.processing">
                                <span v-if="!form.processing">Save Settings</span>
                                <span v-else>Saving...</span>
                            </Button>
                            <Transition enter-active-class="transition ease-in-out" enter-from-class="opacity-0"
                                leave-active-class="transition ease-in-out" leave-to-class="opacity-0">
                                <p v-show="form.recentlySuccessful" class="text-sm text-green-600">Saved successfully!</p>
                            </Transition>
                        </div>
                    </form>
                </Tabs>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>

<style scoped>
input[type="color"] {
    -webkit-appearance: none;
    border: 1px solid #d1d5db;
    padding: 2px;
    cursor: pointer;
}
input[type="color"]::-webkit-color-swatch-wrapper {
    padding: 0;
}
input[type="color"]::-webkit-color-swatch {
    border: none;
}
</style>