<script setup>
import { ref, onMounted } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import StatCard from '@/Components/dashboard/StatsCard.vue';
import {
    Boxes,
    Package,
    AlertTriangle,
    ClipboardList,
    Users,
    ShieldCheck,
    Search,
    RefreshCcw,
    FileBarChart,
    FileText,
    CheckCircle
} from 'lucide-vue-next';

const props = defineProps({
    stats: {
        type: Object,
        default: () => ({ totalInventory: 0, pendingOrders: 0, stockAlerts: 0 })
    },
    inventoryItems: {
        type: Array,
        default: () => []
    },
    pendingOrders: {
        type: Array,
        default: () => [
            { id: 'ORD-2026-001', productName: 'Premium Cotton Tee', targetQty: 2500, deadline: 'Feb 28' },
            { id: 'ORD-2026-002', productName: 'Denim Slim Fit', targetQty: 1200, deadline: 'Mar 05' }
        ]
    }
});

// --- State Management ---
const viewMode = ref('planning'); // 'planning' or 'audit'
const selectedOrder = ref(null);
const materialNeeds = ref([]);

// --- Audit State ---
const auditItems = ref([
    { id: 'INV-001', name: 'Cotton Jersey (Blue)', systemQty: 1200, physicalQty: 1195, unit: 'm', category: 'Fabric' },
    { id: 'INV-002', name: 'Polyester Blend Yarn', systemQty: 500, physicalQty: 500, unit: 'kg', category: 'Yarn' },
    { id: 'INV-003', name: 'Indigo Dye', systemQty: 100, physicalQty: 85, unit: 'L', category: 'Chemicals' },
]);

// --- Logic: Planning (MRP) ---
const selectOrder = (order) => {
    selectedOrder.value = order;
    // Simulate Bill of Materials (BOM) Calculation
    materialNeeds.value = [
        { id: 1, name: 'Raw Cotton Fiber', needed: (order.targetQty * 0.4).toFixed(1), stock: 450, unit: 'kg' },
        { id: 2, name: 'Eco-Friendly Dye', needed: (order.targetQty * 0.05).toFixed(1), stock: 120, unit: 'L' },
        { id: 3, name: 'Woven Labels', needed: order.targetQty, stock: 5000, unit: 'pcs' },
    ];
};

const finalizePlan = () => {
    alert(`Material Requirement Plan for ${selectedOrder.value.id} finalized. Forwarded to Procurement staff.`);
};

// --- Logic: Audit ---
const calculateVariance = (item) => {
    return item.physicalQty - item.systemQty;
};

const getVarianceClass = (variance) => {
    if (variance === 0) return 'text-emerald-500';
    return 'text-rose-500 font-bold';
};

const submitAuditReport = () => {
    const totalDiscrepancies = auditItems.value.filter(i => i.physicalQty !== i.systemQty).length;
    alert(`Inventory Variance Report generated. ${totalDiscrepancies} discrepancies found. Sent to Finance Close.`);
};

// Auto-select first order on load
onMounted(() => {
    if (props.pendingOrders.length > 0) selectOrder(props.pendingOrders[0]);
});
</script>

