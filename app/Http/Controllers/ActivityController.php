<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\User;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ActivityController extends Controller
{
    public function __construct(private readonly ActivityService $activityService) {}

    public function index(Request $request)
    {
        Gate::authorize('viewAny', Activity::class);

        $query = Activity::with(['assignedUser', 'creator', 'latestUpdate']);

        // Filters
        if ($request->filled('date')) {
            $query->whereDate('activity_date', $request->date);
        } else {
            $query->today();
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        if ($request->filled('type')) {
            $query->where('activity_type', $request->type);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'ilike', "%{$request->search}%")
                  ->orWhere('description', 'ilike', "%{$request->search}%");
            });
        }

        $activities = $query
            ->orderByRaw("CASE priority WHEN 'critical' THEN 1 WHEN 'high' THEN 2 WHEN 'medium' THEN 3 ELSE 4 END")
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        $personnel = User::active()->select('id', 'full_name', 'employee_id')->get();

        return view('activities.index', compact('activities', 'personnel'));
    }

    public function create()
    {
        Gate::authorize('create', Activity::class);

        $personnel    = User::active()->select('id', 'full_name', 'employee_id')->get();
        $activityTypes = Activity::ACTIVITY_TYPES;

        return view('activities.create', compact('personnel', 'activityTypes'));
    }

    public function store(Request $request)
    {
        Gate::authorize('create', Activity::class);

        $validated = $request->validate([
            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string|max:2000',
            'priority'      => 'required|in:low,medium,high,critical',
            'activity_type' => 'required|string|max:100',
            'assigned_to'   => 'nullable|exists:users,id',
            'activity_date' => 'required|date',
            'remarks'       => 'nullable|string|max:1000',
        ]);

        $activity = $this->activityService->createActivity($validated);

        return redirect()->route('activities.show', $activity)
            ->with('success', 'Activity created successfully.');
    }

    public function show(Activity $activity)
    {
        Gate::authorize('view', $activity);

        $activity->load(['assignedUser', 'creator', 'updates.updatedBy']);

        return view('activities.show', compact('activity'));
    }

    public function edit(Activity $activity)
    {
        Gate::authorize('update', $activity);

        $personnel    = User::active()->select('id', 'full_name', 'employee_id')->get();
        $activityTypes = Activity::ACTIVITY_TYPES;

        return view('activities.edit', compact('activity', 'personnel', 'activityTypes'));
    }

    public function update(Request $request, Activity $activity)
    {
        Gate::authorize('update', $activity);

        $validated = $request->validate([
            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string|max:2000',
            'priority'      => 'required|in:low,medium,high,critical',
            'activity_type' => 'required|string|max:100',
            'assigned_to'   => 'nullable|exists:users,id',
            'activity_date' => 'required|date',
        ]);

        $activity->update($validated);

        return redirect()->route('activities.show', $activity)
            ->with('success', 'Activity updated successfully.');
    }

    public function updateStatus(Request $request, Activity $activity)
    {
        Gate::authorize('updateStatus', $activity);

        $validated = $request->validate([
            'status'  => 'required|in:pending,in_progress,done,escalated',
            'remarks' => 'nullable|string|max:1000',
        ]);

        $update = $this->activityService->updateActivityStatus(
            $activity,
            $validated['status'],
            $validated['remarks'] ?? null
        );

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'update'  => $update,
                'activity'=> $activity->fresh(['latestUpdate']),
            ]);
        }

        return back()->with('success', 'Activity status updated.');
    }

    public function destroy(Activity $activity)
    {
        Gate::authorize('delete', $activity);

        $activity->delete();

        return redirect()->route('activities.index')
            ->with('success', 'Activity deleted.');
    }
}
