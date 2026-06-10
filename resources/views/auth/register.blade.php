@extends('layouts.guest')
@section('title', 'Register')
@section('content')
    <h1 class="font-display text-2xl font-semibold mb-1">Create your account</h1>
    <p class="text-slate text-sm mb-6">Start building your security awareness.</p>

    @if ($errors->any())
        <div class="border border-coral/30 bg-coral/10 text-coral text-sm px-3 py-2 rounded-lg mb-5">
            <ul class="space-y-0.5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf
        <div>
            <label class="text-sm text-slate block mb-1.5">Name</label>
            <input type="text" name="name" value="{{ old('name') }}" required autofocus
                   class="w-full bg-paper border border-line rounded-lg px-3.5 py-2.5 text-sm focus:border-teal focus:ring-1 focus:ring-teal focus:outline-none transition">
        </div>
        <div>
            <label class="text-sm text-slate block mb-1.5">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required
                   class="w-full bg-paper border border-line rounded-lg px-3.5 py-2.5 text-sm focus:border-teal focus:ring-1 focus:ring-teal focus:outline-none transition">
        </div>
        <div>
            <label class="text-sm text-slate block mb-1.5">Password <span class="text-slate/60">(min 8)</span></label>
            <input type="password" name="password" required
                   class="w-full bg-paper border border-line rounded-lg px-3.5 py-2.5 text-sm focus:border-teal focus:ring-1 focus:ring-teal focus:outline-none transition">
        </div>
        <div>
            <label class="text-sm text-slate block mb-1.5">Confirm password</label>
            <input type="password" name="password_confirmation" required
                   class="w-full bg-paper border border-line rounded-lg px-3.5 py-2.5 text-sm focus:border-teal focus:ring-1 focus:ring-teal focus:outline-none transition">
        </div>
        <button class="w-full bg-teal text-white font-medium py-2.5 rounded-lg hover:bg-teal/90 transition">Create account</button>
    </form>

    <p class="text-center text-sm text-slate mt-6">Have an account? <a href="{{ route('login') }}" class="text-teal font-medium hover:underline">Sign in</a></p>
@endsection
