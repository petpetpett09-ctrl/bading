<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import {
    Plus,
    Search,
    Pencil,
    Trash2,
    Tag,
    Users,
    Percent,
    X,
    Check,
    Filter,
    ArrowDownToLine,
    Ticket,
    Building2,
    ExternalLink
} from 'lucide-vue-next';

const props = defineProps({
    priceBooks: {
        type: Object,
        default: () => ({ data: [], links: [], meta: {} }),
    },
    // Mock data for Customer Specific Rates (to be replaced by backend props)
    customerRates: {
        type: Array,
        default: () => [
            { id: 101, company: 'TechLogistics Corp', rate: '12%', status: 'active', type: 'Fixed' },
            { id: 102, company: 'Manila Build Supplies', rate: 'Tier: Gold', status: 'active', type: 'Tiered' },
        ]
    },
    filters: { type: Object, default: () => ({ search: '' }) },
});

// Tab State
const activeTab = ref('tiers'); // 'tiers' or 'rates'

const showCreateModal = ref(false);
const showEditModal = ref(false);
const showDeleteModal = ref(false);
const selectedPriceBook = ref(null);
const search = ref(props.filters.search);

let searchTimeout;
const updateSearch = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(route('eco.manager.book'), { search: search.value }, { preserveState: true, replace: true });
    }, 300);
};

const createForm = useForm({ name: '', discount: '', target: '', status: 'active', members: 0 });
const editForm = useForm({ name: '', discount: '', target: '', status: 'active', members: 0 });

const submitCreate = () => {
    createForm.post(route('eco.manager.book.store'), {
        onSuccess: () => { createForm.reset(); showCreateModal.value = false; },
    });
};

const openEditModal = (priceBook) => {
    selectedPriceBook.value = priceBook;
    editForm.name = priceBook.name;
    editForm.discount = priceBook.discount;
    editForm.target = priceBook.target;
    editForm.status = priceBook.status;
    editForm.members = priceBook.members;
    showEditModal.value = true;
};

const submitEdit = () => {
    editForm.put(route('eco.manager.book.update', selectedPriceBook.value.id), {
        onSuccess: () => { editForm.reset(); showEditModal.value = false; selectedPriceBook.value = null; },
    });
};

const confirmDelete = (priceBook) => {
    selectedPriceBook.value = priceBook;
    showDeleteModal.value = true;
};

const deletePriceBook = () => {
    router.delete(route('eco.manager.book.destroy', selectedPriceBook.value.id), {
        onSuccess: () => { showDeleteModal.value = false; selectedPriceBook.value = null; },
    });
};

const toggleStatus = (priceBook) => {
    router.patch(route('eco.manager.book.toggle-status', priceBook.id), {}, { preserveState: true, preserveScroll: true });
};

const stats = computed(() => [
    { label: 'Active Price Books', value: props.priceBooks.data?.filter(pb => pb.status === 'active').length || 0, icon: Tag, color: 'text-purple-600', bg: 'bg-purple-50' },
    { label: 'Total Members', value: props.priceBooks.data?.reduce((acc, pb) => acc + (pb.members || 0), 0) || 0, icon: Users, color: 'text-blue-600', bg: 'bg-blue-50' },
    { label: 'Discounted Sales', value: '₱1.1M', icon: Ticket, color: 'text-emerald-600', bg: 'bg-emerald-50' },
    { label: 'Avg. Discount', value: props.priceBooks.data?.length ? (props.priceBooks.data.reduce((acc, pb) => acc + (parseFloat(pb.discount) || 0), 0) / props.priceBooks.data.length).toFixed(1) + '%' : '0%', icon: Percent, color: 'text-amber-600', bg: 'bg-amber-50' },
]);
</script>

