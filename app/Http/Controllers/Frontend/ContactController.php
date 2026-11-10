<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    /**
     * Store a contact-form inquiry and notify the sales inbox.
     */
    public function submit(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'department' => ['required', 'string', 'max:100'],
            'service' => ['nullable', 'string', 'max:100'],
            'message' => ['required', 'string', 'min:5', 'max:3000'],
        ]);

        $inquiry = Inquiry::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'department' => $validated['department'],
            'service_id' => $validated['service'] ?? null,
            'message' => $validated['message'],
            'ip_address' => $request->ip(),
            'status' => 'new',
            'priority' => 'normal',
        ]);

        $adminEmail = Setting::get('sales_email');

        if ($adminEmail) {
            try {
                Mail::raw(
                    "New inquiry received\n\nName: {$inquiry->name}\nEmail: {$inquiry->email}\nPhone: {$inquiry->phone}\nDepartment: {$inquiry->department}\n\n{$inquiry->message}",
                    fn ($message) => $message->to($adminEmail)->subject("New Inquiry: [{$inquiry->department}] from {$inquiry->name}")
                );
            } catch (\Throwable $exception) {
                Log::warning('Inquiry notification email failed: '.$exception->getMessage());
            }
        }

        $message = 'Thank you! Your message has been received. Our team will reply within 15 minutes during office hours.';

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return back()->with('contact_success', $message);
    }
}
