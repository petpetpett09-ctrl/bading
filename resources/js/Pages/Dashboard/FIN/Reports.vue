<template>
  <AuthenticatedLayout>
    <Head title="Financial Reports" />
    <div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 p-6">
      <div class="max-w-7xl mx-auto">
        <div class="mb-8">
          <h1 class="text-4xl font-bold text-slate-900">Financial Reports</h1>
          <p class="text-slate-600 mt-2">Generate and analyze financial statements and reports</p>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div>
              <label class="block text-sm font-semibold text-slate-700 mb-2">Report Type</label>
              <select 
                v-model="reportType" 
                class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
                <option value="income_statement">Income Statement</option>
                <option value="cash_flow">Cash Flow</option>
                <option value="balance_sheet">Balance Sheet</option>
                <option value="expense_by_category">Expenses by Category</option>
                <option value="revenue_customer">Revenue by Customer</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-semibold text-slate-700 mb-2">Start Date</label>
              <input 
                v-model="startDate" 
                type="date" 
                class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
              />
            </div>
            <div>
              <label class="block text-sm font-semibold text-slate-700 mb-2">End Date</label>
              <input 
                v-model="endDate" 
                type="date" 
                class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
              />
            </div>
            <div class="flex items-end gap-2">
              <button 
                @click="generateReport"
                class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium"
              >
                Generate
              </button>
            </div>
          </div>

          <div class="flex gap-2">
            <button 
              @click="exportCSV"
              class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm"
            >
              Export CSV
            </button>
            <button 
              @click="exportPDF"
              class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition text-sm"
            >
              Export PDF
            </button>
          </div>
        </div>

        <div v-if="reportData" class="bg-white rounded-lg shadow-md p-6">
          <h2 class="text-2xl font-bold text-slate-900 mb-6">{{ reportTitle }}</h2>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-6 border border-blue-200">
              <p class="text-sm text-slate-600 mb-2">Total Revenue</p>
              <p class="text-3xl font-bold text-slate-900">{{ formatCurrency(reportData.totalRevenue) }}</p>
            </div>
            <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-lg p-6 border border-red-200">
              <p class="text-sm text-slate-600 mb-2">Total Expenses</p>
              <p class="text-3xl font-bold text-slate-900">{{ formatCurrency(reportData.totalExpenses) }}</p>
            </div>
            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-6 border border-green-200">
              <p class="text-sm text-slate-600 mb-2">Net Profit</p>
              <p class="text-3xl font-bold text-slate-900">{{ formatCurrency(reportData.netProfit) }}</p>
            </div>
          </div>

          <div class="overflow-x-auto">
            <table class="w-full">
              <thead>
                <tr class="bg-slate-100 border-b border-slate-300">
                  <th class="px-6 py-3 text-left text-sm font-semibold text-slate-700">Category</th>
                  <th class="px-6 py-3 text-left text-sm font-semibold text-slate-700">Description</th>
                  <th class="px-6 py-3 text-right text-sm font-semibold text-slate-700">Amount</th>
                  <th class="px-6 py-3 text-right text-sm font-semibold text-slate-700">Percentage</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="item in reportData.items" :key="item.id" class="border-b border-slate-200 hover:bg-slate-50">
                  <td class="px-6 py-4 text-sm font-medium text-slate-800">{{ item.category }}</td>
                  <td class="px-6 py-4 text-sm text-slate-600">{{ item.description }}</td>
                  <td class="px-6 py-4 text-sm text-right font-semibold text-slate-800">{{ formatCurrency(item.amount) }}</td>
                  <td class="px-6 py-4 text-sm text-right text-slate-600">{{ item.percentage }}%</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <div v-else class="bg-white rounded-lg shadow-md p-12 text-center">
          <p class="text-lg text-slate-500">Select filters and click Generate to view reports</p>
        </div>

        <div class="mt-6 flex gap-4">
          <Link
            href="/finance/dashboard"
            class="px-6 py-2 bg-slate-600 text-white rounded-lg hover:bg-slate-700 transition font-medium"
          >
            Back to Dashboard
          </Link>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

interface ReportItem {
  id: string
  category: string
  description: string
  amount: number
  percentage: number
}

interface ReportData {
  totalRevenue: number
  totalExpenses: number
  netProfit: number
  items: ReportItem[]
}

interface Props {
  report: ReportData | null
  report_type: string
  date_range: {
    start_date: string
    end_date: string
  }
}

const props = defineProps<Props>()

const reportType = ref(props.report_type)
const startDate = ref(props.date_range.start_date)
const endDate = ref(props.date_range.end_date)
const reportData = computed(() => props.report)

const reportTitle = computed(() => {
  const titles: Record<string, string> = {
    income_statement: 'Income Statement',
    cash_flow: 'Cash Flow Statement',
    balance_sheet: 'Balance Sheet',
    expense_by_category: 'Expenses by Category',
    revenue_customer: 'Revenue by Customer',
  }
  return titles[reportType.value] || 'Financial Report'
})

const formatCurrency = (amount: number | null | undefined) => {
  if (amount === null || amount === undefined || isNaN(amount)) {
    return '₱0.00'
  }
  return '₱' + new Intl.NumberFormat('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  }).format(amount)
}

const generateReport = () => {
  // Navigate to reports with the selected type and date range
  const params = new URLSearchParams({
    type: reportType.value,
    start_date: startDate.value,
    end_date: endDate.value,
  })
  window.location.href = `/finance/reports?${params.toString()}`
}

const exportCSV = () => {
  if (!reportData.value) return
  alert('CSV export placeholder - would export: ' + reportType.value)
}

const exportPDF = () => {
  if (!reportData.value) return
  alert('PDF export placeholder - would export: ' + reportType.value)
}
</script>
