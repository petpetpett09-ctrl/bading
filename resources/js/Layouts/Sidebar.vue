<script setup>
import { usePage, Link } from '@inertiajs/vue3'
import { computed } from 'vue'
import {
    LayoutDashboard,
    BarChart3,
    Package,
    LogOut,
    ChevronRight,
    CreditCard,
    UserPlus,
    ClipboardList,
    ChartNoAxesCombined,
    HandCoins,
    FileUser,
    DoorOpen,
    BicepsFlexed,
    Truck,
    Wallet,
    Factory,
    Book,
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

    // --- Standard Corporate ERP Navigation ---
    const items = [
        { label: 'Main Dashboard', href: route('dashboard'), icon: LayoutDashboard },
    ]

    const userRole = user.value?.role?.toUpperCase();
    const userPosition = user.value?.position?.toLowerCase();

    // --- Trainee Navigation ---
    if (userPosition === 'trainee') {
        items.push(
            { label: 'Trainee Dashboard', href: route('trainee.dashboard'), icon: LayoutDashboard },
            { label: 'Time In/Out', href: route('trainee.timekeeping'), icon: Clock },
            { label: 'Attendance', href: route('trainee.attendance'), icon: CalendarDays },
            { label: 'Payslips', href: route('trainee.payslip'), icon: HandCoins }
        );
        return items;
    }

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

    if (userRole === 'FIN') {
        items.push(
            { label: 'Finance Dashboard', href: route('finance.dashboard'), icon: Wallet },
            { label: 'Transactions', href: route('finance.transactions.index'), icon: CreditCard },
            { label: 'Invoices & Bills', href: route('finance.invoices-bills.index'), icon: FileUser },
            { label: 'Reports', href: route('finance.reports.index'), icon: ChartNoAxesCombined }
        );
    }

    if (userRole === 'MAN') {
        if (userPosition === 'manager') {
            items.push({ label: 'Manufacturing MGMT', href: route('man.manager.dashboard'), icon: Factory });
        } else if (userPosition === 'staff') {
            items.push({ label: 'Production Line', href: route('man.employee.dashboard'), icon: Factory });
        }
    }

    if (userRole === 'INV') {
        if (userPosition === 'manager') {
            items.push({ label: 'Inventory Dashboard', href: route('inv.manager.dashboard'), icon: Boxes });
        } else if (userPosition === 'staff') {
            items.push({ label: 'Stock Control', href: route('inv.employee.dashboard'), icon: Boxes });
        }
    }

    if (userRole === 'ORD') {
        if (userPosition === 'manager') {
            items.push({ label: 'Order Analytics', href: route('ord.manager.dashboard'), icon: ShoppingCart });
        } else if (userPosition === 'staff') {
            items.push({ label: 'Order Processing', href: route('ord.employee.dashboard'), icon: ShoppingCart });
        }
    }

    if (userRole === 'WAR') {
        if (userPosition === 'manager') {
            items.push({ label: 'Warehouse MGMT', href: route('war.manager.dashboard'), icon: Warehouse });
        } else if (userPosition === 'staff') {
            items.push({ label: 'Warehouse Floor', href: route('war.employee.dashboard'), icon: Warehouse });
        }
    }

    if (userRole === 'CRM') {
        if (userPosition === 'manager') {
            items.push({ label: 'Relations Dashboard', href: route('crm.manager.dashboard'), icon: Users });
        } else if (userPosition === 'staff') {
            items.push({ label: 'Customer Support', href: route('crm.employee.dashboard'), icon: Users });
        }
    }

    if (userRole === 'ECO') {
        if (userPosition === 'manager') {
            // items.push({ label: 'Store Management', href: route('eco.manager.dashboard'), icon: Globe });
            items.push({ label: 'Book Management', href: route('eco.manager.book'), icon: Book });
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
</script>

<template>
    <aside class="hidden md:flex md:w-64 md:flex-col md:fixed md:inset-y-0 z-50">
        <div
            class="flex flex-col flex-grow bg-white dark:bg-gray-900 border-r border-gray-100 dark:border-gray-800 shadow-sm">

            <div class="flex items-center h-20 flex-shrink-0 px-5 border-b border-gray-50 dark:border-gray-800">
                <div class="h-10 w-10 flex-shrink-0 mr-3">
                    <img src="/images/applogo.png" alt="Monti Textile Logo" class="h-full w-full object-contain" />
                </div>
                <div class="flex flex-col">
                    <h2 class="text-md font-bold text-gray-900 dark:text-white leading-tight">
                        Monti <span class="text-blue-600">Textile</span>
                    </h2>
                    <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">
                        {{ isEmployeePortal ? 'Employee Portal' : 'ERP System' }}
                    </span>
                </div>
            </div>

            <div class="flex-1 flex flex-col overflow-y-auto py-6">
                <nav class="flex-1 px-3 space-y-1.5">
                    <Link v-for="item in navItems" :key="item.label" :href="item.href" :class="[
                        isActive(item.href)
                            ? 'bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400 ring-1 ring-blue-100 dark:ring-blue-900/30'
                            : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-white'
                    ]"
                        class="group flex items-center justify-between px-3 py-2.5 text-sm font-bold rounded-xl transition-all duration-200">
                        <div class="flex items-center">
                            <component :is="item.icon"
                                :class="[isActive(item.href) ? 'text-blue-600' : 'text-gray-400']"
                                class="mr-3 h-5 w-5 flex-shrink-0" />
                            <span class="truncate">{{ item.label }}</span>
                        </div>
                        <ChevronRight v-if="isActive(item.href)" class="h-4 w-4 flex-shrink-0" />
                    </Link>
                </nav>
            </div>

            <div
                class="flex-shrink-0 border-t border-gray-100 dark:border-gray-800 p-4 bg-gray-50/50 dark:bg-gray-900/50">
                <div class="flex items-center group p-1.5 rounded-2xl">
                    <div class="flex-shrink-0">
                        <div
                            class="h-9 w-9 rounded-xl bg-blue-600 flex items-center justify-center text-white font-bold shadow-md">
                            {{ user?.name?.charAt(0) }}
                        </div>
                    </div>
                    <div class="ml-3 flex-1 min-w-0">
                        <p class="text-xs font-bold text-gray-900 dark:text-white truncate uppercase tracking-tight">
                            {{ user?.name }}
                        </p>
                        <div class="flex items-center text-[9px] font-bold text-blue-600 dark:text-blue-400 uppercase">
                            {{ isEmployeePortal ? (user?.employee_id || 'STAFF') : (user?.role + ' • ' + user?.position)
                            }}
                        </div>
                    </div>
                    <Link :href="route('logout')" method="post" as="button"
                        class="ml-2 p-2 rounded-lg text-gray-400 hover:text-red-600 transition-all">
                        <LogOut class="h-4 w-4" />
                    </Link>
                </div>
            </div>
        </div>
    </aside>
</template>