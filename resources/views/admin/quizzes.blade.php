@extends('admin.layout')
@section('title', 'Quizzes')
@section('heading', 'Quiz management')

@section('content')
    <div class="grid lg:grid-cols-3 gap-5">
        {{-- Create form --}}
        <div class="bg-card border border-line rounded-2xl p-6 card-shadow h-fit">
            <h2 class="font-display text-lg font-semibold mb-4">New quiz</h2>
            <form method="POST" action="{{ route('admin.quizzes.store') }}" class="space-y-3">
                @csrf
                <input type="text" name="title" placeholder="Title" required
                       class="w-full bg-paper border border-line rounded-lg px-3 py-2 text-sm focus:border-teal focus:outline-none">
                <textarea name="description" rows="2" placeholder="Short description (shown on the quiz card)"
                          class="w-full bg-paper border border-line rounded-lg px-3 py-2 text-sm focus:border-teal focus:outline-none resize-y"></textarea>
                <select name="lesson_id" class="w-full bg-paper border border-line rounded-lg px-3 py-2 text-sm focus:border-teal focus:outline-none">
                    <option value="">No linked lesson</option>
                    @foreach ($lessons as $l)
                        <option value="{{ $l->id }}">{{ $l->title }}</option>
                    @endforeach
                </select>
                <button class="w-full bg-teal text-white font-medium py-2 rounded-lg hover:bg-teal/90 transition">Create quiz</button>
                <p class="text-xs text-slate">After creating, open <b>Questions</b> to add its questions.</p>
            </form>
        </div>

        {{-- List --}}
        <div class="lg:col-span-2 space-y-3">
            @foreach ($quizzes as $quiz)
                <div class="bg-card border border-line rounded-2xl card-shadow overflow-hidden">
                    <div class="flex flex-wrap items-center gap-3 px-5 py-3.5">
                        <div class="flex-1 min-w-[200px]">
                            <p class="font-medium text-sm">{{ $quiz->title }}</p>
                            <p class="text-xs text-slate mt-0.5">{{ $quiz->questions_count }} {{ Str::plural('question', $quiz->questions_count) }}{{ $quiz->description ? ' · '.Str::limit($quiz->description, 60) : '' }}</p>
                        </div>
                        <span class="text-xs {{ $quiz->is_active ? 'text-teal' : 'text-slate' }}">{{ $quiz->is_active ? 'Active' : 'Hidden' }}</span>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.quizzes.questions', $quiz->id) }}"
                               class="text-xs bg-teal text-white px-2.5 py-1 rounded-lg hover:bg-teal/90 transition">Questions</a>
                            <form method="POST" action="{{ route('admin.quizzes.update', $quiz->id) }}">
                                @csrf @method('PUT')
                                <input type="hidden" name="is_active" value="{{ $quiz->is_active ? 0 : 1 }}">
                                <button class="text-xs border border-line px-2.5 py-1 rounded-lg hover:border-teal hover:text-teal transition">{{ $quiz->is_active ? 'Hide' : 'Show' }}</button>
                            </form>
                            <form method="POST" action="{{ route('admin.quizzes.delete', $quiz->id) }}"
                                  onsubmit="return confirm('Delete this quiz and all its questions?')">
                                @csrf @method('DELETE')
                                <button class="text-xs border border-coral/40 text-coral px-2.5 py-1 rounded-lg hover:bg-coral/10 transition">Delete</button>
                            </form>
                        </div>
                    </div>

                    {{-- Expandable edit --}}
                    <details>
                        <summary class="px-5 py-2 text-xs text-slate border-t border-line cursor-pointer hover:text-teal select-none">Edit details</summary>
                        <form method="POST" action="{{ route('admin.quizzes.update', $quiz->id) }}" class="px-5 pb-5 pt-2 space-y-3 bg-paper/40">
                            @csrf @method('PUT')
                            <div>
                                <label class="block text-xs text-slate mb-1">Title</label>
                                <input name="title" value="{{ $quiz->title }}" required
                                       class="w-full bg-card border border-line rounded-lg px-3 py-2 text-sm focus:border-teal focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs text-slate mb-1">Description</label>
                                <textarea name="description" rows="2"
                                          class="w-full bg-card border border-line rounded-lg px-3 py-2 text-sm focus:border-teal focus:outline-none resize-y">{{ $quiz->description }}</textarea>
                            </div>
                            <div>
                                <label class="block text-xs text-slate mb-1">Linked lesson</label>
                                <select name="lesson_id" class="w-full bg-card border border-line rounded-lg px-3 py-2 text-sm focus:border-teal focus:outline-none">
                                    <option value="">No linked lesson</option>
                                    @foreach ($lessons as $l)
                                        <option value="{{ $l->id }}" @selected($quiz->lesson_id === $l->id)>{{ $l->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button class="bg-teal text-white text-sm font-medium px-5 py-2 rounded-lg hover:bg-teal/90 transition">Save changes</button>
                        </form>
                    </details>
                </div>
            @endforeach
            <div class="p-2">{{ $quizzes->links() }}</div>
        </div>
    </div>
@endsection
