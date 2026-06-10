@extends('admin.layout')
@section('title', 'Questions')
@section('heading', 'Questions — '.$quiz->title)

@section('content')
<div class="mb-5">
    <a href="{{ route('admin.quizzes') }}" class="text-sm text-slate hover:text-teal transition">← Back to quizzes</a>
    <p class="text-slate text-sm mt-2">Options A and B are required; C and D are optional. Pick which option is correct, and add an explanation — it's shown to learners after they answer.</p>
</div>

{{-- Existing questions --}}
@forelse ($quiz->questions as $q)
    <div class="bg-card border border-line rounded-2xl p-6 card-shadow mb-4">
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-semibold uppercase tracking-wide text-teal">Question {{ $loop->iteration }}</span>
            <div class="flex items-center gap-3">
                <span class="text-xs text-slate">Correct: <b class="uppercase text-teal">{{ $q->correct_answer }}</b></span>
                <form method="POST" action="{{ route('admin.quizzes.questions.delete', $q->id) }}"
                      onsubmit="return confirm('Delete this question?')">
                    @csrf @method('DELETE')
                    <button class="text-xs border border-coral/40 text-coral px-2.5 py-1 rounded-lg hover:bg-coral/10 transition">Delete</button>
                </form>
            </div>
        </div>
        <form method="POST" action="{{ route('admin.quizzes.questions.update', $q->id) }}" class="space-y-3">
            @csrf @method('PUT')
            <div>
                <label class="block text-xs text-slate mb-1">Question</label>
                <textarea name="question" rows="2" required
                          class="w-full bg-paper border border-line rounded-lg px-3 py-2 text-sm focus:border-teal focus:outline-none resize-y">{{ $q->question }}</textarea>
            </div>
            <div class="grid md:grid-cols-2 gap-3">
                @foreach (['a','b','c','d'] as $opt)
                    <div>
                        <label class="block text-xs text-slate mb-1 uppercase">Option {{ $opt }} {{ in_array($opt, ['a','b']) ? '' : '(optional)' }}</label>
                        <input name="option_{{ $opt }}" value="{{ $q->{'option_'.$opt} }}" {{ in_array($opt, ['a','b']) ? 'required' : '' }}
                               class="w-full bg-paper border border-line rounded-lg px-3 py-2 text-sm focus:border-teal focus:outline-none">
                    </div>
                @endforeach
            </div>
            <div class="grid md:grid-cols-[130px_1fr] gap-3">
                <div>
                    <label class="block text-xs text-slate mb-1">Correct answer</label>
                    <select name="correct_answer" class="w-full bg-paper border border-line rounded-lg px-2 py-2 text-sm focus:border-teal focus:outline-none">
                        @foreach (['a','b','c','d'] as $opt)
                            <option value="{{ $opt }}" @selected($q->correct_answer === $opt)>{{ strtoupper($opt) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-slate mb-1">Explanation (shown after answering)</label>
                    <input name="explanation" value="{{ $q->explanation }}"
                           class="w-full bg-paper border border-line rounded-lg px-3 py-2 text-sm focus:border-teal focus:outline-none">
                </div>
            </div>
            <button class="bg-teal text-white text-sm font-medium px-5 py-2 rounded-lg hover:bg-teal/90 transition">Save</button>
        </form>
    </div>
@empty
    <div class="bg-card border border-line rounded-2xl p-8 text-center card-shadow mb-4">
        <p class="text-slate text-sm">No questions yet — this quiz won't be useful until it has at least one. Add the first below.</p>
    </div>
@endforelse

{{-- Add new question --}}
<div class="bg-card border border-line rounded-2xl p-6 card-shadow mt-6">
    <h2 class="font-display text-lg font-semibold mb-4">Add a question</h2>
    <form method="POST" action="{{ route('admin.quizzes.questions.store', $quiz->id) }}" class="space-y-3">
        @csrf
        <div>
            <label class="block text-xs text-slate mb-1">Question</label>
            <textarea name="question" rows="2" required placeholder="What's the strongest signal an email is fake?"
                      class="w-full bg-paper border border-line rounded-lg px-3 py-2 text-sm focus:border-teal focus:outline-none resize-y"></textarea>
        </div>
        <div class="grid md:grid-cols-2 gap-3">
            @foreach (['a','b','c','d'] as $opt)
                <div>
                    <label class="block text-xs text-slate mb-1 uppercase">Option {{ $opt }} {{ in_array($opt, ['a','b']) ? '' : '(optional)' }}</label>
                    <input name="option_{{ $opt }}" {{ in_array($opt, ['a','b']) ? 'required' : '' }}
                           class="w-full bg-paper border border-line rounded-lg px-3 py-2 text-sm focus:border-teal focus:outline-none">
                </div>
            @endforeach
        </div>
        <div class="grid md:grid-cols-[130px_1fr] gap-3">
            <div>
                <label class="block text-xs text-slate mb-1">Correct answer</label>
                <select name="correct_answer" class="w-full bg-paper border border-line rounded-lg px-2 py-2 text-sm focus:border-teal focus:outline-none">
                    @foreach (['a','b','c','d'] as $opt)
                        <option value="{{ $opt }}">{{ strtoupper($opt) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs text-slate mb-1">Explanation</label>
                <input name="explanation" placeholder="Why the correct answer is correct"
                       class="w-full bg-paper border border-line rounded-lg px-3 py-2 text-sm focus:border-teal focus:outline-none">
            </div>
        </div>
        <button class="bg-teal text-white text-sm font-medium px-5 py-2 rounded-lg hover:bg-teal/90 transition">Add question</button>
    </form>
</div>
@endsection
