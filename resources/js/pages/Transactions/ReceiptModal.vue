<template>
    <div v-if="isOpen" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <!-- Background overlay -->
            <div class="absolute inset-0 bg-gray-500/75 dark:bg-gray-900/75" aria-hidden="true" @click="closeModal"></div>

            <!-- Modal container -->
            <div
                class="relative transform overflow-hidden rounded-xl bg-white dark:bg-gray-800 text-left shadow-xl transition-all w-full max-w-lg mx-auto my-8">
                <!-- Receipt Content -->
                <div id="receipt" class="bg-white dark:bg-gray-800 relative overflow-hidden">
                    <!-- Watermark -->
                    <div
                        class="absolute text-indigo-500/10 dark:text-indigo-400/10 -left-20 -top-20 text-9xl font-bold transform -rotate-30 select-none">
                        {{ transaction.type === 'credit' ? 'RECEIPT' : 'PAYMENT' }}
                    </div>

                    <!-- Header -->
                    <div class="bg-gradient-to-r from-indigo-600 to-purple-700 p-6 text-white relative">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-14 h-14 bg-white/20 rounded-full flex items-center justify-center">
                                    <font-awesome-icon
                                        :icon="['fas', transaction.type === 'credit' ? 'hand-holding-heart' : 'receipt']"
                                        class="text-2xl" />
                                </div>
                                <div class="ml-4">
                                    <h1 class="text-2xl font-bold">{{ transaction.type === 'credit' ? 'Donation Receipt' :
                                        'Payment Receipt' }}</h1>
                                    <p class="text-indigo-100">
                                        {{ transaction.type === 'credit'
                                            ? 'Thank you for your generous support!'
                                            : 'Thank you for your payment!' }}
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-indigo-100 text-sm">Transaction #</p>
                                <p class="font-mono font-semibold">{{ transaction.txn_id }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Body -->
                    <div class="p-6 relative">
                        <!-- Organization Info -->
                        <div class="mb-8 text-center">
                            <div
                                class="w-20 h-20 mx-auto mb-4 rounded-full border-4 border-indigo-100/50 dark:border-indigo-900/50 overflow-hidden bg-white dark:bg-gray-700 flex items-center justify-center">
                                <font-awesome-icon :icon="['fas', 'building']"
                                    class="text-indigo-600 dark:text-indigo-400 text-3xl" />
                            </div>
                            <h2 class="text-xl font-bold text-gray-800 dark:text-white">Your Organization</h2>
                            <p class="text-gray-500 dark:text-gray-300">Building a better tomorrow</p>
                        </div>

                        <!-- Transaction Details -->
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-5 mb-6">
                            <div
                                class="flex justify-between items-center mb-4 pb-4 border-b border-gray-200 dark:border-gray-600">
                                <div>
                                    <p class="text-gray-500 dark:text-gray-300 text-sm">Transaction Amount</p>
                                    <p class="text-3xl font-bold text-gray-800 dark:text-white">
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
                                    <span class="text-gray-600 dark:text-gray-300">Date</span>
                                    <span class="font-semibold text-gray-800 dark:text-white">{{
                                        formatDate(transaction.created_at) }}</span>
                                </div>

                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-300">Payment Method</span>
                                    <span class="font-semibold text-gray-800 dark:text-white capitalize">{{
                                        transaction.payment_method }}</span>
                                </div>

                                <!-- Show Donor for Credit, Raiser for Debit -->
                                <div v-if="transaction.donor && transaction.type === 'credit'" class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-300">Donor Name</span>
                                    <span class="font-semibold text-gray-800 dark:text-white">{{ transaction.donor.name
                                    }}</span>
                                </div>

                                <div v-if="transaction.type === 'debit'" class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-300">Raiser Name</span>
                                    <span class="font-semibold text-gray-800 dark:text-white">
                                        {{ transaction.donor?.name || 'Organization' }}
                                    </span>
                                </div>

                                <div v-if="transaction.fund" class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-300">Fund</span>
                                    <span class="font-semibold text-gray-800 dark:text-white">{{ transaction.fund.name
                                    }}</span>
                                </div>

                                <div v-if="transaction.purpose" class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-300">Purpose</span>
                                    <span class="font-semibold text-gray-800 dark:text-white">{{ transaction.purpose
                                    }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="text-center">
                            <p class="text-gray-400 dark:text-gray-400 text-sm mb-1">
                                {{ transaction.type === 'credit'
                                    ? 'Your contribution helps us continue our important work.'
                                    : 'This payment helps us continue our important work.' }}
                            </p>
                            <p class="text-gray-400 dark:text-gray-400 text-xs">
                                This receipt serves as an official record.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="bg-gray-50 dark:bg-gray-700/30 px-6 py-4 flex flex-wrap justify-center gap-3">
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
                        class="bg-gray-200 hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-800 dark:text-white px-4 py-2 rounded-lg flex items-center transition-colors">
                        <font-awesome-icon :icon="['fas', 'print']" class="mr-2" />
                        Print
                    </button>
                    <button @click="closeModal"
                        class="text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white px-4 py-2 rounded-lg flex items-center transition-colors">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useToast } from 'vue-toastification';
import html2canvas from 'html2canvas';
import type { Transaction } from '@/types';

const toast = useToast();

const props = defineProps<{
    transaction: Transaction;
}>();

const emit = defineEmits(['close']);
const isOpen = ref(false);

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

const formatCurrency = (amount: string) => {
    const numberAmount = parseFloat(amount.replace(/,/g, ''));

    if (isNaN(numberAmount)) {
        return 'Invalid amount';
    }

    const formatted = numberAmount.toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });

    // Use the Unicode Taka symbol directly
    return `BDT ${formatted}`;
};

