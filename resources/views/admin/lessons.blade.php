@extends('admin.layout')
@section('title', 'Lessons')
@section('heading', 'Lesson management')

@section('content')
    <div class="grid lg:grid-cols-3 gap-5">
        {{-- Create form --}}
        <div class="bg-card border border-line rounded-2xl p-6 card-shadow h-fit">
            <h2 class="font-display text-lg font-semibold mb-4">New lesson</h2>
            <form method="POST" action="{{ route('admin.lessons.store') }}" class="space-y-3">
                @csrf
                <input type="text" name="title" placeholder="Title" required
                       class="w-full bg-paper border border-line rounded-lg px-3 py-2 text-sm focus:border-teal focus:outline-none">
                <input type="text" name="summary" placeholder="Summary (shown on the lesson hero)"
                       class="w-full bg-paper border border-line rounded-lg px-3 py-2 text-sm focus:border-teal focus:outline-none">
                <input type="text" name="category" placeholder="Category (e.g. Email Security)"
                       class="w-full bg-paper border border-line rounded-lg px-3 py-2 text-sm focus:border-teal focus:outline-none">
                <div class="grid grid-cols-3 gap-2">
                    <select name="difficulty" required class="bg-paper border border-line rounded-lg px-2 py-2 text-sm focus:border-teal focus:outline-none">
                        <option value="beginner">Beginner</option>
                        <option value="intermediate">Intermediate</option>
                        <option value="advanced">Advanced</option>
                    </select>
                    <input type="number" name="duration_minutes" placeholder="Mins" min="1" max="240"
                           class="bg-paper border border-line rounded-lg px-2 py-2 text-sm focus:border-teal focus:outline-none">
                    <input type="number" name="points" placeholder="Points" min="0" max="1000"
                           class="bg-paper border border-line rounded-lg px-2 py-2 text-sm focus:border-teal focus:outline-none">
                </div>
                <textarea name="content" rows="5" placeholder="Fallback content (used until the lesson has sections)" required
                          class="w-full bg-paper border border-line rounded-lg px-3 py-2 text-sm focus:border-teal focus:outline-none resize-y"></textarea>
                <button class="w-full bg-teal text-white font-medium py-2 rounded-lg hover:bg-teal/90 transition">Create lesson</button>
                <p class="text-xs text-slate">After creating, open <b>Sections</b> on the lesson to build its parts (read / cards / exercise / quiz).</p>
            </form>
        </div>

        {{-- List --}}
        <div class="lg:col-span-2 space-y-3">
            @foreach ($lessons as $lesson)
                @php $diff = ['beginner'=>'teal','intermediate'=>'amber','advanced'=>'coral'][$lesson->difficulty] ?? 'slate'; @endphp
                <div class="bg-card border border-line rounded-2xl card-shadow overflow-hidden">
                    <div class="flex flex-wrap items-center gap-3 px-5 py-3.5">
                        <div class="flex-1 min-w-[200px]">
                            <p class="font-medium text-sm">{{ $lesson->title }}</p>
                            <p class="text-xs text-slate mt-0.5">
                                {{ $lesson->category ?? 'No category' }}
                                · {{ $lesson->sections_count }} {{ Str::plural('section', $lesson->sections_count) }}
                                @if ($lesson->duration_minutes) · {{ $lesson->duration_minutes }} min @endif
                                @if ($lesson->points) · {{ $lesson->points }} pts @endif
                            </p>
                        </div>
                        <span class="text-xs px-2 py-0.5 rounded-full bg-{{ $diff }}/10 text-{{ $diff }}">{{ $lesson->difficulty }}</span>
                        <span class="text-xs {{ $lesson->is_active ? 'text-teal' : 'text-slate' }}">{{ $lesson->is_active ? 'Active' : 'Hidden' }}</span>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.lessons.sections', $lesson->id) }}"
                               class="text-xs bg-teal text-white px-2.5 py-1 rounded-lg hover:bg-teal/90 transition">Sections</a>
                            <form method="POST" action="{{ route('admin.lessons.update', $lesson->id) }}">
                                @csrf @method('PUT')
                                <input type="hidden" name="is_active" value="{{ $lesson->is_active ? 0 : 1 }}">
                                <button class="text-xs border border-line px-2.5 py-1 rounded-lg hover:border-teal hover:text-teal transition">{{ $lesson->is_active ? 'Hide' : 'Show' }}</button>
                            </form>
                            <form method="POST" action="{{ route('admin.lessons.delete', $lesson->id) }}"
                                  onsubmit="return confirm('Delete this lesson and its sections?')">
                                @csrf @method('DELETE')
                                <button class="text-xs border border-coral/40 text-coral px-2.5 py-1 rounded-lg hover:bg-coral/10 transition">Delete</button>
                            </form>
                        </div>
                    </div>

                    {{-- Expandable edit --}}
                    <details>
                        <summary class="px-5 py-2 text-xs text-slate border-t border-line cursor-pointer hover:text-teal select-none">Edit details</summary>
                        <form method="POST" action="{{ route('admin.lessons.update', $lesson->id) }}" class="px-5 pb-5 pt-2 space-y-3 bg-paper/40">
                            @csrf @method('PUT')
                            <div class="grid md:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs text-slate mb-1">Title</label>
                                    <input name="title" value="{{ $lesson->title }}" required
                                           class="w-full bg-card border border-line rounded-lg px-3 py-2 text-sm focus:border-teal focus:outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs text-slate mb-1">Category</label>
                                    <input name="category" value="{{ $lesson->category }}"
                                           class="w-full bg-card border border-line rounded-lg px-3 py-2 text-sm focus:border-teal focus:outline-none">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs text-slate mb-1">Summary</label>
                                <input name="summary" value="{{ $lesson->summary }}"
                                       class="w-full bg-card border border-line rounded-lg px-3 py-2 text-sm focus:border-teal focus:outline-none">
                            </div>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                <div>
                                    <label class="block text-xs text-slate mb-1">Difficulty</label>
                                    <select name="difficulty" class="w-full bg-card border border-line rounded-lg px-2 py-2 text-sm focus:border-teal focus:outline-none">
                                        @foreach (['beginner','intermediate','advanced'] as $d)
                                            <option value="{{ $d }}" @selected($lesson->difficulty === $d)>{{ ucfirst($d) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs text-slate mb-1">Minutes</label>
                                    <input type="number" name="duration_minutes" value="{{ $lesson->duration_minutes }}" min="1" max="240"
                                           class="w-full bg-card border border-line rounded-lg px-3 py-2 text-sm focus:border-teal focus:outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs text-slate mb-1">Points</label>
                                    <input type="number" name="points" value="{{ $lesson->points }}" min="0" max="1000"
                                           class="w-full bg-card border border-line rounded-lg px-3 py-2 text-sm focus:border-teal focus:outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs text-slate mb-1">Order</label>
                                    <input type="number" name="order_index" value="{{ $lesson->order_index }}" min="0"
                                           class="w-full bg-card border border-line rounded-lg px-3 py-2 text-sm focus:border-teal focus:outline-none">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs text-slate mb-1">Fallback content</label>
                                <textarea name="content" rows="4"
                                          class="w-full bg-card border border-line rounded-lg px-3 py-2 text-sm focus:border-teal focus:outline-none resize-y">{{ $lesson->content }}</textarea>
                            </div>
                            <button class="bg-teal text-white text-sm font-medium px-5 py-2 rounded-lg hover:bg-teal/90 transition">Save changes</button>
                        </form>
                    </details>
                </div>
            @endforeach
            <div class="p-2">{{ $lessons->links() }}</div>
        </div>
    </div>
@endsection
