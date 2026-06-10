@extends('admin.layout')
@section('title', 'Overview')
@section('heading', 'Overview')

@section('content')
    <div class="grid sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        @php
            $cards = [
                ['Users', $totalUsers],
                ['Lessons', $totalLessons],
                ['Quizzes', $totalQuizzes],
                ['Scans', $totalScans],
                ['Reports', $totalReports],
            ];
        @endphp
        @foreach ($cards as [$label, $value])
            <div class="bg-card border border-line rounded-2xl p-5 card-shadow">
                <p class="text-sm text-slate mb-1">{{ $label }}</p>
                <p class="font-display text-3xl font-semibold">{{ $value }}</p>
            </div>
        @endforeach
    </div>

    <div class="grid lg:grid-cols-3 gap-5">
        {{-- Risk distribution --}}
        <div class="bg-card border border-line rounded-2xl p-6 card-shadow">
            <h2 class="font-display text-lg font-semibold mb-4">Risk distribution</h2>
            @php
                $riskRows = [['High','high','coral'],['Medium','medium','amber'],['Low','low','teal']];
                $maxRisk = max(1, $risk['high'], $risk['medium'], $risk['low']);
            @endphp
            <div class="space-y-3">
                @foreach ($riskRows as [$label, $key, $color])
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-slate">{{ $label }}</span>
                            <span class="font-medium text-{{ $color }}">{{ $risk[$key] }}</span>
                        </div>
                        <div class="h-2 bg-paper rounded-full overflow-hidden">
                            <div class="h-full bg-{{ $color }} rounded-full" style="width: {{ ($risk[$key] / $maxRisk) * 100 }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Recent users --}}
        <div class="lg:col-span-2 bg-card border border-line rounded-2xl p-6 card-shadow">
            <h2 class="font-display text-lg font-semibold mb-4">Recent users</h2>
            <div class="divide-y divide-line">
                @foreach ($recentUsers as $u)
                    <div class="flex items-center justify-between py-2.5">
                        <div>
                            <p class="text-sm font-medium">{{ $u->name }}</p>
                            <p class="text-xs text-slate">{{ $u->email }}</p>
                        </div>
                        <span class="text-xs px-2 py-0.5 rounded-full {{ $u->role === 'admin' ? 'bg-amber/10 text-amber' : 'bg-paper text-slate' }}">{{ $u->role }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
