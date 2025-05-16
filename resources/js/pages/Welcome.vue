<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import CopyRight from '@/components/CopyRight.vue';
</script>

<template>
    <Head title="OikkoSoft - Donation Management Platform">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
        <meta name="description"
            content="Comprehensive solution for managing donations, funds, and campaigns for your organization">
    </Head>

    <div class="min-h-screen bg-gradient-to-b from-gray-50 to-white text-gray-900">
        <!-- Header -->
        <header class="sticky top-0 z-50 bg-white/80 backdrop-blur-sm border-b border-gray-200">
            <div class="container mx-auto px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                        <span class="ml-2 text-xl font-bold text-gray-900">OikkoSoft</span>
                    </div>
                    <nav class="hidden md:flex items-center space-x-8">
                        <a href="#features" class="text-gray-600 hover:text-indigo-600 transition">Features</a>
                        <a href="#how-it-works" class="text-gray-600 hover:text-indigo-600 transition">How It Works</a>
                        <a href="#pricing" class="text-gray-600 hover:text-indigo-600 transition">Pricing</a>
                        <a href="#contact" class="text-gray-600 hover:text-indigo-600 transition">Contact</a>
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
                        Streamline Your <span class="text-indigo-600">Donation Management</span>
                    </h1>
                    <p class="text-lg text-gray-600 mb-8">
                        OikkoSoft provides comprehensive tools for organizations to manage donations, track funds, and run
                        campaigns efficiently.
                    </p>
                    <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
                        <Link :href="route('register')"
                            class="px-8 py-3 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700 transition">
                        Start Free Trial
                        </Link>
                        <a href="#features"
                            class="px-8 py-3 border border-gray-300 rounded-lg font-medium hover:border-indigo-400 transition">
                            Learn More
                        </a>
                    </div>
                </div>
                <div class="md:w-1/2">
                    <img src="/images/dashboard-preview.png" alt="OikkoSoft Dashboard Preview"
                        class="rounded-xl shadow-xl border border-gray-200">
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section id="features" class="py-20 bg-gray-50">
            <div class="container mx-auto px-6">
                <div class="text-center mb-16">
                    <h2 class="text-3xl font-bold mb-4">Powerful Features for Your Organization</h2>
                    <p class="text-gray-600 max-w-2xl mx-auto">
                        Designed to simplify fund management and donation tracking for nonprofits and charitable
                        organizations.
                    </p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div class="bg-white p-8 rounded-xl shadow-sm hover:shadow-md transition">
                        <div class="w-12 h-12 bg-indigo-50 rounded-lg flex items-center justify-center mb-6">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-3">Fund Management</h3>
                        <p class="text-gray-600">
                            Easily create and manage different funds (main and campaign) with detailed tracking of all
                            transactions.
                        </p>
                    </div>

                    <div class="bg-white p-8 rounded-xl shadow-sm hover:shadow-md transition">
                        <div class="w-12 h-12 bg-indigo-50 rounded-lg flex items-center justify-center mb-6">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-3">Campaign Adjustments</h3>
                        <p class="text-gray-600">
                            Transfer funds between main and campaign accounts with full audit trails and notes for
                            transparency.
                        </p>
