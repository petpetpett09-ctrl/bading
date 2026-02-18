<script setup>
import { ref, onMounted } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import StatCard from '@/Components/dashboard/StatsCard.vue';
import {
    Boxes, Package, Truck, AlertTriangle, ClipboardList,
    FileText, CheckCircle, Users, ShieldCheck
} from 'lucide-vue-next';

const props = defineProps({
    stats: {
        type: Object,
        default: () => ({ totalInventory: '45,280', pendingOrders: 12, stockAlerts: 3 })
    },
    pendingOrders: {
        type: Array,
        default: () => [
            { id: 'ORD-101', productName: 'Summer Cotton Polo', targetQty: 5000, deadline: '2026-03-15' },
            { id: 'ORD-102', productName: 'Indigo Slim Denim', targetQty: 2500, deadline: '2026-03-22' }
        ]
    }
});

// --- State Management ---
const viewMode = ref('mrp'); // 'mrp' or 'sourcing'
const selectedOrder = ref(null);
const materialNeeds = ref([]);
const selectedMaterialForSourcing = ref(null);

// Dummy Vendor Data
const vendorQuotes = ref([
    { id: 1, name: 'Heritage Textile Mills', price: 8.45, leadTime: '10 days', rating: 4.9, status: 'Preferred' },
    { id: 2, name: 'Eastern Yarn Co.', price: 7.90, leadTime: '18 days', rating: 4.5, status: 'Verified' },
    { id: 3, name: 'Skyline Fabric Ltd', price: 9.10, leadTime: '5 days', rating: 4.7, status: 'Approved' }
]);

// --- Logic ---
const selectOrder = (order) => {
    selectedOrder.value = order;
    // Explode Materials
    materialNeeds.value = [
        { id: 1, name: '200GSM Combed Cotton', type: 'Fabric', needed: (order.targetQty * 0.35).toFixed(2), stock: 850, unit: 'm' },
        { id: 2, name: 'Reactive Dye (Navy)', type: 'Chemicals', needed: (order.targetQty * 0.02).toFixed(2), stock: 15, unit: 'L' },
        { id: 3, name: 'Branded Resin Buttons', type: 'Trim', needed: (order.targetQty * 3), stock: 12000, unit: 'pcs' }
    ];
    // Auto-select the first material for the sourcing view
    selectedMaterialForSourcing.value = materialNeeds.value[0];
};

const openSourcing = (material) => {
    selectedMaterialForSourcing.value = material;
    viewMode.value = 'sourcing';
};

onMounted(() => {
    if (props.pendingOrders.length > 0) {
        selectOrder(props.pendingOrders[0]);
    }
});
</script>

