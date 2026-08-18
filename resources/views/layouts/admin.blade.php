<!DOCTYPE html>
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
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'অ্যাডমিন') — {{ $site['brandName'] }}</title>

    <link rel="manifest" href="{{ route('admin.manifest') }}">
    <link rel="icon" href="{{ $site['faviconUrl'] ?: '/icon-192.png' }}">
    <meta name="theme-color" content="{{ $site['colorGlobal'] }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="min-h-full bg-gray-50 text-gray-800">

@include('admin.partials.nav')

<main class="mx-auto max-w-7xl px-4 py-6">
    @if (session('status'))
        <div class="mb-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-800">
            ✓ {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <ul class="space-y-1">
                @foreach ($errors->all() as $error)
                    <li>⚠️ {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @yield('content')
</main>

<script>
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/sw.js').catch(function () {});
    }
</script>
@stack('scripts')
</body>
</html>
