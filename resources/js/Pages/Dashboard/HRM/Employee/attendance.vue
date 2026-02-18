<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { ref, onMounted, onUnmounted, computed } from 'vue'
import {
    Clock,
    Search,
    Users,
    MoreHorizontal,
    Sunrise,
    Sunset,
    Moon,
    LogIn,
    LogOut,
    History,
    Timer,
    Coffee,
    X,
    Calendar as CalendarIcon,
    CheckCircle2
} from 'lucide-vue-next'

const props = defineProps({
    attendance_status: {
        type: Object,
        default: () => ({ is_clocked_in: false, last_action: '08:45 AM', total_hours_today: '0h 0m' })
    },
    employee_attendance: {
        type: Array,
        default: () => []
    }
})

// --- LIVE CLOCK LOGIC ---
const currentTime = ref(new Date().toLocaleTimeString())
let timerInterval
onMounted(() => {
    timerInterval = setInterval(() => {
        currentTime.value = new Date().toLocaleTimeString()
    }, 1000)
})
onUnmounted(() => clearInterval(timerInterval))

// --- SHIFT MANAGEMENT LOGIC ---
const isModalOpen = ref(false)
const selectedDept = ref('HRM')
const shiftDate = ref(new Date().toISOString().substr(0, 10))
const showSuccessToast = ref(false)
const departments = ['HRM', 'SCM', 'FIN', 'MAN', 'INV', 'ORD', 'WAR', 'CRM', 'ECO']

// Inertia Form Helper for Database Connection
const form = useForm({
    user_id: null,
    shift_type: '',
    effective_date: '',
    dept_code: ''
})

const getShiftIcon = (shift) => {
    if (shift === 'Morning') return Sunrise
    if (shift === 'Afternoon' || shift === 'Evening') return Sunset
    return Moon
}

const openShiftModal = () => { isModalOpen.value = true }
const closeModal = () => { isModalOpen.value = false }

const updateEmployeeShift = (employeeId, newShift) => {
    // Preparing the form data to match backend validation
    form.user_id = employeeId
    form.shift_type = newShift
    form.effective_date = shiftDate.value
    form.dept_code = selectedDept.value

    // POST request to the backend route defined in web.php
    form.post(route('hrm.employee.attendance.update-shift'), {
        preserveScroll: true,
        onSuccess: () => {
            showSuccessToast.value = true
            setTimeout(() => showSuccessToast.value = false, 3000)
        }
    })
}

/** * FIXED FILTER LOGIC:
 * Matches against 'dept' property from controller.
 * Also added a fallback to 'role' in case the prop name varies.
 */
const filteredEmployees = computed(() => {
    return props.employee_attendance.filter(emp => {
        const empDept = emp.dept || emp.role || '';
        return empDept.toString().toUpperCase() === selectedDept.value.toUpperCase();
    })
})
</script>

