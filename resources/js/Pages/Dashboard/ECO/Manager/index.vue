<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import {
    PencilSquareIcon,
    TrashIcon,
    MagnifyingGlassIcon,
    FunnelIcon,
    PlusIcon,
    ArrowUpOnSquareIcon,
    ArrowDownOnSquareIcon,
    BuildingOfficeIcon,
    CheckIcon,
    XMarkIcon,
    ClockIcon,
    UserGroupIcon,
    BanknotesIcon,
    ShieldCheckIcon,
    ArrowRightOnRectangleIcon
} from '@heroicons/vue/24/outline';

// Reactive Tab State
const activeTab = ref('approvals');

// B2B Company Data for Approval
const pendingCompanies = [
    { id: 1, name: 'TechLogistics Corp', contact: 'Juan Dela Cruz', mobile: '+63 915 234 5678', email: 'juan@techlog.ph', status: 'Pending', date: 'Feb 16, 2026' },
    { id: 3, name: 'Cavite IT Solutions', contact: 'Mark Reyes', mobile: '+63 920 111 2233', email: 'mark@cavite-it.ph', status: 'Pending', date: 'Feb 17, 2026' },
];

// Data for Verified Companies
const verifiedCompanies = [
    { id: 2, name: 'Manila Build Supplies', contact: 'Maria Santos', mobile: '+63 917 555 1234', email: 'maria@mbs.com.ph', status: 'Approved', score: '98', joinDate: 'Jan 12, 2026' },
    { id: 4, name: 'Cebu Textile Hub', contact: 'Robert Lim', mobile: '+63 932 444 5566', email: 'robert@cebutextile.ph', status: 'Approved', score: '95', joinDate: 'Jan 20, 2026' },
];

const stats = [
    { label: 'Total Clients', value: '2,000', icon: UserGroupIcon },
    { label: 'Pending Approval', value: '12', icon: ClockIcon },
    { label: 'Verified Partners', value: '1,800', icon: ShieldCheckIcon },
    { label: 'Total B2B Rev', value: '₱4.2M', icon: BanknotesIcon },
];

const handleApprove = (id) => console.log('Approved Company:', id);
const handleReject = (id) => console.log('Rejected Company:', id);
</script>

