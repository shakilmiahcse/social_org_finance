<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/org/Layout.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useForm, Head } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    organization: Object,
    timezones: Array,
    currencies: Array
});

const form = useForm({
    _method: 'PUT',
    name: props.organization?.name || '',
    email: props.organization?.email || '',
    phone: props.organization?.phone || '',
    address: props.organization?.address || '',
    website: props.organization?.website || '',
    timezone: props.organization?.timezone || '',
    currency: props.organization?.currency || '',
    slogan: props.organization?.slogan || '',
    logo_path: null as File | null,
});

const fileInput = ref<HTMLInputElement | null>(null);
const previewUrl = ref(
    props.organization?.logo_path ? `/${props.organization.logo_path}` : null
);

const handleFileChange = (e: Event) => {
    const file = (e.target as HTMLInputElement).files?.[0];
    if (file) {
        form.logo_path = file;
        previewUrl.value = URL.createObjectURL(file);
    }
};

const submit = () => {
    form.post(route('org.update'), {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            if (fileInput.value) fileInput.value.value = '';
            previewUrl.value = props.organization?.logo_path;
        },
    });
};
</script>

<template>
    <AppLayout>

        <Head title="Organization Settings" />
        <SettingsLayout>
            <div class="space-y-6">
                <HeadingSmall title="Organization Settings" description="Update your organization's basic details." />

                <form @submit.prevent="submit" class="space-y-6" enctype="multipart/form-data">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Name -->
                        <div class="grid gap-2">
                            <Label for="name">Name</Label>
                            <Input id="name" v-model="form.name" type="text" />
                            <InputError :message="form.errors.name" />
                        </div>

                        <!-- Slogan -->
                        <div class="grid gap-2">
                            <Label for="slogan">Slogan</Label>
                            <Input id="slogan" v-model="form.slogan" type="text" />
                            <InputError :message="form.errors.slogan" />
                        </div>

                        <!-- Email -->
                        <div class="grid gap-2">
                            <Label for="email">Email</Label>
                            <Input id="email" v-model="form.email" type="email" />
                            <InputError :message="form.errors.email" />
                        </div>

                        <!-- Phone -->
                        <div class="grid gap-2">
                            <Label for="phone">Phone</Label>
                            <Input id="phone" v-model="form.phone" type="text" />
                            <InputError :message="form.errors.phone" />
                        </div>

                        <!-- Website -->
                        <div class="grid gap-2">
                            <Label for="website">Website</Label>
                            <Input id="website" v-model="form.website" type="url" />
                            <InputError :message="form.errors.website" />
                        </div>

                        <!-- Timezone -->
                        <div class="grid gap-2">
                            <Label for="timezone">Timezone</Label>
                            <select id="timezone" v-model="form.timezone" class="w-full border rounded px-3 py-2">
                                <option disabled value="">Select Timezone</option>
                                <option v-for="zone in props.timezones" :key="zone" :value="zone">
                                    {{ zone }}
                                </option>
                            </select>
                            <InputError :message="form.errors.timezone" />
                        </div>

                        <!-- Currency -->
                        <div class="grid gap-2">
                            <Label for="currency">Currency</Label>
                            <select id="currency" v-model="form.currency" class="w-full border rounded px-3 py-2">
                                <option disabled value="">Select Currency</option>
                                <option v-for="currency in props.currencies" :key="currency" :value="currency">
                                    {{ currency }}
                                </option>
                            </select>
                            <InputError :message="form.errors.currency" />
                        </div>

                        <!-- Address -->
                        <div class="md:col-span-2 grid gap-2">
                            <Label for="address">Address</Label>
                            <textarea id="address" v-model="form.address" rows="3"
                                class="w-full border rounded px-3 py-2"></textarea>
                            <InputError :message="form.errors.address" />
                        </div>

                        <!-- Logo Upload -->
                        <div class="md:col-span-2 grid gap-2">
                            <Label for="logo">Organization Logo</Label>
                            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                                <div
                                    class="w-28 h-28 rounded-md border bg-white flex items-center justify-center overflow-hidden shadow-sm">
                                    <img v-if="previewUrl" :src="previewUrl" alt="Logo preview"
                                        class="w-full h-full object-cover" />
                                    <div v-else class="text-gray-400 text-sm text-center px-2">
                                        No Logo
                                    </div>
                                </div>

                                <div class="flex-1 w-full">
                                    <input id="logo" ref="fileInput" type="file" @change="handleFileChange" accept="image/*"
                                        class="w-full border rounded px-3 py-2 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-primary/90" />
                                    <InputError :message="form.errors.logo_path" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="flex items-center gap-4 justify-center">
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
