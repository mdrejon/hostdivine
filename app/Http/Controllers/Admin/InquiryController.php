<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class InquiryController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Inquiry::query()->latest();

        if ($request->filled('search')) {
            $s = $request->input('search');
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%")
                    ->orWhere('message', 'like', "%{$s}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('department')) {
            $query->where('department', $request->input('department'));
        }

        $departments = [
            'Pre-Sales & Plan Inquiries',
            'Technical Support (General)',
            'Free Website Migration Request',
            'BTCL .BD Domain Processing',
            'Reseller / Master Reseller Solutions',
            'Dedicated Server & Enterprise Clusters',
        ];

        return Inertia::render('Admin/Inquiries/Index', [
            'inquiries' => $query->paginate(15)->through(fn ($inq) => [
                'id' => $inq->id,
                'name' => $inq->name,
                'email' => $inq->email,
                'phone' => $inq->phone,
                'department' => $inq->department,
                'service_id' => $inq->service_id,
                'message' => $inq->message,
                'status' => $inq->status,
                'created_at' => $inq->created_at->format('M d, Y h:i A'),
            ]),
            'departments' => $departments,
            'filters' => $request->only(['search', 'status', 'department']),
        ]);
    }

    public function show(Inquiry $inquiry): Response
    {
        return Inertia::render('Admin/Inquiries/Show', [
            'inquiry' => [
                'id' => $inquiry->id,
                'name' => $inquiry->name,
                'email' => $inquiry->email,
                'phone' => $inquiry->phone,
                'department' => $inquiry->department,
                'service_id' => $inquiry->service_id,
                'message' => $inquiry->message,
                'status' => $inquiry->status,
                'priority' => $inquiry->priority,
                'admin_notes' => $inquiry->admin_notes,
                'ip_address' => $inquiry->ip_address,
                'created_at' => $inquiry->created_at->format('M d, Y h:i A'),
            ],
        ]);
    }

    public function update(Request $request, Inquiry $inquiry): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|string|in:new,in_progress,contacted,closed,spam',
            'priority' => 'required|string|in:low,normal,high,urgent',
            'admin_notes' => 'nullable|string',
        ]);

        $inquiry->update($validated);

        return back()->with('success', 'Inquiry updated successfully.');
    }

    public function updateStatus(Request $request, Inquiry $inquiry): RedirectResponse
    {
        $request->validate(['status' => 'required|string|in:new,in_progress,contacted,closed,spam']);
        $inquiry->update(['status' => $request->input('status')]);

        return back()->with('success', 'Inquiry status updated.');
    }

    public function destroy(Inquiry $inquiry): RedirectResponse
    {
        $inquiry->delete();

        return redirect()->route('admin.inquiries.index')->with('success', 'Inquiry deleted.');
    }
}
