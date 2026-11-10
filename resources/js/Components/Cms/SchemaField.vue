<script setup>
import { computed, ref } from 'vue';
import RepeaterField from './RepeaterField.vue';

/**
 * One schema field bound to model[field.key].
 */
const props = defineProps({
    field: { type: Object, required: true },
    model: { type: Object, required: true },
});

const inputClass = 'w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white';

const value = computed({
    get: () => props.model[props.field.key],
    set: (v) => { props.model[props.field.key] = v; },
});

const selectValue = computed({
    get: () => (value.value === undefined || value.value === null ? '' : String(value.value)),
    set: (v) => { value.value = v; },
});

// "list" fields are edited as one item per line.
const listText = computed({
    get: () => (Array.isArray(value.value) ? value.value.join('\n') : ''),
    set: (text) => { value.value = text.split('\n'); },
});

const uploading = ref(false);
const uploadError = ref('');

const csrf = () => decodeURIComponent((document.cookie.match(/XSRF-TOKEN=([^;]+)/) || [])[1] || '');

const upload = async (event) => {
    const file = event.target.files?.[0];
    if (!file) return;
    uploading.value = true;
    uploadError.value = '';
    const body = new FormData();
    body.append('file', file);
    try {
        const response = await fetch(route('admin.uploads.store'), {
            method: 'POST',
            body,
            headers: { Accept: 'application/json', 'X-XSRF-TOKEN': csrf() },
            credentials: 'same-origin',
        });
        const json = await response.json();
        if (!response.ok) throw new Error(json.message || 'Upload failed');
        value.value = json.path;
    } catch (error) {
        uploadError.value = error.message;
    } finally {
        uploading.value = false;
        event.target.value = '';
    }
};

const preview = computed(() => {
    const v = value.value || '';
    if (!v) return '';
    return /^(https?:)?\/\//.test(v) ? v : '/' + v.replace(/^\/+/, '');
});
</script>

<template>
    <div class="text-xs">
        <label v-if="field.type !== 'toggle'" class="block font-semibold text-slate-600 mb-1">{{ field.label }}</label>

        <input v-if="['text', 'url'].includes(field.type)" v-model="value" type="text" :class="inputClass" :placeholder="field.type === 'url' ? '/page, #anchor or https://…' : ''" />

        <input v-else-if="field.type === 'number'" v-model.number="value" type="number" :class="inputClass" />

        <textarea v-else-if="field.type === 'textarea'" v-model="value" rows="3" :class="inputClass"></textarea>

        <textarea v-else-if="field.type === 'rich'" v-model="value" rows="2" :class="[inputClass, 'font-mono text-[11px]']" placeholder="Text, links allowed: <a href=&quot;/page&quot;>…</a>"></textarea>

        <textarea v-else-if="field.type === 'html'" v-model="value" rows="6" :class="[inputClass, 'font-mono text-[11px]']"></textarea>

        <textarea v-else-if="field.type === 'list'" v-model="listText" rows="4" :class="inputClass" placeholder="One item per line"></textarea>

        <select v-else-if="field.type === 'select'" v-model="selectValue" :class="inputClass">
            <option v-for="(label, key) in field.options" :key="key" :value="String(key)">{{ label }}</option>
        </select>

        <label v-else-if="field.type === 'toggle'" class="inline-flex items-center gap-2 cursor-pointer select-none font-semibold text-slate-600 py-1">
            <input v-model="value" type="checkbox" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500" />
            <span>{{ field.label }}</span>
        </label>

        <div v-else-if="field.type === 'icon'" class="flex items-center gap-2">
            <span class="w-9 h-9 shrink-0 rounded-lg bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-600">
                <i :class="value || 'fa-regular fa-square'"></i>
            </span>
            <input v-model="value" type="text" :class="inputClass" placeholder="fa-solid fa-check" />
        </div>

        <div v-else-if="field.type === 'image'" class="flex items-center gap-2">
            <span class="w-14 h-9 shrink-0 rounded-lg bg-slate-100 border border-slate-200 overflow-hidden flex items-center justify-center">
                <img v-if="preview" :src="preview" alt="" class="max-w-full max-h-full object-contain" />
                <i v-else class="fa-regular fa-image text-slate-400"></i>
            </span>
            <input v-model="value" type="text" :class="inputClass" placeholder="assets/img/… or upload" />
            <label class="shrink-0 px-3 py-2 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-lg font-semibold cursor-pointer">
                <i :class="uploading ? 'fa-solid fa-spinner fa-spin' : 'fa-solid fa-upload'"></i>
                <input type="file" accept="image/*" class="sr-only" @change="upload" />
            </label>
        </div>

        <RepeaterField v-else-if="field.type === 'repeater'" :field="field" :model="model" />

        <p v-if="uploadError" class="mt-1 text-rose-600">{{ uploadError }}</p>
        <p v-if="field.help" class="mt-1 text-[11px] text-slate-400">{{ field.help }}</p>
    </div>
</template>
