<script setup>
import { computed, ref, watch } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    tlds: Array,
});

const search = ref('');
const list = ref('all');
const page = ref(1);
const perPage = 50;
const showModal = ref(false);
const editingTld = ref(null);

const terms = [1, 2, 3, 5, 10];

const blank = () => ({
    tld: '', price_1yr: '', price_2yr: '', price_3yr: '', price_5yr: '', price_10yr: '',
    transfer_price: '', renew_prices: { 1: '', 2: '', 3: '', 5: '', 10: '' }, badge: '',
    popular_order: '', featured_order: '', renew_order: '', sort_order: 0, is_active: true,
});

const form = useForm(blank());

const lists = [
    ['all', 'All extensions'],
    ['popular', 'Popular table'],
    ['featured', 'Search chips'],
    ['renew', 'Renewal table'],
];

const filteredTlds = computed(() => {
    const q = search.value.toLowerCase().replace(/^\.?/, '.');
    let rows = props.tlds.filter((t) => search.value.length < 2 || t.tld.includes(q) || t.tld.includes(search.value.toLowerCase()));
    if (list.value !== 'all') {
        const key = `${list.value}_order`;
        rows = rows.filter((t) => t[key]).sort((a, b) => a[key] - b[key]);
    }
    return rows;
});

const pages = computed(() => Math.max(1, Math.ceil(filteredTlds.value.length / perPage)));
const visible = computed(() => filteredTlds.value.slice((page.value - 1) * perPage, page.value * perPage));
watch([search, list], () => { page.value = 1; });

const money = (v) => (v === null || v === undefined || v === '' ? '—' : Number(v).toLocaleString('en-US', { minimumFractionDigits: 2 }));

const openCreateModal = () => {
    editingTld.value = null;
    Object.assign(form, blank());
    form.clearErrors();
    showModal.value = true;
};

const openEditModal = (tld) => {
    editingTld.value = tld;
    form.clearErrors();
    Object.assign(form, blank(), Object.fromEntries(Object.entries(tld).filter(([k]) => k in blank()).map(([k, v]) => [k, v ?? ''])));
    form.renew_prices = Object.assign({ 1: '', 2: '', 3: '', 5: '', 10: '' }, tld.renew_prices || {});
    form.is_active = Boolean(tld.is_active);
    showModal.value = true;
};

const payload = () => {
    const data = form.data();
    ['price_1yr', 'price_2yr', 'price_3yr', 'price_5yr', 'price_10yr', 'transfer_price', 'popular_order', 'featured_order', 'renew_order'].forEach((k) => {
        if (data[k] === '') data[k] = null;
    });
    return data;
};

const saveTld = () => {
    const options = { preserveScroll: true, onSuccess: () => { showModal.value = false; } };
    form.transform(payload);
    editingTld.value
        ? form.put(route('admin.domain-pricing.update', editingTld.value.id), options)
        : form.post(route('admin.domain-pricing.store'), options);
};

const togglePopular = (tld) => router.patch(route('admin.domain-pricing.toggle-popular', tld.id), {}, { preserveScroll: true });

const deleteTld = (tld) => {
    if (confirm(`Delete extension ${tld.tld}?`)) {
        router.delete(route('admin.domain-pricing.destroy', tld.id), { preserveScroll: true });
    }
};

const inputClass = 'w-full px-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-lg';
</script>

