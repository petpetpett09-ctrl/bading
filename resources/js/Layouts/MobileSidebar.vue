<script setup>
import { ref, computed } from 'vue'
import { usePage, Link } from '@inertiajs/vue3'
import {
    Menu,
    X,
    LogOut,
    ShieldCheck,
    LayoutDashboard,
    BarChart3,
    Package,
    UserPlus,
    ClipboardList,
    ChartNoAxesCombined,
    HandCoins,
    FileUser,
    DoorOpen,
    CreditCard,
    BicepsFlexed,
    Truck,
    Wallet,
    Factory,
    Boxes,
    ShoppingCart,
    Warehouse,
    Users,
    Globe,
    // --- Added Missing Icons for Employee Portal ---
    Clock,
    CalendarDays,
    History,
    Settings
} from 'lucide-vue-next'

const isOpen = ref(false)
const page = usePage()
const user = computed(() => page.props.auth.user)
const currentUrl = computed(() => page.url)

// Determines if the user is currently within the Employee ID Portal routes
const isEmployeePortal = computed(() => currentUrl.value.startsWith('/dashboard/employee-ui'))

const navItems = computed(() => {
    // --- Employee ID Specific Navigation ---
    if (isEmployeePortal.value) {
        return [
            { label: 'Employee Dashboard', href: route('employee.ui.dashboard'), icon: Clock },
            { label: 'My Attendance', href: route('employee.ui.clock'), icon: CalendarDays },
            { label: 'Leave Request', href: route('employee.ui.leave'), icon: History },
            { label: 'Payslip', href: route('employee.ui.payslip'), icon: HandCoins },
        ]
    }

    const items = [
        { label: 'Main Dashboard', href: route('dashboard'), icon: LayoutDashboard },
    ]

    const userRole = user.value?.role?.toUpperCase();
    const userPosition = user.value?.position?.toLowerCase();

    // --- HRM Department Logic ---
    if (userRole === 'HRM') {
        if (userPosition === 'manager') {
            items.push(
                { label: 'Onboarding', href: route('hrm.manager.onboarding'), icon: BarChart3 },
                { label: 'Payroll', href: route('hrm.manager.payroll'), icon: HandCoins },
                { label: 'Analytics', href: route('hrm.manager.analytics'), icon: ChartNoAxesCombined }
            );
        } else if (userPosition === 'staff') {
            items.push(
                { label: 'Recruitment', href: route('hrm.applicants.index'), icon: UserPlus },
                { label: 'Interview', href: route('hrm.employee.interview'), icon: ClipboardList },
                { label: 'Training & Development', href: route('hrm.employee.training'), icon: BicepsFlexed },
                { label: 'Attendance', href: route('hrm.employee.attendance'), icon: FileUser },
                { label: 'Leave Management', href: route('hrm.employee.leave'), icon: DoorOpen },
                { label: 'Payroll', href: route('hrm.employee.hrmstaffpayroll'), icon: HandCoins },
            );
        }
    }

    // --- SCM Department Logic ---
    if (userRole === 'SCM') {
        if (userPosition === 'manager') {
            items.push(
                { label: 'Sourcing', href: route('scm.manager.sourcing'), icon: Truck },
                { label: 'Audit', href: route('scm.manager.audit'), icon: ChartNoAxesCombined },
                { label: 'Close', href: route('scm.manager.close'), icon: DoorOpen }
            );
        } else if (userPosition === 'staff') {
            items.push(
                { label: 'Inbound', href: route('scm.employee.inbound'), icon: Truck },
                { label: 'Recieving', href: route('scm.employee.recieving'), icon: Truck },
                { label: 'Inventory Management', href: route('scm.employee.inventory'), icon: Package },
                { label: 'Verifications', href: route('scm.employee.verification'), icon: HandCoins }
            );
        }
    }

    // --- Finance (FIN) ---
    if (userRole === 'FIN') {
        if (userPosition === 'manager') {
            items.push({ label: 'Finance Dashboard', href: route('fin.manager.dashboard'), icon: Wallet });
        } else if (userPosition === 'staff') {
            items.push({ label: 'Finance Portal', href: route('fin.employee.dashboard'), icon: Wallet });
        }
    }

    // --- Manufacturing (MAN) ---
    if (userRole === 'MAN') {
        if (userPosition === 'manager') {
            items.push({ label: 'Manufacturing', href: route('man.manager.dashboard'), icon: Factory });
        } else if (userPosition === 'staff') {
            items.push({ label: 'Production Line', href: route('man.employee.dashboard'), icon: Factory });
        }
    }

    // --- Inventory (INV) ---
    if (userRole === 'INV') {
        if (userPosition === 'manager') {
            items.push({ label: 'Inventory Control', href: route('inv.manager.dashboard'), icon: Boxes });
        } else if (userPosition === 'staff') {
            items.push({ label: 'Stock Control', href: route('inv.employee.dashboard'), icon: Boxes });
        }
    }

    // --- Order Management (ORD) ---
    if (userRole === 'ORD') {
        if (userPosition === 'manager') {
            items.push({ label: 'Order Management', href: route('ord.manager.dashboard'), icon: ShoppingCart });
        } else if (userPosition === 'staff') {
            items.push({ label: 'Order Processing', href: route('ord.employee.dashboard'), icon: ShoppingCart });
        }
    }

    // --- Warehouse (WAR) ---
    if (userRole === 'WAR') {
        if (userPosition === 'manager') {
            items.push({ label: 'Warehouse', href: route('war.manager.dashboard'), icon: Warehouse });
        } else if (userPosition === 'staff') {
            items.push({ label: 'Warehouse Floor', href: route('war.employee.dashboard'), icon: Warehouse });
        }
    }

    // --- CRM ---
    if (userRole === 'CRM') {
        if (userPosition === 'manager') {
            items.push({ label: 'Customer Relations', href: route('crm.manager.dashboard'), icon: Users });
        } else if (userPosition === 'staff') {
            items.push({ label: 'Customer Support', href: route('crm.employee.dashboard'), icon: Users });
        }
    }

    // --- E-Commerce (ECO) ---
    if (userRole === 'ECO') {
        if (userPosition === 'manager') {
            items.push({ label: 'Book Management', href: route('eco.manager.book'), icon: Globe });
            items.push({ label: 'Credit Management', href: route('eco.manager.credit'), icon: CreditCard });

        } else if (userPosition === 'staff') {
            items.push({ label: 'Online Store', href: route('eco.employee.dashboard'), icon: Globe });
        }
    }

    return items
})

