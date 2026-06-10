@extends('layouts.app')
@section('title', $quiz->title)

@php $n = $quiz->questions->count(); @endphp

@section('content')

<div x-data="quizTaker({{ $n }})" class="max-w-2xl mx-auto">

    {{-- Hero --}}
    <div class="rounded-2xl mb-6 p-6 md:p-7" style="background:linear-gradient(135deg,#EAF5F1,#F4FAF8);border:1px solid #e6e2d8">
        <a href="{{ route('quizzes.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-teal mb-2 hover:underline">← All quizzes</a>
        <h1 class="font-display text-3xl font-semibold tracking-tight">{{ $quiz->title }}</h1>
        @if ($quiz->description)<p class="text-slate mt-1.5">{{ $quiz->description }}</p>@endif
        <div class="flex items-center gap-3 mt-4">
            <div class="flex-1 h-2 rounded-full overflow-hidden bg-white border border-line">
                <div class="h-full bg-teal rounded-full transition-all" :style="`width:${Math.round(answeredCount()/total*100)}%`"></div>
            </div>
            <span class="text-sm font-semibold text-slate" x-text="answeredCount() + ' / ' + total + ' answered'"></span>
        </div>
    </div>

    {{-- Question dots --}}
    <div class="flex flex-wrap gap-2 mb-5">
        @foreach ($quiz->questions as $i => $q)
            <button type="button" @click="go({{ $i }})"
                    class="w-9 h-9 rounded-full text-sm font-bold border transition"
                    :class="active === {{ $i }} ? 'bg-teal border-teal text-white'
                          : (answered[{{ $q->id }}] ? 'bg-tealsoft border-teal/40 text-teal' : 'bg-card border-line text-slate hover:border-teal')">
                {{ $i + 1 }}
            </button>
        @endforeach
    </div>

    <form method="POST" action="{{ route('quizzes.submit', $quiz->id) }}">
        @csrf
        @foreach ($quiz->questions as $i => $q)
            <div x-show="active === {{ $i }}" x-transition
                 class="bg-card border border-line rounded-2xl p-6 md:p-7 card-shadow">
                <p class="text-xs font-semibold tracking-wide uppercase text-teal mb-3">Question {{ $i + 1 }} of {{ $n }}</p>
                <p class="font-display text-lg font-semibold mb-5">{{ $q->question }}</p>
                <div class="space-y-2.5">
                    @foreach (['a','b','c','d'] as $opt)
                        @php $optText = $q->{"option_$opt"}; @endphp
                        @if ($optText)
                            <label class="flex items-center gap-3 border border-line rounded-xl px-4 py-3 cursor-pointer hover:border-teal transition has-[:checked]:border-teal has-[:checked]:bg-tealsoft">
                                <input type="radio" name="answers[{{ $q->id }}]" value="{{ $opt }}"
                                       @change="mark({{ $q->id }})" class="accent-teal">
                                <span class="w-6 h-6 rounded-full bg-paper border border-line grid place-items-center text-[11px] font-bold text-slate uppercase shrink-0">{{ $opt }}</span>
                                <span class="text-sm">{{ $optText }}</span>
                            </label>
                        @endif
                    @endforeach
                </div>

                <div class="mt-6 flex items-center justify-between gap-3">
                    <button type="button" @click="go({{ $i - 1 }})" @if ($i === 0) disabled @endif
                            class="border border-line text-slate font-semibold px-5 py-2.5 rounded-xl transition {{ $i === 0 ? 'opacity-40 cursor-not-allowed' : 'hover:border-teal hover:text-teal' }}">
                        ← Back
                    </button>

                    @if ($i < $n - 1)
                        <button type="button" @click="go({{ $i + 1 }})" :disabled="! answered[{{ $q->id }}]"
                                class="font-bold px-6 py-2.5 rounded-xl transition"
                                :class="answered[{{ $q->id }}] ? 'bg-teal text-white hover:bg-teal/90' : 'bg-line text-slate cursor-not-allowed'">
                            Next →
                        </button>
                    @else
                        <button type="submit" :disabled="answeredCount() < total"
                                class="font-bold px-6 py-2.5 rounded-xl transition"
                                :class="answeredCount() >= total ? 'bg-teal text-white hover:bg-teal/90' : 'bg-line text-slate cursor-not-allowed'">
                            Submit answers
                        </button>
                    @endif
                </div>
                @if ($i === $n - 1)
                    <p class="text-xs text-slate mt-3 text-right" x-show="answeredCount() < total"
                       x-text="'Answer all ' + total + ' questions to submit — ' + (total - answeredCount()) + ' left'"></p>
                @endif
            </div>
        @endforeach
    </form>
</div>

<script>
function quizTaker(total) {
    return {
        total,
        active: 0,
        answered: {},                   // question_id -> true
        mark(id) { this.answered[id] = true; },
        answeredCount() { return Object.keys(this.answered).length; },
        go(i) {
            if (i < 0 || i >= this.total) return;
            this.active = i;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },
    }
}
</script>
@endsection
