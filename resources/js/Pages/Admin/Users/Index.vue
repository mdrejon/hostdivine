<script setup>
import { ref } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    users: Array,
});

const showModal = ref(false);
const editingUser = ref(null);

const form = useForm({
    id: null,
    name: '',
    email: '',
    role: 'admin',
    password: '',
    is_active: true,
});

const openCreateModal = () => {
    editingUser.value = null;
    form.reset();
    showModal.value = true;
};

const openEditModal = (u) => {
    editingUser.value = u;
    form.id = u.id;
    form.name = u.name;
    form.email = u.email;
    form.role = u.role || 'admin';
    form.password = '';
    form.is_active = Boolean(u.is_active);
    showModal.value = true;
};

const saveUser = () => {
    if (editingUser.value) {
        router.put(route('admin.users.update', editingUser.value.id), form.data(), {
            onSuccess: () => { showModal.value = false; }
        });
    } else {
        router.post(route('admin.users.store'), form.data(), {
            onSuccess: () => { showModal.value = false; }
        });
    }
};

const deleteUser = (u) => {
    if (confirm(`Delete user ${u.name}?`)) {
        router.delete(route('admin.users.destroy', u.id));
    }
};
</script>

<template>
    <AdminLayout>
        <Head title="Staff & User Management" />

        <div class="space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-slate-800">User & Staff Management</h2>
                    <p class="text-sm text-slate-400">Manage administrator roles, staff access permissions, and passwords.</p>
                </div>
                <button 
                    @click="openCreateModal"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg shadow-sm flex items-center space-x-2 transition self-start sm:self-auto"
                >
                    <i class="fa-solid fa-user-plus"></i>
                    <span>Add New Staff</span>
                </button>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-600">
                        <thead class="bg-slate-50 text-slate-400 font-semibold uppercase tracking-wider border-b border-slate-100">
                            <tr>
                                <th class="px-5 py-3">Staff Name</th>
                                <th class="px-5 py-3">Email Address</th>
                                <th class="px-5 py-3">Role</th>
                                <th class="px-5 py-3">Status</th>
                                <th class="px-5 py-3">Created</th>
                                <th class="px-5 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="u in users" :key="u.id" class="hover:bg-slate-50 transition">
                                <td class="px-5 py-3.5 font-bold text-slate-800 flex items-center space-x-2.5">
                                    <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-700 font-bold flex items-center justify-center text-xs">
                                        {{ u.name.substring(0, 2).toUpperCase() }}
                                    </div>
                                    <span>{{ u.name }}</span>
                                </td>
                                <td class="px-5 py-3.5 text-slate-500 font-mono">{{ u.email }}</td>
                                <td class="px-5 py-3.5">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-purple-50 text-purple-700">
                                        {{ u.role.replace('_', ' ') }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <span :class="[u.is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-400', 'px-2 py-0.5 rounded-full text-[10px] font-bold']">
                                        {{ u.is_active ? 'Active' : 'Disabled' }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-slate-400 whitespace-nowrap">
                                    {{ u.created_at ? new Date(u.created_at).toLocaleDateString() : '—' }}
                                </td>
                                <td class="px-5 py-3.5 text-right whitespace-nowrap space-x-1">
                                    <button @click="openEditModal(u)" class="px-2.5 py-1 bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white rounded-lg font-semibold transition">
                                        Edit
                                    </button>
                                    <button 
                                        v-if="u.id !== $page.props.auth?.user?.id" 
                                        @click="deleteUser(u)" 
                                        class="p-1 text-rose-500 hover:text-rose-700 rounded transition"
                                    >
                                        <i class="fa-regular fa-trash-can"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Modal -->
            <div v-if="showModal" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4 overflow-y-auto">
                <div class="bg-white rounded-2xl border border-slate-200 max-w-md w-full p-6 shadow-2xl space-y-4 my-8">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <h3 class="text-base font-bold text-slate-800">{{ editingUser ? 'Edit Staff User' : 'Add New Staff' }}</h3>
                        <button @click="showModal = false" class="text-slate-400 hover:text-slate-600 text-lg">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>

                    <form @submit.prevent="saveUser" class="space-y-3 text-xs">
                        <div>
                            <label class="block font-semibold text-slate-600 mb-1">Full Name *</label>
                            <input v-model="form.name" type="text" required class="w-full py-1.5 bg-slate-50 border border-slate-200 rounded-lg" />
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-600 mb-1">Email Address *</label>
                            <input v-model="form.email" type="email" required class="w-full py-1.5 bg-slate-50 border border-slate-200 rounded-lg" />
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-600 mb-1">Role Permission</label>
                            <select v-model="form.role" class="w-full py-1.5 bg-slate-50 border border-slate-200 rounded-lg">
                                <option value="super_admin">Super Admin (Full Access)</option>
                                <option value="admin">Admin</option>
                                <option value="editor">Content Editor</option>
                                <option value="support">Support Agent (Inquiries only)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-600 mb-1">
                                Password {{ editingUser ? '(Leave empty to keep current)' : '*' }}
                            </label>
                            <input v-model="form.password" type="password" :required="!editingUser" class="w-full py-1.5 bg-slate-50 border border-slate-200 rounded-lg font-mono" />
                        </div>

                        <div class="pt-2">
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input v-model="form.is_active" type="checkbox" class="rounded text-blue-600" />
                                <span class="font-semibold text-slate-700">Account is Active</span>
                            </label>
                        </div>

                        <div class="flex justify-end space-x-2 pt-4 border-t border-slate-100">
                            <button type="button" @click="showModal = false" class="px-4 py-2 border border-slate-200 text-slate-600 rounded-lg font-semibold">
                                Cancel
                            </button>
                            <button type="submit" :disabled="form.processing" class="px-5 py-2 bg-blue-600 text-white rounded-lg font-bold shadow-sm">
                                Save Staff User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

