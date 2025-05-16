<template>
    <div v-if="isOpen" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <!-- Background overlay -->
            <div class="absolute inset-0 bg-gray-500/75 dark:bg-gray-900/75" aria-hidden="true" @click="closeModal"></div>

            <!-- Modal container -->
            <div
                class="relative transform overflow-hidden rounded-xl bg-white dark:bg-gray-800 text-left shadow-xl transition-all w-full max-w-lg mx-auto my-8">
                <!-- Receipt Content -->
                <div id="receipt" ref="receiptElement" class="bg-white dark:bg-gray-800 relative overflow-hidden p-0">
                    <!-- Watermark -->
                    <div
                        class="absolute text-indigo-500/10 dark:text-indigo-400/10 -left-20 -top-20 text-9xl font-bold transform -rotate-30 select-none pointer-events-none">
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
                                    <h1 class="text-2xl font-bold">{{ transaction.type === 'credit' ? 'অনুদানের রসিদ' :
                                        'পেমেন্ট রসিদ' }}</h1>
                                    <p class="text-indigo-100">
                                        {{ transaction.type === 'credit'
                                            ? 'আপনার উদার সহায়তার জন্য ধন্যবাদ!'
                                            : 'আপনার পেমেন্টের জন্য ধন্যবাদ!' }}
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
                                <img
                                v-if="organization.logo_path"
                                :src="organization.logo_path"
                                alt="Organization Logo"
                                class="object-contain w-full h-full"
                                />
                                <font-awesome-icon
                                v-else
                                :icon="['fas', 'building']"
                                class="text-indigo-600 dark:text-indigo-400 text-3xl"
                                />
                            </div>
                            <h2 class="text-xl font-bold text-gray-800 dark:text-white">{{ organization.name }}</h2>
                            <p class="text-gray-500 dark:text-gray-300">{{ organization.slogan }}</p>
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
                                    <span class="text-gray-600 dark:text-gray-300">তারিখ</span>
                                    <span class="font-semibold text-gray-800 dark:text-white">{{
                                        formatDate(transaction.created_at) }}</span>
                                </div>

                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-300">{{ transaction.type === 'credit' ? 'অনুদান মাধ্যম' :
                                        'পেমেন্ট মাধ্যম' }}</span>
                                    <span class="font-semibold text-gray-800 dark:text-white capitalize">{{
                                        transaction.payment_method }}</span>
                                </div>

                                <!-- Show Donor for Credit, Raiser for Debit -->
                                <div v-if="transaction.donor && transaction.type === 'credit'" class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-300">দাতার নাম</span>
                                    <span class="font-semibold text-gray-800 dark:text-white">{{ transaction.donor.name
                                    }}</span>
                                </div>

                                <div v-if="transaction.type === 'debit'" class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-300">উত্তোলনকারীর নাম</span>
                                    <span class="font-semibold text-gray-800 dark:text-white">
                                        {{ transaction.donor?.name || 'Organization' }}
                                    </span>
                                </div>

                                <div v-if="transaction.fund" class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-300">তহবিল</span>
                                    <span class="font-semibold text-gray-800 dark:text-white">{{ transaction.fund.name
                                    }}</span>
                                </div>

                                <div v-if="transaction.purpose" class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-300">উদ্দেশ্য</span>
                                    <span class="font-semibold text-gray-800 dark:text-white">{{ transaction.purpose
                                    }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="text-center">
                            <p class="text-gray-400 dark:text-gray-400 text-sm mb-1">
                                {{ transaction.type === 'credit'
                                    ? 'আপনার সহযোগিতা আমাদের কাজ অব্যাহত রাখার অনুপ্রেরণা জোগায়।'
                                    : 'এই অর্থায়ন কল্যাণমূলক কার্যক্রমের উন্নয়নে ব্যবহৃত হয়েছে।' }}
                            </p>
                            <p class="text-gray-400 dark:text-gray-400 text-xs">
                                এই রসিদটি সংরক্ষণের জন্য একটি অফিসিয়াল ডকুমেন্ট।
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
                        class="bg-red-500 text-white dark:text-gray-300 hover:text-gray-800 dark:hover:text-white px-4 py-2 rounded-lg flex items-center transition-colors">
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

interface Organization {
    name: string;
    slogan: string;
    logo_path: string;
}

const props = defineProps<{
    transaction: Transaction;
    organization: Organization;
}>();

const emit = defineEmits(['close']);
const isOpen = ref(false);
const receiptElement = ref<HTMLElement | null>(null);

// Check if Web Share API is available
const isWebShareSupported = () => {
    return navigator.share && navigator.canShare;
};

// Check if Web Share API with files is supported
const isFileShareSupported = () => {
    if (!navigator.canShare) return false;

    try {
        return navigator.canShare({
            files: [new File([''], 'test.png', { type: 'image/png' })],
            title: 'Test',
            text: 'Test'
        });
    } catch {
        return false;
    }
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

const formatCurrency = (amount: string) => {
    const numberAmount = parseFloat(amount.replace(/,/g, ''));

    if (isNaN(numberAmount)) {
        return 'Invalid amount';
    }

    const formatted = numberAmount.toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });

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
    if (!receiptElement.value) {
        throw new Error('Receipt element not found');
    }

    // Add a small delay to ensure all elements are rendered
    await new Promise(resolve => setTimeout(resolve, 100));

    // Force light mode for printing
    const originalDarkMode = document.documentElement.classList.contains('dark');
    document.documentElement.classList.remove('dark');

    // Create a container for the clone that's off-screen and hidden
    const container = document.createElement('div');
    container.style.position = 'fixed';
    container.style.left = '-9999px';
    container.style.top = '0';
    container.style.visibility = 'hidden';
    container.style.zIndex = '-9999';
    container.id = 'receipt-capture-container';

    // Clone the element with all children
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
            backgroundColor: '#ffffff',
            logging: false,
            useCORS: true,
            scrollX: 0,
            scrollY: 0,
            windowWidth: clone.scrollWidth,
            windowHeight: clone.scrollHeight,
            ignoreElements: (element) => {
                return element.tagName === 'BUTTON';
            },
            onclone: (clonedDoc) => {
                // Ensure any cloned elements are also hidden
                const clonedContainer = clonedDoc.getElementById('receipt-capture-container');
                if (clonedContainer) {
                    clonedContainer.style.visibility = 'hidden';
                    clonedContainer.style.position = 'fixed';
                    clonedContainer.style.left = '-9999px';
                }
            }
        });

        // Restore original dark mode state
        if (originalDarkMode) {
            document.documentElement.classList.add('dark');
        }

        return canvas;
    } finally {
        // Ensure container is removed even if errors occur
        const container = document.getElementById('receipt-capture-container');
        if (container) {
            document.body.removeChild(container);
        }
    }
};

