<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    inquiry: Object,
});

const form = useForm({
    status: props.inquiry.status,
    priority: props.inquiry.priority,
    admin_notes: props.inquiry.admin_notes || '',
});

const submit = () => {
    form.put(route('admin.inquiries.update', props.inquiry.id), {
        preserveScroll: true,
    });
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
        <Head :title="'Inquiry #' + inquiry.id" />

        <div class="max-w-4xl mx-auto space-y-6">
            <div class="flex items-center justify-between">
                <Link :href="route('admin.inquiries.index')" class="text-xs font-semibold text-blue-600 hover:underline flex items-center space-x-1">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span>Back to Inquiries</span>
                </Link>
                <div class="flex items-center space-x-2">
                    <a 
                        v-if="getWhatsAppLink(inquiry)" 
                        :href="getWhatsAppLink(inquiry)" 
                        target="_blank"
                        class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg shadow-sm flex items-center space-x-1.5 transition"
                    >
                        <i class="fa-brands fa-whatsapp text-sm"></i>
                        <span>Reply via WhatsApp</span>
                    </a>
                    <a 
                        :href="'mailto:' + inquiry.email + '?subject=Re: Host Divine Inquiry - ' + inquiry.department"
                        class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg shadow-sm flex items-center space-x-1.5 transition"
                    >
                        <i class="fa-regular fa-envelope text-sm"></i>
                        <span>Reply via Email</span>
                    </a>
                </div>
            </div>

            <!-- Detail Card -->
            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
                <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Inquiry #{{ inquiry.id }}</span>
                        <h2 class="text-xl font-bold text-slate-800">{{ inquiry.name }}</h2>
                    </div>
                    <span class="text-xs text-slate-400">{{ inquiry.created_at }}</span>
                </div>

                <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="space-y-4 text-xs md:col-span-1 border-b md:border-b-0 md:border-r border-slate-100 pb-4 md:pb-0 md:pr-4">
                        <div>
                            <span class="text-slate-400 block font-semibold">Email:</span>
                            <a :href="'mailto:' + inquiry.email" class="text-blue-600 font-medium hover:underline">{{ inquiry.email }}</a>
                        </div>
                        <div>
                            <span class="text-slate-400 block font-semibold">Phone / WhatsApp:</span>
                            <span class="text-slate-700 font-mono">{{ inquiry.phone || 'Not provided' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block font-semibold">Department:</span>
                            <span class="inline-block mt-1 px-2.5 py-1 rounded bg-slate-100 font-medium text-slate-700">{{ inquiry.department }}</span>
                        </div>
                        <div v-if="inquiry.service_id">
                            <span class="text-slate-400 block font-semibold">Service / Domain ID:</span>
                            <span class="text-slate-700 font-mono">{{ inquiry.service_id }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block font-semibold">IP Address:</span>
                            <span class="text-slate-400 font-mono text-[11px]">{{ inquiry.ip_address || '127.0.0.1' }}</span>
                        </div>
                    </div>

                    <div class="md:col-span-2 space-y-4">
                        <div>
                            <span class="text-xs font-semibold text-slate-400 block mb-1">Customer Message:</span>
                            <div class="bg-slate-50 border border-slate-100 p-4 rounded-xl text-sm text-slate-700 whitespace-pre-line leading-relaxed">
                                {{ inquiry.message }}
                            </div>
                        </div>

                        <!-- Update Status & Notes Form -->
                        <form @submit.prevent="submit" class="pt-4 border-t border-slate-100 space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1">Status</label>
                                    <select v-model="form.status" class="w-full text-xs py-2 bg-slate-50 border border-slate-200 rounded-lg focus:ring-1 focus:ring-blue-500">
                                        <option value="new">New</option>
                                        <option value="in_progress">In Progress</option>
                                        <option value="contacted">Contacted</option>
                                        <option value="closed">Closed / Resolved</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1">Priority</label>
                                    <select v-model="form.priority" class="w-full text-xs py-2 bg-slate-50 border border-slate-200 rounded-lg focus:ring-1 focus:ring-blue-500">
                                        <option value="low">Low</option>
                                        <option value="normal">Normal</option>
                                        <option value="high">High</option>
                                        <option value="urgent">Urgent</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Internal Admin Notes</label>
                                <textarea 
                                    v-model="form.admin_notes" 
                                    rows="3" 
                                    placeholder="Add notes for your team (e.g., client called via WhatsApp, quotation sent)..." 
                                    class="w-full text-xs p-3 bg-slate-50 border border-slate-200 rounded-lg focus:ring-1 focus:ring-blue-500"
                                ></textarea>
                            </div>

                            <button 
                                type="submit" 
                                :disabled="form.processing"
                                class="px-5 py-2 text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-sm transition"
                            >
                                Save Changes
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

