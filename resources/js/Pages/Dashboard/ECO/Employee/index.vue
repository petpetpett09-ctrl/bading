<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import {
    Plus,
    Search,
    Pencil,
    Trash2,
    Package,
    Layers,
    Boxes,
    BarChart3,
    X,
    Filter,
    ArrowDownToLine,
    ClipboardList,
    Archive,
    ShoppingCart
} from 'lucide-vue-next';

const props = defineProps({
    products: {
        type: Object,
        default: () => ({ data: [], links: [], meta: {} }),
    },
    filters: { type: Object, default: () => ({ search: '' }) },
});

// Tab State for Catalog Management
const activeTab = ref('inventory'); // 'inventory' or 'bulk-pricing'

const search = ref(props.filters.search);
let searchTimeout;
const updateSearch = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(route('eco.employee.dashboard'), { search: search.value }, { preserveState: true, replace: true });
    }, 300);
};

// Mock Data for Staff-side Catalog Management
const catalogItems = [
    { id: 1, name: 'Industrial Grade Silk', sku: 'TEX-SILK-001', stock: 1200, category: 'Raw Materials', bulk_min: 50, price: '₱1,200/kg' },
    { id: 2, name: 'Premium Cotton Roll', sku: 'TEX-COT-042', stock: 450, category: 'Fabrics', bulk_min: 20, price: '₱850/roll' },
];

const stats = computed(() => [
    { label: 'Total SKUs', value: '142', icon: Package, color: 'text-purple-600', bg: 'bg-purple-50' },
    { label: 'Low Stock Alert', value: '5', icon: Archive, color: 'text-rose-600', bg: 'bg-rose-50' },
    { label: 'Active Bulk Tiers', value: '12', icon: Layers, color: 'text-blue-600', bg: 'bg-blue-50' },
    { label: 'Pending Updates', value: '3', icon: ClipboardList, color: 'text-amber-600', bg: 'bg-amber-50' },
]);
</script>

