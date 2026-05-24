@extends('layouts.app')
@section('title', 'Create Activity')
@section('page-title', 'New Activity')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-slate-900/60 border border-white/5 rounded-2xl p-6 md:p-8">
        <div class="flex items-center gap-3 mb-8">
            <div class="w-10 h-10 rounded-xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center">
                <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            </div>
            <div>
                <h2 class="text-lg font-semibold text-slate-200">Create Activity</h2>
                <p class="text-sm text-slate-500">Add a new operational task to track</p>
            </div>
        </div>

        <form method="POST" action="{{ route('activities.store') }}" class="space-y-5">
            @csrf

            <!-- Title -->
            <div>
                <label class="block text-sm font-medium text-slate-400 mb-1.5">Activity Title <span class="text-red-400">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}" required
                       class="w-full bg-slate-800/80 border {{ $errors->has('title') ? 'border-red-500/50' : 'border-white/10' }} rounded-xl px-4 py-2.5 text-sm text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500/40 transition-all"
                       placeholder="e.g. Daily SMS Count Comparison">
                @error('title') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>

            <!-- Description -->
            <div>
                <label class="block text-sm font-medium text-slate-400 mb-1.5">Description</label>
                <textarea name="description" rows="3"
                          class="w-full bg-slate-800/80 border {{ $errors->has('description') ? 'border-red-500/50' : 'border-white/10' }} rounded-xl px-4 py-2.5 text-sm text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500/40 transition-all resize-none"
                          placeholder="Provide additional context about this activity...">{{ old('description') }}</textarea>
                @error('description') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>

            <!-- Type + Priority row -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-1.5">Activity Type <span class="text-red-400">*</span></label>
                    <select name="activity_type" required class="w-full bg-slate-800/80 border {{ $errors->has('activity_type') ? 'border-red-500/50' : 'border-white/10' }} rounded-xl px-4 py-2.5 text-sm text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500/40 transition-all">
                        <option value="">Select type...</option>
                        @foreach ($activityTypes as $key => $label)
                            <option value="{{ $key }}" {{ old('activity_type') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('activity_type') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-1.5">Priority Level <span class="text-red-400">*</span></label>
                    <select name="priority" required class="w-full bg-slate-800/80 border {{ $errors->has('priority') ? 'border-red-500/50' : 'border-white/10' }} rounded-xl px-4 py-2.5 text-sm text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500/40 transition-all">
                        <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>🟢 Low</option>
                        <option value="medium" {{ old('priority', 'medium') === 'medium' ? 'selected' : '' }}>🟡 Medium</option>
                        <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>🟠 High</option>
                        <option value="critical" {{ old('priority') === 'critical' ? 'selected' : '' }}>🔴 Critical</option>
                    </select>
                    @error('priority') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Assigned To + Date row -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-1.5">Assign To</label>
                    <select name="assigned_to" class="w-full bg-slate-800/80 border {{ $errors->has('assigned_to') ? 'border-red-500/50' : 'border-white/10' }} rounded-xl px-4 py-2.5 text-sm text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500/40 transition-all">
                        <option value="">Unassigned</option>
                        @foreach ($personnel as $person)
                            <option value="{{ $person->id }}" {{ old('assigned_to') === $person->id ? 'selected' : '' }}>
                                {{ $person->full_name }} ({{ $person->employee_id }})
                            </option>
                        @endforeach
                    </select>
                    @error('assigned_to') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-1.5">Activity Date <span class="text-red-400">*</span></label>
                    <input type="date" name="activity_date" value="{{ old('activity_date', today()->toDateString()) }}" required
                           class="w-full bg-slate-800/80 border {{ $errors->has('activity_date') ? 'border-red-500/50' : 'border-white/10' }} rounded-xl px-4 py-2.5 text-sm text-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500/40 transition-all">
                    @error('activity_date') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Remarks -->
            <div>
                <label class="block text-sm font-medium text-slate-400 mb-1.5">Initial Remarks</label>
                <textarea name="remarks" rows="2"
                          class="w-full bg-slate-800/80 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500/40 transition-all resize-none"
                          placeholder="Any initial notes or observations...">{{ old('remarks') }}</textarea>
            </div>

            <!-- Actions -->
            <div class="flex gap-3 pt-2">
                <a href="{{ route('activities.index') }}" class="flex-1 py-2.5 rounded-xl border border-white/10 text-slate-400 hover:text-white text-sm font-medium transition-colors text-center">
                    Cancel
                </a>
                <button type="submit" class="flex-1 py-2.5 rounded-xl bg-indigo-500 hover:bg-indigo-400 text-white text-sm font-semibold transition-colors shadow-lg shadow-indigo-500/20">
                    Create Activity
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
