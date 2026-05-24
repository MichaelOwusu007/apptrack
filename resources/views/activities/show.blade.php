@extends('layouts.app')
@section('title', $activity->title)
@section('page-title', 'Activity Detail')

@section('content')
<div class="max-w-4xl mx-auto space-y-4">

    {{-- ── Breadcrumb ───────────────────────────────────────────────────── --}}
    <div class="flex items-center gap-2 text-sm text-slate-500">
        <a href="{{ route('activities.index') }}" class="hover:text-slate-300 transition-colors">Activities</a>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-slate-400 truncate">{{ $activity->title }}</span>
    </div>

    {{-- ── Activity card ────────────────────────────────────────────────── --}}
    <div class="bg-slate-900/60 border border-white/5 rounded-2xl overflow-hidden">
        <!-- Header stripe by priority -->
        <div class="h-1 w-full {{ match($activity->priority) { 'critical' => 'bg-red-500', 'high' => 'bg-orange-500', 'medium' => 'bg-amber-500', default => 'bg-emerald-500' } }}"></div>

        <div class="p-6">
            <div class="flex flex-col md:flex-row md:items-start gap-5">
                <div class="flex-1 space-y-3">
                    <div class="flex items-center gap-3 flex-wrap">
                        <h2 class="text-xl font-bold text-slate-100">{{ $activity->title }}</h2>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-semibold uppercase tracking-wider border {{ $activity->priority_color }}">
                            {{ $activity->priority }}
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg text-xs font-semibold uppercase tracking-wider border {{ $activity->status_color }}">
                            @if ($activity->status === 'pending') <span class="w-1.5 h-1.5 rounded-full bg-amber-400 pulse-dot"></span> @endif
                            {{ str_replace('_', ' ', $activity->status) }}
                        </span>
                        @if ($activity->is_overdue)
                            <span class="px-2.5 py-0.5 rounded-lg text-xs font-semibold uppercase tracking-wider bg-red-500/20 text-red-400 border border-red-500/30">⚠ Overdue</span>
                        @endif
                    </div>

                    @if ($activity->description)
                        <p class="text-sm text-slate-400 leading-relaxed">{{ $activity->description }}</p>
                    @endif

                    <!-- Meta grid -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 pt-2">
                        @php
                            $meta = [
                                ['label' => 'Type', 'value' => $activity->activity_type_label],
                                ['label' => 'Date', 'value' => $activity->activity_date->format('M d, Y')],
                                ['label' => 'Created By', 'value' => $activity->creator->full_name],
                                ['label' => 'Created At', 'value' => $activity->created_at->format('M d, H:i')],
                            ];
                        @endphp
                        @foreach ($meta as $m)
                        <div class="bg-slate-800/60 rounded-xl p-3">
                            <p class="text-[11px] text-slate-600 uppercase tracking-wider mb-1">{{ $m['label'] }}</p>
                            <p class="text-sm font-medium text-slate-300">{{ $m['value'] }}</p>
                        </div>
                        @endforeach
                    </div>

                    <!-- Assigned user -->
                    @if ($activity->assignedUser)
                    <div class="flex items-center gap-3 bg-slate-800/40 rounded-xl p-3">
                        <img src="{{ $activity->assignedUser->profile_photo_url }}" class="w-10 h-10 rounded-full ring-2 ring-slate-700" alt="">
                        <div>
                            <p class="text-xs text-slate-500 uppercase tracking-wider">Assigned To</p>
                            <p class="text-sm font-medium text-slate-200">{{ $activity->assignedUser->full_name }}</p>
                            <p class="text-xs text-slate-500">{{ $activity->assignedUser->department }} · {{ $activity->assignedUser->primary_role_label }}</p>
                        </div>
                    </div>
                    @endif

                    @if ($activity->remarks)
                    <div class="bg-amber-500/5 border border-amber-500/15 rounded-xl p-3">
                        <p class="text-xs text-amber-500 uppercase tracking-wider mb-1">Latest Remarks</p>
                        <p class="text-sm text-slate-300">{{ $activity->remarks }}</p>
                    </div>
                    @endif
                </div>

                <!-- Action buttons -->
                <div class="shrink-0 flex flex-row md:flex-col gap-2">
                    @can('updateStatus', $activity)
                    <button onclick="document.getElementById('updateModal').classList.remove('hidden')"
                            class="flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-500 hover:bg-indigo-400 text-white text-sm font-medium transition-colors shadow-lg shadow-indigo-500/20">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        Update Status
                    </button>
                    @endcan

                    @can('update', $activity)
                    <a href="{{ route('activities.edit', $activity) }}"
                       class="flex items-center gap-2 px-4 py-2 rounded-xl border border-white/10 hover:border-white/20 text-slate-400 hover:text-white text-sm font-medium transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Edit
                    </a>
                    @endcan

                    @can('delete', $activity)
                    <form method="POST" action="{{ route('activities.destroy', $activity) }}"
                          onsubmit="return confirm('Delete this activity? This action cannot be undone.')">
                        @csrf @method('DELETE')
                        <button type="submit" class="w-full flex items-center gap-2 px-4 py-2 rounded-xl border border-red-500/20 hover:bg-red-500/10 text-red-400 hover:text-red-300 text-sm font-medium transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            Delete
                        </button>
                    </form>
                    @endcan
                </div>
            </div>
        </div>
    </div>

    {{-- ── Update History Timeline ──────────────────────────────────────── --}}
    <div class="bg-slate-900/60 border border-white/5 rounded-2xl p-6">
        <h3 class="text-sm font-semibold text-slate-200 mb-6 flex items-center gap-2">
            <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Update History
            <span class="px-2 py-0.5 rounded-full bg-indigo-500/20 text-indigo-400 text-xs font-bold">{{ $activity->updates->count() }}</span>
        </h3>

        @if ($activity->updates->isEmpty())
            <div class="flex flex-col items-center justify-center py-10 text-center">
                <svg class="w-10 h-10 text-slate-700 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-sm text-slate-600">No updates recorded yet</p>
            </div>
        @else
            <div class="relative">
                <!-- Timeline line -->
                <div class="absolute left-5 top-0 bottom-0 w-px bg-white/5"></div>

                <div class="space-y-4 pl-14">
                    @foreach ($activity->updates as $update)
                    <div class="relative">
                        <!-- Avatar -->
                        <div class="absolute -left-14 top-1">
                            <img src="{{ $update->updatedBy?->profile_photo_url }}" class="w-8 h-8 rounded-full ring-2 ring-slate-900 border border-white/10 object-cover" alt="">
                        </div>

                        <div class="bg-slate-800/40 rounded-xl p-4">
                            <div class="flex items-start justify-between gap-3 flex-wrap mb-3">
                                <div>
                                    <p class="text-sm font-semibold text-slate-200">{{ $update->personnel_name }}</p>
                                    <p class="text-xs text-slate-500">{{ $update->personnel_role }} · {{ $update->personnel_department }}</p>
                                </div>
                                <p class="text-xs text-slate-500 shrink-0">{{ $update->created_at->format('M d, Y — H:i:s') }}</p>
                            </div>

                            <!-- Status change -->
                            <div class="flex items-center gap-2 mb-3">
                                @if ($update->previous_status)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold uppercase border {{ \App\Models\Activity::STATUS_COLORS[$update->previous_status] ?? 'text-slate-400 border-slate-600' }}">
                                    {{ str_replace('_', ' ', $update->previous_status) }}
                                </span>
                                <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                                @endif
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold uppercase border {{ \App\Models\Activity::STATUS_COLORS[$update->new_status] ?? 'text-slate-400 border-slate-600' }}">
                                    {{ str_replace('_', ' ', $update->new_status) }}
                                </span>
                            </div>

                            @if ($update->remarks)
                            <p class="text-sm text-slate-300 bg-slate-900/60 rounded-lg px-3 py-2">{{ $update->remarks }}</p>
                            @endif

                            <!-- Technical info -->
                            <div class="mt-3 flex items-center gap-4 text-[11px] text-slate-600 font-mono">
                                <span>{{ $update->ip_address }}</span>
                                <span>·</span>
                                <span class="truncate">{{ $update->browser }}</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>

