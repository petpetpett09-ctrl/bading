<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';

defineProps<{
    canLogin?: boolean;
    canRegister?: boolean;
    laravelVersion: string;
    phpVersion: string;
}>();

const products = [
    { name: 'Organic Cotton', desc: 'Sustainable, high-thread-count cotton for premium apparel.', icon: 'M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707' },
    { name: 'Technical Fabrics', desc: 'High-performance synthetics for industrial and athletic use.', icon: 'M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751A11.956 11.956 0 0112 2.714z' },
    { name: 'Eco-Blends', desc: 'Recycled polyester and bamboo fibers for the eco-conscious brand.', icon: 'M7.5 12a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0z' },
];

function handleImageError() {
    document.getElementById('screenshot-container')?.classList.add('!hidden');
    document.getElementById('background')?.classList.add('!hidden');
}
</script>

<template>

    <Head title="Monti Textile Corp | Global Manufacturing" />

    <div
        class="bg-slate-50 text-slate-900 dark:bg-slate-950 dark:text-slate-100 min-h-screen font-sans selection:bg-blue-500 selection:text-white">
        <div class="absolute inset-0 opacity-[0.03] dark:opacity-[0.05] pointer-events-none"
            style="background-image: url('https://www.transparenttextures.com/patterns/carbon-fibre.png');">
        </div>

        <div class="relative w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <header class="flex items-center justify-between py-8 border-b border-slate-200 dark:border-slate-800">
                <div class="flex items-center gap-2">
                    <div class="h-10 w-10 flex-shrink-0 mr-3">
                        <img src="/images/applogo.png" alt="Monti Textile Logo" class="h-full w-full object-contain" />
                    </div>
                    <span
                        class="text-2xl font-black tracking-tight uppercase text-blue-900 dark:text-blue-400">Monti<span
                            class="font-light text-slate-500">Textile</span></span>
                </div>

                <nav v-if="canLogin" class="flex items-center space-x-4">
                    <Link v-if="$page.props.auth.user" :href="route('dashboard')"
                        class="text-sm font-semibold bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-full transition shadow-lg shadow-blue-500/30">
                        Go to Dashboard
                    </Link>
                    <template v-else>
                        <Link :href="route('login')"
                            class="text-sm font-medium hover:text-blue-600 dark:hover:text-blue-400 transition">Log in
                        </Link>
                        <Link v-if="canRegister" :href="route('register')"
                            class="text-sm font-semibold border-2 border-blue-600 text-blue-600 dark:text-blue-400 px-5 py-2 rounded-full hover:bg-blue-600 hover:text-white transition">
                            Partner Registration</Link>
                    </template>
                </nav>
            </header>

            <main class="py-12">
                <section class="grid lg:grid-cols-2 gap-12 items-center mb-20">
                    <div>
                        <span
                            class="inline-block px-3 py-1 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 text-xs font-bold uppercase tracking-widest mb-4">Established
                            1985</span>
                        <h1 class="text-5xl lg:text-7xl font-extrabold leading-tight mb-6">
                            The Future of <span class="text-blue-600">Textile</span> Engineering.
                        </h1>
                        <p class="text-lg text-slate-600 dark:text-slate-400 mb-8 max-w-xl">
                            Monti Textile Corporation delivers high-precision fabric solutions to the world’s leading
                            fashion houses
                            and industrial manufacturers. From raw fiber to finished bolt, we define quality.
                        </p>
                        <div class="flex gap-4">
                            <button
                                class="bg-blue-600 text-white px-8 py-4 rounded-xl font-bold hover:translate-y-[-2px] transition active:scale-95 shadow-xl shadow-blue-600/20">Our
                                Catalog</button>
                            <button
                                class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-8 py-4 rounded-xl font-bold hover:bg-slate-50 transition shadow-sm">Sustainability
                                Report</button>
                        </div>
                    </div>
                    <div id="screenshot-container" class="relative">
                        <div
                            class="absolute -inset-4 bg-gradient-to-tr from-blue-500 to-indigo-500 rounded-3xl blur-2xl opacity-20 dark:opacity-40">
                        </div>
                        <img src="https://images.unsplash.com/photo-1558227691-41ea78d1f631?q=80&w=2000&auto=format&fit=crop"
                            alt="Textile Production"
                            class="relative rounded-2xl shadow-2xl border border-white/20 object-cover aspect-[4/3]"
                            @error="handleImageError" />
                    </div>
                </section>

                <hr class="border-slate-200 dark:border-slate-800 my-16">

                <section class="grid md:grid-cols-3 gap-8 mb-24">
                    <div
                        class="group bg-white dark:bg-slate-900 p-8 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-blue-400 transition shadow-sm">
                        <div
                            class="size-12 bg-blue-50 dark:bg-blue-900/20 rounded-lg flex items-center justify-center text-blue-600 mb-6 group-hover:scale-110 transition">
                            <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold mb-3">Workforce HRM</h3>
                        <p class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed mb-6">Manage our 500+
                            global staff
                            members through the integrated Human Resource portal.</p>
                        <a href="#" class="inline-flex items-center text-blue-600 font-bold text-sm">Access Portal <svg
                                class="size-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M9 5l7 7-7 7" stroke-width="3" />
                            </svg></a>
                    </div>

                    <div
                        class="group bg-white dark:bg-slate-900 p-8 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-blue-400 transition shadow-sm">
                        <div
                            class="size-12 bg-blue-50 dark:bg-blue-900/20 rounded-lg flex items-center justify-center text-blue-600 mb-6 group-hover:scale-110 transition">
                            <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold mb-3">Supply Chain (SCM)</h3>
                        <p class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed mb-6">Real-time tracking of
                            raw
                            materials and logistics across 50+ countries served.</p>
                        <a href="#" class="inline-flex items-center text-blue-600 font-bold text-sm">Track Logistics
                            <svg class="size-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M9 5l7 7-7 7" stroke-width="3" />
                            </svg></a>
                    </div>

                    <div
                        class="group bg-white dark:bg-slate-900 p-8 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-blue-400 transition shadow-sm">
                        <div
                            class="size-12 bg-blue-50 dark:bg-blue-900/20 rounded-lg flex items-center justify-center text-blue-600 mb-6 group-hover:scale-110 transition">
                            <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.675.27a6 6 0 01-3.86.517L5.238 15.24a2 2 0 00-1.022.547l-1.414 1.414a2 2 0 001.414 3.414h15.586a2 2 0 001.414-3.414l-1.414-1.414z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold mb-3">Fabric Innovation</h3>
                        <p class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed mb-6">Explore our latest
                            patents in
                            moisture-wicking and antimicrobial textile tech.</p>
                        <a href="#" class="inline-flex items-center text-blue-600 font-bold text-sm">Research Data <svg
                                class="size-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M9 5l7 7-7 7" stroke-width="3" />
                            </svg></a>
                    </div>
                </section>

                <section class="mb-24">
                    <h2 class="text-3xl font-bold mb-12 text-center">Core Textile Categories</h2>
                    <div class="grid md:grid-cols-3 gap-6">
                        <div v-for="product in products" :key="product.name"
                            class="p-1 rounded-2xl bg-gradient-to-b from-slate-200 to-transparent dark:from-slate-800">
                            <div class="bg-white dark:bg-slate-900 rounded-[calc(1rem-1px)] p-6 h-full">
                                <h4 class="font-bold text-lg mb-2">{{ product.name }}</h4>
                                <p class="text-slate-500 text-sm">{{ product.desc }}</p>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="bg-blue-900 rounded-3xl p-12 text-white overflow-hidden relative shadow-2xl">
                    <div
                        class="absolute top-0 right-0 -translate-y-1/2 translate-x-1/4 size-96 bg-blue-500/20 rounded-full blur-3xl">
                    </div>
                    <div class="relative grid md:grid-cols-3 gap-8 text-center">
                        <div>
                            <div class="text-5xl font-black mb-2">500+</div>
                            <div class="text-blue-200 uppercase tracking-widest text-xs font-bold">Skilled Experts</div>
                        </div>
                        <div>
                            <div class="text-5xl font-black mb-2">38</div>
                            <div class="text-blue-200 uppercase tracking-widest text-xs font-bold">Years of Trust</div>
                        </div>
                        <div>
                            <div class="text-5xl font-black mb-2">100%</div>
                            <div class="text-blue-200 uppercase tracking-widest text-xs font-bold">Quality Certified
                            </div>
                        </div>
                    </div>
                </section>
            </main>

            <footer class="py-12 border-t border-slate-200 dark:border-slate-800">
                <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                    <div class="text-sm text-slate-500 dark:text-slate-400">
                        © 2026 Monti Textile Manufacturing Co. <span class="mx-2">|</span>
                        <span class="font-mono text-[10px] opacity-60">System: L{{ laravelVersion }} - P{{ phpVersion
                        }}</span>
                    </div>
                    <div class="flex space-x-8 text-sm font-medium">
                        <a href="#" class="hover:text-blue-600 transition">Privacy</a>
                        <a href="#" class="hover:text-blue-600 transition">Terms</a>
                        <a href="#" class="hover:text-blue-600 transition">Global Support</a>
                        <a href="#" class="hover:text-blue-600 transition">Investor Relations</a>
                    </div>
                </div>
            </footer>
        </div>
    </div>
</template>