<template>

    <Head title="Client Approval Dashboard" />

    <AuthenticatedLayout>


        <div class="p-4 sm:p-6 lg:p-8 bg-[#F5F6FA] min-h-screen">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-8">
                <div v-for="stat in stats" :key="stat.label"
                    class="bg-white p-4 sm:p-5 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between transition hover:shadow-md">
                    <div>
                        <p class="text-[10px] sm:text-xs font-bold text-gray-400 uppercase tracking-tighter">{{
                            stat.label }}</p>
                        <p class="text-xl sm:text-2xl font-black text-gray-800 mt-1">{{ stat.value }}</p>
                    </div>
                    <div class="p-2 sm:p-3 bg-purple-50 rounded-lg">
                        <component :is="stat.icon" class="h-5 w-5 sm:h-6 sm:w-6 text-[#6E49CB]" />
                    </div>
                </div>
            </div>

            <div class="flex overflow-x-auto space-x-4 mb-6 border-b border-gray-200 no-scrollbar">
                <button @click="activeTab = 'approvals'"
                    :class="[activeTab === 'approvals' ? 'bg-white border-t border-x border-gray-200 rounded-t-lg text-[#5D44A7] font-bold' : 'text-gray-400 font-medium hover:text-gray-600']"
                    class="whitespace-nowrap px-4 sm:px-6 py-2 text-sm transition-all duration-200">
                    Account Approvals
                </button>
                <button @click="activeTab = 'verified'"
                    :class="[activeTab === 'verified' ? 'bg-white border-t border-x border-gray-200 rounded-t-lg text-[#5D44A7] font-bold' : 'text-gray-400 font-medium hover:text-gray-600']"
                    class="whitespace-nowrap px-4 sm:px-6 py-2 text-sm transition-all duration-200">
                    Verified Companies
                </button>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between mb-8 space-y-4 lg:space-y-0">
                    <div class="flex flex-col sm:flex-row sm:items-center space-y-3 sm:space-y-0 sm:space-x-3">
                        <h1 class="text-xl sm:text-2xl font-bold text-gray-800 tracking-tight italic">
                            {{ activeTab === 'approvals' ? 'Business Registrations' : 'Verified Business Partners' }}
                        </h1>
                        <div class="flex flex-wrap gap-2">
                            <button
                                class="flex items-center px-3 py-2 bg-[#6E49CB] text-white rounded-md text-xs sm:text-sm font-medium hover:bg-[#5D44A7] transition shadow-md">
                                <PlusIcon class="h-4 w-4 mr-1 sm:mr-2" /> {{ activeTab === 'approvals' ?
                                    'RegisterClient' : 'Add Partner' }}
                            </button>
                            <button
                                class="flex items-center px-3 py-2 border border-gray-300 text-gray-600 rounded-md text-xs sm:text-sm font-medium hover:bg-gray-50 transition">
                                <ArrowDownOnSquareIcon class="h-4 w-4 mr-1 sm:mr-2" /> Export Logs
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between lg:justify-end space-x-4 sm:space-x-6">
                        <div
                            class="text-right text-[10px] text-gray-400 font-bold uppercase tracking-widest hidden sm:block">
                            <p>{{ activeTab === 'approvals' ? 'Verification Rate' : 'Total Active' }}: <span
                                    class="text-[#6E49CB]">92%</span></p>
                        </div>
                        <button
                            class="flex items-center px-4 sm:px-6 py-2 bg-[#6E49CB] text-white rounded-md text-xs sm:text-sm font-medium shadow-md">
                            <FunnelIcon class="h-4 w-4 mr-2" /> Filter
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto -mx-4 sm:mx-0">
                    <div class="inline-block min-w-full align-middle px-4 sm:px-0">
                        <table class="min-w-full text-left">
                            <thead>
                                <tr
                                    class="text-gray-400 text-[10px] sm:text-[11px] font-bold uppercase tracking-[0.1em] border-b border-gray-100 bg-gray-50/50 italic">
                                    <th class="p-3 sm:p-4 font-normal">Icon</th>
                                    <th class="p-3 sm:p-4 font-normal text-gray-600 font-bold">Business Name</th>
                                    <th class="p-3 sm:p-4 font-normal">Contact Person</th>
                                    <th class="p-3 sm:p-4 font-normal hidden md:table-cell">
                                        {{ activeTab === 'approvals' ? 'Contact No.' : 'Member Score' }}
                                    </th>
                                    <th class="p-3 sm:p-4 font-normal text-center sm:text-left">Status</th>
                                    <th class="p-3 sm:p-4 font-normal hidden lg:table-cell">
                                        {{ activeTab === 'approvals' ? 'Applied Date' : 'Verified Since' }}
                                    </th>
                                    <th class="p-3 sm:p-4 font-normal text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <tr v-if="activeTab === 'approvals'" v-for="company in pendingCompanies"
                                    :key="company.id" class="group hover:bg-gray-50 transition duration-150">
                                    <td class="p-3 sm:p-4">
                                        <div
                                            class="h-8 w-8 sm:h-10 sm:w-10 bg-purple-50 rounded-full flex items-center justify-center border border-purple-100 text-center mx-auto sm:mx-0">
                                            <BuildingOfficeIcon class="h-5 w-5 sm:h-6 sm:w-6 text-[#6E49CB]" />
                                        </div>
                                    </td>
                                    <td
                                        class="p-3 sm:p-4 font-bold text-gray-800 text-xs sm:text-sm italic whitespace-nowrap">
                                        {{ company.name }}</td>
                                    <td
                                        class="p-3 sm:p-4 text-gray-600 text-xs sm:text-sm font-medium whitespace-nowrap">
                                        {{ company.contact }}</td>
                                    <td
                                        class="p-3 sm:p-4 text-gray-500 text-[10px] sm:text-xs hidden md:table-cell whitespace-nowrap">
                                        {{ company.mobile }}</td>
                                    <td class="p-3 sm:p-4">
                                        <span
                                            class="px-2 sm:px-3 py-1 rounded-full text-[9px] sm:text-[10px] font-bold uppercase tracking-widest bg-orange-100 text-orange-600">
                                            Pending
                                        </span>
                                    </td>
                                    <td
                                        class="p-3 sm:p-4 text-gray-400 text-[10px] sm:text-xs font-semibold hidden lg:table-cell whitespace-nowrap">
                                        {{ company.date }}</td>
                                    <td class="p-3 sm:p-4">
                                        <div class="flex items-center justify-end space-x-1 sm:space-x-2">
                                            <button @click="handleApprove(company.id)"
                                                class="p-1.5 sm:p-2 bg-emerald-50 text-emerald-600 rounded-lg hover:bg-emerald-600 hover:text-white transition shadow-sm">
                                                <CheckIcon class="h-4 w-4" />
                                            </button>
                                            <button @click="handleReject(company.id)"
                                                class="p-1.5 sm:p-2 bg-rose-50 text-rose-500 rounded-lg hover:bg-rose-500 hover:text-white transition shadow-sm">
                                                <XMarkIcon class="h-4 w-4" />
                                            </button>
                                            <button
                                                class="px-2 sm:px-4 py-1.5 bg-[#6E49CB] text-white rounded text-[9px] sm:text-[10px] font-bold uppercase hover:bg-[#5D44A7] transition italic">Review</button>
                                        </div>
                                    </td>
                                </tr>

                                <tr v-if="activeTab === 'verified'" v-for="company in verifiedCompanies"
                                    :key="company.id" class="group hover:bg-gray-50 transition duration-150">
                                    <td class="p-3 sm:p-4">
                                        <div
                                            class="h-8 w-8 sm:h-10 sm:w-10 bg-emerald-50 rounded-full flex items-center justify-center border border-emerald-100 text-center mx-auto sm:mx-0">
                                            <ShieldCheckIcon class="h-5 w-5 sm:h-6 sm:w-6 text-emerald-600" />
                                        </div>
                                    </td>
                                    <td
                                        class="p-3 sm:p-4 font-bold text-gray-800 text-xs sm:text-sm italic whitespace-nowrap">
                                        {{ company.name }}</td>
                                    <td
                                        class="p-3 sm:p-4 text-gray-600 text-xs sm:text-sm font-medium whitespace-nowrap">
                                        {{ company.contact }}</td>
                                    <td class="p-3 sm:p-4 hidden md:table-cell">
                                        <div class="flex items-center space-x-2">
                                            <div class="w-16 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                                <div class="bg-emerald-500 h-full" :style="`width: ${company.score}%`">
                                                </div>
                                            </div>
                                            <span class="text-[10px] font-bold text-emerald-600">{{ company.score
                                            }}%</span>
                                        </div>
                                    </td>
                                    <td class="p-3 sm:p-4">
                                        <span
                                            class="px-2 sm:px-3 py-1 rounded-full text-[9px] sm:text-[10px] font-bold uppercase tracking-widest bg-emerald-100 text-emerald-600">
                                            Verified
                                        </span>
                                    </td>
                                    <td
                                        class="p-3 sm:p-4 text-gray-400 text-[10px] sm:text-xs font-semibold hidden lg:table-cell whitespace-nowrap">
                                        {{ company.joinDate }}</td>
                                    <td class="p-3 sm:p-4">
                                        <div class="flex items-center justify-end space-x-1 sm:space-x-2">
                                            <button
                                                class="p-1.5 sm:p-2 text-gray-400 hover:text-[#6E49CB] hover:bg-purple-50 rounded-lg transition">
                                                <PencilSquareIcon class="h-4 w-4" />
                                            </button>
                                            <button
                                                class="p-1.5 sm:p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition">
                                                <TrashIcon class="h-4 w-4" />
                                            </button>
                                            <button
                                                class="flex items-center px-2 sm:px-4 py-1.5 bg-gray-900 text-white rounded text-[9px] sm:text-[10px] font-bold uppercase hover:bg-black transition italic">
                                                <ArrowRightOnRectangleIcon class="h-3 w-3 mr-1" /> Login
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