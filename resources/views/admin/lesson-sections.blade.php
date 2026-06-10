@extends('admin.layout')
@section('title', 'Sections')
@section('heading', 'Sections — '.$lesson->title)

@php
    $typeColor = ['read'=>'slate','cards'=>'teal','exercise'=>'amber','quiz'=>'coral'];
@endphp

@section('content')
<div class="mb-5">
    <a href="{{ route('admin.lessons') }}" class="text-sm text-slate hover:text-teal transition">← Back to lessons</a>
    <p class="text-slate text-sm mt-2">
        Each section is one "part" of the lesson. <b>Body</b> accepts HTML.
        Interactive types (<b>cards</b>, <b>exercise</b>, <b>quiz</b>) also need <b>Meta JSON</b> — copy a template from the examples at the bottom.
    </p>
</div>

{{-- Existing sections --}}
@forelse ($lesson->sections as $section)
    <div class="bg-card border border-line rounded-2xl p-6 card-shadow mb-4">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2">
                <span class="text-xs font-semibold uppercase tracking-wide text-teal">Part {{ $loop->iteration }}</span>
                <span class="text-[11px] font-bold uppercase px-2 py-0.5 rounded-full bg-{{ $typeColor[$section->type] ?? 'slate' }}/10 text-{{ $typeColor[$section->type] ?? 'slate' }}">{{ $section->type }}</span>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-xs text-slate">ID {{ $section->id }}</span>
                <form method="POST" action="{{ route('admin.lessons.sections.delete', $section->id) }}"
                      onsubmit="return confirm('Delete this section?')">
                    @csrf @method('DELETE')
                    <button class="text-xs border border-coral/40 text-coral px-2.5 py-1 rounded-lg hover:bg-coral/10 transition">Delete</button>
                </form>
            </div>
        </div>
        <form method="POST" action="{{ route('admin.lessons.sections.update', $section->id) }}" class="space-y-3">
            @csrf @method('PUT')
            <div class="grid md:grid-cols-[110px_120px_1fr_80px] gap-3">
                <div>
                    <label class="block text-xs text-slate mb-1">Type</label>
                    <select name="type" class="w-full bg-paper border border-line rounded-lg px-2 py-2 text-sm focus:border-teal focus:outline-none">
                        @foreach (['read','cards','exercise','quiz'] as $t)
                            <option value="{{ $t }}" @selected($section->type === $t)>{{ ucfirst($t) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-slate mb-1">Label</label>
                    <input name="label" value="{{ $section->label }}" placeholder="Read"
                           class="w-full bg-paper border border-line rounded-lg px-3 py-2 text-sm focus:border-teal focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs text-slate mb-1">Title</label>
                    <input name="title" value="{{ $section->title }}" required
                           class="w-full bg-paper border border-line rounded-lg px-3 py-2 text-sm focus:border-teal focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs text-slate mb-1">Order</label>
                    <input name="sort_order" type="number" value="{{ $section->sort_order }}" min="0"
                           class="w-full bg-paper border border-line rounded-lg px-3 py-2 text-sm focus:border-teal focus:outline-none">
                </div>
            </div>
            <div>
                <label class="block text-xs text-slate mb-1">Body (HTML — intro text shown above interactive content)</label>
                <textarea name="body" rows="4"
                          class="w-full bg-paper border border-line rounded-lg px-3 py-2 text-sm font-mono focus:border-teal focus:outline-none resize-y">{{ $section->body }}</textarea>
            </div>
            <div>
                <label class="block text-xs text-slate mb-1">Meta JSON (required for cards / exercise / quiz)</label>
                <textarea name="meta" rows="6" placeholder='{"cards":[...]} — see examples below'
                          class="w-full bg-paper border border-line rounded-lg px-3 py-2 text-xs font-mono focus:border-teal focus:outline-none resize-y">{{ $section->meta ? json_encode($section->meta, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : '' }}</textarea>
            </div>
            <button class="bg-teal text-white text-sm font-medium px-5 py-2 rounded-lg hover:bg-teal/90 transition">Save</button>
        </form>
    </div>
@empty
    <div class="bg-card border border-line rounded-2xl p-8 text-center card-shadow mb-4">
        <p class="text-slate text-sm">No sections yet. Add the first one below — until then, the lesson page falls back to its old content field.</p>
    </div>
@endforelse

{{-- Add new section --}}
<div class="bg-card border border-line rounded-2xl p-6 card-shadow mt-6">
    <h2 class="font-display text-lg font-semibold mb-4">Add a section</h2>
    <form method="POST" action="{{ route('admin.lessons.sections.store', $lesson->id) }}" class="space-y-3">
        @csrf
        <div class="grid md:grid-cols-[110px_120px_1fr] gap-3">
            <div>
                <label class="block text-xs text-slate mb-1">Type</label>
                <select name="type" class="w-full bg-paper border border-line rounded-lg px-2 py-2 text-sm focus:border-teal focus:outline-none">
                    <option value="read">Read</option>
                    <option value="cards">Cards</option>
                    <option value="exercise">Exercise</option>
                    <option value="quiz">Quiz</option>
                </select>
            </div>
            <div>
                <label class="block text-xs text-slate mb-1">Label</label>
                <input name="label" placeholder="Read"
                       class="w-full bg-paper border border-line rounded-lg px-3 py-2 text-sm focus:border-teal focus:outline-none">
            </div>
            <div>
                <label class="block text-xs text-slate mb-1">Title</label>
                <input name="title" required placeholder="Why phishing works"
                       class="w-full bg-paper border border-line rounded-lg px-3 py-2 text-sm focus:border-teal focus:outline-none">
            </div>
        </div>
        <div>
            <label class="block text-xs text-slate mb-1">Body (HTML)</label>
            <textarea name="body" rows="5" placeholder="<p>Over <strong>90% of breaches</strong> start with a phishing email.</p>"
                      class="w-full bg-paper border border-line rounded-lg px-3 py-2 text-sm font-mono focus:border-teal focus:outline-none resize-y"></textarea>
        </div>
        <div>
            <label class="block text-xs text-slate mb-1">Meta JSON (leave empty for Read sections)</label>
            <textarea name="meta" rows="6" placeholder='Copy a template from the examples below'
                      class="w-full bg-paper border border-line rounded-lg px-3 py-2 text-xs font-mono focus:border-teal focus:outline-none resize-y"></textarea>
        </div>
        <button class="bg-teal text-white text-sm font-medium px-5 py-2 rounded-lg hover:bg-teal/90 transition">Add section</button>
    </form>
</div>

{{-- Meta templates --}}
<div class="bg-card border border-line rounded-2xl p-6 card-shadow mt-6">
    <h2 class="font-display text-lg font-semibold mb-1">Meta JSON templates</h2>
    <p class="text-slate text-sm mb-4">Copy, paste into the Meta field, and edit the values.</p>

    <details class="mb-2">
        <summary class="text-sm font-semibold text-teal cursor-pointer select-none">Cards — open-all-to-continue flag cards</summary>
        <pre class="bg-paper border border-line rounded-lg p-3 text-xs font-mono mt-2 overflow-x-auto">{
  "cards": [
    { "icon": "🎭", "title": "Sender doesn't match",
      "body": "Look past the display name — read the domain after the @.",
      "example": "\"PayPal\" &lt;security@paypa1-support.com&gt;" },
    { "icon": "⏱", "title": "Artificial urgency",
      "body": "Deadlines and threats push you to act before you think.",
      "example": "…within 24 hours or your account will be closed" }
  ]
}</pre>
    </details>

    <details class="mb-2">
        <summary class="text-sm font-semibold text-amber cursor-pointer select-none">Exercise — click-the-red-flags email</summary>
        <pre class="bg-paper border border-line rounded-lg p-3 text-xs font-mono mt-2 overflow-x-auto">{
  "specimen": "#1042",
  "from_name": "PayPal Security",
  "from_email": "security@paypa1-support.com",
  "from_flag": { "tag": "Spoofed sender", "why": "The domain is paypa1-support.com — not paypal.com." },
  "to": "you@example.com",
  "subject": "Unusual activity — verification required",
  "body": [
    { "hot": "Dear Valued Customer,", "tag": "Generic greeting", "why": "A real service knows your name." },
    { "text": "We detected unusual login activity on your account." },
    { "pre": "Please verify your identity ", "hot": "within 24 hours or your account will be suspended", "post": ".",
      "tag": "Artificial urgency", "why": "Deadline + threat = pressure to click." },
    { "button": "Verify My Account", "url": "http://paypa1-secure.win/login",
      "tag": "Mismatched link", "why": "Look-alike domain with a 1 instead of an l." },
    { "text": "Thank you,\nThe PayPal Security Team" }
  ]
}</pre>
        <p class="text-xs text-slate mt-1.5">Blocks: <code>{"text": …}</code> is plain; <code>{"hot": …, "tag": …, "why": …}</code> is a clickable red flag (optional <code>pre</code>/<code>post</code> around it); <code>{"button": …, "url": …, "tag": …, "why": …}</code> is a fake button flag.</p>
    </details>

    <details>
        <summary class="text-sm font-semibold text-coral cursor-pointer select-none">Quiz — one-shot knowledge check</summary>
        <pre class="bg-paper border border-line rounded-lg p-3 text-xs font-mono mt-2 overflow-x-auto">{
  "questions": [
    { "q": "What's the strongest signal an email is fake?",
      "options": [
        "It mentions passwords",
        "The sender's domain isn't the real company domain",
        "It arrived at night"
      ],
      "answer": 1,
      "note": "The domain after the @ is the truth — display names can say anything." }
  ]
}</pre>
        <p class="text-xs text-slate mt-1.5"><code>answer</code> is the 0-based index of the correct option.</p>
    </details>
</div>
@endsection