{{-- ── Update Status Modal ──────────────────────────────────────────────── --}}
<div id="updateModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
    <div class="bg-slate-900 border border-white/10 rounded-2xl p-6 w-full max-w-md shadow-2xl">
        <h3 class="text-lg font-semibold text-slate-200 mb-5">Update Activity Status</h3>
        <form method="POST" action="{{ route('activities.update-status', $activity) }}">
            @csrf
            @method('PATCH')
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-1.5">New Status <span class="text-red-400">*</span></label>
                    <select name="status" required class="w-full bg-slate-800 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500/40">
                        <option value="pending" {{ $activity->status === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="in_progress" {{ $activity->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="done" {{ $activity->status === 'done' ? 'selected' : '' }}>Done</option>
                        <option value="escalated" {{ $activity->status === 'escalated' ? 'selected' : '' }}>Escalated</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-1.5">Remarks</label>
                    <textarea name="remarks" rows="3" class="w-full bg-slate-800 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500/40 resize-none" placeholder="Describe what was done or observed..."></textarea>
                </div>
                <div class="flex gap-3">
                    <button type="button" onclick="document.getElementById('updateModal').classList.add('hidden')" class="flex-1 py-2.5 rounded-xl border border-white/10 text-slate-400 hover:text-white text-sm font-medium transition-colors">Cancel</button>
                    <button type="submit" class="flex-1 py-2.5 rounded-xl bg-indigo-500 hover:bg-indigo-400 text-white text-sm font-semibold transition-colors shadow-lg shadow-indigo-500/20">Save Update</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
