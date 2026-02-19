<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { CreditCard, TrendingUp, FileText, BarChart3 } from 'lucide-vue-next';

defineProps({
    user: Object,
    stats: Object,
    reports: Array,
});
</script>

<template>

    <Head title="Finance Dashboard" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Finance Portal
            </h2>
        </template>
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Main Navigation -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Dashboard Card -->
                    <Link :href="route('finance.dashboard')" class="group">
                        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow cursor-pointer border border-blue-200">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-800">Dashboard</h3>
                                <TrendingUp class="w-6 h-6 text-blue-600" />
                            </div>
                            <p class="text-sm text-gray-600">View financial metrics and overview</p>
                        </div>
                    </Link>

                    <!-- Transactions Card -->
                    <Link :href="route('finance.transactions.index')" class="group">
                        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow cursor-pointer border border-green-200">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-800">Transactions</h3>
                                <CreditCard class="w-6 h-6 text-green-600" />
                            </div>
                            <p class="text-sm text-gray-600">Manage income and expenses</p>
                        </div>
                    </Link>

                    <!-- Invoices & Bills Card -->
                    <Link :href="route('finance.invoices-bills.index')" class="group">
                        <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow cursor-pointer border border-orange-200">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-800">Invoices & Bills</h3>
                                <FileText class="w-6 h-6 text-orange-600" />
                            </div>
                            <p class="text-sm text-gray-600">Track receivables and payables</p>
                        </div>
                    </Link>

                    <!-- Reports Card -->
                    <Link :href="route('finance.reports.index')" class="group">
                        <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow cursor-pointer border border-purple-200">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-800">Reports</h3>
                                <BarChart3 class="w-6 h-6 text-purple-600" />
                            </div>
                            <p class="text-sm text-gray-600">Generate financial reports</p>
                        </div>
                    </Link>
                </div>

                <!-- Quick Stats -->
                <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Stats</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="p-4 bg-blue-50 rounded-lg border border-blue-200">
                            <p class="text-sm text-gray-600 mb-1">Total Revenue</p>
                            <p class="text-2xl font-bold text-blue-600">$0.00</p>
                        </div>
                        <div class="p-4 bg-red-50 rounded-lg border border-red-200">
                            <p class="text-sm text-gray-600 mb-1">Total Expenses</p>
                            <p class="text-2xl font-bold text-red-600">$0.00</p>
                        </div>
                        <div class="p-4 bg-green-50 rounded-lg border border-green-200">
                            <p class="text-sm text-gray-600 mb-1">Net Profit</p>
                            <p class="text-2xl font-bold text-green-600">$0.00</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>