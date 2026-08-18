<!DOCTYPE html>
<html lang="bn" class="h-full antialiased" style="
    --color-brand: {{ $site['colorPrimary'] }};
    --color-brand-dark: {{ $site['colorSecondary'] }};
    --color-ink: {{ $site['colorGlobal'] }};
    --color-brand-light: {{ lighten($site['colorPrimary']) }};
">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>অ্যাডমিন লগইন — {{ $site['brandName'] }}</title>
    <link rel="icon" href="{{ $site['faviconUrl'] ?: '/icon-192.png' }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-full items-center justify-center bg-brand-light px-4">
    <div class="w-full max-w-sm rounded-3xl bg-white p-8 shadow-lg">
        <div class="mb-6 text-center">
            <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-brand text-2xl text-white">🔒</span>
            <h1 class="mt-3 text-xl font-bold text-ink">অ্যাডমিন লগইন</h1>
            <p class="mt-1 text-sm text-gray-500">{{ $site['brandName'] }} ম্যানেজমেন্ট প্যানেল</p>
        </div>

        <form method="POST" action="{{ route('admin.login.post') }}" class="space-y-4">
            @csrf
            <input type="password" name="password" required autofocus placeholder="পাসওয়ার্ড"
                   class="w-full rounded-xl border px-3 py-2.5 text-sm outline-none {{ $errors->any() ? 'border-red-400 focus:border-red-500' : 'border-gray-200 focus:border-brand' }}">

            @error('password')
                <p class="text-xs font-medium text-red-500">⚠️ {{ $message }}</p>
            @enderror

            <button type="submit" class="w-full rounded-full bg-brand py-3 text-sm font-bold text-white transition hover:bg-brand-dark">
                লগইন
            </button>
        </form>

        <p class="mt-6 text-center text-xs text-gray-400">
            <a href="{{ route('home') }}" class="hover:text-brand">← সাইটে ফিরে যান</a>
        </p>
    </div>
</body>
</html>
