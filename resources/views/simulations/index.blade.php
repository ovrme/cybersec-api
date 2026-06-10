@extends('layouts.app')
@section('title', 'Simulations')

@php
    $typeArt = [
        'phishing_email'     => ['mint',  'M4 6h16v12H4zM4 8l8 5 8-5'],
        'fake_url'           => ['rose',  'M10 13a5 5 0 007.5.5l2-2a5 5 0 00-7-7l-1.2 1.2M14 11a5 5 0 00-7.5-.5l-2 2a5 5 0 007 7l1.2-1.2'],
        'social_engineering' => ['cream', 'M9 8a3.4 3.4 0 1 0 0-1zM2.8 20c.7-3.2 3.2-5 6.2-5s5.5 1.8 6.2 5'],
    ];

    // Photo per attack type. Swap any URL to change a card's image —
    // if a URL ever fails to load, the card falls back to gradient + icon.
    $typeImg = [
        'phishing_email'     => 'https://images.unsplash.com/photo-1563986768609-322da13575f3?w=640&h=300&fit=crop&q=60&auto=format', // laptop + mail
        'fake_url'           => 'https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?w=640&h=300&fit=crop&q=60&auto=format', // code screen
        'social_engineering' => 'https://images.unsplash.com/photo-1521791136064-7986c2920216?w=640&h=300&fit=crop&q=60&auto=format', // handshake
    ];
    $defaultImg = 'https://images.unsplash.com/photo-1550751827-4bd374c3f58b?w=640&h=300&fit=crop&q=60&auto=format'; // cyber abstract

    $artBg = [
        'mint'  => 'linear-gradient(135deg,#EAF5F1,#F4FAF8)',
        'cream' => 'linear-gradient(135deg,#FBF2E0,#FDF8EE)',
        'rose'  => 'linear-gradient(135deg,#FAE9EC,#FDF4F5)',
    ];
    $artColor = ['mint'=>'#0f766e','cream'=>'#c9820a','rose'=>'#d1495b'];

    $bySim = $history->groupBy('simulation_id');
    $rate = $stats['total'] > 0 ? (int) round($stats['avoided'] / $stats['total'] * 100) : null;
    $C = 138.2;
@endphp

@section('content')

{{-- ===== Instinct strip ===== --}}
<div class="rounded-2xl p-6 md:p-7 mb-7 text-white flex flex-wrap items-center gap-6"
     style="background:linear-gradient(120deg,#0f766e,#0a5249);box-shadow:0 10px 26px rgba(14,111,95,.28)">
    <div class="w-14 h-14 rounded-2xl flex items-center justify-center shrink-0" style="background:rgba(255,255,255,.14)">
        <svg class="w-7 h-7" fill="none" stroke="#fff" stroke-width="1.7" viewBox="0 0 24 24"><path d="M13 2 3 14h9l-1 8 10-12h-9z"/></svg>
    </div>
    <div class="flex-1 min-w-[220px]">
        @if ($rate !== null)
            <p class="text-[11px] font-bold tracking-[.1em] mb-1" style="color:rgba(255,255,255,.75)">YOUR INSTINCT RATE</p>
            <h3 class="font-display text-xl font-semibold mb-1">You spotted {{ $stats['avoided'] }} of {{ $stats['total'] }} attacks</h3>
            <p class="text-sm" style="color:rgba(255,255,255,.85)">
                {{ $rate >= 80 ? 'Sharp instincts — keep them warm.' : ($rate >= 50 ? 'Getting there. Each run builds the reflex.' : 'No shame — that\'s exactly what practice is for.') }}
            </p>
        @else
            <p class="text-[11px] font-bold tracking-[.1em] mb-1" style="color:rgba(255,255,255,.75)">SAFE PRACTICE RANGE</p>
            <h3 class="font-display text-xl font-semibold mb-1">Face real attacks — without the consequences</h3>
            <p class="text-sm" style="color:rgba(255,255,255,.85)">Each simulation is a real-world attack pattern. Decide how you'd respond, then learn what gave it away.</p>
        @endif
    </div>
    @if ($rate !== null)
        <div class="flex items-center gap-3 pl-5 ml-auto" style="border-left:1px solid rgba(255,255,255,.22)">
            <div class="relative w-[50px] h-[50px]">
                <svg width="50" height="50" viewBox="0 0 52 52">
                    <circle cx="26" cy="26" r="22" fill="none" stroke="rgba(255,255,255,.22)" stroke-width="5"/>
                    <circle cx="26" cy="26" r="22" fill="none" stroke="#FFD98A" stroke-width="5" stroke-linecap="round"
                            stroke-dasharray="{{ $C }}" stroke-dashoffset="{{ $C - $C * $rate / 100 }}" transform="rotate(-90 26 26)"/>
                </svg>
                <span class="absolute inset-0 grid place-items-center text-xs font-bold">{{ $rate }}%</span>
            </div>
            <div><b class="block text-sm">{{ $stats['avoided'] }} avoided</b><span class="text-xs" style="color:rgba(255,255,255,.75)">{{ $stats['fell_for_it'] }} fell for</span></div>
        </div>
    @endif
