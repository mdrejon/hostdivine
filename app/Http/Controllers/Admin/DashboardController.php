<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\DomainTld;
use App\Models\Inquiry;
use App\Models\Page;
use App\Models\User;
use Carbon\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $today = Carbon::today();

        $stats = [
            'todayInquiries' => Inquiry::whereDate('created_at', $today)->count(),
            'pendingInquiries' => Inquiry::whereIn('status', ['new', 'in_progress'])->count(),
            'newInquiries' => Inquiry::where('status', 'new')->count(),
            'totalInquiries' => Inquiry::count(),
            'activePages' => Page::where('is_active', true)->count(),
            'domainTlds' => DomainTld::where('is_active', true)->count(),
            'staffUsers' => User::where('is_active', true)->count(),
            'publishedPosts' => BlogPost::where('is_published', true)->count(),
        ];

        // 7-day inquiries chart data
        $weeklyChart = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $weeklyChart[] = [
                'day' => $date->format('D'),
                'date' => $date->format('Y-m-d'),
                'count' => Inquiry::whereDate('created_at', $date)->count(),
            ];
        }

        // Status counts breakdown
        $statusCounts = [
            'new' => Inquiry::where('status', 'new')->count(),
            'in_progress' => Inquiry::where('status', 'in_progress')->count(),
            'contacted' => Inquiry::where('status', 'contacted')->count(),
            'closed' => Inquiry::where('status', 'closed')->count(),
        ];

        // Recent inquiries
        $recentInquiries = Inquiry::latest()
            ->take(6)
            ->get()
            ->map(fn ($inq) => [
                'id' => $inq->id,
                'name' => $inq->name,
                'email' => $inq->email,
                'phone' => $inq->phone,
                'department' => $inq->department,
                'message' => $inq->message,
                'status' => $inq->status,
                'created_at' => $inq->created_at->diffForHumans(),
            ]);

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'weeklyChart' => $weeklyChart,
            'statusCounts' => $statusCounts,
            'recentInquiries' => $recentInquiries,
        ]);
    }
}
