<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 p-6">
    <Head title="Finance Dashboard" />

    <div class="max-w-7xl mx-auto">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-4xl font-bold text-slate-900 mb-2">Finance Dashboard</h1>
        <p class="text-slate-600">Monitor your financial metrics and recent activity</p>
      </div>

      <!-- Metrics Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Revenue -->
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-medium text-slate-600">Total Revenue</h3>
            <svg
              class="w-6 h-6 text-blue-500"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
              />
            </svg>
          </div>
          <p class="text-3xl font-bold text-slate-900">{{ formatCurrency(metrics.current_revenue) }}</p>
          <p class="text-xs text-slate-500 mt-2">Current Month</p>
        </div>

        <!-- Total Expenses -->
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-red-500">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-medium text-slate-600">Total Expenses</h3>
            <svg
              class="w-6 h-6 text-red-500"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M13 10V3L4 14h7v7l9-11h-7z"
              />
            </svg>
          </div>
          <p class="text-3xl font-bold text-slate-900">{{ formatCurrency(metrics.current_expenses) }}</p>
          <p class="text-xs text-slate-500 mt-2">Current Month</p>
        </div>

        <!-- Net Profit -->
        <div
          class="bg-white rounded-lg shadow p-6 border-l-4"
          :class="metrics.net_profit >= 0 ? 'border-green-500' : 'border-orange-500'"
        >
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-medium text-slate-600">Net Profit</h3>
            <svg
              class="w-6 h-6"
              :class="metrics.net_profit >= 0 ? 'text-green-500' : 'text-orange-500'"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"
              />
            </svg>
          </div>
          <p class="text-3xl font-bold text-slate-900">{{ formatCurrency(metrics.net_profit) }}</p>
          <p class="text-xs text-slate-500 mt-2">Current Month</p>
        </div>

        <!-- Cash Flow -->
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-medium text-slate-600">Cash Flow (30 Days)</h3>
            <svg
              class="w-6 h-6 text-purple-500"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
              />
            </svg>
          </div>
          <p class="text-3xl font-bold text-slate-900">{{ formatCurrency(metrics.cash_flow_30_days) }}</p>
          <p class="text-xs text-slate-500 mt-2">Net Movement</p>
        </div>
      </div>

      <!-- Outstanding & Upcoming -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <!-- Outstanding Invoices -->
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
          <h3 class="text-lg font-semibold text-slate-900 mb-4 flex items-center">
            <span class="text-2xl mr-2">📊</span> Outstanding Invoices
          </h3>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <p class="text-sm text-slate-600 mb-1">Count</p>
              <p class="text-3xl font-bold text-yellow-600">{{ metrics.outstanding_invoices_count }}</p>
            </div>
            <div>
              <p class="text-sm text-slate-600 mb-1">Total Amount</p>
              <p class="text-3xl font-bold text-slate-900">{{ formatCurrency(metrics.outstanding_invoices_amount) }}</p>
            </div>
          </div>
          <Link
            :href="route('finance.invoices-bills.index')"
            class="text-blue-600 text-sm mt-4 inline-block hover:text-blue-800"
          >
            View all invoices →
          </Link>
        </div>

        <!-- Upcoming Bills -->
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-red-500">
          <h3 class="text-lg font-semibold text-slate-900 mb-4 flex items-center">
            <span class="text-2xl mr-2">📋</span> Upcoming Bills
          </h3>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <p class="text-sm text-slate-600 mb-1">Count</p>
              <p class="text-3xl font-bold text-red-600">{{ metrics.upcoming_bills_count }}</p>
            </div>
            <div>
              <p class="text-sm text-slate-600 mb-1">Total Amount</p>
              <p class="text-3xl font-bold text-slate-900">{{ formatCurrency(metrics.upcoming_bills_amount) }}</p>
            </div>
          </div>
          <Link
            :href="route('finance.invoices-bills.index')"
            class="text-blue-600 text-sm mt-4 inline-block hover:text-blue-800"
          >
            View all bills →
          </Link>
        </div>
      </div>

      <!-- Charts -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Revenue Trend -->
        <div class="bg-white rounded-lg shadow p-6">
          <h3 class="text-lg font-semibold text-slate-900 mb-4">Revenue Trend (12 Months)</h3>
          <div class="h-64 bg-slate-50 rounded flex items-center justify-center">
            <p class="text-slate-500">Chart visualization coming soon</p>
          </div>
        </div>

        <!-- Expense Trend -->
        <div class="bg-white rounded-lg shadow p-6">
          <h3 class="text-lg font-semibold text-slate-900 mb-4">Expense Trend (12 Months)</h3>
          <div class="h-64 bg-slate-50 rounded flex items-center justify-center">
            <p class="text-slate-500">Chart visualization coming soon</p>
          </div>
        </div>
      </div>

      <!-- Recent Transactions -->
      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold text-slate-900">Recent Transactions</h3>
          <Link :href="route('finance.transactions.index')" class="text-blue-600 text-sm hover:text-blue-800">
            View all →
          </Link>
        </div>

        <div v-if="recent_transactions.length > 0" class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead>
              <tr class="border-b border-slate-200">
                <th class="text-left py-3 px-4 font-medium text-slate-700">Date</th>
                <th class="text-left py-3 px-4 font-medium text-slate-700">Description</th>
                <th class="text-left py-3 px-4 font-medium text-slate-700">Category</th>
                <th class="text-left py-3 px-4 font-medium text-slate-700">Type</th>
                <th class="text-right py-3 px-4 font-medium text-slate-700">Amount</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="txn in recent_transactions" :key="txn.id" class="border-b border-slate-100 hover:bg-slate-50">
                <td class="py-3 px-4 text-slate-600">{{ txn.date }}</td>
                <td class="py-3 px-4 text-slate-900">{{ txn.description }}</td>
                <td class="py-3 px-4 text-slate-600">{{ txn.category }}</td>
                <td class="py-3 px-4">
                  <span
                    class="px-2 py-1 rounded text-xs font-medium"
                    :class="
                      txn.type === 'income'
                        ? 'bg-green-100 text-green-800'
                        : txn.type === 'expense'
                          ? 'bg-red-100 text-red-800'
                          : 'bg-blue-100 text-blue-800'
                    "
                  >
                    {{ txn.type }}
                  </span>
                </td>
                <td
                  class="py-3 px-4 text-right font-semibold"
                  :class="txn.type === 'income' ? 'text-green-600' : 'text-red-600'"
                >
                  {{ txn.type === 'income' ? '+' : '-' }}{{ formatCurrency(txn.amount) }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div v-else class="py-8 text-center text-slate-500">
          <p>No recent transactions</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import type { DashboardMetrics, Transaction } from '@/types/finance';

interface Props {
  metrics: DashboardMetrics;
  recent_transactions: Transaction[];
}

defineProps<Props>();

const formatCurrency = (amount: number): string => {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
  }).format(amount);
};
</script>
