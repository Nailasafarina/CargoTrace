@extends('layouts.guest')

@section('content')
<div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 sm:p-8 shadow-2xl backdrop-blur-xl">
    <div class="text-center mb-6">
        <h1 class="text-2xl font-bold text-white tracking-tight">Sign in to CargoTrace</h1>
        <p class="text-xs text-slate-400 mt-1">Access global shipment operations and tracking</p>
    </div>

    <!-- Quick Demo Accounts Selector (Vanilla JS Quick Fill) -->
    <div class="mb-6 p-3.5 bg-slate-950/70 border border-slate-800 rounded-xl">
        <div class="flex items-center justify-between mb-2">
            <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Demo Credentials</span>
            <span class="text-[10px] text-sky-400">Click to autofill</span>
        </div>
        <div class="grid grid-cols-3 gap-2">
            <button type="button" onclick="fillDemo('admin@cargotrace.test', 'Password123!')" class="px-2 py-1.5 rounded-lg bg-purple-500/10 hover:bg-purple-500/20 text-purple-300 border border-purple-500/30 text-xs font-medium transition cursor-pointer text-center">
                Admin
            </button>
            <button type="button" onclick="fillDemo('operator@cargotrace.test', 'Password123!')" class="px-2 py-1.5 rounded-lg bg-amber-500/10 hover:bg-amber-500/20 text-amber-300 border border-amber-500/30 text-xs font-medium transition cursor-pointer text-center">
                Operator
            </button>
            <button type="button" onclick="fillDemo('customer1@cargotrace.test', 'Password123!')" class="px-2 py-1.5 rounded-lg bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-xs font-medium transition cursor-pointer text-center">
                Customer
            </button>
        </div>
    </div>

    <!-- Login Form -->
    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-xs font-medium text-slate-300 mb-1.5">Email Address</label>
            <div class="relative">
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    autocomplete="username"
                    class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent transition"
                    placeholder="name@cargotrace.test"
                />
            </div>
            @error('email')
                <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label for="password" class="block text-xs font-medium text-slate-300">Password</label>
            </div>
            <div class="relative">
                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent transition"
                    placeholder="••••••••••••"
                />
            </div>
            @error('password')
                <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between text-xs">
            <label class="flex items-center gap-2 cursor-pointer text-slate-300">
                <input
                    type="checkbox"
                    name="remember"
                    class="w-4 h-4 rounded border-slate-700 bg-slate-950 text-sky-500 focus:ring-sky-500/20"
                />
                Remember this device
            </label>
        </div>

        <!-- Submit Button -->
        <div class="pt-2">
            <button
                type="submit"
                class="w-full py-2.5 px-4 rounded-xl bg-gradient-to-r from-sky-500 to-indigo-600 hover:from-sky-400 hover:to-indigo-500 text-white text-sm font-semibold shadow-lg shadow-sky-500/25 transition duration-200 cursor-pointer"
            >
                Sign In
            </button>
        </div>
    </form>

    <div class="mt-6 pt-5 border-t border-slate-800 text-center text-xs text-slate-400">
        New customer?
        <a href="{{ route('register') }}" class="font-medium text-sky-400 hover:text-sky-300 transition">Create an account</a>
    </div>
</div>

<script>
    function fillDemo(email, password) {
        document.getElementById('email').value = email;
        document.getElementById('password').value = password;
    }
</script>
@endsection