<template>

    <Head title="Attendance Management" />

    <AuthenticatedLayout>
        <Transition enter-active-class="transform transition duration-300 ease-out"
            enter-from-class="translate-y-[-100%] opacity-0" enter-to-class="translate-y-0 opacity-100"
            leave-active-class="transition duration-200 ease-in" leave-from-class="opacity-100"
            leave-to-class="opacity-0">
            <div v-if="showSuccessToast"
                class="fixed top-6 right-6 z-[100] bg-emerald-500 text-white px-6 py-4 rounded-2xl shadow-2xl flex items-center gap-3">
                <CheckCircle2 class="h-5 w-5" />
                <span class="font-bold text-sm">Shift Updated Successfully</span>
            </div>
        </Transition>

        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 mb-8">
            <div>
                <h1 class="text-2xl font-black text-slate-900 dark:text-white uppercase tracking-tight">
                    Attendance <span class="text-blue-600">Management</span>
                </h1>
                <p class="text-slate-500 dark:text-slate-400 text-sm font-medium">Monitoring staff duty cycles and
                    real-time logs.</p>
            </div>

            <div class="flex items-center gap-4">
                <button @click="openShiftModal"
                    class="bg-slate-900 dark:bg-white dark:text-slate-900 text-white px-6 py-3 rounded-2xl font-bold text-sm shadow-lg hover:scale-105 transition-all flex items-center gap-2">
                    <CalendarIcon class="h-4 w-4" />
                    Manage Shifts
                </button>

                <div
                    class="bg-white dark:bg-slate-800 px-6 py-3 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-xl shadow-blue-500/5 flex items-center gap-4">
                    <Clock class="h-5 w-5 text-blue-600 animate-pulse" />
                    <h2 class="text-xl font-black text-slate-900 dark:text-white tracking-tighter">{{ currentTime }}
                    </h2>
                </div>
            </div>
        </div>

        <div
            class="bg-white dark:bg-slate-800 rounded-[2.5rem] border border-slate-100 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="p-8 border-b border-slate-50 dark:border-slate-700 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl text-emerald-600">
                        <History class="h-5 w-5" />
                    </div>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">Active Duty Logs</h2>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 dark:bg-slate-900/50">
                            <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                Employee</th>
                            <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Dept
                            </th>
                            <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Shift
                            </th>
                            <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Clock
                                In</th>
                            <th
                                class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">
                                Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        <tr v-for="staff in employee_attendance" :key="staff.id"
                            class="hover:bg-slate-50/50 group transition-all">
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-xs">
                                        {{ staff.name.charAt(0) }}</div>
                                    <span class="text-sm font-bold text-slate-900 dark:text-white">{{ staff.name
                                    }}</span>
                                </div>
                            </td>
                            <td class="px-8 py-5 text-xs font-black text-blue-600 uppercase">{{ staff.dept || staff.role
                                }}</td>
                            <td class="px-8 py-5">
                                <div
                                    class="flex items-center gap-2 text-xs font-bold text-slate-700 dark:text-slate-200">
                                    <component :is="getShiftIcon(staff.shift)" class="h-3.5 w-3.5 text-amber-500" />
                                    {{ staff.shift || 'Not Set' }}
                                </div>
                            </td>
                            <td class="px-8 py-5 text-sm font-semibold text-slate-600">{{ staff.clock_in || '---' }}
                            </td>
                            <td class="px-8 py-5 text-right">
                                <button class="p-2 hover:bg-slate-100 rounded-lg">
                                    <MoreHorizontal class="h-4 w-4 text-slate-400" />
                                </button>
                            </td>
                        </tr>
                        <tr v-if="employee_attendance.length === 0">
                            <td colspan="5" class="px-8 py-12 text-center text-slate-400 font-medium">No active duty
                                logs found for today.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <Transition enter-active-class="duration-300 ease-out" enter-from-class="opacity-0" enter-to-class="opacity-100"
            leave-active-class="duration-200 ease-in" leave-from-class="opacity-100" leave-to-class="opacity-0">
            <div v-if="isModalOpen" class="fixed inset-0 z-[60] flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="closeModal"></div>

                <div
                    class="relative bg-white dark:bg-slate-800 w-full max-w-4xl rounded-[3rem] shadow-2xl overflow-hidden border border-white/20 transform transition-all">
                    <div
                        class="p-8 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center bg-slate-50/50">
                        <div>
                            <h2 class="text-xl font-black text-slate-900 dark:text-white uppercase tracking-tight">Shift
                                Scheduler</h2>
                            <p class="text-xs text-slate-500 font-medium">Reassign employee shifts by department</p>
                        </div>
                        <button @click="closeModal"
                            class="p-2 hover:bg-rose-50 hover:text-rose-500 rounded-xl transition-colors">
                            <X class="h-6 w-6" />
                        </button>
                    </div>

                    <div class="p-8">
                        <div class="flex flex-col md:flex-row gap-6 mb-8">
                            <div class="flex-1">
                                <label
                                    class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 block">Effective
                                    Date</label>
                                <input type="date" v-model="shiftDate"
                                    class="w-full bg-slate-100 dark:bg-slate-900 border-none rounded-2xl px-4 py-3 font-bold text-sm focus:ring-2 focus:ring-blue-500 transition-all" />
                            </div>
                            <div class="flex-[2]">
                                <label
                                    class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 block">Departments</label>
                                <div class="flex flex-wrap gap-2">
                                    <button v-for="dept in departments" :key="dept" @click="selectedDept = dept"
                                        :class="selectedDept === dept ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/30' : 'bg-slate-100 dark:bg-slate-900 text-slate-500 hover:bg-slate-200'"
                                        class="px-4 py-2 rounded-xl text-[10px] font-black transition-all">
                                        {{ dept }}
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4 max-h-[400px] overflow-y-auto pr-2 custom-scroll">
                            <div v-for="emp in filteredEmployees" :key="emp.id"
                                class="flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-900/50 rounded-2xl border border-slate-100 dark:border-slate-700 hover:border-blue-300 transition-colors">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="h-10 w-10 bg-white dark:bg-slate-800 rounded-full flex items-center justify-center shadow-sm font-bold text-blue-600">
                                        {{ emp.name.charAt(0) }}</div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-900 dark:text-white">{{ emp.name }}</p>
                                        <p class="text-[10px] font-bold text-slate-400 uppercase">Current: {{ emp.shift
                                            || 'None' }}</p>
                                    </div>
                                </div>

                                <div class="flex gap-2">
                                    <button v-for="s in ['Morning', 'Afternoon', 'Graveyard']" :key="s"
                                        @click="updateEmployeeShift(emp.id, s)" :disabled="form.processing"
                                        class="p-2 rounded-xl border border-slate-200 dark:border-slate-700 hover:border-blue-500 hover:text-blue-500 transition-all flex items-center gap-2 disabled:opacity-50"
                                        :title="s">
                                        <component :is="getShiftIcon(s)" class="h-4 w-4" />
                                        <span class="text-[10px] font-bold hidden md:block">{{ s }}</span>
                                    </button>
                                </div>
                            </div>
                            <div v-if="filteredEmployees.length === 0" class="text-center py-12">
                                <Users class="h-12 w-12 text-slate-200 mx-auto mb-3" />
                                <p class="text-sm text-slate-400 font-bold">No employees found in {{ selectedDept }}</p>
                            </div>
                        </div>
                    </div>

                    <div
                        class="p-8 bg-slate-50 dark:bg-slate-900/50 border-t border-slate-100 dark:border-slate-700 text-right">
                        <button @click="closeModal"
                            class="px-8 py-3 bg-slate-900 dark:bg-white dark:text-slate-900 text-white rounded-2xl font-black text-xs uppercase tracking-widest shadow-xl transition-transform active:scale-95">
                            Close Scheduler
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </AuthenticatedLayout>
</template>

<style scoped>
.custom-scroll::-webkit-scrollbar {
    width: 6px;
}

.custom-scroll::-webkit-scrollbar-track {
    background: transparent;
}

.custom-scroll::-webkit-scrollbar-thumb {
    background: #e2e8f0;
    border-radius: 10px;
}

.dark .custom-scroll::-webkit-scrollbar-thumb {
    background: #334155;
}
</style>