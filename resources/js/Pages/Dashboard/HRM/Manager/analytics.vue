<script setup>
import { ref, onMounted } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {
    Users,
    UserCheck,
    Clock,
    Wallet,
    TrendingUp,
    FileText,
    Calendar,
    Download,
    Lightbulb,
    Bot,
    MoreHorizontal,
    Search
} from 'lucide-vue-next';

// Shadcn UI Imports
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card';
import { Input } from '@/Components/ui/input';
import { Badge } from '@/Components/ui/badge';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow
} from '@/Components/ui/table';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
} from '@/Components/ui/dialog';

// Reactive State
const isReportModalOpen = ref(false);

const stats = [
    { label: 'Employee Turnover Rate', value: '8.2%', trend: '-1.5%', up: false, icon: Users, color: 'blue' },
    { label: 'Employee Satisfaction', value: '4.8/5', trend: '+0.3', up: true, icon: UserCheck, color: 'emerald' },
    { label: 'Time to Hire', value: '24 days', trend: '-3 days', up: false, icon: Clock, color: 'amber' },
    { label: 'Cost per Hire', value: '₱15,240', trend: '+5.2%', up: true, icon: Wallet, color: 'indigo' },
];

const chartData = [
    { m: 'Jan', h: '85%' }, { m: 'Feb', h: '72%' }, { m: 'Mar', h: '68%' },
    { m: 'Apr', h: '78%' }, { m: 'May', h: '82%' }, { m: 'Jun', h: '88%' },
    { m: 'Jul', h: '92%' }, { m: 'Aug', h: '85%' }, { m: 'Sep', h: '78%' },
    { m: 'Oct', h: '82%' }, { m: 'Nov', h: '86%' }, { m: 'Dec', h: '91%' },
];

const reportForm = useForm({
    name: '',
    type: 'analytical',
    range: 'Last 30 Days',
});

const animateBars = ref(false);
onMounted(() => {
    setTimeout(() => { animateBars.value = true; }, 300);
});
</script>