<template>
    <AuthenticatedLayout>
        <div class="mb-6 flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">SCM Management Dashboard</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 font-medium uppercase tracking-wider">
                    {{ viewMode === 'planning' ? 'Phase 1: Planning & MRP' : 'Phase 8: Inventory Audit & Variance' }}
                </p>
            </div>
            <div class="flex bg-gray-100 dark:bg-gray-700 p-1 rounded-xl shadow-inner">
                <button @click="viewMode = 'planning'"
                    :class="viewMode === 'planning' ? 'bg-white dark:bg-gray-600 shadow-sm text-blue-600' : 'text-gray-500'"
                    class="px-6 py-1.5 rounded-lg text-sm font-bold transition-all">Planning</button>
                <button @click="viewMode = 'audit'"
                    :class="viewMode === 'audit' ? 'bg-white dark:bg-gray-600 shadow-sm text-blue-600' : 'text-gray-500'"
                    class="px-6 py-1.5 rounded-lg text-sm font-bold transition-all">Inventory Audit</button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
            <template v-if="viewMode === 'planning'">
                <StatCard title="Target Production" :value="stats.pendingOrders || '0'" :icon="Package" />
                <StatCard title="Material Shortages" value="2" :icon="AlertTriangle"
                    colorClass="text-amber-600 bg-amber-50" />
                <StatCard title="Active Vendors" value="12" :icon="Users" />
            </template>
            <template v-else>
                <StatCard title="Items Audited" value="84%" :icon="Search" />
                <StatCard title="Variance Rate" value="1.2%" :icon="RefreshCcw"
                    colorClass="text-amber-600 bg-amber-50" />
                <StatCard title="Financial Impact" value="-$450.00" :icon="FileBarChart"
                    colorClass="text-rose-600 bg-rose-50" />
            </template>
        </div>

        <div v-if="viewMode === 'planning'"
            class="grid grid-cols-1 lg:grid-cols-12 gap-6 animate-in fade-in duration-500">
            <div
                class="lg:col-span-4 bg-white dark:bg-gray-800 p-5 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700">
                <h3
                    class="font-bold text-gray-700 dark:text-gray-200 flex items-center mb-4 text-sm uppercase tracking-tight">
                    <ClipboardList class="w-4 h-4 mr-2 text-blue-500" />
                    Production Queue
                </h3>
                <div class="space-y-3">
                    <div v-for="order in pendingOrders" :key="order.id" @click="selectOrder(order)"
                        :class="selectedOrder?.id === order.id ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-100 dark:border-gray-700'"
                        class="p-4 border rounded-xl cursor-pointer hover:bg-gray-50 transition">
                        <div class="flex justify-between items-start">
                            <span class="font-bold text-gray-800 dark:text-white">{{ order.productName }}</span>
                            <span class="text-xs font-mono text-blue-600">{{ order.id }}</span>
                        </div>
                        <div class="mt-2 flex justify-between text-xs text-gray-500">
                            <span>Qty: {{ order.targetQty }}</span>
                            <span>Due: {{ order.deadline }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div
                class="lg:col-span-8 bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div v-if="selectedOrder">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="font-bold text-gray-800 dark:text-white flex items-center">
                            <FileText class="w-5 h-5 mr-2 text-indigo-500" />
                            MRP Explosion: {{ selectedOrder.productName }}
                        </h3>
                        <button @click="finalizePlan"
                            class="flex items-center text-xs bg-indigo-600 text-white px-4 py-2 rounded-lg font-bold hover:bg-indigo-700 transition">
                            <CheckCircle class="w-3.5 h-3.5 mr-2" />
                            Finalize Planning
                        </button>
                    </div>
                    <table class="w-full text-left text-sm">
                        <thead
                            class="text-gray-400 uppercase text-[10px] font-black tracking-widest border-b border-gray-100 dark:border-gray-700">
                            <tr>
                                <th class="pb-3">Required Material</th>
                                <th class="pb-3 text-right">Needed</th>
                                <th class="pb-3 text-right">In Stock</th>
                                <th class="pb-3 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                            <tr v-for="item in materialNeeds" :key="item.id" class="group">
                                <td class="py-4 font-semibold text-gray-700 dark:text-gray-200">{{ item.name }}</td>
                                <td class="py-4 text-right font-mono">{{ item.needed }} {{ item.unit }}</td>
                                <td class="py-4 text-right text-gray-500">{{ item.stock }} {{ item.unit }}</td>
                                <td class="py-4 text-center">
                                    <span
                                        :class="Number(item.needed) > item.stock ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-700'"
                                        class="px-2 py-1 rounded text-[10px] font-bold uppercase">
                                        {{ Number(item.needed) > item.stock ? 'Shortage' : 'Covered' }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div v-if="viewMode === 'audit'"
            class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden animate-in slide-in-from-bottom-4 duration-500">
            <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                <div>
                    <h3 class="font-bold text-gray-800 dark:text-white flex items-center">
                        <ShieldCheck class="w-5 h-5 mr-2 text-emerald-500" />
                        Physical Stock Spot Check
                    </h3>
                    <p class="text-xs text-gray-500 mt-1">Comparing Warehouse Records against System Ledger</p>
                </div>
                <button @click="submitAuditReport"
                    class="bg-gray-900 dark:bg-blue-600 hover:bg-black text-white px-5 py-2 rounded-xl text-sm font-bold transition">
                    Generate Variance Report
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead
                        class="bg-gray-50 dark:bg-gray-900/50 text-[10px] uppercase tracking-widest text-gray-400 font-black">
                        <tr>
                            <th class="px-6 py-4">Item ID</th>
                            <th class="px-6 py-4">Material Name</th>
                            <th class="px-6 py-4 text-right">System Record</th>
                            <th class="px-6 py-4 text-right">Physical Count</th>
                            <th class="px-6 py-4 text-right">Variance</th>
                            <th class="px-6 py-4 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        <tr v-for="item in auditItems" :key="item.id"
                            class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition">
                            <td class="px-6 py-4 font-mono text-xs text-blue-600">{{ item.id }}</td>
                            <td class="px-6 py-4">
                                <span class="block font-bold text-gray-800 dark:text-white">{{ item.name }}</span>
                                <span class="text-[10px] text-gray-400">{{ item.category }}</span>
                            </td>
                            <td class="px-6 py-4 text-right font-medium text-gray-600">{{ item.systemQty }} {{ item.unit
                                }}</td>
                            <td class="px-6 py-4 text-right">
                                <input type="number" v-model="item.physicalQty"
                                    class="w-24 text-right border-gray-200 dark:border-gray-600 dark:bg-gray-700 rounded-lg text-sm focus:ring-blue-500">
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span :class="getVarianceClass(calculateVariance(item))">
                                    {{ calculateVariance(item) > 0 ? '+' : '' }}{{ calculateVariance(item) }} {{
                                    item.unit }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span v-if="calculateVariance(item) === 0"
                                    class="bg-emerald-100 text-emerald-700 text-[10px] font-bold px-2 py-1 rounded-md uppercase">Matched</span>
                                <span v-else
                                    class="bg-rose-100 text-rose-700 text-[10px] font-bold px-2 py-1 rounded-md uppercase">Discrepancy</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AuthenticatedLayout>
</template>