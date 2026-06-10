@extends('layouts.app')
@section('title', $lesson->title)

@php
    $diff = ['beginner'=>'teal','intermediate'=>'amber','advanced'=>'coral'][$lesson->difficulty] ?? 'slate';

    // Prefer real DB sections. If a lesson has none yet, fall back to splitting
    // its old `content` field on blank lines so existing lessons still work.
    if ($lesson->sections && $lesson->sections->count()) {
        $parts = $lesson->sections->map(fn ($s) => [
            'type'  => $s->type ?? 'read',
            'label' => $s->label,
            'title' => $s->title,
            'body'  => $s->body,
            'meta'  => $s->meta ?? [],
            'html'  => true,
        ])->all();
    } else {
        $chunks = preg_split('/\n\s*\n/', trim((string) $lesson->content));
        $chunks = array_values(array_filter(array_map('trim', $chunks)));
        if (empty($chunks)) { $chunks = [(string) $lesson->content]; }
        $parts = array_map(fn ($c) => ['type'=>'read','label'=>null,'title'=>null,'body'=>$c,'meta'=>[],'html'=>false], $chunks);
    }
    $n = count($parts);

    // How many interactions each part requires before Continue unlocks.
    $needed = [];
    $quizTotal = 0;
    foreach ($parts as $i => $part) {
        $needed[$i] = match ($part['type']) {
            'cards'    => count($part['meta']['cards'] ?? []),
            'quiz'     => count($part['meta']['questions'] ?? []),
            'exercise' => (isset($part['meta']['from_flag']) ? 1 : 0)
                        + collect($part['meta']['body'] ?? [])->filter(fn ($b) => isset($b['tag']))->count(),
            default    => 0,
        };
        if ($part['type'] === 'quiz') $quizTotal += count($part['meta']['questions'] ?? []);
    }
@endphp

@section('content')

<div x-data="lessonReader({
        total: {{ $n }},
        start: {{ $startSection }},
        alreadyDone: {{ $completed ? 'true' : 'false' }},
        needed: @json(array_values($needed)),
        quizTotal: {{ $quizTotal }},
        progressUrl: '{{ route('lessons.progress', $lesson->id) }}',
        csrf: document.querySelector('meta[name=csrf-token]').content,
     })">

{{-- ===== Hero ===== --}}
<div class="rounded-2xl mb-7 overflow-hidden" style="background:linear-gradient(135deg,#EAF5F1,#F4FAF8);border:1px solid #e6e2d8">
    <div class="p-7 md:p-9">
        <a href="{{ route('lessons.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-teal mb-3 hover:underline">← Back to lessons</a>
        <div class="flex items-end justify-between gap-6 flex-wrap">
            <div>
                <span class="inline-block text-[11px] font-bold tracking-wider rounded-full px-3 py-1 bg-{{ $diff }}/10 text-{{ $diff }} mb-3">
                    {{ strtoupper($lesson->difficulty) }}@if($lesson->category) · {{ strtoupper($lesson->category) }}@endif
                </span>
                <h1 class="font-display text-4xl font-semibold tracking-tight">{{ $lesson->title }}</h1>
                @if (! empty($lesson->summary))
                    <p class="text-slate mt-2 max-w-lg">{{ $lesson->summary }}</p>
                @endif
                <div class="flex flex-wrap gap-5 mt-4 text-sm text-slate">
                    @if ($lesson->duration_minutes)<span>⏱ <b class="text-ink">~{{ $lesson->duration_minutes }} min</b></span>@endif
                    @if ($lesson->points)<span>🏆 <b class="text-ink">+{{ $lesson->points }} pts</b></span>@endif
                    <span>📚 <b class="text-ink">{{ $n }} {{ Str::plural('part', $n) }}</b></span>
                </div>
            </div>
            <div class="min-w-[200px]">
                <div class="flex items-center gap-2.5 text-sm font-semibold text-slate">
                    <div class="w-44 h-2 rounded-full overflow-hidden bg-white border border-line">
                        <div class="h-full bg-teal rounded-full transition-all" :style="`width:${pct()}%`"></div>
                    </div>
                    <span x-text="pct() + '%'"></span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ===== Layout: rail + sections ===== --}}
