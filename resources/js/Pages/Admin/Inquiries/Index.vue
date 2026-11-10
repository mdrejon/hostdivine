<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    inquiries: Object,
    departments: Array,
    filters: Object,
});

const search = ref(props.filters?.search || '');
const status = ref(props.filters?.status || '');
const department = ref(props.filters?.department || '');

const applyFilter = () => {
    router.get(route('admin.inquiries.index'), {
        search: search.value,
        status: status.value,
        department: department.value,
    }, { preserveState: true, replace: true });
};

const updateStatus = (inquiry, newStatus) => {
    router.patch(route('admin.inquiries.update-status', inquiry.id), {
        status: newStatus,
    }, { preserveScroll: true });
};

const deleteInquiry = (id) => {
    if (confirm('Are you sure you want to delete this inquiry?')) {
        router.delete(route('admin.inquiries.destroy', id));
    }
};

const getStatusBadge = (st) => {
    switch (st) {
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
    const text = encodeURIComponent(`Hello ${inquiry.name}, thank you for contacting Host Divine Networks regarding ${inquiry.department}.`);
    return `https://wa.me/${cleanPhone}?text=${text}`;
};
</script>

<template>
    <AdminLayout>
        <Head title="Manage Inquiries" />

        <div class="space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-slate-800">Inquiries & Leads</h2>
                    <p class="text-sm text-slate-400">Incoming inquiries from website contact form and migration requests.</p>
                </div>
            </div>

            <!-- Filter Strip -->
            <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm flex flex-col md:flex-row gap-3">
                <div class="flex-1 relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-xs text-slate-400"></i>
                    <input 
                        v-model="search" 
                        @keyup.enter="applyFilter"
                        type="text" 
                        placeholder="Search by name, email or message..." 
                        class="w-full pl-8 pr-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:ring-1 focus:ring-blue-500"
                    />
                </div>
                <div class="w-full md:w-48">
                    <select v-model="status" @change="applyFilter" class="w-full text-xs py-1.5 bg-slate-50 border border-slate-200 rounded-lg">
                        <option value="">All Statuses</option>
                        <option value="new">New</option>
                        <option value="in_progress">In Progress</option>
                        <option value="contacted">Contacted</option>
                        <option value="closed">Closed / Resolved</option>
                    </select>
                </div>
                <div class="w-full md:w-56">
                    <select v-model="department" @change="applyFilter" class="w-full text-xs py-1.5 bg-slate-50 border border-slate-200 rounded-lg">
                        <option value="">All Departments</option>
                        <option v-for="dept in departments" :key="dept" :value="dept">{{ dept }}</option>
                    </select>
                </div>
                <button @click="applyFilter" class="px-4 py-1.5 text-xs font-semibold bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Filter
                </button>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-600">
                        <thead class="bg-slate-50 text-slate-400 font-semibold uppercase tracking-wider border-b border-slate-100">
                            <tr>
                                <th class="px-5 py-3">Customer</th>
                                <th class="px-5 py-3">Department</th>
                                <th class="px-5 py-3">Message</th>
                                <th class="px-5 py-3">Status</th>
                                <th class="px-5 py-3">Date</th>
                                <th class="px-5 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-if="inquiries.data.length === 0">
                                <td colspan="6" class="px-5 py-8 text-center text-slate-400">
                                    No inquiries found matching criteria.
                                </td>
                            </tr>
                            <tr v-for="inq in inquiries.data" :key="inq.id" class="hover:bg-slate-50 transition">
                                <td class="px-5 py-3.5">
                                    <div class="font-bold text-slate-800">{{ inq.name }}</div>
                                    <div class="text-slate-400">{{ inq.email }}</div>
                                    <div v-if="inq.phone" class="text-slate-500 text-[11px] font-mono">{{ inq.phone }}</div>
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="inline-block px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 text-slate-700">
                                        {{ inq.department }}
                                    </span>
                                    <div v-if="inq.service_id" class="text-[10px] text-slate-400 mt-0.5">Service: {{ inq.service_id }}</div>
                                </td>
                                <td class="px-5 py-3.5 max-w-xs truncate" :title="inq.message">
                                    {{ inq.message }}
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap">
                                    <select 
                                        :value="inq.status" 
                                        @change="updateStatus(inq, $event.target.value)"
                                        :class="['text-[11px] font-bold rounded-lg border py-1 px-2 focus:ring-1 focus:ring-blue-500', getStatusBadge(inq.status)]"
                                    >
                                        <option value="new">New</option>
                                        <option value="in_progress">In Progress</option>
                                        <option value="contacted">Contacted</option>
                                        <option value="closed">Closed</option>
                                    </select>
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap text-slate-400">
                                    {{ inq.created_at }}
                                </td>
                                <td class="px-5 py-3.5 text-right whitespace-nowrap space-x-1.5">
                                    <a 
                                        v-if="getWhatsAppLink(inq)" 
                                        :href="getWhatsAppLink(inq)" 
                                        target="_blank" 
                                        class="inline-flex items-center justify-center w-7 h-7 bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white rounded-lg transition"
                                        title="WhatsApp Message"
                                    >
                                        <i class="fa-brands fa-whatsapp"></i>
                                    </a>
                                    <Link 
                                        :href="route('admin.inquiries.show', inq.id)" 
                                        class="inline-flex items-center justify-center w-7 h-7 bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white rounded-lg transition"
                                        title="View Details & Notes"
                                    >
                                        <i class="fa-regular fa-eye"></i>
                                    </Link>
                                    <button 
                                        @click="deleteInquiry(inq.id)" 
                                        class="inline-flex items-center justify-center w-7 h-7 bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white rounded-lg transition"
                                        title="Delete"
                                    >
                                        <i class="fa-regular fa-trash-can"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="inquiries.links && inquiries.links.length > 3" class="p-4 border-t border-slate-100 flex justify-between items-center text-xs">
                    <span class="text-slate-400">Showing {{ inquiries.from }} to {{ inquiries.to }} of {{ inquiries.total }} entries</span>
                    <div class="flex space-x-1">
                        <Link 
                            v-for="(link, i) in inquiries.links" 
                            :key="i"
                            :href="link.url || '#'"
                            :class="[
                                link.active ? 'bg-blue-600 text-white font-bold' : 'bg-slate-50 text-slate-600 hover:bg-slate-100',
                                'px-3 py-1 rounded border border-slate-200'
                            ]"
                            v-html="link.label"
                        />
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

