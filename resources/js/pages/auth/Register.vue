<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import { Progress } from '@/components/ui/progress';
import { computed } from 'vue';

const props = defineProps({
    step: {
        type: Number,
        default: 1,
    },
    orgData: {
        type: Object,
        default: () => ({}),
    },
});

// Organization form - initialize with session data if available
const orgForm = useForm({
    org_name: props.orgData.org_name || '',
    org_email: props.orgData.org_email || '',
    org_phone: props.orgData.org_phone || '',
    org_address: props.orgData.org_address || '',
});

// User form
const userForm = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const steps = computed(() => [
    {
        id: 1,
        name: 'Organization',
        status: props.step === 1 ? 'current' : 'complete',
        completed: props.step > 1
    },
    {
        id: 2,
        name: 'Your Account',
        status: props.step === 2 ? 'current' : props.step > 2 ? 'complete' : 'upcoming',
        completed: props.step > 2
    },
]);

const progressValue = computed(() => (props.step / 2) * 100);

const submitOrganization = () => {
    orgForm.post(route('register.organization'), {
        preserveScroll: true,
        onSuccess: () => {
            window.location.href = route('register', { step: 2 });
        }
    });
};

const submitUser = () => {
    userForm.post(route('register.user'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <AuthBase title="Create an account" description="Enter your details below to create your account">

        <Head title="Register" />

        <!-- Progress bar -->
        <div class="mb-8">
            <Progress :model-value="progressValue" class="h-2" />
            <nav class="flex items-center justify-center mt-4">
                <ol role="list" class="flex items-center space-x-4">
                    <li v-for="step in steps" :key="step.id" class="flex items-center">
                        <span :class="{
                            'relative flex h-5 w-5 items-center justify-center rounded-full bg-primary': step.status === 'current' || step.completed,
                            'relative flex h-5 w-5 items-center justify-center rounded-full bg-muted': step.status === 'upcoming',
                        }">
                            <span v-if="step.completed" class="h-3 w-3 rounded-full bg-primary-foreground" />
                            <span v-else-if="step.status === 'current'"
                                class="h-3 w-3 rounded-full bg-primary-foreground animate-pulse" />
                            <span v-else class="h-3 w-3 rounded-full bg-muted-foreground" />
                            <span class="sr-only">{{ step.name }}</span>
                        </span>
                        <span :class="{
                            'ml-2 text-sm font-medium text-primary': step.status === 'current',
                            'ml-2 text-sm font-medium text-muted-foreground': step.status !== 'current',
                        }">{{ step.name }}</span>
                    </li>
                </ol>
            </nav>
        </div>

        <!-- Step 1: Organization Form -->
        <form v-if="step === 1" @submit.prevent="submitOrganization" class="flex flex-col gap-6">
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="org_name">Organization Name *</Label>
                    <Input id="org_name" type="text" required autofocus v-model="orgForm.org_name"
                        placeholder="Your organization name" :disabled="orgForm.processing" />
                    <InputError :message="orgForm.errors.org_name" />
                </div>

                <div class="grid gap-2">
                    <Label for="org_email">Organization Email *</Label>
                    <Input id="org_email" type="email" required v-model="orgForm.org_email"
                        placeholder="organization@example.com" :disabled="orgForm.processing" />
                    <InputError :message="orgForm.errors.org_email" />
                </div>

                <div class="grid gap-2">
                    <Label for="org_phone">Organization Phone</Label>
                    <Input id="org_phone" type="tel" v-model="orgForm.org_phone" placeholder="+880XXXXXXXXXX"
                        :disabled="orgForm.processing" />
                    <InputError :message="orgForm.errors.org_phone" />
                </div>

                <div class="grid gap-2">
                    <Label for="org_address">Organization Address</Label>
                    <Input id="org_address" type="text" v-model="orgForm.org_address" placeholder="123 Main St, Dhaka"
                        :disabled="orgForm.processing" />
                    <InputError :message="orgForm.errors.org_address" />
                </div>

                <Button type="submit" class="mt-2 w-full" :disabled="orgForm.processing">
                    <LoaderCircle v-if="orgForm.processing" class="h-4 w-4 animate-spin mr-2" />
                    Continue to Account Setup
                </Button>
            </div>

            <div class="text-center text-sm text-muted-foreground">
                Already have an account?
                <TextLink :href="route('login')" class="underline underline-offset-4">Log in</TextLink>
            </div>
        </form>

        <!-- Step 2: User Form -->
        <form v-else @submit.prevent="submitUser" class="flex flex-col gap-6">
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="name">Your Full Name *</Label>
                    <Input id="name" type="text" required autofocus v-model="userForm.name" placeholder="John Doe"
                        :disabled="userForm.processing" />
                    <InputError :message="userForm.errors.name" />
                </div>

                <div class="grid gap-2">
                    <Label for="email">Your Email *</Label>
                    <Input id="email" type="email" required v-model="userForm.email" placeholder="you@example.com"
                        :disabled="userForm.processing" />
                    <InputError :message="userForm.errors.email" />
                </div>

                <div class="grid gap-2">
                    <Label for="password">Password *</Label>
                    <Input id="password" type="password" required v-model="userForm.password"
                        placeholder="At least 8 characters" :disabled="userForm.processing" />
                    <InputError :message="userForm.errors.password" />
                </div>

                <div class="grid gap-2">
                    <Label for="password_confirmation">Confirm Password *</Label>
                    <Input id="password_confirmation" type="password" required v-model="userForm.password_confirmation"
                        placeholder="Confirm your password" :disabled="userForm.processing" />
                    <InputError :message="userForm.errors.password_confirmation" />
                </div>

                <div class="flex gap-4">
                    <Button type="button" variant="outline" class="w-full"
                        @click="router.get(route('register', { step: 1 }))" :disabled="userForm.processing">
                        Back
                    </Button>
                    <Button type="submit" class="w-full" :disabled="userForm.processing">
                        <LoaderCircle v-if="userForm.processing" class="h-4 w-4 animate-spin mr-2" />
                        Complete Registration
                    </Button>
                </div>
            </div>
        </form>
    </AuthBase>
</template>
