<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SkillShield')  ·  SkillShield</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600;9..144,700&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        display: ['Fraunces', 'serif'],
                        sans: ['Outfit', 'sans-serif'],
                    },
                    colors: {
                        paper: '#f7f5f0',
                        card: '#ffffff',
                        ink: '#1d2520',
                        slate: '#5a6760',
                        teal: '#0f766e',
                        tealsoft: '#d6ece9',
                        coral: '#d1495b',
                        amber: '#c9820a',
                        line: '#e6e2d8',
                    },
                },
            },
        }
    </script>
    <style>
        body { background-color: #f7f5f0; }
        .card-shadow { box-shadow: 0 1px 2px rgba(29,37,32,.04), 0 8px 24px -12px rgba(29,37,32,.12); }
        ::selection { background:#0f766e; color:#fff; }
        .fade-up { animation: fadeup .5s ease both; }
        @keyframes fadeup { from { opacity:0; transform:translateY(8px) } to { opacity:1; transform:none } }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="font-sans text-ink min-h-screen antialiased overflow-x-hidden">

@auth
<nav class="bg-card/85 backdrop-blur-md border-b border-line sticky top-0 z-50">
    <div class="w-full px-8 h-14 flex items-center justify-between gap-4">
        {{-- Brand --}}
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 shrink-0">
            <img src="{{ asset('images/logo.png') }}" alt="SkillShield" class="h-8 w-auto">
            <span class="font-display font-semibold text-lg">SkillShield</span>
        </a>

        {{-- Icon nav --}}
        <nav class="hidden lg:flex items-center gap-0.5 text-sm">
            @php
                $nav = [
                    ['dashboard',             'Dashboard',   'M3 12l9-9 9 9M5 10v10h14V10'],
                    ['lessons.index',         'Lessons',     'M12 14l9-5-9-5-9 5 9 5zM12 14v7'],
                    ['scanner.index',         'Scanner',     'M21 21l-4.3-4.3M11 19a8 8 0 1 0 0-16 8 8 0 0 0 0 16z'],
                    ['quizzes.index',         'Quizzes',     'M9 12l2 2 4-4M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z'],
                    ['simulations.index',     'Simulations', 'M13 2 3 14h9l-1 8 10-12h-9z'],
                    ['recommendations.index', 'Tips',        'M12 8v4l3 3m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0z'],
                    ['leaderboard.index',     'Ranks',       'M3 3v18h18M7 14l4-4 4 4 5-5'],
                ];
            @endphp
            @foreach ($nav as [$route, $label, $path])
                <a href="{{ route($route) }}"
                   class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg transition {{ request()->routeIs(explode('.', $route)[0].'*') ? 'bg-tealsoft text-teal font-medium' : 'text-slate hover:text-ink hover:bg-paper' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="{{ $path }}"/></svg>
                    {{ $label }}
                </a>
            @endforeach
        </nav>

        {{-- Right: admin + account dropdown --}}
        <div class="flex items-center gap-3 shrink-0">
            @if (auth()->user()->role === 'admin')
                <a href="{{ route('admin.dashboard') }}" class="text-xs font-medium text-amber bg-amber/10 px-2.5 py-1 rounded-full hover:bg-amber/20 transition">Admin</a>
            @endif

            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" @click.outside="open = false"
                        class="flex items-center gap-2 rounded-lg px-1.5 py-1 hover:bg-paper transition">
                    <span class="w-8 h-8 rounded-full bg-teal text-white flex items-center justify-center font-medium text-sm">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                    <span class="hidden sm:block text-sm font-medium">{{ auth()->user()->name }}</span>
                    <svg class="w-4 h-4 text-slate transition" :class="open && 'rotate-180'" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 9l6 6 6-6"/></svg>
                </button>

                <div x-show="open" x-transition x-cloak
                    class="absolute right-0 mt-2 w-48 bg-card border border-line rounded-xl card-shadow py-1.5 z-50">
                    <a href="{{ route('profile.show') }}" class="flex items-center gap-2.5 px-4 py-2 text-sm text-slate hover:bg-paper hover:text-ink transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 14a4 4 0 1 0-8 0M12 7a3 3 0 1 0 0 6 3 3 0 0 0 0-6zM20 21a8 8 0 1 0-16 0"/></svg>
                        Profile
                    </a>
                    <a href="{{ route('reports.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-sm text-slate hover:bg-paper hover:text-ink transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6M9 16h6M9 8h6M5 3h14v18H5z"/></svg>
                        Reports
                    </a>
                    <div class="border-t border-line my-1.5"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="flex items-center gap-2.5 w-full text-left px-4 py-2 text-sm text-coral hover:bg-coral/5 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 17l5-5-5-5M21 12H9M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/></svg>
                            Sign out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</nav>
@endauth

@if (session('status'))
    <div class="max-w-6xl mx-auto px-6 mt-5">
        <div class="border border-teal/30 bg-tealsoft text-teal text-sm px-4 py-2.5 rounded-lg fade-up">
            {{ session('status') }}
        </div>
    </div>
@endif

@if ($errors->any() && ! View::hasSection('hides_errors'))
    <div class="max-w-6xl mx-auto px-6 mt-5">
        <div class="border border-coral/30 bg-coral/10 text-coral text-sm px-4 py-2.5 rounded-lg">
            {{ $errors->first() }}
        </div>
    </div>
@endif

<main class="max-w-6xl mx-auto px-6 py-9">
    @yield('content')
</main>

<footer class="bg-ink text-paper mt-12 w-screen left-1/2 -translate-x-1/2 relative">
    <div class="w-full px-10 py-8">
        <div class="grid md:grid-cols-[1.5fr_1fr_1fr_1fr] gap-10">
            <div>
                <div class="flex items-center gap-2.5 mb-4">
                    <img src="{{ asset('images/logo.png') }}" alt="SkillShield" class="h-8 w-auto">
                    <span class="font-display font-semibold text-xl">SkillShield</span>
                </div>
                <p class="text-paper/60 text-sm leading-relaxed max-w-xs">Cybersecurity awareness, Learn to spot scams, practise on real attacks, and track your progress.</p>
            </div>
            <div>
                <p class="font-display font-semibold mb-4">Product</p>
                <ul class="space-y-2.5 text-sm text-paper/60">
                    <li><a href="{{ route('lessons.index') }}" class="hover:text-tealsoft transition">Lessons</a></li>
                    <li><a href="{{ route('quizzes.index') }}" class="hover:text-tealsoft transition">Quizzes</a></li>
                    <li><a href="{{ route('simulations.index') }}" class="hover:text-tealsoft transition">Simulations</a></li>
                    <li><a href="{{ route('scanner.index') }}" class="hover:text-tealsoft transition">Scanner</a></li>
                </ul>
            </div>
            <div>
                <p class="font-display font-semibold mb-4">Learn</p>
                <ul class="space-y-2.5 text-sm text-paper/60">
                    <li><a href="{{ route('lessons.index') }}" class="hover:text-tealsoft transition">Phishing</a></li>
                    <li><a href="{{ route('lessons.index') }}" class="hover:text-tealsoft transition">Passwords</a></li>
                    <li><a href="{{ route('lessons.index') }}" class="hover:text-tealsoft transition">Safe browsing</a></li>
                    <li><a href="{{ route('lessons.index') }}" class="hover:text-tealsoft transition">Social engineering</a></li>
                </ul>
            </div>
            <div>
                <p class="font-display font-semibold mb-4">Account</p>
                <ul class="space-y-2.5 text-sm text-paper/60">
                    <li><a href="{{ route('profile.show') }}" class="hover:text-tealsoft transition">Profile</a></li>
                    <li><a href="{{ route('reports.index') }}" class="hover:text-tealsoft transition">Reports</a></li>
                    <li><a href="{{ route('leaderboard.index') }}" class="hover:text-tealsoft transition">Ranks</a></li>
                </ul>
            </div>
        </div>
        <div class="border-t border-paper/10 mt-8 pt-5 flex flex-col md:flex-row items-center justify-between gap-3 text-sm text-paper/50">
            <span>© {{ date('Y') }} SkillShield. A cybersecurity awareness platform.</span>
            <span>Stay vigilant.</span>
        </div>
    </div>
</footer>

</body>
</html>
