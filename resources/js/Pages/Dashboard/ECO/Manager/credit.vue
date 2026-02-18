<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import {
    Plus,
    Search,
    Pencil,
    Trash2,
    Users,
    CreditCard,
    Calendar,
    DollarSign,
    Check,
    X,
    Eye,
    Clock,
    Filter,
    ArrowDownToLine,
    TrendingUp
} from 'lucide-vue-next';

const props = defineProps({
    creditAccounts: {
        type: Object,
        default: () => ({ data: [], links: {}, meta: {} }),
    },
    filters: {
        type: Object,
        default: () => ({ search: '' }),
    },
});

const showCreateModal = ref(false);
const showEditModal = ref(false);
const showDeleteModal = ref(false);
const showScheduleModal = ref(false);
const selectedCreditAccount = ref(null);
const search = ref(props.filters.search);

let searchTimeout;
const updateSearch = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(
            route('eco.manager.credit'),
            { search: search.value },
            { preserveState: true, replace: true }
        );
    }, 300);
};

const createForm = useForm({
    customer_name: '',
    total_amount: '',
    paid_amount: 0,
    term_type: 'one-time',
    installment_months: 12,
    status: 'active',
});

const editForm = useForm({
    customer_name: '',
    total_amount: '',
    paid_amount: 0,
    term_type: 'one-time',
    installment_months: 12,
    status: 'active',
});

const submitCreate = () => {
    createForm.post(route('eco.manager.credit.store'), {
        onSuccess: () => {
            createForm.reset();
            showCreateModal.value = false;
        },
    });
};

const openEditModal = (account) => {
    selectedCreditAccount.value = account;
    editForm.customer_name = account.customer_name;
    editForm.total_amount = account.total_amount;
    editForm.paid_amount = account.paid_amount;
    editForm.term_type = account.term_type;
    editForm.installment_months = account.installment_months || 12;
    editForm.status = account.status;
    showEditModal.value = true;
};

const submitEdit = () => {
    editForm.put(route('eco.manager.credit.update', selectedCreditAccount.value.id), {
        onSuccess: () => {
            editForm.reset();
            showEditModal.value = false;
            selectedCreditAccount.value = null;
        },
    });
};

const confirmDelete = (account) => {
    selectedCreditAccount.value = account;
    showDeleteModal.value = true;
};

const deleteCreditAccount = () => {
    router.delete(route('eco.manager.credit.destroy', selectedCreditAccount.value.id), {
        onSuccess: () => {
            showDeleteModal.value = false;
            selectedCreditAccount.value = null;
        },
    });
};

const toggleStatus = (account) => {
    router.patch(route('eco.manager.credit.toggle-status', account.id), {}, { preserveState: true, preserveScroll: true });
};

const viewSchedule = (account) => {
    selectedCreditAccount.value = account;
    showScheduleModal.value = true;
};

const remainingBalance = (account) => (account.total_amount - account.paid_amount).toFixed(2);
const progressPercentage = (account) => account.total_amount === 0 ? 0 : ((account.paid_amount / account.total_amount) * 100).toFixed(1);

const generateSchedule = (account) => {
    if (account.term_type === 'one-time') {
        return [{ due_date: 'Agreement Date', amount: account.total_amount, status: account.paid_amount >= account.total_amount ? 'paid' : 'pending' }];
    } else {
        const monthly = account.total_amount / account.installment_months;
        const schedule = [];
        const today = new Date();
        for (let i = 1; i <= account.installment_months; i++) {
            const due = new Date(today);
            due.setMonth(today.getMonth() + i);
            const formattedDate = due.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
            const paidThisInstallment = Math.min(monthly, Math.max(0, account.paid_amount - (i - 1) * monthly));
            schedule.push({
                installment: i,
                due_date: formattedDate,
                amount: monthly.toFixed(2),
                paid: paidThisInstallment > 0 ? paidThisInstallment.toFixed(2) : '0.00',
                status: paidThisInstallment >= monthly ? 'paid' : paidThisInstallment > 0 ? 'partial' : 'pending',
            });
        }
        return schedule;
    }
};

