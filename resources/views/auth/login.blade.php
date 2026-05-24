<x-auth-layout>
    <div class="w-full max-w-md relative z-10">
        <!-- Logo -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-br from-indigo-500 to-violet-600 shadow-2xl shadow-indigo-500/40 mb-4">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-white" style="font-family: var(--font-display)">AppTrack Pro</h1>
            <p class="text-slate-500 text-sm mt-1">Application Support Activity Management</p>
        </div>

        <!-- Card -->
        <div class="bg-slate-900/80 backdrop-blur-xl border border-white/10 rounded-2xl p-8 shadow-2xl shadow-black/50">
            <h2 class="text-lg font-semibold text-slate-200 mb-6">Sign in to your account</h2>

            <!-- Session Status -->
            @if (session('status'))
                <div class="mb-4 text-sm text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 rounded-lg px-4 py-3">{{ session('status') }}</div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-1.5">Email address</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                           class="w-full bg-slate-800/80 border {{ $errors->has('email') ? 'border-red-500/50' : 'border-white/10' }} rounded-xl px-4 py-2.5 text-sm text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500/50 transition-all"
                           placeholder="you@company.com">
                    @error('email') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                </div>

                <!-- Password -->
                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-1.5">Password</label>
                    <input type="password" name="password" required autocomplete="current-password"
                           class="w-full bg-slate-800/80 border {{ $errors->has('password') ? 'border-red-500/50' : 'border-white/10' }} rounded-xl px-4 py-2.5 text-sm text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500/50 transition-all"
                           placeholder="••••••••">
                    @error('password') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                </div>

                <!-- Remember + Forgot -->
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded border-white/20 bg-slate-800 text-indigo-500 focus:ring-indigo-500/30">
                        <span class="text-sm text-slate-400">Remember me</span>
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-sm text-indigo-400 hover:text-indigo-300 transition-colors">Forgot password?</a>
                    @endif
                </div>

                <!-- Submit -->
                <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-indigo-500 hover:bg-indigo-400 text-white text-sm font-semibold transition-all duration-150 shadow-lg shadow-indigo-500/30 hover:shadow-indigo-500/50 hover:-translate-y-0.5 active:translate-y-0">
                    Sign In
                </button>
            </form>
        </div>

        <p class="text-center text-xs text-slate-600 mt-6">
            &copy; {{ date('Y') }} AppTrack Pro — Npontu Technologies
        </p>
    </div>
</x-auth-layout>
