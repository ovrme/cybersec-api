@extends('layouts.app')
@section('title', 'Security Tips')

@php
    $lc       = ['high' => 'coral', 'medium' => 'amber', 'low' => 'teal'][$level] ?? 'slate';
    $ringHex  = ['coral' => '#f2a0ab', 'amber' => '#FFD98A', 'teal' => '#7fd6c8'][$lc] ?? '#FFD98A';
    $levelMsg = [
        'high'   => 'Your risk is high — start with the critical items below. Each one closes a real gap.',
        'medium' => 'You\'re in the middle band. A few habits below will push you into the safe zone.',
        'low'    => 'Low risk — nice work. These tips keep your instincts sharp.',
    ][$level] ?? '';

    $prioColor = ['critical' => 'coral', 'high' => 'amber', 'medium' => 'teal', 'low' => 'slate'];
    $prioRank  = ['critical' => 0, 'high' => 1, 'medium' => 2, 'low' => 3];

    // Map each tip to a "Do it now" action by title keywords — view-level,
    // so the controller's static tips stay untouched.
    if (! function_exists('tipAction')) {
        function tipAction($title) {
            $t = strtolower($title);
            if (str_contains($t,'lesson') || str_contains($t,'learning'))  return ['Open lessons',        route('lessons.index')];
            if (str_contains($t,'quiz'))                                   return ['Take a quiz',         route('quizzes.index')];
            if (str_contains($t,'link'))                                   return ['Scan a link',         route('scanner.index')];
            if (str_contains($t,'password') || str_contains($t,'two-factor') || str_contains($t,'authentication'))
                                                                           return ['Account security lessons', route('lessons.index', ['category' => 'Account Security'])];
            if (str_contains($t,'sender') || str_contains($t,'attachment') || str_contains($t,'email'))
                                                                           return ['Email security lessons',   route('lessons.index', ['category' => 'Email Security'])];
            if (str_contains($t,'wi-fi') || str_contains($t,'wifi') || str_contains($t,'vpn') || str_contains($t,'updated') || str_contains($t,'software'))
                                                                           return ['Web security lessons',     route('lessons.index', ['category' => 'Web Security'])];
            return null;
        }
    }

    // Split into action groups, keeping priority order within each
    $sorted  = collect($tips)->sortBy(fn ($t) => $prioRank[$t['priority']] ?? 9)->values();
    $urgent  = $sorted->filter(fn ($t) => in_array($t['priority'], ['critical', 'high']))->values();
    $ongoing = $sorted->filter(fn ($t) => ! in_array($t['priority'], ['critical', 'high']))->values();
@endphp

@section('content')

<div x-data="tipChecklist()">

{{-- ===== Risk strip ===== --}}
<div class="rounded-2xl p-6 md:p-7 mb-7 text-white flex flex-wrap items-center gap-6"
     style="background:linear-gradient(120deg,#0f766e,#0a5249);box-shadow:0 10px 26px rgba(14,111,95,.28)">
    <div class="w-14 h-14 rounded-2xl flex items-center justify-center shrink-0" style="background:rgba(255,255,255,.14)">
        <svg class="w-7 h-7" fill="none" stroke="#fff" stroke-width="1.7" viewBox="0 0 24 24"><path d="M12 2l8 3.5V11c0 5-3.4 8.6-8 10-4.6-1.4-8-5-8-10V5.5L12 2z"/><path d="M12 8v4M12 15.5h.01"/></svg>
    </div>
    <div class="flex-1 min-w-[220px]">
        <p class="text-[11px] font-bold tracking-[.1em] mb-1" style="color:rgba(255,255,255,.75)">RECOMMENDATIONS FOR YOUR RISK LEVEL</p>
        <h3 class="font-display text-xl font-semibold mb-1">
            <span class="capitalize">{{ $level }}</span> risk{{ $score !== null ? ' · '.$score.'/100' : '' }}
        </h3>
        <p class="text-sm" style="color:rgba(255,255,255,.85)">{{ $levelMsg }}</p>
    </div>
    <div class="flex items-center gap-3 pl-5 ml-auto" style="border-left:1px solid rgba(255,255,255,.22)">
        @if ($score !== null)
            @php $C = 138.2; @endphp
            <div class="relative w-[50px] h-[50px]">
                <svg width="50" height="50" viewBox="0 0 52 52">
                    <circle cx="26" cy="26" r="22" fill="none" stroke="rgba(255,255,255,.22)" stroke-width="5"/>
                    <circle cx="26" cy="26" r="22" fill="none" stroke="{{ $ringHex }}" stroke-width="5" stroke-linecap="round"
                            stroke-dasharray="{{ $C }}" stroke-dashoffset="{{ $C - $C * min($score, 100) / 100 }}" transform="rotate(-90 26 26)"/>
                </svg>
                <span class="absolute inset-0 grid place-items-center text-xs font-bold">{{ $score }}</span>
            </div>
        @endif
        <div>
            <b class="block text-sm" x-text="doneCount() + ' of {{ count($tips) }} done'"></b>
            <span class="text-xs" style="color:rgba(255,255,255,.75)">Check tips off as you go</span>
        </div>
    </div>
</div>

{{-- ===== Urgent group ===== --}}
@if ($urgent->count())
    <p class="text-xs font-semibold tracking-wide uppercase text-slate mb-3">Do these first</p>
    <div class="grid md:grid-cols-2 gap-5 mb-8">
        @foreach ($urgent as $tip)
            @include('recommendations._tip', ['tip' => $tip, 'prioColor' => $prioColor])
        @endforeach
    </div>
@endif

{{-- ===== Ongoing group ===== --}}
@if ($ongoing->count())
    <p class="text-xs font-semibold tracking-wide uppercase text-slate mb-3">Keep building the habit</p>
    <div class="grid md:grid-cols-2 gap-5">
        @foreach ($ongoing as $tip)
            @include('recommendations._tip', ['tip' => $tip, 'prioColor' => $prioColor])
        @endforeach
    </div>
@endif

</div>

<script>
function tipChecklist() {
    return {
        done: JSON.parse(localStorage.getItem('skillshield_tips_done') || '{}'),
        toggle(key) {
            this.done[key] = ! this.done[key];
            localStorage.setItem('skillshield_tips_done', JSON.stringify(this.done));
        },
        doneCount() { return Object.values(this.done).filter(Boolean).length; },
    }
}
</script>
@endsection
