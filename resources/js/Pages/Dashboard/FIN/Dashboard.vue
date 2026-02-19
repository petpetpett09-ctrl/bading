<template>
  <AuthenticatedLayout>
    <Head title="Finance Dashboard" />
    
    <div class="mb-8">
      <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Finance Dashboard</h1>
      <p class="text-gray-500 dark:text-gray-400">Monitor your financial metrics and key performance indicators</p>
    </div>

    <!-- Key Metrics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm">
        <div class="flex items-center justify-between mb-4">
          <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-xl">
            <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
          <span class="text-xs font-bold text-emerald-600 bg-emerald-50 dark:bg-emerald-900/20 px-3 py-1 rounded-full">↑ 12%</span>
        </div>
        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Revenue</p>
        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ formatCurrency(props.metrics.current_revenue) }}</p>
      </div>

      <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm">
        <div class="flex items-center justify-between mb-4">
          <div class="p-3 bg-red-50 dark:bg-red-900/20 rounded-xl">
            <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4m0-4l4-4"/>
            </svg>
          </div>
          <span class="text-xs font-bold text-red-600 bg-red-50 dark:bg-red-900/20 px-3 py-1 rounded-full">↑ 8%</span>
        </div>
        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Expenses</p>
        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ formatCurrency(props.metrics.current_expenses) }}</p>
      </div>

      <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm">
        <div class="flex items-center justify-between mb-4">
          <div class="p-3 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl">
            <svg class="h-6 w-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4m0 0L5 7m0 0v8"/>
            </svg>
          </div>
          <span class="text-xs font-bold text-emerald-600 bg-emerald-50 dark:bg-emerald-900/20 px-3 py-1 rounded-full">Optimal</span>
        </div>
        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Net Profit</p>
        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ formatCurrency(props.metrics.net_profit) }}</p>
      </div>

      <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm">
        <div class="flex items-center justify-between mb-4">
          <div class="p-3 bg-purple-50 dark:bg-purple-900/20 rounded-xl">
            <svg class="h-6 w-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v2.757a1 1 0 00.553.894l1.447.724a1 1 0 001.553-.894V4a2 2 0 00-2-2H4a2 2 0 00-2 2v14a2 2 0 002 2h13"/>
            </svg>
          </div>
          <span class="text-xs font-bold text-purple-600 bg-purple-50 dark:bg-purple-900/20 px-3 py-1 rounded-full">30 Days</span>
        </div>
        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Cash Flow</p>
        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ formatCurrency(props.metrics.cash_flow_30_days) }}</p>
      </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
      <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-50 dark:border-gray-700">
          <h3 class="text-lg font-bold text-gray-900 dark:text-white">Outstanding Invoices</h3>
          <p class="text-sm text-gray-500 dark:text-gray-400">Accounts Receivable</p>
        </div>
        <div class="p-6">
          <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
              <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Count</p>
              <p class="text-3xl font-bold text-yellow-600 dark:text-yellow-400">{{ props.metrics.outstanding_invoices_count }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Total Amount</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ formatCurrency(props.metrics.outstanding_invoices_amount) }}</p>
            </div>
          </div>
          <Link href="/finance/invoices-bills" class="text-blue-600 dark:text-blue-400 text-sm font-semibold hover:text-blue-700 dark:hover:text-blue-300">
            View all →
          </Link>
        </div>
      </div>

      <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-50 dark:border-gray-700">
          <h3 class="text-lg font-bold text-gray-900 dark:text-white">Upcoming Bills</h3>
          <p class="text-sm text-gray-500 dark:text-gray-400">Accounts Payable</p>
        </div>
        <div class="p-6">
          <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
              <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Count</p>
              <p class="text-3xl font-bold text-orange-600 dark:text-orange-400">{{ props.metrics.upcoming_bills_count }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Total Amount</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ formatCurrency(props.metrics.upcoming_bills_amount) }}</p>
            </div>
          </div>
          <Link href="/finance/invoices-bills" class="text-blue-600 dark:text-blue-400 text-sm font-semibold hover:text-blue-700 dark:hover:text-blue-300">
            View all →
          </Link>
        </div>
      </div>
    </div>

    <!-- Recent Transactions -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
      <div class="p-6 border-b border-gray-50 dark:border-gray-700 flex items-center justify-between">
        <div>
          <h3 class="text-lg font-bold text-gray-900 dark:text-white">Recent Transactions</h3>
          <p class="text-sm text-gray-500 dark:text-gray-400">Latest financial activity</p>
        </div>
        <Link href="/finance/transactions" class="text-blue-600 dark:text-blue-400 text-sm font-semibold hover:text-blue-700 dark:hover:text-blue-300">
          View all →
        </Link>
      </div>

      <div v-if="props.recent_transactions.length > 0" class="overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr class="bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700">
              <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest text-left">Date</th>
              <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest text-left">Description</th>
              <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest text-left">Category</th>
              <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest text-left">Type</th>
              <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest text-right">Amount</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
            <tr v-for="txn in props.recent_transactions" :key="txn.id" class="hover:bg-gray-50 dark:hover:bg-gray-900/30 transition-colors">
              <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ txn.date }}</td>
              <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">{{ txn.description }}</td>
              <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ txn.category }}</td>
              <td class="px-6 py-4 text-sm">
                <span :class="getTypeBadgeClass(txn.type)" class="px-3 py-1 rounded-full text-xs font-semibold">
                  {{ txn.type.charAt(0).toUpperCase() + txn.type.slice(1) }}
                </span>
              </td>
              <td class="px-6 py-4 text-sm text-right font-bold text-gray-900 dark:text-white">
                {{ formatCurrency(txn.amount) }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div v-else class="p-12 text-center text-gray-500 dark:text-gray-400">
        <p class="text-sm">No recent transactions</p>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

interface DashboardMetrics {
  current_revenue: number
  current_expenses: number
  net_profit: number
  cash_flow_30_days: number
  outstanding_invoices_count: number
  outstanding_invoices_amount: number
  upcoming_bills_count: number
  upcoming_bills_amount: number
}

interface Transaction {
  id: number
  date: string
  description: string
  category: string
  type: 'income' | 'expense' | 'transfer'
  amount: number
  account_name?: string
}

interface Props {
  metrics: DashboardMetrics
  recent_transactions: Transaction[]
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
