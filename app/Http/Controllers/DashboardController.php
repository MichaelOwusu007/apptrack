<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ActivityUpdate;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(private readonly ActivityService $activityService) {}

    public function index()
    {
        $user        = Auth::user();
        $stats       = $this->activityService->getTodayStats();
        $weeklyTrend = $this->activityService->getWeeklyTrend();
        $handover    = $this->activityService->getHandoverActivities();

        $todayActivities = Activity::today()
            ->with(['assignedUser', 'creator', 'latestUpdate'])
            ->orderByRaw("CASE priority WHEN 'critical' THEN 1 WHEN 'high' THEN 2 WHEN 'medium' THEN 3 ELSE 4 END")
            ->orderBy('status')
            ->paginate(10);

        $recentUpdates = ActivityUpdate::with(['activity', 'updatedBy'])
            ->whereDate('created_at', today())
            ->latest()
            ->take(10)
            ->get();

        return view('dashboard.index', compact(
            'stats',
            'weeklyTrend',
            'handover',
            'todayActivities',
            'recentUpdates',
            'user'
        ));
    }
}
