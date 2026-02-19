<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 p-6">
    <Head title="Financial Reports" />

    <div class="max-w-7xl mx-auto">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-4xl font-bold text-slate-900 mb-2">Financial Reports</h1>
        <p class="text-slate-600">Generate and analyze financial statements</p>
      </div>

      <!-- Report Selection and Filters -->
      <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Report Type</label>
            <select
              v-model="selectedReport"
              @change="loadReport"
              class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
            >
              <option value="income_statement">Income Statement (P&L)</option>
              <option value="balance_sheet">Balance Sheet</option>
              <option value="cash_flow">Cash Flow Statement</option>
              <option value="expense_by_category">Expense by Category</option>
              <option value="revenue_by_customer">Revenue by Customer</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Start Date</label>
            <input
              v-model="dateRange.startDate"
              type="date"
              @change="loadReport"
              class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">End Date</label>
            <input
              v-model="dateRange.endDate"
              type="date"
              @change="loadReport"
              class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
            />
          </div>
        </div>

        <div class="flex gap-4">
          <Link
            :href="`/finance/reports/export?type=${selectedReport}&start_date=${dateRange.startDate}&end_date=${dateRange.endDate}`"
            class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition"
          >
            📥 Export as CSV
          </Link>
          <Link
            :href="`/finance/reports/export?type=${selectedReport}&start_date=${dateRange.startDate}&end_date=${dateRange.endDate}&format=pdf`"
            class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition"
          >
            📄 Export as PDF
          </Link>
        </div>
      </div>

      <!-- Income Statement -->
      <div v-if="selectedReport === 'income_statement'" class="bg-white rounded-lg shadow p-6">
        <h2 class="text-2xl font-bold text-slate-900 mb-6">Income Statement (Profit & Loss)</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
          <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-6 rounded-lg border border-blue-200">
            <h3 class="text-sm font-medium text-slate-700 mb-2">Total Revenue</h3>
            <p class="text-3xl font-bold text-slate-900">{{ formatCurrency(getSummary().total_revenue) }}</p>
          </div>
          <div class="bg-gradient-to-br from-red-50 to-red-100 p-6 rounded-lg border border-red-200">
            <h3 class="text-sm font-medium text-slate-700 mb-2">Total Expenses</h3>
            <p class="text-3xl font-bold text-slate-900">{{ formatCurrency(getSummary().total_expenses) }}</p>
          </div>
          <div
            :class="[
              'bg-gradient-to-br p-6 rounded-lg border',
              getSummary().net_profit >= 0
                ? 'from-green-50 to-green-100 border-green-200'
                : 'from-orange-50 to-orange-100 border-orange-200',
            ]"
          >
            <h3 class="text-sm font-medium text-slate-700 mb-2">Net Profit/Loss</h3>
            <p class="text-3xl font-bold text-slate-900">{{ formatCurrency(getSummary().net_profit) }}</p>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Revenue by Category -->
          <div>
            <h3 class="text-lg font-semibold text-slate-900 mb-4">Revenue by Category</h3>
            <div class="space-y-3">
              <div v-for="item in getRevenueByCategory()" :key="item.category" class="flex justify-between items-center p-3 bg-slate-50 rounded">
                <span class="font-medium text-slate-700">{{ item.category }}</span>
                <span class="font-semibold text-green-600">{{ formatCurrency(item.amount) }}</span>
              </div>
              <div v-if="getRevenueByCategory().length === 0" class="text-slate-500 text-center py-4">
                No revenue data
              </div>
            </div>
          </div>

          <!-- Expenses by Category -->
          <div>
            <h3 class="text-lg font-semibold text-slate-900 mb-4">Expenses by Category</h3>
            <div class="space-y-3">
              <div v-for="item in getExpensesByCategory()" :key="item.category" class="flex justify-between items-center p-3 bg-slate-50 rounded">
                <span class="font-medium text-slate-700">{{ item.category }}</span>
                <span class="font-semibold text-red-600">{{ formatCurrency(item.amount) }}</span>
              </div>
              <div v-if="getExpensesByCategory().length === 0" class="text-slate-500 text-center py-4">
                No expense data
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Cash Flow Statement -->
      <div v-if="selectedReport === 'cash_flow'" class="bg-white rounded-lg shadow p-6">
        <h2 class="text-2xl font-bold text-slate-900 mb-6">Cash Flow Statement</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
          <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-6 rounded-lg border border-blue-200">
            <h3 class="text-sm font-medium text-slate-700 mb-2">Total Inflow</h3>
            <p class="text-3xl font-bold text-slate-900">{{ formatCurrency(getSummary().total_inflow) }}</p>
          </div>
          <div class="bg-gradient-to-br from-red-50 to-red-100 p-6 rounded-lg border border-red-200">
            <h3 class="text-sm font-medium text-slate-700 mb-2">Total Outflow</h3>
            <p class="text-3xl font-bold text-slate-900">{{ formatCurrency(getSummary().total_outflow) }}</p>
          </div>
          <div
            :class="[
              'bg-gradient-to-br p-6 rounded-lg border',
              getSummary().net_cash_flow >= 0
                ? 'from-green-50 to-green-100 border-green-200'
                : 'from-orange-50 to-orange-100 border-orange-200',
            ]"
          >
            <h3 class="text-sm font-medium text-slate-700 mb-2">Net Cash Flow</h3>
            <p class="text-3xl font-bold text-slate-900">{{ formatCurrency(getSummary().net_cash_flow) }}</p>
          </div>
        </div>

        <h3 class="text-lg font-semibold text-slate-900 mb-4">Daily Cash Flow</h3>
        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-200">
              <tr>
                <th class="text-left py-3 px-6 font-medium text-slate-700">Date</th>
                <th class="text-right py-3 px-6 font-medium text-slate-700">Net Flow</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in getDailyFlow()" :key="item.date" class="border-b border-slate-100 hover:bg-slate-50">
                <td class="py-3 px-6 text-slate-600">{{ item.date }}</td>
                <td
                  class="py-3 px-6 text-right font-semibold"
                  :class="item.net_flow >= 0 ? 'text-green-600' : 'text-red-600'"
                >
                  {{ item.net_flow >= 0 ? '+' : '' }}{{ formatCurrency(item.net_flow) }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Expense by Category -->
      <div v-if="selectedReport === 'expense_by_category'" class="bg-white rounded-lg shadow p-6">
        <h2 class="text-2xl font-bold text-slate-900 mb-6">Expense Report by Category</h2>

        <div class="bg-gradient-to-br from-slate-50 to-slate-100 p-6 rounded-lg mb-8 border border-slate-200">
          <h3 class="text-sm font-medium text-slate-700 mb-2">Total Expenses</h3>
          <p class="text-3xl font-bold text-slate-900">{{ formatCurrency(getSummary().total_expenses) }}</p>
        </div>

        <div class="space-y-4">
          <div v-for="item in getCategories()" :key="item.category">
            <div class="flex justify-between items-center mb-2">
              <span class="font-medium text-slate-700">{{ item.category }}</span>
              <span class="text-sm text-slate-600">{{ item.percentage }}%</span>
            </div>
            <div class="w-full bg-slate-200 rounded-full h-2">
              <div class="bg-gradient-to-r from-red-500 to-orange-500 h-2 rounded-full" :style="{ width: item.percentage + '%' }"></div>
            </div>
            <div class="text-right text-sm text-slate-600 mt-1">{{ formatCurrency(item.amount) }}</div>
          </div>
        </div>
      </div>

      <!-- Placeholder for other reports -->
      <div v-if="['balance_sheet', 'revenue_by_customer'].includes(selectedReport)" class="bg-white rounded-lg shadow p-12 text-center">
        <p class="text-lg text-slate-500">
          {{ selectedReport === 'balance_sheet' ? 'Balance Sheet' : 'Revenue by Customer' }} report coming soon
        </p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import type { ReportData } from '@/types/finance';

interface Props {
  report: ReportData;
  report_type: string;
  date_range: {
    start_date: string;
    end_date: string;
  };
}

const props = defineProps<Props>();

const selectedReport = ref(props.report_type);
const dateRange = ref({
  startDate: props.date_range.start_date,
  endDate: props.date_range.end_date,
});
const report = ref<ReportData>(props.report);

const formatCurrency = (amount: number | undefined): string => {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
  }).format(amount || 0);
};

const getSummary = () => {
  return {
    total_revenue: report.value.summary?.total_revenue || 0,
    total_expenses: report.value.summary?.total_expenses || 0,
    net_profit: report.value.summary?.net_profit || 0,
    total_inflow: report.value.summary?.total_inflow || 0,
    total_outflow: report.value.summary?.total_outflow || 0,
    net_cash_flow: report.value.summary?.net_cash_flow || 0,
  };
};

const getRevenueByCategory = () => {
  return report.value.revenue_by_category || [];
};

const getExpensesByCategory = () => {
  return report.value.expenses_by_category || [];
};

const getDailyFlow = () => {
  return report.value.daily_flow || [];
};

const getCategories = () => {
  return report.value.categories || [];
};

const loadReport = () => {
  // This would be called when filters change
  // For now, the page reloads with new params via Inertia
};
</script>
