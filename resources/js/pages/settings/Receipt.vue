<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/org/Layout.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useForm, Head } from '@inertiajs/vue3';

const props = defineProps({
    receiptSettings: Object,
    can: {
        type: Object as () => {
            view: boolean;
            update: boolean;
        },
        required: true,
    },
});

const form = useForm({
    _method: 'PUT',
    receipt: {
        header: {
            title: props.receiptSettings.header.title,
            subtitle: props.receiptSettings.header.subtitle,
            color: props.receiptSettings.header.color,
            icon: props.receiptSettings.header.icon,
        },
        body: {
            watermark_text: props.receiptSettings.body.watermark_text,
            watermark_color: props.receiptSettings.body.watermark_color,
            background_color: props.receiptSettings.body.background_color,
            transaction_style: props.receiptSettings.body.transaction_style,
        },
        footer: {
            message: props.receiptSettings.footer.message,
            note: props.receiptSettings.footer.note,
        },
        labels: {
            amount: props.receiptSettings.labels.amount,
            date: props.receiptSettings.labels.date,
            method: props.receiptSettings.labels.method,
            donor: props.receiptSettings.labels.donor,
            fund: props.receiptSettings.labels.fund,
            purpose: props.receiptSettings.labels.purpose,
        },
    }
});

const colorOptions = [
    { value: 'bg-gradient-to-r from-green-600 to-emerald-700', label: 'Green Gradient' },
    { value: 'bg-gradient-to-r from-blue-600 to-indigo-700', label: 'Blue Gradient' },
    { value: 'bg-gradient-to-r from-purple-600 to-pink-700', label: 'Purple Gradient' },
    { value: 'bg-gradient-to-r from-red-600 to-orange-700', label: 'Red Gradient' },
];

const iconOptions = [
    'hand-holding-heart',
    'receipt',
    'donate',
    'hands-helping',
    'gift',
    'building',
    'university',
    'church',
];

const submit = () => {
    form.post(route('receipt.update'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
        },
    });
};
</script>

