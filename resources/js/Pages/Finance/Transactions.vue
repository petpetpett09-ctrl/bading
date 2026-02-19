<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 p-6">
    <Head title="Transaction Management" />

    <div class="max-w-7xl mx-auto">
      <!-- Header -->
      <div class="mb-8 flex items-center justify-between">
        <div>
          <h1 class="text-4xl font-bold text-slate-900 mb-2">Transactions</h1>
          <p class="text-slate-600">Manage and track all financial transactions</p>
        </div>
        <Link
          :href="route('finance.transactions.create')"
          class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition"
        >
          + New Transaction
        </Link>
      </div>

      <!-- Filters -->
      <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-semibold text-slate-900 mb-4">Filters</h3>
        <form @submit.prevent="applyFilters" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Start Date</label>
            <input
              v-model="filterForm.start_date"
              type="date"
              class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">End Date</label>
            <input
              v-model="filterForm.end_date"
              type="date"
              class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Type</label>
            <select
              v-model="filterForm.type"
              class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
            >
              <option value="">All Types</option>
              <option value="income">Income</option>
              <option value="expense">Expense</option>
              <option value="transfer">Transfer</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Category</label>
            <select
              v-model="filterForm.category"
              class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
            >
              <option value="">All Categories</option>
              <option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
            </select>
          </div>
          <div class="flex items-end gap-2">
            <button
              type="submit"
              class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition"
            >
              Filter
            </button>
            <button
              type="button"
              @click="resetFilters"
              class="flex-1 bg-slate-200 text-slate-700 px-4 py-2 rounded-lg hover:bg-slate-300 transition"
            >
              Reset
            </button>
          </div>
        </form>

        <div class="mt-4 flex gap-4">
          <input
            v-model="filterForm.search"
            type="text"
            placeholder="Search by description or reference..."
            class="flex-1 px-3 py-2 border border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
            @keyup.enter="applyFilters"
          />
          <Link
            :href="route('finance.transactions.export', filterForm)"
            class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition"
          >
            Export CSV
          </Link>
        </div>
      </div>

      <!-- Transactions Table -->
      <div class="bg-white rounded-lg shadow overflow-hidden">
        <div v-if="transactions.data.length > 0" class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-200">
              <tr>
                <th class="text-left py-3 px-6 font-medium text-slate-700">Date</th>
                <th class="text-left py-3 px-6 font-medium text-slate-700">Description</th>
                <th class="text-left py-3 px-6 font-medium text-slate-700">Category</th>
                <th class="text-left py-3 px-6 font-medium text-slate-700">Type</th>
                <th class="text-left py-3 px-6 font-medium text-slate-700">Account</th>
                <th class="text-right py-3 px-6 font-medium text-slate-700">Amount</th>
                <th class="text-center py-3 px-6 font-medium text-slate-700">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="txn in transactions.data" :key="txn.id" class="border-b border-slate-100 hover:bg-slate-50">
                <td class="py-3 px-6 text-slate-600">{{ txn.date }}</td>
                <td class="py-3 px-6 text-slate-900">{{ txn.description }}</td>
                <td class="py-3 px-6 text-slate-600">{{ txn.category }}</td>
                <td class="py-3 px-6">
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
                <td class="py-3 px-6 text-slate-600">{{ getAccountName(txn) }}</td>
                <td
                  class="py-3 px-6 text-right font-semibold"
                  :class="txn.type === 'income' ? 'text-green-600' : 'text-red-600'"
                >
                  {{ txn.type === 'income' ? '+' : '-' }}{{ formatCurrency(txn.amount) }}
                </td>
                <td class="py-3 px-6 text-center">
                  <div class="flex justify-center gap-2">
                    <Link
                      :href="route('finance.transactions.edit', txn.id)"
                      class="text-blue-600 hover:text-blue-800 text-sm"
                    >
                      Edit
                    </Link>
                    <button
                      @click="deleteTransaction(txn.id)"
                      class="text-red-600 hover:text-red-800 text-sm"
                    >
                      Delete
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div v-else class="py-8 text-center text-slate-500">
          <p>No transactions found. Start by creating a new transaction.</p>
        </div>

        <!-- Pagination -->
        <div v-if="transactions.links && transactions.links.length > 0" class="px-6 py-4 border-t border-slate-200 flex justify-center gap-2">
          <Link
            v-for="link in transactions.links"
            :key="link.label"
            :href="link.url || '#'"
            :class="[
              'px-3 py-1 rounded text-sm font-medium transition',
              link.active
                ? 'bg-blue-600 text-white'
                : link.url
                  ? 'text-blue-600 hover:bg-blue-50'
                  : 'text-slate-400 cursor-not-allowed',
            ]"
            v-html="link.label"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import type { Transaction } from '@/types/finance';

interface Props {
  transactions: {
    data: Transaction[];
    links?: Array<{ label: string; url?: string; active: boolean }>;
  };
  categories: string[];
  filters: Record<string, any>;
}

const props = defineProps<Props>();

const filterForm = ref({
  start_date: props.filters.start_date || '',
  end_date: props.filters.end_date || '',
  type: props.filters.type || '',
  category: props.filters.category || '',
  search: props.filters.search || '',
});

const formatCurrency = (amount: number): string => {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
  }).format(amount);
};

const getAccountName = (txn: any): string => {
  return txn.account_name || txn.account?.name || 'Unknown Account';
};

const applyFilters = () => {
  const form = useForm(filterForm.value);
  form.get(route('finance.transactions.index'));
};

const resetFilters = () => {
  filterForm.value = {
    start_date: '',
    end_date: '',
    type: '',
    category: '',
    search: '',
  };
  applyFilters();
};

const deleteTransaction = (id: number) => {
  if (confirm('Are you sure you want to delete this transaction?')) {
    useForm({}).delete(route('finance.transactions.destroy', id));
  }
};
</script>
