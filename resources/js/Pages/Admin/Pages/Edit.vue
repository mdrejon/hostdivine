<script setup>
import { computed, ref } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import SectionEditor from '@/Components/Cms/SectionEditor.vue';

const props = defineProps({
    page: Object,
    sections: Array,
    types: Object,
    library: Array,
});

const tab = ref('sections');
const editing = ref(null);
const adding = ref(null); // { afterId }
const addSearch = ref('');

const form = useForm({
    title: props.page.title,
    slug: props.page.slug,
    hero_background: props.page.hero_background || '',
    meta_title: props.page.meta_title || '',
    meta_description: props.page.meta_description || '',
    meta_keywords: props.page.meta_keywords || '',
    og_image: props.page.og_image || '',
    is_active: Boolean(props.page.is_active),
});

const saveSettings = () => form.put(route('admin.pages.update', props.page.id), { preserveScroll: true });

const typeOf = (section) => props.types[section.type] || { label: section.type, icon: 'fa-solid fa-square', fields: [], description: '' };

const summary = (section) => {
    const data = section.shared ? section.shared.data : section.data;
    return data?.title || data?.eyebrow || data?.badge || (data?.slides?.[0]?.title) || '';
};

const filteredTypes = computed(() => {
    const q = addSearch.value.toLowerCase();
    return Object.entries(props.types).filter(([key, type]) => !q || type.label.toLowerCase().includes(q) || type.description.toLowerCase().includes(q));
});

const opts = { preserveScroll: true };

const move = (index, step) => {
    const ids = props.sections.map((s) => s.id);
    const target = index + step;
    if (target < 0 || target >= ids.length) return;
    [ids[index], ids[target]] = [ids[target], ids[index]];
    router.post(route('admin.sections.reorder', props.page.id), { ids }, opts);
};

const addType = (type) => {
    router.post(route('admin.sections.store', props.page.id), { type, after_id: adding.value?.afterId }, { ...opts, onSuccess: () => { adding.value = null; } });
};

const addShared = (sharedId) => {
    router.post(route('admin.sections.store', props.page.id), { shared_id: sharedId, after_id: adding.value?.afterId }, { ...opts, onSuccess: () => { adding.value = null; } });
};

const toggle = (section) => router.patch(route('admin.sections.toggle', section.id), {}, opts);
const duplicate = (section) => router.post(route('admin.sections.duplicate', section.id), {}, opts);
const share = (section) => {
    if (confirm('Move this section to the shared library? You can then add it to other pages and edit it in one place.')) {
        router.post(route('admin.sections.share', section.id), {}, opts);
    }
};
const detach = (section) => {
    if (confirm('Make a page-only copy of this shared section? Later edits will no longer affect other pages.')) {
        router.post(route('admin.sections.detach', section.id), {}, opts);
    }
};
const remove = (section) => {
    if (confirm(section.shared ? 'Remove this shared section from this page? (It stays in the library.)' : `Delete section "${section.name}"?`)) {
        router.delete(route('admin.sections.destroy', section.id), opts);
    }
};

const inputClass = 'w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg';
</script>