const isActive = (href) => {
    return currentUrl.value === href || currentUrl.value.startsWith(href + '/')
}

const closeSidebar = () => {
    isOpen.value = false
}
</script>

<template>
    <div class="md:hidden">
        <button @click="isOpen = true"
            class="p-2 rounded-xl text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none">
            <Menu class="h-6 w-6" />
        </button>

        <Teleport to="body">
            <div v-if="isOpen" class="fixed inset-0 z-[60] flex md:hidden">
                <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="closeSidebar"></div>

                <div class="relative flex-1 flex flex-col max-w-xs w-full bg-white dark:bg-gray-900 shadow-2xl">
                    <div class="absolute top-0 right-0 -mr-12 pt-4">
                        <button @click="closeSidebar"
                            class="h-10 w-10 rounded-full bg-white/10 text-white flex items-center justify-center">
                            <X class="h-6 w-6" />
                        </button>
                    </div>

                    <div class="flex-shrink-0 px-6 py-6 border-b border-gray-100 dark:border-gray-800">
                        <div class="flex items-center mb-6">
                            <div class="h-10 w-10 flex-shrink-0 mr-3">
                                <img src="/images/applogo.png" alt="Monti Textile Logo"
                                    class="h-full w-full object-contain" />
                            </div>
                            <div class="flex flex-col">
                                <h2 class="text-xl font-bold text-gray-900 dark:text-white leading-tight">
                                    Monti <span class="text-blue-600">Textile</span>
                                </h2>
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                                    {{ isEmployeePortal ? 'Employee Portal' : 'ERP System' }}
                                </span>
                            </div>
                        </div>

                        <div
                            class="flex items-center p-3 rounded-2xl bg-gray-50 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-700">
                            <div
                                class="h-10 w-10 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold shadow-sm">
                                {{ user?.name?.charAt(0) }}
                            </div>
                            <div class="ml-3 overflow-hidden">
                                <p class="text-sm font-bold text-gray-900 dark:text-white truncate uppercase">{{
                                    user?.name }}</p>
                                <div
                                    class="flex items-center text-[10px] font-semibold text-blue-600 dark:text-blue-400 uppercase tracking-wider">
                                    <ShieldCheck class="h-3 w-3 mr-1" />
                                    {{ isEmployeePortal ? (user?.employee_id || 'STAFF') : (user?.role + ' • ' +
                                        user?.position) }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex-1 h-0 pt-4 pb-4 overflow-y-auto">
                        <nav class="px-3 space-y-1.5">
                            <Link v-for="item in navItems" :key="item.label" :href="item.href" @click="closeSidebar"
                                :class="[
                                    isActive(item.href)
                                        ? 'bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400 ring-1 ring-blue-100 dark:ring-blue-900/30'
                                        : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800'
                                ]"
                                class="group flex items-center px-3 py-3 text-base font-bold rounded-xl transition-all">
                                <component :is="item.icon"
                                    :class="[isActive(item.href) ? 'text-blue-600' : 'text-gray-400']"
                                    class="mr-3 h-5 w-5" />
                                {{ item.label }}
                            </Link>
                        </nav>
                    </div>

                    <div class="p-4 border-t border-gray-100 dark:border-gray-800 bg-gray-50/30 dark:bg-gray-900/30">
                        <Link :href="route('logout')" method="post" as="button"
                            class="flex w-full items-center justify-center px-4 py-3 rounded-xl bg-red-50 dark:bg-red-900/10 text-red-600 font-bold text-sm transition-colors hover:bg-red-100 dark:hover:bg-red-900/20">
                            <LogOut class="mr-2 h-5 w-5" />
                            Sign Out
                        </Link>
                    </div>
                </div>
            </div>
        </Teleport>
    </div>
</template>