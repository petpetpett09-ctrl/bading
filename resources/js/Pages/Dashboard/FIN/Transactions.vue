<template>
  <AuthenticatedLayout>
    <Head title="Transactions" />
    <div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 p-6">
      <div class="max-w-6xl mx-auto">
        <div class="mb-8">
          <h1 class="text-4xl font-bold text-slate-900">Transactions</h1>
          <p class="text-slate-600 mt-2">Manage and track all financial transactions</p>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
          <div class="flex flex-col md:flex-row gap-4 mb-6">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search transactions..."
              class="flex-1 px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
            <select
              v-model="filterType"
              class="px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              <option value="">All Types</option>
              <option value="income">Income</option>
              <option value="expense">Expense</option>
              <option value="transfer">Transfer</option>
            </select>
            <button
              @click="resetFilters"
              class="px-6 py-2 bg-slate-500 text-white rounded-lg hover:bg-slate-600 transition"
            >
              Reset
            </button>
          </div>

          <div class="overflow-x-auto">
            <table class="w-full">
              <thead>
                <tr class="bg-slate-100 border-b border-slate-300">
                  <th class="px-6 py-3 text-left text-sm font-semibold text-slate-700">Date</th>
                  <th class="px-6 py-3 text-left text-sm font-semibold text-slate-700">Description</th>
                  <th class="px-6 py-3 text-left text-sm font-semibold text-slate-700">Category</th>
                  <th class="px-6 py-3 text-left text-sm font-semibold text-slate-700">Type</th>
                  <th class="px-6 py-3 text-right text-sm font-semibold text-slate-700">Amount</th>
                  <th class="px-6 py-3 text-center text-sm font-semibold text-slate-700">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-if="filteredTransactions.length === 0" class="border-b border-slate-200">
                  <td colspan="6" class="px-6 py-8 text-center text-slate-500">
                    <p class="text-lg">No transactions found</p>
                    <p class="text-sm mt-2">Start adding transactions to see them here</p>
                  </td>
                </tr>
                <tr v-for="transaction in filteredTransactions" :key="transaction.id" class="border-b border-slate-200 hover:bg-slate-50">
                  <td class="px-6 py-4 text-sm text-slate-800">{{ transaction.date }}</td>
                  <td class="px-6 py-4 text-sm text-slate-800">{{ transaction.description }}</td>
                  <td class="px-6 py-4 text-sm text-slate-600">{{ transaction.category }}</td>
                  <td class="px-6 py-4 text-sm">
                    <span
                      :class="getTypeBadgeClass(transaction.type)"
                      class="px-3 py-1 rounded-full text-xs font-semibold"
                    >
                      {{ transaction.type.charAt(0).toUpperCase() + transaction.type.slice(1) }}
                    </span>
                  </td>
                  <td class="px-6 py-4 text-sm text-right font-semibold text-slate-800">
                    {{ formatCurrency(transaction.amount) }}
                  </td>
                  <td class="px-6 py-4 text-center">
                    <button
                      @click="editTransaction(transaction.id)"
                      class="text-blue-600 hover:text-blue-800 mr-3 text-sm font-medium"
                    >
                      Edit
                    </button>
                    <button
                      @click="deleteTransaction(transaction.id)"
                      class="text-red-600 hover:text-red-800 text-sm font-medium"
                    >
                      Delete
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="mt-6 flex items-center justify-between">
            <p class="text-sm text-slate-600">
              Showing {{ filteredTransactions.length }} transactions
            </p>
            <div class="flex gap-2">
              <button
                @click="previousPage"
                :disabled="currentPage === 1"
                class="px-4 py-2 bg-slate-200 text-slate-700 rounded-lg hover:bg-slate-300 disabled:opacity-50 disabled:cursor-not-allowed transition"
              >
                Previous
              </button>
              <span class="px-4 py-2 text-sm font-medium text-slate-700">
                Page {{ currentPage }} of {{ totalPages }}
              </span>
              <button
                @click="nextPage"
                :disabled="currentPage === totalPages"
                class="px-4 py-2 bg-slate-200 text-slate-700 rounded-lg hover:bg-slate-300 disabled:opacity-50 disabled:cursor-not-allowed transition"
              >
                Next
              </button>
            </div>
          </div>
        </div>

        <div class="flex gap-4">
          <Link
            href="/finance/transactions/create"
            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium"
          >
            New Transaction
          </Link>
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

interface Transaction {
  id: number
  date: string
  description: string
  category: string
  type: 'income' | 'expense' | 'transfer'
  amount: number
  reference_number?: string
  notes?: string
}

interface Account {
  id: number
  name: string
}

interface Props {
  transactions: {
    data: Transaction[]
    links: any
  }
  categories: string[]
  accounts: Account[]
  filters: Record<string, any>
}

const props = defineProps<Props>()

const searchQuery = ref('')
const filterType = ref('')
const currentPage = ref(1)
const itemsPerPage = ref(20)

const filteredTransactions = computed(() => {
  return props.transactions.data.filter((t: Transaction) => {
    const matchesSearch = t.description.toLowerCase().includes(searchQuery.value.toLowerCase())
    const matchesType = !filterType.value || t.type === filterType.value
    return matchesSearch && matchesType
  })
})

const totalPages = computed(() => Math.ceil(filteredTransactions.value.length / itemsPerPage.value))

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  })
}

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

const resetFilters = () => {
  searchQuery.value = ''
  filterType.value = ''
  currentPage.value = 1
}

const nextPage = () => {
  if (currentPage.value < totalPages.value) currentPage.value++
}

const previousPage = () => {
  if (currentPage.value > 1) currentPage.value--
}

const editTransaction = (id: number) => {
  // Navigate to edit page
}

const deleteTransaction = (id: number) => {
  if (confirm('Are you sure you want to delete this transaction?')) {
    // Use Inertia form to delete
  }
}
</script>