<template>
    <AdminLayout>
        <Head title="Domain Pricing" />

        <div class="space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-slate-800">Domain Pricing &amp; TLDs</h2>
                    <p class="text-sm text-slate-400">Prices in BDT. Positions control the popular table, the domain-search chips and the renewal table.</p>
                </div>
                <button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg shadow-sm self-start sm:self-auto" @click="openCreateModal">
                    <i class="fa-solid fa-plus mr-1"></i> Add extension
                </button>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 p-3 shadow-sm flex flex-col lg:flex-row lg:items-center justify-between gap-3">
                <div class="flex flex-wrap gap-1">
                    <button v-for="l in lists" :key="l[0]" :class="['px-3 py-1.5 text-xs font-semibold rounded-lg', list === l[0] ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-100']" @click="list = l[0]">{{ l[1] }}</button>
                </div>
                <div class="flex items-center gap-3">
                    <input v-model="search" type="text" placeholder="Search .com, .bd…" class="w-56 px-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-lg" />
                    <span class="text-xs text-slate-500 whitespace-nowrap"><strong class="text-slate-800">{{ filteredTlds.length }}</strong> extensions</span>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-600">
                        <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider text-[10px]">
                            <tr>
                                <th class="px-4 py-2.5">Extension</th>
                                <th v-for="t in terms" :key="t" class="px-3 py-2.5 text-right">{{ t }} yr</th>
                                <th class="px-3 py-2.5 text-right">Transfer</th>
                                <th class="px-3 py-2.5 text-center">Lists</th>
                                <th class="px-3 py-2.5 text-center">Status</th>
                                <th class="px-4 py-2.5"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="tld in visible" :key="tld.id" class="hover:bg-slate-50">
                                <td class="px-4 py-2.5 font-mono font-bold text-slate-800">{{ tld.tld }} <span v-if="tld.badge" class="ml-1 px-1.5 py-0.5 bg-amber-100 text-amber-700 rounded text-[10px] font-sans">{{ tld.badge }}</span></td>
                                <td v-for="t in terms" :key="t" class="px-3 py-2.5 text-right font-mono">{{ money(tld[`price_${t}yr`]) }}</td>
                                <td class="px-3 py-2.5 text-right font-mono">{{ money(tld.transfer_price) }}</td>
                                <td class="px-3 py-2.5 text-center whitespace-nowrap">
                                    <button :class="['px-1.5 py-0.5 rounded text-[10px] font-bold', tld.popular_order ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 text-slate-400']" title="Toggle popular table" @click="togglePopular(tld)">P{{ tld.popular_order || '' }}</button>
                                    <span v-if="tld.featured_order" class="ml-1 px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-700" title="Search chip">C{{ tld.featured_order }}</span>
                                    <span v-if="tld.renew_order" class="ml-1 px-1.5 py-0.5 rounded text-[10px] font-bold bg-purple-100 text-purple-700" title="Renewal table">R{{ tld.renew_order }}</span>
                                </td>
                                <td class="px-3 py-2.5 text-center">
                                    <span :class="['px-2 py-0.5 rounded-full text-[10px] font-bold', tld.is_active ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-100 text-slate-500']">{{ tld.is_active ? 'Active' : 'Hidden' }}</span>
                                </td>
                                <td class="px-4 py-2.5 text-right whitespace-nowrap">
                                    <button class="px-2.5 py-1 bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white rounded-lg font-semibold" @click="openEditModal(tld)">Edit</button>
                                    <button class="ml-1 p-1 text-rose-500 hover:text-rose-700" @click="deleteTld(tld)"><i class="fa-regular fa-trash-can"></i></button>
                                </td>
                            </tr>
                            <tr v-if="!visible.length"><td colspan="10" class="px-4 py-10 text-center text-slate-400">No extensions found.</td></tr>
                        </tbody>
                    </table>
                </div>
                <div v-if="pages > 1" class="flex items-center justify-between px-4 py-3 border-t border-slate-100 text-xs">
                    <button class="px-3 py-1.5 border border-slate-200 rounded-lg disabled:opacity-40" :disabled="page === 1" @click="page--">Previous</button>
                    <span class="text-slate-500">Page {{ page }} of {{ pages }}</span>
                    <button class="px-3 py-1.5 border border-slate-200 rounded-lg disabled:opacity-40" :disabled="page === pages" @click="page++">Next</button>
                </div>
            </div>
        </div>

        <div v-if="showModal" class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4 overflow-y-auto" @click.self="showModal = false">
            <form class="bg-white rounded-2xl max-w-2xl w-full p-6 shadow-2xl space-y-4 text-xs my-8" @submit.prevent="saveTld">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-base font-bold text-slate-800">{{ editingTld ? `Edit ${editingTld.tld}` : 'Add extension' }}</h3>
                    <button type="button" class="text-slate-400 hover:text-slate-600 text-lg" @click="showModal = false"><i class="fa-solid fa-xmark"></i></button>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <div class="col-span-2">
                        <label class="block font-semibold text-slate-600 mb-1">Extension *</label>
                        <input v-model="form.tld" type="text" required placeholder=".com" :class="[inputClass, 'font-mono']" />
                        <p v-if="form.errors.tld" class="text-rose-600 mt-1">{{ form.errors.tld }}</p>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-600 mb-1">Chip badge</label>
                        <input v-model="form.badge" type="text" placeholder="Hot" :class="inputClass" />
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-600 mb-1">Sort order</label>
                        <input v-model="form.sort_order" type="number" :class="inputClass" />
                    </div>
                </div>

                <div>
                    <p class="font-bold text-slate-500 uppercase tracking-wider text-[10px] mb-2">Registration price (BDT) — leave empty if a term isn't offered</p>
                    <div class="grid grid-cols-3 sm:grid-cols-6 gap-3">
                        <div v-for="t in terms" :key="t">
                            <label class="block font-semibold text-slate-600 mb-1">{{ t }} year{{ t > 1 ? 's' : '' }}</label>
                            <input v-model="form[`price_${t}yr`]" type="number" step="0.01" min="0" :class="inputClass" />
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-600 mb-1">Transfer</label>
                            <input v-model="form.transfer_price" type="number" step="0.01" min="0" :class="inputClass" />
                        </div>
                    </div>
                </div>

                <div>
                    <p class="font-bold text-slate-500 uppercase tracking-wider text-[10px] mb-2">Renewal price (BDT) — empty terms use the 1-year renewal × years</p>
                    <div class="grid grid-cols-3 sm:grid-cols-5 gap-3">
                        <div v-for="t in terms" :key="t">
                            <label class="block font-semibold text-slate-600 mb-1">{{ t }} year{{ t > 1 ? 's' : '' }}</label>
                            <input v-model="form.renew_prices[t]" type="number" step="0.01" min="0" :class="inputClass" />
                        </div>
                    </div>
                </div>

                <div>
                    <p class="font-bold text-slate-500 uppercase tracking-wider text-[10px] mb-2">Show in lists (position number; empty = not shown)</p>
                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block font-semibold text-slate-600 mb-1">Popular table</label>
                            <input v-model="form.popular_order" type="number" min="1" :class="inputClass" />
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-600 mb-1">Domain search chips</label>
                            <input v-model="form.featured_order" type="number" min="1" :class="inputClass" />
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-600 mb-1">Renewal table</label>
                            <input v-model="form.renew_order" type="number" min="1" :class="inputClass" />
                        </div>
                    </div>
                </div>

                <label class="inline-flex items-center gap-2 font-semibold text-slate-600">
                    <input v-model="form.is_active" type="checkbox" class="rounded border-slate-300 text-blue-600" /> Active on the website
                </label>

                <div class="flex justify-end gap-2 pt-4 border-t border-slate-100">
                    <button type="button" class="px-4 py-2 border border-slate-200 text-slate-600 rounded-lg font-semibold" @click="showModal = false">Cancel</button>
                    <button type="submit" :disabled="form.processing" class="px-5 py-2 bg-blue-600 text-white rounded-lg font-bold">Save extension</button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
