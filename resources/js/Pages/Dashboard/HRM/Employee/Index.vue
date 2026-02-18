<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted, computed } from 'vue';

// Props passed from DashboardController
const props = defineProps({
    auth: Object,
    employees: {
        type: Array,
        default: () => []
    },
    stats: {
        type: Object,
        default: () => ({
            total: 0,
            present: 0,
            on_leave: 0
        })
    }
});

const search = ref('');

// Filtered employee list for search functionality
const filteredEmployees = computed(() => {
    return props.employees.filter(user =>
        user.name.toLowerCase().includes(search.value.toLowerCase()) ||
        user.email.toLowerCase().includes(search.value.toLowerCase()) ||
        user.role.toLowerCase().includes(search.value.toLowerCase())
    );
});

// Real-time Logic Safety Check
onMounted(() => {
    // If Echo is not installed, it will console log instead of white-screening
    if (typeof Echo !== 'undefined') {
        Echo.channel('users')
            .listen('UserUpdated', (e) => {
                router.reload({ preserveScroll: true, only: ['employees', 'stats'] });
            });
    } else {
        console.warn('Laravel Echo is not defined. Using manual refresh mode.');
    }
});

onUnmounted(() => {
    if (typeof Echo !== 'undefined') {
        Echo.leave('users');
    }
});

const getStatusClass = (user) => {
    if (user.is_active) return 'bg-green-100 text-green-800';
    return 'bg-gray-100 text-gray-800';
};
</script>

<template>

    <Head title="Employee Management" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    HRM Workforce Directory
                </h2>
                <Link :href="route('applicants.index')"
                    class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 transition">
                    View Applicants
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                        <p class="text-sm font-medium text-gray-500 uppercase">Total Users</p>
                        <h3 class="text-2xl font-bold text-gray-900">{{ stats.total }}</h3>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                        <p class="text-sm font-medium text-gray-500 uppercase">Active Status</p>
                        <h3 class="text-2xl font-bold text-green-600">{{ stats.present }}</h3>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                        <p class="text-sm font-medium text-gray-500 uppercase">Leave Balance</p>
                        <h3 class="text-2xl font-bold text-yellow-600">{{ props.stats.leaveBalance }}</h3>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                        <p class="text-sm font-medium text-gray-500 uppercase">Tasks</p>
                        <h3 class="text-2xl font-bold text-blue-600">{{ props.stats.assignedTasks }}</h3>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <h3 class="text-lg font-bold text-gray-900">User Table (All Departments)</h3>
                            <div class="relative">
                                <input v-model="search" type="text" placeholder="Search name or role..."
                                    class="w-full md:w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 text-sm" />
                                <div class="absolute left-3 top-2.5 text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        User</th>
                                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Department</th>
                                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Role/Pos
                                    </th>
                                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th
                                        class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr v-for="user in filteredEmployees" :key="user.id"
                                    class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div
                                                class="h-8 w-8 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-700 font-bold text-xs">
                                                {{ user.name.charAt(0) }}
                                            </div>
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
                                                <div class="text-[10px] text-gray-500">{{ user.email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ user.department || 'Unassigned' }}
                                    </td>
                                    <td class="px-6 py-4 text-xs font-medium text-gray-600 uppercase">
                                        {{ user.role }} - {{ user.position }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            :class="`px-2 py-0.5 rounded-full text-[10px] font-bold ${getStatusClass(user)}`">
                                            {{ user.is_active ? 'ACTIVE' : 'INACTIVE' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm font-medium">
                                        <button class="text-indigo-600 hover:text-indigo-900">View Details</button>
                                    </td>
                                </tr>
                                <tr v-if="filteredEmployees.length === 0">
                                    <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                                        No users found in the database.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>