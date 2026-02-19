<template>
  <AuthenticatedLayout>
    <Head title="New Invoice" />
    <Teleport to="body">
      <Transition
        enter-active-class="transition duration-300 ease-out"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition duration-200 ease-in"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 bg-slate-900/60 backdrop-blur-sm" @click="goBack">
          <Transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="scale-95 opacity-0"
            enter-to-class="scale-100 opacity-100"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="scale-100 opacity-100"
            leave-to-class="scale-95 opacity-0"
          >
            <div class="relative w-full max-w-2xl bg-white dark:bg-slate-900 rounded-[2.5rem] shadow-2xl overflow-hidden flex flex-col max-h-[90vh]" @click.stop>
        <!-- Header -->
        <div class="px-8 py-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between sticky top-0 bg-white dark:bg-slate-900 z-20">
          <div>
            <h2 class="text-xl font-black text-slate-900 dark:text-white uppercase tracking-tight">New <span class="text-blue-600">Invoice</span></h2>
            <p class="text-xs text-slate-500 font-medium tracking-tight">Create and track customer invoices</p>
          </div>
          <Link href="/finance/invoices-bills" class="p-2 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-full transition-colors">
            <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </Link>
        </div>

        <!-- Form Content -->
        <div class="overflow-y-auto p-8 custom-scrollbar space-y-6">
          <form @submit.prevent="submitForm" class="space-y-6">
            <!-- Invoice Details Section -->
            <div class="bg-blue-50/50 dark:bg-blue-900/10 p-6 rounded-[2rem] border border-blue-100 dark:border-blue-900/20">
              <div class="flex items-center mb-6">
                <div class="h-10 w-10 bg-blue-600 rounded-2xl flex items-center justify-center mr-4 shadow-lg shadow-blue-500/30">
                  <svg class="text-white h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                  </svg>
                </div>
                <h4 class="text-lg font-black text-slate-800 dark:text-white">Invoice Details</h4>
              </div>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-1.5">
                  <label class="text-xs font-bold text-slate-500 ml-1">Invoice Number *</label>
                  <input v-model="form.invoice_number" type="text" placeholder="INV-001" required
                    class="w-full px-5 py-3.5 rounded-2xl bg-white dark:bg-slate-800 border-none ring-1 ring-slate-200 dark:ring-slate-700 text-sm focus:ring-2 focus:ring-blue-600"/>
                  <p v-if="form.errors.invoice_number" class="text-red-600 text-xs mt-1">{{ form.errors.invoice_number }}</p>
                </div>
                <div class="space-y-1.5">
                  <label class="text-xs font-bold text-slate-500 ml-1">Customer *</label>
                  <input v-model="form.customer" type="text" placeholder="Customer name" required
                    class="w-full px-5 py-3.5 rounded-2xl bg-white dark:bg-slate-800 border-none ring-1 ring-slate-200 dark:ring-slate-700 text-sm focus:ring-2 focus:ring-blue-600"/>
                  <p v-if="form.errors.customer" class="text-red-600 text-xs mt-1">{{ form.errors.customer }}</p>
                </div>
                <div class="space-y-1.5">
                  <label class="text-xs font-bold text-slate-500 ml-1">Issue Date *</label>
                  <input v-model="form.issue_date" type="date" required
                    class="w-full px-5 py-3.5 rounded-2xl bg-white dark:bg-slate-800 border-none ring-1 ring-slate-200 dark:ring-slate-700 text-sm focus:ring-2 focus:ring-blue-600"/>
                  <p v-if="form.errors.issue_date" class="text-red-600 text-xs mt-1">{{ form.errors.issue_date }}</p>
                </div>
                <div class="space-y-1.5">
                  <label class="text-xs font-bold text-slate-500 ml-1">Due Date *</label>
                  <input v-model="form.due_date" type="date" required
                    class="w-full px-5 py-3.5 rounded-2xl bg-white dark:bg-slate-800 border-none ring-1 ring-slate-200 dark:ring-slate-700 text-sm focus:ring-2 focus:ring-blue-600"/>
                  <p v-if="form.errors.due_date" class="text-red-600 text-xs mt-1">{{ form.errors.due_date }}</p>
                </div>
              </div>
            </div>

            <!-- Amount & Status Section -->
            <div class="bg-slate-50/50 dark:bg-slate-800/50 p-6 rounded-[2rem] border border-slate-100 dark:border-slate-800 shadow-sm">
              <div class="flex items-center mb-6">
                <div class="h-10 w-10 bg-slate-600 rounded-2xl flex items-center justify-center mr-4">
                  <svg class="text-white h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                </div>
                <h4 class="text-lg font-black text-slate-800 dark:text-white">Amount & Status</h4>
              </div>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-1.5">
                  <label class="text-xs font-bold text-slate-500 ml-1">Amount (₱) *</label>
                  <input v-model="form.total_amount" type="number" step="0.01" min="0" placeholder="0.00" required
                    class="w-full px-5 py-3.5 rounded-2xl bg-white dark:bg-slate-800 border-none ring-1 ring-slate-200 dark:ring-slate-700 text-sm focus:ring-2 focus:ring-blue-600"/>
                  <p v-if="form.errors.total_amount" class="text-red-600 text-xs mt-1">{{ form.errors.total_amount }}</p>
                </div>
                <div class="space-y-1.5">
                  <label class="text-xs font-bold text-slate-500 ml-1">Status *</label>
                  <select v-model="form.status" required
                    class="w-full px-5 py-3.5 rounded-2xl bg-white dark:bg-slate-800 border-none ring-1 ring-slate-200 dark:ring-slate-700 text-sm focus:ring-2 focus:ring-blue-600">
                    <option value="">Select Status</option>
                    <option value="draft">Draft</option>
                    <option value="sent">Sent</option>
                    <option value="paid">Paid</option>
                    <option value="overdue">Overdue</option>
                  </select>
                  <p v-if="form.errors.status" class="text-red-600 text-xs mt-1">{{ form.errors.status }}</p>
                </div>
              </div>
            </div>

            <!-- Notes Section -->
            <div class="bg-slate-50/50 dark:bg-slate-800/50 p-6 rounded-[2rem] border border-slate-100 dark:border-slate-800">
              <div class="flex items-center mb-6">
                <div class="h-10 w-10 bg-slate-600 rounded-2xl flex items-center justify-center mr-4">
                  <svg class="text-white h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                  </svg>
                </div>
                <h4 class="text-lg font-black text-slate-800 dark:text-white">Notes</h4>
              </div>
              <textarea v-model="form.notes" rows="3" placeholder="Additional notes (optional)"
                class="w-full px-5 py-3.5 rounded-2xl bg-white dark:bg-slate-800 border-none ring-1 ring-slate-200 dark:ring-slate-700 text-sm focus:ring-2 focus:ring-blue-600">
              </textarea>
            </div>
          </form>
        </div>

        <!-- Footer -->
        <div class="p-8 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50 flex justify-end gap-3">
          <Link href="/finance/invoices-bills"
            class="px-6 py-3.5 bg-slate-500 hover:bg-slate-600 text-white rounded-2xl font-bold text-sm transition-all">
            Cancel
          </Link>
          <button @click="submitForm" :disabled="form.processing"
            class="px-8 py-3.5 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl font-bold text-sm shadow-xl shadow-blue-500/25 active:scale-95 disabled:opacity-50 transition-all">
            {{ form.processing ? 'Creating...' : 'Create Invoice' }}
          </button>
        </div>
            </div>
          </Transition>
        </div>
      </Transition>
    </Teleport>
  </AuthenticatedLayout>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

interface InvoiceForm {
  invoice_number: string
  customer: string
  issue_date: string
  due_date: string
  total_amount: number | null
  status: string
  notes: string
}

const form = useForm<InvoiceForm>({
  invoice_number: '',
  customer: '',
  issue_date: new Date().toISOString().split('T')[0],
  due_date: '',
  total_amount: null,
  status: '',
  notes: '',
})

const submitForm = () => {
  form.post('/finance/invoices', {
    onSuccess: () => {
      // Successfully created, will redirect to invoices-bills
    },
    onError: (errors) => {
      console.error('Form errors:', errors)
    },
  })
}

const goBack = () => {
  window.location.href = '/finance/invoices-bills'
}
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #e2e8f0;
  border-radius: 10px;
}

.dark .custom-scrollbar::-webkit-scrollbar-thumb {
  background: #334155;
}
</style>
