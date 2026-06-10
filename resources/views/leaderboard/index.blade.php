@extends('layouts.app')
@section('title', 'Leaderboard')

@php
    $riskColor   = ['low' => 'teal', 'medium' => 'amber', 'high' => 'coral', 'unrated' => 'slate'];
    $avatarHues  = ['#0f766e', '#c9820a', '#d1495b', '#5a6760', '#3b6ea5'];
    $hue         = fn ($name) => $avatarHues[crc32($name) % count($avatarHues)];
    $initials    = fn ($name) => strtoupper(mb_substr(trim($name), 0, 1));

    $me     = $rows->firstWhere('user_id', $myId);
    $above  = $me && $me['rank'] > 1 ? $rows->firstWhere('rank', $me['rank'] - 1) : null;
    $gap    = ($me && $above) ? round($above['points'] - $me['points'], 1) : null;

    $podium = $rows->take(3);
    $rest   = $rows->slice(3)->values();
    // Visual order: 2nd · 1st · 3rd
    $podiumOrder = collect([$podium->get(1), $podium->get(0), $podium->get(2)])->filter();
@endphp

@section('content')

{{-- ===== Your rank strip ===== --}}
<div class="rounded-2xl p-6 md:p-7 mb-7 text-white flex flex-wrap items-center gap-6"
     style="background:linear-gradient(120deg,#0f766e,#0a5249);box-shadow:0 10px 26px rgba(14,111,95,.28)">
    <div class="w-14 h-14 rounded-2xl flex items-center justify-center shrink-0" style="background:rgba(255,255,255,.14)">
        <svg class="w-7 h-7" fill="none" stroke="#fff" stroke-width="1.7" viewBox="0 0 24 24"><path d="M4 19V5M4 19h16M8 16v-5m4 5V8m4 8v-3"/></svg>
    </div>
    <div class="flex-1 min-w-[220px]">
        @if ($me)
            <p class="text-[11px] font-bold tracking-[.1em] mb-1" style="color:rgba(255,255,255,.75)">YOUR STANDING</p>
            <h3 class="font-display text-xl font-semibold mb-1">
                Rank #{{ $me['rank'] }} of {{ $rows->count() }} · {{ $me['points'] }} pts
            </h3>
            <p class="text-sm" style="color:rgba(255,255,255,.85)">
                @if ($me['rank'] === 1)
                    Top of the board — defend the crown.
                @elseif ($gap !== null)
                    {{ $gap }} {{ Str::plural('point', $gap == 1 ? 1 : 2) }} behind #{{ $me['rank'] - 1 }}.
                    A lesson is worth 2 pts — quiz scores move the average.
                @endif
            </p>
        @else
            <p class="text-[11px] font-bold tracking-[.1em] mb-1" style="color:rgba(255,255,255,.75)">NOT RANKED YET</p>
            <h3 class="font-display text-xl font-semibold mb-1">Take one quiz to enter the board</h3>
            <p class="text-sm" style="color:rgba(255,255,255,.85)">Points = average quiz score + lessons completed × 2.</p>
        @endif
    </div>
    @if ($me)
        <div class="flex items-center gap-3 pl-5 ml-auto" style="border-left:1px solid rgba(255,255,255,.22)">
            <div><b class="block text-sm">{{ $me['lessons_done'] }} lessons · {{ $me['quizzes_taken'] }} quizzes</b>
                 <span class="text-xs" style="color:rgba(255,255,255,.75)">Avg quiz {{ $me['avg_quiz_score'] }}%</span></div>
        </div>
    @else
        <a href="{{ route('quizzes.index') }}" class="bg-white text-teal font-bold px-6 py-3 rounded-xl hover:-translate-y-0.5 transition">Take a quiz</a>
    @endif
</div>

@if ($rows->isEmpty())
    <div class="bg-card border border-line rounded-2xl p-12 text-center card-shadow">
        <p class="font-display text-xl font-semibold mb-1">No ranked learners yet</p>
        <p class="text-slate text-sm">Be the first — <a href="{{ route('quizzes.index') }}" class="text-teal font-semibold hover:underline">take a quiz</a> to appear here.</p>
    </div>
