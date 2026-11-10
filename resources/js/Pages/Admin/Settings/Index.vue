<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    settings: Object,
    keys: Array,
});

const activeTab = ref('general');

const form = useForm(Object.fromEntries(props.keys.map((key) => [key, props.settings[key] ?? ''])));

const tabs = [
    ['general', 'General & Branding'],
    ['contact', 'Contact & Integrations'],
    ['seo', 'SEO & Tracking Scripts'],
];

const fields = {
    general: [
        ['site_name', 'Website name (default page title)'],
        ['brand_name', 'Brand name (logo alt, footer)'],
        ['site_tagline', 'Default meta description', 'textarea'],
        ['site_logo', 'Header logo path', 'mono'],
        ['site_logo_dark', 'Footer logo path', 'mono'],
        ['site_favicon', 'Favicon (SVG) path', 'mono'],
        ['established_year', 'Established year'],
    ],
    contact: [
        ['sales_email', 'Sales email (receives contact-form inquiries)'],
        ['support_email', 'Support email'],
        ['abuse_email', 'Abuse / NOC email'],
        ['whatsapp_number', 'WhatsApp number'],
        ['client_area_url', 'Client area URL'],
        ['robot_cart_url', 'Order cart URL (domain search)'],
    ],
    seo: [
        ['google_analytics_id', 'Google Analytics 4 ID', 'mono'],
        ['header_scripts', 'Custom code before </head> (GTM, Pixel…)', 'code'],
        ['footer_scripts', 'Custom code before </body>', 'code'],
        ['robots_txt', 'robots.txt', 'code'],
    ],
};

const save = () => form.post(route('admin.settings.update'), { preserveScroll: true });
const inputClass = 'w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg';
</script>

<template>
    <AdminLayout>
        <Head title="Global Settings" />

        <div class="space-y-6">
            <div>
                <h2 class="text-2xl font-bold text-slate-800">Global Website Settings</h2>
                <p class="text-sm text-slate-400">Brand, contact details and tracking scripts.</p>
            </div>

            <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 text-xs text-blue-800 flex items-start gap-3">
                <i class="fa-solid fa-circle-info mt-0.5"></i>
                <p>
                    The header strip, menus, social links, footer and floating dock are edited in
                    <Link :href="route('admin.layout.index')" class="font-bold underline">Site Layout</Link>.
                    Page titles and descriptions are set per page in <Link :href="route('admin.pages.index')" class="font-bold underline">Pages</Link>.
                </p>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 p-2 shadow-sm flex gap-1 overflow-x-auto">
                <button v-for="tab in tabs" :key="tab[0]" :class="['px-4 py-2 text-xs rounded-lg whitespace-nowrap', activeTab === tab[0] ? 'bg-blue-600 text-white font-bold' : 'text-slate-600 hover:bg-slate-100']" @click="activeTab = tab[0]">{{ tab[1] }}</button>
            </div>

            <form class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm text-xs" @submit.prevent="save">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div v-for="field in fields[activeTab]" :key="field[0]" :class="['textarea', 'code'].includes(field[2]) ? 'sm:col-span-2' : ''">
                        <label class="block font-semibold text-slate-600 mb-1">{{ field[1] }}</label>
                        <textarea v-if="field[2] === 'textarea'" v-model="form[field[0]]" rows="2" :class="inputClass"></textarea>
                        <textarea v-else-if="field[2] === 'code'" v-model="form[field[0]]" rows="5" :class="[inputClass, 'font-mono text-[11px]']"></textarea>
                        <input v-else v-model="form[field[0]]" type="text" :class="[inputClass, field[2] === 'mono' ? 'font-mono text-[11px]' : '']" />
                    </div>
                </div>
                <div class="flex justify-end pt-6 mt-6 border-t border-slate-100">
                    <button type="submit" :disabled="form.processing" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-md">Save Settings</button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
