<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 p-6">
    <Head title="Invoices & Bills" />

    <div class="max-w-7xl mx-auto">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-4xl font-bold text-slate-900 mb-2">Invoices & Bills</h1>
        <p class="text-slate-600">Manage customer invoices and vendor bills</p>
      </div>

      <!-- Filters -->
      <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form @submit.prevent="applyFilters" class="grid grid-cols-1 md:grid-cols-4 gap-4">
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
      </div>

      <!-- Tabs -->
      <div class="flex gap-4 mb-6">
        <button
          @click="activeTab = 'invoices'"
          :class="[
            'px-4 py-2 font-medium rounded-lg transition',
            activeTab === 'invoices'
              ? 'bg-blue-600 text-white'
              : 'bg-white text-slate-900 border border-slate-300 hover:bg-slate-50',
          ]"
        >
          Invoices
          <span class="ml-2 inline-block bg-slate-100 text-slate-900 px-2 py-1 rounded text-xs">
            {{ invoices.length }}
          </span>
        </button>
        <button
          @click="activeTab = 'bills'"
          :class="[
            'px-4 py-2 font-medium rounded-lg transition',
            activeTab === 'bills'
              ? 'bg-blue-600 text-white'
              : 'bg-white text-slate-900 border border-slate-300 hover:bg-slate-50',
          ]"
        >
          Bills
          <span class="ml-2 inline-block bg-slate-100 text-slate-900 px-2 py-1 rounded text-xs">
            {{ bills.length }}
          </span>
        </button>
      </div>

      <!-- Invoices Section -->
      <div v-if="activeTab === 'invoices'" class="space-y-6">
        <div class="flex items-center justify-between">
          <h2 class="text-2xl font-bold text-slate-900">Invoices</h2>
          <Link
            :href="route('finance.invoices.create')"
            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition"
          >
            + New Invoice
          </Link>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
          <div v-if="invoices.length > 0" class="overflow-x-auto">
            <table class="w-full text-sm">
              <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                  <th class="text-left py-3 px-6 font-medium text-slate-700">Invoice #</th>
                  <th class="text-left py-3 px-6 font-medium text-slate-700">Customer</th>
                  <th class="text-left py-3 px-6 font-medium text-slate-700">Issue Date</th>
                  <th class="text-left py-3 px-6 font-medium text-slate-700">Due Date</th>
                  <th class="text-right py-3 px-6 font-medium text-slate-700">Amount</th>
                  <th class="text-center py-3 px-6 font-medium text-slate-700">Status</th>
                  <th class="text-center py-3 px-6 font-medium text-slate-700">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="inv in invoices" :key="inv.id" class="border-b border-slate-100 hover:bg-slate-50">
                  <td class="py-3 px-6 font-medium text-slate-900">{{ inv.invoice_number }}</td>
                  <td class="py-3 px-6 text-slate-600">{{ inv.customer }}</td>
                  <td class="py-3 px-6 text-slate-600">{{ inv.issue_date }}</td>
                  <td class="py-3 px-6 text-slate-600">
                    {{ inv.due_date }}
                    <span
                      v-if="getDaysUntilDue(inv.due_date, inv.days_until_due) < 0"
                      class="ml-2 text-xs bg-red-100 text-red-800 px-2 py-1 rounded"
                    >
                      {{ Math.abs(getDaysUntilDue(inv.due_date, inv.days_until_due)) }} days overdue
                    </span>
                  </td>
                  <td class="py-3 px-6 text-right font-semibold text-slate-900">
                    {{ formatCurrency(inv.total_amount) }}
                  </td>
                  <td class="py-3 px-6 text-center">
                    <span
                      class="px-3 py-1 rounded-full text-xs font-medium"
                      :class="getStatusClass(inv.status)"
                    >
                      {{ inv.status }}
                    </span>
                  </td>
                  <td class="py-3 px-6 text-center">
                    <div class="flex justify-center gap-2">
                      <button
                        v-if="inv.status !== 'paid'"
                        @click="markInvoicePaid(inv.id)"
                        class="text-green-600 hover:text-green-800 text-sm"
                      >
                        Mark Paid
                      </button>
                      <Link
                        :href="route('finance.invoices.download', inv.id)"
                        class="text-blue-600 hover:text-blue-800 text-sm"
                      >
                        Download PDF
                      </Link>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div v-else class="py-8 text-center text-slate-500">
            <p>No invoices found</p>
          </div>
        </div>
      </div>

      <!-- Bills Section -->
      <div v-if="activeTab === 'bills'" class="space-y-6">
        <div class="flex items-center justify-between">
          <h2 class="text-2xl font-bold text-slate-900">Bills</h2>
          <Link
            :href="route('finance.bills.create')"
            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition"
          >
            + New Bill
          </Link>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
          <div v-if="bills.length > 0" class="overflow-x-auto">
            <table class="w-full text-sm">
              <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                  <th class="text-left py-3 px-6 font-medium text-slate-700">Bill #</th>
                  <th class="text-left py-3 px-6 font-medium text-slate-700">Vendor</th>
                  <th class="text-left py-3 px-6 font-medium text-slate-700">Bill Date</th>
                  <th class="text-left py-3 px-6 font-medium text-slate-700">Due Date</th>
                  <th class="text-right py-3 px-6 font-medium text-slate-700">Amount</th>
                  <th class="text-center py-3 px-6 font-medium text-slate-700">Status</th>
                  <th class="text-center py-3 px-6 font-medium text-slate-700">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="bill in bills" :key="bill.id" class="border-b border-slate-100 hover:bg-slate-50">
                  <td class="py-3 px-6 font-medium text-slate-900">{{ bill.bill_number }}</td>
                  <td class="py-3 px-6 text-slate-600">{{ bill.vendor }}</td>
                  <td class="py-3 px-6 text-slate-600">{{ bill.bill_date }}</td>
                  <td class="py-3 px-6 text-slate-600">
                    {{ bill.due_date }}
                    <span
                      v-if="getDaysUntilDue(bill.due_date, bill.days_until_due) < 0"
                      class="ml-2 text-xs bg-red-100 text-red-800 px-2 py-1 rounded"
                    >
                      {{ Math.abs(getDaysUntilDue(bill.due_date, bill.days_until_due)) }} days overdue
                    </span>
                  </td>
                  <td class="py-3 px-6 text-right font-semibold text-slate-900">
                    {{ formatCurrency(bill.total_amount) }}
                  </td>
                  <td class="py-3 px-6 text-center">
                    <span
                      class="px-3 py-1 rounded-full text-xs font-medium"
                      :class="getStatusClass(bill.status)"
                    >
                      {{ bill.status }}
                    </span>
                  </td>
                  <td class="py-3 px-6 text-center">
                    <div class="flex justify-center gap-2">
                      <button
                        v-if="bill.status !== 'paid'"
                        @click="markBillPaid(bill.id)"
                        class="text-green-600 hover:text-green-800 text-sm"
                      >
                        Mark Paid
                      </button>
                      <Link
                        :href="route('finance.bills.download', bill.id)"
                        class="text-blue-600 hover:text-blue-800 text-sm"
                      >
                        Download PDF
                      </Link>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div v-else class="py-8 text-center text-slate-500">
            <p>No bills found</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import type { Invoice, Bill } from '@/types/finance';

