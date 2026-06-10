@extends('admin.layout')
@section('title', 'Scan logs')
@section('heading', 'Scan logs')

@section('content')
    <div class="bg-card border border-line rounded-2xl overflow-hidden card-shadow">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-line text-left text-slate text-xs uppercase tracking-wide">
                    <th class="px-5 py-3 font-medium">User</th>
                    <th class="px-5 py-3 font-medium">Type</th>
                    <th class="px-5 py-3 font-medium">Input</th>
                    <th class="px-5 py-3 font-medium">Risk</th>
                    <th class="px-5 py-3 font-medium">When</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-line">
                @foreach ($logs as $log)
                    @php $rc = ['high'=>'coral','medium'=>'amber','low'=>'teal'][$log->risk_level] ?? 'slate'; @endphp
                    <tr>
                        <td class="px-5 py-3">{{ $log->user->name ?? 'Guest' }}</td>
                        <td class="px-5 py-3"><span class="text-xs px-2 py-0.5 rounded-full bg-paper text-slate">{{ $log->input_type }}</span></td>
                        {{-- {{ }} auto-escapes, so stored input is XSS-safe here --}}
                        <td class="px-5 py-3 text-slate max-w-xs truncate">{{ \Illuminate\Support\Str::limit($log->input_data, 50) }}</td>
                        <td class="px-5 py-3"><span class="text-xs font-medium text-{{ $rc }}">{{ $log->risk_level }} ({{ $log->risk_score }})</span></td>
                        <td class="px-5 py-3 text-slate text-xs">{{ $log->created_at->diffForHumans() }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-4">{{ $logs->links() }}</div>
    </div>
@endsection
