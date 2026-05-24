@extends('layouts.app')
@section('title', 'Edit Activity')
@section('page-title', 'Edit Activity')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-slate-900/60 border border-white/5 rounded-2xl p-6 md:p-8">
        <div class="flex items-center gap-3 mb-8">
            <div class="w-10 h-10 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center">
                <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-lg font-semibold text-slate-200">Edit Activity</h2>
                <p class="text-sm text-slate-500 truncate max-w-sm">{{ $activity->title }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('activities.update', $activity) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-slate-400 mb-1.5">Activity Title <span class="text-red-400">*</span></label>
                <input type="text" name="title" value="{{ old('title', $activity->title) }}" required
                       class="w-full bg-slate-800/80 border {{ $errors->has('title') ? 'border-red-500/50' : 'border-white/10' }} rounded-xl px-4 py-2.5 text-sm text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition-all">
                @error('title') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-400 mb-1.5">Description</label>
                <textarea name="description" rows="3"
                          class="w-full bg-slate-800/80 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition-all resize-none"
                          placeholder="Provide additional context...">{{ old('description', $activity->description) }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-1.5">Activity Type <span class="text-red-400">*</span></label>
                    <select name="activity_type" required class="w-full bg-slate-800/80 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition-all">
                        @foreach ($activityTypes as $key => $label)
                            <option value="{{ $key }}" {{ old('activity_type', $activity->activity_type) === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-1.5">Priority Level <span class="text-red-400">*</span></label>
                    <select name="priority" required class="w-full bg-slate-800/80 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition-all">
                        <option value="low"      {{ old('priority', $activity->priority) === 'low'      ? 'selected' : '' }}>🟢 Low</option>
                        <option value="medium"   {{ old('priority', $activity->priority) === 'medium'   ? 'selected' : '' }}>🟡 Medium</option>
                        <option value="high"     {{ old('priority', $activity->priority) === 'high'     ? 'selected' : '' }}>🟠 High</option>
                        <option value="critical" {{ old('priority', $activity->priority) === 'critical' ? 'selected' : '' }}>🔴 Critical</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-1.5">Assign To</label>
                    <select name="assigned_to" class="w-full bg-slate-800/80 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition-all">
                        <option value="">Unassigned</option>
                        @foreach ($personnel as $person)
                            <option value="{{ $person->id }}" {{ old('assigned_to', $activity->assigned_to) === $person->id ? 'selected' : '' }}>
                                {{ $person->full_name }} ({{ $person->employee_id }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-1.5">Activity Date <span class="text-red-400">*</span></label>
                    <input type="date" name="activity_date" value="{{ old('activity_date', $activity->activity_date->toDateString()) }}" required
                           class="w-full bg-slate-800/80 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition-all">
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <a href="{{ route('activities.show', $activity) }}" class="flex-1 py-2.5 rounded-xl border border-white/10 text-slate-400 hover:text-white text-sm font-medium transition-colors text-center">Cancel</a>
                <button type="submit" class="flex-1 py-2.5 rounded-xl bg-indigo-500 hover:bg-indigo-400 text-white text-sm font-semibold transition-colors shadow-lg shadow-indigo-500/20">Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection
