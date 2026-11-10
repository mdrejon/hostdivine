<script setup>
import { computed, ref, watch } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    pages: Array,
});

const search = ref('');
const showCreate = ref(false);

const filtered = computed(() => {
    const q = search.value.toLowerCase();
    return props.pages.filter((p) => !q || p.title.toLowerCase().includes(q) || p.path.includes(q));
});

const form = useForm({ title: '', slug: '', is_active: true });
const slugTouched = ref(false);

watch(() => form.title, (title) => {
    if (!slugTouched.value) {
        form.slug = title.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
    }
});

const create = () => {
    form.post(route('admin.pages.store'), { onSuccess: () => { showCreate.value = false; form.reset(); } });
};

const destroy = (page) => {
    if (confirm(`Delete "${page.title}" and all of its sections?`)) {
        router.delete(route('admin.pages.destroy', page.id), { preserveScroll: true });
    }
};
</script>

<template>
    <AdminLayout>
        <Head title="Pages" />

        <div class="space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-slate-800">Website Pages</h2>
                    <p class="text-sm text-slate-400">Every page is built from sections. Header, menus and footer are edited once in Site Layout.</p>
                </div>
                <button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg shadow-sm self-start sm:self-auto" @click="showCreate = true">
                    <i class="fa-solid fa-plus mr-1"></i> New Page
                </button>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
                <div class="p-3 border-b border-slate-100">
                    <input v-model="search" type="text" placeholder="Search pages…" class="w-full sm:w-72 px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg" />
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs">
                        <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider text-[10px]">
                            <tr>
                                <th class="text-left px-4 py-2.5">Page</th>
                                <th class="text-left px-4 py-2.5">URL</th>
                                <th class="text-center px-4 py-2.5">Sections</th>
                                <th class="text-center px-4 py-2.5">Status</th>
                                <th class="text-left px-4 py-2.5">Updated</th>
                                <th class="px-4 py-2.5"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="page in filtered" :key="page.id" class="hover:bg-slate-50">
                                <td class="px-4 py-3 font-semibold text-slate-800">
                                    <Link :href="route('admin.pages.edit', page.id)" class="hover:text-blue-600">{{ page.title }}</Link>
                                </td>
                                <td class="px-4 py-3"><a :href="page.url" target="_blank" class="font-mono text-slate-500 hover:text-blue-600">{{ page.path }}</a></td>
                                <td class="px-4 py-3 text-center text-slate-600">{{ page.sections_count }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span :class="['px-2 py-0.5 rounded-full text-[10px] font-bold', page.is_active ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-100 text-slate-500']">{{ page.is_active ? 'Published' : 'Draft' }}</span>
                                </td>
                                <td class="px-4 py-3 text-slate-400">{{ page.updated_at }}</td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    <Link :href="route('admin.pages.edit', page.id)" class="px-2.5 py-1 bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white rounded-lg font-semibold">Edit</Link>
                                    <button v-if="page.slug !== 'home'" class="ml-1 p-1 text-rose-500 hover:text-rose-700" title="Delete" @click="destroy(page)"><i class="fa-regular fa-trash-can"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div v-if="showCreate" class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4" @click.self="showCreate = false">
            <form class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4 text-xs" @submit.prevent="create">
                <h3 class="text-base font-bold text-slate-800">New page</h3>
                <div>
                    <label class="block font-semibold text-slate-600 mb-1">Title *</label>
                    <input v-model="form.title" type="text" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg" />
                    <p v-if="form.errors.title" class="text-rose-600 mt-1">{{ form.errors.title }}</p>
                </div>
                <div>
                    <label class="block font-semibold text-slate-600 mb-1">URL *</label>
                    <div class="flex items-center">
                        <span class="px-2 py-2 bg-slate-100 border border-r-0 border-slate-200 rounded-l-lg text-slate-500 font-mono">/</span>
                        <input v-model="form.slug" type="text" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-r-lg font-mono" @input="slugTouched = true" />
                    </div>
                    <p v-if="form.errors.slug" class="text-rose-600 mt-1">{{ form.errors.slug }}</p>
                </div>
                <label class="inline-flex items-center gap-2 font-semibold text-slate-600">
                    <input v-model="form.is_active" type="checkbox" class="rounded border-slate-300 text-blue-600" /> Publish immediately
                </label>
                <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button" class="px-4 py-2 border border-slate-200 rounded-lg font-semibold text-slate-600" @click="showCreate = false">Cancel</button>
                    <button type="submit" :disabled="form.processing" class="px-5 py-2 bg-blue-600 text-white rounded-lg font-bold">Create page</button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
