<script setup lang="ts">
import { useForm, Head, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/org/Layout.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import { Button } from '@/components/ui/button';

const props = defineProps<{
    funds: Array<any>,
    main_fund_id: number | null,
}>();

const form = useForm({
    main_fund_id: props.main_fund_id,
});

const updateFundType = () => {
    form.put(route('fund-type.update'), {
        preserveScroll: true,
        onSuccess: () => {
            router.reload({ only: ['main_fund_id'] }); // Refresh updated prop only
        },
    });
};
</script>

<template>
    <AppLayout>

        <Head title="Fund Type Settings" />

        <SettingsLayout>
            <HeadingSmall title="Select Main Fund"
                description="Only one fund can be marked as 'main'. All others will become campaigns." />

            <form @submit.prevent="updateFundType" class="space-y-6">
                <div>
                    <label for="main_fund_id" class="block text-sm font-medium text-gray-700 mb-1">Main Fund</label>
                    <div class="relative">
                        <select id="main_fund_id" v-model="form.main_fund_id"
                            class="block w-1/2 appearance-none rounded-md border border-gray-300 bg-white px-4 py-2 pr-10 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <option disabled value="">Select Fund</option>
                            <option v-for="fund in funds" :key="fund.id" :value="fund.id">
                                {{ fund.name }} ({{ fund.type }})
                            </option>
                        </select>

                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div>
                    <Button :disabled="form.processing">Save Settings</Button>
                </div>

                <div v-if="form.recentlySuccessful" class="text-sm text-green-600">Saved.</div>
            </form>
        </SettingsLayout>
    </AppLayout>
</template>
