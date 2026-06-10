@extends('layouts.guest')
@section('title', 'Verify email')
@section('content')
    <h1 class="font-display text-2xl font-semibold mb-1">Verify your email</h1>
    <p class="text-slate text-sm mb-6 leading-relaxed">
        We sent a verification link to your inbox. In local development
        (MAIL_MAILER=log) the link is written to <code class="text-teal text-xs">storage/logs/laravel.log</code>.
    </p>
    @if (session('status'))
        <div class="border border-teal/30 bg-tealsoft text-teal text-sm px-3 py-2 rounded-lg mb-4">{{ session('status') }}</div>
    @endif
    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button class="w-full bg-teal text-white font-medium py-2.5 rounded-lg hover:bg-teal/90 transition">Resend verification link</button>
    </form>
    <form method="POST" action="{{ route('logout') }}" class="mt-3">
        @csrf
        <button class="w-full text-sm text-slate hover:text-coral transition">Sign out</button>
    </form>
@endsection
