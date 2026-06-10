@extends('admin.layout')
@section('title', 'Tips')
@section('heading', 'Security tips')

@php
    $prioColor = ['critical' => 'coral', 'high' => 'amber', 'medium' => 'teal', 'low' => 'slate'];
    $levels    = ['high' => 'High-risk users', 'medium' => 'Medium-risk users', 'low' => 'Low-risk users'];
    $prios     = ['critical', 'high', 'medium', 'low'];
@endphp

@section('content')
    <p class="text-slate text-sm mb-5">
        Tips appear on the user-facing <b>Tips</b> page based on each user's risk level.
        High-risk users also see medium-level tips.
    </p>

    <div class="grid lg:grid-cols-3 gap-5">
        {{-- Create form --}}
        <div class="bg-card border border-line rounded-2xl p-6 card-shadow h-fit">
            <h2 class="font-display text-lg font-semibold mb-4">New tip</h2>
            <form method="POST" action="{{ route('admin.tips.store') }}" class="space-y-3">
                @csrf
                <input type="text" name="title" placeholder="Title (e.g. Hover before you click)" required
                       class="w-full bg-paper border border-line rounded-lg px-3 py-2 text-sm focus:border-teal focus:outline-none">
                <textarea name="description" rows="3" required placeholder="One or two sentences of practical advice"
                          class="w-full bg-paper border border-line rounded-lg px-3 py-2 text-sm focus:border-teal focus:outline-none resize-y"></textarea>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-xs text-slate mb-1">Shown to</label>
                        <select name="level" class="w-full bg-paper border border-line rounded-lg px-2 py-2 text-sm focus:border-teal focus:outline-none">
                            @foreach ($levels as $v => $label)
                                <option value="{{ $v }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-slate mb-1">Priority badge</label>
                        <select name="priority" class="w-full bg-paper border border-line rounded-lg px-2 py-2 text-sm focus:border-teal focus:outline-none">
                            @foreach ($prios as $p)
                                <option value="{{ $p }}">{{ ucfirst($p) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <button class="w-full bg-teal text-white font-medium py-2 rounded-lg hover:bg-teal/90 transition">Create tip</button>
            </form>
        </div>

        {{-- List --}}
        <div class="lg:col-span-2 space-y-3">
            @foreach ($tips as $tip)
                <div class="bg-card border border-line rounded-2xl card-shadow overflow-hidden">
                    <div class="flex flex-wrap items-center gap-3 px-5 py-3.5">
                        <div class="flex-1 min-w-[200px]">
                            <p class="font-medium text-sm">{{ $tip->title }}</p>
                            <p class="text-xs text-slate mt-0.5">{{ Str::limit($tip->description, 70) }}</p>
                        </div>
                        <span class="text-[11px] font-bold uppercase px-2.5 py-1 rounded-full bg-{{ $prioColor[$tip->priority] }}/10 text-{{ $prioColor[$tip->priority] }}">{{ $tip->priority }}</span>
                        <span class="text-xs text-slate">{{ $levels[$tip->level] }}</span>
                        <span class="text-xs {{ $tip->is_active ? 'text-teal' : 'text-slate' }}">{{ $tip->is_active ? 'Active' : 'Hidden' }}</span>
                        <div class="flex items-center gap-2">
                            <form method="POST" action="{{ route('admin.tips.update', $tip->id) }}">
                                @csrf @method('PUT')
                                <input type="hidden" name="is_active" value="{{ $tip->is_active ? 0 : 1 }}">
                                <button class="text-xs border border-line px-2.5 py-1 rounded-lg hover:border-teal hover:text-teal transition">{{ $tip->is_active ? 'Hide' : 'Show' }}</button>
                            </form>
                            <form method="POST" action="{{ route('admin.tips.delete', $tip->id) }}"
                                  onsubmit="return confirm('Delete this tip?')">
                                @csrf @method('DELETE')
                                <button class="text-xs border border-coral/40 text-coral px-2.5 py-1 rounded-lg hover:bg-coral/10 transition">Delete</button>
                            </form>
                        </div>
                    </div>

                    {{-- Expandable edit --}}
                    <details>
                        <summary class="px-5 py-2 text-xs text-slate border-t border-line cursor-pointer hover:text-teal select-none">Edit details</summary>
                        <form method="POST" action="{{ route('admin.tips.update', $tip->id) }}" class="px-5 pb-5 pt-2 space-y-3 bg-paper/40">
                            @csrf @method('PUT')
                            <div>
                                <label class="block text-xs text-slate mb-1">Title</label>
                                <input name="title" value="{{ $tip->title }}" required
                                       class="w-full bg-card border border-line rounded-lg px-3 py-2 text-sm focus:border-teal focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs text-slate mb-1">Description</label>
                                <textarea name="description" rows="2" required
                                          class="w-full bg-card border border-line rounded-lg px-3 py-2 text-sm focus:border-teal focus:outline-none resize-y">{{ $tip->description }}</textarea>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs text-slate mb-1">Shown to</label>
                                    <select name="level" class="w-full bg-card border border-line rounded-lg px-2 py-2 text-sm focus:border-teal focus:outline-none">
                                        @foreach ($levels as $v => $label)
                                            <option value="{{ $v }}" @selected($tip->level === $v)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs text-slate mb-1">Priority badge</label>
                                    <select name="priority" class="w-full bg-card border border-line rounded-lg px-2 py-2 text-sm focus:border-teal focus:outline-none">
                                        @foreach ($prios as $p)
                                            <option value="{{ $p }}" @selected($tip->priority === $p)>{{ ucfirst($p) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <button class="bg-teal text-white text-sm font-medium px-5 py-2 rounded-lg hover:bg-teal/90 transition">Save changes</button>
                        </form>
                    </details>
                </div>
            @endforeach
            <div class="p-2">{{ $tips->links() }}</div>
        </div>
    </div>
@endsection
