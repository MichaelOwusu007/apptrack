@extends('layouts.app')
@section('title', 'Create User')
@section('page-title', 'Create User')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-slate-900/60 border border-white/5 rounded-2xl p-6 md:p-8">
        <div class="flex items-center gap-3 mb-8">
            <div class="w-10 h-10 rounded-xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center">
                <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
            </div>
            <div>
                <h2 class="text-lg font-semibold text-slate-200">Create New User</h2>
                <p class="text-sm text-slate-500">Add a team member to the system</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-5">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-1.5">Full Name <span class="text-red-400">*</span></label>
                    <input type="text" name="full_name" value="{{ old('full_name') }}" required
                           class="w-full bg-slate-800/80 border {{ $errors->has('full_name') ? 'border-red-500/50' : 'border-white/10' }} rounded-xl px-4 py-2.5 text-sm text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition-all"
                           placeholder="John Doe">
                    @error('full_name') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-1.5">Employee ID <span class="text-red-400">*</span></label>
                    <input type="text" name="employee_id" value="{{ old('employee_id') }}" required
                           class="w-full bg-slate-800/80 border {{ $errors->has('employee_id') ? 'border-red-500/50' : 'border-white/10' }} rounded-xl px-4 py-2.5 text-sm text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition-all font-mono"
                           placeholder="EMP-001">
                    @error('employee_id') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-400 mb-1.5">Email Address <span class="text-red-400">*</span></label>
                <input type="email" name="email" value="{{ old('email') }}" required
                       class="w-full bg-slate-800/80 border {{ $errors->has('email') ? 'border-red-500/50' : 'border-white/10' }} rounded-xl px-4 py-2.5 text-sm text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition-all"
                       placeholder="john@company.com">
                @error('email') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-1.5">Phone Number</label>
                    <input type="text" name="phone_number" value="{{ old('phone_number') }}"
                           class="w-full bg-slate-800/80 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition-all"
                           placeholder="+233 XX XXX XXXX">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-1.5">Department</label>
                    <input type="text" name="department" value="{{ old('department') }}"
                           class="w-full bg-slate-800/80 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition-all"
                           placeholder="Applications Support">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-400 mb-1.5">System Role <span class="text-red-400">*</span></label>
                <select name="role" required class="w-full bg-slate-800/80 border {{ $errors->has('role') ? 'border-red-500/50' : 'border-white/10' }} rounded-xl px-4 py-2.5 text-sm text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition-all">
                    <option value="">Select a role...</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->name }}" {{ old('role') === $role->name ? 'selected' : '' }}>
                            {{ ucwords(str_replace('_', ' ', $role->name)) }}
                        </option>
                    @endforeach
                </select>
                @error('role') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-1.5">Password <span class="text-red-400">*</span></label>
                    <input type="password" name="password" required
                           class="w-full bg-slate-800/80 border {{ $errors->has('password') ? 'border-red-500/50' : 'border-white/10' }} rounded-xl px-4 py-2.5 text-sm text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition-all"
                           placeholder="Min. 8 characters">
                    @error('password') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-1.5">Confirm Password <span class="text-red-400">*</span></label>
                    <input type="password" name="password_confirmation" required
                           class="w-full bg-slate-800/80 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition-all"
                           placeholder="Repeat password">
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <a href="{{ route('admin.users.index') }}" class="flex-1 py-2.5 rounded-xl border border-white/10 text-slate-400 hover:text-white text-sm font-medium transition-colors text-center">Cancel</a>
                <button type="submit" class="flex-1 py-2.5 rounded-xl bg-indigo-500 hover:bg-indigo-400 text-white text-sm font-semibold transition-colors shadow-lg shadow-indigo-500/20">Create User</button>
            </div>
        </form>
    </div>
</div>
@endsection
