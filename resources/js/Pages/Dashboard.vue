<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

interface Transaction {
  id: number
  date: string
  description: string
  amount: number
  type: string
  category: string
  account_name: string
}

interface Metrics {
  current_revenue: number
  current_expenses: number
  net_profit: number
  cash_flow_30_days: number
  outstanding_invoices_count: number
  outstanding_invoices_amount: number
  upcoming_bills_count: number
  upcoming_bills_amount: number
}

interface Props {
  metrics?: Metrics
  recent_transactions?: Transaction[]
}

const props = defineProps<Props>()

const formatCurrency = (amount: number | null | undefined) => {
  if (amount === null || amount === undefined || isNaN(amount)) {
    return '₱0.00'
  }
  return '₱' + new Intl.NumberFormat('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  }).format(amount)
}

const getTypeBadgeClass = (type: string) => {
  const baseClass = 'font-semibold'
  if (type === 'income') return `${baseClass} bg-green-100 text-green-800`
  if (type === 'expense') return `${baseClass} bg-red-100 text-red-800`
  if (type === 'transfer') return `${baseClass} bg-blue-100 text-blue-800`
  return baseClass
}
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Finance Overview Section -->
                <div v-if="props.metrics" class="mb-8">
                    <div class="mb-6">
                        <h3 class="text-2xl font-bold text-gray-900">Financial Overview</h3>
                        <p class="text-gray-600 mt-1">Current month financial metrics</p>
                    </div>

                    <!-- Key Metrics Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
                            <p class="text-sm text-gray-600 mb-2">Total Revenue</p>
                            <p class="text-3xl font-bold text-gray-900">{{ formatCurrency(props.metrics.current_revenue) }}</p>
                            <p class="text-xs text-gray-500 mt-2">Current month</p>
                        </div>
                        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-red-500">
                            <p class="text-sm text-gray-600 mb-2">Total Expenses</p>
                            <p class="text-3xl font-bold text-gray-900">{{ formatCurrency(props.metrics.current_expenses) }}</p>
                            <p class="text-xs text-gray-500 mt-2">Current month</p>
                        </div>
                        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
                            <p class="text-sm text-gray-600 mb-2">Net Profit</p>
                            <p class="text-3xl font-bold text-gray-900">{{ formatCurrency(props.metrics.net_profit) }}</p>
                            <p class="text-xs text-gray-500 mt-2">Current month</p>
                        </div>
                        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
                            <p class="text-sm text-gray-600 mb-2">Cash Flow</p>
                            <p class="text-3xl font-bold text-gray-900">{{ formatCurrency(props.metrics.cash_flow_30_days) }}</p>
                            <p class="text-xs text-gray-500 mt-2">Last 30 days</p>
                        </div>
                    </div>

                    <!-- AR/AP Summary -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                        <div class="bg-white rounded-lg shadow p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-lg font-semibold text-gray-900">Outstanding Invoices</h4>
                                <span class="bg-blue-100 text-blue-800 text-sm font-semibold px-3 py-1 rounded-full">
                                    {{ props.metrics.outstanding_invoices_count }}
                                </span>
                            </div>
                            <p class="text-2xl font-bold text-gray-900">{{ formatCurrency(props.metrics.outstanding_invoices_amount) }}</p>
                            <p class="text-sm text-gray-600 mt-2">Awaiting payment</p>
                            <Link
                                href="/finance/invoices-bills"
                                class="text-blue-600 hover:text-blue-800 text-sm font-semibold mt-4 inline-block"
                            >
                                View Details →
                            </Link>
                        </div>
                        <div class="bg-white rounded-lg shadow p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-lg font-semibold text-gray-900">Upcoming Bills</h4>
                                <span class="bg-orange-100 text-orange-800 text-sm font-semibold px-3 py-1 rounded-full">
                                    {{ props.metrics.upcoming_bills_count }}
                                </span>
                            </div>
                            <p class="text-2xl font-bold text-gray-900">{{ formatCurrency(props.metrics.upcoming_bills_amount) }}</p>
                            <p class="text-sm text-gray-600 mt-2">Due within 30 days</p>
                            <Link
                                href="/finance/invoices-bills"
                                class="text-blue-600 hover:text-blue-800 text-sm font-semibold mt-4 inline-block"
                            >
                                View Details →
                            </Link>
                        </div>
                    </div>

                    <!-- Recent Transactions -->
                    <div v-if="props.recent_transactions && props.recent_transactions.length > 0" class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-lg font-semibold text-gray-900">Recent Transactions</h4>
                            <Link
                                href="/finance/transactions"
                                class="text-blue-600 hover:text-blue-800 text-sm font-semibold"
                            >
                                View All →
                            </Link>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50 border-b border-gray-200">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Date</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Description</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Category</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Type</th>
                                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700">Amount</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <tr v-for="transaction in props.recent_transactions.slice(0, 5)" :key="transaction.id" class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm text-gray-700">{{ transaction.date }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-700">{{ transaction.description }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-700">
                                            <span class="bg-gray-100 text-gray-800 text-xs px-2 py-1 rounded">
                                                {{ transaction.category }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            <span :class="['px-3 py-1 rounded-full text-xs', getTypeBadgeClass(transaction.type)]">
                                                {{ transaction.type.charAt(0).toUpperCase() + transaction.type.slice(1) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-right font-semibold text-gray-900">
                                            {{ formatCurrency(transaction.amount) }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Fallback when no finance data -->
                <div v-else class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <p class="mb-4">Welcome to your dashboard!</p>
                        <Link
                            href="/finance/dashboard"
                            class="text-blue-600 hover:text-blue-800 font-semibold"
                        >
                            View Finance Dashboard →
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
