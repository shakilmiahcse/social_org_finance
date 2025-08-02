<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import CopyRight from '@/components/CopyRight.vue';
import { ref } from 'vue';

const activeFAQ = ref(1); // First FAQ is active by default

const toggleFAQ = (id: number) => {
    activeFAQ.value = activeFAQ.value === id ? 0 : id; // Toggle between active and none
};
</script>

<template>
    <Head title="OikkoSoft - Comprehensive Donation Management">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
        <meta name="description" content="End-to-end solution for managing donations, donors, funds, and financial tracking for social organizations">
    </Head>

    <div class="min-h-screen bg-gradient-to-b from-gray-50 to-white text-gray-900">
        <!-- Header -->
        <header class="sticky top-0 z-50 bg-white/80 backdrop-blur-sm border-b border-gray-200">
            <div class="container mx-auto px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <a href="/">
                        <img class="w-auto h-10" src="/images/logo.png" alt="OikkoSoft">
                        </a>
                    </div>
                    <nav class="hidden md:flex items-center space-x-8">
                        <a href="#features" class="text-gray-600 hover:text-indigo-600 transition">Features</a>
                        <a href="#solutions" class="text-gray-600 hover:text-indigo-600 transition">Solutions</a>
                        <a href="#testimonials" class="text-gray-600 hover:text-indigo-600 transition">Testimonials</a>
                        <a href="#pricing" class="text-gray-600 hover:text-indigo-600 transition">Pricing</a>
                    </nav>
                    <div class="flex items-center space-x-4">
                        <Link v-if="$page.props.auth.user" :href="route('dashboard')"
                            class="px-4 py-2 text-sm font-medium text-indigo-600 hover:text-indigo-800 transition">
                        Dashboard
                        </Link>
                        <template v-else>
                            <Link :href="route('login')"
                                class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-indigo-600 transition">
                            Log in
                            </Link>
                            <Link :href="route('register')"
                                class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 transition">
                            Get Started
                            </Link>
                        </template>
                    </div>
                </div>
            </div>
        </header>

        <!-- Hero Section -->
        <section class="py-20">
            <div class="container mx-auto px-6 flex flex-col md:flex-row items-center">
                <div class="md:w-1/2 mb-10 md:mb-0">
                    <h1 class="text-4xl md:text-5xl font-bold leading-tight mb-6">
                        Transform Your <span class="text-indigo-600">Donation Operations</span>
                    </h1>
                    <p class="text-lg text-gray-600 mb-8">
                        OikkoSoft provides comprehensive tools for social organizations to manage donors, track funds, process transactions, and run campaigns with complete financial transparency.
                    </p>
                    <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
                        <Link :href="route('register')"
                            class="px-8 py-3 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700 transition">
                        Start Free Trial
                        </Link>
                        <a href="#demo-video"
                            class="px-8 py-3 border border-gray-300 rounded-lg font-medium hover:border-indigo-400 transition flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v8a2 2 0 01-2 2h-2a2 2 0 01-2-2V6z"></path>
                            </svg>
                            Watch Demo
                        </a>
                    </div>
                    <div class="mt-8 flex items-center">
                        <div class="flex -space-x-2">
                            <img class="w-10 h-10 rounded-full border-2 border-white" src="/images/user/user1.jpg" alt="User 1">
                            <img class="w-10 h-10 rounded-full border-2 border-white" src="/images/user/user2.jpg" alt="User 2">
                            <img class="w-10 h-10 rounded-full border-2 border-white" src="/images/user/user3.jpg" alt="User 3">
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Trusted by 500+ organizations</p>
                            <div class="flex items-center">
                                <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                                <span class="ml-1 text-sm text-gray-600">4.9/5 from 120+ reviews</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="md:w-1/2">
                    <div class="relative rounded-xl shadow-xl border border-gray-200 overflow-hidden">
                        <img src="/images/dashboard-preview.png" alt="OikkoSoft Dashboard Preview" class="w-full h-auto">
                        <div class="absolute inset-0 bg-gradient-to-t from-gray-900/30 to-transparent"></div>
                        <div class="absolute bottom-0 left-0 right-0 p-6 text-white">
                            <h3 class="text-lg font-semibold">Real-time Donation Tracking</h3>
                            <p class="text-sm opacity-90">Monitor all transactions across funds and campaigns</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Logo Cloud -->
        <section class="py-12 bg-gray-50">
            <div class="container mx-auto px-6">
                <p class="text-center text-gray-500 mb-8">Trusted by leading social organizations</p>
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-8 items-center">
                    <img src="/images/org/images1.png" alt="Client Logo" class="h-25 opacity-60 hover:opacity-100 transition mx-auto p-10">
                    <img src="/images/org/images2.png" alt="Client Logo" class="h-25 opacity-60 hover:opacity-100 transition mx-auto p-10">
                    <img src="/images/org/images3.png" alt="Client Logo" class="h-25 opacity-60 hover:opacity-100 transition mx-auto p-10">
                    <img src="/images/org/images4.png" alt="Client Logo" class="h-25 opacity-60 hover:opacity-100 transition mx-auto p-10">
                    <img src="/images/org/images5.png" alt="Client Logo" class="h-25 opacity-60 hover:opacity-100 transition mx-auto p-10">
                    <img src="/images/org/images6.png" alt="Client Logo" class="h-25 opacity-60 hover:opacity-100 transition mx-auto p-10">
                </div>
            </div>
        </section>

        <!-- Key Features Section -->
        <section id="features" class="py-20">
            <div class="container mx-auto px-6">
                <div class="text-center mb-16">
                    <span class="inline-block px-3 py-1 text-sm font-semibold text-indigo-600 bg-indigo-50 rounded-full mb-4">Features</span>
                    <h2 class="text-3xl font-bold mb-4">Comprehensive Donation Management</h2>
                    <p class="text-gray-600 max-w-2xl mx-auto">
                        Everything you need to manage donors, funds, and financial transactions in one powerful platform.
                    </p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- Donor Management -->
                    <div class="bg-white p-8 rounded-xl shadow-sm hover:shadow-md transition border border-gray-100">
                        <div class="w-12 h-12 bg-indigo-50 rounded-lg flex items-center justify-center mb-6">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-3">Donor Management</h3>
                        <p class="text-gray-600 mb-4">
                            Comprehensive donor profiles with contact details, blood groups, donation history, and communication logs.
                        </p>
                        <ul class="space-y-2 text-sm text-gray-600">
                            <li class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Track donor demographics and preferences
                            </li>
                            <li class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Segment donors for targeted campaigns
                            </li>
                            <li class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Maintain complete donation histories
                            </li>
                        </ul>
                    </div>

                    <!-- Fund Tracking -->
                    <div class="bg-white p-8 rounded-xl shadow-sm hover:shadow-md transition border border-gray-100">
                        <div class="w-12 h-12 bg-indigo-50 rounded-lg flex items-center justify-center mb-6">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-3">Fund Tracking</h3>
                        <p class="text-gray-600 mb-4">
                            Manage multiple funds (main and campaign) with detailed tracking of all financial transactions.
                        </p>
                        <ul class="space-y-2 text-sm text-gray-600">
                            <li class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Separate main and campaign funds
                            </li>
                            <li class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Track credits and debits
                            </li>
                            <li class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Fund-to-fund transfers with audit trails
                            </li>
                        </ul>
                    </div>

                    <!-- Transaction Processing -->
                    <div class="bg-white p-8 rounded-xl shadow-sm hover:shadow-md transition border border-gray-100">
                        <div class="w-12 h-12 bg-indigo-50 rounded-lg flex items-center justify-center mb-6">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-3">Transaction Processing</h3>
                        <p class="text-gray-600 mb-4">
                            Record and categorize all financial transactions with donor associations and document attachments.
                        </p>
                        <ul class="space-y-2 text-sm text-gray-600">
                            <li class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Associate with donors and funds
                            </li>
                            <li class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Attach receipts and documents
                            </li>
                            <li class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Multi-currency support
                            </li>
                        </ul>
                    </div>

                    <!-- Campaign Management -->
                    <div class="bg-white p-8 rounded-xl shadow-sm hover:shadow-md transition border border-gray-100">
                        <div class="w-12 h-12 bg-indigo-50 rounded-lg flex items-center justify-center mb-6">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-3">Campaign Management</h3>
                        <p class="text-gray-600 mb-4">
                            Create and manage fundraising campaigns with specific goals, timeframes, and performance tracking.
                        </p>
                        <ul class="space-y-2 text-sm text-gray-600">
                            <li class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Dedicated campaign funds
                            </li>
                            <li class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Progress tracking
                            </li>
                            <li class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Adjustment history
                            </li>
                        </ul>
                    </div>

                    <!-- Document Management -->
                    <div class="bg-white p-8 rounded-xl shadow-sm hover:shadow-md transition border border-gray-100">
                        <div class="w-12 h-12 bg-indigo-50 rounded-lg flex items-center justify-center mb-6">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-3">Document Management</h3>
                        <p class="text-gray-600 mb-4">
                            Securely store and organize transaction documents with version control and access management.
                        </p>
                        <ul class="space-y-2 text-sm text-gray-600">
                            <li class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Attach files to transactions
                            </li>
                            <li class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Categorize by type
                            </li>
                            <li class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Role-based access control
                            </li>
                        </ul>
                    </div>

                    <!-- Reporting & Analytics -->
                    <div class="bg-white p-8 rounded-xl shadow-sm hover:shadow-md transition border border-gray-100">
                        <div class="w-12 h-12 bg-indigo-50 rounded-lg flex items-center justify-center mb-6">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-3">Reporting & Analytics</h3>
                        <p class="text-gray-600 mb-4">
                            Generate detailed financial reports, donor analytics, and campaign performance metrics.
                        </p>
                        <ul class="space-y-2 text-sm text-gray-600">
                            <li class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Custom report builder
                            </li>
                            <li class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Export to multiple formats
                            </li>
                            <li class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Scheduled report delivery
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <!-- Demo Video Section -->
        <section id="demo-video" class="py-20 bg-gray-50">
            <div class="container mx-auto px-6">
                <div class="text-center mb-12">
                    <span class="inline-block px-3 py-1 text-sm font-semibold text-indigo-600 bg-indigo-50 rounded-full mb-4">Product Tour</span>
                    <h2 class="text-3xl font-bold mb-4">See OikkoSoft in Action</h2>
                    <p class="text-gray-600 max-w-2xl mx-auto">
                        Watch our 2-minute demo to see how OikkoSoft can transform your donation management.
                    </p>
                </div>
                <div class="max-w-4xl mx-auto rounded-xl overflow-hidden shadow-xl border border-gray-200 bg-black">
                    <div class="aspect-w-16 aspect-h-9">
                        <div class="w-full h-full flex items-center justify-center bg-gray-900 text-white">
                            <button class="w-16 h-16 rounded-full bg-indigo-600 hover:bg-indigo-700 transition flex items-center justify-center">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Solutions Section -->
        <section id="solutions" class="py-20">
            <div class="container mx-auto px-6">
                <div class="text-center mb-16">
                    <span class="inline-block px-3 py-1 text-sm font-semibold text-indigo-600 bg-indigo-50 rounded-full mb-4">Solutions</span>
                    <h2 class="text-3xl font-bold mb-4">Tailored for Your Organization Type</h2>
                    <p class="text-gray-600 max-w-2xl mx-auto">
                        OikkoSoft adapts to meet the specific needs of different types of social organizations.
                    </p>
                </div>

                <div class="grid md:grid-cols-3 gap-8">
                    <div class="bg-white p-8 rounded-xl shadow-sm hover:shadow-md transition border border-gray-100">
                        <div class="w-16 h-16 bg-indigo-50 rounded-lg flex items-center justify-center mb-6 mx-auto">
                            <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-3 text-center">Nonprofits</h3>
                        <p class="text-gray-600 text-center mb-4">
                            Comprehensive tools for donor management, grant tracking, and fund allocation.
                        </p>
                        <ul class="space-y-2 text-sm text-gray-600">
                            <li class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Donor relationship management
                            </li>
                            <li class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Grant and restricted fund tracking
                            </li>
                            <li class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Board reporting tools
                            </li>
                        </ul>
                    </div>

                    <div class="bg-white p-8 rounded-xl shadow-sm hover:shadow-md transition border border-gray-100">
                        <div class="w-16 h-16 bg-indigo-50 rounded-lg flex items-center justify-center mb-6 mx-auto">
                            <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-3 text-center">Community Groups</h3>
                        <p class="text-gray-600 text-center mb-4">
                            Simple solutions for grassroots organizations and local community initiatives.
                        </p>
                        <ul class="space-y-2 text-sm text-gray-600">
                            <li class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Volunteer coordination
                            </li>
                            <li class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Event fundraising tools
                            </li>
                            <li class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Simple reporting
                            </li>
                        </ul>
                    </div>

                    <div class="bg-white p-8 rounded-xl shadow-sm hover:shadow-md transition border border-gray-100">
                        <div class="w-16 h-16 bg-indigo-50 rounded-lg flex items-center justify-center mb-6 mx-auto">
                            <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-3 text-center">Blood Banks</h3>
                        <p class="text-gray-600 text-center mb-4">
                            Specialized features for blood donation centers and health-related organizations.
                        </p>
                        <ul class="space-y-2 text-sm text-gray-600">
                            <li class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Blood group tracking
                            </li>
                            <li class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Donor health records
                            </li>
                            <li class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Compliance documentation
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <!-- Testimonials Section -->
        <section id="testimonials" class="py-20 bg-gray-50">
            <div class="container mx-auto px-6">
                <div class="text-center mb-16">
                    <span class="inline-block px-3 py-1 text-sm font-semibold text-indigo-600 bg-indigo-50 rounded-full mb-4">Testimonials</span>
                    <h2 class="text-3xl font-bold mb-4">Trusted by Social Organizations Worldwide</h2>
                    <p class="text-gray-600 max-w-2xl mx-auto">
                        Hear from organizations that have transformed their donation management with OikkoSoft.
                    </p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-100">
                        <div class="flex items-center mb-4">
                            <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                            <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                            <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                            <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                            <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                        </div>
                        <p class="text-gray-600 mb-6">
                            "OikkoSoft has transformed how we manage our donor relationships and track donations. The ability to associate transactions with specific campaigns and generate detailed reports has saved us countless hours."
                        </p>
                        <div class="flex items-center">
                            <img class="w-10 h-10 rounded-full mr-4" src="/images/user/user1.jpg" alt="Sarah Johnson">
                            <div>
                                <h4 class="font-semibold">Sarah Johnson</h4>
                                <p class="text-sm text-gray-500">Director, Hope Foundation</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-100">
                        <div class="flex items-center mb-4">
                            <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                            <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                            <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                            <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                            <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                        </div>
                        <p class="text-gray-600 mb-6">
                            "The campaign adjustment feature has been a game-changer for us. We can easily move funds between our main account and specific campaigns while maintaining perfect audit trails for our board."
                        </p>
                        <div class="flex items-center">
                            <img class="w-10 h-10 rounded-full mr-4" src="/images/user/user2.jpg" alt="Michael Chen">
                            <div>
                                <h4 class="font-semibold">Michael Chen</h4>
                                <p class="text-sm text-gray-500">Finance Manager, Community Aid</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-100">
                        <div class="flex items-center mb-4">
                            <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                            <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                            <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                            <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                            <svg class="w-5 h-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                        </div>
                        <p class="text-gray-600 mb-6">
                            "As a blood bank, we needed specialized donor tracking with health information. OikkoSoft's flexible platform adapted perfectly to our needs, and their support team was incredibly helpful during setup."
                        </p>
                        <div class="flex items-center">
                            <img class="w-10 h-10 rounded-full mr-4" src="/images/user/user4.jpg" alt="Dr. Aisha Rahman">
                            <div>
                                <h4 class="font-semibold">Dr. Aisha Rahman</h4>
                                <p class="text-sm text-gray-500">Medical Director, LifeBlood Network</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Pricing Section -->
        <section id="pricing" class="py-20 bg-gray-50">
            <div class="container mx-auto px-6">
                <div class="text-center mb-16">
                    <span class="inline-block px-3 py-1 text-sm font-semibold text-indigo-600 bg-indigo-50 rounded-full mb-4">Pricing</span>
                    <h2 class="text-3xl font-bold mb-4">Start Free, Upgrade When You Need</h2>
                    <p class="text-gray-600 max-w-2xl mx-auto">
                        Our free plan handles most needs, with optional upgrades when your organization grows.
                    </p>
                </div>

                <div class="grid md:grid-cols-3 gap-8 max-w-5xl mx-auto">
                    <!-- Free Plan -->
                    <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-200">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-xl font-semibold mb-2">Free</h3>
                                <p class="text-gray-600 mb-6">Perfect for getting started and small organizations</p>
                            </div>
                            <span class="inline-block px-3 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">RECOMMENDED</span>
                        </div>
                        <div class="mb-6">
                            <span class="text-4xl font-bold">$0</span>
                            <span class="text-gray-500">/forever</span>
                        </div>
                        <ul class="space-y-3 mb-8">
                            <li class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Up to 5 users
                            </li>
                            <li class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                500 donor records
                            </li>
                            <li class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                5 active campaigns
                            </li>
                            <li class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Basic reporting
                            </li>
                            <li class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Community support
                            </li>
                        </ul>
                        <Link :href="route('register')"
                            class="block w-full px-6 py-3 text-center bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700 transition">
                            Get Started Free
                        </Link>
                    </div>

                    <!-- Growth Plan -->
                    <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-200">
                        <h3 class="text-xl font-semibold mb-4">Growth</h3>
                        <p class="text-gray-600 mb-6">For growing organizations that need more capacity</p>
                        <div class="mb-6">
                            <span class="text-4xl font-bold">$29</span>
                            <span class="text-gray-500">/month</span>
                        </div>
                        <ul class="space-y-3 mb-8">
                            <li class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Up to 10 users
                            </li>
                            <li class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                5,000 donor records
                            </li>
                            <li class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                10 active campaigns
                            </li>
                            <li class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Advanced reporting
                            </li>
                            <li class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Email support
                            </li>
                        </ul>
                        <Link :href="route('register')"
                            class="block w-full px-6 py-3 text-center bg-gray-100 text-gray-800 rounded-lg font-medium hover:bg-gray-200 transition">
                            Upgrade When Needed
                        </Link>
                    </div>

                    <!-- Enterprise Plan -->
                    <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-200">
                        <h3 class="text-xl font-semibold mb-4">Enterprise</h3>
                        <p class="text-gray-600 mb-6">For large organizations with special requirements</p>
                        <div class="mb-6">
                            <span class="text-4xl font-bold">Custom</span>
                        </div>
                        <ul class="space-y-3 mb-8">
                            <li class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Unlimited users
                            </li>
                            <li class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                25,000+ donor records
                            </li>
                            <li class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Unlimited campaigns
                            </li>
                            <li class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Custom reporting
                            </li>
                            <li class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Priority support
                            </li>
                        </ul>
                        <a href="#contact"
                            class="block w-full px-6 py-3 text-center bg-gray-100 text-gray-800 rounded-lg font-medium hover:bg-gray-200 transition">
                            Contact Us
                        </a>
                    </div>
                </div>

                <div class="mt-12 text-center">
                    <p class="text-gray-600 mb-4">Our free plan is designed to handle most small to medium organizations. Upgrade only when you need more capacity.</p>
                    <p class="text-sm text-gray-500">Server resources are optimized to stay free as long as possible. Paid plans help cover costs when usage grows.</p>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-20 bg-indigo-600 text-white">
            <div class="container mx-auto px-6 text-center">
                <h2 class="text-3xl font-bold mb-6">Ready to Transform Your Donation Management?</h2>
                <p class="text-indigo-100 max-w-2xl mx-auto mb-8">
                    Join hundreds of organizations who trust OikkoSoft to manage their funds and donations efficiently.
                </p>
                <div class="flex flex-col sm:flex-row justify-center space-y-4 sm:space-y-0 sm:space-x-4">
                    <Link :href="route('register')"
                        class="inline-block px-8 py-3 bg-white text-indigo-600 rounded-lg font-medium hover:bg-gray-100 transition">
                    Start Your Free Trial
                    </Link>
                    <a href="#demo-video"
                        class="inline-block px-8 py-3 border border-white text-white rounded-lg font-medium hover:bg-white hover:text-indigo-600 transition flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v8a2 2 0 01-2 2h-2a2 2 0 01-2-2V6z"></path>
                        </svg>
                        Watch Demo
                    </a>
                </div>
            </div>
        </section>

        <!-- FAQ Section -->
        <section class="py-20 bg-gray-50">
            <div class="container mx-auto px-6">
                <div class="text-center mb-16">
                    <span class="inline-block px-3 py-1 text-sm font-semibold text-indigo-600 bg-indigo-50 rounded-full mb-4">FAQ</span>
                    <h2 class="text-3xl font-bold mb-4">Frequently Asked Questions</h2>
                    <p class="text-gray-600 max-w-2xl mx-auto">
                        Find answers to common questions about OikkoSoft and donation management.
                    </p>
                </div>

                <div class="max-w-3xl mx-auto">
                    <div class="space-y-4">
                        <!-- FAQ Item 1 (expanded by default) -->
                        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                            <button 
                                class="flex items-center justify-between w-full text-left"
                                @click="toggleFAQ(1)"
                            >
                                <h3 class="font-semibold text-lg">How can OikkoSoft be free?</h3>
                                <svg 
                                    class="w-5 h-5 text-indigo-600 transition-transform duration-200"
                                    :class="{ 'rotate-180': activeFAQ === 1 }"
                                    fill="none" 
                                    stroke="currentColor" 
                                    viewBox="0 0 24 24" 
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div 
                                class="mt-4 text-gray-600 transition-all duration-200 overflow-hidden"
                                :class="{ 'hidden': activeFAQ !== 1 }"
                            >
                                <p>We optimize our servers to keep costs low and offer a generous free tier. When organizations grow beyond the free limits, paid upgrades help cover the additional server costs while keeping the platform sustainable for everyone.</p>
                            </div>
                        </div>

                        <!-- FAQ Item 2 -->
                        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                            <button 
                                class="flex items-center justify-between w-full text-left"
                                @click="toggleFAQ(2)"
                            >
                                <h3 class="font-semibold text-lg">How does OikkoSoft handle donor data privacy?</h3>
                                <svg 
                                    class="w-5 h-5 text-indigo-600 transition-transform duration-200"
                                    :class="{ 'rotate-180': activeFAQ === 2 }"
                                    fill="none" 
                                    stroke="currentColor" 
                                    viewBox="0 0 24 24" 
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div 
                                class="mt-4 text-gray-600 transition-all duration-200 overflow-hidden"
                                :class="{ 'hidden': activeFAQ !== 2 }"
                            >
                                <p>OikkoSoft takes data privacy seriously. All donor information is encrypted both in transit and at rest. We comply with global data protection regulations including GDPR, and provide role-based access controls to ensure only authorized personnel can access sensitive donor information.</p>
                            </div>
                        </div>

                        <!-- FAQ Item 3 -->
                        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                            <button 
                                class="flex items-center justify-between w-full text-left"
                                @click="toggleFAQ(3)"
                            >
                                <h3 class="font-semibold text-lg">How does OikkoSoft ensure data security for donor information?</h3>
                                <svg 
                                    class="w-5 h-5 text-indigo-600 transition-transform duration-200"
                                    :class="{ 'rotate-180': activeFAQ === 3 }"
                                    fill="none" 
                                    stroke="currentColor" 
                                    viewBox="0 0 24 24" 
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div 
                                class="mt-4 text-gray-600 transition-all duration-200 overflow-hidden"
                                :class="{ 'hidden': activeFAQ !== 3 }"
                            >
                                <p>OikkoSoft takes donor privacy seriously. All sensitive donor data including contact information and donation history is:</p>
                                <ul class="mt-2 space-y-2 pl-5">
                                    <li class="flex items-start">
                                        <svg class="w-4 h-4 text-green-500 mt-1 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Encrypted both in transit (SSL/TLS) and at rest (AES-256)
                                    </li>
                                    <li class="flex items-start">
                                        <svg class="w-4 h-4 text-green-500 mt-1 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Protected by role-based access controls
                                    </li>
                                    <li class="flex items-start">
                                        <svg class="w-4 h-4 text-green-500 mt-1 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Backed up daily with point-in-time recovery
                                    </li>
                                    <li class="flex items-start">
                                        <svg class="w-4 h-4 text-green-500 mt-1 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Compliant with major data protection regulations
                                    </li>
                                </ul>
                                <p class="mt-3">We never share or sell your donor data to third parties.</p>
                            </div>
                        </div>

                        <!-- FAQ Item 4 -->
                        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                            <button 
                                class="flex items-center justify-between w-full text-left"
                                @click="toggleFAQ(4)"
                            >
                                <h3 class="font-semibold text-lg">How does the campaign adjustment feature work?</h3>
                                <svg 
                                    class="w-5 h-5 text-indigo-600 transition-transform duration-200"
                                    :class="{ 'rotate-180': activeFAQ === 4 }"
                                    fill="none" 
                                    stroke="currentColor" 
                                    viewBox="0 0 24 24" 
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div 
                                class="mt-4 text-gray-600 transition-all duration-200 overflow-hidden"
                                :class="{ 'hidden': activeFAQ !== 4 }"
                            >
                                <p>Our campaign adjustment feature allows you to transfer funds between your main fund and campaign funds while maintaining a complete audit trail. Each adjustment records who made the change, when, and why (with optional notes). This ensures complete transparency in your financial operations.</p>
                            </div>
                        </div>

                        <!-- FAQ Item 5 -->
                        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                            <button 
                                class="flex items-center justify-between w-full text-left"
                                @click="toggleFAQ(5)"
                            >
                                <h3 class="font-semibold text-lg">What payment methods can donors use?</h3>
                                <svg 
                                    class="w-5 h-5 text-indigo-600 transition-transform duration-200"
                                    :class="{ 'rotate-180': activeFAQ === 5 }"
                                    fill="none" 
                                    stroke="currentColor" 
                                    viewBox="0 0 24 24" 
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div 
                                class="mt-4 text-gray-600 transition-all duration-200 overflow-hidden"
                                :class="{ 'hidden': activeFAQ !== 5 }"
                            >
                                <p>OikkoSoft supports recording donations made via cash, check, bank transfer, and credit/debit cards (through our payment processor integrations). You can also record in-kind donations and pledges for future payments.</p>
                            </div>
                        </div>

                        <!-- FAQ Item 6 -->
                        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                            <button 
                                class="flex items-center justify-between w-full text-left"
                                @click="toggleFAQ(6)"
                            >
                                <h3 class="font-semibold text-lg">Is training included with my subscription?</h3>
                                <svg 
                                    class="w-5 h-5 text-indigo-600 transition-transform duration-200"
                                    :class="{ 'rotate-180': activeFAQ === 6 }"
                                    fill="none" 
                                    stroke="currentColor" 
                                    viewBox="0 0 24 24" 
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div 
                                class="mt-4 text-gray-600 transition-all duration-200 overflow-hidden"
                                :class="{ 'hidden': activeFAQ !== 6 }"
                            >
                                <p>Yes! All plans include access to our comprehensive knowledge base and video tutorials. Professional and Enterprise plans include onboarding sessions with our team. Enterprise customers receive dedicated training sessions tailored to their organization's needs.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-gray-900 text-gray-400 pt-12 pb-8">
            <div class="container mx-auto px-6">
                <div class="grid md:grid-cols-4 gap-8 mb-12">
                    <div>
                        <div class="flex items-center mb-4">
                            <a href="/">
                                <img src="/images/logo.png" alt="Logo" class="h-10 bg-white">
                            </a>
                        </div>
                        <p class="text-sm mb-4">
                            Comprehensive solution for managing donations, funds, and campaigns for your organization.
                        </p>
                        <div class="flex space-x-4">
                            <a href="#" class="text-gray-400 hover:text-white transition">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"></path>
                                </svg>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-white transition">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"></path>
                                </svg>
                            </a>
                            <a href="https://www.linkedin.com/in/shakil-miah-dev" target="_blank" class="text-gray-400 hover:text-white transition">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"></path>
                                </svg>
                            </a>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-white font-semibold mb-4">Product</h3>
                        <ul class="space-y-2">
                            <li><a href="#features" class="hover:text-white transition">Features</a></li>
                            <li><a href="#solutions" class="hover:text-white transition">Solutions</a></li>
                            <li><a href="#pricing" class="hover:text-white transition">Pricing</a></li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="text-white font-semibold mb-4">Resources</h3>
                        <ul class="space-y-2">
                            <li><a href="#" class="hover:text-white transition">Documentation</a></li>
                            <li><a href="#" class="hover:text-white transition">Guides</a></li>
                            <li><a href="#" class="hover:text-white transition">Blog</a></li>
                            <li><a href="#" class="hover:text-white transition">Webinars</a></li>
                            <li><a href="#" class="hover:text-white transition">Support</a></li>
                        </ul>
                    </div>

                    <div id="contact">
                        <h3 class="text-white font-semibold mb-4">Contact Us</h3>
                        <ul class="space-y-3">
                            <li class="flex items-start">
                                <svg class="w-5 h-5 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                <span>shakilmiahr55@gmail.com</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-5 h-5 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                                <span>01303895377</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-5 h-5 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <span>Dhanmondi, Dhaka</span>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="border-gray-800 pt-8">
                    <CopyRight />
                </div>
            </div>
        </footer>
    </div>
</template>