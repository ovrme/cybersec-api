<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Welcome')  ·  SkillShield</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600;9..144,700&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: {
            fontFamily: { display:['Fraunces','serif'], sans:['Outfit','sans-serif'] },
            colors: { paper:'#f7f5f0', card:'#ffffff', ink:'#1d2520', slate:'#5a6760', teal:'#0f766e', tealsoft:'#d6ece9', coral:'#d1495b', line:'#e6e2d8' },
        }}}
    </script>
    <style>
        body { background:#f7f5f0; }
        ::selection { background:#0f766e; color:#fff; }
    </style>
</head>
<body class="font-sans text-ink min-h-screen flex items-center justify-center p-6">
    <div class="w-full max-w-md">
        <div class="flex items-center gap-2.5 justify-center mb-8">
            <img src="{{ asset('images/logo.png') }}" alt="SkillShield" class="h-8 w-auto">
            <span class="font-display font-semibold text-2xl tracking-tight">SkillShield</span>
        </div>
        <div class="bg-card border border-line rounded-2xl p-8" style="box-shadow:0 10px 40px -16px rgba(29,37,32,.18)">
            @yield('content')
        </div>
        <p class="text-center text-xs text-slate mt-6">Cybersecurity awareness</p>
    </div>
</body>
</html>
