<!DOCTYPE html>
{{-- ক্লায়েন্টের বাছাই করা রঙ → CSS variable (পুরো সাইটে প্রযোজ্য) --}}
<html lang="bn" class="h-full antialiased" style="
    --color-brand: {{ $site['colorPrimary'] }};
    --color-accent: {{ $site['colorPrimary'] }};
    --color-brand-dark: {{ $site['colorSecondary'] }};
    --color-ink: {{ $site['colorGlobal'] }};
    --color-brand-light: {{ lighten($site['colorPrimary']) }};
">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="{{ $site['colorPrimary'] }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', $site['brandName'].' — '.$site['tagline'])</title>
    <meta name="description" content="@yield('description', $site['description'])">
    <meta name="keywords" content="{{ implode(', ', $site['keywords']) }}">
    <meta name="application-name" content="{{ $site['brandName'] }}">
    <link rel="canonical" href="@yield('canonical', url()->current())">

    <meta property="og:type" content="website">
    <meta property="og:locale" content="bn_BD">
    <meta property="og:site_name" content="{{ $site['brandName'] }}">
    <meta property="og:title" content="@yield('title', $site['brandName'].' — '.$site['tagline'])">
    <meta property="og:description" content="@yield('description', $site['description'])">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('title', $site['brandName'].' — '.$site['tagline'])">
    <meta name="twitter:description" content="@yield('description', $site['description'])">

    <link rel="manifest" href="{{ route('manifest') }}">
    <link rel="icon" href="{{ $site['faviconUrl'] ?: '/icon-192.png' }}">
    <link rel="apple-touch-icon" href="{{ $site['faviconUrl'] ?: '/icon-192.png' }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="{{ $site['brandName'] }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="min-h-full flex flex-col bg-white text-gray-800">

@if ($site['facebookPixelId'])
    {{-- Meta Pixel (browser) — server CAPI-র সাথে একই event_id দিয়ে dedup হয় --}}
    <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window,document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', @json($site['facebookPixelId'])); fbq('track','PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none" alt=""
        src="https://www.facebook.com/tr?id={{ $site['facebookPixelId'] }}&ev=PageView&noscript=1"></noscript>
@endif

@yield('body')

{{-- হালকা ভিজিট beacon (fire-and-forget) + সার্ভিস ওয়ার্কার --}}
<script>
    (function () {
        try {
            var path = window.location.pathname;
            if (path.indexOf('/admin') === 0) return;
            var body = JSON.stringify({ path: path, ref: document.referrer });
            var url = @json(route('track'));
            if (navigator.sendBeacon) {
                navigator.sendBeacon(url, new Blob([body], { type: 'application/json' }));
            } else {
                fetch(url, { method: 'POST', body: body, keepalive: true, headers: { 'Content-Type': 'application/json' } });
            }
        } catch (e) { /* analytics ব্যর্থ হলেও সাইট চলবে */ }
    })();

    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/sw.js').catch(function () {});
    }
</script>
@stack('scripts')
</body>
</html>