<div class="grid lg:grid-cols-[240px_1fr] gap-7 items-start">

    <aside class="lg:sticky lg:top-20 bg-card border border-line rounded-2xl p-4 card-shadow">
        <h3 class="text-[11px] font-semibold tracking-[.1em] uppercase text-slate mb-3">Lesson outline</h3>
        @foreach ($parts as $i => $part)
            <button type="button" @click="go({{ $i }})"
                    class="flex items-center gap-2.5 w-full text-left px-2.5 py-2 rounded-lg text-sm font-medium transition"
                    :class="{{ $i }} > maxUnlocked ? 'text-slate/40 cursor-not-allowed'
                          : (active === {{ $i }} ? 'bg-tealsoft text-teal font-semibold'
                          : (done.includes({{ $i }}) ? 'text-ink' : 'text-slate hover:bg-paper'))">
                <span class="w-5 h-5 rounded-full grid place-items-center text-[10px] shrink-0 border-2 transition"
                      :class="done.includes({{ $i }}) ? 'bg-teal border-teal text-white' : 'border-line'">
                    <span x-show="done.includes({{ $i }})">✓</span>
                </span>
                {{ $part['label'] ?? 'Part '.($i + 1) }}
            </button>
        @endforeach
        <button type="button" @click="go({{ $n }})"
                class="flex items-center gap-2.5 w-full text-left px-2.5 py-2 rounded-lg text-sm font-medium transition"
                :class="{{ $n }} > maxUnlocked ? 'text-slate/40 cursor-not-allowed'
                      : (active === {{ $n }} ? 'bg-tealsoft text-teal font-semibold' : 'text-slate hover:bg-paper')">
            <span class="w-5 h-5 rounded-full grid place-items-center text-[10px] shrink-0 border-2"
                  :class="finished ? 'bg-teal border-teal text-white' : 'border-line'"><span x-show="finished">✓</span></span>
            Complete
        </button>
    </aside>

    <div>
        @foreach ($parts as $i => $part)
            <section x-show="active === {{ $i }}" x-transition
                     class="bg-card border border-line rounded-2xl card-shadow p-7 md:p-8 mb-5">
                <div class="text-xs font-semibold tracking-wide uppercase text-teal mb-2">
                    Part {{ $i + 1 }} of {{ $n }}@if ($part['label']) · {{ $part['label'] }}@endif
                </div>
                @if ($part['title'])
                    <h2 class="font-display text-2xl font-semibold mb-3">{{ $part['title'] }}</h2>
                @endif

                {{-- ============ READ ============ --}}
                @if ($part['type'] === 'read')
                    @if ($part['html'])
                        <div class="lesson-html">{!! $part['body'] !!}</div>
                    @else
                        <div class="lesson-html whitespace-pre-line">{{ $part['body'] }}</div>
                    @endif

                {{-- ============ CARDS ============ --}}
                @elseif ($part['type'] === 'cards')
                    @if ($part['body'])<div class="lesson-html mb-4">{!! $part['body'] !!}</div>@endif
                    <div class="grid md:grid-cols-2 gap-3">
                        @foreach ($part['meta']['cards'] ?? [] as $c => $card)
                            <div x-data="{ open: false }"
                                 @click="open = !open; hit({{ $i }}, 'c{{ $c }}')"
                                 class="border rounded-xl p-4 cursor-pointer transition"
                                 :class="(hits[{{ $i }}] || []).includes('c{{ $c }}') ? 'border-teal bg-tealsoft/30' : 'border-line bg-paper/50 hover:border-teal'">
                                <div class="flex items-center justify-between gap-2">
                                    <h4 class="font-semibold text-sm">{{ $card['icon'] ?? '' }} {{ $card['title'] }}</h4>
                                    <span class="text-slate transition" :class="open && 'rotate-180'">▾</span>
                                </div>
                                <div x-show="open" x-collapse class="text-sm text-slate mt-2">
                                    {{ $card['body'] }}
                                    @if (! empty($card['example']))
                                        <span class="block mt-2 font-mono text-xs text-coral bg-coral/10 rounded-md px-2.5 py-1.5 break-all">{{ $card['example'] }}</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                {{-- ============ EXERCISE (spot the red flags) ============ --}}
                @elseif ($part['type'] === 'exercise')
                    @php $f = 0; @endphp
                    @if ($part['body'])<div class="lesson-html mb-4">{!! $part['body'] !!}</div>@endif

                    <div class="flex items-center justify-between gap-3 rounded-t-xl px-4 py-3 text-white text-sm font-semibold" style="background:#0a5249">
                        <span>📧 Reported specimen {{ $part['meta']['specimen'] ?? '' }}</span>
                        <span class="font-mono text-xs rounded-full px-3 py-1" style="background:rgba(255,255,255,.16)">
                            <span x-text="got({{ $i }})"></span> / {{ $needed[$i] }} found
                        </span>
                    </div>
                    <div class="border border-line border-t-0 rounded-b-xl overflow-hidden bg-card">
                        <div class="px-5 py-4 border-b border-line bg-paper/60 text-sm grid gap-1.5">
                            <div class="flex gap-2"><span class="text-slate w-14 shrink-0">From</span>
                                <span>"{{ $part['meta']['from_name'] ?? '' }}"
                                @if (isset($part['meta']['from_flag']))
                                    <button type="button" @click="hit({{ $i }}, 'f{{ $f }}')" class="hotspot font-mono text-[13px]"
                                            :class="(hits[{{ $i }}] || []).includes('f{{ $f }}') && 'caught'">&lt;{{ $part['meta']['from_email'] }}&gt;</button>
                                @else
                                    <span class="font-mono text-[13px]">&lt;{{ $part['meta']['from_email'] ?? '' }}&gt;</span>
                                @endif
                                </span>
                            </div>
                            @if (isset($part['meta']['from_flag']))
                                <div x-show="(hits[{{ $i }}] || []).includes('f{{ $f }}')" x-transition class="annot">
                                    <b>Red flag · {{ $part['meta']['from_flag']['tag'] }}</b>{{ $part['meta']['from_flag']['why'] }}
                                </div>
                                @php $f++; @endphp
                            @endif
                            <div class="flex gap-2"><span class="text-slate w-14 shrink-0">To</span><span>{{ $part['meta']['to'] ?? '' }}</span></div>
                            <div class="flex gap-2"><span class="text-slate w-14 shrink-0">Subject</span><b>{{ $part['meta']['subject'] ?? '' }}</b></div>
                        </div>
                        <div class="p-5 text-[15px] leading-7">
                            @foreach ($part['meta']['body'] ?? [] as $block)
                                @if (isset($block['button']))
                                    <p class="text-center my-3">
                                        <button type="button" @click="hit({{ $i }}, 'f{{ $f }}')"
                                                class="hotspot inline-block" :class="(hits[{{ $i }}] || []).includes('f{{ $f }}') && 'caught'">
                                            <span class="inline-block text-white font-semibold text-sm px-6 py-2.5 rounded-md" style="background:#0070BA">{{ $block['button'] }}</span>
                                        </button><br>
                                        <span class="text-xs text-slate">Link preview: <span class="font-mono">{{ $block['url'] }}</span></span>
                                    </p>
                                    <div x-show="(hits[{{ $i }}] || []).includes('f{{ $f }}')" x-transition class="annot">
                                        <b>Red flag · {{ $block['tag'] }}</b>{{ $block['why'] }}
                                    </div>
                                    @php $f++; @endphp
                                @elseif (isset($block['tag']))
                                    <p class="mb-3">{{ $block['pre'] ?? '' }}<button type="button" @click="hit({{ $i }}, 'f{{ $f }}')"
                                            class="hotspot" :class="(hits[{{ $i }}] || []).includes('f{{ $f }}') && 'caught'">{{ $block['hot'] }}</button>{{ $block['post'] ?? '' }}</p>
                                    <div x-show="(hits[{{ $i }}] || []).includes('f{{ $f }}')" x-transition class="annot">
                                        <b>Red flag · {{ $block['tag'] }}</b>{{ $block['why'] }}
                                    </div>
                                    @php $f++; @endphp
                                @else
                                    <p class="mb-3 whitespace-pre-line">{{ $block['text'] ?? '' }}</p>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    <p class="text-sm text-teal font-medium mt-3" x-show="can({{ $i }})" x-transition>All {{ $needed[$i] }} red flags found ✓ Nice instincts.</p>

                {{-- ============ QUIZ ============ --}}
                @elseif ($part['type'] === 'quiz')
                    @if ($part['body'])<div class="lesson-html mb-4">{!! $part['body'] !!}</div>@endif
                    @foreach ($part['meta']['questions'] ?? [] as $q => $question)
                        @php $k = "'{$i}-{$q}'"; @endphp
                        <div class="border border-line rounded-xl p-4 mb-4">
                            <h4 class="font-semibold text-sm mb-3">{{ $q + 1 }}. {{ $question['q'] }}</h4>
                            @foreach ($question['options'] as $o => $opt)
                                <button type="button"
                                        @click="answer({{ $i }}, {{ $q }}, {{ $o }}, {{ $question['answer'] }})"
                                        :disabled="quiz[{{ $k }}]"
                                        class="q-opt"
                                        :class="quiz[{{ $k }}]
                                            ? (quiz[{{ $k }}].picked === {{ $o }}
                                                ? (quiz[{{ $k }}].right ? 'sel-right' : 'sel-wrong')
                                                : ({{ $o }} === {{ $question['answer'] }} && ! quiz[{{ $k }}].right ? 'reveal-right' : ''))
                                            : ''">
                                    <span class="radio"></span>{{ $opt }}
                                </button>
                            @endforeach
                            <div x-show="quiz[{{ $k }}]" x-transition class="text-[13px] rounded-lg px-3.5 py-2.5 mt-1"
                                 :class="quiz[{{ $k }}] && quiz[{{ $k }}].right ? 'bg-tealsoft text-teal' : 'bg-coral/10 text-coral'">
                                <span x-show="quiz[{{ $k }}] && quiz[{{ $k }}].right">✓ Correct.</span>
                                <span x-show="quiz[{{ $k }}] && ! quiz[{{ $k }}].right">✗ Not quite.</span>
                                {{ $question['note'] ?? '' }}
                            </div>
                        </div>
                    @endforeach
                @endif

                {{-- ============ Footer / gate ============ --}}
                <div class="mt-7 flex items-center gap-3 flex-wrap">
                    <button type="button" @click="next({{ $i }})" :disabled="! can({{ $i }})"
                            class="inline-flex items-center gap-2 font-bold px-6 py-3 rounded-xl transition"
                            :class="can({{ $i }}) ? 'bg-teal text-white hover:bg-teal/90' : 'bg-line text-slate cursor-not-allowed'">
                        {{ $i < $n - 1 ? 'Continue' : 'Finish lesson' }}
                    </button>
                    @if ($needed[$i] > 0)
                        <span class="text-sm text-slate" x-show="! can({{ $i }})"
                              x-text="got({{ $i }}) + ' of {{ $needed[$i] }} ' + '{{ $part['type'] === 'cards' ? 'cards viewed' : ($part['type'] === 'quiz' ? 'answered' : 'red flags found') }}'"></span>
                    @else
                        <span class="text-sm text-slate">Part {{ $i + 1 }} / {{ $n }}</span>
                    @endif
                </div>
            </section>
        @endforeach

        {{-- ============ Completion ============ --}}
        <section x-show="active === {{ $n }}" x-transition
                 class="bg-card border border-line rounded-2xl card-shadow p-9 text-center">
            <div class="w-16 h-16 rounded-2xl bg-tealsoft grid place-items-center mx-auto mb-4">
                <svg width="34" height="38" viewBox="0 0 22 24" fill="none"><path d="M11 1L21 5v6c0 6.1-4.3 10.6-10 12C5.3 21.6 1 17.1 1 11V5l10-4z" fill="#0f766e"/><path d="M7 11.5l3 3 5.5-5.5" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </div>
            <h2 class="font-display text-2xl font-semibold mb-2">Lesson complete — you're harder to fool</h2>
            <p class="text-slate max-w-md mx-auto mb-6">You've finished <b>{{ $lesson->title }}</b>. Mark it complete to update your progress and risk score.</p>

            <div class="flex justify-center gap-3.5 flex-wrap mb-7">
                @if ($lesson->points)
                    <div class="bg-paper/60 border border-line rounded-xl px-6 py-3.5 min-w-[130px]">
                        <div class="font-display text-2xl font-semibold text-teal">+{{ $lesson->points }} pts</div>
                        <div class="text-xs text-slate mt-0.5">Points earned</div>
                    </div>
                @endif
                <div class="bg-paper/60 border border-line rounded-xl px-6 py-3.5 min-w-[130px]">
                    <div class="font-display text-2xl font-semibold text-ink">{{ $n }}/{{ $n }}</div>
                    <div class="text-xs text-slate mt-0.5">Parts finished</div>
                </div>
                @if ($quizTotal > 0)
                    <div class="bg-paper/60 border border-line rounded-xl px-6 py-3.5 min-w-[130px]">
                        <div class="font-display text-2xl font-semibold text-ink" x-text="quizStats.answered ? quizStats.correct + '/{{ $quizTotal }}' : '—'"></div>
                        <div class="text-xs text-slate mt-0.5">Knowledge check</div>
                    </div>
                @endif
            </div>

            @if ($completed)
                <p class="text-teal font-medium">✓ You've already completed this lesson.</p>
                <a href="{{ route('lessons.index') }}" class="inline-block mt-4 border border-teal text-teal font-semibold px-6 py-3 rounded-xl hover:bg-tealsoft transition">Back to lessons</a>
            @else
                <form method="POST" action="{{ route('lessons.complete', $lesson->id) }}" class="inline">@csrf
                    <button class="bg-teal text-white font-bold px-7 py-3 rounded-xl hover:bg-teal/90 transition">Mark as complete</button>
                </form>
                <a href="{{ route('lessons.index') }}" class="inline-block ml-2 border border-line text-slate font-semibold px-6 py-3 rounded-xl hover:border-teal hover:text-teal transition">Back to lessons</a>
            @endif
        </section>
    </div>
