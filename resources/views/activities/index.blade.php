@extends('layouts.app')
@section('title', 'Activities')
@section('page-title', 'Activities')

@section('content')
<div class="space-y-4">

    {{-- ── Filter bar ──────────────────────────────────────────────────── --}}
    <form method="GET" action="{{ route('activities.index') }}" x-data="{ open: false }">
        <div class="bg-slate-900/60 border border-white/5 rounded-2xl p-4">
            <div class="flex flex-col md:flex-row gap-3">
                <!-- Search -->
                <div class="flex-1 relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search activities..." class="w-full bg-slate-800/80 border border-white/10 rounded-xl pl-9 pr-4 py-2 text-sm text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500/40 transition-all">
                </div>

                <!-- Date -->
                <input type="date" name="date" value="{{ request('date', today()->toDateString()) }}" class="bg-slate-800/80 border border-white/10 rounded-xl px-4 py-2 text-sm text-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500/40 transition-all">

                <!-- Status -->
                <select name="status" class="bg-slate-800/80 border border-white/10 rounded-xl px-4 py-2 text-sm text-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500/40 transition-all">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="done" {{ request('status') === 'done' ? 'selected' : '' }}>Done</option>
                    <option value="escalated" {{ request('status') === 'escalated' ? 'selected' : '' }}>Escalated</option>
                </select>

                <!-- Priority -->
                <select name="priority" class="bg-slate-800/80 border border-white/10 rounded-xl px-4 py-2 text-sm text-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500/40 transition-all">
                    <option value="">All Priority</option>
                    <option value="critical" {{ request('priority') === 'critical' ? 'selected' : '' }}>Critical</option>
                    <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>High</option>
                    <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Low</option>
                </select>

                <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-500 hover:bg-indigo-400 text-white text-sm font-medium transition-colors shadow-lg shadow-indigo-500/20">Filter</button>
                <a href="{{ route('activities.index') }}" class="px-4 py-2 rounded-xl border border-white/10 text-slate-400 hover:text-white hover:border-white/20 text-sm font-medium transition-colors text-center">Reset</a>
            </div>
        </div>
    </form>

    {{-- ── Create button ───────────────────────────────────────────────── --}}
    <div class="flex items-center justify-between">
        <p class="text-sm text-slate-500">{{ $activities->total() }} activities found</p>
        @can('create', App\Models\Activity::class)
        <a href="{{ route('activities.create') }}" class="flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-500 hover:bg-indigo-400 text-white text-sm font-medium transition-colors shadow-lg shadow-indigo-500/20">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
            New Activity
        </a>
        @endcan
    </div>

    {{-- ── Activity Cards ──────────────────────────────────────────────── --}}
    @if ($activities->isEmpty())
        <div class="bg-slate-900/60 border border-white/5 rounded-2xl flex flex-col items-center justify-center py-20 text-center">
            <svg class="w-14 h-14 text-slate-700 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            <p class="text-slate-400 font-medium">No activities found</p>
            <p class="text-slate-600 text-sm mt-1">Try adjusting your filters</p>
        </div>
    @else
        <div class="grid grid-cols-1 gap-3">
            @foreach ($activities as $activity)
            <div class="bg-slate-900/60 border border-white/5 hover:border-white/10 rounded-2xl p-5 transition-all group">
                <div class="flex flex-col md:flex-row md:items-center gap-4">

                    <!-- Priority indicator -->
                    <div class="shrink-0 w-1 self-stretch rounded-full {{ match($activity->priority) { 'critical' => 'bg-red-500', 'high' => 'bg-orange-500', 'medium' => 'bg-amber-500', default => 'bg-emerald-500' } }}"></div>

                    <!-- Main info -->
                    <div class="flex-1 min-w-0 space-y-2">
                        <div class="flex items-start gap-3 flex-wrap">
                            <h3 class="font-semibold text-slate-200 group-hover:text-white transition-colors">{{ $activity->title }}</h3>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[11px] font-semibold uppercase tracking-wider border {{ $activity->priority_color }}">
                                {{ $activity->priority }}
                            </span>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-[11px] font-semibold uppercase tracking-wider border {{ $activity->status_color }}">
                                @if ($activity->status === 'pending') <span class="w-1.5 h-1.5 rounded-full bg-amber-400 pulse-dot"></span> @endif
                                {{ str_replace('_', ' ', $activity->status) }}
                            </span>
                            @if ($activity->is_overdue)
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[11px] font-semibold uppercase tracking-wider bg-red-500/20 text-red-400 border border-red-500/30">
                                    ⚠ OVERDUE
                                </span>
                            @endif
                        </div>
                        @if ($activity->description)
                            <p class="text-sm text-slate-500 line-clamp-1">{{ $activity->description }}</p>
                        @endif
                        <div class="flex items-center gap-4 text-xs text-slate-600 flex-wrap">
                            <span class="font-mono">{{ $activity->activity_type_label }}</span>
                            <span>·</span>
                            <span>{{ $activity->activity_date->format('M d, Y') }}</span>
                            <span>·</span>
                            <span>by {{ $activity->creator->full_name }}</span>
                            @if ($activity->latestUpdate)
                                <span>·</span>
                                <span>Updated {{ $activity->latestUpdate->created_at->diffForHumans() }} by {{ $activity->latestUpdate->personnel_name }}</span>
                            @endif
                        </div>
                    </div>

                    <!-- Assigned person -->
                    @if ($activity->assignedUser)
                    <div class="shrink-0 flex items-center gap-2">
                        <img src="{{ $activity->assignedUser->profile_photo_url }}" class="w-8 h-8 rounded-full ring-2 ring-slate-700" alt="">
                        <div class="hidden md:block">
                            <p class="text-xs font-medium text-slate-300">{{ $activity->assignedUser->full_name }}</p>
                            <p class="text-[11px] text-slate-600">{{ $activity->assignedUser->primary_role_label }}</p>
                        </div>
                    </div>
                    @endif

                    <!-- Actions -->
                    <div class="shrink-0 flex items-center gap-2">
                        @can('updateStatus', $activity)
                        <button onclick="openStatusModal('{{ $activity->id }}', '{{ $activity->status }}')"
                                class="px-3 py-1.5 rounded-lg bg-indigo-500/10 hover:bg-indigo-500/20 text-indigo-400 text-xs font-medium transition-colors">
                            Update Status
                        </button>
                        @endcan
                        <a href="{{ route('activities.show', $activity) }}"
                           class="p-1.5 rounded-lg text-slate-600 hover:text-slate-300 hover:bg-white/5 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-4">{{ $activities->links() }}</div>
    @endif
