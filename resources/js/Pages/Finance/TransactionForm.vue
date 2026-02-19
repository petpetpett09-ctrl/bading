<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 p-6">
    <Head :title="transaction ? 'Edit Transaction' : 'New Transaction'" />

    <div class="max-w-3xl mx-auto">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-4xl font-bold text-slate-900 mb-2">
          {{ transaction ? 'Edit Transaction' : 'Create New Transaction' }}
        </h1>
        <p class="text-slate-600">Record a new financial transaction</p>
      </div>

      <form @submit.prevent="submit" class="bg-white rounded-lg shadow p-8">
        <!-- Error Messages -->
        <div v-if="Object.keys(form.errors).length > 0" class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
          <h3 class="font-semibold text-red-900 mb-2">Please fix the following errors:</h3>
          <ul class="list-disc list-inside text-sm text-red-800">
            <li v-for="(error, key) in form.errors" :key="key">{{ error }}</li>
          </ul>
        </div>

        <!-- Form Fields -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
          <!-- Date -->
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Date *</label>
            <input
              v-model="form.date"
              type="date"
              required
              class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
          </div>

          <!-- Type -->
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Type *</label>
            <select
              v-model="form.type"
              required
              class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
              <option value="">Select Type</option>
              <option value="income">Income</option>
              <option value="expense">Expense</option>
              <option value="transfer">Transfer</option>
            </select>
          </div>

          <!-- Account -->
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Account *</label>
            <select
              v-model="form.account_id"
              required
              class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
              <option value="">Select Account</option>
              <option v-for="acc in accounts" :key="acc.id" :value="acc.id">
                {{ acc.code }} - {{ acc.name }}
              </option>
            </select>
          </div>

          <!-- Category -->
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Category *</label>
            <input
              v-model="form.category"
              list="categories"
              required
              type="text"
              placeholder="e.g., Utilities, Sales, Salary"
              class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
            <datalist id="categories">
              <option v-for="cat in categories" :key="cat" :value="cat"></option>
            </datalist>
          </div>

          <!-- Amount -->
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Amount *</label>
            <input
              v-model.number="form.amount"
              type="number"
              step="0.01"
              min="0.01"
              required
              placeholder="0.00"
              class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
          </div>

          <!-- Reference Number -->
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Reference Number</label>
            <input
              v-model="form.reference_number"
              type="text"
              placeholder="e.g., Check #, Invoice #"
              class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
          </div>
        </div>

        <!-- Description -->
        <div class="mb-6">
          <label class="block text-sm font-medium text-slate-700 mb-2">Description *</label>
          <textarea
            v-model="form.description"
            required
            rows="3"
            placeholder="Describe the transaction details"
            class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          ></textarea>
        </div>

        <!-- Notes -->
        <div class="mb-6">
          <label class="block text-sm font-medium text-slate-700 mb-2">Notes</label>
          <textarea
            v-model="form.notes"
            rows="2"
            placeholder="Additional notes (optional)"
            class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          ></textarea>
        </div>

        <!-- Form Actions -->
        <div class="flex gap-4">
          <button
            type="submit"
            :disabled="form.processing"
            class="flex-1 bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 disabled:opacity-50 transition font-medium"
          >
            {{ form.processing ? 'Processing...' : transaction ? 'Update Transaction' : 'Create Transaction' }}
          </button>
          <Link
            :href="route('finance.transactions.index')"
            class="flex-1 bg-slate-200 text-slate-700 px-6 py-2 rounded-lg hover:bg-slate-300 transition font-medium text-center"
          >
            Cancel
          </Link>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import type { Account } from '@/types/finance';

interface Props {
  accounts: Account[];
  categories: string[];
  transaction?: { id: number; account_id: number; date: string; description: string; amount: number; type: string; category: string; reference_number?: string; notes?: string };
}

const props = defineProps<Props>();

const form = useForm({
  account_id: props.transaction?.account_id || '',
  date: props.transaction?.date || new Date().toISOString().split('T')[0],
  description: props.transaction?.description || '',
  amount: props.transaction?.amount || 0,
  type: props.transaction?.type || '',
  category: props.transaction?.category || '',
  reference_number: props.transaction?.reference_number || '',
  notes: props.transaction?.notes || '',
});

const submit = () => {
  if (props.transaction) {
    form.patch(route('finance.transactions.update', props.transaction.id));
  } else {
    form.post(route('finance.transactions.store'));
  }
};
</script>
