<script setup lang="ts">
import { ref } from 'vue'; // Added for toggle state
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps<{
    canResetPassword?: boolean;
    status?: string;
}>();

// Toggle state: 'email' or 'employee'
const loginType = ref<'email' | 'employee'>('email');

const form = useForm({
    email: '',
    employee_id: '', // Added for employee login
    password: '',
    remember: false,
});

const submit = () => {
    // Determine the route based on the active toggle
    const routeName = loginType.value === 'email' ? 'login' : 'employee.login.store';

    form.post(route(routeName), {
        onFinish: () => {
            form.reset('password');
        },
    });
};

const setLoginType = (type: 'email' | 'employee') => {
    loginType.value = type;
    form.clearErrors();
};
</script>

<template>
    <GuestLayout>

        <Head title="Monti Textile - Secure Login" />

        <div class="mb-10 text-center">
            <div class="flex items-center justify-center p-3">
                <Link href="/">
                    <div class="h-20 w-20 flex-shrink-0 transition hover:opacity-80">
                        <img src="/images/applogo.png" alt="Monti Textile Logo" class="h-full w-full object-contain" />
                    </div>
                </Link>
            </div>
            <h1 class="text-3xl font-black tracking-tight text-slate-900 dark:text-white uppercase">
                Monti<span class="font-light text-blue-600">Textile</span>
            </h1>
            <p class="mt-2 text-sm font-medium text-slate-500 dark:text-slate-400">
                Secure Enterprise Management Access
            </p>
        </div>

        <div
            class="flex p-1 mb-8 bg-slate-100 dark:bg-slate-800/50 rounded-xl border border-slate-200 dark:border-slate-700">
            <button type="button" @click="setLoginType('email')" :class="[
                'flex-1 py-2 text-xs font-bold uppercase tracking-wider rounded-lg transition-all',
                loginType === 'email'
                    ? 'bg-white dark:bg-slate-700 text-blue-600 shadow-sm'
                    : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'
            ]">
                Corporate Account
            </button>
            <button type="button" @click="setLoginType('employee')" :class="[
                'flex-1 py-2 text-xs font-bold uppercase tracking-wider rounded-lg transition-all',
                loginType === 'employee'
                    ? 'bg-white dark:bg-slate-700 text-blue-600 shadow-sm'
                    : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'
            ]">
                Employee Account
            </button>
        </div>

        <transition enter-active-class="transition ease-out duration-200" enter-from-class="opacity-0 -translate-y-1"
            enter-to-class="opacity-100 translate-y-0">
            <div v-if="status"
                class="mb-6 rounded-xl bg-emerald-50 border border-emerald-100 p-4 text-sm text-emerald-700 dark:bg-emerald-900/20 dark:border-emerald-800 dark:text-emerald-400">
                <div class="flex items-center">
                    <svg class="size-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd" />
                    </svg>
                    {{ status }}
                </div>
            </div>
        </transition>

        <form @submit.prevent="submit" class="space-y-5">
            <div v-if="loginType === 'email'" class="relative group">
                <InputLabel for="email" value="Corporate Email"
                    class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5 ml-1" />
                <div class="relative">
                    <div
                        class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 group-focus-within:text-blue-600 transition-colors">
                        <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206" />
                        </svg>
                    </div>
                    <TextInput id="email" type="email"
                        class="block w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-800/50 border-slate-200 dark:border-slate-700 rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-600 transition-all"
                        v-model="form.email" required autofocus placeholder="name@monticorp.com" />
                </div>
                <InputError class="mt-2 text-xs" :message="form.errors.email" />
            </div>

            <div v-else class="relative group">
                <InputLabel for="employee_id" value="Employee ID"
                    class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5 ml-1" />
                <div class="relative">
                    <div
                        class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 group-focus-within:text-blue-600 transition-colors">
                        <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                    </div>
                    <TextInput id="employee_id" type="text"
                        class="block w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-800/50 border-slate-200 dark:border-slate-700 rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-600 transition-all"
                        v-model="form.employee_id" required autofocus placeholder="EMP-XXXX" />
                </div>
                <InputError class="mt-2 text-xs" :message="form.errors.employee_id" />
            </div>

            <div class="relative group">
                <div class="flex items-center justify-between mb-1.5 ml-1">
                    <InputLabel for="password" value="Password"
                        class="text-xs font-bold uppercase tracking-wider text-slate-500" />
                    <Link v-if="canResetPassword" :href="route('password.request')"
                        class="text-xs font-semibold text-blue-600 hover:text-blue-700 dark:text-blue-400">
                        Forgot?
                    </Link>
                </div>
                <div class="relative">
                    <div
                        class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 group-focus-within:text-blue-600 transition-colors">
                        <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <TextInput id="password" type="password"
                        class="block w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-800/50 border-slate-200 dark:border-slate-700 rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-600 transition-all"
                        v-model="form.password" required placeholder="••••••••" />
                </div>
                <InputError class="mt-2 text-xs" :message="form.errors.password" />
            </div>

            <div class="flex items-center px-1">
                <Checkbox name="remember" v-model:checked="form.remember"
                    class="rounded border-slate-300 text-blue-600 shadow-sm focus:ring-blue-500 dark:bg-slate-800 dark:border-slate-700" />
                <span class="ms-2 text-sm text-slate-600 dark:text-slate-400">Keep me signed in for 30 days</span>
            </div>

            <PrimaryButton
                class="w-full py-4 justify-center bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-sm font-bold uppercase tracking-widest rounded-xl transition-all shadow-lg shadow-blue-600/25"
                :class="{ 'opacity-50 cursor-not-allowed': form.processing }" :disabled="form.processing">
                <div v-if="form.processing" class="flex items-center">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                        </circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    Processing...
                </div>
                <span v-else>Authorize Access</span>
            </PrimaryButton>

            <div class="pt-4 text-center border-t border-slate-100 dark:border-slate-800">
                <p class="text-sm text-slate-500 dark:text-slate-400">
                    New partner?
                    <Link :href="route('register')"
                        class="font-bold text-blue-600 hover:text-blue-700 dark:text-blue-400">
                        Request Account
                    </Link>
                </p>
            </div>
        </form>
    </GuestLayout>
</template>

<style scoped>
/* Optional: Adding a soft inner glow to inputs on focus */
input:focus {
    box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
}
</style>