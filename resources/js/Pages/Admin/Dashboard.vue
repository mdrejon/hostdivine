<script setup>
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    stats: {
        type: Object,
        default: () => ({
            todayInquiries: 0,
            pendingInquiries: 0,
            newInquiries: 0,
            totalInquiries: 0,
            activePages: 0,
            domainTlds: 0,
            staffUsers: 1,
            publishedPosts: 0,
        })
    },
    weeklyChart: {
        type: Array,
        default: () => []
    },
    statusCounts: {
        type: Object,
        default: () => ({
            new: 0,
            in_progress: 0,
            contacted: 0,
            closed: 0,
        })
    },
    recentInquiries: {
        type: Array,
        default: () => []
    }
});

const totalStatusCount = computed(() => {
    return (props.statusCounts.new || 0) + (props.statusCounts.in_progress || 0) + (props.statusCounts.contacted || 0) + (props.statusCounts.closed || 0) || 1;
});

const getStatusBadge = (status) => {
    switch (status) {
        case 'new': return 'bg-amber-100 text-amber-800 border-amber-200';
        case 'in_progress': return 'bg-blue-100 text-blue-800 border-blue-200';
        case 'contacted': return 'bg-indigo-100 text-indigo-800 border-indigo-200';
        case 'closed': return 'bg-emerald-100 text-emerald-800 border-emerald-200';
        default: return 'bg-slate-100 text-slate-800 border-slate-200';
    }
};

const getWhatsAppLink = (inquiry) => {
    if (!inquiry.phone) return null;
    const cleanPhone = inquiry.phone.replace(/[^0-9]/g, '');
    const text = encodeURIComponent(`Hello ${inquiry.name}, thank you for contacting Host Divine Networks regarding your inquiry on ${inquiry.department}.`);
    return `https://wa.me/${cleanPhone}?text=${text}`;
};
</script>

