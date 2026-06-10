@extends('layouts.app')
@section('title', 'Dashboard')

@php
    $level = $risk->level ?? null;
    $score = $risk->score ?? 0;
    $circ  = 2 * 3.14159 * 52;
    $dash  = $circ * ($score / 100);
    $lc    = ['low' => 'teal', 'medium' => 'amber', 'high' => 'coral'][$level] ?? 'teal';
    $lcHex = ['teal' => '#0f766e', 'amber' => '#c9820a', 'coral' => '#d1495b'][$lc];

    // Sparkline points from risk history (lower score = better)
    $riskHistory = $riskHistory ?? [];
    $spark = null;
    if (count($riskHistory) >= 2) {
        $w = 220; $h = 56; $n = count($riskHistory);
        $pts = collect($riskHistory)->map(function ($v, $i) use ($w, $h, $n) {
            $x = $n > 1 ? $i / ($n - 1) * $w : 0;
            $y = $h - ($v / 100 * $h);
            return round($x, 1).','.round($y, 1);
        })->implode(' ');
        $spark = ['pts' => $pts, 'w' => $w, 'h' => $h];
    }

    // Tip of the day — rotates daily, no backend needed
    $tips = [
        ['Hover before you click.', 'Always hover over a link to preview its true destination. The visible text can say anything — the real URL is what matters.'],
        ['Read the domain after the @.', 'Display names are decoration. The sender\'s real identity is the domain in their address — paypa1.com is not paypal.com.'],
        ['Urgency is the attack.', 'Any message demanding action "within 24 hours" is using pressure to stop you thinking. Real requests survive a pause.'],
        ['One password, one account.', 'A unique password per account means one breach can\'t cascade. A password manager makes this effortless.'],
        ['Verify out-of-band.', 'A strange request from your boss? Call them on a number you already have — never the contact details in the message itself.'],
        ['Gift cards are a siren.', 'No legitimate company or executive settles anything in gift card codes. That request is always an attack.'],
        ['Updates are armour.', 'Most malware exploits already-patched holes. Installing updates promptly closes the doors attackers rely on.'],
    ];
    [$tipTitle, $tipBody] = $tips[now()->dayOfYear % count($tips)];
@endphp

@section('content')

