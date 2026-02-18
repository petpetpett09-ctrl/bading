<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted, computed } from 'vue';
import {
    Clock,
    Calendar as CalendarIcon,
    ChevronRight,
    History,
    Timer,
    CheckCircle2,
    Power,
    BellRing,
    Sunrise,
    Sunset,
    Moon
} from 'lucide-vue-next';

const props = defineProps({
    today_log: Object,      //
    assigned_shift: Object, //
    history: Array,         //
});

// --- CLOCK LOGIC ---
const isClockedIn = computed(() => !!props.today_log?.clock_in && !props.today_log?.clock_out);

// NEW: This computed property prevents the "Unterminated string" error by keeping strings out of the template HTML
const shiftScheduleDisplay = computed(() => {
    return props.assigned_shift?.schedule_range || '8AM - 5PM';
});

const clockButtonText = computed(() => {
    if (isClockedIn.value) return 'Clock Out';
    if (props.today_log?.clock_out) return 'Log Finished';
    return 'Clock In';
});

const handleClockToggle = () => {
    router.post(route('employee.attendance.toggle'), {}, {
        preserveScroll: true,
    });
};

// --- REAL-TIME CLOCK LOGIC ---
const currentTime = ref('');
let timerInterval = null;

const updateTime = () => {
    const now = new Date();
    currentTime.value = now.toLocaleTimeString('en-US', {
        hour12: true,
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    }).replace(/\s?[APM]/g, '');
};

const getShiftIcon = (type) => {
    if (type === 'Morning') return Sunrise;
    if (type === 'Afternoon') return Sunset;
    return Moon;
};

const getStatusStyles = (status) => {
    switch (status) {
        case 'On-Time': return 'bg-emerald-100 text-emerald-700 border-emerald-200';
        case 'Late': return 'bg-amber-100 text-amber-700 border-amber-200';
        default: return 'bg-rose-100 text-rose-700 border-rose-200';
    }
};

onMounted(() => {
    updateTime();
    timerInterval = setInterval(updateTime, 1000);
});

onUnmounted(() => {
    if (timerInterval) clearInterval(timerInterval);
});
</script>

