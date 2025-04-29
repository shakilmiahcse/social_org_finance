<script setup lang="ts">
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { useToast } from 'vue-toastification';

const toast = useToast();

const props = defineProps({
    transaction: {
        type: Object,
        required: true
    },
    donors: {
        type: Array as () => Array<{ id: number, name: string }>,
        default: () => []
    },
    funds: {
        type: Array as () => Array<{ id: number, name: string }>,
        default: () => []
    }
});

const emit = defineEmits(['close']);

const isOpen = ref(false);
const form = ref({
    id: null,
    donor_id: null,
    fund_id: null,
    amount: '0.00',
    type: 'credit',
    purpose: '',
    payment_method: 'cash',
    reference: '',
    note: '',
    status: 'pending',
});

watch(() => props.transaction, (newTransaction) => {
    if (newTransaction) {
        form.value = {
            id: newTransaction.id,
            donor_id: newTransaction.donor_id,
            fund_id: newTransaction.fund_id,
            amount: newTransaction.amount,
            type: newTransaction.type,
            purpose: newTransaction.purpose,
            payment_method: newTransaction.payment_method,
            reference: newTransaction.reference,
            note: newTransaction.note,
            status: newTransaction.status,
        };
    }
});

const submit = () => {
    router.put(route('transactions.update', form.value.id), form.value, {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Transaction updated successfully');
            closeModal();
        },
        onError: (errors) => {
            Swal.fire({
                title: 'Error!',
                text: Object.values(errors).join('\n'),
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }
    });
};

const closeModal = () => {
    isOpen.value = false;
    emit('close');
};

defineExpose({
    open: () => isOpen.value = true,
    close: closeModal
});
</script>

<template>
    <div v-if="isOpen" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75" @click="closeModal"></div>
            </div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-5xl sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-3">Edit Transaction</h3>
                    <form @submit.prevent="submit">
                        <!-- Grid for responsive two-column layout -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Donor ID -->
                            <div class="mb-3">
                                <label for="donor_id" class="block text-sm font-medium text-gray-700">
                                    Donor/Raiser
                                </label>
                                <select v-model="form.donor_id" id="donor_id"
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 sm:text-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Select Donor/Raiser</option>
                                    <option v-for="donor in donors" :key="donor.id" :value="donor.id">
                                        {{ donor.name }}
                                    </option>
                                </select>
                            </div>

                            <!-- Fund ID -->
                            <div class="mb-3">
                                <label for="fund_id" class="block text-sm font-medium text-gray-700">
                                    Fund <span class="text-red-500">*</span>
                                </label>
                                <select v-model="form.fund_id" id="fund_id" required
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 sm:text-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Select Fund</option>
                                    <option v-for="fund in funds" :key="fund.id" :value="fund.id">
                                        {{ fund.name }}
                                    </option>
                                </select>
                            </div>

                            <!-- Amount -->
                            <div class="mb-3">
                                <label for="amount" class="block text-sm font-medium text-gray-700">
                                    Amount <span class="text-red-500">*</span>
                                </label>
                                <input v-model="form.amount" type="number" step="0.01" id="amount" required
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 sm:text-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                            </div>

                            <!-- Payment Method -->
                            <div class="mb-3">
                                <label for="payment_method" class="block text-sm font-medium text-gray-700">
                                    Payment Method <span class="text-red-500">*</span>
                                </label>
                                <select v-model="form.payment_method" id="payment_method" required
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 sm:text-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="cash">Cash</option>
                                    <option value="bkash">bKash</option>
                                    <option value="bank">Bank</option>
                                </select>
                            </div>

                            <!-- Purpose -->
                            <div class="mb-3">
                                <label for="purpose" class="block text-sm font-medium text-gray-700">
                                    Purpose
                                </label>
                                <input v-model="form.purpose" type="text" id="purpose"
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 sm:text-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                            </div>

                            <!-- Reference -->
                            <div class="mb-3">
                                <label for="reference" class="block text-sm font-medium text-gray-700">
                                    Reference
                                </label>
                                <input v-model="form.reference" type="text" id="reference"
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 sm:text-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                            </div>

                            <!-- Status -->
                            <div class="mb-3">
                                <label for="status" class="block text-sm font-medium text-gray-700">
                                    Status
                                </label>
                                <select v-model="form.status" id="status"
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 sm:text-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="pending">Pending</option>
                                    <option value="completed">Completed</option>
                                    <option value="canceled">Canceled</option>
                                </select>
                            </div>

                            <!-- Type (Smart Radio Buttons) -->
                            <div class="mb-3">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Type <span
                                        class="text-red-500">*</span></label>
                                <div class="flex gap-2">
                                    <!-- Credit Option -->
                                    <label class="inline-flex items-center">
                                        <input type="radio" value="credit" v-model="form.type" class="hidden peer" />
                                        <span class="px-4 py-2 rounded-full border border-gray-300 text-sm font-medium cursor-pointer transition-colors duration-200
                                            peer-checked:bg-gray-800 peer-checked:text-white peer-checked:border-gray-800
                                            hover:bg-gray-100">
                                            Credit
                                        </span>
                                    </label>
                                    <!-- Debit Option -->
                                    <label class="inline-flex items-center">
                                        <input type="radio" value="debit" v-model="form.type" class="hidden peer" />
                                        <span class="px-4 py-2 rounded-full border border-gray-300 text-sm font-medium cursor-pointer transition-colors duration-200
                                            peer-checked:bg-gray-800 peer-checked:text-white peer-checked:border-gray-800
                                            hover:bg-gray-100">
                                            Debit
                                        </span>
                                    </label>
                                </div>
                            </div>

                        </div>
                        <!-- Note -->
                        <div class="mb-3">
                            <label for="note" class="block text-sm font-medium text-gray-700">
                                Note
                            </label>
                            <textarea v-model="form.note" id="note" rows="3"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 sm:text-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                        </div>
                    </form>
                </div>

                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" @click="submit"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Update
                    </button>
                    <button type="button" @click="closeModal"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
