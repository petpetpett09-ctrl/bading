<template>
  <AuthenticatedLayout>
    <Head :title="isEditMode ? 'Edit Transaction' : 'New Transaction'" />
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
            <h2 class="text-xl font-black text-slate-900 dark:text-white uppercase tracking-tight">{{ isEditMode ? 'Edit' : 'New' }} <span class="text-purple-600">Transaction</span></h2>
            <p class="text-xs text-slate-500 font-medium tracking-tight">Record financial transaction details</p>
          </div>
          <Link href="/finance/transactions" class="p-2 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-full transition-colors">
            <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </Link>
        </div>

        <!-- Form Content -->
        <div class="overflow-y-auto p-8 custom-scrollbar space-y-6">
          <form @submit.prevent="submitForm" class="space-y-6">
            <!-- Date, Type & Account Section -->
            <div class="bg-purple-50/50 dark:bg-purple-900/10 p-6 rounded-[2rem] border border-purple-100 dark:border-purple-900/20">
              <div class="flex items-center mb-6">
                <div class="h-10 w-10 bg-purple-600 rounded-2xl flex items-center justify-center mr-4 shadow-lg shadow-purple-500/30">
                  <svg class="text-white h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12M8 11h12M8 15h12M3 7h.01M3 11h.01M3 15h.01"/>
                  </svg>
                </div>
                <h4 class="text-lg font-black text-slate-800 dark:text-white">Transaction Basics</h4>
              </div>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-1.5">
                  <label class="text-xs font-bold text-slate-500 ml-1">Date *</label>
                  <input v-model="formData.date" type="date" required
                    class="w-full px-5 py-3.5 rounded-2xl bg-white dark:bg-slate-800 border-none ring-1 ring-slate-200 dark:ring-slate-700 text-sm focus:ring-2 focus:ring-purple-600"/>
                </div>
                <div class="space-y-1.5">
                  <label class="text-xs font-bold text-slate-500 ml-1">Type *</label>
                  <select v-model="formData.type" required
                    class="w-full px-5 py-3.5 rounded-2xl bg-white dark:bg-slate-800 border-none ring-1 ring-slate-200 dark:ring-slate-700 text-sm focus:ring-2 focus:ring-purple-600">
                    <option value="">Select Type</option>
                    <option value="income">Income</option>
                    <option value="expense">Expense</option>
                    <option value="transfer">Transfer</option>
                  </select>
                </div>
                <div class="space-y-1.5">
                  <label class="text-xs font-bold text-slate-500 ml-1">Account *</label>
                  <select v-model="formData.account" required
                    class="w-full px-5 py-3.5 rounded-2xl bg-white dark:bg-slate-800 border-none ring-1 ring-slate-200 dark:ring-slate-700 text-sm focus:ring-2 focus:ring-purple-600">
                    <option value="">Select Account</option>
                    <option value="checking">Checking</option>
                    <option value="savings">Savings</option>
                    <option value="credit_card">Credit Card</option>
                  </select>
                </div>
                <div class="space-y-1.5">
                  <label class="text-xs font-bold text-slate-500 ml-1">Category *</label>
                  <input v-model="formData.category" type="text" placeholder="e.g., Utilities, Sales" required
                    class="w-full px-5 py-3.5 rounded-2xl bg-white dark:bg-slate-800 border-none ring-1 ring-slate-200 dark:ring-slate-700 text-sm focus:ring-2 focus:ring-purple-600"/>
                </div>
              </div>
            </div>

            <!-- Amount & Reference Section -->
            <div class="bg-slate-50/50 dark:bg-slate-800/50 p-6 rounded-[2rem] border border-slate-100 dark:border-slate-800 shadow-sm">
              <div class="flex items-center mb-6">
                <div class="h-10 w-10 bg-slate-600 rounded-2xl flex items-center justify-center mr-4">
                  <svg class="text-white h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                </div>
                <h4 class="text-lg font-black text-slate-800 dark:text-white">Amount & Reference</h4>
              </div>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-1.5">
                  <label class="text-xs font-bold text-slate-500 ml-1">Amount (₱) *</label>
                  <input v-model.number="formData.amount" type="number" step="0.01" min="0" placeholder="0.00" required
                    class="w-full px-5 py-3.5 rounded-2xl bg-white dark:bg-slate-800 border-none ring-1 ring-slate-200 dark:ring-slate-700 text-sm focus:ring-2 focus:ring-purple-600"/>
                </div>
                <div class="space-y-1.5">
                  <label class="text-xs font-bold text-slate-500 ml-1">Reference Number</label>
                  <input v-model="formData.reference" type="text" placeholder="Check #, Invoice #, etc"
                    class="w-full px-5 py-3.5 rounded-2xl bg-white dark:bg-slate-800 border-none ring-1 ring-slate-200 dark:ring-slate-700 text-sm focus:ring-2 focus:ring-purple-600"/>
                </div>
              </div>
            </div>

            <!-- Description Section -->
            <div class="bg-slate-50/50 dark:bg-slate-800/50 p-6 rounded-[2rem] border border-slate-100 dark:border-slate-800">
              <div class="flex items-center mb-6">
                <div class="h-10 w-10 bg-slate-600 rounded-2xl flex items-center justify-center mr-4">
                  <svg class="text-white h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                  </svg>
                </div>
                <h4 class="text-lg font-black text-slate-800 dark:text-white">Description</h4>
              </div>
              <textarea v-model="formData.description" rows="3" placeholder="Describe the transaction" required
                class="w-full px-5 py-3.5 rounded-2xl bg-white dark:bg-slate-800 border-none ring-1 ring-slate-200 dark:ring-slate-700 text-sm focus:ring-2 focus:ring-purple-600">
              </textarea>
            </div>

            <!-- Notes Section -->
            <div class="bg-slate-50/50 dark:bg-slate-800/50 p-6 rounded-[2rem] border border-slate-100 dark:border-slate-800">
              <div class="flex items-center mb-6">
                <div class="h-10 w-10 bg-slate-600 rounded-2xl flex items-center justify-center mr-4">
                  <svg class="text-white h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L9 21H5v-4L16.732 3.732z"/>
                  </svg>
                </div>
                <h4 class="text-lg font-black text-slate-800 dark:text-white">Notes</h4>
              </div>
              <textarea v-model="formData.notes" rows="2" placeholder="Additional notes (optional)"
                class="w-full px-5 py-3.5 rounded-2xl bg-white dark:bg-slate-800 border-none ring-1 ring-slate-200 dark:ring-slate-700 text-sm focus:ring-2 focus:ring-purple-600">
              </textarea>
            </div>

            <!-- Errors Display -->
            <div v-if="formErrors.length > 0" class="bg-red-50/50 dark:bg-red-900/10 p-4 rounded-2xl border border-red-200 dark:border-red-900/20">
              <p class="text-xs font-bold text-red-600 mb-2">Please fix the following errors:</p>
              <ul class="space-y-1">
                <li v-for="(error, index) in formErrors" :key="index" class="text-xs text-red-600">• {{ error }}</li>
              </ul>
            </div>
          </form>
        </div>

        <!-- Footer -->
        <div class="p-8 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50 flex justify-end gap-3">
          <Link href="/finance/transactions"
            class="px-6 py-3.5 bg-slate-500 hover:bg-slate-600 text-white rounded-2xl font-bold text-sm transition-all">
            Cancel
          </Link>
          <button @click="submitForm" :disabled="form.processing"
            class="px-8 py-3.5 bg-purple-600 hover:bg-purple-700 text-white rounded-2xl font-bold text-sm shadow-xl shadow-purple-500/25 active:scale-95 disabled:opacity-50 transition-all">
            {{ submitButtonText }}
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
import { ref, computed } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

