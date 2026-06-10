@extends('layouts.app')
@section('title', 'Result — '.$simulation->title)

@php
    $rc = $fellForIt ? 'coral' : 'teal';

    // Type-specific "what gave it away" — view-level education,
    // no controller change needed.
    $giveaways = match ($simulation->type) {
        'phishing_email' => [
            'Check the sender domain after the @ — display names can say anything',
            'Urgency and deadlines are pressure tactics, not service',
            'Hover links before clicking; the real URL tells the truth',
        ],
        'fake_url' => [
            'Look-alike characters: paypa1 with a "1", micros0ft with a "0"',
            'The real domain is right before the first single "/" — not in the subdomain',
            'No HTTPS on a login page is an instant stop sign',
        ],
        'social_engineering' => [
            '"Keep this between us" — secrecy requests are a manipulation signature',
            'Real urgent requests survive a verification call to a known number',
            'Authority + urgency + unusual payment method (gift cards) = attack pattern',
        ],
        default => [
            'Pause before acting — pressure is the attacker\'s main tool',
            'Verify through a channel you already trust, not the one in the message',
        ],
    };

    $lessonCategory = match ($simulation->type) {
        'phishing_email'     => 'Email Security',
        'fake_url'           => 'Web Security',
        'social_engineering' => 'Threat Awareness',
        default              => null,
    };
@endphp

@section('content')
<div class="max-w-2xl mx-auto">

    {{-- Verdict --}}
    <div class="bg-card border border-line rounded-2xl p-8 text-center card-shadow fade-up mb-5">
        <div class="w-16 h-16 rounded-2xl grid place-items-center mx-auto mb-4 {{ $fellForIt ? 'bg-coral/10' : 'bg-tealsoft' }}">
            <span class="text-3xl">{{ $fellForIt ? '⚠️' : '🛡️' }}</span>
        </div>
        <p class="font-display text-2xl font-semibold text-{{ $rc }} mb-1.5">{{ $message }}</p>
        <p class="text-sm text-slate">{{ $simulation->title }} · {{ ucwords(str_replace('_', ' ', $simulation->type)) }}</p>
    </div>

    {{-- What gave it away --}}
    <div class="bg-card border border-line rounded-2xl p-6 card-shadow mb-5">
        <p class="text-xs font-semibold tracking-wide uppercase text-slate mb-3">
            {{ $fellForIt ? 'What to watch for next time' : 'What gave it away' }}
        </p>
        <div class="space-y-2.5">
            @foreach ($giveaways as $g)
                <div class="flex items-start gap-2.5 text-sm">
                    <span class="w-2 h-2 rounded-full mt-1.5 shrink-0 bg-{{ $rc }}"></span>
                    <span class="text-ink/90">{{ $g }}</span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Takeaway --}}
    <div class="bg-tealsoft border border-teal/20 rounded-2xl p-5 mb-6">
        <p class="text-sm text-teal font-bold mb-1">Takeaway</p>
        <p class="text-sm text-ink/90">{{ $tip }}</p>
    </div>

    {{-- Actions --}}
    <div class="flex gap-3">
        <a href="{{ route('simulations.index') }}" class="flex-1 text-center bg-teal text-white font-bold py-3 rounded-xl hover:bg-teal/90 transition">Try another simulation</a>
        @if ($lessonCategory)
            <a href="{{ route('lessons.index', ['category' => $lessonCategory]) }}"
               class="flex-1 text-center border border-line text-slate font-semibold py-3 rounded-xl hover:border-teal hover:text-teal transition">
                {{ $fellForIt ? 'Learn the signs' : 'Related lessons' }}
            </a>
        @else
            <a href="{{ route('lessons.index') }}" class="flex-1 text-center border border-line text-slate font-semibold py-3 rounded-xl hover:border-teal hover:text-teal transition">Browse lessons</a>
        @endif
    </div>
</div>
@endsection
