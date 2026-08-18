<!DOCTYPE html>
<html lang="bn" class="h-full antialiased" style="
    --color-bg: {{ $site['colorGlobal'] }};
    --color-accent: {{ $site['colorAccent'] }};
    --color-surface: {{ tint($site['colorGlobal'], 0.055) }};
    --color-raised: {{ tint($site['colorGlobal'], 0.095) }};
    --color-line: {{ tint($site['colorGlobal'], 0.16) }};
">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>অ্যাডমিন লগইন — {{ $site['brandName'] }}</title>
    <link rel="icon" href="{{ $site['faviconUrl'] ?: '/icon-192.png' }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tiro+Bangla:ital@0;1&family=Anek+Bangla:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-full items-center justify-center px-4">
    <div class="panel w-full max-w-sm p-9">
        <div class="mb-8 text-center">
            <p class="font-display text-2xl tracking-[0.26em]">{{ mb_strtoupper($site['brandName']) }}</p>
            <div class="rule-gold mx-auto mt-4 max-w-[120px]"></div>
            <p class="mt-4 text-xs uppercase tracking-[0.24em] text-[color:var(--color-muted)]">অ্যাডমিন প্যানেল</p>
        </div>

        <form method="POST" action="{{ route('admin.login.post') }}" class="space-y-4">
            @csrf
            <input type="password" name="password" required autofocus placeholder="পাসওয়ার্ড"
                   class="field {{ $errors->any() ? 'border-red-500' : '' }}">

            @error('password')
                <p class="text-xs text-red-400">⚠️ {{ $message }}</p>
            @enderror

            <button type="submit" class="btn-gold w-full">লগইন</button>
        </form>

        <p class="mt-8 text-center text-xs">
            <a href="{{ route('home') }}" class="text-[color:var(--color-muted)] transition hover:text-[color:var(--color-accent)]">← সাইটে ফিরে যান</a>
        </p>
    </div>
</body>
</html>
