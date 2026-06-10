<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SkillShield — Cybersecurity awareness</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600;9..144,700;9..144,900&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: {
                fontFamily: { display: ['Fraunces','serif'], sans: ['Outfit','sans-serif'] },
                colors: { paper:'#f7f5f0', card:'#fff', ink:'#1d2520', slate:'#5a6760',
                          teal:'#0f766e', tealsoft:'#d6ece9', coral:'#d1495b', amber:'#c9820a', line:'#e6e2d8' },
            } }
        }
    </script>
    <style>
        body { background:#f7f5f0; }
        .card-shadow { box-shadow:0 1px 2px rgba(29,37,32,.04),0 8px 24px -12px rgba(29,37,32,.12); }
        ::selection { background:#0f766e; color:#fff; }
        .hero-grid { background-image:radial-gradient(#0f766e1f 1px,transparent 1px); background-size:22px 22px; }
        .fade-up { animation:fadeup .7s cubic-bezier(.2,.7,.2,1) both; }
        @keyframes fadeup { from{opacity:0;transform:translateY(16px)} to{opacity:1;transform:none} }
        .d1{animation-delay:.05s}.d2{animation-delay:.15s}.d3{animation-delay:.25s}.d4{animation-delay:.35s}.d5{animation-delay:.45s}
        .blob { filter:blur(64px); opacity:.5; }
        .img-fallback { background:linear-gradient(135deg,#0f766e22,#c9820a18); }
        .ph { object-fit:cover; width:100%; height:100%; display:block; }
    </style>
</head>
<body class="font-sans text-ink antialiased overflow-x-hidden">

<nav class="sticky top-0 z-50 bg-paper/80 backdrop-blur border-b border-line">
    <div class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between">
        <a href="/" class="flex items-center gap-2.5">
            <img src="{{ asset('images/logo.png') }}" alt="SkillShield" class="h-8 w-auto">
            <span class="font-display font-semibold text-xl tracking-tight">SkillShield</span>
        </a>
        <div class="hidden md:flex items-center gap-8 text-sm text-slate">
            <a href="#features" class="hover:text-ink transition">Features</a>
            <a href="#how" class="hover:text-ink transition">How it works</a>
            <a href="#stats" class="hover:text-ink transition">Why it matters</a>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('login') }}" class="text-sm text-slate hover:text-ink transition">Sign in</a>
            <a href="{{ route('register') }}" class="text-sm bg-teal text-white font-medium px-4 py-2 rounded-lg hover:bg-teal/90 transition">Get started</a>
        </div>
    </div>
</nav>

<header class="relative hero-grid overflow-hidden">
    <div class="absolute -top-20 -left-20 w-96 h-96 bg-tealsoft rounded-full blob"></div>
    <div class="absolute top-40 -right-24 w-96 h-96 bg-amber/20 rounded-full blob"></div>
    <div class="relative max-w-6xl mx-auto px-6 pt-20 pb-16 grid lg:grid-cols-2 gap-12 items-center">
        <div>
            <span class="fade-up d1 inline-flex items-center gap-2 text-xs font-medium text-teal bg-tealsoft px-3 py-1.5 rounded-full mb-6">
                <span class="w-1.5 h-1.5 rounded-full bg-teal"></span> Cybersecurity awareness
            </span>
            <h1 class="fade-up d2 font-display text-5xl md:text-6xl font-semibold leading-[1.05] tracking-tight">
                Spot the scam <span class="text-teal italic">before</span> it spots you.
            </h1>
            <p class="fade-up d3 text-lg text-slate mt-6 max-w-md leading-relaxed">
                SkillShield turns security from a lecture into a habit — interactive lessons, real phishing drills, and a live risk score that actually means something.
            </p>
            <div class="fade-up d4 flex items-center gap-3 mt-8">
                <a href="{{ route('register') }}" class="bg-teal text-white font-medium px-7 py-3.5 rounded-xl hover:bg-teal/90 transition shadow-lg shadow-teal/20">Start learning free </a>
                <a href="#how" class="border border-line bg-card px-7 py-3.5 rounded-xl font-medium hover:border-teal hover:text-teal transition">How it works</a>
            </div>
            <div class="fade-up d5 flex items-center gap-6 mt-8 text-sm text-slate">
                <span class="flex items-center gap-1.5"><svg class="w-4 h-4 text-teal" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Free to start</span>
                <span class="flex items-center gap-1.5"><svg class="w-4 h-4 text-teal" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> No card required</span>
            </div>
        </div>

        <div class="fade-up d4 relative">
            <div class="img-fallback rounded-3xl overflow-hidden card-shadow aspect-[4/3] border border-line">
                <img class="ph" loading="lazy" alt="Person working securely on a laptop"
                     src="https://images.unsplash.com/photo-1517433456452-f9633a875f6f?w=900&q=80"
                     onerror="this.style.display='none'">
            </div>
            <div class="absolute -bottom-5 -left-5 bg-card border border-line rounded-2xl p-4 card-shadow w-44">
                <p class="text-xs text-slate mb-1">Your risk score</p>
                <div class="flex items-end gap-2">
                    <span class="font-display text-4xl font-semibold text-teal">82</span>
                    <span class="text-xs text-slate mb-1">/ 100</span>
                </div>
                <div class="h-1.5 bg-line rounded-full mt-2 overflow-hidden"><div class="h-full bg-teal rounded-full" style="width:82%"></div></div>
            </div>
        </div>
    </div>
</header>

<div class="border-y border-line bg-card/50">
    <div class="max-w-6xl mx-auto px-6 py-5 flex flex-wrap items-center justify-center gap-x-10 gap-y-2 text-slate text-sm">
        <span class="font-display font-medium">Built for</span>
        <span>Students</span><span>·</span><span>Small teams</span><span>·</span><span>Anyone with an inbox</span>
    </div>
</div>

<section id="features" class="max-w-6xl mx-auto px-6 py-24">
    <div class="text-center mb-14">
        <p class="text-sm text-teal font-medium mb-2">Everything in one place</p>
        <h2 class="font-display text-4xl font-semibold">Tools that build real instincts</h2>
    </div>
    <div class="grid md:grid-cols-3 gap-6">
        @php
            $features = [
                ['#0f766e','M12 2 4 6v6c0 5 3.5 8 8 10 4.5-2 8-5 8-10V6z','Phishing detection','Paste a suspicious email and get an instant breakdown of the red flags — sender tricks, urgency tactics, and dodgy links.'],
                ['#c9820a','M10 13a5 5 0 0 0 7 0l3-3a5 5 0 0 0-7-7l-1 1M14 11a5 5 0 0 0-7 0l-3 3a5 5 0 0 0 7 7l1-1','URL analyzer','Check any link before you click it. SkillShield flags lookalike domains, risky patterns, and known tricks.'],
                ['#d1495b','M9 12l2 2 4-4M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z','Interactive quizzes','Test what you know with scenario-based questions, then see exactly why each answer was right or wrong.'],
                ['#0f766e','M13 2 3 14h9l-1 8 10-12h-9z','Attack simulations','Walk through realistic phishing and social-engineering scenarios in a safe space — make the mistakes here, not at work.'],
                ['#c9820a','M3 3v18h18M7 14l4-4 4 4 5-5','Live risk score','A single number that reflects your real habits and knowledge, updated as you learn and practice.'],
                ['#d1495b','M12 8v4l3 3m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0z','Personalized tips','Targeted recommendations based on where you slip up, so you improve where it counts.'],
            ];
        @endphp
        @foreach ($features as [$c,$path,$title,$desc])
            <div class="bg-card border border-line rounded-2xl p-7 card-shadow hover:-translate-y-1 transition">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-5" style="background:{{ $c }}1a">
                    <svg class="w-6 h-6" fill="none" stroke="{{ $c }}" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="{{ $path }}"/></svg>
                </div>
                <h3 class="font-display text-xl font-semibold mb-2">{{ $title }}</h3>
                <p class="text-slate text-sm leading-relaxed">{{ $desc }}</p>
            </div>
        @endforeach
    </div>
</section>

<section class="max-w-6xl mx-auto px-6 pb-24">
    <div class="grid lg:grid-cols-2 gap-10 items-center bg-card border border-line rounded-3xl overflow-hidden card-shadow">
        <div class="p-10 lg:p-12">
            <p class="text-sm text-teal font-medium mb-2">Learn by doing</p>
            <h2 class="font-display text-3xl font-semibold leading-tight mb-4">Practice on real attacks, safely</h2>
            <p class="text-slate leading-relaxed mb-6">SkillShield's simulations drop you into convincing phishing emails, fake login pages, and social-engineering phone scripts. You decide: trust it or flag it — then learn exactly what gave it away.</p>
            <ul class="space-y-3 text-sm">
                @foreach (['Realistic, modern attack scenarios','Instant feedback on every decision','Tracks what you fall for, so you improve'] as $point)
                    <li class="flex items-start gap-2.5">
                        <svg class="w-5 h-5 text-teal shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
                        <span>{{ $point }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
        <div class="img-fallback h-full min-h-[320px]">
            <img class="ph" loading="lazy" alt="Reviewing security alerts on screen"
                 src="https://images.unsplash.com/photo-1563986768609-322da13575f3?w=900&q=80"
                 onerror="this.style.display='none'">
        </div>
    </div>
</section>

<section id="stats" class="bg-ink text-paper">
    <div class="max-w-6xl mx-auto px-6 py-20">
        <div class="text-center mb-12">
            <h2 class="font-display text-4xl font-semibold">Why this matters</h2>
            <p class="text-paper/60 mt-2">Human error is behind the vast majority of breaches. Training changes that.</p>
        </div>
        <div class="grid md:grid-cols-3 gap-8 text-center">
            @php $facts = [['82%','of breaches involve a human element'],['1 in 3','people click a phishing link when untrained'],['70%','drop in click rates after regular training']]; @endphp
            @foreach ($facts as [$n,$l])
                <div>
                    <p class="font-display text-6xl font-semibold text-tealsoft">{{ $n }}</p>
                    <p class="text-paper/70 mt-3 max-w-xs mx-auto">{{ $l }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section id="how" class="max-w-6xl mx-auto px-6 py-24">
    <div class="text-center mb-14">
        <p class="text-sm text-teal font-medium mb-2">Three steps</p>
        <h2 class="font-display text-4xl font-semibold">Get sharper in minutes</h2>
    </div>
    <div class="grid md:grid-cols-3 gap-6">
        @php $steps = [['01','Learn','Work through bite-sized lessons on phishing, passwords, social engineering and safe browsing.'],['02','Practice','Take quizzes and run live attack simulations to put your knowledge under pressure.'],['03','Track','Watch your risk score climb as your instincts sharpen, with tips tailored to your weak spots.']]; @endphp
        @foreach ($steps as [$n,$t,$d])
            <div class="relative bg-card border border-line rounded-2xl p-7 card-shadow">
                <span class="font-display text-5xl font-semibold text-tealsoft">{{ $n }}</span>
                <h3 class="font-display text-xl font-semibold mt-3 mb-2">{{ $t }}</h3>
                <p class="text-slate text-sm leading-relaxed">{{ $d }}</p>
            </div>
        @endforeach
    </div>
</section>

<section class="max-w-6xl mx-auto px-6 pb-24">
    <div class="relative bg-teal rounded-3xl overflow-hidden text-center px-6 py-16 text-white">
        <div class="absolute -top-16 -right-10 w-72 h-72 bg-white/10 rounded-full blob"></div>
        <div class="relative">
            <h2 class="font-display text-4xl md:text-5xl font-semibold max-w-2xl mx-auto leading-tight">Ready to stop guessing what's safe?</h2>
            <p class="text-tealsoft mt-4 max-w-md mx-auto">Create a free account and take your first lesson in under two minutes.</p>
            <a href="{{ route('register') }}" class="inline-block mt-8 bg-white text-teal font-semibold px-8 py-3.5 rounded-xl hover:bg-paper transition">Create my account </a>
        </div>
    </div>
</section>

<footer class="bg-ink text-paper">
    <div class="max-w-6xl mx-auto px-6 py-14">
        <div class="grid md:grid-cols-[1.5fr_1fr_1fr_1fr] gap-10">
            <div>
                <div class="flex items-center gap-2.5 mb-4">
                    <img src="{{ asset('images/logo.png') }}" alt="SkillShield" class="h-8 w-auto">
                    <span class="font-display font-semibold text-xl">SkillShield</span>
                </div>
                <p class="text-paper/60 text-sm leading-relaxed max-w-xs">Cybersecurity awareness, Learn to spot scams, practice on real attacks, and track your progress.</p>
            </div>
            @php
                $cols = [
                    'Product' => ['Features','Lessons','Quizzes','Simulations'],
                    'Learn'   => ['Phishing','Passwords','Safe browsing','Social engineering'],
                    'Account' => ['Sign in','Get started','Dashboard'],
                ];
            @endphp
            @foreach ($cols as $heading => $links)
                <div>
                    <p class="font-display font-semibold mb-4">{{ $heading }}</p>
                    <ul class="space-y-2.5 text-sm text-paper/60">
                        @foreach ($links as $link)
                            <li><a href="{{ $link === 'Sign in' ? route('login') : ($link === 'Get started' ? route('register') : '#') }}" class="hover:text-tealsoft transition">{{ $link }}</a></li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>
        <div class="border-t border-paper/10 mt-12 pt-6 flex flex-col md:flex-row items-center justify-between gap-3 text-sm text-paper/50">
            <span>© {{ date('Y') }} SkillShield. A cybersecurity awareness platform.</span>
            <span>Stay vigilant.</span>
        </div>
    </div>
</footer>

</body>
</html>
