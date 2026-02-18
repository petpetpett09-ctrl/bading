<script setup>
import { ref, onMounted } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import StatCard from '@/Components/dashboard/StatsCard.vue';
import {
    Boxes,
    Package,
    Truck,
    AlertTriangle,
    ClipboardList,
    FileText,
    CheckCircle
} from 'lucide-vue-next';

// 1. Stats Data
const statsData = ref({
    totalInventory: '42,500',
    pendingOrders: '12',
    stockAlerts: '3',
    inTransit: '8'
});

// 2. Production Orders Dummy Data
const ordersList = ref([
    { id: 'ORD-2026-001', productName: 'Premium Cotton Polo', targetQty: 5000, deadline: 'Feb 25, 2026' },
    { id: 'ORD-2026-002', productName: 'Indigo Slim Denim', targetQty: 2200, deadline: 'Mar 05, 2026' },
    { id: 'ORD-2026-003', productName: 'Canvas Tote Bag', targetQty: 10000, deadline: 'Mar 12, 2026' }
]);

const selectedOrder = ref(null);
const materialNeeds = ref([]);

// 3. BOM Explosion Logic
const selectOrder = (order) => {
    selectedOrder.value = order;

    // Dummy logic for textile requirements
    materialNeeds.value = [
        { id: 1, name: '200GSM Combed Cotton', type: 'Fabric', needed: (order.targetQty * 0.35).toFixed(2), stock: 850, unit: 'm' },
        { id: 2, name: 'Reactive Dye - Navy', type: 'Chemicals', needed: (order.targetQty * 0.02).toFixed(2), stock: 15, unit: 'L' },
        { id: 3, name: 'Reinforced Poly-Thread', type: 'Trims', needed: (order.targetQty * 1.2).toFixed(0), stock: 4000, unit: 'spools' },
        { id: 4, name: 'Branded Resin Buttons', type: 'Hardware', needed: (order.targetQty * 3), stock: 12000, unit: 'pcs' }
    ];
};

const finalizePlan = () => {
    alert(`Material Requirement Plan for ${selectedOrder.value.productName} finalized!`);
};

// Force selection on load
onMounted(() => {
    selectOrder(ordersList.value[0]);
});
</script>

<template>
    <AuthenticatedLayout>
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">SCM Management Dashboard</h2>
            <p class="text-sm text-gray-500">Step 1: Planning & MRP Explosion</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <StatCard title="Total Inventory" :value="statsData.totalInventory" :icon="Boxes" />
            <StatCard title="Pending Orders" :value="statsData.pendingOrders" :icon="Package" />
            <StatCard title="Stock Alerts" :value="statsData.stockAlerts" :icon="AlertTriangle"
                colorClass="text-rose-600 bg-rose-50 dark:bg-rose-900/20" />
            <StatCard title="In-Transit" :value="statsData.inTransit" :icon="Truck" />
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            <div
                class="lg:col-span-4 bg-white dark:bg-gray-800 p-5 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <h3 class="font-bold text-gray-400 text-xs uppercase tracking-widest mb-4 flex items-center">
                    <ClipboardList class="w-4 h-4 mr-2" /> Production Queue
                </h3>

                <div class="space-y-3">
                    <div v-for="order in ordersList" :key="order.id" @click="selectOrder(order)" :class="[
                        'p-4 border rounded-xl cursor-pointer transition relative overflow-hidden',
                        selectedOrder?.id === order.id ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-100 dark:border-gray-700 hover:bg-gray-50'
                    ]">
                        <div v-if="selectedOrder?.id === order.id" class="absolute left-0 top-0 h-full w-1 bg-blue-500">
                        </div>
                        <p class="text-[10px] font-bold text-blue-600 uppercase">{{ order.id }}</p>
                        <h4 class="font-bold text-gray-800 dark:text-white">{{ order.productName }}</h4>
                        <div class="flex justify-between mt-2 text-[11px] text-gray-500">
                            <span>Qty: {{ order.targetQty }}</span>
                            <span>Due: {{ order.deadline }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div
                class="lg:col-span-8 bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div v-if="selectedOrder">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="font-bold text-gray-800 dark:text-white flex items-center">
                            <FileText class="w-5 h-5 mr-2 text-indigo-500" />
                            Material Analysis: {{ selectedOrder.id }}
                        </h3>
                        <button @click="finalizePlan"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-xs font-bold transition shadow-sm flex items-center">
                            <CheckCircle class="w-4 h-4 mr-2" /> Finalize Plan
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead
                                class="text-[10px] text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-700">
                                <tr>
                                    <th class="pb-3">Raw Material</th>
                                    <th class="pb-3 text-right">Required</th>
                                    <th class="pb-3 text-right">Stock</th>
                                    <th class="pb-3 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                                <tr v-for="item in materialNeeds" :key="item.id" class="group">
                                    <td class="py-4">
                                        <span class="block font-bold text-gray-800 dark:text-gray-200">{{ item.name
                                            }}</span>
                                        <span class="text-[10px] text-gray-400 uppercase tracking-tighter">{{ item.type
                                            }}</span>
                                    </td>
                                    <td class="py-4 text-right font-mono font-bold">{{ item.needed }} {{ item.unit }}
                                    </td>
                                    <td class="py-4 text-right text-gray-400 font-mono">{{ item.stock }} {{ item.unit }}
                                    </td>
                                    <td class="py-4 text-center">
                                        <span
                                            :class="Number(item.needed) > item.stock ? 'text-rose-600 bg-rose-100' : 'text-emerald-600 bg-emerald-100'"
                                            class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-tighter">
                                            {{ Number(item.needed) > item.stock ? 'Shortage' : 'Available' }}
                                        </span>
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