</div>

{{-- ===== Simulation cards ===== --}}
@if ($simulations->isEmpty())
    <div class="bg-card border border-line rounded-2xl p-12 text-center card-shadow">
        <p class="font-display text-xl font-semibold mb-1">No simulations available yet</p>
        <p class="text-slate text-sm">Check back soon — or build your instincts in the <a href="{{ route('lessons.index') }}" class="text-teal font-semibold hover:underline">lessons</a>.</p>
    </div>
@else
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($simulations as $sim)
            @php
                [$art, $iconPath] = $typeArt[$sim->type] ?? ['cream', 'M13 2 3 14h9l-1 8 10-12h-9z'];
                $img       = $typeImg[$sim->type] ?? $defaultImg;
                $typeLabel = ucwords(str_replace('_', ' ', $sim->type));
                $runs      = $bySim[$sim->id] ?? collect();
                $lastRun   = $runs->first();
            @endphp
            <a href="{{ route('simulations.show', $sim->id) }}"
               class="group bg-card border border-line rounded-2xl overflow-hidden card-shadow flex flex-col hover:-translate-y-1 transition">
                {{-- Photo header; gradient + icon shows through if image fails --}}
                <div class="h-32 relative grid place-items-center" style="background:{{ $artBg[$art] }}">
                    <svg class="w-10 h-10" fill="none" stroke="{{ $artColor[$art] }}" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="{{ $iconPath }}"/></svg>
                    <img src="{{ $img }}" alt="" loading="lazy" onerror="this.remove()"
                         class="absolute inset-0 w-full h-full object-cover group-hover:scale-[1.03] transition duration-300">
                    <div class="absolute inset-0" style="background:linear-gradient(180deg,rgba(13,38,33,.05),rgba(13,38,33,.25))"></div>
                    @if ($lastRun)
                        @if (! $lastRun->fell_for_it)
                            <span class="absolute top-3 right-3 text-[11px] font-bold tracking-wide bg-white text-teal rounded-full px-2.5 py-1 shadow">🛡 AVOIDED</span>
                        @else
                            <span class="absolute top-3 right-3 text-[11px] font-bold tracking-wide bg-white text-coral rounded-full px-2.5 py-1 shadow">⚠ FELL FOR IT</span>
                        @endif
                    @endif
                </div>
                <div class="p-5 flex flex-col gap-2 flex-1">
                    <span class="self-start text-[11px] font-bold tracking-wider rounded-full px-3 py-1 bg-teal/10 text-teal uppercase">{{ $typeLabel }}</span>
                    <h3 class="font-display text-xl font-semibold leading-snug group-hover:text-teal transition">{{ $sim->title }}</h3>
                    @if (! empty($sim->description))
                        <p class="text-slate text-sm">{{ $sim->description }}</p>
                    @endif
                    <div class="mt-auto pt-3 border-t border-line flex items-center justify-between text-sm text-slate">
                        @if ($runs->count())
                            <span>{{ $runs->count() }} {{ Str::plural('run', $runs->count()) }}</span>
                            <span class="text-teal font-bold">Run again ↻</span>
                        @else
                            <span>Not yet attempted</span>
                            <span class="text-teal font-bold">Run simulation →</span>
                        @endif
                    </div>
                </div>
            </a>
        @endforeach
    </div>
@endif

{{-- ===== Recent history ===== --}}
@if ($history->count())
    <p class="text-xs font-semibold tracking-wide uppercase text-slate mt-9 mb-3">Recent runs</p>
    <div class="bg-card border border-line rounded-2xl card-shadow divide-y divide-line">
        @foreach ($history->take(6) as $run)
            <div class="flex items-center gap-3 px-5 py-3.5 text-sm">
                <span class="w-6 h-6 rounded-full grid place-items-center text-xs font-bold shrink-0
                             {{ $run->fell_for_it ? 'bg-coral/10 text-coral' : 'bg-tealsoft text-teal' }}">
                    {{ $run->fell_for_it ? '✗' : '✓' }}
                </span>
                <span class="font-medium flex-1">{{ $run->simulation->title ?? 'Simulation' }}</span>
                <span class="{{ $run->fell_for_it ? 'text-coral' : 'text-teal' }} font-medium hidden sm:block">
                    {{ $run->fell_for_it ? 'Fell for it' : 'Avoided' }}
                </span>
                <span class="text-slate text-xs">{{ $run->created_at->diffForHumans() }}</span>
            </div>
        @endforeach
    </div>
@endif
@endsection