interface Props {
  invoices: Invoice[];
  bills: Bill[];
  invoice_statuses: string[];
  bill_statuses: string[];
  filters: Record<string, any>;
}

const props = defineProps<Props>();

const activeTab = ref<'invoices' | 'bills'>('invoices');

const filterForm = ref({
  start_date: props.filters.start_date || '',
  end_date: props.filters.end_date || '',
});

const formatCurrency = (amount: number): string => {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
  }).format(amount);
};

const getStatusClass = (status: string): string => {
  const classes: Record<string, string> = {
    draft: 'bg-slate-100 text-slate-800',
    sent: 'bg-blue-100 text-blue-800',
    received: 'bg-blue-100 text-blue-800',
    approved: 'bg-purple-100 text-purple-800',
    paid: 'bg-green-100 text-green-800',
    overdue: 'bg-red-100 text-red-800',
    cancelled: 'bg-slate-100 text-slate-800',
  };
  return classes[status] || 'bg-slate-100 text-slate-800';
};

const getDaysUntilDue = (dueDate: string, daysFromApi?: number): number => {
  if (daysFromApi !== undefined) {
    return daysFromApi;
  }
  const today = new Date();
  today.setHours(0, 0, 0, 0);
  const due = new Date(dueDate);
  due.setHours(0, 0, 0, 0);
  const diffTime = due.getTime() - today.getTime();
  return Math.ceil(diffTime / (1000 * 60 * 60 * 24));
};

const applyFilters = () => {
  const form = useForm(filterForm.value);
  form.get(route('finance.invoices-bills.index'));
};

const resetFilters = () => {
  filterForm.value = {
    start_date: '',
    end_date: '',
  };
  applyFilters();
};

const markInvoicePaid = (id: number) => {
  useForm({}).patch(route('finance.invoices.pay', id));
};

const markBillPaid = (id: number) => {
  useForm({}).patch(route('finance.bills.pay', id));
};
</script>