@else

    {{-- ===== Podium ===== --}}
    @if ($podium->count() >= 2)
        <div class="grid grid-cols-3 gap-4 items-end mb-7 max-w-2xl mx-auto">
            @foreach ($podiumOrder as $p)
                @php
                    $first = $p['rank'] === 1;
                    $medal = [1 => '🥇', 2 => '🥈', 3 => '🥉'][$p['rank']];
                    $isMe  = $p['user_id'] === $myId;
                @endphp
                <div class="bg-card border rounded-2xl card-shadow text-center px-3 {{ $first ? 'py-7 border-amber/50' : 'py-5 border-line' }} {{ $isMe ? 'ring-2 ring-teal/40' : '' }}">
                    <div class="text-2xl mb-1.5">{{ $medal }}</div>
                    <div class="mx-auto rounded-full text-white grid place-items-center font-bold mb-2 {{ $first ? 'w-14 h-14 text-xl' : 'w-11 h-11' }}"
                         style="background:{{ $hue($p['name']) }}">{{ $initials($p['name']) }}</div>
                    <p class="font-display font-semibold leading-tight {{ $first ? 'text-lg' : 'text-[15px]' }} {{ $isMe ? 'text-teal' : '' }}">
                        {{ $p['name'] }}{{ $isMe ? ' (you)' : '' }}
                    </p>
                    <p class="font-display text-amber font-semibold mt-1 {{ $first ? 'text-2xl' : 'text-lg' }}">{{ $p['points'] }} pts</p>
                    <p class="text-xs text-slate mt-0.5">{{ $p['lessons_done'] }} lessons · {{ $p['quizzes_taken'] }} quizzes</p>
                </div>
            @endforeach
        </div>
    @endif

    {{-- ===== Table ===== --}}
    @php $tableRows = $podium->count() >= 2 ? $rest : $rows; @endphp
    @if ($tableRows->count())
        <div class="bg-card border border-line rounded-2xl overflow-hidden card-shadow">
            <div class="grid grid-cols-[48px_1fr_auto] sm:grid-cols-[48px_1fr_auto_auto_auto_auto] gap-3 px-6 py-3 border-b border-line text-xs text-slate uppercase tracking-wide">
                <span>#</span><span>Learner</span>
                <span class="text-right hidden sm:block">Avg quiz</span>
                <span class="text-right hidden sm:block">Lessons</span>
                <span class="text-right hidden sm:block">Risk</span>
                <span class="text-right">Points</span>
            </div>
            @foreach ($tableRows as $row)
                @php
                    $isMe = $row['user_id'] === $myId;
                    $rc   = $riskColor[$row['risk_level']] ?? 'slate';
                @endphp
                <div class="grid grid-cols-[48px_1fr_auto] sm:grid-cols-[48px_1fr_auto_auto_auto_auto] gap-3 px-6 py-3.5 items-center border-b border-line/60 last:border-0 {{ $isMe ? 'bg-tealsoft/50' : '' }}">
                    <span class="font-semibold text-slate">{{ $row['rank'] }}</span>
                    <span class="flex items-center gap-2.5 min-w-0">
                        <span class="w-8 h-8 rounded-full text-white grid place-items-center text-sm font-bold shrink-0"
                              style="background:{{ $hue($row['name']) }}">{{ $initials($row['name']) }}</span>
                        <span class="text-sm truncate {{ $isMe ? 'text-teal font-semibold' : '' }}">{{ $row['name'] }}{{ $isMe ? ' (you)' : '' }}</span>
                    </span>
                    <span class="text-sm text-slate text-right hidden sm:block">{{ $row['avg_quiz_score'] }}%</span>
                    <span class="text-sm text-slate text-right hidden sm:block">{{ $row['lessons_done'] }}</span>
                    <span class="text-right hidden sm:block">
                        <span class="text-[11px] font-bold uppercase tracking-wide px-2.5 py-1 rounded-full bg-{{ $rc }}/10 text-{{ $rc }}">{{ $row['risk_level'] }}</span>
                    </span>
                    <span class="text-sm font-bold text-amber text-right">{{ $row['points'] }}</span>
                </div>
            @endforeach
        </div>
    @endif

    <p class="text-xs text-slate mt-4">Points = average quiz score + lessons completed × 2.</p>
@endif
@endsection