<template>
    <AdminLayout>
        <Head :title="`Edit ${page.title}`" />

        <div class="space-y-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <Link :href="route('admin.pages.index')" class="text-xs font-semibold text-slate-400 hover:text-blue-600"><i class="fa-solid fa-arrow-left mr-1"></i> All pages</Link>
                    <h2 class="text-2xl font-bold text-slate-800">{{ page.title }}</h2>
                    <a :href="page.url" target="_blank" class="text-xs font-mono text-blue-600 hover:underline">{{ page.url }} <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i></a>
                </div>
                <div class="flex bg-white border border-slate-200 rounded-lg p-1 self-start">
                    <button v-for="t in [['sections', 'Sections'], ['settings', 'Page settings & SEO']]" :key="t[0]" :class="['px-4 py-1.5 text-xs font-semibold rounded-md', tab === t[0] ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-100']" @click="tab = t[0]">{{ t[1] }}</button>
                </div>
            </div>

            <!-- Sections -->
            <div v-if="tab === 'sections'" class="space-y-2">
                <div v-for="(section, index) in sections" :key="section.id">
                    <div :class="['bg-white rounded-xl border shadow-sm flex flex-col sm:flex-row sm:items-center gap-3 p-3', section.is_active ? 'border-slate-200' : 'border-dashed border-slate-300 opacity-60']">
                        <div class="flex sm:flex-col gap-1 shrink-0">
                            <button class="w-7 h-7 rounded-md text-slate-400 hover:bg-slate-100 hover:text-slate-700 disabled:opacity-30" :disabled="index === 0" title="Move up" @click="move(index, -1)"><i class="fa-solid fa-chevron-up"></i></button>
                            <button class="w-7 h-7 rounded-md text-slate-400 hover:bg-slate-100 hover:text-slate-700 disabled:opacity-30" :disabled="index === sections.length - 1" title="Move down" @click="move(index, 1)"><i class="fa-solid fa-chevron-down"></i></button>
                        </div>
                        <div class="w-10 h-10 shrink-0 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center"><i :class="typeOf(section).icon"></i></div>
                        <button class="flex-1 min-w-0 text-left" @click="editing = section">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="font-bold text-sm text-slate-800 truncate">{{ section.name || typeOf(section).label }}</span>
                                <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-500 text-[10px] font-semibold">{{ typeOf(section).label }}</span>
                                <span v-if="section.shared" class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 text-[10px] font-semibold"><i class="fa-solid fa-link mr-1"></i>Shared · {{ section.shared.usages_count }} page(s)</span>
                                <span v-if="!section.is_active" class="px-2 py-0.5 rounded-full bg-slate-200 text-slate-600 text-[10px] font-semibold">Hidden</span>
                            </div>
                            <p class="text-xs text-slate-400 truncate">{{ summary(section) }}</p>
                        </button>
                        <div class="flex items-center gap-1 shrink-0 text-xs">
                            <button class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold" @click="editing = section"><i class="fa-solid fa-pen mr-1"></i>Edit</button>
                            <button class="p-2 text-slate-400 hover:text-slate-700" :title="section.is_active ? 'Hide' : 'Show'" @click="toggle(section)"><i :class="section.is_active ? 'fa-regular fa-eye' : 'fa-regular fa-eye-slash'"></i></button>
                            <button class="p-2 text-slate-400 hover:text-slate-700" title="Duplicate" @click="duplicate(section)"><i class="fa-regular fa-copy"></i></button>
                            <button v-if="!section.shared" class="p-2 text-slate-400 hover:text-amber-600" title="Make shared (reuse on other pages)" @click="share(section)"><i class="fa-solid fa-share-nodes"></i></button>
                            <button v-else class="p-2 text-slate-400 hover:text-amber-600" title="Detach (page-only copy)" @click="detach(section)"><i class="fa-solid fa-link-slash"></i></button>
                            <button class="p-2 text-slate-400 hover:text-rose-600" title="Delete" @click="remove(section)"><i class="fa-regular fa-trash-can"></i></button>
                        </div>
                    </div>
                    <div class="flex justify-center -my-0.5">
                        <button class="relative z-10 my-1 px-3 py-0.5 text-[11px] font-semibold text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-full" @click="adding = { afterId: section.id }"><i class="fa-solid fa-plus mr-1"></i>Insert section here</button>
                    </div>
                </div>

                <button class="w-full py-4 border-2 border-dashed border-slate-300 rounded-xl text-sm font-semibold text-blue-600 hover:bg-blue-50" @click="adding = { afterId: null }">
                    <i class="fa-solid fa-plus mr-1"></i> Add section
                </button>
            </div>

            <!-- Settings & SEO -->
            <form v-else class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 grid grid-cols-1 md:grid-cols-2 gap-4 text-xs" @submit.prevent="saveSettings">
                <div>
                    <label class="block font-semibold text-slate-600 mb-1">Page title (admin & breadcrumb)</label>
                    <input v-model="form.title" type="text" :class="inputClass" />
                    <p v-if="form.errors.title" class="text-rose-600 mt-1">{{ form.errors.title }}</p>
                </div>
                <div>
                    <label class="block font-semibold text-slate-600 mb-1">URL</label>
                    <div class="flex items-center">
                        <span class="px-2 py-2 bg-slate-100 border border-r-0 border-slate-200 rounded-l-lg text-slate-500 font-mono">/</span>
                        <input v-model="form.slug" type="text" :disabled="page.is_home" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-r-lg font-mono disabled:opacity-60" />
                    </div>
                    <p v-if="form.errors.slug" class="text-rose-600 mt-1">{{ form.errors.slug }}</p>
                </div>
                <div>
                    <label class="block font-semibold text-slate-600 mb-1">Hero background image</label>
                    <select v-model="form.hero_background" :class="inputClass">
                        <option value="">Default (header-home-2)</option>
                        <option value="1">Style 1 (header-home)</option>
                        <option value="2">Style 2 (header-home-1)</option>
                        <option value="3">Style 3 (header-home-2)</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <label class="inline-flex items-center gap-2 font-semibold text-slate-600 py-2">
                        <input v-model="form.is_active" type="checkbox" :disabled="page.is_home" class="rounded border-slate-300 text-blue-600" /> Published
                    </label>
                </div>
                <div class="md:col-span-2 border-t border-slate-100 pt-4 font-bold uppercase tracking-wider text-[10px] text-slate-400">Search engines</div>
                <div class="md:col-span-2">
                    <label class="block font-semibold text-slate-600 mb-1">Meta title <span class="font-normal text-slate-400">({{ form.meta_title.length }}/60)</span></label>
                    <input v-model="form.meta_title" type="text" :class="inputClass" />
                </div>
                <div class="md:col-span-2">
                    <label class="block font-semibold text-slate-600 mb-1">Meta description <span class="font-normal text-slate-400">({{ form.meta_description.length }}/160)</span></label>
                    <textarea v-model="form.meta_description" rows="3" :class="inputClass"></textarea>
                </div>
                <div>
                    <label class="block font-semibold text-slate-600 mb-1">Keywords</label>
                    <input v-model="form.meta_keywords" type="text" :class="inputClass" />
                </div>
                <div>
                    <label class="block font-semibold text-slate-600 mb-1">Social share image</label>
                    <input v-model="form.og_image" type="text" placeholder="assets/img/header-home.webp" :class="inputClass" />
                </div>
                <div class="md:col-span-2 flex justify-end pt-2 border-t border-slate-100">
                    <button type="submit" :disabled="form.processing" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-bold">Save settings</button>
                </div>
            </form>
        </div>

        <!-- Add section -->
        <div v-if="adding" class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4" @click.self="adding = null">
            <div class="bg-white rounded-2xl max-w-4xl w-full max-h-[85vh] flex flex-col shadow-2xl">
                <div class="flex items-center justify-between gap-3 p-5 border-b border-slate-100">
                    <h3 class="text-base font-bold text-slate-800">Add a section</h3>
                    <input v-model="addSearch" type="text" placeholder="Search section types…" class="flex-1 max-w-xs px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg" />
                    <button class="p-2 text-slate-400 hover:text-slate-700" @click="adding = null"><i class="fa-solid fa-xmark text-lg"></i></button>
                </div>
                <div class="overflow-y-auto p-5 space-y-6">
                    <div v-if="library.length">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-amber-600 mb-2"><i class="fa-solid fa-link mr-1"></i>Shared sections (edit once, show everywhere)</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                            <button v-for="item in library" :key="item.id" class="text-left p-3 border border-amber-200 bg-amber-50/50 hover:bg-amber-50 rounded-xl" @click="addShared(item.id)">
                                <div class="font-semibold text-sm text-slate-800">{{ item.name }}</div>
                                <div class="text-xs text-slate-500">{{ types[item.type]?.label }}</div>
                            </button>
                        </div>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-2">New section</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                            <button v-for="[key, type] in filteredTypes" :key="key" class="text-left p-3 border border-slate-200 hover:border-blue-400 hover:bg-blue-50/50 rounded-xl flex gap-3" @click="addType(key)">
                                <span class="w-9 h-9 shrink-0 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center"><i :class="type.icon"></i></span>
                                <span>
                                    <span class="block font-semibold text-sm text-slate-800">{{ type.label }}</span>
                                    <span class="block text-xs text-slate-500">{{ type.description }}</span>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <SectionEditor v-if="editing" :key="editing.id" :section="editing" :type="typeOf(editing)" :preview-url="page.url" @close="editing = null" />
    </AdminLayout>
</template>
