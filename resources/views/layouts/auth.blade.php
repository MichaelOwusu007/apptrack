<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'AppTrack Pro') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=Syne:wght@700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root { --font-display: 'Syne', sans-serif; --font-body: 'DM Sans', sans-serif; }
        * { font-family: var(--font-body); }
        .grid-bg {
            background-image: linear-gradient(rgba(99,102,241,0.06) 1px, transparent 1px),
                              linear-gradient(90deg, rgba(99,102,241,0.06) 1px, transparent 1px);
            background-size: 40px 40px;
        }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen antialiased">
    <div class="min-h-screen grid-bg flex flex-col items-center justify-center p-4 relative overflow-hidden">
        <!-- Ambient orbs -->
        <div class="absolute top-1/4 left-1/4 w-96 h-96 rounded-full bg-indigo-600/10 blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-1/4 right-1/4 w-96 h-96 rounded-full bg-violet-600/10 blur-3xl pointer-events-none"></div>

        {{ $slot }}
    </div>
</body>
</html>