interface FormData {
  date: string
  type: string
  account: string
  category: string
  amount: number
  reference: string
  description: string
  notes: string
}

interface TransactionForm extends FormData {}

const props = defineProps({
  transactionId: String,
})

const isEditMode = computed(() => !!props.transactionId)

const headerTitle = computed(() => 
  isEditMode.value ? 'Edit Transaction' : 'New Transaction'
)

const submitButtonText = computed(() =>
  isEditMode.value ? 'Update Transaction' : 'Create Transaction'
)

const formData = ref<FormData>({
  date: new Date().toISOString().split('T')[0],
  type: '',
  account: '',
  category: '',
  amount: 0,
  reference: '',
  description: '',
  notes: '',
})

const form = useForm<TransactionForm>({
  date: formData.value.date,
  type: '',
  account: '',
  category: '',
  amount: 0,
  reference: '',
  description: '',
  notes: '',
})

const formErrors = ref<string[]>([])

const goBack = () => {
  window.location.href = '/finance/transactions'
}

const submitForm = () => {
  formErrors.value = []
  
  if (!formData.value.date) formErrors.value.push('Date is required')
  if (!formData.value.type) formErrors.value.push('Type is required')
  if (!formData.value.account) formErrors.value.push('Account is required')
  if (!formData.value.category) formErrors.value.push('Category is required')
  if (formData.value.amount <= 0) formErrors.value.push('Amount must be greater than 0')
  if (!formData.value.description) formErrors.value.push('Description is required')

  if (formErrors.value.length === 0) {
    // Update form data and submit
    form.date = formData.value.date
    form.type = formData.value.type
    form.account = formData.value.account
    form.category = formData.value.category
    form.amount = formData.value.amount
    form.reference = formData.value.reference
    form.description = formData.value.description
    form.notes = formData.value.notes
    
    if (isEditMode.value) {
      form.put(`/finance/transactions/${props.transactionId}`, {
        onSuccess: () => goBack(),
      })
    } else {
      form.post('/finance/transactions', {
        onSuccess: () => goBack(),
      })
    }
  }
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