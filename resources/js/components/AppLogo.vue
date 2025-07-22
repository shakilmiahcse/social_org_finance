<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();

// Get organization name from shared props
const organizationName = computed(() => {
    return page.props.auth?.organization?.name || 'OikkoSoft'; // Fallback to 'OikkoSoft' if not set
});
</script>

<template>
    <div class="flex size-8 items-center justify-center">
        <AppLogoIcon class="size-5 fill-current text-white dark:text-black" />
    </div>
    <div class="ml-1 grid flex-1 text-left text-lg">
        <span class="mb-0.5 truncate font-semibold leading-none py-1">{{ organizationName }}</span>
    </div>
</template>