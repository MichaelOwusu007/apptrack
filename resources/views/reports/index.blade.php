@extends('layouts.app')
@section('title', 'Reports')
@section('page-title', 'Activity Reports')

@section('content')
<div class="space-y-4">

    {{-- ── Summary Cards ────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-3 gap-3">
        @php
            $summaryCards = [
                ['label' => 'Total Filtered', 'value' => $summary['total'], 'color' => 'text-indigo-400'],
                ['label' => 'Completed',       'value' => $summary['done'],  'color' => 'text-emerald-400'],
                ['label' => 'Pending',         'value' => $summary['pending'], 'color' => 'text-amber-400'],
            ];
        @endphp
        @foreach ($summaryCards as $card)
        <div class="bg-slate-900/60 border border-white/5 rounded-2xl p-4 text-center">
            <p class="text-2xl font-bold {{ $card['color'] }}">{{ $card['value'] }}</p>
            <p class="text-xs text-slate-500 mt-1">{{ $card['label'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- ── Filter form ──────────────────────────────────────────────────── --}}
    <form method="GET" action="{{ route('reports.index') }}" id="reportForm">
        <div class="bg-slate-900/60 border border-white/5 rounded-2xl p-5 space-y-4">
            <h3 class="text-sm font-semibold text-slate-300">Filter Report</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 xl:grid-cols-6 gap-3">
                <div>
                    <label class="block text-xs text-slate-500 mb-1">Date From</label>
                    <input type="date" name="date_from" value="{{ request('date_from', today()->toDateString()) }}" class="w-full bg-slate-800/80 border border-white/10 rounded-xl px-3 py-2 text-sm text-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500/40">
                </div>
                <div>
                    <label class="block text-xs text-slate-500 mb-1">Date To</label>
                    <input type="date" name="date_to" value="{{ request('date_to', today()->toDateString()) }}" class="w-full bg-slate-800/80 border border-white/10 rounded-xl px-3 py-2 text-sm text-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500/40">
                </div>
                <div>
                    <label class="block text-xs text-slate-500 mb-1">Status</label>
                    <select name="status" class="w-full bg-slate-800/80 border border-white/10 rounded-xl px-3 py-2 text-sm text-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500/40">
                        <option value="">All</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="done" {{ request('status') === 'done' ? 'selected' : '' }}>Done</option>
                        <option value="escalated" {{ request('status') === 'escalated' ? 'selected' : '' }}>Escalated</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-slate-500 mb-1">Priority</label>
                    <select name="priority" class="w-full bg-slate-800/80 border border-white/10 rounded-xl px-3 py-2 text-sm text-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500/40">
                        <option value="">All</option>
                        <option value="critical">Critical</option>
                        <option value="high">High</option>
                        <option value="medium">Medium</option>
                        <option value="low">Low</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-slate-500 mb-1">Personnel</label>
                    <select name="assigned_to" class="w-full bg-slate-800/80 border border-white/10 rounded-xl px-3 py-2 text-sm text-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500/40">
                        <option value="">All</option>
                        @foreach ($personnel as $person)
                            <option value="{{ $person->id }}" {{ request('assigned_to') === $person->id ? 'selected' : '' }}>{{ $person->full_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-slate-500 mb-1">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Title..." class="w-full bg-slate-800/80 border border-white/10 rounded-xl px-3 py-2 text-sm text-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500/40 placeholder-slate-600">
                </div>
            </div>
            <div class="flex flex-wrap gap-2 pt-1">
                <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-500 hover:bg-indigo-400 text-white text-sm font-medium transition-colors shadow-lg shadow-indigo-500/20">Apply Filters</button>
                <a href="{{ route('reports.index') }}" class="px-4 py-2 rounded-xl border border-white/10 text-slate-400 hover:text-white text-sm font-medium transition-colors">Reset</a>
                <div class="ml-auto flex gap-2">
                    <a href="{{ route('reports.export.pdf', request()->query()) }}" target="_blank" class="flex items-center gap-2 px-4 py-2 rounded-xl border border-red-500/20 bg-red-500/5 hover:bg-red-500/10 text-red-400 text-sm font-medium transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Export PDF
                    </a>
                    <a href="{{ route('reports.export.excel', request()->query()) }}" class="flex items-center gap-2 px-4 py-2 rounded-xl border border-emerald-500/20 bg-emerald-500/5 hover:bg-emerald-500/10 text-emerald-400 text-sm font-medium transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        Export Excel
                    </a>
                    <button type="button" onclick="window.print()" class="flex items-center gap-2 px-4 py-2 rounded-xl border border-white/10 text-slate-400 hover:text-white text-sm font-medium transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        Print
                    </button>
                </div>
            </div>
        </div>
    </form>

    {{-- ── Results table ────────────────────────────────────────────────── --}}
    <div class="bg-slate-900/60 border border-white/5 rounded-2xl overflow-hidden">
        @if ($activities->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 text-center">
                <svg class="w-12 h-12 text-slate-700 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                <p class="text-slate-500">No activities found for the selected filters</p>
            </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-800/50 border-b border-white/5 text-[11px] uppercase tracking-wider text-slate-500">
                        <th class="text-left px-5 py-3 font-medium">Activity</th>
                        <th class="text-left px-4 py-3 font-medium">Date</th>
                        <th class="text-left px-4 py-3 font-medium">Type</th>
                        <th class="text-left px-4 py-3 font-medium">Priority</th>
                        <th class="text-left px-4 py-3 font-medium">Status</th>
                        <th class="text-left px-4 py-3 font-medium">Assigned To</th>
                        <th class="text-left px-4 py-3 font-medium">Updates</th>
                        <th class="text-left px-4 py-3 font-medium">Last Updated By</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @foreach ($activities as $activity)
                    <tr class="hover:bg-white/[0.02] transition-colors">
                        <td class="px-5 py-3.5 max-w-xs">
                            <p class="font-medium text-slate-200 truncate">{{ $activity->title }}</p>
                            @if ($activity->remarks)
                                <p class="text-xs text-slate-600 truncate mt-0.5">{{ $activity->remarks }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-sm text-slate-400 whitespace-nowrap">{{ $activity->activity_date->format('d M Y') }}</td>
                        <td class="px-4 py-3.5 text-xs text-slate-500 font-mono">{{ $activity->activity_type_label }}</td>
                        <td class="px-4 py-3.5">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold uppercase tracking-wider border {{ $activity->priority_color }}">{{ $activity->priority }}</span>
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md text-[11px] font-semibold uppercase tracking-wider border {{ $activity->status_color }}">{{ str_replace('_', ' ', $activity->status) }}</span>
                        </td>
                        <td class="px-4 py-3.5 text-sm text-slate-400">{{ $activity->assignedUser?->full_name ?? '—' }}</td>
                        <td class="px-4 py-3.5 text-center">
                            <span class="text-sm font-medium text-slate-300">{{ $activity->updates->count() }}</span>
                        </td>
                        <td class="px-4 py-3.5">
                            @if ($activity->updates->first())
                                <p class="text-xs text-slate-300">{{ $activity->updates->first()->personnel_name }}</p>
                                <p class="text-[11px] text-slate-600">{{ $activity->updates->first()->created_at->format('M d, H:i') }}</p>
                            @else
                                <span class="text-xs text-slate-600">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5">
                            <a href="{{ route('activities.show', $activity) }}" class="p-1.5 rounded-lg text-slate-600 hover:text-indigo-400 hover:bg-indigo-400/10 transition-colors inline-flex">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="px-5 py-4 border-t border-white/5">
            {{ $activities->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