</div>

{{-- ── Status Update Modal ─────────────────────────────────────────────── --}}
<div id="statusModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" x-data>
    <div class="bg-slate-900 border border-white/10 rounded-2xl p-6 w-full max-w-md shadow-2xl" @click.stop>
        <h3 class="text-lg font-semibold text-slate-200 mb-5">Update Activity Status</h3>
        <form id="statusForm" method="POST">
            @csrf
            @method('PATCH')
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-1.5">New Status</label>
                    <select name="status" id="statusSelect" class="w-full bg-slate-800 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500/40">
                        <option value="pending">Pending</option>
                        <option value="in_progress">In Progress</option>
                        <option value="done">Done</option>
                        <option value="escalated">Escalated</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-1.5">Remarks <span class="text-slate-600">(optional)</span></label>
                    <textarea name="remarks" rows="3" placeholder="Add a remark about this update..." class="w-full bg-slate-800 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500/40 resize-none"></textarea>
                </div>
                <div class="flex gap-3 pt-1">
                    <button type="button" onclick="closeStatusModal()" class="flex-1 py-2.5 rounded-xl border border-white/10 text-slate-400 hover:text-white text-sm font-medium transition-colors">Cancel</button>
                    <button type="submit" class="flex-1 py-2.5 rounded-xl bg-indigo-500 hover:bg-indigo-400 text-white text-sm font-medium transition-colors shadow-lg shadow-indigo-500/20">Update Status</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openStatusModal(activityId, currentStatus) {
    document.getElementById('statusModal').classList.remove('hidden');
    document.getElementById('statusForm').action = `/activities/${activityId}/status`;
    document.getElementById('statusSelect').value = currentStatus;
}
function closeStatusModal() {
    document.getElementById('statusModal').classList.add('hidden');
}
document.getElementById('statusModal').addEventListener('click', function(e) {
    if (e.target === this) closeStatusModal();
});
</script>
@endpush