<template>
    <AppLayout>
        <Head v-if="props.can.view" title="Receipt Settings" />
        <SettingsLayout>
            <div class="space-y-6">
                <HeadingSmall title="Receipt Settings" description="Customize the appearance and content of your receipts." />

                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Header Settings -->
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                        <h3 class="text-lg font-medium mb-4">Header Settings</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Title -->
                            <div class="grid gap-2">
                                <Label for="header-title">Title</Label>
                                <Input id="header-title" v-model="form.receipt.header.title" type="text" />
                                <InputError :message="form.errors['receipt.header.title']" />
                            </div>

                            <!-- Subtitle -->
                            <div class="grid gap-2">
                                <Label for="header-subtitle">Subtitle</Label>
                                <Input id="header-subtitle" v-model="form.receipt.header.subtitle" type="text" />
                                <InputError :message="form.errors['receipt.header.subtitle']" />
                            </div>

                            <!-- Replace Select components with native select -->
                            <select v-model="form.receipt.header.color" class="w-full border rounded px-3 py-2">
                                <option v-for="option in colorOptions" :key="option.value" :value="option.value">
                                {{ option.label }}
                                </option>
                            </select>

                            <!-- Icon -->
                            <div class="grid gap-2">
                                <Label for="header-icon">Icon</Label>
                                <select v-model="form.receipt.header.icon" class="w-full border rounded px-3 py-2">
                                    <option v-for="icon in iconOptions" :key="icon" :value="icon">
                                        {{ icon }}
                                    </option>
                                </select>
                                <InputError :message="form.errors['receipt.header.icon']" />
                            </div>
                        </div>
                    </div>

                    <!-- Body Settings -->
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                        <h3 class="text-lg font-medium mb-4">Body Settings</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Watermark Text -->
                            <div class="grid gap-2">
                                <Label for="watermark-text">Watermark Text</Label>
                                <Input id="watermark-text" v-model="form.receipt.body.watermark_text" type="text" />
                                <InputError :message="form.errors['receipt.body.watermark_text']" />
                            </div>

                            <!-- Watermark Color -->
                            <div class="grid gap-2">
                                <Label for="watermark-color">Watermark Color</Label>
                                <Input id="watermark-color" v-model="form.receipt.body.watermark_color" type="text" />
                                <InputError :message="form.errors['receipt.body.watermark_color']" />
                            </div>

                            <!-- Background Color -->
                            <div class="grid gap-2">
                                <Label for="background-color">Background Color</Label>
                                <Input id="background-color" v-model="form.receipt.body.background_color" type="text" />
                                <InputError :message="form.errors['receipt.body.background_color']" />
                            </div>

                            <!-- Transaction Style -->
                            <div class="grid gap-2">
                                <Label for="transaction-style">Transaction Style</Label>
                                <Input id="transaction-style" v-model="form.receipt.body.transaction_style" type="text" />
                                <InputError :message="form.errors['receipt.body.transaction_style']" />
                            </div>
                        </div>
                    </div>

                    <!-- Footer Settings -->
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                        <h3 class="text-lg font-medium mb-4">Footer Settings</h3>
                        <div class="grid grid-cols-1 gap-6">
                            <!-- Message -->
                            <div class="grid gap-2">
                                <Label for="footer-message">Footer Message</Label>
                                <Input id="footer-message" v-model="form.receipt.footer.message" type="text" />
                                <InputError :message="form.errors['receipt.footer.message']" />
                            </div>

                            <!-- Note -->
                            <div class="grid gap-2">
                                <Label for="footer-note">Footer Note</Label>
                                <textarea id="footer-note" v-model="form.receipt.footer.note" rows="3"
                                    class="w-full border rounded px-3 py-2"></textarea>
                                <InputError :message="form.errors['receipt.footer.note']" />
                            </div>
                        </div>
                    </div>

                    <!-- Label Settings -->
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                        <h3 class="text-lg font-medium mb-4">Label Settings</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Amount Label -->
                            <div class="grid gap-2">
                                <Label for="label-amount">Amount Label</Label>
                                <Input id="label-amount" v-model="form.receipt.labels.amount" type="text" />
                                <InputError :message="form.errors['receipt.labels.amount']" />
                            </div>

                            <!-- Date Label -->
                            <div class="grid gap-2">
                                <Label for="label-date">Date Label</Label>
                                <Input id="label-date" v-model="form.receipt.labels.date" type="text" />
                                <InputError :message="form.errors['receipt.labels.date']" />
                            </div>

                            <!-- Method Label -->
                            <div class="grid gap-2">
                                <Label for="label-method">Method Label</Label>
                                <Input id="label-method" v-model="form.receipt.labels.method" type="text" />
                                <InputError :message="form.errors['receipt.labels.method']" />
                            </div>

                            <!-- Donor Label -->
                            <div class="grid gap-2">
                                <Label for="label-donor">Donor Label</Label>
                                <Input id="label-donor" v-model="form.receipt.labels.donor" type="text" />
                                <InputError :message="form.errors['receipt.labels.donor']" />
                            </div>

                            <!-- Fund Label -->
                            <div class="grid gap-2">
                                <Label for="label-fund">Fund Label</Label>
                                <Input id="label-fund" v-model="form.receipt.labels.fund" type="text" />
                                <InputError :message="form.errors['receipt.labels.fund']" />
                            </div>

                            <!-- Purpose Label -->
                            <div class="grid gap-2">
                                <Label for="label-purpose">Purpose Label</Label>
                                <Input id="label-purpose" v-model="form.receipt.labels.purpose" type="text" />
                                <InputError :message="form.errors['receipt.labels.purpose']" />
                            </div>
                        </div>
                    </div>

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
            </div>
        </SettingsLayout>
    </AppLayout>
</template>