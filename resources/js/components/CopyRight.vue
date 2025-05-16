<template>
    <footer class="w-full text-sm text-gray-500">
        <div class="border-t border-gray-800 mt-12 pt-5 flex flex-col md:flex-row justify-between items-center">
            <div class="flex flex-wrap gap-x-2">
                <span>© Oikko Soft {{ currentYear }}. All rights reserved.</span>
                <span>|</span>
                <span>
                    Developed by
                    <a href="#" class="hover:underline text-success">Shakil Miah</a>
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
