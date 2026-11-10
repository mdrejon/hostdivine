<script setup>
import { reactive, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import SchemaForm from './SchemaForm.vue';

/**
 * Slide-over editor for one section. For a section linked to a shared
 * section, the shared content is edited (and changes every page using it).
 */
const props = defineProps({
    section: { type: Object, required: true },
    type: { type: Object, required: true },
    previewUrl: { type: String, default: '' },
});

const emit = defineEmits(['close']);

const source = props.section.shared ?? props.section;
const name = ref(source.name || props.type.label);
const data = reactive(JSON.parse(JSON.stringify(source.data || {})));
const saving = ref(false);

const save = () => {
    saving.value = true;
    router.put(route('admin.sections.update', props.section.id), { name: name.value, data }, {
        preserveScroll: true,
        onSuccess: () => emit('close'),
        onFinish: () => { saving.value = false; },
    });
};
</script>

<template>
    <div class="fixed inset-0 z-50 flex justify-end bg-slate-900/40" @click.self="emit('close')">
        <div class="w-full max-w-3xl h-full bg-slate-50 shadow-2xl flex flex-col">
            <div class="flex items-start justify-between gap-4 px-5 py-4 bg-white border-b border-slate-200">
                <div class="min-w-0">
                    <div class="flex items-center gap-2 text-[11px] font-bold uppercase tracking-wider text-blue-600">
                        <i :class="type.icon"></i> {{ type.label }}
                        <span v-if="section.shared" class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 normal-case tracking-normal">
                            Shared · used on {{ section.shared.usages_count }} page(s)
                        </span>
                    </div>
                    <input v-model="name" type="text" class="mt-1 w-full text-base font-bold text-slate-800 bg-transparent border-0 p-0 focus:ring-0" placeholder="Section name (admin only)" />
                    <p class="text-xs text-slate-400">{{ type.description }}</p>
                </div>
                <button type="button" class="p-2 text-slate-400 hover:text-slate-700" @click="emit('close')"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>

            <form class="flex-1 overflow-y-auto p-5" @submit.prevent="save">
                <SchemaForm :fields="type.fields" :model-value="data" />
                <button type="submit" class="hidden"></button>
            </form>

            <div class="flex items-center justify-between gap-3 px-5 py-3 bg-white border-t border-slate-200">
                <a v-if="previewUrl" :href="previewUrl" target="_blank" class="text-xs font-semibold text-slate-500 hover:text-blue-600">
                    <i class="fa-solid fa-arrow-up-right-from-square mr-1"></i> View page
                </a>
                <span v-else></span>
                <div class="flex gap-2">
                    <button type="button" class="px-4 py-2 text-xs font-semibold border border-slate-200 rounded-lg text-slate-600" @click="emit('close')">Cancel</button>
                    <button type="button" :disabled="saving" class="px-5 py-2 text-xs font-bold bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow-sm disabled:opacity-60" @click="save">
                        <i v-if="saving" class="fa-solid fa-spinner fa-spin mr-1"></i> Save section
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
