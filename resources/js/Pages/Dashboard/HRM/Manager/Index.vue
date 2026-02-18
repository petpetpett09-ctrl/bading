<script setup>
import { ref } from 'vue' // Added for modal state
import { Head, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import {
    Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot
} from '@headlessui/vue' // Added for the UI modal
import {
    Users,
    UserPlus,
    Calendar,
    TrendingUp,
    Clock,
    CheckCircle2,
    MoreHorizontal,
    Search,
    Filter,
    UserCheck,
    ArrowUpCircle,
    AlertTriangle // Added for modal icon
} from 'lucide-vue-next'

const props = defineProps({
    stats: {
        type: Object,
        default: () => ({
            totalEmployees: 0,
            activeRecruitment: 0,
            pendingLeaves: 0,
            attendanceRate: 0
        })
    },
    suggestedTrainees: {
        type: Array,
        default: () => []
    }
})

// Modal State Logic
const isConfirmingPromotion = ref(false)
const selectedTrainee = ref(null)

const confirmPromotion = (trainee) => {
    selectedTrainee.value = trainee
    isConfirmingPromotion.value = true
}

const closeModal = () => {
    isConfirmingPromotion.value = false
    selectedTrainee.value = null
}

const promoteToStaff = () => {
    if (selectedTrainee.value) {
        router.post(route('hrm.manager.finalize-promotion', selectedTrainee.value.id), {}, {
            preserveScroll: true,
            onSuccess: () => closeModal(),
        });
    }
}

const getStatusClass = (status) => {
    return status === 'Active'
        ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400'
        : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400';
}
</script>

<template>

    <Head title="HRM Manager Dashboard" />

    <AuthenticatedLayout>
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">HRM Manager Dashboard</h1>
            <p class="text-gray-500 dark:text-gray-400">Welcome back! Here's what's happening with your workforce today.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div
                class="bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                        <Users class="h-6 w-6 text-blue-600 dark:text-blue-400" />
                    </div>
                    <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full">+4%</span>
                </div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Employees</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.totalEmployees }}</p>
            </div>

            <div
                class="bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-2 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg">
                        <UserPlus class="h-6 w-6 text-indigo-600 dark:text-indigo-400" />
                    </div>
                    <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2 py-1 rounded-full">{{
                        suggestedTrainees.length }} Pending</span>
                </div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Promotion Suggestions</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ suggestedTrainees.length }}</p>
            </div>

            <div
                class="bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-2 bg-amber-50 dark:bg-amber-900/20 rounded-lg">
                        <Calendar class="h-6 w-6 text-amber-600 dark:text-amber-400" />
                    </div>
                    <span class="text-xs font-bold text-amber-600 bg-amber-50 px-2 py-1 rounded-full">Action
                        Required</span>
                </div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pending Leaves</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.pendingLeaves }}</p>
            </div>

            <div
                class="bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-2 bg-emerald-50 dark:bg-emerald-900/20 rounded-lg">
                        <TrendingUp class="h-6 w-6 text-emerald-600 dark:text-emerald-400" />
                    </div>
                    <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full">Optimal</span>
                </div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Attendance Rate</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.attendanceRate }}%</p>
            </div>
        </div>

        <div
            class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden mb-8">
            <div
                class="p-6 border-b border-gray-50 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">Promotion Suggestions</h2>
                    <p class="text-sm text-gray-500">Review trainees recommended for staff positions.</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/50 dark:bg-gray-900/50">
                            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest">Trainee</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest">Department
                            </th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest">Suggested At
                            </th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest text-right">
                                Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                        <tr v-for="trainee in suggestedTrainees" :key="trainee.id"
                            class="hover:bg-gray-50/50 dark:hover:bg-gray-900/30 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div
                                        class="h-10 w-10 rounded-xl bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 flex items-center justify-center font-bold text-sm mr-3">
                                        {{trainee.name.split(' ').map(n => n[0]).join('')}}
                                    </div>
                                    <div>
                                        <span class="block text-sm font-bold text-gray-900 dark:text-white">{{
                                            trainee.name }}</span>
                                        <span class="text-[10px] text-gray-400 uppercase font-black">ID: #{{
                                            trainee.employee_id || trainee.id }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="px-2 py-1 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 rounded text-[10px] font-bold uppercase">
                                    {{ trainee.role }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ new Date(trainee.suggested_at).toLocaleDateString() }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button @click="confirmPromotion(trainee)"
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold uppercase tracking-widest transition-all shadow-lg shadow-blue-500/20 active:scale-95">
                                    <ArrowUpCircle class="h-4 w-4" />
                                    Promote
                                </button>
                            </td>
                        </tr>
                        <tr v-if="suggestedTrainees.length === 0">
                            <td colspan="4" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-gray-400">
                                    <UserCheck class="h-12 w-12 mb-3 opacity-20" />
                                    <p class="text-sm font-medium">No promotion suggestions at this time.</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <TransitionRoot as="template" :show="isConfirmingPromotion">
            <Dialog as="div" class="relative z-50" @close="closeModal">
                <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0"
                    enter-to="opacity-100" leave="ease-in duration-200" leave-from="opacity-100" leave-to="opacity-0">
                    <div class="fixed inset-0 bg-gray-500/75 dark:bg-gray-900/80 transition-opacity" />
                </TransitionChild>

                <div class="fixed inset-0 z-10 overflow-y-auto">
                    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                        <TransitionChild as="template" enter="ease-out duration-300"
                            enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                            enter-to="opacity-100 translate-y-0 sm:scale-100" leave="ease-in duration-200"
                            leave-from="opacity-100 translate-y-0 sm:scale-100"
                            leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                            <DialogPanel
                                class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-gray-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-100 dark:border-gray-700">
                                <div class="p-6">
                                    <div class="sm:flex sm:items-start">
                                        <div
                                            class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl bg-blue-100 dark:bg-blue-900/30 sm:mx-0 sm:h-10 sm:w-10">
                                            <AlertTriangle class="h-6 w-6 text-blue-600 dark:text-blue-400"
                                                aria-hidden="true" />
                                        </div>
                                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                            <DialogTitle as="h3"
                                                class="text-lg font-bold leading-6 text-gray-900 dark:text-white">
                                                Confirm Promotion
                                            </DialogTitle>
                                            <div class="mt-2">
                                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                                    Are you sure you want to officially promote <span
                                                        class="font-bold text-gray-900 dark:text-white">{{
                                                        selectedTrainee?.name }}</span> to the staff position? This
                                                    action will update their access and records across the system.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-8 flex flex-col sm:flex-row-reverse gap-3">
                                        <button type="button"
                                            class="inline-flex w-full justify-center rounded-xl bg-blue-600 px-4 py-2.5 text-xs font-bold uppercase tracking-widest text-white shadow-sm hover:bg-blue-700 transition-all active:scale-95 sm:w-auto"
                                            @click="promoteToStaff">
                                            Confirm Promotion
                                        </button>
                                        <button type="button"
                                            class="inline-flex w-full justify-center rounded-xl bg-white dark:bg-gray-700 px-4 py-2.5 text-xs font-bold uppercase tracking-widest text-gray-900 dark:text-white shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 transition-all sm:w-auto"
                                            @click="closeModal">
                                            Cancel
                                        </button>
                                    </div>
                                </div>
                            </DialogPanel>
                        </TransitionChild>
                    </div>
                </div>
            </Dialog>
        </TransitionRoot>

    </AuthenticatedLayout>
</template>