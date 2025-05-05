<template>
    <div class="relative h-2.5 w-full overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700">
        <div class="h-full bg-blue-600 transition-all duration-300 ease-linear" :class="{
            'bg-blue-600': !indeterminate,
            'animate-indeterminate bg-gradient-to-r from-blue-500 via-blue-400 to-blue-500': indeterminate
        }" :style="{ width: indeterminate ? '50%' : `${modelValue}%` }" />
    </div>
</template>

<script setup>
defineProps({
    modelValue: {
        type: Number,
        default: 0,
        validator: (value) => value >= 0 && value <= 100,
    },
    indeterminate: {
        type: Boolean,
        default: false,
    },
});
</script>

<style>
@keyframes indeterminate {
    0% {
        transform: translateX(-100%);
    }

    100% {
        transform: translateX(200%);
    }
}

.animate-indeterminate {
    animation: indeterminate 1.5s infinite linear;
}
</style>