const statusColorClasses = (status: string) => {
    const statusMap: Record<string, string> = {
        completed: 'text-green-600 bg-green-100/50 dark:text-green-400 dark:bg-green-900/30',
        pending: 'text-yellow-600 bg-yellow-100/50 dark:text-yellow-400 dark:bg-yellow-900/30',
        canceled: 'text-red-600 bg-red-100/50 dark:text-red-400 dark:bg-red-900/30'
    };
    return statusMap[status.toLowerCase()] || 'text-gray-600 bg-gray-100/50 dark:text-gray-400 dark:bg-gray-900/30';
};

const statusIcon = (status: string) => {
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
    const element = document.getElementById('receipt');
    if (!element) throw new Error('Receipt element not found');

    // Create a clone to avoid modifying the original element
    const clone = element.cloneNode(true) as HTMLElement;
    clone.style.visibility = 'hidden';
    clone.style.position = 'absolute';
    clone.style.left = '-9999px';
    document.body.appendChild(clone);

    try {
        const canvas = await html2canvas(clone, {
            scale: 2,
            useCORS: true,
            backgroundColor: '#ffffff',
            logging: false,
            allowTaint: false,
            scrollX: 0,
            scrollY: 0,
            windowWidth: element.scrollWidth,
            windowHeight: element.scrollHeight
        });
        return canvas;
    } finally {
        document.body.removeChild(clone);
    }
};

const shareReceipt = async () => {
    try {
        const canvas = await generateReceiptImage();

        canvas.toBlob(async (blob) => {
            if (!blob) {
                throw new Error('Failed to create image blob');
            }

            const file = new File([blob], `receipt-${props.transaction.txn_id}.png`, {
                type: 'image/png'
            });

            if (navigator.canShare && navigator.canShare({ files: [file] })) {
                await navigator.share({
                    title: props.transaction.type === 'credit' ? 'Donation Receipt' : 'Payment Receipt',
                    text: props.transaction.type === 'credit'
                        ? `I just donated ${formatCurrency(props.transaction.amount)}`
                        : `Payment of ${formatCurrency(props.transaction.amount)}`,
                    files: [file]
                });
            } else {
                downloadImageFromCanvas(canvas);
                toast.info('Sharing not supported. The receipt has been downloaded instead.');
            }
        }, 'image/png');
    } catch (error) {
        console.error('Error sharing receipt:', error);
        toast.error(`Error sharing receipt: ${error instanceof Error ? error.message : 'Unknown error'}`);
    }
};

const downloadImageFromCanvas = (canvas: HTMLCanvasElement) => {
    const link = document.createElement('a');
    link.download = `${props.transaction.type === 'credit' ? 'donation' : 'payment'}-receipt-${props.transaction.txn_id}.png`;
    link.href = canvas.toDataURL('image/png');
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    toast.success('Receipt downloaded successfully');
};

const downloadReceipt = async () => {
    try {
        const canvas = await generateReceiptImage();
        downloadImageFromCanvas(canvas);
    } catch (error) {
        console.error('Download error:', error);
        toast.error(`Error downloading receipt: ${error instanceof Error ? error.message : 'Unknown error'}`);
    }
};

const printReceipt = async () => {
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
                        @page { size: auto; margin: 0; }
                        body { margin: 0; padding: 0; }
                        img { max-width: 100%; height: auto; display: block; }
                    </style>
                </head>
                <body>
                    <img src="${dataUrl}" />
                    <script>
                        window.onload = function() {
                            setTimeout(function() {
                                window.print();
                                window.close();
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
/* Ensure all colors use standard formats */
.gradient-bg {
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
}

.watermark {
    color: #4f46e5;
    opacity: 0.03;
}

@media print {
    body {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
}</style>
