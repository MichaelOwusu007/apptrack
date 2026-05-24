@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Operations Dashboard')

@section('content')
<div class="space-y-6" x-data="dashboard()">

    {{-- Stats Row --}}
    <div class="grid grid-cols-2 md:grid-cols-4 xl:grid-cols-7 gap-3">
        @php
            $statCards = [
                ['label' => "Today's Total", 'value' => $stats['total'] ?? 0, 'color' => 'indigo', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                ['label' => 'Pending', 'value' => $stats['pending'] ?? 0, 'color' => 'amber', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['label' => 'In Progress', 'value' => $stats['in_progress'] ?? 0, 'color' => 'blue', 'icon' => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15'],
                ['label' => 'Completed', 'value' => $stats['done'] ?? 0, 'color' => 'emerald', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['label' => 'Escalated', 'value' => $stats['escalated'] ?? 0, 'color' => 'red', 'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'],
                ['label' => 'Critical', 'value' => $stats['critical'] ?? 0, 'color' => 'orange', 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z'],
                ['label' => 'Overdue', 'value' => $stats['overdue'] ?? 0, 'color' => 'rose', 'icon' => 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
            ];

            $colorMap = [
                'indigo' => 'text-indigo-400 bg-indigo-400/10 border-indigo-400/20',
                'amber' => 'text-amber-400 bg-amber-400/10 border-amber-400/20',
                'blue' => 'text-blue-400 bg-blue-400/10 border-blue-400/20',
                'emerald' => 'text-emerald-400 bg-emerald-400/10 border-emerald-400/20',
                'red' => 'text-red-400 bg-red-400/10 border-red-400/20',
                'orange' => 'text-orange-400 bg-orange-400/10 border-orange-400/20',
                'rose' => 'text-rose-400 bg-rose-400/10 border-rose-400/20',
            ];
        @endphp

        @foreach ($statCards as $card)
            <div class="bg-slate-900/60 border border-white/5 rounded-2xl p-4 flex flex-col gap-3 hover:border-white/10 transition-colors group">
                <div class="flex items-center justify-between">
                    <span class="text-xs text-slate-500 font-medium">{{ $card['label'] }}</span>

                    <div class="w-7 h-7 rounded-lg {{ $colorMap[$card['color']] }} border flex items-center justify-center">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}" />
                        </svg>
                    </div>
                </div>

                <p class="text-2xl font-bold text-white leading-none">{{ $card['value'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- Handover Alert --}}
    @if (isset($handover['previous_shift']) && $handover['previous_shift']->isNotEmpty())
        <div class="bg-red-500/5 border border-red-500/20 rounded-2xl p-5">
            <div class="flex items-center gap-3 mb-4">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-red-500 pulse-dot"></span>
                    <h3 class="text-sm font-semibold text-red-400">⚠ Shift Handover Required</h3>
                </div>

                <span class="px-2 py-0.5 rounded-full bg-red-500/20 text-red-400 text-xs font-bold">
                    {{ $handover['previous_shift']->count() }} pending
                </span>
            </div>

            <div class="space-y-2">
                @foreach ($handover['previous_shift']->take(5) as $activity)
                    <div class="flex items-center gap-4 bg-slate-900/60 rounded-xl px-4 py-3 border border-red-500/10">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-xs font-semibold uppercase tracking-wider px-2 py-0.5 rounded-md border {{ $activity->priority_color }}">
                                    {{ $activity->priority }}
                                </span>

                                <span class="text-sm font-medium text-slate-200 truncate">
                                    {{ $activity->title }}
                                </span>
                            </div>

                            <div class="flex items-center gap-3 text-xs text-slate-500">
                                <span>{{ $activity->activity_date->format('M d') }}</span>
                                <span>·</span>
                                <span>{{ $activity->assignedUser?->full_name ?? 'Unassigned' }}</span>

                                @if ($activity->latestUpdate)
                                    <span>·</span>
                                    <span>Last: {{ $activity->latestUpdate->created_at->diffForHumans() }}</span>
                                @endif
                            </div>
                        </div>

                        <a href="{{ route('activities.show', $activity) }}" class="shrink-0 px-3 py-1.5 rounded-lg bg-red-500/10 hover:bg-red-500/20 text-red-400 text-xs font-medium transition-colors">
                            View
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        {{-- Weekly Trend --}}
        <div class="bg-slate-900/60 border border-white/5 rounded-2xl p-5">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-semibold text-slate-200">Weekly Activity Trend</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Pending, completed and escalated work</p>
                </div>
            </div>

            <div id="weekly-trend-chart" class="min-h-[260px]"></div>
        </div>

        {{-- Recent Updates --}}
        <div class="bg-slate-900/60 border border-white/5 rounded-2xl p-5">

        <h3 class="text-sm font-semibold text-slate-200 mb-4">Today's Updates</h3>

        @if ($recentUpdates->isEmpty())
            <div class="flex flex-col items-center justify-center h-40 text-center">
                <svg class="w-10 h-10 text-slate-700 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>

                <p class="text-sm text-slate-600">No updates yet today</p>
            </div>
        @else
            <div class="space-y-3 overflow-y-auto max-h-52">
                @foreach ($recentUpdates as $update)
                    <div class="flex gap-3">
                        <div class="shrink-0 mt-1 flex flex-col items-center">
                            <div class="w-6 h-6 rounded-full overflow-hidden ring-2 ring-slate-800">
                                <img src="{{ $update->updatedBy?->profile_photo_url }}" class="w-full h-full object-cover" alt="">
                            </div>

                            @if (!$loop->last)
                                <div class="w-px h-full bg-white/5 mt-1"></div>
                            @endif
                        </div>

                        <div class="flex-1 pb-3 border-b border-white/5 last:border-0 last:pb-0">
                            <p class="text-xs text-slate-300 leading-snug">
                                <span class="font-medium">{{ $update->personnel_name }}</span>
                                marked
                                <span class="font-mono text-[10px] px-1 py-0.5 rounded {{ $update->status_badge_color }} border">
                                    {{ $update->new_status }}
                                </span>
                            </p>

                            <p class="text-[11px] text-slate-600 mt-0.5 truncate">
                                {{ $update->activity->title ?? '—' }}
                            </p>

                            <p class="text-[10px] text-slate-600 mt-0.5">
                                {{ $update->created_at->format('H:i') }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
        </div>
    </div>

    

    {{-- Today's Activities Table --}}
    <div class="bg-slate-900/60 border border-white/5 rounded-2xl overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-white/5">
            <div>
                <h3 class="text-sm font-semibold text-slate-200">Today's Activities</h3>
                <p class="text-xs text-slate-500 mt-0.5">{{ now()->format('l, d F Y') }}</p>
            </div>

            <a href="{{ route('activities.index') }}" class="text-xs text-indigo-400 hover:text-indigo-300 font-medium transition-colors">
                View all →
            </a>
        </div>

        @if ($todayActivities->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 text-center">
                <svg class="w-12 h-12 text-slate-700 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>

                <p class="text-sm font-medium text-slate-500">No activities for today</p>
                <p class="text-xs text-slate-600 mt-1">Start by creating the first activity</p>

                <a href="{{ route('activities.create') }}" class="mt-4 px-4 py-2 rounded-lg bg-indigo-500/10 hover:bg-indigo-500/20 text-indigo-400 text-sm font-medium transition-colors">
                    + Create Activity
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-white/5 text-[11px] uppercase tracking-wider text-slate-500">
                            <th class="text-left px-5 py-3 font-medium">Activity</th>
                            <th class="text-left px-4 py-3 font-medium">Type</th>
                            <th class="text-left px-4 py-3 font-medium">Priority</th>
                            <th class="text-left px-4 py-3 font-medium">Status</th>
                            <th class="text-left px-4 py-3 font-medium">Assigned To</th>
                            <th class="text-left px-4 py-3 font-medium">Last Update</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-white/5">
                        @foreach ($todayActivities as $activity)
                            <tr class="hover:bg-white/[0.02] transition-colors group">
                                <td class="px-5 py-3.5">
                                    <p class="font-medium text-slate-200 group-hover:text-white transition-colors">
                                        {{ $activity->title }}
                                    </p>

                                    @if ($activity->remarks)
                                        <p class="text-xs text-slate-600 truncate max-w-xs mt-0.5">
                                            {{ $activity->remarks }}
                                        </p>
                                    @endif
                                </td>

                                <td class="px-4 py-3.5">
                                    <span class="text-xs text-slate-400 font-mono">
                                        {{ $activity->activity_type_label }}
                                    </span>
                                </td>

                                <td class="px-4 py-3.5">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-semibold uppercase tracking-wider border {{ $activity->priority_color }}">
                                        {{ $activity->priority }}
                                    </span>
                                </td>

                                <td class="px-4 py-3.5">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-semibold uppercase tracking-wider border {{ $activity->status_color }}">
                                        @if ($activity->status === 'pending')
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-400 pulse-dot"></span>
                                        @endif

                                        {{ str_replace('_', ' ', $activity->status) }}
                                    </span>
                                </td>

                                <td class="px-4 py-3.5">
                                    @if ($activity->assignedUser)
                                        <div class="flex items-center gap-2">
                                            <img src="{{ $activity->assignedUser->profile_photo_url }}" class="w-6 h-6 rounded-full" alt="">

                                            <span class="text-xs text-slate-400">
                                                {{ $activity->assignedUser->full_name }}
                                            </span>
                                        </div>
                                    @else
                                        <span class="text-xs text-slate-600">Unassigned</span>
                                    @endif
                                </td>

                                <td class="px-4 py-3.5">
                                    @if ($activity->latestUpdate)
                                        <p class="text-xs text-slate-500">
                                            {{ $activity->latestUpdate->created_at->diffForHumans() }}
                                        </p>

                                        <p class="text-[11px] text-slate-600">
                                            by {{ $activity->latestUpdate->personnel_name }}
                                        </p>
                                    @else
                                        <span class="text-xs text-slate-600">No updates</span>
                                    @endif
                                </td>

                                <td class="px-4 py-3.5">
                                    <a href="{{ route('activities.show', $activity) }}" class="p-1.5 rounded-lg text-slate-600 hover:text-indigo-400 hover:bg-indigo-400/10 transition-colors inline-flex">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($todayActivities->hasPages())
                <div class="px-5 py-4 border-t border-white/5">
                    {{ $todayActivities->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
function dashboard() {
    return {
        weeklyTrend: @json($weeklyTrend),
        init() {
            this.renderChart();
        },
        renderChart() {
            const target = document.querySelector('#weekly-trend-chart');

            if (!target || typeof ApexCharts === 'undefined') {
                return;
            }

            const chart = new ApexCharts(target, {
                chart: {
                    type: 'area',
                    height: 260,
                    toolbar: { show: false },
                    foreColor: '#94a3b8',
                },
                series: [
                    { name: 'Pending', data: this.weeklyTrend.pending ?? [] },
                    { name: 'Completed', data: this.weeklyTrend.completed ?? [] },
                    { name: 'Escalated', data: this.weeklyTrend.escalated ?? [] },
                ],
                xaxis: {
                    categories: this.weeklyTrend.labels ?? [],
                    axisBorder: { show: false },
                    axisTicks: { show: false },
                },
                yaxis: {
                    min: 0,
                    forceNiceScale: true,
                    labels: { formatter: value => Math.round(value) },
                },
                colors: ['#f59e0b', '#10b981', '#ef4444'],
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth', width: 2 },
                fill: {
                    type: 'gradient',
                    gradient: { opacityFrom: 0.35, opacityTo: 0.02 },
                },
                grid: {
                    borderColor: 'rgba(148, 163, 184, 0.12)',
                    strokeDashArray: 4,
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'right',
                    labels: { colors: '#94a3b8' },
                },
                tooltip: {
                    theme: 'dark',
                },
            });

            chart.render();
        }
    };
}
</script>
@endpush
