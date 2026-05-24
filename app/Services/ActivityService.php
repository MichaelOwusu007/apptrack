<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\ActivityUpdate;
use App\Models\AppNotification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ActivityService
{
    public function __construct(
        private readonly AuditService $auditService
    ) {}

    public function createActivity(array $data): Activity
    {
        $activity = Activity::create([
            ...$data,
            'created_by' => Auth::id(),
            'activity_date' => $data['activity_date'] ?? today(),
            'status' => 'pending',
        ]);

        $this->auditService->log('create_activity', 'Activity', $activity->id, null, $data, "Created activity: {$activity->title}");

        if ($activity->assigned_to && $activity->assigned_to !== Auth::id()) {
            $this->notifyUser($activity->assigned_to, 'New Activity Assigned', "You have been assigned: {$activity->title}", 'info', route('activities.show', $activity->id));
        }

        return $activity;
    }

    public function updateActivityStatus(Activity $activity, string $newStatus, ?string $remarks = null): ActivityUpdate
    {
        $user = Auth::user();
        $oldStatus = $activity->status;

        $activity->update([
            'status' => $newStatus,
            'remarks' => $remarks ?? $activity->remarks,
            'completed_at' => $newStatus === 'done' ? now() : null,
        ]);

        $update = $this->recordUpdate($activity, $user, $oldStatus, $newStatus, $remarks);

        $this->auditService->log(
            'update_status',
            'Activity',
            $activity->id,
            ['status' => $oldStatus],
            ['status' => $newStatus, 'remarks' => $remarks],
            "Status changed from {$oldStatus} to {$newStatus} for: {$activity->title}"
        );

        if ($newStatus === 'escalated') {
            User::role('supervisor')
                ->select('id')
                ->get()
                ->each(fn (User $supervisor) => $this->notifyUser(
                    $supervisor->id,
                    'Activity Escalated',
                    "{$activity->title} has been escalated by {$user->full_name}",
                    'error',
                    route('activities.show', $activity->id)
                ));
        }

        return $update;
    }

    private function recordUpdate(Activity $activity, User $user, string $oldStatus, string $newStatus, ?string $remarks): ActivityUpdate
    {
        return ActivityUpdate::create([
            'activity_id' => $activity->id,
            'updated_by' => $user->id,
            'personnel_name' => $user->full_name,
            'personnel_role' => $user->primary_role_label,
            'personnel_department' => $user->department,
            'previous_status' => $oldStatus,
            'new_status' => $newStatus,
            'remarks' => $remarks,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'browser' => $this->browserLabel(request()->userAgent()),
        ]);
    }

    private function browserLabel(?string $userAgent): ?string
    {
        if (!$userAgent) {
            return null;
        }

        return match (true) {
            str_contains($userAgent, 'Edg/') => 'Microsoft Edge',
            str_contains($userAgent, 'Chrome/') => 'Chrome',
            str_contains($userAgent, 'Firefox/') => 'Firefox',
            str_contains($userAgent, 'Safari/') => 'Safari',
            default => 'Unknown Browser',
        };
    }

    private function notifyUser(string $userId, string $title, string $message, string $type = 'info', ?string $link = null): void
    {
        AppNotification::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'link' => $link,
        ]);
    }

    public function getTodayStats(): array
    {
        $today = today();

        $statusCounts = Activity::forDate($today)
            ->select('status', DB::raw('count(*) as aggregate'))
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        return [
            'total' => (int) $statusCounts->sum(),
            'pending' => (int) ($statusCounts['pending'] ?? 0),
            'in_progress' => (int) ($statusCounts['in_progress'] ?? 0),
            'done' => (int) ($statusCounts['done'] ?? 0),
            'escalated' => (int) ($statusCounts['escalated'] ?? 0),
            'critical' => Activity::forDate($today)->where('priority', 'critical')->count(),
            'overdue' => Activity::where('status', '!=', 'done')
                ->where('activity_date', '<', $today)
                ->count(),
        ];
    }

    public function getHandoverActivities(): array
    {
        $previousShift = Activity::where('status', '!=', 'done')
            ->where('activity_date', '<', today())
            ->with(['assignedUser', 'latestUpdate'])
            ->orderByRaw("CASE priority WHEN 'critical' THEN 1 WHEN 'high' THEN 2 WHEN 'medium' THEN 3 ELSE 4 END")
            ->orderBy('activity_date', 'asc')
            ->get();

        $todayPending = Activity::today()
            ->where('status', 'pending')
            ->with(['assignedUser', 'latestUpdate'])
            ->orderByRaw("CASE priority WHEN 'critical' THEN 1 WHEN 'high' THEN 2 WHEN 'medium' THEN 3 ELSE 4 END")
            ->get();

        return [
            'previous_shift' => $previousShift,
            'today_pending' => $todayPending,
        ];
    }

    public function getWeeklyTrend(): array
    {
        $start = today()->subDays(6);
        $end = today();

        $rows = Activity::query()
            ->selectRaw('activity_date, status, count(*) as aggregate')
            ->whereBetween('activity_date', [$start, $end])
            ->groupBy('activity_date', 'status')
            ->orderBy('activity_date')
            ->get()
            ->groupBy(fn (Activity $activity) => $activity->activity_date->toDateString());

        $labels = [];
        $pending = [];
        $completed = [];
        $escalated = [];

        foreach ($start->toPeriod($end) as $date) {
            $key = $date->toDateString();
            $dayRows = $rows->get($key, collect())->keyBy('status');

            $labels[] = $date->format('D');
            $pending[] = (int) ($dayRows->get('pending')->aggregate ?? 0);
            $completed[] = (int) ($dayRows->get('done')->aggregate ?? 0);
            $escalated[] = (int) ($dayRows->get('escalated')->aggregate ?? 0);
        }

        return compact('labels', 'pending', 'completed', 'escalated');
    }
}
