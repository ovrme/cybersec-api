@extends('admin.layout')
@section('title', 'Reports')
@section('heading', 'Generated reports')

@section('content')
    <div class="bg-card border border-line rounded-2xl overflow-hidden card-shadow">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-line text-left text-slate text-xs uppercase tracking-wide">
                    <th class="px-5 py-3 font-medium">ID</th>
                    <th class="px-5 py-3 font-medium">User</th>
                    <th class="px-5 py-3 font-medium">Generated</th>
                    <th class="px-5 py-3 font-medium text-right">File</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-line">
                @foreach ($reports as $report)
                    <tr>
                        <td class="px-5 py-3">#{{ $report->id }}</td>
                        <td class="px-5 py-3">{{ $report->user->name ?? '—' }}</td>
                        <td class="px-5 py-3 text-slate text-xs">{{ $report->created_at->format('M j, Y · g:i A') }}</td>
                        <td class="px-5 py-3 text-right">
                            <a href="{{ asset('storage/' . $report->file_path) }}" target="_blank" class="text-xs text-teal hover:underline">Download </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-4">{{ $reports->links() }}</div>
    </div>
@endsection
