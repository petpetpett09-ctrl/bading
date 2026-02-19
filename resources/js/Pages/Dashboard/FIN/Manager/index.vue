<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { CreditCard, TrendingUp, FileText, BarChart3, ArrowUpRight, ArrowDownLeft } from 'lucide-vue-next';

defineProps({
    user: Object,
    stats: Object,
    reports: Array,
});
</script>

<template>

    <Head title="Finance Dashboard" />
    <AuthenticatedLayout>
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Finance Manager Dashboard</h1>
            <p class="text-gray-500 dark:text-gray-400">Monitor your financial metrics and key performance indicators</p>
        </div>

        <!-- Main Navigation Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Dashboard Card -->
            <Link :href="route('finance.dashboard')" class="group">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-md transition-all">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-xl">
                            <TrendingUp class="h-6 w-6 text-blue-600 dark:text-blue-400" />
                        </div>
                        <span class="text-xs font-bold text-blue-600 bg-blue-50 dark:bg-blue-900/20 px-3 py-1 rounded-full">View</span>
                    </div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Dashboard</p>
                    <p class="text-lg font-bold text-gray-900 dark:text-white mt-1">Overview</p>
                </div>
            </Link>

            <!-- Transactions Card -->
            <Link :href="route('finance.transactions.index')" class="group">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-md transition-all">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl">
                            <CreditCard class="h-6 w-6 text-emerald-600 dark:text-emerald-400" />
                        </div>
                        <span class="text-xs font-bold text-emerald-600 bg-emerald-50 dark:bg-emerald-900/20 px-3 py-1 rounded-full">Track</span>
                    </div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Transactions</p>
                    <p class="text-lg font-bold text-gray-900 dark:text-white mt-1">Income & Expenses</p>
                </div>
            </Link>

            <!-- Invoices & Bills Card -->
            <Link :href="route('finance.invoices-bills.index')" class="group">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-md transition-all">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-amber-50 dark:bg-amber-900/20 rounded-xl">
                            <FileText class="h-6 w-6 text-amber-600 dark:text-amber-400" />
                        </div>
                        <span class="text-xs font-bold text-amber-600 bg-amber-50 dark:bg-amber-900/20 px-3 py-1 rounded-full">Manage</span>
                    </div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Invoices & Bills</p>
                    <p class="text-lg font-bold text-gray-900 dark:text-white mt-1">Receivables</p>
                </div>
            </Link>

            <!-- Reports Card -->
            <Link :href="route('finance.reports.index')" class="group">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-md transition-all">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-purple-50 dark:bg-purple-900/20 rounded-xl">
                            <BarChart3 class="h-6 w-6 text-purple-600 dark:text-purple-400" />
                        </div>
                        <span class="text-xs font-bold text-purple-600 bg-purple-50 dark:bg-purple-900/20 px-3 py-1 rounded-full">Analyze</span>
                    </div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Reports</p>
                    <p class="text-lg font-bold text-gray-900 dark:text-white mt-1">Analytics</p>
                </div>
            </Link>
        </div>

        <!-- Financial Overview -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-50 dark:border-gray-700">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">Financial Overview</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Current financial status and key metrics</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 p-6">
                <div class="p-6 bg-blue-50 dark:bg-blue-900/10 rounded-2xl border border-blue-100 dark:border-blue-900/20">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Revenue</p>
                        <ArrowUpRight class="h-4 w-4 text-emerald-600" />
                    </div>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">$0.00</p>
                    <p class="text-xs text-gray-500 mt-2">Current Period</p>
                </div>
                <div class="p-6 bg-red-50 dark:bg-red-900/10 rounded-2xl border border-red-100 dark:border-red-900/20">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Expenses</p>
                        <ArrowDownLeft class="h-4 w-4 text-red-600" />
                    </div>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">$0.00</p>
                    <p class="text-xs text-gray-500 mt-2">Current Period</p>
                </div>
                <div class="p-6 bg-emerald-50 dark:bg-emerald-900/10 rounded-2xl border border-emerald-100 dark:border-emerald-900/20">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Net Profit</p>
                        <TrendingUp class="h-4 w-4 text-emerald-600" />
                    </div>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">$0.00</p>
                    <p class="text-xs text-gray-500 mt-2">Current Period</p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>