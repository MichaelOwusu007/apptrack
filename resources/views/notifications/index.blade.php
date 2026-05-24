@extends('layouts.app')
@section('title', 'Notifications')
@section('page-title', 'Notifications')

@section('content')
<div class="max-w-3xl mx-auto space-y-4">

    <div class="flex items-center justify-between">
        <p class="text-sm text-slate-500">
            {{ $notifications->total() }} notifications
            @if ($unreadCount > 0)
                <span class="ml-2 px-2 py-0.5 rounded-full bg-red-500/20 text-red-400 text-xs font-bold">{{ $unreadCount }} unread</span>
            @endif
        </p>
        @if ($unreadCount > 0)
        <form method="POST" action="{{ route('notifications.read-all') }}">
            @csrf
            <button type="submit" class="text-sm text-indigo-400 hover:text-indigo-300 font-medium transition-colors">Mark all as read</button>
        </form>
        @endif
    </div>

    <div class="bg-slate-900/60 border border-white/5 rounded-2xl overflow-hidden divide-y divide-white/5">
        @forelse ($notifications as $notification)
        <div class="flex items-start gap-4 px-5 py-4 {{ $notification->read_at ? '' : 'bg-indigo-500/5' }} hover:bg-white/[0.02] transition-colors">
            <!-- Type icon -->
            <div class="shrink-0 mt-0.5 w-9 h-9 rounded-xl flex items-center justify-center {{ match($notification->type) { 'success' => 'bg-emerald-500/10', 'warning' => 'bg-amber-500/10', 'error' => 'bg-red-500/10', default => 'bg-blue-500/10' } }}">
                <svg class="w-4 h-4 {{ $notification->type_color }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    @if ($notification->type === 'success')
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    @elseif ($notification->type === 'error')
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    @elseif ($notification->type === 'warning')
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    @else
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    @endif
                </svg>
            </div>

            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-0.5">
                    <p class="text-sm font-semibold text-slate-200">{{ $notification->title }}</p>
                    @if (!$notification->read_at)
                        <span class="w-2 h-2 rounded-full bg-indigo-500 shrink-0"></span>
                    @endif
                </div>
                <p class="text-sm text-slate-400">{{ $notification->message }}</p>
                <p class="text-xs text-slate-600 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
            </div>

            <div class="shrink-0 flex items-center gap-2">
                @if ($notification->link)
                    <a href="{{ $notification->link }}" class="px-3 py-1.5 rounded-lg bg-white/5 hover:bg-white/10 text-slate-400 hover:text-white text-xs font-medium transition-colors">View</a>
                @endif
                @if (!$notification->read_at)
                <form method="POST" action="{{ route('notifications.read', $notification) }}">
                    @csrf
                    <button type="submit" class="p-1.5 rounded-lg text-slate-600 hover:text-slate-300 hover:bg-white/5 transition-colors" title="Mark as read">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </button>
                </form>
                @endif
            </div>
        </div>
        @empty
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <svg class="w-14 h-14 text-slate-700 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            <p class="text-slate-400 font-medium">All caught up!</p>
            <p class="text-slate-600 text-sm mt-1">No notifications at the moment</p>
        </div>
        @endforelse
    </div>

    @if ($notifications->hasPages())
    <div>{{ $notifications->links() }}</div>
    @endif
</div>
@endsection
