<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin')  ·  SkillShield Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600;9..144,700&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: {
            fontFamily: { display:['Fraunces','serif'], sans:['Outfit','sans-serif'] },
            colors: { paper:'#f7f5f0', card:'#ffffff', ink:'#1d2520', slate:'#5a6760', teal:'#0f766e', tealsoft:'#d6ece9', coral:'#d1495b', amber:'#c9820a', line:'#e6e2d8' },
        }}}
    </script>
    <style>
        body { background:#f7f5f0; }
        .card-shadow { box-shadow: 0 1px 2px rgba(29,37,32,.04), 0 8px 24px -12px rgba(29,37,32,.12); }
        ::selection { background:#0f766e; color:#fff; }
        /* Slim scrollbar for the sidebar */
        #adminSidebar::-webkit-scrollbar { width: 5px; }
        #adminSidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,.15); border-radius: 99px; }
    </style>
</head>
<body class="font-sans text-ink min-h-screen antialiased">
<div class="flex min-h-screen">

    {{-- Mobile backdrop --}}
    <div id="sidebarBackdrop" onclick="toggleSidebar(false)"
         class="fixed inset-0 z-40 bg-ink/50 hidden md:!hidden"></div>

    {{-- Sidebar: sticky on desktop, slide-in drawer on mobile --}}
    <aside id="adminSidebar"
           class="fixed md:sticky inset-y-0 left-0 top-0 z-50 w-60 h-screen overflow-y-auto bg-ink text-paper/80 flex flex-col
                  -translate-x-full md:translate-x-0 transition-transform duration-200 shrink-0">
        <div class="px-6 h-16 flex items-center gap-2.5 border-b border-white/10 shrink-0">
            <span class="w-7 h-7 rounded-lg bg-teal flex items-center justify-center text-white font-display font-bold text-sm">S</span>
            <span class="font-display font-semibold text-white">Admin</span>
            {{-- Close (mobile only) --}}
            <button onclick="toggleSidebar(false)" class="ml-auto md:hidden p-1.5 rounded-lg hover:bg-white/10 transition" aria-label="Close menu">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M6 6l12 12M18 6L6 18"/></svg>
            </button>
        </div>
        <nav class="flex-1 p-3 space-y-1 text-sm">
            @php
                $items = [
                    'admin.dashboard'   => 'Overview',
                    'admin.users'       => 'Users',
                    'admin.lessons'     => 'Lessons',
                    'admin.quizzes'     => 'Quizzes',
                    'admin.simulations' => 'Simulations',
                    'admin.tips'        => 'Tips',
                    'admin.scan-logs'   => 'Scan logs',
                    'admin.reports'     => 'Reports',
                ];
            @endphp
            @foreach ($items as $route => $label)
                {{-- Match the route AND its children, so e.g. a lesson's
                     Sections page still highlights "Lessons" --}}
                @php $active = request()->routeIs($route) || request()->routeIs($route.'.*'); @endphp
                <a href="{{ route($route) }}"
                   class="block px-3 py-2 rounded-lg transition {{ $active ? 'bg-teal text-white' : 'hover:bg-white/5 hover:text-white' }}">
                    {{ $label }}
                </a>
            @endforeach
        </nav>
        <div class="p-3 border-t border-white/10 space-y-1 text-sm shrink-0">
            <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-lg hover:bg-white/5 hover:text-white transition">← Back to app</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="w-full text-left px-3 py-2 rounded-lg hover:bg-white/5 hover:text-coral transition">Sign out</button>
            </form>
        </div>
    </aside>

    {{-- Main --}}
    <div class="flex-1 min-w-0">
        <header class="bg-card border-b border-line h-16 flex items-center gap-3 px-4 md:px-6 sticky top-0 z-30">
            {{-- Hamburger (mobile only) --}}
            <button onclick="toggleSidebar(true)" class="md:hidden p-2 -ml-1 rounded-lg hover:bg-paper transition" aria-label="Open menu">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16"/></svg>
            </button>
            <h1 class="font-display text-lg font-semibold truncate">@yield('heading', 'Admin')</h1>
        </header>

        @if (session('status'))
            <div class="px-6 mt-4">
                <div class="border border-teal/30 bg-tealsoft text-teal text-sm px-4 py-2.5 rounded-lg">{{ session('status') }}</div>
            </div>
        @endif
        @if ($errors->any())
            <div class="px-6 mt-4">
                <div class="border border-coral/30 bg-coral/10 text-coral text-sm px-4 py-2.5 rounded-lg">{{ $errors->first() }}</div>
            </div>
        @endif

        <main class="p-4 md:p-6">
            @yield('content')
        </main>
    </div>
</div>

<script>
function toggleSidebar(open) {
    const sb = document.getElementById('adminSidebar');
    const bd = document.getElementById('sidebarBackdrop');
    sb.classList.toggle('-translate-x-full', !open);
    bd.classList.toggle('hidden', !open);
    document.body.style.overflow = open ? 'hidden' : '';
}
// Close the drawer with Escape
document.addEventListener('keydown', e => { if (e.key === 'Escape') toggleSidebar(false); });
// Reset state when resizing up to desktop
window.addEventListener('resize', () => { if (window.innerWidth >= 768) toggleSidebar(false); });
</script>
</body>
</html>
