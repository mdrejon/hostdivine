<script setup>
import { ref } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    smtp: Object,
});

const form = useForm({
    mail_mailer: props.smtp.mail_mailer || 'smtp',
    mail_host: props.smtp.mail_host || 'mail.hostdivine.com',
    mail_port: props.smtp.mail_port || '587',
    mail_username: props.smtp.mail_username || '',
    mail_password: props.smtp.mail_password || '',
    mail_encryption: props.smtp.mail_encryption || 'tls',
    mail_from_address: props.smtp.mail_from_address || 'noreply@hostdivine.com',
    mail_from_name: props.smtp.mail_from_name || 'Host Divine Networks',
});

const testEmailForm = useForm({
    test_email: '',
});

const isSendingTest = ref(false);

const saveSmtp = () => {
    form.post(route('admin.smtp.update'));
};

const sendTestEmail = () => {
    if (!testEmailForm.test_email) {
        alert('Please enter a recipient email address.');
        return;
    }
    isSendingTest.value = true;
    testEmailForm.post(route('admin.smtp.test'), {
        onFinish: () => { isSendingTest.value = false; }
    });
};
</script>

<template>
    <AdminLayout>
        <Head title="Email SMTP Settings" />

        <div class="max-w-4xl mx-auto space-y-6">
            <div>
                <h2 class="text-2xl font-bold text-slate-800">Email SMTP Configuration</h2>
                <p class="text-sm text-slate-400">Configure outbound SMTP mail delivery for contact form notifications and client replies.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Left: Form -->
                <div class="md:col-span-2 bg-white rounded-xl border border-slate-200 p-6 shadow-sm space-y-4 text-xs">
                    <form @submit.prevent="saveSmtp" class="space-y-4">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block font-semibold text-slate-600 mb-1">Mail Driver</label>
                                <input v-model="form.mail_mailer" type="text" class="w-full py-2 bg-slate-50 border border-slate-200 rounded-lg font-mono" />
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-600 mb-1">Encryption</label>
                                <select v-model="form.mail_encryption" class="w-full py-2 bg-slate-50 border border-slate-200 rounded-lg">
                                    <option value="tls">TLS (Port 587)</option>
                                    <option value="ssl">SSL (Port 465)</option>
                                    <option value="none">None (Port 25)</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-3">
                            <div class="col-span-2">
                                <label class="block font-semibold text-slate-600 mb-1">SMTP Host Server</label>
                                <input v-model="form.mail_host" type="text" placeholder="mail.yourdomain.com" class="w-full py-2 bg-slate-50 border border-slate-200 rounded-lg font-mono" />
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-600 mb-1">Port</label>
                                <input v-model="form.mail_port" type="text" placeholder="587" class="w-full py-2 bg-slate-50 border border-slate-200 rounded-lg font-mono" />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block font-semibold text-slate-600 mb-1">SMTP Username</label>
                                <input v-model="form.mail_username" type="text" class="w-full py-2 bg-slate-50 border border-slate-200 rounded-lg font-mono" />
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-600 mb-1">SMTP Password</label>
                                <input v-model="form.mail_password" type="password" placeholder="••••••••••••" class="w-full py-2 bg-slate-50 border border-slate-200 rounded-lg font-mono" />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 pt-2 border-t border-slate-100">
                            <div>
                                <label class="block font-semibold text-slate-600 mb-1">From Email Address</label>
                                <input v-model="form.mail_from_address" type="email" class="w-full py-2 bg-slate-50 border border-slate-200 rounded-lg" />
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-600 mb-1">From Sender Name</label>
                                <input v-model="form.mail_from_name" type="text" class="w-full py-2 bg-slate-50 border border-slate-200 rounded-lg" />
                            </div>
                        </div>

                        <div class="pt-4 border-t border-slate-100 flex justify-end">
                            <button type="submit" :disabled="form.processing" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg shadow-sm transition">
                                Save SMTP Settings
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Right: Test Email Tool -->
                <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm space-y-4 text-xs h-fit">
                    <h3 class="font-bold text-slate-800 text-sm flex items-center space-x-2">
                        <i class="fa-solid fa-paper-plane text-blue-600"></i>
                        <span>Test Outbound Email</span>
                    </h3>
                    <p class="text-slate-500 leading-relaxed">
                        Verify your SMTP credentials by dispatching an instant test email message.
                    </p>

                    <form @submit.prevent="sendTestEmail" class="space-y-3">
                        <div>
                            <label class="block font-semibold text-slate-600 mb-1">Send Test Email To</label>
                            <input v-model="testEmailForm.test_email" type="email" placeholder="your@email.com" required class="w-full py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs" />
                        </div>
                        <button 
                            type="submit" 
                            :disabled="isSendingTest"
                            class="w-full py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg shadow-sm transition flex items-center justify-center space-x-1.5"
                        >
                            <i v-if="isSendingTest" class="fa-solid fa-circle-notch fa-spin"></i>
                            <i v-else class="fa-solid fa-envelope"></i>
                            <span>{{ isSendingTest ? 'Sending...' : 'Send Test Email' }}</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

