@extends('layouts.app')
@section('title', 'Profile')
@section('page-title', 'My Profile')

@section('content')
<div class="max-w-2xl mx-auto space-y-4">

    <!-- Profile info card -->
    <div class="bg-slate-900/60 border border-white/5 rounded-2xl p-6 md:p-8">
        <div class="flex items-center gap-5 mb-8">
            <div class="relative">
                <img src="{{ $user->profile_photo_url }}" class="w-20 h-20 rounded-2xl ring-2 ring-indigo-500/30 object-cover" alt="">
                <div class="absolute -bottom-1 -right-1 w-6 h-6 rounded-full bg-emerald-500 border-2 border-slate-900 flex items-center justify-center">
                    <span class="w-2 h-2 rounded-full bg-white"></span>
                </div>
            </div>
            <div>
                <h2 class="text-xl font-bold text-slate-100">{{ $user->full_name }}</h2>
                <p class="text-sm text-slate-400">{{ $user->email }}</p>
                <div class="flex items-center gap-2 mt-1.5">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-semibold border text-violet-400 bg-violet-400/10 border-violet-400/20">{{ $user->primary_role_label }}</span>
                    <span class="text-xs text-slate-600">{{ $user->employee_id }}</span>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('profile.update') }}" class="space-y-5">
            @csrf
            @method('PATCH')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-1.5">Full Name</label>
                    <input type="text" name="full_name" value="{{ old('full_name', $user->full_name) }}"
                           class="w-full bg-slate-800/80 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-1.5">Phone Number</label>
                    <input type="text" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}"
                           class="w-full bg-slate-800/80 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition-all">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-400 mb-1.5">Department</label>
                <input type="text" name="department" value="{{ old('department', $user->department) }}"
                       class="w-full bg-slate-800/80 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition-all">
            </div>

            <div class="pt-2 border-t border-white/5">
                <h3 class="text-sm font-semibold text-slate-300 mb-4">Change Password <span class="text-slate-500 font-normal">(optional)</span></h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-400 mb-1.5">Current Password</label>
                        <input type="password" name="current_password"
                               class="w-full bg-slate-800/80 border {{ $errors->has('current_password') ? 'border-red-500/50' : 'border-white/10' }} rounded-xl px-4 py-2.5 text-sm text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition-all">
                        @error('current_password') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-400 mb-1.5">New Password</label>
                            <input type="password" name="password"
                                   class="w-full bg-slate-800/80 border {{ $errors->has('password') ? 'border-red-500/50' : 'border-white/10' }} rounded-xl px-4 py-2.5 text-sm text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition-all">
                            @error('password') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-400 mb-1.5">Confirm New Password</label>
                            <input type="password" name="password_confirmation"
                                   class="w-full bg-slate-800/80 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition-all">
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" class="w-full py-2.5 rounded-xl bg-indigo-500 hover:bg-indigo-400 text-white text-sm font-semibold transition-colors shadow-lg shadow-indigo-500/20">
                Save Changes
            </button>
        </form>
    </div>

    <!-- Account info -->
    <div class="bg-slate-900/60 border border-white/5 rounded-2xl p-6">
        <h3 class="text-sm font-semibold text-slate-300 mb-4">Account Information</h3>
        <div class="grid grid-cols-2 gap-3">
            <div class="bg-slate-800/40 rounded-xl p-3">
                <p class="text-xs text-slate-600 uppercase tracking-wider mb-1">Member Since</p>
                <p class="text-sm font-medium text-slate-300">{{ $user->created_at->format('M d, Y') }}</p>
            </div>
            <div class="bg-slate-800/40 rounded-xl p-3">
                <p class="text-xs text-slate-600 uppercase tracking-wider mb-1">Last Login</p>
                <p class="text-sm font-medium text-slate-300">{{ $user->last_login_at?->format('M d, Y H:i') ?? 'N/A' }}</p>
            </div>
            <div class="bg-slate-800/40 rounded-xl p-3">
                <p class="text-xs text-slate-600 uppercase tracking-wider mb-1">Activities Assigned</p>
                <p class="text-sm font-medium text-slate-300">{{ $user->assignedActivities()->count() }}</p>
            </div>
            <div class="bg-slate-800/40 rounded-xl p-3">
                <p class="text-xs text-slate-600 uppercase tracking-wider mb-1">Activities Created</p>
                <p class="text-sm font-medium text-slate-300">{{ $user->createdActivities()->count() }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
