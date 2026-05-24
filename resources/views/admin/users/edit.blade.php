@extends('layouts.app')
@section('title', 'Edit User')
@section('page-title', 'Edit User')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-slate-900/60 border border-white/5 rounded-2xl p-6 md:p-8">
        <div class="flex items-center gap-4 mb-8">
            <img src="{{ $user->profile_photo_url }}" class="w-14 h-14 rounded-2xl ring-2 ring-slate-700 object-cover" alt="">
            <div>
                <h2 class="text-lg font-semibold text-slate-200">{{ $user->full_name }}</h2>
                <p class="text-sm text-slate-500">{{ $user->email }} · {{ $user->employee_id }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-1.5">Full Name <span class="text-red-400">*</span></label>
                    <input type="text" name="full_name" value="{{ old('full_name', $user->full_name) }}" required
                           class="w-full bg-slate-800/80 border {{ $errors->has('full_name') ? 'border-red-500/50' : 'border-white/10' }} rounded-xl px-4 py-2.5 text-sm text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition-all">
                    @error('full_name') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-1.5">Employee ID <span class="text-red-400">*</span></label>
                    <input type="text" name="employee_id" value="{{ old('employee_id', $user->employee_id) }}" required
                           class="w-full bg-slate-800/80 border {{ $errors->has('employee_id') ? 'border-red-500/50' : 'border-white/10' }} rounded-xl px-4 py-2.5 text-sm text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition-all font-mono">
                    @error('employee_id') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-400 mb-1.5">Email Address <span class="text-red-400">*</span></label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                       class="w-full bg-slate-800/80 border {{ $errors->has('email') ? 'border-red-500/50' : 'border-white/10' }} rounded-xl px-4 py-2.5 text-sm text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition-all">
                @error('email') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-1.5">Phone Number</label>
                    <input type="text" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}"
                           class="w-full bg-slate-800/80 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-1.5">Department</label>
                    <input type="text" name="department" value="{{ old('department', $user->department) }}"
                           class="w-full bg-slate-800/80 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition-all">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-1.5">Role <span class="text-red-400">*</span></label>
                    <select name="role" required class="w-full bg-slate-800/80 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition-all">
                        @foreach ($roles as $role)
                            <option value="{{ $role->name }}" {{ old('role', $user->primary_role) === $role->name ? 'selected' : '' }}>
                                {{ ucwords(str_replace('_', ' ', $role->name)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-1.5">Account Status</label>
                    <select name="is_active" class="w-full bg-slate-800/80 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition-all">
                        <option value="1" {{ $user->is_active ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ !$user->is_active ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <a href="{{ route('admin.users.index') }}" class="flex-1 py-2.5 rounded-xl border border-white/10 text-slate-400 hover:text-white text-sm font-medium transition-colors text-center">Cancel</a>
                <button type="submit" class="flex-1 py-2.5 rounded-xl bg-indigo-500 hover:bg-indigo-400 text-white text-sm font-semibold transition-colors shadow-lg shadow-indigo-500/20">Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection
