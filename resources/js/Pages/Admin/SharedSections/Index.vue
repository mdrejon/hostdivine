<script setup>
import { ref } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import SectionEditor from '@/Components/Cms/SectionEditor.vue';

const props = defineProps({
    sections: Array,
    types: Object,
});

const editing = ref(null);
const showCreate = ref(false);
const form = useForm({ type: 'cta_banner', name: '' });

const typeOf = (key) => props.types[key] || { label: key, icon: 'fa-solid fa-square', fields: [], description: '' };

const create = () => form.post(route('admin.shared-sections.store'), { onSuccess: () => { showCreate.value = false; form.reset(); } });

const toggle = (section) => router.patch(route('admin.sections.toggle', section.id), {}, { preserveScroll: true });

const remove = (section) => {
    const where = section.pages.length ? ` It will be removed from: ${section.pages.map((p) => p.title).join(', ')}.` : '';
    if (confirm(`Delete shared section "${section.name}"?${where}`)) {
        router.delete(route('admin.sections.destroy', section.id), { preserveScroll: true });
    }
};
</script>

<template>
    <AdminLayout>
        <Head title="Shared Sections" />

        <div class="space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-slate-800">Shared Sections</h2>
                    <p class="text-sm text-slate-400">Common blocks edited in one place and shown on many pages. Add them from a page's "Add section" dialog.</p>
                </div>
                <button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg shadow-sm self-start sm:self-auto" @click="showCreate = true">
                    <i class="fa-solid fa-plus mr-1"></i> New shared section
                </button>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <div v-for="section in sections" :key="section.id" :class="['bg-white rounded-xl border shadow-sm p-4 space-y-3', section.is_active ? 'border-slate-200' : 'border-dashed border-slate-300 opacity-70']">
                    <div class="flex items-start gap-3">
                        <span class="w-10 h-10 shrink-0 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center"><i :class="typeOf(section.type).icon"></i></span>
                        <div class="flex-1 min-w-0">
                            <div class="font-bold text-sm text-slate-800 truncate">{{ section.name }}</div>
                            <div class="text-xs text-slate-400">{{ typeOf(section.type).label }}</div>
                        </div>
                        <div class="flex items-center gap-1 text-xs">
                            <button class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold" @click="editing = section"><i class="fa-solid fa-pen mr-1"></i>Edit</button>
                            <button class="p-2 text-slate-400 hover:text-slate-700" :title="section.is_active ? 'Hide everywhere' : 'Show'" @click="toggle(section)"><i :class="section.is_active ? 'fa-regular fa-eye' : 'fa-regular fa-eye-slash'"></i></button>
                            <button class="p-2 text-slate-400 hover:text-rose-600" title="Delete" @click="remove(section)"><i class="fa-regular fa-trash-can"></i></button>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-1 text-[11px]">
                        <span class="text-slate-400 mr-1">Used on:</span>
                        <Link v-for="page in section.pages" :key="page.id" :href="route('admin.pages.edit', page.id)" class="px-2 py-0.5 bg-slate-100 hover:bg-blue-50 hover:text-blue-600 rounded-full text-slate-600">{{ page.title }}</Link>
                        <span v-if="!section.pages.length" class="text-slate-400 italic">not used yet</span>
                    </div>
                </div>
                <div v-if="!sections.length" class="lg:col-span-2 bg-white rounded-xl border border-slate-200 p-10 text-center text-xs text-slate-400">
                    No shared sections yet. Use "Make shared" on any page section, or create one here.
                </div>
            </div>
        </div>

        <div v-if="showCreate" class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4" @click.self="showCreate = false">
            <form class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4 text-xs" @submit.prevent="create">
                <h3 class="text-base font-bold text-slate-800">New shared section</h3>
                <div>
                    <label class="block font-semibold text-slate-600 mb-1">Name *</label>
                    <input v-model="form.name" type="text" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg" />
                </div>
                <div>
                    <label class="block font-semibold text-slate-600 mb-1">Type *</label>
                    <select v-model="form.type" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg">
                        <option v-for="(type, key) in types" :key="key" :value="key">{{ type.label }}</option>
                    </select>
                </div>
                <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button" class="px-4 py-2 border border-slate-200 rounded-lg font-semibold text-slate-600" @click="showCreate = false">Cancel</button>
                    <button type="submit" :disabled="form.processing" class="px-5 py-2 bg-blue-600 text-white rounded-lg font-bold">Create</button>
                </div>
            </form>
        </div>

        <SectionEditor
            v-if="editing"
            :key="editing.id"
            :section="{ id: editing.id, name: editing.name, data: editing.data, shared: null }"
            :type="typeOf(editing.type)"
            @close="editing = null"
        />
    </AdminLayout>
</template>
