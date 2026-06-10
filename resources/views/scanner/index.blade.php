@extends('layouts.app')
@section('title', 'Scanner')

@php
    // Verdict styling + copy per risk level
    $rc  = $result ? (['high' => 'coral', 'medium' => 'amber', 'low' => 'teal'][$result['risk_level']] ?? 'slate') : null;
    $rcBg = ['coral' => 'rgba(209,73,91,.08)', 'amber' => 'rgba(201,130,10,.10)', 'teal' => '#d6ece9', 'slate' => '#f1f0ec'];
    $verdictSub = $result ? ([
        'low'    => "No strong phishing signals in this {$type}.",
        'medium' => "Some warning signs — treat this {$type} with caution.",
        'high'   => "Multiple red flags — do not click links or reply to this {$type}.",
    ][$result['risk_level']] ?? '') : '';

    // URL breakdown (when a URL was scanned)
    $urlParts = null;
    if ($result && $type === 'url' && ! empty($input)) {
        $u = parse_url(str_contains($input, '://') ? $input : 'http://' . $input);
        $flagText  = strtolower(implode(' ', $result['flags']));
        $urlParts = [
            'scheme'     => ($u['scheme'] ?? 'http') . '://',
            'schemeBad'  => str_contains($flagText, 'https'),
            'host'       => $u['host'] ?? '',
            'hostBad'    => str_contains($flagText, 'domain') || str_contains($flagText, 'pattern') || str_contains($flagText, 'ip address') || str_contains($flagText, 'extension'),
            'path'       => ($u['path'] ?? '') . (isset($u['query']) ? '?' . $u['query'] : ''),
        ];
    }
@endphp

@section('content')

<div class="grid lg:grid-cols-2 gap-6 items-start">

    {{-- ===================== EMAIL SCAN ===================== --}}
    <section class="bg-card border border-line rounded-2xl p-7 card-shadow">
        <div class="flex items-center gap-3 mb-1">
            <div class="w-10 h-10 rounded-xl bg-tealsoft grid place-items-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="#0f766e" stroke-width="1.7" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/></svg>
            </div>
            <h2 class="font-display text-xl font-semibold">Email scan</h2>
        </div>
        <p class="text-slate text-sm mb-4">Paste a suspicious email and we'll check it for phishing signals.</p>

        <form method="POST" action="{{ route('scanner.email') }}" id="emailForm">
            @csrf
            <textarea name="content" id="emailInput" rows="7" required placeholder="Paste email body here…"
                      class="w-full bg-paper border border-line rounded-xl px-3.5 py-3 text-sm focus:border-teal focus:ring-1 focus:ring-teal focus:outline-none transition resize-y">{{ $type === 'email' ? ($input ?? '') : '' }}</textarea>
            <p class="text-xs text-slate my-2.5">No email handy?
                <button type="button" onclick="loadEmailExample()" class="text-teal font-semibold underline underline-offset-2">Try an example</button>
            </p>
            <button class="w-full bg-teal text-white font-bold py-3 rounded-xl hover:bg-teal/90 transition">Analyze email</button>
        </form>

        @if ($result && $type === 'email')
            @include('scanner._result')
        @endif
    </section>

    {{-- ===================== URL SCAN ===================== --}}
    <section class="bg-card border border-line rounded-2xl p-7 card-shadow">
        <div class="flex items-center gap-3 mb-1">
            <div class="w-10 h-10 rounded-xl bg-tealsoft grid place-items-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="#0f766e" stroke-width="1.7" viewBox="0 0 24 24"><path d="M10 13a5 5 0 007.5.5l2-2a5 5 0 00-7-7l-1.2 1.2"/><path d="M14 11a5 5 0 00-7.5-.5l-2 2a5 5 0 007 7l1.2-1.2"/></svg>
            </div>
            <h2 class="font-display text-xl font-semibold">URL scan</h2>
        </div>
        <p class="text-slate text-sm mb-4">Enter a link and we'll inspect it before you ever click it.</p>

        <form method="POST" action="{{ route('scanner.url') }}" id="urlForm">
            @csrf
            <input type="text" name="url" id="urlInput" required placeholder="https://example.com/login"
                   value="{{ $type === 'url' ? ($input ?? '') : '' }}"
                   class="w-full bg-paper border border-line rounded-xl px-3.5 h-12 text-sm font-mono focus:border-teal focus:ring-1 focus:ring-teal focus:outline-none transition">
            <p class="text-xs text-slate my-2.5">No URL handy?
                <button type="button" onclick="loadUrlExample()" class="text-teal font-semibold underline underline-offset-2">Try an example</button>
            </p>
            <button class="w-full bg-teal text-white font-bold py-3 rounded-xl hover:bg-teal/90 transition">Analyze URL</button>
        </form>

        @if ($result && $type === 'url')
            @include('scanner._result')
        @endif
    </section>

</div>

<script>
function loadEmailExample() {
    document.getElementById('emailInput').value =
`Dear Valued Customer,

We detected unusual activity on your account. To avoid suspension, please verify your account immediately.

Verify here: http://paypa1.com/secure-login

Thank you,
PayPal Security Team`;
    document.getElementById('emailForm').submit();
}
function loadUrlExample() {
    document.getElementById('urlInput').value = 'http://paypa1.com/account-verify/login';
    document.getElementById('urlForm').submit();
}
// After a scan, bring the result into view
document.addEventListener('DOMContentLoaded', () => {
    const r = document.getElementById('scanResult');
    if (r) r.scrollIntoView({ behavior: 'smooth', block: 'center' });
});
</script>
@endsection
