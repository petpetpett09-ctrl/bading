<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import Select from '@/Components/ui/Select.vue';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: '',
    position: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>

        <Head title="Monti Textile - Register" />

        <div class="mb-8 text-center">
            <h1 class="text-3xl font-bold text-[#1E40AF] dark:text-blue-400">Monti Textile</h1>
            <p class="mt-2 text-gray-600 dark:text-gray-300">Create New Employee Account</p>
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <div>
                <InputLabel for="name" value="Full Name" class="text-gray-700 dark:text-gray-300" />
                <TextInput id="name" type="text"
                    class="mt-1 block w-full border-gray-300 focus:border-[#1E40AF] focus:ring-[#1E40AF] dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    v-model="form.name" required autofocus autocomplete="name" placeholder="John Doe" />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div class="mt-4">
                <InputLabel for="email" value="Company Email" class="text-gray-700 dark:text-gray-300" />
                <TextInput id="email" type="email"
                    class="mt-1 block w-full border-gray-300 focus:border-[#1E40AF] focus:ring-[#1E40AF] dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    v-model="form.email" required autocomplete="username" placeholder="john.doe@montitextile.com" />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="grid gap-6 md:grid-cols-2">
                <div class="mt-4">
                    <InputLabel for="role" value="Department" class="text-gray-700 dark:text-gray-300" />
                    <Select v-model="form.role" required>
                        <option value="" disabled>Select department</option>
                        <option value="HRM">Human Resource Management</option>
                        <option value="SCM">Supply Chain Management</option>
                        <option value="FIN">Finance Management</option>
                        <option value="MAN">Manufacturing Management</option>
                        <option value="INV">Inventory Management</option>
                        <option value="ORD">Order Management</option>
                        <option value="WAR">Warehouse Management</option>
                        <option value="CRM">Customer Relationship Management</option>
                        <option value="ECO">Ecommerce Management</option>
                    </Select>
                    <InputError class="mt-2" :message="form.errors.role" />
                </div>

                <div class="mt-4">
                    <InputLabel for="position" value="Position" class="text-gray-700 dark:text-gray-300" />
                    <Select v-model="form.position" required>
                        <option value="" disabled>Select position</option>
                        <option value="manager">Manager</option>
                        <option value="staff">Staff</option>
                        <option value="trainee">Trainee</option>
                    </Select>
                    <InputError class="mt-2" :message="form.errors.position" />
                </div>
            </div>

            <div class="grid gap-6 md:grid-cols-2">
                <div class="mt-4">
                    <InputLabel for="password" value="Password" class="text-gray-700 dark:text-gray-300" />
                    <TextInput id="password" type="password"
                        class="mt-1 block w-full border-gray-300 focus:border-[#1E40AF] focus:ring-[#1E40AF] dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                        v-model="form.password" required autocomplete="new-password" placeholder="••••••••" />
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <div class="mt-4">
                    <InputLabel for="password_confirmation" value="Confirm Password"
                        class="text-gray-700 dark:text-gray-300" />
                    <TextInput id="password_confirmation" type="password"
                        class="mt-1 block w-full border-gray-300 focus:border-[#1E40AF] focus:ring-[#1E40AF] dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                        v-model="form.password_confirmation" required autocomplete="new-password"
                        placeholder="••••••••" />
                    <InputError class="mt-2" :message="form.errors.password_confirmation" />
                </div>
            </div>

            <div class="mt-6 rounded-lg bg-blue-50 p-4 dark:bg-blue-900/20">
                <h3 class="mb-2 text-sm font-semibold text-blue-800 dark:text-blue-300">Account Information</h3>
                <p class="text-xs text-blue-700 dark:text-blue-400">
                    Your account will be created with the selected department and position.
                    Please ensure you have the correct credentials from your department head.
                </p>
            </div>

            <div class="flex items-center justify-between mt-6">
                <Link :href="route('login')"
                    class="text-sm text-[#1E40AF] hover:text-[#1E3A8A] hover:underline dark:text-blue-400 dark:hover:text-blue-300">
                    Already have an account? Sign in
                </Link>

                <PrimaryButton
                    class="bg-[#1E40AF] hover:bg-[#1E3A8A] focus:bg-[#1E3A8A] dark:bg-blue-600 dark:hover:bg-blue-700"
                    :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    <span v-if="form.processing" class="inline-flex items-center">
                        <svg class="mr-2 h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                        </svg>
                        Creating account...
                    </span>
                    <span v-else>Create Employee Account</span>
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>