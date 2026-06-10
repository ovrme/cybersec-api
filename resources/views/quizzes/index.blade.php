@extends('layouts.app')
@section('title', 'Quizzes')

@php
    if (! function_exists('quizArt')) {
        function quizArt($text) {
            $t = strtolower($text);
            if (str_contains($t,'phish') || str_contains($t,'email'))            return ['mint','M4 6h16v12H4zM4 8l8 5 8-5'];
            if (str_contains($t,'password') || str_contains($t,'account') || str_contains($t,'auth')) return ['cream','M12 2l8 3.5V11c0 5-3.4 8.6-8 10-4.6-1.4-8-5-8-10V5.5L12 2zM8.8 11.8l2.3 2.3 4.1-4.4'];
            if (str_contains($t,'brows') || str_contains($t,'web') || str_contains($t,'link') || str_contains($t,'wifi')) return ['rose','M12 3a9 9 0 1 0 0 18 9 9 0 0 0 0-18zM3 12h18M12 3c2.6 2.6 3.8 5.7 3.8 9S14.6 18.4 12 21'];
            if (str_contains($t,'social') || str_contains($t,'engineer'))        return ['mint','M9 8a3.4 3.4 0 1 0 0-1zM2.8 20c.7-3.2 3.2-5 6.2-5s5.5 1.8 6.2 5'];
            return ['cream','M9 12l2 2 4-4M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z'];
        }
    }

    // Photo per topic. Swap any URL here to change a card's image —
    // if a URL ever fails to load, the card falls back to gradient + icon.
    if (! function_exists('quizImg')) {
        function quizImg($text) {
            $t = strtolower($text);
            $u = fn ($id) => "https://images.unsplash.com/{$id}?w=640&h=300&fit=crop&q=60&auto=format";
            if (str_contains($t,'phish') || str_contains($t,'email'))   return $u('photo-1563986768609-322da13575f3');   // laptop + mail
            if (str_contains($t,'password') || str_contains($t,'account') || str_contains($t,'auth')) return $u('photo-1614064641938-3bbee52942c7'); // padlock
            if (str_contains($t,'brows') || str_contains($t,'web') || str_contains($t,'link') || str_contains($t,'wifi')) return $u('photo-1526374965328-7f61d4dc18c5'); // code screen
            if (str_contains($t,'social') || str_contains($t,'engineer')) return $u('photo-1521791136064-7986c2920216'); // handshake
            if (str_contains($t,'mobile') || str_contains($t,'device'))   return $u('photo-1512941937669-90a1b58e7e9c'); // phone
            return $u('photo-1550751827-4bd374c3f58b');                                                                   // cyber abstract
        }
    }

    $artBg = [
        'mint'  => 'linear-gradient(135deg,#EAF5F1,#F4FAF8)',
        'cream' => 'linear-gradient(135deg,#FBF2E0,#FDF8EE)',
        'rose'  => 'linear-gradient(135deg,#FAE9EC,#FDF4F5)',
    ];
    $artColor = ['mint'=>'#0f766e','cream'=>'#c9820a','rose'=>'#d1495b'];
@endphp

@section('content')

<div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
    @foreach ($quizzes as $quiz)
        @php
            [$art, $iconPath] = quizArt($quiz->title);
            $img      = quizImg($quiz->title.' '.$quiz->description);
            $stat     = $stats[$quiz->id] ?? null;
            $best     = $stat ? (int) $stat->best : null;
            $passed   = $best !== null && $best >= 70;
            $estMin   = max(1, (int) ceil($quiz->questions_count * 0.5));
        @endphp
        <a href="{{ route('quizzes.show', $quiz->id) }}"
           class="group bg-card border border-line rounded-2xl overflow-hidden card-shadow flex flex-col hover:-translate-y-1 transition">
            {{-- Photo header; gradient + icon shows through if image fails --}}
            <div class="h-28 relative grid place-items-center" style="background:{{ $artBg[$art] }}">
                <svg class="w-10 h-10" fill="none" stroke="{{ $artColor[$art] }}" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="{{ $iconPath }}"/></svg>
                <img src="{{ $img }}" alt="" loading="lazy" onerror="this.remove()"
                     class="absolute inset-0 w-full h-full object-cover group-hover:scale-[1.03] transition duration-300">
                <div class="absolute inset-0" style="background:linear-gradient(180deg,rgba(13,38,33,.05),rgba(13,38,33,.25))"></div>
                @if ($passed)
                    <span class="absolute top-3 right-3 text-[11px] font-bold tracking-wide bg-white text-teal rounded-full px-2.5 py-1 shadow">✓ PASSED</span>
                @elseif ($best !== null)
                    <span class="absolute top-3 right-3 text-[11px] font-bold tracking-wide bg-white text-amber rounded-full px-2.5 py-1 shadow">BEST {{ $best }}%</span>
                @endif
            </div>
            <div class="p-5 flex flex-col gap-2 flex-1">
                <span class="self-start text-[11px] font-bold tracking-wider rounded-full px-3 py-1 bg-teal/10 text-teal">{{ $quiz->questions_count }} {{ Str::plural('QUESTION', $quiz->questions_count) }}</span>
                <h3 class="font-display text-xl font-semibold leading-snug group-hover:text-teal transition">{{ $quiz->title }}</h3>
                @if ($quiz->description)<p class="text-slate text-sm">{{ $quiz->description }}</p>@endif

                @if ($best !== null && ! $passed)
                    <div class="h-1.5 rounded-full overflow-hidden bg-line mt-1">
                        <div class="h-full rounded-full bg-amber" style="width:{{ $best }}%"></div>
                    </div>
                @endif

                <div class="mt-auto pt-3 border-t border-line flex items-center justify-between text-sm text-slate">
                    @if ($passed)
                        <span>Best {{ $best }}% · {{ $stat->attempts }} {{ Str::plural('attempt', $stat->attempts) }}</span>
                        <span class="font-bold text-slate">Retake ↻</span>
                    @elseif ($best !== null)
                        <span>{{ $stat->attempts }} {{ Str::plural('attempt', $stat->attempts) }} · pass at 70%</span>
                        <span class="text-teal font-bold">Try again →</span>
                    @else
                        <span>⏱ ~{{ $estMin }} min</span>
                        <span class="text-teal font-bold">Start quiz →</span>
                    @endif
                </div>
            </div>
        </a>
    @endforeach
</div>

@if ($quizzes->isEmpty())
    <div class="bg-card border border-line rounded-2xl p-12 text-center card-shadow">
        <p class="font-display text-xl font-semibold mb-1">No quizzes yet</p>
        <p class="text-slate text-sm">Check back soon — or build your instincts in the <a href="{{ route('lessons.index') }}" class="text-teal font-semibold hover:underline">lessons</a>.</p>
    </div>
@endif
@endsection