const shareReceipt = async () => {
    try {
        const canvas = await generateReceiptImage();

        // Convert canvas to blob
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
                ? `I donated ${formatCurrency(props.transaction.amount)} to Your Organization`
                : `Payment of ${formatCurrency(props.transaction.amount)} to Your Organization`
        };

        // Check if sharing is supported
        if (navigator.canShare && navigator.canShare(shareData)) {
            try {
                // Add a small delay to ensure share dialog stays open
                await new Promise<void>((resolve, reject) => {
                    navigator.share(shareData)
                        .then(() => resolve())
                        .catch(reject);

                    // Add a fallback timeout in case the share dialog closes immediately
                    setTimeout(() => {
                        // This helps keep the share dialog open on some browsers
                        console.log('Share dialog active');
                    }, 100);
                });

                return;
            } catch (shareError) {
                console.log('Share failed, falling back to download', shareError);
                // Continue to download fallback
            }
        }

        // Fallback to download
        downloadImageFromCanvas(canvas);
        toast.info('Sharing not supported. The receipt has been downloaded instead.');

    } catch (error) {
        console.error('Error sharing receipt:', error);

        if (error instanceof Error) {
            if (error.message.includes('cancel')) {
                // User cancelled the share - no need to show error
                return;
            }
            toast.error(`Error sharing: ${error.message}`);
        }

        // Final fallback
        try {
            const canvas = await generateReceiptImage();
            downloadImageFromCanvas(canvas);
        } catch (fallbackError) {
            console.error('Fallback failed:', fallbackError);
        }
    }
};

const downloadImageFromCanvas = (canvas: HTMLCanvasElement) => {
    return new Promise<void>((resolve, reject) => {
        try {
            const link = document.createElement('a');
            link.download = `receipt-${props.transaction.txn_id}.png`;

            // Use setTimeout to ensure the click happens after the current event loop
            setTimeout(() => {
                try {
                    link.href = canvas.toDataURL('image/png');
                    link.style.display = 'none';
                    document.body.appendChild(link);
                    link.click();

                    // Clean up after a small delay
                    setTimeout(() => {
                        document.body.removeChild(link);
                        URL.revokeObjectURL(link.href);
                        resolve();
                    }, 100);
                } catch (error) {
                    reject(error);
                }
            }, 50);
        } catch (error) {
            reject(error);
        }
    });
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

        // Create a new window with proper print styles
        const printWindow = window.open('', '_blank');

        if (printWindow) {
            printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Print Receipt</title>
                    <style>
                        @page {
                            size: auto;
                            margin: 5mm;
                        }
                        body {
                            margin: 0;
                            padding: 0;
                            display: flex;
                            justify-content: center;
                            align-items: center;
                            min-height: 100vh;
                        }
                        img {
                            max-width: 100%;
                            height: auto;
                            object-fit: contain;
                        }
                    </style>
                </head>
                <body>
                    <img src="${dataUrl}" />
                    <script>
                        window.onload = function() {
                            setTimeout(function() {
                                window.print();
                                setTimeout(function() {
                                    window.close();
                                }, 100);
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

/* Add to your style section */
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

/* Ensure all elements are visible for html2canvas */
#receipt {
    background-color: white !important;
    color: black !important;
}

#receipt .dark\:bg-gray-800 {
    background-color: white !important;
}

#receipt .dark\:text-white {
    color: black !important;
}</style>
