@extends('layouts.app')
@section('title', 'User Management')
@section('page-title', 'User Management')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <p class="text-sm text-slate-500">{{ $users->total() }} registered users</p>
        <a href="{{ route('admin.users.create') }}" class="flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-500 hover:bg-indigo-400 text-white text-sm font-medium transition-colors shadow-lg shadow-indigo-500/20">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
            Add User
        </a>
    </div>

    <div class="bg-slate-900/60 border border-white/5 rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-800/50 border-b border-white/5 text-[11px] uppercase tracking-wider text-slate-500">
                        <th class="text-left px-5 py-3 font-medium">User</th>
                        <th class="text-left px-4 py-3 font-medium">Employee ID</th>
                        <th class="text-left px-4 py-3 font-medium">Department</th>
                        <th class="text-left px-4 py-3 font-medium">Role</th>
                        <th class="text-left px-4 py-3 font-medium">Status</th>
                        <th class="text-left px-4 py-3 font-medium">Activities</th>
                        <th class="text-left px-4 py-3 font-medium">Last Login</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse ($users as $user)
                    <tr class="hover:bg-white/[0.02] transition-colors">
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-3">
                                <img src="{{ $user->profile_photo_url }}" class="w-9 h-9 rounded-full ring-2 ring-slate-700 object-cover" alt="">
                                <div>
                                    <p class="font-medium text-slate-200">{{ $user->full_name }}</p>
                                    <p class="text-xs text-slate-500">{{ $user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3.5 font-mono text-sm text-slate-400">{{ $user->employee_id }}</td>
                        <td class="px-4 py-3.5 text-sm text-slate-400">{{ $user->department ?? '—' }}</td>
                        <td class="px-4 py-3.5">
                            @php
                                $roleColors = ['admin' => 'text-violet-400 bg-violet-400/10 border-violet-400/20', 'supervisor' => 'text-blue-400 bg-blue-400/10 border-blue-400/20', 'support_staff' => 'text-slate-300 bg-white/5 border-white/10'];
                                $roleColor = $roleColors[$user->primary_role] ?? 'text-slate-400 bg-white/5 border-white/10';
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-semibold border {{ $roleColor }}">
                                {{ $user->primary_role_label }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5">
                            @if ($user->is_active)
                                <span class="inline-flex items-center gap-1.5 text-xs text-emerald-400"><span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>Active</span>
                            @else
                                <span class="inline-flex items-center gap-1.5 text-xs text-slate-500"><span class="w-1.5 h-1.5 rounded-full bg-slate-600"></span>Inactive</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-center">
                            <span class="text-sm text-slate-300">{{ $user->assigned_activities_count }}</span>
                            <span class="text-xs text-slate-600 ml-1">assigned</span>
                        </td>
                        <td class="px-4 py-3.5 text-xs text-slate-500">
                            {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="flex items-center gap-1">
                                <a href="{{ route('admin.users.edit', $user) }}" class="p-1.5 rounded-lg text-slate-500 hover:text-indigo-400 hover:bg-indigo-400/10 transition-colors" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                @if ($user->id !== auth()->id())
                                <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="p-1.5 rounded-lg text-slate-500 hover:text-amber-400 hover:bg-amber-400/10 transition-colors" title="{{ $user->is_active ? 'Deactivate' : 'Activate' }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $user->is_active ? 'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636' : 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' }}"/></svg>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Delete {{ $user->full_name }}? This cannot be undone.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-lg text-slate-500 hover:text-red-400 hover:bg-red-400/10 transition-colors" title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-5 py-16 text-center text-slate-600">No users found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($users->hasPages())
        <div class="px-5 py-4 border-t border-white/5">{{ $users->links() }}</div>
        @endif
    </div>
</div>
@endsection
