<script setup>
import { ref } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    posts: Array,
    categories: Array,
});

const showModal = ref(false);
const editingPost = ref(null);

const form = useForm({
    category: '',
    title: '',
    slug: '',
    excerpt: '',
    content: '',
    icon: 'fa-solid fa-newspaper',
    featured_image: '',
    author_name: 'Host Divine Team',
    read_time: '6 min read',
    is_published: true,
    published_at: '',
    meta_title: '',
    meta_description: '',
});

const openCreateModal = () => {
    editingPost.value = null;
    form.reset();
    form.clearErrors();
    showModal.value = true;
};

const openEditModal = (post) => {
    editingPost.value = post;
    form.clearErrors();
    Object.keys(form.data()).forEach((key) => { form[key] = post[key] ?? ''; });
    form.published_at = post.published_at ? post.published_at.slice(0, 10) : '';
    form.is_published = Boolean(post.is_published);
    showModal.value = true;
};

const savePost = () => {
    const options = { preserveScroll: true, onSuccess: () => { showModal.value = false; } };
    editingPost.value
        ? form.put(route('admin.blog.update', editingPost.value.id), options)
        : form.post(route('admin.blog.store'), options);
};

const slugify = () => {
    if (!editingPost.value && form.title) {
        form.slug = form.title.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
    }
};

const deletePost = (post) => {
    if (confirm(`Delete post "${post.title}"?`)) {
        router.delete(route('admin.blog.destroy', post.id));
    }
};
</script>