<template>

    <Head title="HR Analytics & Reports" />

    <AuthenticatedLayout>
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">HR Analytics & Reports</h1>
            <p class="text-gray-500 dark:text-gray-400">Data-driven insights and comprehensive workforce intelligence.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div v-for="stat in stats" :key="stat.label"
                class="bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div :class="`p-2 bg-${stat.color}-50 dark:bg-${stat.color}-900/20 rounded-lg`">
                        <component :is="stat.icon"
                            :class="`h-6 w-6 text-${stat.color}-600 dark:text-${stat.color}-400`" />
                    </div>
                    <span :class="[
                        stat.up ? 'text-emerald-600 bg-emerald-50' : 'text-blue-600 bg-blue-50',
                        'text-xs font-bold px-2 py-1 rounded-full'
                    ]">
                        {{ stat.trend }}
                    </span>
                </div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ stat.label }}</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stat.value }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <div class="lg:col-span-2 space-y-8">
                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
                    <div
                        class="p-6 border-b border-gray-50 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <h2 class="text-lg font-bold text-gray-900 dark:text-white">Key HR Metrics Trends</h2>
                            <p class="text-sm text-gray-500">Employee Satisfaction Score (Monthly Average)</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <select
                                class="text-sm border-gray-200 dark:border-gray-600 dark:bg-gray-700 rounded-xl px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500">
                                <option>Last 30 Days</option>
                                <option>Last Quarter</option>
                            </select>
                            <Button variant="outline" size="icon" class="rounded-xl">
                                <Download class="h-4 w-4" />
                            </Button>
                        </div>
                    </div>
                    <div class="p-6">
                        <div
                            class="h-[250px] flex items-end justify-between px-4 relative border-b border-gray-100 dark:border-gray-700">
                            <div v-for="bar in chartData" :key="bar.m"
                                class="relative group flex flex-col items-center w-full mx-1">
                                <div class="w-full bg-blue-600 dark:bg-blue-500 rounded-t-md transition-all duration-1000 ease-out opacity-80 group-hover:opacity-100"
                                    :style="{ height: animateBars ? bar.h : '0%' }">
                                </div>
                                <span class="mt-4 text-[10px] text-gray-400 font-bold uppercase tracking-tighter">{{
                                    bar.m }}</span>
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-4 mt-8 text-center">
                            <div>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">4.8</p>
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Current Score
                                </p>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-emerald-600">+0.3</p>
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Growth</p>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-blue-600">92%</p>
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Efficiency</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-gray-50 dark:border-gray-700 flex items-center justify-between">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">Department Analysis</h2>
                        <button class="text-sm font-bold text-blue-600 hover:text-blue-700">View Full Report</button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50/50 dark:bg-gray-900/50">
                                    <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest">
                                        Department</th>
                                    <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest">
                                        Headcount</th>
                                    <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest">
                                        Turnover</th>
                                    <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest">
                                        Status</th>
                                    <th
                                        class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest text-right">
                                        Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                                <tr class="group hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center font-bold text-sm text-gray-900 dark:text-white">
                                            <div
                                                class="h-8 w-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center mr-3">
                                                <Users class="h-4 w-4" />
                                            </div>
                                            HR Department
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-600 dark:text-gray-400">24 <span
                                            class="text-emerald-500 ml-1">+2</span></td>
                                    <td class="px-6 py-4 text-sm font-bold text-gray-900 dark:text-white">5.2%</td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="bg-emerald-100 text-emerald-700 px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider">Optimal</span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <button
                                            class="p-2 hover:bg-white rounded-lg border border-transparent hover:border-gray-100 shadow-sm transition-all">
                                            <MoreHorizontal class="h-4 w-4 text-gray-400" />
                                        </button>
                                    </td>
                                </tr>
                                <tr class="group hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center font-bold text-sm text-gray-900 dark:text-white">
                                            <div
                                                class="h-8 w-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center mr-3">
                                                <TrendingUp class="h-4 w-4" />
                                            </div>
                                            Production
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-600 dark:text-gray-400">156 <span
                                            class="text-blue-500 ml-1">+12</span></td>
                                    <td class="px-6 py-4 text-sm font-bold text-gray-900 dark:text-white">12.4%</td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="bg-amber-100 text-amber-700 px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider">Stable</span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <button
                                            class="p-2 hover:bg-white rounded-lg border border-transparent hover:border-gray-100 shadow-sm transition-all">
                                            <MoreHorizontal class="h-4 w-4 text-gray-400" />
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="space-y-8">
                <!-- <div class="bg-blue-600 rounded-2xl p-6 text-white shadow-lg shadow-blue-200 dark:shadow-none">
                    <h3 class="text-lg font-bold mb-2">Generate Reports</h3>
                    <p class="text-blue-100 text-sm mb-6">Create custom analytical data exports for executive review.
                    </p>
                    <div class="space-y-3">
                        <Button @click="isReportModalOpen = true"
                            class="w-full bg-white text-blue-600 hover:bg-blue-50 border-none rounded-xl font-bold py-6">
                            <FileText class="mr-2 h-4 w-4" /> Custom Report
                        </Button>
                        <Button variant="ghost" class="w-full text-white hover:bg-blue-700 rounded-xl font-bold">
                            <Calendar class="mr-2 h-4 w-4" /> Schedule Auto-Report
                        </Button>
                    </div>
                </div> -->

                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="font-bold text-gray-900 dark:text-white">AI Predictions</h3>
                        <Badge variant="outline"
                            class="text-blue-600 border-blue-200 animate-pulse text-[10px] font-bold">LIVE</Badge>
                    </div>
                    <div class="space-y-4">
                        <div
                            class="p-4 rounded-xl bg-gray-50 dark:bg-gray-900/50 border border-gray-100 dark:border-gray-700 flex gap-4">
                            <Lightbulb class="h-5 w-5 text-amber-500 shrink-0" />
                            <div>
                                <h4 class="text-sm font-bold text-gray-900 dark:text-white">Turnover Alert</h4>
                                <p class="text-xs text-gray-500 mt-1 leading-relaxed">High risk of attrition in
                                    Production (15%) next quarter.</p>
                            </div>
                        </div>
                        <div
                            class="p-4 rounded-xl bg-gray-50 dark:bg-gray-900/50 border border-gray-100 dark:border-gray-700 flex gap-4">
                            <Bot class="h-5 w-5 text-blue-500 shrink-0" />
                            <div>
                                <h4 class="text-sm font-bold text-gray-900 dark:text-white">Hiring Efficiency</h4>
                                <p class="text-xs text-gray-500 mt-1 leading-relaxed">QC roles take 18% longer to fill
                                    than company average.</p>
                            </div>
                        </div>
                        <Button variant="outline"
                            class="w-full rounded-xl py-5 border-gray-200 text-gray-600 font-bold text-xs uppercase tracking-widest">
                            Generate More Insights
                        </Button>
                    </div>
                </div>
            </div>
        </div>

        <Dialog v-model:open="isReportModalOpen">
            <DialogContent class="sm:max-w-[500px] rounded-2xl p-0 overflow-hidden border-none shadow-2xl">
                <div class="p-8">
                    <DialogHeader>
                        <DialogTitle class="text-2xl font-bold tracking-tight">Generate Custom Report</DialogTitle>
                        <DialogDescription class="text-gray-500">Choose your parameters to build a detailed HR report.
                        </DialogDescription>
                    </DialogHeader>
                    <div class="space-y-6 py-6">
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">Report Name</label>
                            <Input v-model="reportForm.name" placeholder="e.g., Annual HR Analytics 2024"
                                class="rounded-xl border-gray-200 py-6" />
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">Type</label>
                                <select v-model="reportForm.type"
                                    class="w-full bg-gray-50 border border-gray-200 text-sm rounded-xl p-3 outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="analytical">Analytical Dashboard</option>
                                    <option value="executive">Executive Summary</option>
                                </select>
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">Date
                                    Range</label>
                                <select v-model="reportForm.range"
                                    class="w-full bg-gray-50 border border-gray-200 text-sm rounded-xl p-3 outline-none focus:ring-2 focus:ring-blue-500">
                                    <option>Last 30 Days</option>
                                    <option>Last Quarter</option>
                                    <option>Year to Date</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="flex gap-3 mt-4">
                        <Button variant="outline" class="flex-1 rounded-xl py-6 font-bold"
                            @click="isReportModalOpen = false">Cancel</Button>
                        <Button
                            class="flex-1 bg-blue-600 hover:bg-blue-700 text-white rounded-xl py-6 font-bold shadow-md shadow-blue-100"
                            @click="isReportModalOpen = false">
                            Generate Report
                        </Button>
                    </div>
                </div>
            </DialogContent>
        </Dialog>
    </AuthenticatedLayout>
</template>

<style scoped>
/* Keeping simple bar animations */
</style>