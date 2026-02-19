<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 p-6">
    <Head title="New Bill" />

    <div class="max-w-3xl mx-auto">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-4xl font-bold text-slate-900 mb-2">Create New Bill</h1>
        <p class="text-slate-600">Record a new vendor bill</p>
      </div>

      <form @submit.prevent="submit" class="bg-white rounded-lg shadow p-8">
        <!-- Error Messages -->
        <div v-if="Object.keys(form.errors).length > 0" class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
          <h3 class="font-semibold text-red-900 mb-2">Please fix the following errors:</h3>
          <ul class="list-disc list-inside text-sm text-red-800">
            <li v-for="(error, key) in form.errors" :key="key">{{ error }}</li>
          </ul>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
          <!-- Bill Number -->
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Bill Number *</label>
            <input v-model="form.bill_number" type="text" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
          </div>

          <!-- Vendor -->
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Vendor *</label>
            <input v-model="form.vendor" type="text" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
          </div>

          <!-- Bill Date -->
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Bill Date *</label>
            <input v-model="form.bill_date" type="date" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
          </div>

          <!-- Due Date -->
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Due Date *</label>
            <input v-model="form.due_date" type="date" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
          </div>

          <!-- Total Amount -->
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Total Amount *</label>
            <input v-model.number="form.total_amount" type="number" step="0.01" min="0.01" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
          </div>

          <!-- Status -->
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Status *</label>
            <select v-model="form.status" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
              <option value="draft">Draft</option>
              <option value="received">Received</option>
              <option value="approved">Approved</option>
              <option value="paid">Paid</option>
              <option value="overdue">Overdue</option>
            </select>
          </div>
        </div>

        <!-- Notes -->
        <div class="mb-6">
          <label class="block text-sm font-medium text-slate-700 mb-2">Notes</label>
          <textarea v-model="form.notes" rows="3" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
        </div>

        <!-- Form Actions -->
        <div class="flex gap-4">
          <button type="submit" :disabled="form.processing" class="flex-1 bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 disabled:opacity-50 transition font-medium">
            {{ form.processing ? 'Processing...' : 'Create Bill' }}
          </button>
          <Link :href="route('finance.invoices-bills.index')" class="flex-1 bg-slate-200 text-slate-700 px-6 py-2 rounded-lg hover:bg-slate-300 transition font-medium text-center">
            Cancel
          </Link>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
  bill_number: '',
  vendor: '',
  bill_date: new Date().toISOString().split('T')[0],
  due_date: new Date(Date.now() + 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0],
  total_amount: 0,
  status: 'draft',
  notes: '',
});

const submit = () => {
  form.post(route('finance.bills.store'));
};
</script>
