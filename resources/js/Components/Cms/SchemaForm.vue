<script setup>
import { computed, ref } from 'vue';
import SchemaField from './SchemaField.vue';

/**
 * Renders a form from a field schema (config/cms.php). Fields with a
 * "group" are shown in collapsible panels; the model is mutated in place.
 */
const props = defineProps({
    fields: { type: Array, required: true },
    modelValue: { type: Object, required: true },
    grouped: { type: Boolean, default: true },
});

const groups = computed(() => {
    if (!props.grouped) {
        return [{ name: null, fields: props.fields }];
    }
    const map = new Map();
    props.fields.forEach((field) => {
        const name = field.group || 'Content';
        if (!map.has(name)) map.set(name, []);
        map.get(name).push(field);
    });
    return [...map.entries()].map(([name, fields]) => ({ name, fields }));
});

const closed = ref({});
const toggle = (name) => { closed.value[name] = !closed.value[name]; };

const widthClass = (field) => ({
    half: 'sm:col-span-3',
    third: 'sm:col-span-2',
}[field.width] || 'sm:col-span-6');
</script>

<template>
    <div class="space-y-4">
        <fieldset v-for="group in groups" :key="group.name ?? 'all'" :class="group.name ? 'border border-slate-200 rounded-xl bg-white' : ''">
            <legend v-if="group.name" class="sr-only">{{ group.name }}</legend>
            <button
                v-if="group.name && groups.length > 1"
                type="button"
                class="w-full flex items-center justify-between px-4 py-2.5 text-xs font-bold uppercase tracking-wider text-slate-500 hover:text-slate-700"
                @click="toggle(group.name)"
            >
                <span>{{ group.name }}</span>
                <i :class="['fa-solid text-[10px]', closed[group.name] ? 'fa-chevron-right' : 'fa-chevron-down']"></i>
            </button>
            <div v-show="!closed[group.name]" :class="['grid grid-cols-1 sm:grid-cols-6 gap-3', group.name && groups.length > 1 ? 'px-4 pb-4' : (group.name ? 'p-4' : '')]">
                <div v-for="field in group.fields" :key="field.key" :class="widthClass(field)">
                    <SchemaField :field="field" :model="modelValue" />
                </div>
            </div>
        </fieldset>
    </div>
</template>
