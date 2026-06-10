@extends('layouts.app')
@section('title', 'Reports')

@section('content')
    <div class="flex flex-wrap items-end justify-between gap-3 mb-7">
        <div>
            <h1 class="font-display text-3xl font-semibold">Security reports</h1>
            <p class="text-slate text-sm mt-1">Generate a PDF summary of your security posture.</p>
        </div>
        <form method="POST" action="{{ route('reports.generate') }}">
            @csrf
            <button class="bg-teal text-white font-medium px-5 py-2.5 rounded-lg hover:bg-teal/90 transition">Generate new report (PDF)</button>
        </form>
    </div>

    <div class="bg-card border border-line rounded-2xl p-7 card-shadow">
        <h2 class="font-display text-lg font-semibold mb-4">Report history</h2>
        @if ($reports->isEmpty())
            <p class="text-slate text-sm">No reports yet. Generate your first one above.</p>
        @else
            <div class="divide-y divide-line">
                @foreach ($reports as $report)
                    <div class="flex items-center justify-between py-3">
                        <div>
                            <p class="text-sm font-medium">Report #{{ $report->id }}</p>
                            <p class="text-xs text-slate">{{ $report->created_at->format('M j, Y · g:i A') }}</p>
                        </div>
                        <a href="{{ asset('storage/' . $report->file_path) }}" target="_blank"
                           class="text-sm text-teal hover:underline">Download </a>
                    </div>
                @endforeach
            </div>
            <p class="text-xs text-slate mt-4">Note: download links require <code>php artisan storage:link</code> to have been run.</p>
        @endif
    </div>
@endsection
