@extends('layouts.app')
@section('title', $simulation->title)

@php
    $typeLabel = ucwords(str_replace('_', ' ', $simulation->type));

    // Split "From:/To:/Subject:" header lines out of the content so the
    // message renders like a real captured email. Anything else is body.
    $headerRows = [];
    $bodyLines  = [];
    foreach (preg_split('/\r?\n/', trim((string) $simulation->content)) as $line) {
        if (empty($bodyLines) && preg_match('/^(From|To|Subject|Sent|Date):\s*(.+)$/i', trim($line), $m)) {
            $headerRows[ucfirst(strtolower($m[1]))] = $m[2];
        } elseif (! (empty($bodyLines) && trim($line) === '')) {
            $bodyLines[] = $line;
        }
    }
    $body = implode("\n", $bodyLines);
@endphp

@section('content')
<div class="max-w-2xl mx-auto">

    {{-- Hero --}}
    <div class="rounded-2xl mb-6 p-6 md:p-7" style="background:linear-gradient(135deg,#FBF2E0,#FDF8EE);border:1px solid #e6e2d8">
        <a href="{{ route('simulations.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-teal mb-2 hover:underline">← All simulations</a>
        <span class="inline-block text-[11px] font-bold tracking-wider rounded-full px-3 py-1 bg-amber/10 text-amber uppercase mb-2">{{ $typeLabel }} · Simulation</span>
        <h1 class="font-display text-3xl font-semibold tracking-tight">{{ $simulation->title }}</h1>
        @if (! empty($simulation->description))
            <p class="text-slate mt-1.5">{{ $simulation->description }}</p>
        @endif
    </div>

    {{-- Captured message --}}
    <div class="flex items-center justify-between gap-3 rounded-t-2xl px-5 py-3 text-white text-sm font-semibold" style="background:#0a5249">
        <span>📨 Simulated message — read it like it just landed in your inbox</span>
    </div>
    <div class="border border-line border-t-0 rounded-b-2xl overflow-hidden bg-card card-shadow mb-7">
        @if (count($headerRows))
            <div class="px-5 py-4 border-b border-line bg-paper/60 text-sm grid gap-1.5">
                @foreach ($headerRows as $k => $v)
                    <div class="flex gap-2">
                        <span class="text-slate w-16 shrink-0">{{ $k }}</span>
                        <span class="{{ $k === 'Subject' ? 'font-semibold' : '' }} {{ $k === 'From' ? 'font-mono text-[13px]' : '' }}">{{ $v }}</span>
                    </div>
                @endforeach
            </div>
        @endif
        <div class="p-5 text-[15px] leading-7 whitespace-pre-line">{{ $body }}</div>
    </div>

    {{-- Decision --}}
    <p class="font-display text-lg font-semibold mb-1">How would you respond?</p>
    <p class="text-sm text-slate mb-4">Answer honestly — this is practice, not a test. The takeaway comes either way.</p>

    <div class="grid sm:grid-cols-2 gap-3">
        <form method="POST" action="{{ route('simulations.interact', $simulation->id) }}">
            @csrf
            <input type="hidden" name="fell_for_it" value="0">
            <button class="w-full h-full text-left border-2 border-teal/40 bg-card rounded-2xl p-5 hover:bg-tealsoft hover:border-teal transition group">
                <span class="text-2xl">🛡️</span>
                <span class="block font-bold text-teal mt-2">This is an attack</span>
                <span class="block text-sm text-slate mt-1">I'd spot the signs and wouldn't engage.</span>
            </button>
        </form>
        <form method="POST" action="{{ route('simulations.interact', $simulation->id) }}">
            @csrf
            <input type="hidden" name="fell_for_it" value="1">
            <button class="w-full h-full text-left border-2 border-coral/40 bg-card rounded-2xl p-5 hover:bg-coral/5 hover:border-coral transition group">
                <span class="text-2xl">⚠️</span>
                <span class="block font-bold text-coral mt-2">Looks legitimate to me</span>
                <span class="block text-sm text-slate mt-1">Honestly? I'd probably have acted on it.</span>
            </button>
        </form>
    </div>
</div>
@endsection