<template>

    <Head title="Product Catalog Management" />

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

            <div class="flex overflow-x-auto space-x-4 mb-6 border-b border-gray-200 no-scrollbar">
                <button @click="activeTab = 'inventory'"
                    :class="[activeTab === 'inventory' ? 'bg-white border-t border-x border-gray-200 rounded-t-lg text-[#5D44A7] font-bold' : 'text-gray-400 font-medium hover:text-gray-600']"
                    class="whitespace-nowrap px-4 sm:px-6 py-2 text-sm transition-all duration-200">
                    Product Catalog
                </button>
                <button @click="activeTab = 'bulk-pricing'"
                    :class="[activeTab === 'bulk-pricing' ? 'bg-white border-t border-x border-gray-200 rounded-t-lg text-[#5D44A7] font-bold' : 'text-gray-400 font-medium hover:text-gray-600']"
                    class="whitespace-nowrap px-4 sm:px-6 py-2 text-sm transition-all duration-200">
                    Bulk Pricing Specs
                </button>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-6">

                <div class="flex flex-col lg:flex-row lg:items-center justify-between mb-8 space-y-4 lg:space-y-0">
                    <div class="flex flex-col sm:flex-row sm:items-center space-y-3 sm:space-y-0 sm:space-x-3">
                        <h1 class="text-xl sm:text-2xl font-bold text-gray-800 tracking-tight italic uppercase">
                            {{ activeTab === 'inventory' ? 'Catalog Management' : 'Bulk Pricing Configurations' }}
                        </h1>
                        <div class="flex flex-wrap gap-2">
                            <button
                                class="flex items-center px-4 py-2 bg-[#6E49CB] text-white rounded-md text-sm font-medium hover:bg-[#5D44A7] transition shadow-md">
                                <Plus class="h-4 w-4 mr-2" /> {{ activeTab === 'inventory' ? 'Add Product' :
                                'AddBulkTier' }}
                            </button>
                            <button
                                class="flex items-center px-4 py-2 border border-gray-300 text-gray-600 rounded-md text-sm font-medium hover:bg-gray-50 transition">
                                <ArrowDownToLine class="h-4 w-4 mr-2" /> Export Catalog
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center space-x-4 justify-between lg:justify-end">
                        <div class="relative w-full max-w-xs">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center">
                                <Search class="h-4 w-4 text-gray-400" />
                            </span>
                            <input v-model="search" @input="updateSearch" type="text"
                                placeholder="Search SKU or Name..."
                                class="block w-full pl-10 pr-3 py-2 border border-gray-200 rounded-lg text-xs focus:ring-[#6E49CB] focus:border-[#6E49CB]" />
                        </div>
                        <button
                            class="flex items-center px-4 py-2 bg-[#6E49CB] text-white rounded-md text-xs font-bold uppercase tracking-widest shadow-md">
                            <Filter class="h-4 w-4 mr-2" /> Filter
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto -mx-4 sm:mx-0">
                    <div class="inline-block min-w-full align-middle px-4 sm:px-0">
                        <table v-if="activeTab === 'inventory'" class="min-w-full text-left">
                            <thead>
                                <tr
                                    class="text-gray-400 text-[10px] sm:text-[11px] font-bold uppercase tracking-[0.1em] border-b border-gray-100 bg-gray-50/50 italic">
                                    <th class="p-4 font-normal text-gray-600 font-bold">Product Specs</th>
                                    <th class="p-4 font-normal">SKU</th>
                                    <th class="p-4 font-normal hidden md:table-cell">Category</th>
                                    <th class="p-4 font-normal">Stock Level</th>
                                    <th class="p-4 font-normal">Unit Price</th>
                                    <th class="p-4 font-normal text-right px-8">Operation</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <tr v-for="item in catalogItems" :key="item.id"
                                    class="group hover:bg-gray-50 transition duration-150">
                                    <td class="p-4">
                                        <div class="flex items-center">
                                            <div
                                                class="h-10 w-10 bg-purple-50 rounded-lg flex items-center justify-center mr-3 border border-purple-100 group-hover:bg-white transition">
                                                <Boxes class="h-5 w-5 text-[#6E49CB]" />
                                            </div>
                                            <div class="flex flex-col">
                                                <span
                                                    class="font-bold text-gray-700 text-sm italic uppercase tracking-tight">{{
                                                        item.name }}</span>
                                                <span
                                                    class="text-[10px] text-gray-400 font-semibold tracking-wide">Industrial
                                                    Grade</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4 text-xs font-bold text-gray-500 font-mono">{{ item.sku }}</td>
                                    <td class="p-4 text-[10px] text-gray-400 font-black uppercase hidden md:table-cell">
                                        {{ item.category }}</td>
                                    <td class="p-4">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-black text-gray-800">{{ item.stock }} units</span>
                                            <div class="w-16 h-1 bg-gray-100 rounded-full mt-1 overflow-hidden">
                                                <div class="bg-[#6E49CB] h-full"
                                                    :style="`width: ${item.stock > 1000 ? 100 : 40}%`"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4 text-[#6E49CB] font-black italic text-sm">{{ item.price }}</td>
                                    <td class="p-4 text-right px-8">
                                        <div class="flex items-center justify-end space-x-3 text-[#6E49CB]">
                                            <button class="hover:bg-purple-50 p-1.5 rounded-lg transition"
                                                title="Edit Specs">
                                                <Pencil class="h-4 w-4" />
                                            </button>
                                            <button
                                                class="px-4 py-1.5 bg-[#6E49CB] text-white rounded text-[10px] font-bold uppercase italic hover:bg-[#5D44A7] transition tracking-wider shadow-sm">
                                                Edit Catalog
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <table v-if="activeTab === 'bulk-pricing'" class="min-w-full text-left">
                            <thead>
                                <tr
                                    class="text-gray-400 text-[10px] sm:text-[11px] font-bold uppercase tracking-[0.1em] border-b border-gray-100 bg-gray-50/50 italic">
                                    <th class="p-4 font-normal text-gray-600 font-bold">Product</th>
                                    <th class="p-4 font-normal">Min. Bulk Quantity</th>
                                    <th class="p-4 font-normal">Bulk Rate</th>
                                    <th class="p-4 font-normal text-right px-8">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <tr v-for="item in catalogItems" :key="item.id + 'bulk'"
                                    class="group hover:bg-gray-50 transition">
                                    <td class="p-4 font-bold text-sm text-gray-700 italic uppercase">{{ item.name }}
                                    </td>
                                    <td class="p-4 text-xs text-gray-500 font-bold">{{ item.bulk_min }} Units</td>
                                    <td class="p-4 font-black text-emerald-600 italic text-sm">₱{{
                                        (parseFloat(item.price.replace(/[^\d.]/g, '')) * 0.9).toLocaleString() }} (10%
                                        Off)</td>
                                    <td class="p-4 text-right px-8">
                                        <button
                                            class="text-[10px] font-black uppercase text-[#6E49CB] hover:underline italic">
                                            Modify Bulk Rules
                                        </button>
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