<script setup>
import { computed, ref } from 'vue';
import SchemaForm from './SchemaForm.vue';

/**
 * A sortable list of objects (plans, links, menu items…). Items can be
 * added, duplicated, removed and moved up or down.
 */
const props = defineProps({
    field: { type: Object, required: true },
    model: { type: Object, required: true },
});

const items = computed(() => {
    if (!Array.isArray(props.model[props.field.key])) {
        props.model[props.field.key] = [];
    }
    return props.model[props.field.key];
});

const open = ref(new Set());
const isOpen = (index) => open.value.has(index);
const toggle = (index) => {
    const next = new Set(open.value);
    next.has(index) ? next.delete(index) : next.add(index);
    open.value = next;
};

const title = (item, index) => {
    const key = props.field.item_label;
    const label = key && item ? item[key] : '';
    return label ? String(label).replace(/<[^>]+>/g, '') : `Item ${index + 1}`;
};

const add = () => {
    items.value.push({});
    toggle(items.value.length - 1);
};

const move = (index, step) => {
    const target = index + step;
    if (target < 0 || target >= items.value.length) return;
    const list = items.value;
    [list[index], list[target]] = [list[target], list[index]];
    open.value = new Set([...open.value].map((i) => (i === index ? target : i === target ? index : i)));
};

const duplicate = (index) => {
    items.value.splice(index + 1, 0, JSON.parse(JSON.stringify(items.value[index])));
};

const remove = (index) => {
    if (!confirm(`Remove "${title(items.value[index], index)}"?`)) return;
    items.value.splice(index, 1);
    open.value = new Set([...open.value].filter((i) => i !== index).map((i) => (i > index ? i - 1 : i)));
};
</script>

<template>
    <div class="space-y-2">
        <div v-for="(item, index) in items" :key="index" class="border border-slate-200 rounded-lg bg-slate-50/60">
            <div class="flex items-center gap-1 px-2 py-1.5">
                <button type="button" class="flex-1 min-w-0 flex items-center gap-2 text-left font-semibold text-slate-700 py-1" @click="toggle(index)">
                    <i :class="['fa-solid text-[10px] text-slate-400', isOpen(index) ? 'fa-chevron-down' : 'fa-chevron-right']"></i>
                    <span class="truncate">{{ title(item, index) }}</span>
                </button>
                <button type="button" class="p-1.5 text-slate-400 hover:text-slate-700 disabled:opacity-30" :disabled="index === 0" title="Move up" @click="move(index, -1)"><i class="fa-solid fa-arrow-up"></i></button>
                <button type="button" class="p-1.5 text-slate-400 hover:text-slate-700 disabled:opacity-30" :disabled="index === items.length - 1" title="Move down" @click="move(index, 1)"><i class="fa-solid fa-arrow-down"></i></button>
                <button type="button" class="p-1.5 text-slate-400 hover:text-blue-600" title="Duplicate" @click="duplicate(index)"><i class="fa-regular fa-copy"></i></button>
                <button type="button" class="p-1.5 text-slate-400 hover:text-rose-600" title="Remove" @click="remove(index)"><i class="fa-regular fa-trash-can"></i></button>
            </div>
            <div v-if="isOpen(index)" class="px-3 pb-3 pt-1 border-t border-slate-200 bg-white rounded-b-lg">
                <SchemaForm :fields="field.fields" :model-value="item" :grouped="false" />
            </div>
        </div>
        <button type="button" class="w-full py-2 border border-dashed border-slate-300 rounded-lg text-blue-600 font-semibold hover:bg-blue-50" @click="add">
            <i class="fa-solid fa-plus mr-1"></i> Add {{ field.label.toLowerCase().replace(/s$/, '') }}
        </button>
    </div>
</template>