<template>

    <Head title="Attendance Portal" />

    <AuthenticatedLayout>
        <div class="p-4 sm:p-6 bg-[#f8faff] min-h-screen">
            <div class="max-w-[1400px] mx-auto">

                <div v-if="assigned_shift"
                    class="mb-8 p-1 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-[2rem] shadow-xl shadow-blue-200 overflow-hidden">
                    <div
                        class="bg-white/10 backdrop-blur-md px-8 py-4 flex flex-col md:flex-row items-center justify-between gap-4">
                        <div class="flex items-center gap-4 text-white">
                            <div class="p-3 bg-white/20 rounded-2xl">
                                <component :is="getShiftIcon(assigned_shift.shift_type)" class="size-6" />
                            </div>
                            <div>
                                <p class="text-[10px] font-black uppercase tracking-[0.2em] opacity-80">Assigned Shift
                                </p>
                                <h2 class="text-xl font-black italic uppercase tracking-tight">
                                    {{ assigned_shift.shift_type }} Duty
                                    <span class="font-light opacity-60 ml-2">({{ shiftScheduleDisplay }})</span>
                                </h2>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 bg-black/20 px-4 py-2 rounded-xl text-white">
                            <BellRing class="size-4 animate-bounce" />
                            <span class="text-[10px] font-black uppercase tracking-widest">Active Schedule</span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8">
                    <div class="lg:col-span-8 space-y-6 lg:space-y-8">

                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                            <div class="w-full md:w-auto">
                                <h1
                                    class="text-2xl sm:text-3xl font-black text-slate-900 uppercase tracking-tight italic">
                                    Duty <span class="text-blue-600 font-light">Command</span>
                                </h1>
                                <p
                                    class="text-[10px] sm:text-xs text-slate-400 font-bold uppercase tracking-widest mt-1">
                                    Status: {{ isClockedIn ? 'Active Shift' : 'Off Duty' }}
                                </p>
                            </div>

                            <div
                                class="w-full md:w-auto flex items-center p-1.5 bg-white rounded-2xl sm:rounded-[2rem] shadow-sm border border-slate-100">
                                <div class="px-4 sm:px-6 py-2 text-right border-r border-slate-100 mr-2">
                                    <p
                                        class="text-[8px] sm:text-[9px] font-black text-slate-400 uppercase tracking-widest">
                                        System Time</p>
                                    <p class="text-sm sm:text-lg font-mono font-black text-slate-800 italic">{{
                                        currentTime }}</p>
                                </div>

                                <button @click="handleClockToggle" :disabled="!!today_log?.clock_out" :class="[
                                    'group flex-1 md:flex-none flex items-center justify-center gap-2 sm:gap-3 px-4 sm:px-8 py-3 sm:py-4 rounded-xl sm:rounded-[1.5rem] transition-all duration-500 font-black uppercase text-[10px] sm:text-[11px] tracking-[0.15em] overflow-hidden',
                                    isClockedIn ? 'bg-rose-500 text-white' : 'bg-emerald-500 text-white',
                                    today_log?.clock_out ? 'bg-slate-300 cursor-not-allowed text-slate-500' : 'shadow-lg hover:scale-[1.02]'
                                ]">
                                    <Power class="size-3 sm:size-4" />
                                    <span>{{ clockButtonText }}</span>
                                </button>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div
                                class="bg-white rounded-[2rem] sm:rounded-[2.5rem] p-6 sm:p-8 shadow-sm border border-slate-100 flex flex-col items-center justify-center">
                                <div class="relative size-28 sm:size-36 flex items-center justify-center">
                                    <svg class="size-full -rotate-90" viewBox="0 0 36 36">
                                        <circle cx="18" cy="18" r="16" fill="none" class="stroke-slate-50"
                                            stroke-width="3"></circle>
                                        <circle cx="18" cy="18" r="16" fill="none" class="stroke-blue-600"
                                            stroke-width="3" stroke-dasharray="92, 100" stroke-linecap="round"></circle>
                                    </svg>
                                    <div class="absolute text-center">
                                        <span class="text-2xl sm:text-4xl font-black text-slate-800 italic">92</span>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="bg-blue-600 rounded-[2rem] sm:rounded-[2.5rem] p-6 sm:p-8 shadow-xl shadow-blue-200 text-white flex flex-col justify-between overflow-hidden relative">
                                <div class="z-10">
                                    <p
                                        class="text-blue-100 text-[9px] sm:text-[10px] font-black uppercase tracking-widest">
                                        In/Out Times</p>
                                    <div class="mt-6 flex gap-4">
                                        <div class="bg-white/10 px-4 py-2 rounded-xl">
                                            <p class="text-[8px] uppercase opacity-60">In</p>
                                            <p class="text-xs font-bold">{{ today_log?.clock_in || '--:--' }}</p>
                                        </div>
                                        <div class="bg-white/10 px-4 py-2 rounded-xl">
                                            <p class="text-[8px] uppercase opacity-60">Out</p>
                                            <p class="text-xs font-bold">{{ today_log?.clock_out || '--:--' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div
                            class="bg-white rounded-[2rem] sm:rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
                            <div class="p-6 sm:p-8 border-b border-slate-50 bg-slate-50/20">
                                <h3 class="text-[10px] sm:text-xs font-black text-slate-800 uppercase tracking-[0.2em]">
                                    Recent Cycles</h3>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left min-w-[500px]">
                                    <thead>
                                        <tr
                                            class="text-[8px] sm:text-[9px] font-black text-slate-400 uppercase tracking-widest">
                                            <th class="px-6 py-5">Date</th>
                                            <th class="px-6 py-5 text-center">In/Out Cycle</th>
                                            <th class="px-6 py-5 text-right">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-50">
                                        <tr v-for="log in history" :key="log.id"
                                            class="hover:bg-slate-50/50 transition-colors">
                                            <td class="px-6 py-5 font-black text-slate-700 italic text-xs">{{ log.date
                                                }}</td>
                                            <td class="px-6 py-5 text-center font-mono text-[11px] text-slate-500">
                                                {{ log.clock_in }} — {{ log.clock_out || '---' }}
                                            </td>
                                            <td class="px-6 py-5 text-right">
                                                <span :class="getStatusStyles(log.status)"
                                                    class="px-3 py-1.5 rounded-lg text-[8px] font-black uppercase border">
                                                    {{ log.status }}
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-4">
                        <div
                            class="bg-white rounded-[2rem] sm:rounded-[2.5rem] shadow-sm border border-slate-100 p-6 sm:p-8">
                            <h2 class="text-lg font-black text-slate-800 uppercase italic">February 2026</h2>
                            <div class="grid grid-cols-7 gap-1.5 mt-6 text-center">
                                <span v-for="d in ['S', 'M', 'T', 'W', 'T', 'F', 'S']" :key="d"
                                    class="text-[8px] font-black text-slate-300 uppercase">{{ d }}</span>
                                <div v-for="i in 28" :key="i" :class="[
                                    'aspect-square flex items-center justify-center text-[10px] font-black rounded-lg border',
                                    i === 16 ? 'bg-blue-600 text-white shadow-lg border-blue-600' : 'text-slate-600 border-transparent hover:bg-blue-50'
                                ]">{{ i }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.font-mono {
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
}

button:active {
    transform: scale(0.96);
}
</style>