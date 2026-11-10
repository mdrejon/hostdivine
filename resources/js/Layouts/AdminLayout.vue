<script setup>
import { ref, computed } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';

const page = usePage();
const user = computed(() => page.props.auth?.user || { name: 'Admin', role: 'super_admin' });
const unreadInquiries = computed(() => page.props.unreadInquiriesCount || 0);
const flash = computed(() => page.props.flash || {});

const sidebarOpen = ref(true);
const mobileMenuOpen = ref(false);
const userDropdownOpen = ref(false);
const searchQuery = ref('');

// Collapsible menus
const websiteMenuOpen = ref(true);
const settingsMenuOpen = ref(false);

const toggleSidebar = () => {
    sidebarOpen.value = !sidebarOpen.value;
};

const toggleMobileMenu = () => {
    mobileMenuOpen.value = !mobileMenuOpen.value;
};

const logout = () => {
    router.post(route('logout'));
};

// Menu navigation items
const menuItems = [
    { name: 'Dashboard', route: 'admin.dashboard', icon: 'fa-solid fa-table-cells-large', exact: true },
    { name: 'Inquiries', route: 'admin.inquiries.index', icon: 'fa-regular fa-envelope', badge: unreadInquiries },
    { name: 'Pages', route: 'admin.pages.index', icon: 'fa-regular fa-file-lines' },
    { name: 'Domain Pricing', route: 'admin.domain-pricing.index', icon: 'fa-solid fa-globe' },
];

const websiteSubItems = [
    { name: 'Site Layout (Header, Menu, Footer)', route: 'admin.layout.index', icon: 'fa-solid fa-table-columns' },
    { name: 'Shared Sections', route: 'admin.shared-sections.index', icon: 'fa-solid fa-link' },
    { name: 'Blog Articles', route: 'admin.blog.index', icon: 'fa-solid fa-newspaper' },
];

const settingsSubItems = [
    { name: 'Global Settings & SEO', route: 'admin.settings.index', icon: 'fa-solid fa-sliders' },
    { name: 'Email SMTP Setting', route: 'admin.smtp.index', icon: 'fa-solid fa-at' },
    { name: 'User Management', route: 'admin.users.index', icon: 'fa-solid fa-users-gear' },
];

const isActive = (routeName) => {
    try {
        const base = routeName.replace(/\.index$/, '');
        return route().current(routeName) || route().current(base + '.*');
    } catch (e) {
        return false;
    }
};

const filteredMenuItems = computed(() => {
    if (!searchQuery.value) return menuItems;
    const q = searchQuery.value.toLowerCase();
    return menuItems.filter(item => item.name.toLowerCase().includes(q));
});

const isDark = ref(false);
const toggleDarkMode = () => {
    isDark.value = !isDark.value;
    if (isDark.value) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
};
</script>

