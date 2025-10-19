<script setup lang="ts">
import { ref, watch, computed, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { useToast } from 'vue-toastification';
import vSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';
import axios from 'axios';

const toast = useToast();

const props = defineProps({
    transaction: {
        type: Object,
        required: true
    }
});

const emit = defineEmits(['close']);

const isOpen = ref(false);
const donorSearch = ref('');
const fundSearch = ref('');
const donors = ref<{ id: number, name: string, phone?: string }[]>([]);
const funds = ref<{ id: number, name: string }[]>([]);

const form = ref({
    id: null,
    donor_id: null as number | null,
    fund_id: null as number | null,
    amount: '0.00',
    type: 'credit',
    purpose: '',
    payment_method: 'cash',
    reference: '',
    note: '',
    status: 'pending',
});

// Fetch initial data
const fetchData = async () => {
    try {
        const [donorsRes, fundsRes] = await Promise.all([
            axios.get('/donors/dropdown'),
            axios.get('/funds/dropdown')
        ]);

        if (donorsRes.data.success) {
            donors.value = donorsRes.data.donors;
        } else {
            throw new Error(donorsRes.data.message);
        }

        if (fundsRes.data.success) {
            funds.value = fundsRes.data.funds;
        } else {
            throw new Error(fundsRes.data.message);
        }
    } catch (error) {
        Swal.fire({
            title: 'Error!',
            text: error.response?.data?.message || error.message || 'Failed to load data',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    }
};

// Filter donors based on search
const filteredDonors = computed(() => {
    if (!donorSearch.value) return donors.value;
    const searchTerm = donorSearch.value.toLowerCase();
    return donors.value.filter(donor =>
        donor.name.toLowerCase().includes(searchTerm) ||
        (donor.phone && donor.phone.includes(searchTerm))
    );
});

// Filter funds based on search
const filteredFunds = computed(() => {
    if (!fundSearch.value) return funds.value;
    return funds.value.filter(fund =>
        fund.name.toLowerCase().includes(fundSearch.value.toLowerCase())
    );
});

watch(() => props.transaction, (newTransaction) => {
    if (newTransaction) {
        form.value = {
            id: newTransaction.id,
            donor_id: newTransaction.donor_id,
            fund_id: newTransaction.fund_id,
            amount: parseFloat(newTransaction.amount.toString().replace(/,/g, '')) || 0,
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
    router.put(`/transactions/${form.value.id}`, form.value, {
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

onMounted(() => {
    fetchData();
});

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
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block font-semibold mb-1">Donor/Raiser</label>
                                <v-select v-model="form.donor_id" :options="filteredDonors" label="name"
                                    :reduce="donor => donor.id" placeholder="Search by name or phone" :filterable="false"
                                    @search="donorSearch = $event" :class="['w-full']">
                                    <template #option="{ name, phone }">
                                        <div>
                                            <span class="truncate">{{ name }}</span>
                                            <span class="text-gray-500 ml-2 whitespace-nowrap" v-if="phone">
                                                ({{ phone }})
                                            </span>
                                        </div>
                                    </template>
                                    <template #selected-option="{ name, phone }">
                                        <div>
                                            <span>{{ name }}</span>
                                            <span class="text-gray-500 ml-2" v-if="phone">
                                                ({{ phone }})
                                            </span>
                                        </div>
                                    </template>
                                    <template #no-options>
                                        No donors found
                                    </template>
                                </v-select>
                            </div>

                            <div>
                                <label class="block font-semibold mb-1">Fund <span class="text-red-500">*</span></label>
                                <v-select v-model="form.fund_id" :options="filteredFunds" label="name"
                                    :reduce="fund => fund.id" placeholder="Search or select fund" :filterable="false"
                                    @search="fundSearch = $event" :class="['w-full']" required>
                                </v-select>
                            </div>

                            <div>
                                <label class="block font-semibold mb-1">Amount <span class="text-red-500">*</span></label>
                                <input v-model="form.amount" type="number" step="0.01"
                                    class="w-full border rounded px-3 py-2" placeholder="Enter Amount" required />
                            </div>

                            <div>
                                <label class="block font-semibold mb-1">Payment Method <span
                                        class="text-red-500">*</span></label>
                                <select v-model="form.payment_method" class="w-full border rounded px-3 py-2" required>
                                    <option value="">Select Payment Method</option>
                                    <option value="cash">Cash</option>
                                    <option value="bank">Bank</option>
                                    <option value="card">Card</option>
                                    <option value="bkash">bkash</option>
                                    <option value="nagad">Nagad</option>
                                    <option value="rocket">Rocket</option>
                                </select>
                            </div>

                            <div>
                                <label class="block font-semibold mb-1">Purpose</label>
                                <input v-model="form.purpose" type="text" class="w-full border rounded px-3 py-2"
                                    placeholder="Enter purpose" />
                            </div>

                            <div>
                                <label class="block font-semibold mb-1">Reference</label>
                                <input v-model="form.reference" type="text" class="w-full border rounded px-3 py-2"
                                    placeholder="Enter reference" />
                            </div>

                            <div>
                                <label for="status" class="block font-semibold mb-1">Status</label>
                                <select v-model="form.status" id="status" class="w-full border rounded px-3 py-2">
                                    <option value="completed">Completed</option>
                                    <option value="pending">Pending</option>
                                    <option value="canceled">Canceled</option>
                                </select>
                            </div>

                            <div>
                                <label class="block font-semibold mb-1">Type</label>
                                <div class="flex gap-2">
                                    <label class="inline-flex items-center">
                                        <input type="radio" value="credit" v-model="form.type" class="hidden peer" />
                                        <span class="px-4 py-2 rounded-full border border-gray-300 text-sm font-medium cursor-pointer transition-colors duration-200
                                            peer-checked:bg-gray-800 peer-checked:text-white peer-checked:border-gray-800
                                            hover:bg-gray-100">
                                            Credit
                                        </span>
                                    </label>
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

                        <div class="mt-4">
                            <label class="block font-semibold mb-1">Note</label>
                            <textarea v-model="form.note" class="w-full border rounded px-3 py-2"
                                placeholder="Enter note"></textarea>
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
