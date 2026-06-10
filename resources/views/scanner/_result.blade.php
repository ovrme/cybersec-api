{{-- resources/views/scanner/_result.blade.php
     Rendered inside the panel that ran the scan.
     Expects: $result (risk_score, risk_level, flags), $type, $rc, $rcBg,
              $verdictSub, $urlParts (url scans only) --}}

<div id="scanResult" class="mt-6 pt-6 border-t border-line fade-up">

    {{-- Verdict --}}
    <div class="flex items-center gap-4 rounded-xl px-4.5 py-4 mb-4" style="background:{{ $rcBg[$rc] }};padding:16px 18px">
        <div class="w-16 h-16 rounded-full bg-card grid place-items-center shrink-0 card-shadow">
            <span class="font-display text-2xl font-semibold text-{{ $rc }}">{{ $result['risk_score'] }}</span>
        </div>
        <div>
            <p class="font-semibold text-[17px] text-{{ $rc }} capitalize">{{ $result['risk_level'] }} risk</p>
            <p class="text-[13px] text-slate mt-0.5">
                {{ $verdictSub }}
                Risk score {{ $result['risk_score'] }}/100 · {{ count($result['flags']) }} {{ Str::plural('signal', count($result['flags'])) }} found.
            </p>
        </div>
    </div>

    {{-- URL breakdown --}}
    @if (! empty($urlParts))
        <div class="flex flex-wrap gap-1.5 mb-4">
            <span class="font-mono text-xs rounded-md px-2.5 py-1.5 {{ $urlParts['schemeBad'] ? 'bg-coral/10 text-coral font-semibold' : 'bg-paper text-slate' }}">{{ $urlParts['scheme'] }}</span>
            <span class="font-mono text-xs rounded-md px-2.5 py-1.5 break-all {{ $urlParts['hostBad'] ? 'bg-coral/10 text-coral font-semibold' : 'bg-paper text-slate' }}">{{ $urlParts['host'] }}</span>
            @if ($urlParts['path'] && $urlParts['path'] !== '/')
                <span class="font-mono text-xs rounded-md px-2.5 py-1.5 bg-paper text-slate break-all">{{ $urlParts['path'] }}</span>
            @endif
        </div>
    @endif

    {{-- Signals --}}
    @if (count($result['flags']) > 0)
        <div class="space-y-2">
            @foreach ($result['flags'] as $flag)
                <div class="flex items-start gap-2.5 border border-line rounded-xl px-3.5 py-3 text-sm">
                    <span class="w-2 h-2 rounded-full mt-1.5 shrink-0 bg-{{ $rc }}"></span>
                    <span class="text-ink">{{ $flag }}</span>
                </div>
            @endforeach
        </div>
    @else
        <div class="flex items-start gap-2.5 border border-line rounded-xl px-3.5 py-3 text-sm">
            <span class="w-2 h-2 rounded-full mt-1.5 shrink-0 bg-teal"></span>
            <span><b>Clean scan.</b> <span class="text-slate">No common phishing patterns found — still verify before entering credentials. A clean scan isn't a guarantee.</span></span>
        </div>
    @endif

    {{-- Tip note --}}
    <p class="text-[13px] text-slate border border-dashed border-line rounded-xl px-3.5 py-2.5 mt-4 bg-paper/50">
        This checker looks for common phishing patterns — it can't catch everything.
        Not sure? Practise spotting these signals in the
        <a href="{{ route('lessons.index', ['category' => 'Email Security']) }}" class="text-teal font-semibold hover:underline">Phishing lessons</a>.
    </p>
</div>