</div>

                    <div class="bg-white p-8 rounded-xl shadow-sm hover:shadow-md transition">
                        <div class="w-12 h-12 bg-indigo-50 rounded-lg flex items-center justify-center mb-6">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-3">Donation Tracking</h3>
                        <p class="text-gray-600">
                            Record and categorize all donations with donor information, payment methods, and attachments.
                        </p>
                    </div>

                    <div class="bg-white p-8 rounded-xl shadow-sm hover:shadow-md transition">
                        <div class="w-12 h-12 bg-indigo-50 rounded-lg flex items-center justify-center mb-6">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-3">Role-Based Access</h3>
                        <p class="text-gray-600">
                            Granular permissions system to control what each team member can access and modify.
                        </p>
                    </div>

                    <div class="bg-white p-8 rounded-xl shadow-sm hover:shadow-md transition">
                        <div class="w-12 h-12 bg-indigo-50 rounded-lg flex items-center justify-center mb-6">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-3">Comprehensive Reports</h3>
                        <p class="text-gray-600">
                            Generate detailed reports on donations, fund balances, and campaign performance.
                        </p>
                    </div>

                    <div class="bg-white p-8 rounded-xl shadow-sm hover:shadow-md transition">
                        <div class="w-12 h-12 bg-indigo-50 rounded-lg flex items-center justify-center mb-6">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-3">Multi-Currency Support</h3>
                        <p class="text-gray-600">
                            Handle donations in multiple currencies with organization-level currency settings.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- How It Works Section -->
        <section id="how-it-works" class="py-20">
            <div class="container mx-auto px-6">
                <div class="text-center mb-16">
                    <h2 class="text-3xl font-bold mb-4">How OikkoSoft Works</h2>
                    <p class="text-gray-600 max-w-2xl mx-auto">
                        A simple yet powerful workflow to manage your organization's financial operations.
                    </p>
                </div>

                <div class="relative">
                    <div class="hidden lg:block absolute top-0 left-1/2 h-full w-1 bg-gray-200"></div>

                    <div class="grid lg:grid-cols-2 gap-12">
                        <!-- Step 1 -->
                        <div class="lg:pr-20">
                            <div class="flex items-center mb-6">
                                <div
                                    class="flex items-center justify-center w-12 h-12 rounded-full bg-indigo-100 text-indigo-600 font-bold text-lg mr-4">
                                    1</div>
                                <h3 class="text-xl font-semibold">Set Up Your Organization</h3>
                            </div>
                            <p class="text-gray-600 mb-6">
                                Create your organization profile with logo, contact information, and preferred currency.
                                Invite team members and assign roles with appropriate permissions.
                            </p>
                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <p class="text-sm text-gray-600 font-medium">"We had our organization set up in minutes, and
                                    our team was ready to go."</p>
                            </div>
                        </div>

                        <!-- Step 2 -->
                        <div class="lg:pl-20 lg:mt-20">
                            <div class="flex items-center mb-6">
                                <div
                                    class="flex items-center justify-center w-12 h-12 rounded-full bg-indigo-100 text-indigo-600 font-bold text-lg mr-4">
                                    2</div>
                                <h3 class="text-xl font-semibold">Create Funds & Campaigns</h3>
                            </div>
                            <p class="text-gray-600 mb-6">
                                Establish your main fund and create campaigns for specific initiatives. Define goals,
                                descriptions, and timelines for each campaign.
                            </p>
                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <p class="text-sm text-gray-600 font-medium">"The ability to track separate campaigns while
                                    seeing the big picture is invaluable."</p>
                            </div>
                        </div>

                        <!-- Step 3 -->
                        <div class="lg:pr-20 lg:mt-20">
                            <div class="flex items-center mb-6">
                                <div
                                    class="flex items-center justify-center w-12 h-12 rounded-full bg-indigo-100 text-indigo-600 font-bold text-lg mr-4">
                                    3</div>
                                <h3 class="text-xl font-semibold">Record Donations</h3>
                            </div>
                            <p class="text-gray-600 mb-6">
                                Add donors to your database and record their contributions. Attach receipts, specify payment
                                methods, and allocate to specific funds or campaigns.
                            </p>
                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <p class="text-sm text-gray-600 font-medium">"Donor management became so much easier with
                                    all information in one place."</p>
                            </div>
                        </div>

                        <!-- Step 4 -->
                        <div class="lg:pl-20 lg:mt-20">
                            <div class="flex items-center mb-6">
                                <div
                                    class="flex items-center justify-center w-12 h-12 rounded-full bg-indigo-100 text-indigo-600 font-bold text-lg mr-4">
                                    4</div>
                                <h3 class="text-xl font-semibold">Manage & Report</h3>
                            </div>
                            <p class="text-gray-600 mb-6">
                                Transfer funds between accounts as needed, generate reports for stakeholders, and maintain
                                complete transparency in all financial operations.
                            </p>
                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <p class="text-sm text-gray-600 font-medium">"Our board members love the clear reports we
                                    can generate for meetings."</p>
                            </div>
                        </div>
                    </div>
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
                <Link :href="route('register')"
                    class="inline-block px-8 py-3 bg-white text-indigo-600 rounded-lg font-medium hover:bg-gray-100 transition">
                Start Your Free Trial
                </Link>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-gray-900 text-gray-400 pt-12 pb-2">
            <div class="container mx-auto px-6">
                <div class="grid md:grid-cols-4 gap-8">
                    <div>
                        <div class="flex items-center mb-4">
                            <svg class="w-8 h-8 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                            <span class="ml-2 text-xl font-bold text-white">OikkoSoft</span>
                        </div>
                        <p class="text-sm mb-4">
                            Comprehensive solution for managing donations, funds, and campaigns for your organization.
                        </p>
                        <div class="flex space-x-4">
                            <a href="#" class="text-gray-400 hover:text-white transition">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z">
                                    </path>
                                </svg>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-white transition">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z">
                                    </path>
                                </svg>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-white transition">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z">
                                    </path>
                                </svg>
                            </a>
                        </div>
</div>

                    <div>
                        <h3 class="text-white font-semibold mb-4">Product</h3>
                        <ul class="space-y-2">
                            <li><a href="#features" class="hover:text-white transition">Features</a></li>
                            <li><a href="#how-it-works" class="hover:text-white transition">How It Works</a></li>
                            <li><a href="#pricing" class="hover:text-white transition">Pricing</a></li>

                 <li><a href="#" class="hover:text-white transition">API</a></li>
                        </ul>
                </div>

                <div>
                    <h3 class="text-white font-semibold mb-4">Company</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="hover:text-white transition">About</a></li>
                        <li><a href="#" class="hover:text-white transition">Blog</a></li>
                        <li><a href="#" class="hover:text-white transition">Careers</a></li>
                        <li><a href="#" class="hover:text-white transition">Contact</a></li>
                    </ul>
                </div>

                <div id="contact">
                    <h3 class="text-white font-semibold mb-4">Contact Us</h3>
                    <ul class="space-y-2">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                </path>
                            </svg>
                            <span>support@oikkosoft.com</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                </path>
                            </svg>
                            <span>+1 (555) 123-4567</span>
                        </li>
                    </ul>
                </div>
            </div>
            <CopyRight />
        </div>
    </footer>
</div></template>