</div>
</div>

<style>
.lesson-html h2{font-family:'Fraunces',serif;font-size:1.3rem;font-weight:600;margin:1.3rem 0 .5rem}
.lesson-html h3{font-family:'Fraunces',serif;font-size:1.1rem;font-weight:600;margin:1.1rem 0 .4rem}
.lesson-html p{margin-bottom:.8rem;line-height:1.7;color:#3C4A46}
.lesson-html ul{margin:.5rem 0 1rem;padding-left:1.2rem;list-style:disc}
.lesson-html ol{margin:.5rem 0 1rem;padding-left:1.2rem;list-style:decimal}
.lesson-html li{margin-bottom:.35rem;color:#3C4A46}
.lesson-html strong{color:#1d2520;font-weight:600}
.lesson-html a{color:#0f766e;text-decoration:underline}
.lesson-html code{font-family:monospace;font-size:.85em;background:#FAE9E8;color:#B83A36;padding:2px 6px;border-radius:5px}
.lesson-html blockquote{border-left:3px solid #0f766e;background:#EAF5F1;padding:10px 14px;border-radius:0 8px 8px 0;margin:1rem 0;color:#1d2520}

/* exercise hotspots */
.hotspot{background:none;border:none;font:inherit;color:inherit;padding:1px 3px;border-radius:5px;cursor:pointer;border-bottom:2px dotted #C2CFCB;transition:background .15s}
.hotspot:hover{background:#d6ece9}
.hotspot.caught{background:#FAE9E8;color:#B83A36;border-bottom-color:#B83A36;font-weight:600;cursor:default}
.annot{font-size:12.5px;background:#FAE9E8;border-left:3px solid #B83A36;color:#7E2B27;padding:8px 12px;border-radius:0 8px 8px 0;max-width:560px;margin:4px 0 10px}
.annot b{display:block;font-size:11px;text-transform:uppercase;letter-spacing:.05em;margin-bottom:2px}

/* quiz options */
.q-opt{display:flex;align-items:flex-start;gap:10px;border:1px solid #e6e2d8;border-radius:10px;padding:11px 14px;margin-bottom:8px;font-size:14px;width:100%;background:#fff;text-align:left;cursor:pointer;transition:border-color .15s}
.q-opt:hover:not(:disabled){border-color:#0f766e}
.q-opt:disabled{cursor:default;opacity:.95}
.q-opt .radio{flex:none;width:16px;height:16px;border-radius:50%;border:2px solid #C2CFCB;margin-top:2px}
.q-opt.sel-right{border-color:#0f766e;background:#d6ece9}
.q-opt.sel-right .radio{border-color:#0f766e;background:#0f766e}
.q-opt.sel-wrong{border-color:#d1495b;background:rgba(209,73,91,.08)}
.q-opt.sel-wrong .radio{border-color:#d1495b;background:#d1495b}
.q-opt.reveal-right{border-color:#0f766e}
</style>

<script>
function lessonReader(cfg) {
    return {
        total: cfg.total,
        needed: cfg.needed,
        active: cfg.alreadyDone ? 0 : cfg.start,
        maxUnlocked: cfg.alreadyDone ? cfg.total : cfg.start,
        done: cfg.alreadyDone
            ? Array.from({length: cfg.total}, (_, i) => i)
            : Array.from({length: cfg.start}, (_, i) => i),
        finished: cfg.alreadyDone,
        hits: {},                                  // sectionIndex -> ['c0','f1',...]
        quiz: {},                                  // 'i-q' -> {picked, right}
        quizStats: { answered: 0, correct: 0 },

        pct() { return Math.round(this.done.length / this.total * 100); },

        hit(i, key) {
            if (! this.hits[i]) this.hits[i] = [];
            if (! this.hits[i].includes(key)) this.hits[i].push(key);
        },
        got(i) { return (this.hits[i] || []).length; },
        can(i) { return this.done.includes(i) || this.got(i) >= (this.needed[i] || 0); },

        answer(i, q, opt, ans) {
            const k = i + '-' + q;
            if (this.quiz[k]) return;              // one shot, no retries
            const right = opt === ans;
            this.quiz[k] = { picked: opt, right };
            this.quizStats.answered++;
            if (right) this.quizStats.correct++;
            this.hit(i, 'q' + q);
        },

        go(i) {
            if (i > this.maxUnlocked) return;
            this.active = i;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },
        next(i) {
            if (! this.can(i)) return;
            if (! this.done.includes(i)) this.done.push(i);
            this.maxUnlocked = Math.max(this.maxUnlocked, i + 1);
            if (i + 1 >= this.total) this.finished = true;
            this.active = i + 1;
            this.persist();
            window.scrollTo({ top: 180, behavior: 'smooth' });
        },
        persist() {
            if (cfg.alreadyDone) return;
            fetch(cfg.progressUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': cfg.csrf },
                body: JSON.stringify({
                    current_section: Math.min(this.maxUnlocked, this.total - 1),
                    progress_percent: this.pct(),
                }),
            }).catch(() => {});
        },
    }
}
</script>
@endsection