const stats = computed(() => {
    const accounts = props.creditAccounts.data || [];
    const totalOutstanding = accounts.reduce((acc, a) => acc + (a.total_amount - a.paid_amount), 0).toFixed(2);
    const avgProgress = accounts.length ? (accounts.reduce((acc, a) => acc + (a.paid_amount / a.total_amount), 0) / accounts.length * 100).toFixed(1) : 0;
    return [
        { label: 'Credit Accounts', value: accounts.length, icon: Users, color: 'text-blue-600', bg: 'bg-blue-50' },
        { label: 'Active Plans', value: accounts.filter(a => a.status === 'active').length, icon: Check, color: 'text-emerald-600', bg: 'bg-emerald-50' },
        { label: 'Outstanding Balance', value: `₱${totalOutstanding}`, icon: DollarSign, color: 'text-purple-600', bg: 'bg-purple-50' },
        { label: 'Avg. Progress', value: `${avgProgress}%`, icon: TrendingUp, color: 'text-amber-600', bg: 'bg-amber-50' },
    ];
});
</script>

<template>

    <Head title="Credit Management" />

    <AuthenticatedLayout>


        <div class="p-4 sm:p-6 lg:p-8 bg-[#F5F6FA] min-h-screen">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-8">
                <div v-for="stat in stats" :key="stat.label"
                    class="bg-white p-4 sm:p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between transition hover:shadow-md">
                    <div>
                        <p class="text-[10px] sm:text-xs font-bold text-gray-400 uppercase tracking-tighter">{{
                            stat.label }}</p>
                        <p class="text-xl sm:text-2xl font-black text-gray-800 mt-1">{{ stat.value }}</p>
                    </div>
                    <div :class="stat.bg" class="p-3 rounded-xl">
                        <component :is="stat.icon" :class="stat.color" class="h-6 w-6" />
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-6">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between mb-8 space-y-4 lg:space-y-0">
                    <div class="flex flex-col sm:flex-row sm:items-center space-y-3 sm:space-y-0 sm:space-x-3">
                        <h1 class="text-xl sm:text-2xl font-bold text-gray-800 tracking-tight italic uppercase">Credit
                            Management</h1>
                        <div class="flex flex-wrap gap-2">
                            <button @click="showCreateModal = true"
                                class="flex items-center px-4 py-2 bg-[#6E49CB] text-white rounded-md text-sm font-medium hover:bg-[#5D44A7] transition shadow-md">
                                <Plus class="h-4 w-4 mr-2" /> Add Account
                            </button>
                            <button
                                class="flex items-center px-4 py-2 border border-gray-300 text-gray-600 rounded-md text-sm font-medium hover:bg-gray-50 transition">
                                <ArrowDownToLine class="h-4 w-4 mr-2" /> Export
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center space-x-4 justify-between lg:justify-end">
                        <div
                            class="text-right text-[10px] text-gray-400 font-bold uppercase tracking-widest hidden sm:block">
                            <p>Active Plans: <span class="text-[#6E49CB]">Verified</span></p>
                        </div>
                        <button
                            class="flex items-center px-6 py-2 bg-[#6E49CB] text-white rounded-md text-sm font-medium shadow-md uppercase font-bold tracking-widest">
                            <Filter class="h-4 w-4 mr-2" /> Filter
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto -mx-4 sm:mx-0">
                    <div class="inline-block min-w-full align-middle px-4 sm:px-0">
                        <table class="min-w-full text-left">
                            <thead>
                                <tr
                                    class="text-gray-400 text-[10px] sm:text-[11px] font-bold uppercase tracking-[0.1em] border-b border-gray-100 bg-gray-50/50 italic">
                                    <th class="p-4 font-normal text-gray-600 font-bold">Customer Name</th>
                                    <th class="p-4 font-normal">Total Balance</th>
                                    <th class="p-4 font-normal">Paid Amount</th>
                                    <th class="p-4 font-normal">Remaining</th>
                                    <th class="p-4 font-normal hidden md:table-cell">Term Type</th>
                                    <th class="p-4 font-normal text-center">Status</th>
                                    <th class="p-4 font-normal text-right px-8">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <tr v-for="account in creditAccounts.data" :key="account.id"
                                    class="group hover:bg-gray-50 transition duration-150">
                                    <td class="p-4">
                                        <div class="flex items-center">
                                            <div
                                                class="h-10 w-10 bg-purple-50 rounded-full flex items-center justify-center mr-3 border border-purple-100 group-hover:bg-white transition">
                                                <Users class="h-5 w-5 text-[#6E49CB]" />
                                            </div>
                                            <span
                                                class="font-bold text-gray-700 text-sm whitespace-nowrap italic uppercase tracking-tight">{{
                                                    account.customer_name }}</span>
                                        </div>
                                    </td>
                                    <td class="p-4 font-black text-gray-800 text-sm whitespace-nowrap italic">₱{{
                                        account.total_amount }}</td>
                                    <td class="p-4 text-emerald-600 font-bold text-sm whitespace-nowrap italic">₱{{
                                        account.paid_amount }}</td>
                                    <td class="p-4 text-rose-500 font-bold text-sm whitespace-nowrap italic">₱{{
                                        remainingBalance(account) }}</td>
                                    <td
                                        class="p-4 text-gray-500 text-[10px] sm:text-xs hidden md:table-cell whitespace-nowrap uppercase font-bold">
                                        <div class="flex items-center">
                                            <component :is="account.term_type === 'one-time' ? CreditCard : Calendar"
                                                class="h-4 w-4 mr-2 text-gray-400" />
                                            {{ account.term_type === 'one-time' ? 'One-time' :
                                                `${account.installment_months} Mo. Plan` }}
                                        </div>
                                    </td>
                                    <td class="p-4 text-center">
                                        <button @click="toggleStatus(account)" :class="[
                                            'px-3 py-1 rounded-full text-[9px] sm:text-[10px] font-bold uppercase tracking-widest transition',
                                            account.status === 'active' ? 'bg-green-100 text-green-600 border border-green-200 hover:bg-green-200' : 'bg-red-100 text-red-400 border border-red-200 hover:bg-red-200'
                                        ]">
                                            {{ account.status === 'active' ? 'Active' : 'Inactive' }}
                                        </button>
                                    </td>
                                    <td class="p-4">
                                        <div class="flex items-center justify-end space-x-3 text-[#6E49CB]">
                                            <button @click="viewSchedule(account)"
                                                class="hover:bg-purple-50 p-1.5 rounded-lg transition" title="Schedule">
                                                <Eye class="h-5 w-5" />
                                            </button>
                                            <button @click="openEditModal(account)"
                                                class="hover:bg-purple-50 p-1.5 rounded-lg transition" title="Edit">
                                                <Pencil class="h-5 w-5" />
                                            </button>
                                            <button @click="confirmDelete(account)"
                                                class="hover:bg-purple-50 p-1.5 rounded-lg transition" title="Delete">
                                                <Trash2 class="h-5 w-5 text-gray-300 hover:text-red-500" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="showCreateModal"
            class="fixed inset-0 z-[100] overflow-y-auto bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4">
            <div
                class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden border border-gray-100 transform transition-all">
                <form @submit.prevent="submitCreate">
                    <div class="px-6 py-4 bg-[#5D44A7] text-white flex justify-between items-center">
                        <h3 class="font-bold text-sm uppercase tracking-widest italic">New Credit Account</h3>
                        <button type="button" @click="showCreateModal = false"
                            class="text-white/50 hover:text-white transition">
                            <X class="h-5 w-5" />
                        </button>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <label
                                class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Customer
                                Name</label>
                            <input v-model="createForm.customer_name" type="text" required
                                class="w-full border-gray-200 rounded-lg text-sm focus:ring-[#6E49CB] focus:border-[#6E49CB]" />
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label
                                    class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Total
                                    Amount</label>
                                <input v-model="createForm.total_amount" type="number" step="0.01" required
                                    class="w-full border-gray-200 rounded-lg text-sm focus:ring-[#6E49CB] focus:border-[#6E49CB]" />
                            </div>
                            <div>
                                <label
                                    class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Paid
                                    Amount</label>
                                <input v-model="createForm.paid_amount" type="number" step="0.01"
                                    class="w-full border-gray-200 rounded-lg text-sm focus:ring-[#6E49CB] focus:border-[#6E49CB]" />
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label
                                    class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Term
                                    Type</label>
                                <select v-model="createForm.term_type"
                                    class="w-full border-gray-200 rounded-lg text-sm focus:ring-[#6E49CB] focus:border-[#6E49CB]">
                                    <option value="one-time">One-time</option>
                                    <option value="installment">Installment</option>
                                </select>
                            </div>
                            <div v-if="createForm.term_type === 'installment'">
                                <label
                                    class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Months</label>
                                <input v-model="createForm.installment_months" type="number"
                                    class="w-full border-gray-200 rounded-lg text-sm focus:ring-[#6E49CB] focus:border-[#6E49CB]" />
                            </div>
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3">
                        <button type="button" @click="showCreateModal = false"
                            class="px-4 py-2 text-xs font-bold text-gray-400 uppercase hover:text-gray-600 transition tracking-widest">Cancel</button>
                        <button type="submit" :disabled="createForm.processing"
                            class="px-6 py-2 bg-[#6E49CB] text-white rounded-lg text-xs font-bold uppercase tracking-widest shadow-md hover:bg-[#5D44A7] transition">Save
                            Account</button>
                    </div>
                </form>
            </div>
        </div>

        <div v-if="showScheduleModal && selectedCreditAccount"
            class="fixed inset-0 z-[100] overflow-y-auto bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4">
            <div
                class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden border border-gray-100 transform transition-all">
                <div class="px-6 py-4 bg-[#5D44A7] text-white flex justify-between items-center">
                    <h3 class="font-bold text-sm uppercase tracking-widest italic">Payment Schedule: {{
                        selectedCreditAccount.customer_name }}</h3>
                    <button type="button" @click="showScheduleModal = false"
                        class="text-white/50 hover:text-white transition">
                        <X class="h-5 w-5" />
                    </button>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
                        <div class="bg-gray-50 p-3 rounded-xl border border-gray-100">
                            <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Total</p>
                            <p class="text-sm font-black text-gray-800 italic">₱{{ selectedCreditAccount.total_amount }}
                            </p>
                        </div>
                        <div class="bg-gray-50 p-3 rounded-xl border border-gray-100">
                            <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Paid</p>
                            <p class="text-sm font-black text-emerald-600 italic">₱{{ selectedCreditAccount.paid_amount
                            }}</p>
                        </div>
                        <div class="bg-gray-50 p-3 rounded-xl border border-gray-100">
                            <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Remaining</p>
                            <p class="text-sm font-black text-rose-500 italic">₱{{
                                remainingBalance(selectedCreditAccount) }}</p>
                        </div>
                        <div class="bg-gray-50 p-3 rounded-xl border border-gray-100">
                            <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Progress</p>
                            <p class="text-sm font-black text-[#6E49CB] italic">{{
                                progressPercentage(selectedCreditAccount) }}%</p>
                        </div>
                    </div>
                    <div class="max-h-[300px] overflow-y-auto no-scrollbar border rounded-xl overflow-hidden">
                        <table class="min-w-full text-left">
                            <thead class="bg-gray-50 sticky top-0">
                                <tr
                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 italic">
                                    <th class="p-3">#</th>
                                    <th class="p-3">Due Date</th>
                                    <th class="p-3">Installment</th>
                                    <th class="p-3">Paid</th>
                                    <th class="p-3 text-right">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <tr v-for="(payment, index) in generateSchedule(selectedCreditAccount)" :key="index"
                                    class="hover:bg-gray-50/50 transition">
                                    <td class="p-3 text-[10px] font-bold text-gray-400">{{ payment.installment || '—' }}
                                    </td>
                                    <td class="p-3 text-[11px] font-bold text-gray-700">{{ payment.due_date }}</td>
                                    <td class="p-3 text-[11px] font-bold text-gray-700 italic">₱{{ payment.amount }}
                                    </td>
                                    <td class="p-3 text-[11px] font-bold text-emerald-600 italic">₱{{ payment.paid ||
                                        '0.00' }}</td>
                                    <td class="p-3 text-right">
                                        <span :class="[
                                            'px-2 py-0.5 rounded-full text-[8px] font-black uppercase tracking-widest',
                                            payment.status === 'paid' ? 'bg-green-50 text-green-600' : payment.status === 'partial' ? 'bg-amber-50 text-amber-600' : 'bg-gray-50 text-gray-400'
                                        ]">
                                            {{ payment.status }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="px-6 py-4 bg-gray-50 flex justify-end">
                    <button @click="showScheduleModal = false"
                        class="px-6 py-2 bg-[#6E49CB] text-white rounded-lg text-xs font-bold uppercase tracking-widest shadow-md hover:bg-[#5D44A7] transition">Close
                        Schedule</button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
input:focus {
    outline: none;
}

.no-scrollbar::-webkit-scrollbar {
    display: none;
}

.no-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

::-webkit-scrollbar {
    width: 4px;
    height: 4px;
}

::-webkit-scrollbar-thumb {
    background: #E5E7EB;
    border-radius: 10px;
}
</style>