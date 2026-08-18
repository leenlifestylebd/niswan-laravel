@php
    // ব্যানার: অ্যাডমিন সেটিংস → না থাকলে প্রোডাক্টের মেইন ছবি
    $bannerDesktop = $site['bannerUrl'] ?: $product?->mainImage();
    $bannerMobile  = $site['bannerMobileUrl'] ?: $bannerDesktop;
@endphp

<section id="top" class="relative min-h-[92vh] w-full overflow-hidden">
    {{-- ফুল-ব্লিড ব্যানার --}}
    @if ($bannerDesktop)
        <picture class="absolute inset-0 block">
            <source media="(min-width: 768px)" srcset="{{ $bannerDesktop }}">
            <img src="{{ $bannerMobile }}" alt="{{ $site['brandName'] }}" fetchpriority="high"
                 class="h-full w-full object-cover object-center">
        </picture>
    @else
        <div class="absolute inset-0 bg-[color:var(--color-surface)]"></div>
    @endif

    {{-- গাঢ় গ্রেডিয়েন্ট — টেক্সট পড়ার জন্য --}}
    <div class="absolute inset-0 bg-gradient-to-t from-[color:var(--color-bg)] via-[color:var(--color-bg)]/55 to-[color:var(--color-bg)]/70"></div>
    <div class="absolute inset-0 bg-gradient-to-r from-[color:var(--color-bg)]/80 via-transparent to-transparent"></div>

    {{-- সোনালি ফ্রেম --}}
    <div class="pointer-events-none absolute inset-4 border border-[color:var(--color-accent)]/25 sm:inset-7"></div>

    {{-- কনটেন্ট --}}
    <div class="relative mx-auto flex min-h-[92vh] max-w-6xl flex-col justify-center px-8 pb-24 pt-28 sm:px-14">
        <div class="max-w-2xl rise">
            <p class="eyebrow">{{ $site['brandName'] }} · সীমিত সংস্করণ</p>

            <h1 class="mt-5 text-4xl leading-[1.15] sm:text-6xl lg:text-7xl">
                {{ $site['bannerHeadline'] }}
            </h1>

            <div class="rule-gold mt-7 max-w-[220px]"></div>

            <p class="mt-6 max-w-lg text-base leading-relaxed text-[color:var(--color-muted)] sm:text-lg">
                {{ $site['bannerSubline'] }}
            </p>

            @if ($product)
                <div class="mt-9 flex flex-wrap items-baseline gap-4">
                    <span class="font-display text-4xl text-[color:var(--color-accent)]">{{ bdt($product->price) }}</span>
                    @if ($product->old_price)
                        <span class="text-lg text-[color:var(--color-muted)] line-through">{{ bdt($product->old_price) }}</span>
                        @if ($product->old_price > $product->price)
                            <span class="border border-[color:var(--color-accent)]/50 px-2 py-0.5 text-xs font-semibold tracking-wider text-[color:var(--color-accent)]">
                                {{ bn_num(round((1 - $product->price / $product->old_price) * 100)) }}% ছাড়
                            </span>
                        @endif
                    @endif
                </div>
            @endif

            <div class="mt-9 flex flex-wrap gap-4">
                <a href="#order" class="btn-gold">এখনই অর্ডার করুন</a>
                <a href="#detail" class="btn-outline">বিস্তারিত দেখুন</a>
            </div>
        </div>
    </div>

    {{-- স্ক্রল ইঙ্গিত --}}
    <a href="#detail" aria-label="নিচে স্ক্রল করুন"
       class="absolute bottom-7 left-1/2 hidden -translate-x-1/2 flex-col items-center gap-2 text-[color:var(--color-muted)] sm:flex">
        <span class="text-[10px] uppercase tracking-[0.3em]">স্ক্রল</span>
        <span class="block h-10 w-px bg-gradient-to-b from-[color:var(--color-accent)] to-transparent"></span>
    </a>
</section>
