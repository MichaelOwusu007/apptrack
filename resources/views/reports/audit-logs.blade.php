@extends('layouts.app')
@section('title', 'Audit Logs')
@section('page-title', 'Audit Logs')

@section('content')
<div class="space-y-4">

    <!-- Filter -->
    <form method="GET" action="{{ route('reports.audit-logs') }}">
        <div class="bg-slate-900/60 border border-white/5 rounded-2xl p-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <select name="action" class="bg-slate-800/80 border border-white/10 rounded-xl px-4 py-2 text-sm text-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500/40">
                    <option value="">All Actions</option>
                    <option value="login" {{ request('action') === 'login' ? 'selected' : '' }}>Login</option>
                    <option value="logout" {{ request('action') === 'logout' ? 'selected' : '' }}>Logout</option>
                    <option value="create_activity" {{ request('action') === 'create_activity' ? 'selected' : '' }}>Create Activity</option>
                    <option value="update_status" {{ request('action') === 'update_status' ? 'selected' : '' }}>Update Status</option>
                    <option value="create_user" {{ request('action') === 'create_user' ? 'selected' : '' }}>Create User</option>
                    <option value="export_pdf_report" {{ request('action') === 'export_pdf_report' ? 'selected' : '' }}>Export PDF</option>
                    <option value="export_excel_report" {{ request('action') === 'export_excel_report' ? 'selected' : '' }}>Export Excel</option>
                </select>
                <select name="user_id" class="bg-slate-800/80 border border-white/10 rounded-xl px-4 py-2 text-sm text-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500/40">
                    <option value="">All Users</option>
                    @foreach ($personnel as $person)
                        <option value="{{ $person->id }}" {{ request('user_id') === $person->id ? 'selected' : '' }}>{{ $person->full_name }}</option>
                    @endforeach
                </select>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="bg-slate-800/80 border border-white/10 rounded-xl px-4 py-2 text-sm text-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500/40">
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="bg-slate-800/80 border border-white/10 rounded-xl px-4 py-2 text-sm text-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500/40">
            </div>
            <div class="flex gap-2 mt-3">
                <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-500 hover:bg-indigo-400 text-white text-sm font-medium transition-colors">Filter</button>
                <a href="{{ route('reports.audit-logs') }}" class="px-4 py-2 rounded-xl border border-white/10 text-slate-400 hover:text-white text-sm font-medium transition-colors">Reset</a>
            </div>
        </div>
    </form>

    <div class="bg-slate-900/60 border border-white/5 rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-800/50 border-b border-white/5 text-[11px] uppercase tracking-wider text-slate-500">
                        <th class="text-left px-5 py-3 font-medium">User</th>
                        <th class="text-left px-4 py-3 font-medium">Action</th>
                        <th class="text-left px-4 py-3 font-medium">Description</th>
                        <th class="text-left px-4 py-3 font-medium">IP Address</th>
                        <th class="text-left px-4 py-3 font-medium">Timestamp</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse ($logs as $log)
                    <tr class="hover:bg-white/[0.02] transition-colors">
                        <td class="px-5 py-3.5">
                            <p class="text-sm font-medium text-slate-200">{{ $log->user_name ?? 'System' }}</p>
                            <p class="text-xs text-slate-500">{{ $log->user_role }}</p>
                        </td>
                        <td class="px-4 py-3.5">
                            @php
                                $actionColors = [
                                    'login'               => 'text-emerald-400 bg-emerald-400/10 border-emerald-400/20',
                                    'logout'              => 'text-slate-400 bg-white/5 border-white/10',
                                    'create_activity'     => 'text-blue-400 bg-blue-400/10 border-blue-400/20',
                                    'update_status'       => 'text-indigo-400 bg-indigo-400/10 border-indigo-400/20',
                                    'create_user'         => 'text-violet-400 bg-violet-400/10 border-violet-400/20',
                                    'delete_user'         => 'text-red-400 bg-red-400/10 border-red-400/20',
                                    'export_pdf_report'   => 'text-amber-400 bg-amber-400/10 border-amber-400/20',
                                    'export_excel_report' => 'text-amber-400 bg-amber-400/10 border-amber-400/20',
                                ];
                                $ac = $actionColors[$log->action] ?? 'text-slate-400 bg-white/5 border-white/10';
                            @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-mono font-medium border {{ $ac }}">
                                {{ $log->action }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5 text-sm text-slate-400 max-w-xs truncate">{{ $log->description }}</td>
                        <td class="px-4 py-3.5 font-mono text-xs text-slate-500">{{ $log->ip_address }}</td>
                        <td class="px-4 py-3.5 text-xs text-slate-500 whitespace-nowrap">{{ $log->created_at->format('M d, Y H:i:s') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-16 text-center text-slate-600">No audit logs found for the selected filters.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($logs->hasPages())
        <div class="px-5 py-4 border-t border-white/5">{{ $logs->links() }}</div>
        @endif
    </div>
</div>
@endsection
