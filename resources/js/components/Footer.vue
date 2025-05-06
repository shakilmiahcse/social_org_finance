<template>
    <footer class="w-full py-4 text-sm text-gray-500 border-t mt-8">
        <div
            class="mx-auto px-4 flex justify-between items-center text-xs text-neutral-500 dark:text-neutral-400">
            <div class="flex flex-wrap gap-x-2">
                <span>© Oikko Soft {{ currentYear }}. All rights reserved.</span>
                <span>|</span>
                <span>
                    Developed by
                    <a href="#" class="hover:underline text-primary">Shakil Miah</a>
                </span>
            </div>
            <div class="text-right">
                Powered by Rokeya Begum Foundation
            </div>
        </div>
    </footer>
</template>

<script setup>
const currentYear = new Date().getFullYear();
</script>
