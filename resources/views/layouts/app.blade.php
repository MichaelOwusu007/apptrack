<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ darkMode: document.documentElement.classList.contains('dark'), sidebarOpen: false }" x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val ? 'true' : 'false'))" :class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'AppTrack Pro') }} — @yield('title', 'Dashboard')</title>

    <script>
        (() => {
            const storedTheme = localStorage.getItem('darkMode');
            const shouldUseDark = storedTheme === null ? true : storedTheme === 'true';

            document.documentElement.classList.toggle('dark', shouldUseDark);
        })();
    </script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&family=Syne:wght@700;800&display=swap" rel="stylesheet">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        :root {
            --font-display: 'Syne', sans-serif;
            --font-body: 'DM Sans', sans-serif;
            --font-mono: 'JetBrains Mono', monospace;
            --sidebar-w: 260px;
            --header-h: 64px;
        }

        * { font-family: var(--font-body); }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(99,102,241,0.3); border-radius: 9999px; }

        /* Status pulse animation */
        @keyframes pulse-dot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50%       { opacity: 0.5; transform: scale(0.8); }
        }
        .pulse-dot { animation: pulse-dot 2s cubic-bezier(0.4, 0, 0.6, 1) infinite; }

        /* Toast slide */
        @keyframes slide-in {
            from { transform: translateX(120%); opacity: 0; }
            to   { transform: translateX(0); opacity: 1; }
        }
        .toast-in { animation: slide-in 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    </style>
</head>

<body class="bg-slate-950 text-slate-100 antialiased min-h-screen" x-cloak>

    <!-- ── Sidebar ──────────────────────────────────────────────────── -->
    <aside
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
        class="fixed inset-y-0 left-0 z-50 w-[260px] bg-slate-900 border-r border-white/5 flex flex-col transition-transform duration-300"
    >
        <!-- Logo -->
        <div class="h-16 flex items-center px-6 border-b border-white/5 shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center shadow-lg shadow-indigo-500/30">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <span class="font-bold text-white tracking-tight" style="font-family: var(--font-display)">AppTrack<span class="text-indigo-400"> Pro</span></span>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">

            @php
                $navLinks = [
                    ['route' => 'dashboard',       'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6', 'label' => 'Dashboard'],
                    ['route' => 'activities.index','icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01', 'label' => 'Activities'],
                    ['route' => 'reports.index',   'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'label' => 'Reports'],
                    ['route' => 'notifications.index','icon' => 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9', 'label' => 'Notifications'],
                ];
            @endphp

            @foreach ($navLinks as $link)
            <a href="{{ route($link['route']) }}"
               class="group flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150
                      {{ request()->routeIs(explode('.', $link['route'])[0] . '*')
                         ? 'bg-indigo-500/15 text-indigo-400 shadow-sm'
                         : 'text-slate-400 hover:text-slate-200 hover:bg-white/5' }}">
                <svg class="w-4 h-4 shrink-0 {{ request()->routeIs(explode('.', $link['route'])[0] . '*') ? 'text-indigo-400' : 'text-slate-500 group-hover:text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $link['icon'] }}"/>
                </svg>
                {{ $link['label'] }}
                @if ($link['route'] === 'notifications.index')
                    @php $unread = auth()->user()->unreadNotifications()->count(); @endphp
                    @if ($unread > 0)
                        <span class="ml-auto flex h-5 min-w-5 items-center justify-center rounded-full bg-red-500 px-1.5 text-[10px] font-bold text-white">{{ $unread }}</span>
                    @endif
                @endif
            </a>
            @endforeach

            @role('admin')
            <div class="pt-3 pb-1">
                <p class="px-3 text-[10px] font-semibold uppercase tracking-widest text-slate-600">Administration</p>
            </div>
            <a href="{{ route('admin.users.index') }}"
               class="group flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150
                      {{ request()->routeIs('admin.*') ? 'bg-violet-500/15 text-violet-400' : 'text-slate-400 hover:text-slate-200 hover:bg-white/5' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                User Management
            </a>
            <a href="{{ route('reports.audit-logs') }}"
               class="group flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150
                      {{ request()->routeIs('reports.audit-logs') ? 'bg-violet-500/15 text-violet-400' : 'text-slate-400 hover:text-slate-200 hover:bg-white/5' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Audit Logs
            </a>
            @endrole
        </nav>

        <!-- User card -->
        <div class="border-t border-white/5 p-3">
            <div class="flex items-center gap-3 rounded-xl p-2.5 hover:bg-white/5 transition-colors">
                <img src="{{ auth()->user()->profile_photo_url }}" class="w-9 h-9 rounded-full ring-2 ring-indigo-500/30 object-cover" alt="">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-slate-200 truncate">{{ auth()->user()->full_name }}</p>
                    <p class="text-xs text-slate-500 truncate">{{ auth()->user()->primary_role_label }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="p-1.5 rounded-lg text-slate-500 hover:text-red-400 hover:bg-red-400/10 transition-colors" title="Logout">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- ── Mobile overlay ────────────────────────────────────────────── -->
    <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 z-40 bg-black/60 backdrop-blur-sm lg:hidden"></div>

    <!-- ── Main content ──────────────────────────────────────────────── -->
    <div class="lg:pl-[260px] min-h-screen flex flex-col">

        <!-- Header -->
        <header class="sticky top-0 z-30 h-16 bg-slate-950/90 backdrop-blur-xl border-b border-white/5 flex items-center px-4 md:px-6 gap-4">
            <!-- Mobile menu toggle -->
            <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-2 rounded-lg text-slate-400 hover:text-white hover:bg-white/5 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>

            <!-- Page title -->
            <div class="flex-1">
                <h1 class="text-sm font-semibold text-slate-200">@yield('page-title', 'Dashboard')</h1>
                <p class="text-xs text-slate-500">{{ now()->format('l, d M Y — H:i') }}</p>
            </div>

            <!-- Right actions -->
            <div class="flex items-center gap-2">
                <!-- Dark mode toggle -->
                <button type="button" @click="darkMode = !darkMode" :title="darkMode ? 'Switch to light mode' : 'Switch to dark mode'" :aria-label="darkMode ? 'Switch to light mode' : 'Switch to dark mode'" class="p-2 rounded-lg text-slate-400 hover:text-white hover:bg-white/5 transition-colors">
                    <svg x-show="!darkMode" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                    <svg x-show="darkMode" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </button>

                <!-- Quick create activity -->
                <a href="{{ route('activities.create') }}" class="hidden md:flex items-center gap-2 px-3.5 py-1.5 rounded-lg bg-indigo-500 hover:bg-indigo-400 text-white text-sm font-medium transition-colors shadow-lg shadow-indigo-500/25">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    New Activity
                </a>
            </div>
        </header>

        <!-- Page content -->
        <main class="flex-1 p-4 md:p-6">
            <!-- Flash messages -->
            @if (session('success'))
                <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show" x-transition class="mb-4 flex items-center gap-3 px-4 py-3 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" x-transition class="mb-4 flex items-center gap-3 px-4 py-3 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-sm">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    @livewireScripts
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    @stack('scripts')
</body>
</html>
