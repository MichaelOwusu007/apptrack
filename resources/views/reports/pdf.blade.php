<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Activity Report — AppTrack Pro</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #1e293b; background: #fff; }
        .header { background: #1e293b; color: #fff; padding: 20px 30px; margin-bottom: 20px; }
        .header h1 { font-size: 18px; font-weight: bold; }
        .header p { font-size: 11px; color: #94a3b8; margin-top: 4px; }
        .meta { padding: 0 30px 16px; display: flex; gap: 20px; }
        .meta-item { font-size: 10px; color: #64748b; }
        .meta-item strong { display: block; font-size: 11px; color: #1e293b; margin-top: 2px; }
        table { width: 100%; border-collapse: collapse; padding: 0 30px; }
        th { background: #f8fafc; border-bottom: 2px solid #e2e8f0; padding: 8px 12px; text-align: left; font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; }
        td { padding: 8px 12px; border-bottom: 1px solid #f1f5f9; font-size: 11px; vertical-align: top; }
        tr:nth-child(even) td { background: #f8fafc; }
        .badge { display: inline-block; padding: 2px 6px; border-radius: 4px; font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; }
        .priority-critical { background: #fef2f2; color: #ef4444; }
        .priority-high { background: #fff7ed; color: #f97316; }
        .priority-medium { background: #fffbeb; color: #f59e0b; }
        .priority-low { background: #f0fdf4; color: #22c55e; }
        .status-done { background: #f0fdf4; color: #16a34a; }
        .status-pending { background: #fffbeb; color: #d97706; }
        .status-in_progress { background: #eff6ff; color: #2563eb; }
        .status-escalated { background: #fef2f2; color: #dc2626; }
        .footer { margin-top: 30px; padding: 12px 30px; border-top: 1px solid #e2e8f0; font-size: 10px; color: #94a3b8; display: flex; justify-content: space-between; }
    </style>
</head>
<body>
    <div class="header">
        <h1>AppTrack Pro — Activity Report</h1>
        <p>Generated on {{ now()->format('l, d F Y \a\t H:i:s') }}</p>
    </div>

    <div class="meta">
        <div class="meta-item">Total Activities<strong>{{ $activities->count() }}</strong></div>
        <div class="meta-item">Completed<strong>{{ $activities->where('status', 'done')->count() }}</strong></div>
        <div class="meta-item">Pending<strong>{{ $activities->where('status', 'pending')->count() }}</strong></div>
        <div class="meta-item">Escalated<strong>{{ $activities->where('status', 'escalated')->count() }}</strong></div>
        @if ($request->date_from || $request->date_to)
        <div class="meta-item">Date Range<strong>{{ $request->date_from ?? 'Any' }} — {{ $request->date_to ?? 'Any' }}</strong></div>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Activity</th>
                <th>Date</th>
                <th>Type</th>
                <th>Priority</th>
                <th>Status</th>
                <th>Assigned To</th>
                <th>Last Updated</th>
                <th>By</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($activities as $i => $activity)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>
                    <strong>{{ $activity->title }}</strong>
                    @if ($activity->remarks)
                        <br><span style="color:#94a3b8; font-size:10px">{{ Str::limit($activity->remarks, 60) }}</span>
                    @endif
                </td>
                <td>{{ $activity->activity_date->format('d M Y') }}</td>
                <td>{{ $activity->activity_type_label }}</td>
                <td><span class="badge priority-{{ $activity->priority }}">{{ $activity->priority }}</span></td>
                <td><span class="badge status-{{ $activity->status }}">{{ str_replace('_', ' ', $activity->status) }}</span></td>
                <td>{{ $activity->assignedUser?->full_name ?? '—' }}</td>
                <td>{{ $activity->updates->first()?->created_at->format('d M H:i') ?? '—' }}</td>
                <td>{{ $activity->updates->first()?->personnel_name ?? '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <span>AppTrack Pro — Npontu Technologies</span>
        <span>Confidential — Internal Use Only</span>
    </div>
</body>
</html>