<template>
    <AdminLayout>
        <Head title="Manage Blog Articles" />

        <div class="space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-slate-800">Blog & Technology Tutorials</h2>
                    <p class="text-sm text-slate-400">Publish guides, server speed benchmarks, and .BD domain guides.</p>
                </div>
                <button 
                    @click="openCreateModal"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg shadow-sm flex items-center space-x-2 transition self-start sm:self-auto"
                >
                    <i class="fa-solid fa-plus"></i>
                    <span>Write New Article</span>
                </button>
            </div>

            <!-- Posts List -->
            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-600">
                        <thead class="bg-slate-50 text-slate-400 font-semibold uppercase tracking-wider border-b border-slate-100">
                            <tr>
                                <th class="px-5 py-3">Article Title</th>
                                <th class="px-5 py-3">Category</th>
                                <th class="px-5 py-3">Author & Read Time</th>
                                <th class="px-5 py-3">Status</th>
                                <th class="px-5 py-3">Date</th>
                                <th class="px-5 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="post in posts" :key="post.id" class="hover:bg-slate-50 transition">
                                <td class="px-5 py-3.5">
                                    <div class="font-bold text-slate-800 flex items-center space-x-2">
                                        <i :class="[post.icon, 'text-slate-400']"></i>
                                        <span>{{ post.title }}</span>
                                    </div>
                                    <div class="text-slate-400 text-[11px] font-mono mt-0.5">/blog/{{ post.slug }}</div>
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-medium">
                                        {{ post.category || 'General' }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="text-slate-700 font-semibold">{{ post.author_name }}</div>
                                    <div class="text-slate-400 text-[11px]">{{ post.read_time }}</div>
                                </td>
                                <td class="px-5 py-3.5">
                                    <span :class="[post.is_published ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-500', 'text-[10px] px-2 py-0.5 rounded-full font-bold']">
                                        {{ post.is_published ? 'Published' : 'Draft' }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-slate-400 whitespace-nowrap">
                                    {{ post.published_at ? new Date(post.published_at).toLocaleDateString() : '—' }}
                                </td>
                                <td class="px-5 py-3.5 text-right whitespace-nowrap space-x-1">
                                    <a :href="'/blog/' + post.slug" target="_blank" class="px-2.5 py-1 bg-slate-100 text-slate-600 hover:bg-slate-200 rounded-lg font-semibold transition" title="Preview on website">
                                        <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                                    </a>
                                    <button @click="openEditModal(post)" class="px-2.5 py-1 bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white rounded-lg font-semibold transition">
                                        Edit
                                    </button>
                                    <button @click="deletePost(post)" class="p-1 text-rose-500 hover:text-rose-700 rounded transition">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Post Edit Modal -->
            <div v-if="showModal" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4 overflow-y-auto">
                <div class="bg-white rounded-2xl border border-slate-200 max-w-2xl w-full p-6 shadow-2xl space-y-4 my-8">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <h3 class="text-base font-bold text-slate-800">
                            {{ editingPost ? 'Edit Article' : 'Write New Article' }}
                        </h3>
                        <button @click="showModal = false" class="text-slate-400 hover:text-slate-600 text-lg">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>

                    <form @submit.prevent="savePost" class="space-y-3 text-xs">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block font-semibold text-slate-600 mb-1">Article Title *</label>
                                <input v-model="form.title" type="text" placeholder="e.g. How LiteSpeed Delivers Faster TTFB" required class="w-full py-1.5 bg-slate-50 border border-slate-200 rounded-lg" @blur="slugify" />
                                <p v-if="form.errors.title" class="text-rose-600 mt-1">{{ form.errors.title }}</p>
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-600 mb-1">Slug *</label>
                                <input v-model="form.slug" type="text" placeholder="e.g. litespeed-faster-ttfb" required class="w-full py-1.5 bg-slate-50 border border-slate-200 rounded-lg font-mono" />
                                <p v-if="form.errors.slug" class="text-rose-600 mt-1">{{ form.errors.slug }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <label class="block font-semibold text-slate-600 mb-1">Category</label>
                                <input v-model="form.category" type="text" list="blog-categories" placeholder="e.g. Domain Guide" class="w-full py-1.5 bg-slate-50 border border-slate-200 rounded-lg" />
                                <datalist id="blog-categories">
                                    <option v-for="cat in categories" :key="cat" :value="cat" />
                                </datalist>
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-600 mb-1">Author Name</label>
                                <input v-model="form.author_name" type="text" class="w-full py-1.5 bg-slate-50 border border-slate-200 rounded-lg" />
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-600 mb-1">Read Time</label>
                                <input v-model="form.read_time" type="text" placeholder="6 min read" class="w-full py-1.5 bg-slate-50 border border-slate-200 rounded-lg" />
                            </div>
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-600 mb-1">Excerpt</label>
                            <textarea v-model="form.excerpt" rows="2" class="w-full p-2 bg-slate-50 border border-slate-200 rounded-lg"></textarea>
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-600 mb-1">Article Content (HTML / Rich text) *</label>
                            <textarea v-model="form.content" rows="8" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-lg font-mono text-xs leading-relaxed"></textarea>
                            <p v-if="form.errors.content" class="text-rose-600 mt-1">{{ form.errors.content }}</p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <label class="block font-semibold text-slate-600 mb-1">Card icon</label>
                                <input v-model="form.icon" type="text" placeholder="fa-solid fa-globe" class="w-full py-1.5 bg-slate-50 border border-slate-200 rounded-lg font-mono" />
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-600 mb-1">Featured image path (optional)</label>
                                <input v-model="form.featured_image" type="text" placeholder="storage/cms/…" class="w-full py-1.5 bg-slate-50 border border-slate-200 rounded-lg font-mono" />
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-600 mb-1">Publish date</label>
                                <input v-model="form.published_at" type="date" class="w-full py-1.5 bg-slate-50 border border-slate-200 rounded-lg" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block font-semibold text-slate-600 mb-1">SEO title (optional)</label>
                                <input v-model="form.meta_title" type="text" class="w-full py-1.5 bg-slate-50 border border-slate-200 rounded-lg" />
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-600 mb-1">SEO description (optional)</label>
                                <input v-model="form.meta_description" type="text" class="w-full py-1.5 bg-slate-50 border border-slate-200 rounded-lg" />
                            </div>
                        </div>

                        <div class="flex items-center space-x-6 pt-2">
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input v-model="form.is_published" type="checkbox" class="rounded text-blue-600" />
                                <span class="font-semibold text-slate-700">Published</span>
                            </label>
                        </div>

                        <div class="flex justify-end space-x-2 pt-4 border-t border-slate-100">
                            <button type="button" @click="showModal = false" class="px-4 py-2 border border-slate-200 text-slate-600 rounded-lg font-semibold">
                                Cancel
                            </button>
                            <button type="submit" :disabled="form.processing" class="px-5 py-2 bg-blue-600 text-white rounded-lg font-bold shadow-sm">
                                Save Article
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