<template>
    <AuthenticatedLayout>
        <div class="mb-6 flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">SCM Management Dashboard</h2>
                <p class="text-sm text-gray-500">Current Phase: {{ viewMode.toUpperCase() }}</p>
            </div>
            <div class="flex bg-gray-100 dark:bg-gray-700 p-1 rounded-lg">
                <button @click="viewMode = 'mrp'"
                    :class="viewMode === 'mrp' ? 'bg-white shadow text-blue-600' : 'text-gray-500'"
                    class="px-4 py-1.5 rounded-md text-sm font-bold transition-all">MRP Planning</button>
                <button @click="viewMode = 'sourcing'"
                    :class="viewMode === 'sourcing' ? 'bg-white shadow text-blue-600' : 'text-gray-500'"
                    class="px-4 py-1.5 rounded-md text-sm font-bold transition-all">Sourcing Terminal</button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <StatCard title="Total Inventory" :value="stats.totalInventory" :icon="Boxes" />
            <StatCard title="Pending Orders" :value="stats.pendingOrders" :icon="Package" />
            <StatCard title="Active Vendors" value="24" :icon="Users" />
            <StatCard title="Stock Alerts" :value="stats.stockAlerts" :icon="AlertTriangle"
                colorClass="text-rose-600 bg-rose-50" />
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <div class="lg:col-span-4 space-y-4">
                <div
                    class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                    <h3 class="text-xs font-bold text-gray-400 uppercase mb-4 flex items-center">
                        <ClipboardList class="w-4 h-4 mr-2" /> Production Queue
                    </h3>
                    <div class="space-y-2">
                        <div v-for="order in pendingOrders" :key="order.id" @click="selectOrder(order)"
                            :class="selectedOrder?.id === order.id ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-transparent hover:bg-gray-50'"
                            class="p-3 border rounded-lg cursor-pointer transition">
                            <p class="text-sm font-bold text-gray-800 dark:text-white">{{ order.productName }}</p>
                            <p class="text-[10px] text-gray-500">{{ order.id }} | Qty: {{ order.targetQty }}</p>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                    <h3 class="text-xs font-bold text-gray-400 uppercase mb-4">Material Requirements</h3>
                    <div v-if="materialNeeds.length" class="space-y-2">
                        <div v-for="item in materialNeeds" :key="item.id" @click="openSourcing(item)"
                            :class="selectedMaterialForSourcing?.id === item.id ? 'border-blue-500 bg-blue-50' : 'border-gray-100'"
                            class="p-3 border rounded-lg cursor-pointer transition flex justify-between items-center">
                            <div>
                                <p class="text-sm font-medium">{{ item.name }}</p>
                                <p class="text-[10px] text-gray-400 uppercase">{{ item.type }}</p>
                            </div>
                            <span class="text-xs font-bold text-blue-600">{{ item.needed }} {{ item.unit }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div
                class="lg:col-span-8 bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">

                <div v-if="viewMode === 'mrp' && selectedOrder">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-bold">MRP Analysis: {{ selectedOrder.id }}</h3>
                        <button class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-bold">Finalize
                            Plan</button>
                    </div>
                    <table class="w-full text-left">
                        <thead class="text-xs text-gray-400 border-b">
                            <tr>
                                <th class="py-3">Material</th>
                                <th class="py-3 text-right">Required</th>
                                <th class="py-3 text-right">In Stock</th>
                                <th class="py-3 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <tr v-for="item in materialNeeds" :key="item.id">
                                <td class="py-4 font-medium">{{ item.name }}</td>
                                <td class="py-4 text-right">{{ item.needed }}</td>
                                <td class="py-4 text-right text-gray-400">{{ item.stock }}</td>
                                <td class="py-4 text-center">
                                    <span
                                        :class="Number(item.needed) > item.stock ? 'text-red-600 bg-red-50' : 'text-green-600 bg-green-50'"
                                        class="px-2 py-1 rounded text-[10px] font-bold">
                                        {{ Number(item.needed) > item.stock ? 'SHORTAGE' : 'OK' }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-else-if="viewMode === 'sourcing' && selectedMaterialForSourcing">
                    <h3 class="text-xl font-bold mb-2">Sourcing: {{ selectedMaterialForSourcing.name }}</h3>
                    <p class="text-sm text-gray-500 mb-8">Compare approved vendors from the AVL.</p>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div v-for="vendor in vendorQuotes" :key="vendor.id" class="p-4 border rounded-xl bg-gray-50">
                            <p class="text-[10px] font-bold text-emerald-600 uppercase">{{ vendor.status }}</p>
                            <h4 class="font-bold text-gray-800">{{ vendor.name }}</h4>
                            <p class="text-2xl font-black my-2">${{ vendor.price }}</p>
                            <p class="text-xs text-gray-500 mb-4">Lead: {{ vendor.leadTime }}</p>
                            <button
                                class="w-full py-2 bg-gray-800 text-white rounded-lg text-xs font-bold uppercase">Assign</button>
                        </div>
                    </div>
                </div>

                <div v-else class="h-full flex flex-col items-center justify-center text-gray-400 py-20">
                    <Truck class="w-12 h-12 mb-4 opacity-20" />
                    <p>Select an item to display details.</p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>