<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { ref } from 'vue';
import {
    Clock,
    Calendar as CalendarIcon,
    ChevronLeft,
    ChevronRight,
    MoreHorizontal,
    FileText,
    CreditCard,
    CheckCircle2,
    UserPlus,
    DoorOpen,
    BadgeCheck,
    Flag,
    Users,
    Layers
} from 'lucide-vue-next';

const isClockedIn = ref(true);

// Mock Data for the UI
const attendanceHistory = [
    { date: 'Feb 15', clockIn: '08:00 AM', status: 'On-Time' },
    { date: 'Feb 14', clockIn: '08:15 AM', status: 'Late' },
];

const schedule = [
    { time: '08:30', title: 'Shift Start', type: 'Work', color: 'bg-purple-100 text-purple-700 border-purple-200' },
    { time: '10:00', title: 'Machine Maintenance', type: 'Task', color: 'bg-amber-100 text-amber-700 border-amber-200' },
    { time: '13:00', title: 'Quality Check', type: 'Meeting', color: 'bg-rose-100 text-rose-700 border-rose-200' },
];
</script>

<template>

    <Head title="Employee Dashboard" />

    <AuthenticatedLayout>
        <div class="p-6 bg-[#f8faff] min-h-screen">
            <div class="max-w-[1400px] mx-auto">

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

                    <div class="lg:col-span-8 space-y-8">

                        <div class="flex justify-between items-center">
                            <h1 class="text-3xl font-bold text-slate-800">Dashboard</h1>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div
                                class="bg-white rounded-[2rem] p-8 shadow-sm border border-slate-100 flex flex-col items-center justify-center relative overflow-hidden">
                                <span class="absolute top-4 left-6 text-sm font-semibold text-slate-400">Monthly
                                    Attendance</span>
                                <div class="relative size-32 flex items-center justify-center">
                                    <svg class="size-full -rotate-90" viewBox="0 0 36 36">
                                        <circle cx="18" cy="18" r="16" fill="none" class="stroke-slate-100"
                                            stroke-width="3"></circle>
                                        <circle cx="18" cy="18" r="16" fill="none" class="stroke-blue-600"
                                            stroke-width="3" stroke-dasharray="85, 100" stroke-linecap="round"></circle>
                                    </svg>
                                    <span class="absolute text-3xl font-bold text-slate-800">85</span>
                                </div>
                                <div class="mt-4 flex gap-4 text-xs font-medium uppercase tracking-tighter">
                                    <span class="flex items-center gap-1.5"><span
                                            class="size-2 rounded-full bg-blue-600"></span> On-Time</span>
                                    <span class="flex items-center gap-1.5"><span
                                            class="size-2 rounded-full bg-slate-200"></span> Late/Absent</span>
                                </div>
                            </div>

                            <div
                                class="relative group h-full min-h-[320px] rounded-[2rem] overflow-hidden shadow-xl shadow-slate-200">
                                <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?q=80&w=600&auto=format&fit=crop"
                                    alt="Profile"
                                    class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" />

                                <div
                                    class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent">
                                </div>

                                <div class="absolute inset-0 p-8 flex flex-col justify-end text-white">
                                    <div class="flex items-center gap-2 mb-2">
                                        <h2 class="text-2xl font-bold">Billy Macalawa</h2>
                                        <BadgeCheck class="size-5 text-white fill-blue-500" />
                                    </div>

                                    <p class="text-slate-200 text-sm leading-relaxed max-w-[200px] mb-6">
                                        HR Staff | Joined Jan 2020 | Employee ID: 12345
                                    </p>

                                    <div class="flex items-center justify-between">
                                        <div class="flex gap-4">
                                            <div class="flex items-center gap-1.5">
                                                <Flag class="size-4 text-slate-300" />
                                                <span class="text-sm font-bold">20</span>
                                            </div>
                                            <div class="flex items-center gap-1.5">
                                                <DoorOpen class="size-4 text-slate-300" />
                                                <span class="text-sm font-bold">8</span>
                                            </div>
                                        </div>

                                        <button
                                            class="bg-white text-slate-900 px-6 py-2.5 rounded-2xl text-sm font-bold flex items-center gap-2 hover:bg-slate-100 transition-colors shadow-lg">
                                            View Full Profile
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div
                                class="bg-indigo-600 rounded-[2rem] p-6 text-white flex flex-col justify-between shadow-lg shadow-indigo-200">
                                <div class="flex items-center gap-3">
                                    <div class="bg-white/20 p-2 rounded-lg">
                                        <Clock class="size-5" />
                                    </div>
                                    <h3 class="font-bold">Active Shift</h3>
                                </div>
                                <div class="mt-4">
                                    <p class="text-xs text-indigo-100 uppercase tracking-widest">Started at 08:00 AM</p>
                                    <p class="text-sm font-medium mt-1">15 Min remaining until break</p>
                                </div>
                                <div class="mt-6 flex items-center justify-between">
                                    <div class="flex -space-x-2">
                                        <div v-for="i in 2" :key="i"
                                            class="size-8 rounded-full border-2 border-indigo-600 bg-slate-200 flex items-center justify-center text-[10px] text-slate-600 font-bold uppercase tracking-tighter">
                                            US</div>
                                    </div>
                                    <ChevronRight class="size-5 text-indigo-200" />
                                </div>
                            </div>

                            <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-slate-100">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-blue-600 font-bold flex items-center gap-2">
                                        <FileText class="size-4" /> Attendance Records
                                    </h3>
                                    <MoreHorizontal class="size-4 text-slate-400" />
                                </div>
                                <div class="space-y-3">
                                    <div v-for="log in attendanceHistory" :key="log.date"
                                        class="flex items-center justify-between p-2 hover:bg-slate-50 rounded-xl transition-colors">
                                        <div class="flex items-center gap-3">
                                            <div class="bg-blue-50 p-2 rounded-lg">
                                                <CalendarIcon class="size-4 text-blue-600" />
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold text-slate-700">{{ log.date }}</p>
                                                <p class="text-[10px] text-slate-400 uppercase tracking-widest">{{
                                                    log.status }}</p>
                                            </div>
                                        </div>
                                        <span class="text-xs font-bold text-slate-500">{{ log.clockIn }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div
                            class="bg-white rounded-[2rem] p-8 shadow-sm border border-slate-100 min-h-[300px] flex flex-col">
                            <div class="flex justify-between items-center mb-8">
                                <h3 class="font-bold text-slate-800">Productivity Trend</h3>
                                <select
                                    class="bg-slate-50 border-none rounded-lg text-xs font-bold text-slate-500 uppercase">
                                    <option>Month</option>
                                </select>
                            </div>
                            <div class="flex-1 flex items-end gap-2 px-4 relative">
                                <div
                                    class="absolute top-0 left-0 w-full h-full border-b border-slate-100 flex items-center justify-center">
                                    <div class="w-full h-[1px] bg-slate-50"></div>
                                </div>
                                <svg class="w-full h-full" viewBox="0 0 400 100" preserveAspectRatio="none">
                                    <path d="M0 80 Q 50 20, 100 70 T 200 50 T 300 80 T 400 20" fill="none"
                                        stroke="#2563eb" stroke-width="3" />
                                    <circle cx="100" cy="70" r="4" fill="white" stroke="#2563eb" stroke-width="2" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-4">
                        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-8 h-full">
                            <div class="flex justify-between items-center mb-8">
                                <h2 class="text-lg font-bold text-slate-800">Jan 10, Tuesday</h2>
                                <div class="flex gap-2">
                                    <ChevronLeft class="size-5 text-slate-400 cursor-pointer" />
                                    <ChevronRight class="size-5 text-slate-400 cursor-pointer" />
                                </div>
                            </div>

                            <div class="grid grid-cols-7 gap-2 mb-8">
                                <span v-for="d in ['S', 'M', 'T', 'W', 'T', 'F', 'S']" :key="d"
                                    class="text-center text-xs font-bold text-slate-300">{{ d }}</span>
                                <div v-for="i in 31" :key="i" :class="[
                                    'size-10 flex items-center justify-center text-sm font-bold rounded-full transition-all cursor-pointer',
                                    i === 10 ? 'bg-blue-600 text-white shadow-lg shadow-blue-200' : 'text-slate-600 hover:bg-slate-50',
                                    i === 16 ? 'bg-amber-400 text-white shadow-lg shadow-amber-200' : ''
                                ]">
                                    {{ i }}
                                </div>
                            </div>

                            <hr class="border-slate-100 mb-8" />

                            <div class="space-y-8 relative">
                                <div class="absolute left-[39px] top-0 h-full w-[1px] bg-slate-100"></div>

                                <div v-for="item in schedule" :key="item.time" class="flex gap-6 relative">
                                    <span class="text-xs font-bold text-slate-400 w-10 text-right">{{ item.time
                                    }}</span>
                                    <div class="flex-1">
                                        <div :class="item.color"
                                            class="p-4 rounded-2xl border shadow-sm transition-transform hover:scale-[1.02] cursor-pointer">
                                            <p class="text-xs font-bold opacity-70 uppercase tracking-widest">{{
                                                item.type }}</p>
                                            <p class="text-sm font-bold mt-1">{{ item.title }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>