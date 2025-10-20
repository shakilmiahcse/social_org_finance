<template>
    <div v-if="isOpen && transaction" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <!-- Background overlay -->
            <div class="absolute inset-0 bg-gray-500 opacity-75" aria-hidden="true" @click="closeModal"></div>

            <!-- Modal container -->
            <div
                class="relative transform overflow-hidden rounded-xl text-left shadow-xl transition-all w-full max-w-lg mx-auto my-8"
                :style="{ backgroundColor: safeReceiptSettings[safeTransactionType].body.background_color }">
                <!-- Receipt Content -->
                <div id="receipt" ref="receiptElement" :style="{ backgroundColor: safeReceiptSettings[safeTransactionType].body.background_color }">
                    <!-- Watermark -->
                    <div
                        class="absolute -left-20 -top-20 text-9xl font-bold transform -rotate-30 select-none pointer-events-none"
                        :style="{ color: safeReceiptSettings[safeTransactionType].body.watermark_color, opacity: '0.1' }">
                        {{ safeReceiptSettings[safeTransactionType].body.watermark_text }}
                    </div>

                    <!-- Header -->
                    <div class="p-6 text-white relative" :style="{ backgroundColor: safeReceiptSettings[safeTransactionType].header.color }">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-14 h-14 bg-[#FAFAFA]/20 rounded-full flex items-center justify-center">
                                    <font-awesome-icon :icon="['fas', safeReceiptSettings[safeTransactionType].header.icon]" class="text-2xl" />
                                </div>
                                <div class="ml-4">
                                    <h1 class="text-2xl font-bold">{{ safeReceiptSettings[safeTransactionType].header.title }}</h1>
                                    <p class="text-white/80">{{ safeReceiptSettings[safeTransactionType].header.subtitle }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-white/80 text-sm">Transaction #</p>
                                <p class="font-mono font-semibold">{{ transaction.txn_id }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Body -->
                    <div class="p-6 relative">
                        <!-- Organization Info -->
                        <div class="mb-8 text-center">
                            <div
                                class="w-20 h-20 mx-auto mb-4 rounded-full border-4 border-gray-100/50 overflow-hidden bg-white flex items-center justify-center">
                                <img
                                    v-if="safeOrganization.logo_path"
                                    :src="safeOrganization.logo_path"
                                    alt="Logo"
                                    class="object-contain w-full h-full"
                                />
                                <font-awesome-icon
                                    v-else
                                    :icon="['fas', 'building']"
                                    class="text-gray-600 text-3xl"
                                />
                            </div>
                            <h2 class="text-xl font-bold text-gray-800">{{ safeOrganization.name }}</h2>
                            <p class="text-gray-600">{{ safeOrganization.slogan }}</p>
                        </div>

                        <!-- Transaction Details -->
                        <div class="rounded-lg p-5 mb-6" :style="{ backgroundColor: safeReceiptSettings[safeTransactionType].body.transaction_style }">
                            <div
                                class="flex justify-between items-center mb-4 pb-4 border-b"
                                :style="{ borderBottomColor: safeReceiptSettings[safeTransactionType].body.transaction_style }">
                                <div>
                                    <p class="text-gray-600 text-sm">{{ safeReceiptSettings[safeTransactionType].labels.amount }}</p>
                                    <p class="text-3xl font-bold text-gray-800">
                                        {{ formatCurrency(transaction.amount) }}
                                    </p>
                                </div>
                                <div :class="statusColorClasses(transaction.status)"
                                    class="px-3 py-2 rounded-lg flex items-center">
                                    <font-awesome-icon :icon="statusIcon(transaction.status)" class="mr-1" />
                                    <span class="font-semibold capitalize">{{ transaction.status }}</span>
                                </div>
                            </div>

                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">{{ safeReceiptSettings[safeTransactionType].labels.date }}</span>
                                    <span class="font-semibold text-gray-800">{{ formatDate(transaction.created_at) }}</span>
                                </div>

                                <div class="flex justify-between">
                                    <span class="text-gray-600">{{ safeReceiptSettings[safeTransactionType].labels.method }}</span>
                                    <span class="font-semibold text-gray-800 capitalize">{{ transaction.payment_method }}</span>
                                </div>

                                <div v-if="transaction.donor && safeTransactionType === 'credit'" class="flex justify-between">
                                    <span class="text-gray-600">{{ safeReceiptSettings[safeTransactionType].labels.donor }}</span>
                                    <span class="font-semibold text-gray-800">{{ transaction.donor.name }}</span>
                                </div>

                                <div v-if="safeTransactionType === 'debit'" class="flex justify-between">
                                    <span class="text-gray-600">{{ safeReceiptSettings[safeTransactionType].labels.donor }}</span>
                                    <span class="font-semibold text-gray-800">
                                        {{ transaction.donor?.name || 'Organization' }}
                                    </span>
                                </div>

                                <div v-if="transaction.fund" class="flex justify-between">
                                    <span class="text-gray-600">{{ safeReceiptSettings[safeTransactionType].labels.fund }}</span>
                                    <span class="font-semibold text-gray-800">{{ transaction.fund.name }}</span>
                                </div>

                                <div v-if="transaction.purpose" class="flex justify-between">
                                    <span class="text-gray-600">{{ safeReceiptSettings[safeTransactionType].labels.purpose }}</span>
                                    <span class="font-semibold text-gray-800">{{ transaction.purpose }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="text-center">
                            <p class="text-gray-600 text-sm mb-1">{{ safeReceiptSettings[safeTransactionType].footer.message }}</p>
                            <p class="text-gray-500 text-xs">{{ safeReceiptSettings[safeTransactionType].footer.note }}</p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="bg-gray-100 px-6 py-4 flex flex-wrap justify-center gap-3">
                    <button @click="shareReceipt"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors">
                        <font-awesome-icon :icon="['fas', 'share-alt']" class="mr-2" />
                        Share
                    </button>
                    <button @click="downloadReceipt"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors">
                        <font-awesome-icon :icon="['fas', 'download']" class="mr-2" />
                        Download
                    </button>
                    <button @click="printReceipt"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-lg flex items-center transition-colors">
                        <font-awesome-icon :icon="['fas', 'print']" class="mr-2" />
                        Print
                    </button>
                    <button @click="closeModal"
                        class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg flex items-center transition-colors">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { useToast } from 'vue-toastification';
import html2canvas from 'html2canvas';
import type { Transaction } from '@/types';

const toast = useToast();

interface Organization {
    name: string;
    slogan: string;
    logo_path: string;
    currency: string;
}

interface ReceiptSettings {
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
}

const props = defineProps<{
    transaction: Transaction | null;
    organization: Organization | null;
    receiptSettings?: ReceiptSettings;
}>();

const emit = defineEmits(['close']);
const isOpen = ref(false);
const receiptElement = ref<HTMLElement | null>(null);

// Default receipt settings
const defaultReceiptSettings: ReceiptSettings = {
    credit: {
        header: {
            title: 'Donation Receipt',
            subtitle: 'Thank you for your generous support!',
            color: '#16a34a',
            icon: 'hand-holding-heart',
        },
        body: {
            watermark_text: 'RECEIPT',
            watermark_color: '#22c55e',
            background_color: '#f0fdf4',
            transaction_style: '#dcfce7',
        },
        footer: {
            message: 'Your support helps us continue our mission.',
            note: 'This receipt is an official document for your records.',
        },
        labels: {
            amount: 'Donation Amount',
            date: 'Date',
            method: 'Donation Method',
            donor: 'Donor Name',
            fund: 'Fund',
            purpose: 'Purpose',
        },
    },
    debit: {
        header: {
            title: 'Payment Receipt',
            subtitle: 'Thank you for your payment!',
            color: '#2563eb',
            icon: 'receipt',
        },
        body: {
            watermark_text: 'PAYMENT',
            watermark_color: '#3b82f6',
            background_color: '#eff6ff',
            transaction_style: '#dbeafe',
        },
        footer: {
            message: 'This payment supports our welfare activities.',
            note: 'This receipt is an official document for your records.',
        },
        labels: {
            amount: 'Payment Amount',
            date: 'Date',
            method: 'Payment Method',
            donor: 'Recipient Name',
            fund: 'Fund',
            purpose: 'Purpose',
        },
    },
};

// Safe receipt settings with fallback
const safeReceiptSettings = computed(() => {
    return props.receiptSettings || defaultReceiptSettings;
});

// Safe transaction type with fallback
const safeTransactionType = computed(() => {
    if (!props.transaction) return 'credit';
    return props.transaction.type === 'debit' ? 'debit' : 'credit';
});

// Safe organization with fallback
const safeOrganization = computed(() => {
    return props.organization || {
        name: 'Organization',
        slogan: 'Helping communities grow',
        logo_path: '',
        currency: 'BDT'
    };
});

const currencySymbol = computed(() => {
    const currency = safeOrganization.value.currency;
    const currencyMap: { [key: string]: string } = {
        'USD': '$',
        'EUR': '€',
        'BDT': '৳',
        'GBP': '£'
    };
    return currencyMap[currency] || '৳';
});

const formatDate = (dateString: string) => {
    if (!dateString) return 'N/A';

    try {
        const options: Intl.DateTimeFormatOptions = {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        };
        return new Date(dateString).toLocaleDateString('bn-BD', options);
    } catch (error) {
        return 'Invalid Date';
    }
};

const formatCurrency = (amount: string) => {
    if (!amount) return `${currencySymbol.value} 0.00`;

    try {
        const numberAmount = parseFloat(amount.replace(/,/g, ''));

        if (isNaN(numberAmount)) {
            return 'Invalid amount';
        }

        const formatted = numberAmount.toLocaleString('bn-BD', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });

        return `${currencySymbol.value} ${formatted}`;
    } catch (error) {
        return 'Invalid amount';
    }
};

const statusColorClasses = (status: string) => {
    if (!status) return 'text-gray-600 bg-gray-100/50';

    const statusMap: Record<string, string> = {
        completed: 'text-green-600 bg-green-200',
        pending: 'text-yellow-600 bg-yellow-200',
        canceled: 'text-red-600 bg-red-200'
    };
    return statusMap[status.toLowerCase()] || 'text-gray-600 bg-gray-100/50';
};

const statusIcon = (status: string) => {
    if (!status) return ['fas', 'info-circle'];

    const iconMap: Record<string, string[]> = {
        completed: ['fas', 'check-circle'],
        pending: ['fas', 'clock'],
        canceled: ['fas', 'times-circle']
    };
    return iconMap[status.toLowerCase()] || ['fas', 'info-circle'];
};

const closeModal = () => {
    isOpen.value = false;
    emit('close');
};

const generateReceiptImage = async (): Promise<HTMLCanvasElement> => {
    if (!receiptElement.value) {
        throw new Error('Receipt element not found');
    }

    await new Promise(resolve => setTimeout(resolve, 100));

    const originalDarkMode = document.documentElement.classList.contains('dark');
    document.documentElement.classList.remove('dark');

    const container = document.createElement('div');
    container.style.position = 'fixed';
    container.style.left = '-9999px';
    container.style.top = '0';
    container.style.visibility = 'hidden';
    container.style.zIndex = '-9999';
    container.id = 'receipt-capture-container';

    const clone = receiptElement.value.cloneNode(true) as HTMLElement;
    clone.style.position = 'relative';
    clone.style.visibility = 'visible';
    clone.style.width = `${receiptElement.value.offsetWidth}px`;
    clone.id = 'receipt-clone';

    container.appendChild(clone);
    document.body.appendChild(container);

    try {
        const canvas = await html2canvas(clone, {
            scale: 2,
            backgroundColor: safeReceiptSettings.value[safeTransactionType.value].body.background_color,
            logging: false,
            useCORS: true,
            scrollX: 0,
            scrollY: 0,
            windowWidth: clone.scrollWidth,
            windowHeight: clone.scrollHeight,
            ignoreElements: (element) => element.tagName === 'BUTTON',
            onclone: (clonedDoc) => {
                const clonedContainer = clonedDoc.getElementById('receipt-capture-container');
                if (clonedContainer) {
                    clonedContainer.style.visibility = 'hidden';
                    clonedContainer.style.position = 'fixed';
                    clonedContainer.style.left = '-9999px';
                }
            }
        });

        if (originalDarkMode) {
            document.documentElement.classList.add('dark');
        }

        return canvas;
    } finally {
        const container = document.getElementById('receipt-capture-container');
        if (container) {
            document.body.removeChild(container);
        }
    }
};

const shareReceipt = async () => {
    if (!props.transaction) {
        toast.error('No transaction data available');
        return;
    }

    try {
        const canvas = await generateReceiptImage();
        const blob = await new Promise<Blob | null>((resolve) => {
            canvas.toBlob(resolve, 'image/png', 1);
        });

        if (!blob) {
            throw new Error('Failed to create image blob');
        }

        const fileName = `receipt-${props.transaction.txn_id}.png`;
        const file = new File([blob], fileName, { type: 'image/png' });

        const shareData = {
            files: [file],
            title: props.transaction.type === 'credit' ? 'Donation Receipt' : 'Payment Receipt',
            text: props.transaction.type === 'credit'
                ? `I donated ${formatCurrency(props.transaction.amount)} to ${safeOrganization.value.name}`
                : `Payment of ${formatCurrency(props.transaction.amount)} to ${safeOrganization.value.name}`
        };

        if (navigator.canShare && navigator.canShare(shareData)) {
            await navigator.share(shareData);
            return;
        }

        downloadImageFromCanvas(canvas);
        toast.info('Sharing not supported. The receipt has been downloaded instead.');
    } catch (error) {
        console.error('Error sharing receipt:', error);
        toast.error(`Error sharing: ${error instanceof Error ? error.message : 'Unknown error'}`);
    }
};

const downloadImageFromCanvas = (canvas: HTMLCanvasElement) => {
    const fileName = props.transaction ? `receipt-${props.transaction.txn_id}.png` : 'receipt.png';
    const link = document.createElement('a');
    link.download = fileName;
    link.href = canvas.toDataURL('image/png');
    link.click();
};

const downloadReceipt = async () => {
    if (!props.transaction) {
        toast.error('No transaction data available');
        return;
    }

    try {
        const canvas = await generateReceiptImage();
        downloadImageFromCanvas(canvas);
    } catch (error) {
        console.error('Download error:', error);
        toast.error(`Error downloading receipt: ${error instanceof Error ? error.message : 'Unknown error'}`);
    }
};

const printReceipt = async () => {
    if (!props.transaction) {
        toast.error('No transaction data available');
        return;
    }

    try {
        const canvas = await generateReceiptImage();
        const dataUrl = canvas.toDataURL('image/png');

        const printWindow = window.open('', '_blank');
        if (printWindow) {
            printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Print Receipt</title>
                    <style>
                        @page { size: auto; margin: 5mm; }
                        body { margin: 0; padding: 0; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
                        img { max-width: 100%; height: auto; object-fit: contain; }
                    </style>
                </head>
                <body>
                    <img src="${dataUrl}"/>
                    <script>
                        window.onload = function() {
                            setTimeout(function() {
                                window.print();
                                setTimeout(function() { window.close(); }, 100);
                            }, 200);
                        };
                    <\/script>
                </body>
                </html>
            `);
            printWindow.document.close();
        } else {
            throw new Error('Could not open print window. Please allow popups.');
        }
    } catch (error) {
        console.error('Print error:', error);
        toast.error(`Error printing receipt: ${error instanceof Error ? error.message : 'Unknown error'}`);
    }
};

defineExpose({
    open: () => (isOpen.value = true),
    close: closeModal,
});
</script>

<style>
@media print {
    body * {
        visibility: hidden;
    }

    #receipt,
    #receipt * {
        visibility: visible;
    }

    #receipt {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        margin: 0;
        padding: 0;
    }
}
</style>
