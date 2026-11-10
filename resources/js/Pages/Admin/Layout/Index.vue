<script setup>
import { reactive, ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import SchemaForm from '@/Components/Cms/SchemaForm.vue';
import RepeaterField from '@/Components/Cms/RepeaterField.vue';

/**
 * Header, menus, footer and site-wide widgets. Each tab is saved on its own.
 */
const props = defineProps({
    definitions: Object,
    values: Object,
});

const keys = Object.keys(props.definitions);
const active = ref(keys[0]);
const saving = ref(false);

// List settings (menus, social links) are edited as a single repeater.
const state = reactive(Object.fromEntries(keys.map((key) => [
    key,
    props.definitions[key].list
        ? { items: JSON.parse(JSON.stringify(props.values[key] || [])) }
        : JSON.parse(JSON.stringify(props.values[key] || {})),
])));

const listField = (key) => ({
    key: 'items',
    label: props.definitions[key].label,
    item_label: props.definitions[key].item_label,
    fields: props.definitions[key].fields,
});

const save = () => {
    const key = active.value;
    const value = props.definitions[key].list ? state[key].items : state[key];
    saving.value = true;
    router.put(route('admin.layout.update', key), { value }, {
        preserveScroll: true,
        preserveState: true,
        onFinish: () => { saving.value = false; },
    });
};
</script>

<template>
    <AdminLayout>
        <Head title="Site Layout" />

        <div class="space-y-6">
            <div>
                <h2 class="text-2xl font-bold text-slate-800">Site Layout</h2>
                <p class="text-sm text-slate-400">Header, menus, footer and shared widgets. Edit once — every page updates.</p>
            </div>

            <div class="flex flex-col lg:flex-row gap-6">
                <nav class="lg:w-60 shrink-0 bg-white border border-slate-200 rounded-xl p-2 h-fit flex lg:flex-col gap-1 overflow-x-auto">
                    <button
                        v-for="key in keys"
                        :key="key"
                        :class="['flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-semibold whitespace-nowrap text-left', active === key ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-100']"
                        @click="active = key"
                    >
                        <i :class="[definitions[key].icon, 'w-4 text-center']"></i>{{ definitions[key].label }}
                    </button>
                </nav>

                <div class="flex-1 min-w-0 bg-white border border-slate-200 rounded-xl shadow-sm">
                    <div class="px-5 py-4 border-b border-slate-100">
                        <h3 class="text-base font-bold text-slate-800">{{ definitions[active].label }}</h3>
                        <p class="text-xs text-slate-400">{{ definitions[active].description }}</p>
                    </div>
                    <form class="p-5" @submit.prevent="save">
                        <RepeaterField v-if="definitions[active].list" :key="active" :field="listField(active)" :model="state[active]" />
                        <SchemaForm v-else :key="active" :fields="definitions[active].fields" :model-value="state[active]" />
                        <div class="flex justify-end pt-5 mt-5 border-t border-slate-100">
                            <button type="submit" :disabled="saving" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg shadow-sm disabled:opacity-60">
                                <i v-if="saving" class="fa-solid fa-spinner fa-spin mr-1"></i> Save {{ definitions[active].label }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