<template>
    <AdminLayout>
        <Head title="Admin Dashboard" />

        <div class="space-y-6">
            <!-- Header Section -->
            <div>
                <h2 class="text-2xl font-bold text-slate-800">Dashboard</h2>
                <p class="text-sm text-slate-400">Overview of inquiries, hosting packages and content updates.</p>
            </div>

            <!-- KPI Cards Grid (Matches Prototype Visual) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Card 1 -->
                <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm hover:shadow transition">
                    <div class="text-3xl font-extrabold text-blue-600 mb-1">{{ stats.todayInquiries }}</div>
                    <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Today's Inquiries</div>
                </div>

                <!-- Card 2 -->
                <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm hover:shadow transition">
                    <div class="text-3xl font-extrabold text-amber-500 mb-1">{{ stats.pendingInquiries }}</div>
                    <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Pending Inquiries</div>
                </div>

                <!-- Card 3 -->
                <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm hover:shadow transition">
                    <div class="text-3xl font-extrabold text-teal-600 mb-1">{{ stats.newInquiries }}</div>
                    <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">New Inquiries</div>
                </div>

                <!-- Card 4 -->
                <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm hover:shadow transition">
                    <div class="text-3xl font-extrabold text-slate-700 mb-1">{{ stats.totalInquiries }}</div>
                    <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Inquiries</div>
                </div>

                <!-- Card 5 -->
                <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm hover:shadow transition">
                    <div class="text-3xl font-extrabold text-emerald-600 mb-1">{{ stats.activePages }}</div>
                    <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Published Pages</div>
                </div>

                <!-- Card 6 -->
                <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm hover:shadow transition">
                    <div class="text-3xl font-extrabold text-indigo-600 mb-1">{{ stats.domainTlds }}</div>
                    <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Domain TLDs Managed</div>
                </div>

                <!-- Card 7 -->
                <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm hover:shadow transition">
                    <div class="text-3xl font-extrabold text-purple-600 mb-1">{{ stats.publishedPosts }}</div>
                    <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Published Articles</div>
                </div>

                <!-- Card 8 -->
                <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm hover:shadow transition">
                    <div class="text-3xl font-extrabold text-rose-600 mb-1">{{ stats.staffUsers }}</div>
                    <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Staff Users</div>
                </div>
            </div>

            <!-- Two-Column Chart & Status Breakdown (Matches Prototype Layout) -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left: 7-Day Inquiries Chart Visual -->
                <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="font-bold text-slate-800 text-sm">Inquiries — Last 7 Days</h3>
                        <span class="text-xs text-slate-400">Daily Leads & Contacts</span>
                    </div>

                    <!-- Custom CSS Bar Visualizer -->
                    <div class="flex items-end justify-between h-48 pt-6 px-4 border-b border-slate-100">
                        <div v-for="bar in weeklyChart" :key="bar.day" class="flex flex-col items-center flex-1 space-y-2">
                            <span class="text-xs font-bold text-slate-500">{{ bar.count }}</span>
                            <div class="w-12 bg-blue-100 rounded-t-md relative flex items-end justify-center transition-all duration-500" :style="{ height: Math.max(bar.count * 20, 16) + 'px' }">
                                <div class="w-full bg-blue-600 rounded-t-md" :style="{ height: (bar.count > 0 ? '100%' : '15%') }"></div>
                            </div>
                            <span class="text-xs font-medium text-slate-500 mt-2">{{ bar.day }}</span>
                        </div>
                    </div>
                </div>

                <!-- Right: Inquiry Status Distribution -->
                <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm flex flex-col justify-between">
                    <div>
                        <h3 class="font-bold text-slate-800 text-sm mb-5">Inquiry Status</h3>
                        
                        <div class="space-y-4">
                            <!-- Pending -->
                            <div>
                                <div class="flex justify-between text-xs font-semibold mb-1">
                                    <span class="text-amber-600">Pending</span>
                                    <span>{{ statusCounts.new || 0 }}</span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                    <div class="bg-amber-400 h-2 rounded-full" :style="{ width: ((statusCounts.new || 0) / totalStatusCount * 100) + '%' }"></div>
                                </div>
                            </div>

                            <!-- Contacted -->
                            <div>
                                <div class="flex justify-between text-xs font-semibold mb-1">
                                    <span class="text-blue-600">Contacted / In Progress</span>
                                    <span>{{ (statusCounts.in_progress || 0) + (statusCounts.contacted || 0) }}</span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                    <div class="bg-blue-500 h-2 rounded-full" :style="{ width: (((statusCounts.in_progress || 0) + (statusCounts.contacted || 0)) / totalStatusCount * 100) + '%' }"></div>
                                </div>
                            </div>

                            <!-- Closed -->
                            <div>
                                <div class="flex justify-between text-xs font-semibold mb-1">
                                    <span class="text-emerald-600">Resolved / Closed</span>
                                    <span>{{ statusCounts.closed || 0 }}</span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                    <div class="bg-emerald-500 h-2 rounded-full" :style="{ width: ((statusCounts.closed || 0) / totalStatusCount * 100) + '%' }"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-slate-100 mt-6">
                        <Link :href="route('admin.inquiries.index')" class="w-full block text-center py-2 text-xs font-semibold text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-lg transition">
                            View All Inquiries →
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Recent Inquiries Table -->
            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-slate-800 text-sm">Recent Customer Inquiries</h3>
                        <p class="text-xs text-slate-400">Latest submissions from the contact form</p>
                    </div>
                    <Link :href="route('admin.inquiries.index')" class="text-xs font-semibold text-blue-600 hover:underline">
                        Manage Leads
                    </Link>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-600">
                        <thead class="bg-slate-50 text-slate-400 font-semibold uppercase tracking-wider border-b border-slate-100">
                            <tr>
                                <th class="px-5 py-3">Customer</th>
                                <th class="px-5 py-3">Department</th>
                                <th class="px-5 py-3">Message Excerpt</th>
                                <th class="px-5 py-3">Status</th>
                                <th class="px-5 py-3">Date</th>
                                <th class="px-5 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-if="recentInquiries.length === 0">
                                <td colspan="6" class="px-5 py-8 text-center text-slate-400">
                                    <i class="fa-regular fa-folder-open text-2xl mb-2 block"></i>
                                    No inquiries received yet. Submit a test inquiry on the contact page.
                                </td>
                            </tr>
                            <tr v-for="inq in recentInquiries" :key="inq.id" class="hover:bg-slate-50 transition">
                                <td class="px-5 py-3.5">
                                    <div class="font-bold text-slate-800">{{ inq.name }}</div>
                                    <div class="text-slate-400">{{ inq.email }}</div>
                                    <div v-if="inq.phone" class="text-slate-400 text-[11px]">{{ inq.phone }}</div>
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="inline-block px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 text-slate-700">
                                        {{ inq.department }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 max-w-xs truncate">
                                    {{ inq.message }}
                                </td>
                                <td class="px-5 py-3.5">
                                    <span :class="['px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border', getStatusBadge(inq.status)]">
                                        {{ inq.status }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap text-slate-400">
                                    {{ inq.created_at }}
                                </td>
                                <td class="px-5 py-3.5 text-right whitespace-nowrap space-x-2">
                                    <a 
                                        v-if="getWhatsAppLink(inq)" 
                                        :href="getWhatsAppLink(inq)" 
                                        target="_blank" 
                                        class="inline-flex items-center justify-center w-7 h-7 bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white rounded-lg transition"
                                        title="Reply on WhatsApp"
                                    >
                                        <i class="fa-brands fa-whatsapp"></i>
                                    </a>
                                    <Link 
                                        :href="route('admin.inquiries.show', inq.id)" 
                                        class="inline-flex items-center justify-center w-7 h-7 bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white rounded-lg transition"
                                        title="View Details"
                                    >
                                        <i class="fa-regular fa-eye"></i>
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

