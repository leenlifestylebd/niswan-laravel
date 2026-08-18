<!DOCTYPE html>
<html lang="bn" class="h-full antialiased" style="
    --color-bg: {{ $site['colorGlobal'] }};
    --color-accent: {{ $site['colorAccent'] }};
    --color-brand: {{ $site['colorPrimary'] }};
    --color-surface: {{ tint($site['colorGlobal'], 0.055) }};
    --color-raised: {{ tint($site['colorGlobal'], 0.095) }};
    --color-line: {{ tint($site['colorGlobal'], 0.16) }};
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
    <link href="https://fonts.googleapis.com/css2?family=Tiro+Bangla:ital@0;1&family=Anek+Bangla:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="min-h-full" x-data="{ menu: false }">

    {{-- মোবাইলে সাইডবার খোলা থাকলে পেছনের অংশ ঢেকে দেয় --}}
    <div x-show="menu" x-cloak @click="menu = false"
         class="fixed inset-0 z-40 bg-black/70 lg:hidden"></div>

    @include('admin.partials.sidebar')

    <div class="lg:pl-64">
        {{-- উপরের সরু বার — মোবাইল মেনু + পেজ শিরোনাম --}}
        <header class="sticky top-0 z-30 flex items-center gap-4 border-b border-[color:var(--color-line)] bg-[color:var(--color-bg)]/95 px-5 py-4 backdrop-blur">
            <button type="button" @click="menu = true" aria-label="মেনু"
                    class="text-[color:var(--color-fg)] lg:hidden">
                <svg viewBox="0 0 24 24" class="h-6 w-6 stroke-current" fill="none" stroke-width="1.6">
                    <path d="M4 7h16M4 12h16M4 17h16" stroke-linecap="round"/>
                </svg>
            </button>

            <h1 class="font-display text-xl">@yield('title', 'অ্যাডমিন')</h1>

            <a href="{{ route('home') }}" target="_blank" rel="noopener"
               class="ml-auto text-xs uppercase tracking-[0.18em] text-[color:var(--color-muted)] transition hover:text-[color:var(--color-accent)]">
                সাইট দেখুন ↗
            </a>
        </header>

        <main class="px-5 py-7 sm:px-7">
            @if (session('status'))
                <div class="mb-5 border border-[color:var(--color-accent)]/50 bg-[color:var(--color-surface)] px-4 py-3 text-sm text-[color:var(--color-accent)]">
                    ✓ {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-5 border border-red-500/50 bg-[color:var(--color-surface)] px-4 py-3 text-sm text-red-300">
                    <ul class="space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>⚠️ {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

<script>
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/sw.js').catch(function () {});
    }
</script>
@stack('scripts')
</body>
</html>