<template>

    <Head title="Price Book Management" />

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
                <button @click="activeTab = 'tiers'"
                    :class="[activeTab === 'tiers' ? 'bg-white border-t border-x border-gray-200 rounded-t-lg text-[#5D44A7] font-bold' : 'text-gray-400 font-medium hover:text-gray-600']"
                    class="whitespace-nowrap px-6 py-2 text-sm transition-all duration-200">
                    Pricing Tiers
                </button>
                <button @click="activeTab = 'rates'"
                    :class="[activeTab === 'rates' ? 'bg-white border-t border-x border-gray-200 rounded-t-lg text-[#5D44A7] font-bold' : 'text-gray-400 font-medium hover:text-gray-600']"
                    class="whitespace-nowrap px-6 py-2 text-sm transition-all duration-200">
                    Customer Specific Rates
                </button>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-6">

                <div class="flex flex-col lg:flex-row lg:items-center justify-between mb-8 space-y-4 lg:space-y-0">
                    <div class="flex flex-col sm:flex-row sm:items-center space-y-3 sm:space-y-0 sm:space-x-3">
                        <h1 class="text-xl sm:text-2xl font-bold text-gray-800 tracking-tight italic uppercase">
                            {{ activeTab === 'tiers' ? 'Price Book Management' : 'Individual Customer Rates' }}
                        </h1>
                        <div class="flex flex-wrap gap-2">
                            <button @click="showCreateModal = true"
                                class="flex items-center px-4 py-2 bg-[#6E49CB] text-white rounded-md text-sm font-medium hover:bg-[#5D44A7] transition shadow-md">
                                <Plus class="h-4 w-4 mr-2" /> {{ activeTab === 'tiers' ? 'Add Tier' : 'Assign Rate' }}
                            </button>
                            <button
                                class="flex items-center px-4 py-2 border border-gray-300 text-gray-600 rounded-md text-sm font-medium hover:bg-gray-50 transition">
                                <ArrowDownToLine class="h-4 w-4 mr-2" /> Export
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between lg:justify-end space-x-4 sm:space-x-6">
                        <div class="relative w-full max-w-xs">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center">
                                <Search class="h-4 w-4 text-gray-400" />
                            </span>
                            <input v-model="search" @input="updateSearch" type="text" placeholder="Search..."
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

                        <table v-if="activeTab === 'tiers'" class="min-w-full text-left">
                            <thead>
                                <tr
                                    class="text-gray-400 text-[10px] sm:text-[11px] font-bold uppercase tracking-[0.1em] border-b border-gray-100 bg-gray-50/50 italic">
                                    <th class="p-4 font-normal text-gray-600 font-bold">Tier Name</th>
                                    <th class="p-4 font-normal">Discount Rate</th>
                                    <th class="p-4 font-normal hidden md:table-cell">Eligibility Target</th>
                                    <th class="p-4 font-normal">Status</th>
                                    <th class="p-4 font-normal hidden lg:table-cell px-6 text-center">Partners</th>
                                    <th class="p-4 font-normal text-right">Operation</th>
                                    <th class="p-4 font-normal text-right px-8">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <tr v-for="book in priceBooks.data" :key="book.id"
                                    class="group hover:bg-gray-50 transition duration-150">
                                    <td class="p-4 italic uppercase font-bold text-sm text-gray-700">
                                        <div class="flex items-center">
                                            <Tag class="h-4 w-4 mr-2 text-[#6E49CB]" />
                                            {{ book.name }}
                                        </div>
                                    </td>
                                    <td class="p-4 text-[#6E49CB] font-black italic text-sm">-{{ book.discount }}</td>
                                    <td class="p-4 text-gray-500 text-[10px] sm:text-xs hidden md:table-cell uppercase">
                                        {{ book.target }}</td>
                                    <td class="p-4">
                                        <button @click="toggleStatus(book)" :class="[
                                            'px-3 py-1 rounded-full text-[9px] sm:text-[10px] font-bold uppercase tracking-widest transition',
                                            book.status === 'active' ? 'bg-green-100 text-green-600 border border-green-200' : 'bg-red-100 text-red-400 border border-red-200'
                                        ]">
                                            {{ book.status === 'active' ? 'Active' : 'Inactive' }}
                                        </button>
                                    </td>
                                    <td
                                        class="p-4 text-gray-400 text-[10px] sm:text-xs font-semibold hidden lg:table-cell text-center">
                                        {{ book.members || 0 }} Partners</td>
                                    <td class="p-4">
                                        <div class="flex items-center justify-end space-x-3 text-[#6E49CB]">
                                            <button @click="openEditModal(book)"
                                                class="hover:bg-purple-50 p-1.5 rounded-lg transition">
                                                <Pencil class="h-5 w-5" />
                                            </button>
                                            <button @click="confirmDelete(book)"
                                                class="hover:bg-purple-50 p-1.5 rounded-lg transition text-gray-300 hover:text-red-500">
                                                <Trash2 class="h-5 w-5" />
                                            </button>
                                        </div>
                                    </td>
                                    <td class="p-4 text-right px-8">
                                        <Link :href="route('eco.manager.book.show', book.id)"
                                            class="px-5 py-1.5 bg-[#6E49CB] text-white rounded text-[10px] font-bold uppercase italic hover:bg-[#5D44A7] transition tracking-wider">
                                            Manage</Link>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <table v-if="activeTab === 'rates'" class="min-w-full text-left">
                            <thead>
                                <tr
                                    class="text-gray-400 text-[10px] sm:text-[11px] font-bold uppercase tracking-[0.1em] border-b border-gray-100 bg-gray-50/50 italic">
                                    <th class="p-4 font-normal text-gray-600 font-bold">Business Partner</th>
                                    <th class="p-4 font-normal">Pricing Type</th>
                                    <th class="p-4 font-normal">Applied Rate</th>
                                    <th class="p-4 font-normal">Account Status</th>
                                    <th class="p-4 font-normal text-right px-8">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <tr v-for="rate in customerRates" :key="rate.id"
                                    class="group hover:bg-gray-50 transition">
                                    <td class="p-4 font-bold text-sm text-gray-700 italic">
                                        <div class="flex items-center uppercase">
                                            <Building2 class="h-4 w-4 mr-2 text-blue-500" />
                                            {{ rate.company }}
                                        </div>
                                    </td>
                                    <td class="p-4 text-xs text-gray-500 uppercase font-semibold">{{ rate.type }}</td>
                                    <td class="p-4 font-black text-[#6E49CB] italic text-sm">{{ rate.rate }}</td>
                                    <td class="p-4">
                                        <span
                                            class="px-3 py-1 rounded-full text-[9px] font-bold uppercase tracking-widest bg-emerald-100 text-emerald-600">
                                            {{ rate.status }}
                                        </span>
                                    </td>
                                    <td class="p-4 text-right px-8">
                                        <button
                                            class="flex items-center justify-end w-full text-blue-600 hover:text-blue-800 transition text-[10px] font-bold uppercase italic">
                                            Adjust Rate
                                            <ExternalLink class="h-3 w-3 ml-1" />
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