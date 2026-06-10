@extends('layouts.guest')
@section('title', 'Sign in')
@section('content')
    <h1 class="font-display text-2xl font-semibold mb-1">Welcome back</h1>
    <p class="text-slate text-sm mb-6">Sign in to your account.</p>

    @if ($errors->any())
        <div class="border border-coral/30 bg-coral/10 text-coral text-sm px-3 py-2 rounded-lg mb-5">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf
        <div>
            <label class="text-sm text-slate block mb-1.5">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus
                   class="w-full bg-paper border border-line rounded-lg px-3.5 py-2.5 text-sm focus:border-teal focus:ring-1 focus:ring-teal focus:outline-none transition">
        </div>
        <div>
            <label class="text-sm text-slate block mb-1.5">Password</label>
            <input type="password" name="password" required
                   class="w-full bg-paper border border-line rounded-lg px-3.5 py-2.5 text-sm focus:border-teal focus:ring-1 focus:ring-teal focus:outline-none transition">
        </div>
        <label class="flex items-center gap-2 text-sm text-slate cursor-pointer">
            <input type="checkbox" name="remember" class="accent-teal rounded"> Keep me signed in
        </label>
        <button class="w-full bg-teal text-white font-medium py-2.5 rounded-lg hover:bg-teal/90 transition">Sign in</button>
    </form>

    <p class="text-center text-sm text-slate mt-6">No account? <a href="{{ route('register') }}" class="text-teal font-medium hover:underline">Create one</a></p>
@endsection
