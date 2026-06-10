@extends('layouts.app')
@section('title', 'Profile')

@section('content')
    <div class="mb-8">
        <h1 class="font-display text-3xl font-semibold">Your profile</h1>
        <p class="text-slate text-sm mt-1">Manage your account details.</p>
    </div>

    <div class="grid lg:grid-cols-3 gap-5">
        {{-- Account card --}}
        <div class="bg-card border border-line rounded-2xl p-7 card-shadow h-fit">
            <div class="w-16 h-16 rounded-2xl bg-tealsoft text-teal flex items-center justify-center font-display text-2xl font-semibold mb-4">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <p class="font-display text-xl font-semibold">{{ $user->name }}</p>
            <p class="text-slate text-sm">{{ $user->email }}</p>
            <span class="inline-block mt-3 text-xs px-2.5 py-1 rounded-full bg-paper border border-line text-slate">{{ ucfirst($user->role) }}</span>
            @if ($user->hasVerifiedEmail())
                <p class="text-xs text-teal mt-3">✓ Email verified</p>
            @endif
        </div>

        {{-- Editable details --}}
        <div class="lg:col-span-2 bg-card border border-line rounded-2xl p-7 card-shadow">
            <h2 class="font-display text-lg font-semibold mb-5">Details</h2>
            <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="text-sm text-slate block mb-1.5">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->profile->phone ?? '') }}"
                           class="w-full bg-paper border border-line rounded-lg px-3.5 py-2.5 text-sm focus:border-teal focus:ring-1 focus:ring-teal focus:outline-none transition">
                </div>
                <div>
                    <label class="text-sm text-slate block mb-1.5">Country</label>
                    <input type="text" name="country" value="{{ old('country', $user->profile->country ?? '') }}"
                           class="w-full bg-paper border border-line rounded-lg px-3.5 py-2.5 text-sm focus:border-teal focus:ring-1 focus:ring-teal focus:outline-none transition">
                </div>
                <div>
                    <label class="text-sm text-slate block mb-1.5">Bio</label>
                    <textarea name="bio" rows="4"
                              class="w-full bg-paper border border-line rounded-lg px-3.5 py-2.5 text-sm focus:border-teal focus:ring-1 focus:ring-teal focus:outline-none transition resize-none">{{ old('bio', $user->profile->bio ?? '') }}</textarea>
                </div>
                <button class="bg-teal text-white font-medium px-6 py-2.5 rounded-lg hover:bg-teal/90 transition">Save changes</button>
            </form>
        </div>
    </div>
@endsection
