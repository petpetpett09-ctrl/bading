<template>
  <AuthenticatedLayout>
    <Head title="Invoices & Bills" />
    <div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 p-6">
      <div class="max-w-6xl mx-auto">
        <!-- Header Section -->
        <div class="mb-8">
          <h1 class="text-3xl font-bold text-gray-900">Invoices &amp; Bills</h1>
          <p class="text-gray-600 mt-1">Manage your accounts receivable and payable</p>
        </div>

        <!-- Tab Navigation -->
        <div class="flex gap-4 mb-6 border-b border-gray-200">
          <button
            @click="activeTab = 'invoices'"
            :class="[
              'px-4 py-2 font-semibold transition-colors',
              activeTab === 'invoices'
                ? 'text-blue-600 border-b-2 border-blue-600'
                : 'text-gray-600 hover:text-gray-900'
            ]"
          >
            Invoices (A/R)
          </button>
          <button
            @click="activeTab = 'bills'"
            :class="[
              'px-4 py-2 font-semibold transition-colors',
              activeTab === 'bills'
                ? 'text-blue-600 border-b-2 border-blue-600'
                : 'text-gray-600 hover:text-gray-900'
            ]"
          >
            Bills (A/P)
          </button>
        </div>

        <!-- Search and Filter -->
        <div class="flex gap-4 mb-6">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search invoices or bills..."
            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
          <select
            v-model="filterStatus"
            class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
            <option value="all">All Status</option>
            <option value="draft">Draft</option>
            <option value="sent">Sent</option>
            <option value="paid">Paid</option>
            <option value="overdue">Overdue</option>
          </select>
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-4 mb-6">
          <Link
            href="/finance/invoices/create"
            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium"
          >
            New Invoice
          </Link>
          <Link
            href="/finance/bills/create"
            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium"
          >
            New Bill
          </Link>
          <Link
            href="/finance/dashboard"
            class="px-6 py-2 bg-slate-600 text-white rounded-lg hover:bg-slate-700 transition font-medium"
          >
            Back to Dashboard
          </Link>
        </div>

        <!-- Invoices Tab -->
        <div v-if="activeTab === 'invoices'">
          <div v-if="props.invoices.data.length === 0" class="bg-white rounded-lg shadow p-8 text-center">
            <p class="text-gray-500 text-lg">No invoices found</p>
          </div>
          <div v-else class="bg-white rounded-lg shadow overflow-hidden">
            <table class="w-full">
              <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                    Invoice Number
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                    Customer
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                    Amount
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                    Status
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200">
                <tr v-for="invoice in props.invoices.data" :key="invoice.id" class="hover:bg-gray-50">
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                    {{ invoice.invoice_number }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                    {{ invoice.customer }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                    ₱{{ invoice.total_amount.toLocaleString() }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm">
                    <span :class="['px-3 py-1 rounded-full text-xs font-semibold', getStatusColor(invoice.status)]">
                      {{ invoice.status.charAt(0).toUpperCase() + invoice.status.slice(1) }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm">
                    <button
                      v-if="invoice.status !== 'paid'"
                      @click="markAsPaid(invoice.id)"
                      class="inline-flex items-center px-3 py-1 bg-green-50 text-green-700 rounded hover:bg-green-100 transition-colors text-xs font-semibold"
                    >
                      Mark as Paid
                    </button>
                    <span v-else class="text-green-600 font-semibold">Paid</span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Bills Tab -->
        <div v-if="activeTab === 'bills'">
          <div v-if="props.bills.data.length === 0" class="bg-white rounded-lg shadow p-8 text-center">
            <p class="text-gray-500 text-lg">No bills found</p>
          </div>
          <div v-else class="bg-white rounded-lg shadow overflow-hidden">
            <table class="w-full">
              <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                    Bill Number
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                    Vendor
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                    Amount
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                    Status
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200">
                <tr v-for="bill in props.bills.data" :key="bill.id" class="hover:bg-gray-50">
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                    {{ bill.bill_number }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                    {{ bill.vendor }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                    ₱{{ bill.total_amount.toLocaleString() }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm">
                    <span :class="['px-3 py-1 rounded-full text-xs font-semibold', getStatusColor(bill.status)]">
                      {{ bill.status.charAt(0).toUpperCase() + bill.status.slice(1) }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm">
                    <button
                      v-if="bill.status !== 'paid'"
                      @click="markAsPaid(bill.id)"
                      class="inline-flex items-center px-3 py-1 bg-green-50 text-green-700 rounded hover:bg-green-100 transition-colors text-xs font-semibold"
                    >
                      Mark as Paid
                    </button>
                    <span v-else class="text-green-600 font-semibold">Paid</span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'

interface Invoice {
  id: number
  invoice_number: string
  customer: string
  issue_date: string
  due_date: string
  total_amount: number
  status: string
  days_until_due: number
}

interface Bill {
  id: number
  bill_number: string
  vendor: string
  bill_date: string
  due_date: string
  total_amount: number
  status: string
  days_until_due: number
}

interface PaginatedData<T> {
  data: T[]
  links: any
}

interface Props {
  invoices: PaginatedData<Invoice>
  bills: PaginatedData<Bill>
}

const props = defineProps<Props>()

// State management
const activeTab = ref<'invoices' | 'bills'>('invoices')
const searchQuery = ref<string>('')
const filterStatus = ref<string>('all')

// Form instance for API calls
const form = useForm({})

/**
 * Get status badge color based on status value
 */
const getStatusColor = (status: string): string => {
  const colors: { [key: string]: string } = {
    draft: 'bg-gray-100 text-gray-700',
    sent: 'bg-blue-100 text-blue-700',
    paid: 'bg-green-100 text-green-700',
    overdue: 'bg-red-100 text-red-700',
  }
  return colors[status] || 'bg-gray-100 text-gray-700'
}

/**
 * Mark invoice or bill as paid
 */
const markAsPaid = (id: number): void => {
  console.log(`Marking ${id} as paid`)
  // In production, this would call: form.post(`/finance/invoices/${id}/mark-paid`)
}
</script>