<template>
    <div class="min-h-screen bg-slate-50 flex flex-col antialiased text-slate-800">
        <!-- Toast Flash Notification -->
        <div v-if="flash.success" class="fixed top-5 right-5 z-50 bg-emerald-600 text-white px-5 py-3 rounded-lg shadow-lg flex items-center space-x-3 transition transform duration-300">
            <i class="fa-solid fa-circle-check text-lg"></i>
            <span>{{ flash.success }}</span>
        </div>
        <div v-if="flash.error" class="fixed top-5 right-5 z-50 bg-rose-600 text-white px-5 py-3 rounded-lg shadow-lg flex items-center space-x-3 transition transform duration-300">
            <i class="fa-solid fa-triangle-exclamation text-lg"></i>
            <span>{{ flash.error }}</span>
        </div>

        <div class="flex flex-1">
            <!-- Sidebar -->
            <aside 
                :class="[
                    sidebarOpen ? 'w-64' : 'w-20',
                    'bg-white border-r border-slate-200 transition-all duration-300 flex flex-col fixed inset-y-0 left-0 z-40 lg:static',
                    mobileMenuOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
                ]"
            >
                <!-- Brand / Admin User Block -->
                <div class="p-4 border-b border-slate-100 flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-full bg-blue-600 text-white font-bold flex items-center justify-center shrink-0 shadow-sm text-sm">
                        {{ user.name ? user.name.substring(0, 2).toUpperCase() : 'AD' }}
                    </div>
                    <div v-if="sidebarOpen" class="overflow-hidden">
                        <div class="font-bold text-slate-800 text-sm truncate leading-tight">{{ user.name }}</div>
                        <div class="text-xs text-slate-400 capitalize">{{ user.role ? user.role.replace('_', ' ') : 'Super Admin' }}</div>
                    </div>
                </div>

                <!-- Menu Search -->
                <div v-if="sidebarOpen" class="px-4 py-3">
                    <div class="relative">
                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-xs text-slate-400"></i>
                        <input 
                            v-model="searchQuery" 
                            type="text" 
                            placeholder="Search Menu Here..." 
                            class="w-full pl-8 pr-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white transition"
                        />
                    </div>
                </div>

                <!-- Navigation List -->
                <nav class="flex-1 overflow-y-auto px-3 py-2 space-y-1 text-sm select-none">
                    <template v-for="item in filteredMenuItems" :key="item.name">
                        <Link 
                            :href="route(item.route)" 
                            :class="[
                                isActive(item.route) 
                                    ? 'bg-blue-600 text-white shadow-sm font-medium' 
                                    : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900',
                                'flex items-center px-3 py-2.5 rounded-lg transition-colors group'
                            ]"
                            :title="!sidebarOpen ? item.name : ''"
                        >
                            <i :class="[item.icon, 'w-5 text-center text-base shrink-0', isActive(item.route) ? 'text-white' : 'text-slate-500 group-hover:text-blue-600']"></i>
                            <span v-if="sidebarOpen" class="ml-3 truncate flex-1">{{ item.name }}</span>
                            <span 
                                v-if="sidebarOpen && item.badge && item.badge.value > 0" 
                                class="ml-auto bg-amber-500 text-white text-xs px-2 py-0.5 rounded-full font-bold"
                            >
                                {{ item.badge.value }}
                            </span>
                        </Link>
                    </template>

                    <!-- Website Management Submenu -->
                    <div class="pt-2">
                        <button 
                            type="button" 
                            @click="websiteMenuOpen = !websiteMenuOpen"
                            class="w-full flex items-center px-3 py-2 text-xs font-semibold uppercase tracking-wider text-slate-400 hover:text-slate-600 justify-between focus:outline-none"
                        >
                            <span v-if="sidebarOpen" class="flex items-center space-x-2">
                                <i class="fa-solid fa-globe text-xs"></i>
                                <span>Website Management</span>
                            </span>
                            <i v-if="sidebarOpen" :class="['fa-solid text-xs transition-transform', websiteMenuOpen ? 'fa-chevron-down' : 'fa-chevron-right']"></i>
                        </button>
                        <div v-show="websiteMenuOpen && sidebarOpen" class="mt-1 space-y-1 pl-2 border-l-2 border-slate-100 ml-3">
                            <Link 
                                v-for="sub in websiteSubItems" 
                                :key="sub.name"
                                :href="route(sub.route)"
                                :class="[
                                    isActive(sub.route) 
                                        ? 'text-blue-600 font-semibold bg-blue-50' 
                                        : 'text-slate-600 hover:text-blue-600 hover:bg-slate-50',
                                    'flex items-center px-3 py-2 rounded-lg text-xs transition'
                                ]"
                            >
                                <i :class="[sub.icon, 'w-4 text-center mr-2 text-slate-400']"></i>
                                <span>{{ sub.name }}</span>
                            </Link>
                        </div>
                    </div>

                    <!-- Global Settings Submenu -->
                    <div class="pt-2">
                        <button 
                            type="button" 
                            @click="settingsMenuOpen = !settingsMenuOpen"
                            class="w-full flex items-center px-3 py-2 text-xs font-semibold uppercase tracking-wider text-slate-400 hover:text-slate-600 justify-between focus:outline-none"
                        >
                            <span v-if="sidebarOpen" class="flex items-center space-x-2">
                                <i class="fa-solid fa-gear text-xs"></i>
                                <span>Global Settings</span>
                            </span>
                            <i v-if="sidebarOpen" :class="['fa-solid text-xs transition-transform', settingsMenuOpen ? 'fa-chevron-down' : 'fa-chevron-right']"></i>
                        </button>
                        <div v-show="settingsMenuOpen && sidebarOpen" class="mt-1 space-y-1 pl-2 border-l-2 border-slate-100 ml-3">
                            <Link 
                                v-for="sub in settingsSubItems" 
                                :key="sub.name"
                                :href="route(sub.route)"
                                :class="[
                                    isActive(sub.route) 
                                        ? 'text-blue-600 font-semibold bg-blue-50' 
                                        : 'text-slate-600 hover:text-blue-600 hover:bg-slate-50',
                                    'flex items-center px-3 py-2 rounded-lg text-xs transition'
                                ]"
                            >
                                <i :class="[sub.icon, 'w-4 text-center mr-2 text-slate-400']"></i>
                                <span>{{ sub.name }}</span>
                            </Link>
                        </div>
                    </div>

                    <!-- System Cache / Backup -->
                    <div class="pt-2">
                        <Link 
                            :href="route('admin.system.cache-clear')" 
                            method="post" 
                            as="button"
                            class="w-full flex items-center px-3 py-2 text-xs text-slate-500 hover:bg-slate-100 hover:text-slate-700 rounded-lg transition"
                        >
                            <i class="fa-solid fa-arrows-rotate w-5 text-center text-slate-400"></i>
                            <span v-if="sidebarOpen" class="ml-3">Clear Cache & Optimize</span>
                        </Link>
                    </div>
                </nav>

                <!-- Footer version -->
                <div v-if="sidebarOpen" class="p-3 border-t border-slate-100 text-center text-[11px] text-slate-400">
                    Host Divine™ Admin v2.4
                </div>
            </aside>

            <!-- Main Workspace Container -->
            <div class="flex-1 flex flex-col min-w-0">
                <!-- Top Header Bar -->
                <header class="bg-white border-b border-slate-200 h-16 flex items-center justify-between px-4 sm:px-6 sticky top-0 z-30">
                    <div class="flex items-center space-x-3">
                        <button 
                            type="button" 
                            @click="toggleSidebar"
                            class="hidden lg:flex p-2 text-slate-500 hover:text-slate-700 hover:bg-slate-100 rounded-lg focus:outline-none"
                            title="Toggle Sidebar"
                        >
                            <i class="fa-solid fa-bars text-lg"></i>
                        </button>
                        <button 
                            type="button" 
                            @click="toggleMobileMenu"
                            class="lg:hidden p-2 text-slate-500 hover:text-slate-700 hover:bg-slate-100 rounded-lg focus:outline-none"
                        >
                            <i class="fa-solid fa-bars text-lg"></i>
                        </button>
                        <h1 class="text-base font-semibold text-slate-700 hidden sm:block">Host Divine Management</h1>
                    </div>

                    <!-- Right Controls -->
                    <div class="flex items-center space-x-3 sm:space-x-4">
                        <!-- Go to Home -->
                        <a 
                            href="/" 
                            target="_blank" 
                            class="flex items-center space-x-1.5 text-xs font-semibold text-blue-600 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition"
                            title="Open public website in new tab"
                        >
                            <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                            <span>Go to Home</span>
                        </a>

                        <!-- Notifications / Inquiries -->
                        <Link 
                            :href="route('admin.inquiries.index')" 
                            class="relative p-2 text-slate-500 hover:text-slate-700 hover:bg-slate-100 rounded-lg transition"
                            title="Customer Inquiries"
                        >
                            <i class="fa-regular fa-bell text-lg"></i>
                            <span 
                                v-if="unreadInquiries > 0" 
                                class="absolute top-1 right-1 w-2.5 h-2.5 bg-rose-500 rounded-full border-2 border-white"
                            ></span>
                        </Link>

                        <!-- Settings Icon -->
                        <Link 
                            :href="route('admin.settings.index')" 
                            class="p-2 text-slate-500 hover:text-slate-700 hover:bg-slate-100 rounded-lg transition"
                            title="Settings"
                        >
                            <i class="fa-solid fa-gear text-lg"></i>
                        </Link>

                        <!-- Dark Mode Toggle -->
                        <button 
                            type="button" 
                            @click="toggleDarkMode" 
                            class="p-2 text-slate-500 hover:text-slate-700 hover:bg-slate-100 rounded-lg transition"
                            title="Toggle Theme"
                        >
                            <i :class="['fa-regular text-lg', isDark ? 'fa-sun' : 'fa-moon']"></i>
                        </button>

                        <!-- User Profile Dropdown -->
                        <div class="relative">
                            <button 
                                type="button" 
                                @click="userDropdownOpen = !userDropdownOpen"
                                class="flex items-center space-x-2 p-1 rounded-full hover:ring-2 hover:ring-slate-200 focus:outline-none transition"
                            >
                                <div class="w-8 h-8 rounded-full bg-blue-600 text-white font-bold flex items-center justify-center text-xs shadow-sm">
                                    {{ user.name ? user.name.substring(0, 2).toUpperCase() : 'AD' }}
                                </div>
                                <span class="hidden md:inline text-xs font-semibold text-slate-700">{{ user.name }}</span>
                                <i class="fa-solid fa-chevron-down text-[10px] text-slate-400"></i>
                            </button>

                            <div 
                                v-show="userDropdownOpen" 
                                @click.outside="userDropdownOpen = false"
                                class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl border border-slate-100 py-1.5 z-50 text-xs"
                            >
                                <div class="px-4 py-2 border-b border-slate-100">
                                    <div class="font-bold text-slate-800">{{ user.name }}</div>
                                    <div class="text-slate-400 truncate">{{ user.email }}</div>
                                </div>
                                <Link :href="route('profile.show')" class="flex items-center px-4 py-2 text-slate-600 hover:bg-slate-50 hover:text-slate-900">
                                    <i class="fa-regular fa-user mr-2 text-slate-400"></i> My Profile
                                </Link>
                                <Link :href="route('admin.settings.index')" class="flex items-center px-4 py-2 text-slate-600 hover:bg-slate-50 hover:text-slate-900">
                                    <i class="fa-solid fa-sliders mr-2 text-slate-400"></i> Settings
                                </Link>
                                <div class="border-t border-slate-100 my-1"></div>
                                <button 
                                    @click="logout" 
                                    class="w-full text-left flex items-center px-4 py-2 text-rose-600 hover:bg-rose-50"
                                >
                                    <i class="fa-solid fa-arrow-right-from-bracket mr-2"></i> Log Out
                                </button>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Dynamic Page Content Slot -->
                <main class="flex-1 p-4 sm:p-6 lg:p-8 overflow-y-auto">
                    <slot />
                </main>
            </div>
        </div>
    </div>
</template>

