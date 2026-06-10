@extends('admin.layout')
@section('title', 'Simulations')
@section('heading', 'Simulation management')

@php $types = ['phishing_email' => 'Phishing email', 'fake_url' => 'Fake URL', 'social_engineering' => 'Social engineering']; @endphp

@section('content')
    <div class="grid lg:grid-cols-3 gap-5">
        {{-- Create form --}}
        <div class="bg-card border border-line rounded-2xl p-6 card-shadow h-fit">
            <h2 class="font-display text-lg font-semibold mb-4">New simulation</h2>
            <form method="POST" action="{{ route('admin.simulations.store') }}" class="space-y-3">
                @csrf
                <input type="text" name="title" placeholder="Title (e.g. The Urgent CEO Request)" required
                       class="w-full bg-paper border border-line rounded-lg px-3 py-2 text-sm focus:border-teal focus:outline-none">
                <select name="type" required class="w-full bg-paper border border-line rounded-lg px-3 py-2 text-sm focus:border-teal focus:outline-none">
                    @foreach ($types as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
                <input type="text" name="description" placeholder="One-line description (shown on the card)"
                       class="w-full bg-paper border border-line rounded-lg px-3 py-2 text-sm focus:border-teal focus:outline-none">
                <textarea name="content" rows="7" required
                          placeholder="From: Michael Chen, CEO&#10;Subject: Quick favor — urgent&#10;&#10;Hi, I need you to purchase five $100 gift cards..."
                          class="w-full bg-paper border border-line rounded-lg px-3 py-2 text-sm font-mono focus:border-teal focus:outline-none resize-y"></textarea>
                <button class="w-full bg-teal text-white font-medium py-2 rounded-lg hover:bg-teal/90 transition">Create simulation</button>
                <p class="text-xs text-slate">Tip: start lines with <code>From:</code>, <code>To:</code>, <code>Subject:</code> — the user page renders them as a real email header.</p>
            </form>
        </div>

        {{-- List --}}
        <div class="lg:col-span-2 space-y-3">
            @foreach ($simulations as $sim)
                <div class="bg-card border border-line rounded-2xl card-shadow overflow-hidden">
                    <div class="flex flex-wrap items-center gap-3 px-5 py-3.5">
                        <div class="flex-1 min-w-[200px]">
                            <p class="font-medium text-sm">{{ $sim->title }}</p>
                            <p class="text-xs text-slate mt-0.5">{{ $types[$sim->type] ?? $sim->type }} · {{ $sim->interactions_count }} {{ Str::plural('run', $sim->interactions_count) }}</p>
                        </div>
                        <span class="text-xs {{ $sim->is_active ? 'text-teal' : 'text-slate' }}">{{ $sim->is_active ? 'Active' : 'Hidden' }}</span>
                        <div class="flex items-center gap-2">
                            <form method="POST" action="{{ route('admin.simulations.update', $sim->id) }}">
                                @csrf @method('PUT')
                                <input type="hidden" name="is_active" value="{{ $sim->is_active ? 0 : 1 }}">
                                <button class="text-xs border border-line px-2.5 py-1 rounded-lg hover:border-teal hover:text-teal transition">{{ $sim->is_active ? 'Hide' : 'Show' }}</button>
                            </form>
                            <form method="POST" action="{{ route('admin.simulations.delete', $sim->id) }}"
                                  onsubmit="return confirm('Delete this simulation and its run history?')">
                                @csrf @method('DELETE')
                                <button class="text-xs border border-coral/40 text-coral px-2.5 py-1 rounded-lg hover:bg-coral/10 transition">Delete</button>
                            </form>
                        </div>
                    </div>

                    {{-- Expandable edit --}}
                    <details>
                        <summary class="px-5 py-2 text-xs text-slate border-t border-line cursor-pointer hover:text-teal select-none">Edit details</summary>
                        <form method="POST" action="{{ route('admin.simulations.update', $sim->id) }}" class="px-5 pb-5 pt-2 space-y-3 bg-paper/40">
                            @csrf @method('PUT')
                            <div class="grid md:grid-cols-[1fr_200px] gap-3">
                                <div>
                                    <label class="block text-xs text-slate mb-1">Title</label>
                                    <input name="title" value="{{ $sim->title }}" required
                                           class="w-full bg-card border border-line rounded-lg px-3 py-2 text-sm focus:border-teal focus:outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs text-slate mb-1">Type</label>
                                    <select name="type" class="w-full bg-card border border-line rounded-lg px-2 py-2 text-sm focus:border-teal focus:outline-none">
                                        @foreach ($types as $value => $label)
                                            <option value="{{ $value }}" @selected($sim->type === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs text-slate mb-1">Description</label>
                                <input name="description" value="{{ $sim->description }}"
                                       class="w-full bg-card border border-line rounded-lg px-3 py-2 text-sm focus:border-teal focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs text-slate mb-1">Message content</label>
                                <textarea name="content" rows="7"
                                          class="w-full bg-card border border-line rounded-lg px-3 py-2 text-sm font-mono focus:border-teal focus:outline-none resize-y">{{ $sim->content }}</textarea>
                            </div>
                            <button class="bg-teal text-white text-sm font-medium px-5 py-2 rounded-lg hover:bg-teal/90 transition">Save changes</button>
                        </form>
                    </details>
                </div>
            @endforeach
            <div class="p-2">{{ $simulations->links() }}</div>
        </div>
    </div>
@endsection
