@extends('layouts.app')
@section('title', 'Lessons')

@php
    if (! function_exists('lessonArt')) {
        function lessonArt($text) {
            $t = strtolower($text);
            if (str_contains($t,'phish') || str_contains($t,'email'))            return ['mint','M4 6h16v12H4zM4 8l8 5 8-5'];
            if (str_contains($t,'password') || str_contains($t,'account') || str_contains($t,'auth')) return ['cream','M12 2l8 3.5V11c0 5-3.4 8.6-8 10-4.6-1.4-8-5-8-10V5.5L12 2zM8.8 11.8l2.3 2.3 4.1-4.4'];
            if (str_contains($t,'brows') || str_contains($t,'web') || str_contains($t,'link') || str_contains($t,'wifi') || str_contains($t,'wi-fi')) return ['rose','M12 3a9 9 0 1 0 0 18 9 9 0 0 0 0-18zM3 12h18M12 3c2.6 2.6 3.8 5.7 3.8 9S14.6 18.4 12 21'];
            if (str_contains($t,'social') || str_contains($t,'engineer') || str_contains($t,'pretext') || str_contains($t,'impersonat')) return ['mint','M9 8a3.4 3.4 0 1 0 0-1zM2.8 20c.7-3.2 3.2-5 6.2-5s5.5 1.8 6.2 5'];
            return ['cream','M12 14l9-5-9-5-9 5 9 5zM12 14v7'];
        }
    }

    // Photo per topic. Swap any URL here to change a card's image —
    // if a URL ever fails to load, the card falls back to gradient + icon.
    if (! function_exists('lessonImg')) {
        function lessonImg($text) {
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
    $diffClass = ['beginner'=>'teal','intermediate'=>'amber','advanced'=>'coral'];
@endphp

@section('content')

{{-- ===== Resume strip ===== --}}
@if ($resume)
<div class="rounded-2xl p-6 md:p-7 mb-7 text-white flex flex-wrap items-center gap-6"
     style="background:linear-gradient(120deg,#0f766e,#0a5249);box-shadow:0 10px 26px rgba(14,111,95,.28)">
    <div class="w-14 h-14 rounded-2xl flex items-center justify-center shrink-0" style="background:rgba(255,255,255,.14)">
        <svg class="w-7 h-7" fill="none" stroke="#fff" stroke-width="1.7" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3.5 2"/></svg>
    </div>
    <div class="flex-1 min-w-[220px]">
        <p class="text-[11px] font-bold tracking-[.1em] mb-1" style="color:rgba(255,255,255,.75)">
            {{ $resume['pct'] > 0 ? 'CONTINUE WHERE YOU LEFT OFF' : 'UP NEXT FOR YOU' }}
        </p>
        <h3 class="font-display text-xl font-semibold mb-2">{{ $resume['title'] }}</h3>
        <div class="h-1.5 rounded-full overflow-hidden max-w-sm" style="background:rgba(255,255,255,.22)"><div class="h-full rounded-full" style="width:{{ $resume['pct'] }}%;background:#FFD98A"></div></div>
        <span class="text-xs mt-1.5 block" style="color:rgba(255,255,255,.85)">{{ $resume['pct'] }}% complete</span>
    </div>
    <a href="{{ route('lessons.show', $resume['id']) }}" class="bg-white text-teal font-bold px-6 py-3 rounded-xl hover:-translate-y-0.5 transition">
        {{ $resume['pct'] > 0 ? 'Resume lesson' : 'Start lesson' }}
    </a>
    <div class="flex items-center gap-3 pl-5 ml-auto" style="border-left:1px solid rgba(255,255,255,.22)">
        @php $ringC = 138.2; $ringOff = $ringC - $ringC * ($totalLessons ? $doneCount/$totalLessons : 0); @endphp
        <svg width="50" height="50" viewBox="0 0 52 52">
            <circle cx="26" cy="26" r="22" fill="none" stroke="rgba(255,255,255,.22)" stroke-width="5"/>
            <circle cx="26" cy="26" r="22" fill="none" stroke="#FFD98A" stroke-width="5" stroke-linecap="round" stroke-dasharray="{{ $ringC }}" stroke-dashoffset="{{ $ringOff }}" transform="rotate(-90 26 26)"/>
        </svg>
        <div><b class="block text-sm">{{ $doneCount }} of {{ $totalLessons }} complete</b><span class="text-xs" style="color:rgba(255,255,255,.75)">Across all modules</span></div>
    </div>
</div>
@endif

{{-- ===== Filter bar ===== --}}
<form method="GET" action="{{ route('lessons.index') }}" class="flex flex-wrap gap-3 items-center mb-5">
    @if ($activeCategory)<input type="hidden" name="category" value="{{ $activeCategory }}">@endif
    <label class="flex items-center gap-2.5 border border-line rounded-xl px-3.5 h-11 bg-card flex-1 min-w-[240px] focus-within:border-teal transition">
        <svg class="w-4 h-4 text-slate" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.5-4.5"/></svg>
        <input name="search" value="{{ $search }}" placeholder="Search lessons..." class="flex-1 bg-transparent outline-none text-sm">
    </label>
    <select name="difficulty" class="h-11 border border-line rounded-xl px-3 text-sm bg-card min-w-[148px] focus:border-teal outline-none">
        <option value="">All levels</option>
        @foreach (['beginner','intermediate','advanced'] as $d)
            <option value="{{ $d }}" @selected($activeDifficulty===$d)>{{ ucfirst($d) }}</option>
        @endforeach
    </select>
    <select name="sort" onchange="this.form.submit()"
            class="h-11 border border-line rounded-xl px-3 text-sm bg-card min-w-[148px] focus:border-teal outline-none">
        <option value="rec"   @selected($activeSort==='rec')>Recommended</option>
        <option value="az"    @selected($activeSort==='az')>A → Z</option>
        <option value="short" @selected($activeSort==='short')>Shortest first</option>
    </select>
    <button class="h-11 bg-teal text-white font-medium px-5 rounded-xl hover:bg-teal/90 transition">Filter</button>
</form>

{{-- ===== Category chips ===== --}}
<div class="flex flex-wrap gap-2 mb-6">
    <a href="{{ route('lessons.index', array_filter(['sort' => $activeSort !== 'rec' ? $activeSort : null])) }}"
       class="rounded-full px-4 py-2 text-sm font-semibold border transition {{ ! $activeCategory ? 'bg-teal border-teal text-white' : 'bg-card border-line text-slate hover:border-teal hover:text-teal' }}">
        All <span class="opacity-65 ml-1">{{ $totalAll }}</span>
    </a>
    @foreach ($categories as $cat)
        <a href="{{ route('lessons.index', array_filter(['category' => $cat, 'sort' => $activeSort !== 'rec' ? $activeSort : null])) }}"
           class="rounded-full px-4 py-2 text-sm font-semibold border transition {{ $activeCategory === $cat ? 'bg-teal border-teal text-white' : 'bg-card border-line text-slate hover:border-teal hover:text-teal' }}">
            {{ $cat }} <span class="opacity-65 ml-1">{{ $catCounts[$cat] ?? 0 }}</span>
        </a>
    @endforeach
</div>

{{-- ===== Grid ===== --}}
@if ($lessons->isEmpty())
    <div class="bg-card border border-line rounded-2xl p-12 text-center card-shadow">
        <p class="font-display text-xl font-semibold mb-1">No lessons match your filters</p>
        <p class="text-slate text-sm">Try a different keyword, category, or level.</p>
        <a href="{{ route('lessons.index') }}" class="inline-block mt-4 border border-line rounded-full px-5 py-2 text-sm font-semibold text-teal hover:border-teal transition">Clear filters</a>
    </div>
@else
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($lessons as $lesson)
            @php
                $done = in_array($lesson->id, $completedIds);
                $pct  = $progressMap[$lesson->id] ?? null;
                [$art, $iconPath] = lessonArt($lesson->category.' '.$lesson->title);
                $img  = lessonImg($lesson->category.' '.$lesson->title);
                $diff = $diffClass[$lesson->difficulty] ?? 'slate';
            @endphp
            <a href="{{ route('lessons.show', $lesson->id) }}"
               class="group bg-card border border-line rounded-2xl overflow-hidden card-shadow flex flex-col hover:-translate-y-1 transition">
                {{-- Photo header; gradient + icon shows through if image fails --}}
                <div class="h-36 relative grid place-items-center" style="background:{{ $artBg[$art] }}">
                    <svg class="w-11 h-11" fill="none" stroke="{{ $artColor[$art] }}" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="{{ $iconPath }}"/></svg>
                    <img src="{{ $img }}" alt="" loading="lazy" onerror="this.remove()"
                         class="absolute inset-0 w-full h-full object-cover group-hover:scale-[1.03] transition duration-300">
                    <div class="absolute inset-0" style="background:linear-gradient(180deg,rgba(13,38,33,.05),rgba(13,38,33,.25))"></div>
                    @if ($done)
                        <span class="absolute top-3 right-3 flex items-center gap-1 text-[11px] font-bold tracking-wide bg-white text-teal rounded-full px-2.5 py-1 shadow">✓ COMPLETED</span>
                    @elseif ($pct)
                        <span class="absolute top-3 right-3 flex items-center gap-1 text-[11px] font-bold tracking-wide bg-white text-amber rounded-full px-2.5 py-1 shadow">● IN PROGRESS</span>
                    @endif
                </div>
                <div class="p-5 flex flex-col gap-2 flex-1">
                    <span class="self-start text-[11px] font-bold tracking-wider rounded-full px-3 py-1 bg-{{ $diff }}/10 text-{{ $diff }}">{{ strtoupper($lesson->difficulty) }}</span>
                    <h3 class="font-display text-xl font-semibold leading-snug group-hover:text-teal transition">{{ $lesson->title }}</h3>
                    @if ($lesson->category)<p class="text-slate text-sm">{{ $lesson->category }}</p>@endif

                    @if ($pct)
                        <div class="h-1.5 rounded-full overflow-hidden bg-line mt-1">
                            <div class="h-full rounded-full bg-amber" style="width:{{ $pct }}%"></div>
                        </div>
                    @endif

                    <div class="mt-auto pt-3 border-t border-line flex items-center justify-between text-sm text-slate">
                        @if ($done)
                            <span>{{ $lesson->duration_minutes ? '⏱ '.$lesson->duration_minutes.' min' : 'Completed' }}</span>
                            <span class="font-bold text-slate">Review ↻</span>
                        @elseif ($pct)
                            <span>{{ $pct }}% done</span>
                            <span class="text-teal font-bold">Resume →</span>
                        @else
                            <span>{{ $lesson->duration_minutes ? '⏱ '.$lesson->duration_minutes.' min' : 'Lesson' }}</span>
                            <span class="text-teal font-bold">Start lesson →</span>
                        @endif
                    </div>
                </div>
            </a>
        @endforeach
    </div>
@endif
@endsection
