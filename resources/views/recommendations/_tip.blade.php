{{-- resources/views/recommendations/_tip.blade.php
     One tip card. Expects: $tip (title, description, priority), $prioColor.
     Lives inside the page's tipChecklist() Alpine scope. --}}

@php
    $pc     = $prioColor[$tip['priority']] ?? 'slate';
    $key    = \Illuminate\Support\Str::slug($tip['title']);
    $action = tipAction($tip['title']);
@endphp

<div class="bg-card border border-line rounded-2xl p-6 card-shadow transition"
     :class="done['{{ $key }}'] && 'opacity-60'">
    <div class="flex items-start gap-3.5">
        {{-- Check-off --}}
        <button type="button" @click="toggle('{{ $key }}')"
                class="w-6 h-6 rounded-full border-2 grid place-items-center shrink-0 mt-0.5 transition"
                :class="done['{{ $key }}'] ? 'bg-teal border-teal text-white' : 'border-line hover:border-teal'"
                :aria-pressed="!! done['{{ $key }}']" aria-label="Mark tip as done">
            <span x-show="done['{{ $key }}']" class="text-xs font-bold">✓</span>
        </button>

        <div class="flex-1 min-w-0">
            <div class="flex items-start justify-between gap-3 mb-1.5">
                <h3 class="font-display text-lg font-semibold leading-snug"
                    :class="done['{{ $key }}'] && 'line-through'">{{ $tip['title'] }}</h3>
                <span class="shrink-0 text-[11px] font-bold uppercase tracking-wide px-2.5 py-1 rounded-full bg-{{ $pc }}/10 text-{{ $pc }}">{{ $tip['priority'] }}</span>
            </div>
            <p class="text-sm text-slate leading-relaxed">{{ $tip['description'] }}</p>

            @if ($action)
                <a href="{{ $action[1] }}" class="inline-flex items-center gap-1 text-sm text-teal font-bold mt-3 hover:underline">
                    {{ $action[0] }} →
                </a>
            @endif
        </div>
    </div>
</div>