{{-- ===================== HERO (unchanged) ===================== --}}
<section class="relative overflow-hidden mb-10 text-paper w-screen left-1/2 -translate-x-1/2 -mt-9"
         style="background:
            radial-gradient(900px 380px at 12% -20%, #0f766e 0%, transparent 55%),
            radial-gradient(700px 460px at 105% 130%, rgba(201,130,10,.45) 0%, transparent 55%),
            #13241f;">
    <div class="absolute inset-0 opacity-50" style="background-image:radial-gradient(rgba(255,255,255,.06) 1px,transparent 1px);background-size:18px 18px;"></div>
    <div class="relative w-full px-10 pt-16 pb-32 grid lg:grid-cols-[1.6fr_1fr] gap-10 items-center">
        <div>
            <span class="inline-flex items-center gap-2 text-xs font-medium px-3 py-1.5 rounded-full mb-5" style="background:rgba(255,255,255,.1);color:#d6ece9">
                <span class="w-1.5 h-1.5 rounded-full" style="background:#d6ece9"></span>
                {{ $level ? "You're in the {$level}-risk zone" : 'Start building your score' }}
            </span>
            <p class="text-sm font-medium mb-2" style="color:#d6ece9">Welcome back, {{ auth()->user()->name }}</p>
            <h1 class="font-display text-6xl md:text-7xl font-semibold tracking-tight" style="color:#fff;line-height:1">Your security dashboard</h1>
            <p class="mt-5 text-base leading-relaxed" style="color:rgba(247,245,240,.65);max-width:34rem">
                @if ($rank) You're ranked #{{ $rank }} of {{ $totalRanked }}. Keep practising to climb higher.
                @else Take a quiz or run a simulation to start building your score. @endif
            </p>
            <div class="flex flex-wrap gap-3 mt-8">
                <a href="{{ route('quizzes.index') }}" class="text-sm font-medium px-7 py-3.5 rounded-xl text-white" style="background:#0f766e">Take a quiz </a>
                <a href="{{ route('simulations.index') }}" class="text-sm font-medium px-7 py-3.5 rounded-xl" style="background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.15);color:#f7f5f0">Run a simulation</a>
            </div>
        </div>
        <div class="flex items-center justify-center">
            <svg width="340" height="340" viewBox="0 0 380 400" class="max-w-full">
                <defs>
                    <radialGradient id="glow" cx="50%" cy="44%" r="60%">
                        <stop offset="0" stop-color="#1d9e75" stop-opacity=".55"/><stop offset="0.6" stop-color="#1d9e75" stop-opacity=".12"/><stop offset="1" stop-color="#1d9e75" stop-opacity="0"/>
                    </radialGradient>
                    <linearGradient id="sfill" x1="0" y1="0" x2="1" y2="1">
                        <stop offset="0" stop-color="rgba(29,158,117,.18)"/><stop offset="1" stop-color="rgba(13,24,16,.4)"/>
                    </linearGradient>
                </defs>
                <circle cx="190" cy="180" r="168" fill="url(#glow)"/>
                <circle cx="190" cy="180" r="158" fill="none" stroke="rgba(214,236,233,.10)" stroke-width="1"/>
                <circle cx="190" cy="180" r="126" fill="none" stroke="rgba(214,236,233,.16)" stroke-width="1"/>
                <circle cx="190" cy="180" r="158" fill="none" stroke="rgba(214,236,233,.35)" stroke-width="2" stroke-dasharray="2 16" stroke-linecap="round"/>
                <path d="M190 64 L300 96 L300 188 C300 240 254 272 190 290 C126 272 80 240 80 188 L80 96 Z" fill="url(#sfill)" stroke="#d6ece9" stroke-width="2.5"/>
                <path d="M190 88 L278 114 L278 186 C278 226 242 252 190 268 C138 252 102 226 102 186 L102 114 Z" fill="none" stroke="rgba(214,236,233,.25)" stroke-width="1.5"/>
                <circle cx="190" cy="166" r="24" fill="none" stroke="#d6ece9" stroke-width="10"/>
                <path d="M190 186 l0 34" stroke="#d6ece9" stroke-width="10" stroke-linecap="round"/>
            </svg>
        </div>
    </div>
</section>

{{-- ===================== RISK SCORE (overlap card) ===================== --}}
<div class="-mt-24 relative z-10 mb-5">
    <div class="bg-card border border-line rounded-2xl p-8 card-shadow grid md:grid-cols-3 gap-8 items-center">
        {{-- Ring + level --}}
        <div class="flex items-center gap-5">
            <div class="relative flex items-center justify-center shrink-0">
                <svg class="w-32 h-32 -rotate-90" viewBox="0 0 120 120">
                    <circle cx="60" cy="60" r="52" fill="none" stroke="#e6e2d8" stroke-width="11"/>
                    @if ($risk)
                        <circle cx="60" cy="60" r="52" fill="none" stroke="{{ $lcHex }}" stroke-width="11" stroke-linecap="round" stroke-dasharray="{{ $dash }} {{ $circ }}"/>
                    @endif
                </svg>
                <div class="absolute text-center">
                    <p class="font-display text-3xl font-semibold text-{{ $lc }}">{{ $score }}</p>
                    <p class="text-xs text-slate">/ 100</p>
                </div>
            </div>
            <div>
                <p class="text-sm text-slate mb-1">Current risk score</p>
                <p class="font-display text-2xl font-semibold">{{ $level ? ucfirst($level).' risk' : 'Not yet scored' }}</p>
                @if (!is_null($scoreDelta) && $scoreDelta != 0)
                    {{-- Risk going DOWN is good --}}
                    <span class="inline-block mt-2 text-xs px-3 py-1 rounded-full {{ $scoreDelta < 0 ? 'bg-teal/10 text-teal' : 'bg-coral/10 text-coral' }}">
                        {{ $scoreDelta < 0 ? '▼ '.$scoreDelta.' — improving' : '▲ +'.$scoreDelta.' — rising' }}
                    </span>
                @endif
                <form method="POST" action="{{ route('risk-score.generate') }}" class="mt-3">@csrf
                    <button class="text-sm text-teal font-medium hover:underline">{{ $risk ? 'Recalculate' : 'Generate score' }} →</button>
                </form>
            </div>
        </div>

        {{-- Trend sparkline --}}
        <div class="md:border-l md:border-line md:pl-8">
            <p class="text-sm text-slate mb-3">Score trend <span class="text-xs">(lower is safer)</span></p>
            @if ($spark)
                <svg viewBox="0 0 {{ $spark['w'] }} {{ $spark['h'] + 4 }}" class="w-full h-16">
                    <polyline points="{{ $spark['pts'] }}" fill="none" stroke="{{ $lcHex }}" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                    @php $last = explode(' ', $spark['pts']); $lastPt = explode(',', end($last)); @endphp
                    <circle cx="{{ $lastPt[0] }}" cy="{{ $lastPt[1] }}" r="4" fill="{{ $lcHex }}"/>
                </svg>
                <p class="text-xs text-slate mt-1.5">Last {{ count($riskHistory) }} calculations</p>
            @else
                <p class="text-sm text-slate py-4">Recalculate your score a few times — the trend line appears once there's history to draw.</p>
            @endif
        </div>

        {{-- Breakdown --}}
        <div class="md:border-l md:border-line md:pl-8">
            <p class="text-sm text-slate mb-4">What's driving your score</p>
            @foreach ($breakdown as $b)
                <div class="mb-3">
                    <div class="flex justify-between text-sm mb-1.5"><span class="text-slate">{{ $b['label'] }}</span><span class="font-semibold text-{{ $b['color'] }}">{{ $b['pct'] }}%</span></div>
                    <div class="h-2 bg-paper rounded-full overflow-hidden"><div class="h-full bg-{{ $b['color'] }} rounded-full" style="width:{{ $b['pct'] }}%"></div></div>
                </div>
            @endforeach
        </div>
    </div>
</div>

{{-- ===================== NEXT BEST ACTIONS ===================== --}}
<div class="grid md:grid-cols-3 gap-4 mb-5">
    {{-- Resume / start lesson --}}
    <a href="{{ $continue ? route('lessons.show', $continue['id']) : route('lessons.index') }}"
       class="group bg-card border border-line rounded-2xl p-5 card-shadow hover:-translate-y-0.5 hover:border-teal transition">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 rounded-xl bg-tealsoft grid place-items-center shrink-0">
                <svg class="w-5 h-5 text-teal" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M12 14l9-5-9-5-9 5 9 5zM12 14v7"/></svg>
            </div>
            <p class="text-xs font-bold uppercase tracking-wide text-teal">Continue learning</p>
        </div>
        @if ($continue)
            <p class="font-display text-lg font-semibold leading-snug group-hover:text-teal transition">{{ $continue['title'] }}</p>
            <div class="flex items-center gap-2.5 mt-2.5">
                <div class="h-1.5 bg-paper rounded-full overflow-hidden flex-1"><div class="h-full bg-teal rounded-full" style="width:{{ $continue['pct'] }}%"></div></div>
                <span class="text-xs text-slate shrink-0">{{ $continue['done'] }}/{{ $continue['total'] }}</span>
            </div>
        @else
            <p class="font-display text-lg font-semibold leading-snug group-hover:text-teal transition">All lessons complete</p>
            <p class="text-sm text-slate mt-1">Review any lesson to keep it fresh.</p>
        @endif
        <p class="text-sm text-teal font-bold mt-3">{{ $continue ? 'Resume →' : 'Browse lessons →' }}</p>
    </a>

    {{-- Quiz --}}
    <a href="{{ route('quizzes.index') }}"
       class="group bg-card border border-line rounded-2xl p-5 card-shadow hover:-translate-y-0.5 hover:border-teal transition">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 rounded-xl bg-amber/10 grid place-items-center shrink-0">
                <svg class="w-5 h-5 text-amber" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
            </div>
            <p class="text-xs font-bold uppercase tracking-wide text-amber">Prove it</p>
        </div>
        <p class="font-display text-lg font-semibold leading-snug group-hover:text-teal transition">
            {{ $quizCount ? 'Push your average above '.min(100, (int) $quizAvg + 10).'%' : 'Take your first quiz' }}
        </p>
        <p class="text-sm text-slate mt-1">{{ $quizCount ? $quizCount.' taken · avg '.$quizAvg.'%' : 'Quiz scores are 40% of your risk score.' }}</p>
        <p class="text-sm text-teal font-bold mt-3">Take a quiz →</p>
    </a>

    {{-- Simulation --}}
    <a href="{{ route('simulations.index') }}"
       class="group bg-card border border-line rounded-2xl p-5 card-shadow hover:-translate-y-0.5 hover:border-teal transition">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 rounded-xl bg-coral/10 grid place-items-center shrink-0">
                <svg class="w-5 h-5 text-coral" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M13 2 3 14h9l-1 8 10-12h-9z"/></svg>
            </div>
            <p class="text-xs font-bold uppercase tracking-wide text-coral">Test your instincts</p>
        </div>
        <p class="font-display text-lg font-semibold leading-snug group-hover:text-teal transition">Face a real attack pattern</p>
        <p class="text-sm text-slate mt-1">Safe to fail — that's the point.</p>
        <p class="text-sm text-teal font-bold mt-3">Run a simulation →</p>
    </a>
</div>

{{-- ===================== STAT CARDS ===================== --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-5">
    @php
        $cards = [
            ['Quizzes', $quizCount, 'teal', 'M9 12l2 2 4-4M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z', 'Avg '.$quizAvg.'%'],
            ['Lessons', $lessonsDone.' / '.$totalLessons, 'teal', 'M12 14l9-5-9-5-9 5 9 5zM12 14v7', $lessonsDone >= $totalLessons && $totalLessons ? 'complete' : 'in progress'],
            ['Scans', $scanCount, 'coral', 'M21 21l-4.3-4.3M11 19a8 8 0 1 0 0-16 8 8 0 0 0 0 16z', $highRiskScans.' high-risk'],
            ['Rank', $rank ? '#'.$rank : '—', 'amber', 'M3 3v18h18M7 14l4-4 4 4 5-5', $rank ? 'of '.$totalRanked.' learners' : 'unranked'],
        ];
    @endphp
    @foreach ($cards as [$label, $val, $c, $path, $sub])
        <div class="bg-card border border-line rounded-2xl p-5 card-shadow flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl flex items-center justify-center bg-{{ $c }}/10 shrink-0">
                <svg class="w-5 h-5 text-{{ $c }}" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="{{ $path }}"/></svg>
            </div>
            <div class="min-w-0"><p class="font-display text-2xl font-semibold leading-none">{{ $val }}</p><p class="text-xs text-slate mt-1">{{ $label }} · {{ $sub }}</p></div>
        </div>
    @endforeach
</div>

{{-- ===================== ACTIVITY + TIP ===================== --}}
<div class="grid lg:grid-cols-3 gap-5 mb-5">
    <div class="lg:col-span-2 bg-card border border-line rounded-2xl p-7 card-shadow">
        <div class="flex items-center justify-between mb-5">
            <div><h2 class="font-display text-lg font-semibold">This week's activity</h2><p class="text-xs text-slate mt-0.5">Quizzes &amp; scans, last 7 days</p></div>
            <div class="flex items-center gap-2">
                @if ($streak >= 2)
                    <span class="text-xs font-bold text-amber bg-amber/10 px-3 py-1 rounded-full">🔥 {{ $streak }}-day streak</span>
                @endif
                <span class="text-xs font-medium text-teal bg-tealsoft px-3 py-1 rounded-full">{{ $weekTotal }} actions</span>
            </div>
        </div>
        <div class="flex items-end justify-between gap-3 h-40">
            @foreach ($week as $day)
                @php $h = $day['count'] ? max(8, (int) round($day['count'] / $weekMax * 100)) : 4; @endphp
                <div class="flex-1 flex flex-col items-center gap-2">
                    <div class="w-full flex items-end justify-center" style="height:120px"><div class="w-full rounded-xl {{ $day['count'] ? 'bg-teal' : 'bg-line' }}" style="height:{{ $h }}%" title="{{ $day['count'] }} actions"></div></div>
                    <span class="text-xs {{ $loop->last ? 'font-bold text-ink' : 'text-slate' }}">{{ $loop->last ? 'Today' : $day['label'] }}</span>
                </div>
            @endforeach
        </div>
    </div>
    <div class="bg-ink text-paper rounded-2xl p-7 card-shadow flex flex-col">
        <div class="flex items-center gap-2 mb-3">
            <svg class="w-5 h-5 text-amber" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 2a7 7 0 0 0-4 12.7V17h8v-2.3A7 7 0 0 0 12 2zM9 21h6"/></svg>
            <p class="text-sm font-medium text-amber">Tip of the day</p>
        </div>
        <p class="font-display text-xl font-semibold leading-snug">{{ $tipTitle }}</p>
        <p class="text-sm mt-2 leading-relaxed flex-1" style="color:rgba(247,245,240,.7)">{{ $tipBody }}</p>
        <a href="{{ route('recommendations.index') }}" class="text-sm text-tealsoft font-medium mt-4 hover:underline">More tips →</a>
    </div>
</div>

{{-- ===================== RECENT QUIZZES + SCANS + ACHIEVEMENTS ===================== --}}
<div class="grid lg:grid-cols-3 gap-5">
    <div class="bg-card border border-line rounded-2xl p-7 card-shadow">
        <div class="flex items-center justify-between mb-4"><h2 class="font-display text-lg font-semibold">Recent quizzes</h2><a href="{{ route('quizzes.index') }}" class="text-sm text-teal hover:underline">All →</a></div>
        @if ($recentQuizzes->isEmpty())
            <p class="text-slate text-sm py-4">No quizzes yet. <a href="{{ route('quizzes.index') }}" class="text-teal font-medium">Take one →</a></p>
        @else
            <div class="space-y-3">
                @foreach ($recentQuizzes as $q)
                    @php $qc = $q['percentage'] >= 70 ? 'teal' : ($q['percentage'] >= 40 ? 'amber' : 'coral'); @endphp
                    <div class="flex items-center justify-between"><div class="min-w-0"><p class="text-sm font-medium truncate">{{ $q['title'] }}</p><p class="text-xs text-slate">{{ $q['score'] }}/{{ $q['total'] }} · {{ $q['when'] }}</p></div><span class="text-sm font-semibold text-{{ $qc }} shrink-0 ml-3">{{ round($q['percentage']) }}%</span></div>
                @endforeach
            </div>
        @endif
    </div>
    <div class="bg-card border border-line rounded-2xl p-7 card-shadow">
        <div class="flex items-center justify-between mb-4"><h2 class="font-display text-lg font-semibold">Recent scans</h2><a href="{{ route('scanner.index') }}" class="text-sm text-teal hover:underline">New →</a></div>
        @if ($recentScans->isEmpty())
            <p class="text-slate text-sm py-4">No scans yet. <a href="{{ route('scanner.index') }}" class="text-teal font-medium">Run one →</a></p>
        @else
            <div class="space-y-3">
                @foreach ($recentScans as $scan)
                    @php $sc = ['high'=>'coral','medium'=>'amber','low'=>'teal'][$scan->risk_level] ?? 'slate'; @endphp
                    <div class="flex items-center gap-2.5"><span class="text-[10px] uppercase px-2 py-0.5 rounded-full bg-{{ $sc }}/10 text-{{ $sc }} shrink-0">{{ $scan->input_type }}</span><span class="text-sm text-slate truncate flex-1">{{ \Illuminate\Support\Str::limit($scan->input_data, 32) }}</span><span class="text-sm font-semibold text-{{ $sc }} shrink-0">{{ $scan->risk_score }}</span></div>
                @endforeach
            </div>
        @endif
    </div>
    <div class="bg-card border border-line rounded-2xl p-7 card-shadow">
        @php $earnedCount = collect($achievements)->where('earned', true)->count(); @endphp
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-display text-lg font-semibold">Achievements</h2>
            <span class="text-xs font-medium text-slate">{{ $earnedCount }}/{{ count($achievements) }}</span>
        </div>
        <div class="space-y-3">
            @foreach ($achievements as $a)
                <div class="flex items-center gap-3 {{ $a['earned'] ? '' : 'opacity-45' }}">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0 {{ $a['earned'] ? 'bg-teal' : 'bg-paper border border-line' }}">
                        @if ($a['earned'])
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        @else
                            <svg class="w-4 h-4 text-slate" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/></svg>
                        @endif
                    </div>
                    <div class="min-w-0"><p class="text-sm font-medium truncate">{{ $a['name'] }}</p><p class="text-xs text-slate truncate">{{ $a['desc'] }}</p></div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
