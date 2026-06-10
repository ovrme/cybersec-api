@extends('layouts.app')
@section('title', 'Results — '.$quiz->title)

@php
    $rc   = $passed ? 'teal' : ($percentage >= 40 ? 'amber' : 'coral');
    $ring = ['teal' => '#0f766e', 'amber' => '#c9820a', 'coral' => '#d1495b'][$rc];
    $C    = 2 * pi() * 52;                       // ring circumference (r=52)
    $off  = $C - $C * min($percentage, 100) / 100;
    $qm   = $quiz->questions->keyBy('id');       // for option text lookup
@endphp

@section('content')
<div class="max-w-2xl mx-auto">

    {{-- Score card --}}
    <div class="bg-card border border-line rounded-2xl p-8 text-center mb-6 card-shadow fade-up">
        <p class="text-sm text-slate mb-4">{{ $quiz->title }}</p>

        <div class="relative w-32 h-32 mx-auto mb-4">
            <svg viewBox="0 0 120 120" class="w-full h-full">
                <circle cx="60" cy="60" r="52" fill="none" stroke="#e6e2d8" stroke-width="9"/>
                <circle cx="60" cy="60" r="52" fill="none" stroke="{{ $ring }}" stroke-width="9" stroke-linecap="round"
                        stroke-dasharray="{{ $C }}" stroke-dashoffset="{{ $off }}" transform="rotate(-90 60 60)"/>
            </svg>
            <div class="absolute inset-0 grid place-items-center">
                <span class="font-display text-3xl font-semibold text-{{ $rc }}">{{ (int) $percentage }}%</span>
            </div>
        </div>

        <p class="font-display text-xl font-semibold mb-1">
            {{ $passed ? 'Passed — well done!' : ($percentage >= 40 ? 'Almost there.' : 'Keep practising.') }}
        </p>
        <p class="text-sm text-slate">
            {{ $passed ? "You're building real instincts." : 'Review the answers below, then try again — pass mark is 70%.' }}
        </p>

        <div class="flex justify-center gap-3.5 flex-wrap mt-6">
            <div class="bg-paper/60 border border-line rounded-xl px-6 py-3 min-w-[110px]">
                <div class="font-display text-xl font-semibold text-ink">{{ $score }}/{{ $total }}</div>
                <div class="text-xs text-slate mt-0.5">Correct</div>
            </div>
            <div class="bg-paper/60 border border-line rounded-xl px-6 py-3 min-w-[110px]">
                <div class="font-display text-xl font-semibold text-{{ $rc }}">{{ $passed ? 'Pass' : 'Retry' }}</div>
                <div class="text-xs text-slate mt-0.5">70% to pass</div>
            </div>
            <div class="bg-paper/60 border border-line rounded-xl px-6 py-3 min-w-[110px]">
                <div class="font-display text-xl font-semibold text-ink">{{ $total - $score }}</div>
                <div class="text-xs text-slate mt-0.5">To review</div>
            </div>
        </div>
    </div>

    {{-- Review --}}
    <p class="text-xs font-semibold tracking-wide uppercase text-slate mb-3">Review your answers</p>
    <div class="space-y-3">
        @foreach ($results as $i => $r)
            @php
                $ic = $r['is_correct'] ? 'teal' : 'coral';
                $q  = $qm[$r['id']] ?? null;
                $yourText    = ($q && $r['your_answer'])    ? $q->{'option_'.$r['your_answer']}    : null;
                $correctText = ($q && $r['correct_answer']) ? $q->{'option_'.$r['correct_answer']} : null;
            @endphp
            <div class="bg-card border border-line rounded-2xl p-5 card-shadow">
                <div class="flex items-start gap-3 mb-3">
                    <span class="w-6 h-6 rounded-full grid place-items-center text-xs font-bold shrink-0 mt-0.5
                                 {{ $r['is_correct'] ? 'bg-tealsoft text-teal' : 'bg-coral/10 text-coral' }}">
                        {{ $r['is_correct'] ? '✓' : '✗' }}
                    </span>
                    <p class="font-medium text-sm leading-relaxed">{{ $i + 1 }}. {{ $r['question'] }}</p>
                </div>
                <div class="ml-9 space-y-1.5 text-sm">
                    <p class="flex flex-wrap gap-1.5 items-baseline">
                        <span class="text-xs text-slate shrink-0 w-24">Your answer</span>
                        <span class="text-{{ $ic }} font-medium">
                            @if ($r['your_answer'])
                                <span class="uppercase font-bold">{{ $r['your_answer'] }}</span>@if($yourText) — {{ $yourText }}@endif
                            @else
                                — (no answer)
                            @endif
                        </span>
                    </p>
                    @unless ($r['is_correct'])
                        <p class="flex flex-wrap gap-1.5 items-baseline">
                            <span class="text-xs text-slate shrink-0 w-24">Correct answer</span>
                            <span class="text-teal font-medium"><span class="uppercase font-bold">{{ $r['correct_answer'] }}</span>@if($correctText) — {{ $correctText }}@endif</span>
                        </p>
                    @endunless
                    @if ($r['explanation'])
                        <p class="text-slate mt-2 border-l-2 border-teal/40 bg-tealsoft/30 rounded-r-lg pl-3 pr-3 py-2">{{ $r['explanation'] }}</p>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    {{-- Actions --}}
    <div class="flex gap-3 mt-6">
        <a href="{{ route('quizzes.show', $quiz->id) }}" class="flex-1 text-center bg-teal text-white font-bold py-3 rounded-xl hover:bg-teal/90 transition">
            {{ $passed ? 'Retake quiz' : 'Try again' }}
        </a>
        <a href="{{ route('quizzes.index') }}" class="flex-1 text-center border border-line text-slate font-semibold py-3 rounded-xl hover:border-teal hover:text-teal transition">All quizzes</a>
    </div>
    @unless ($passed)
        <p class="text-center text-sm text-slate mt-4">
            Want a refresher first? Revisit the <a href="{{ route('lessons.index') }}" class="text-teal font-semibold hover:underline">lessons</a> on this topic.
        </p>
    @endunless
</div>
@endsection
