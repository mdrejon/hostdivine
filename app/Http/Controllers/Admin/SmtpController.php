<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response;

class SmtpController extends Controller
{
    public function index(): Response
    {
        $smtp = [
            'mail_mailer' => Setting::get('mail_mailer', 'smtp'),
            'mail_host' => Setting::get('mail_host', 'mail.hostdivine.com'),
            'mail_port' => Setting::get('mail_port', '587'),
            'mail_username' => Setting::get('mail_username', ''),
            'mail_password' => Setting::get('mail_password', ''),
            'mail_encryption' => Setting::get('mail_encryption', 'tls'),
            'mail_from_address' => Setting::get('mail_from_address', 'noreply@hostdivine.com'),
            'mail_from_name' => Setting::get('mail_from_name', 'Host Divine Networks'),
        ];

        return Inertia::render('Admin/Settings/Smtp', [
            'smtp' => $smtp,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'mail_mailer' => 'required|string',
            'mail_host' => 'required|string',
            'mail_port' => 'required|numeric',
            'mail_username' => 'nullable|string',
            'mail_password' => 'nullable|string',
            'mail_encryption' => 'required|string',
            'mail_from_address' => 'required|email',
            'mail_from_name' => 'required|string',
        ]);

        foreach ($validated as $key => $value) {
            Setting::set($key, $value ?? '', 'smtp');
        }

        return back()->with('success', 'SMTP settings saved successfully.');
    }

    public function sendTestEmail(Request $request): RedirectResponse
    {
        $request->validate([
            'test_email' => 'required|email',
        ]);

        try {
            $to = $request->input('test_email');
            Mail::raw('This is a test notification from Host Divine™ Admin Dashboard confirming outbound SMTP email delivery is operational.', function ($message) use ($to) {
                $message->to($to)
                    ->subject('Host Divine™ SMTP Test Message');
            });

            return back()->with('success', 'Test email dispatched successfully to '.$to);
        } catch (\Throwable $e) {
            return back()->with('error', 'SMTP Test Failed: '.$e->getMessage());
        }
    }
}
