<header class="sticky top-0 z-40 border-b border-gray-100 bg-white text-ink">
    <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-3">
        <a href="{{ route('home') }}" class="flex items-center gap-2">
            @if ($site['logoUrl'])
                <img src="{{ $site['logoUrl'] }}" alt="{{ $site['brandName'] }}"
                     style="height: {{ $site['logoHeight'] }}px"
                     class="w-auto max-w-[220px] object-contain">
            @else
                <span class="flex h-9 w-9 items-center justify-center rounded-full bg-brand text-lg text-white">🧕</span>
                <span class="text-lg font-bold text-ink">{{ $site['brandName'] }}</span>
            @endif
        </a>

        <nav class="hidden items-center gap-6 text-sm font-medium text-gray-600 md:flex">
            <a href="{{ route('home') }}#products" class="hover:text-brand">প্রোডাক্ট</a>
            <a href="{{ route('home') }}#reviews" class="hover:text-brand">রিভিউ</a>
        </nav>

        <a href="{{ route('home') }}#products"
           class="rounded-full bg-brand px-5 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-dark">
            অর্ডার করুন
        </a>
    </div>
</header>
