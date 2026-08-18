@php
    // ব্যানার: অ্যাডমিন সেটিংস → না থাকলে প্রোডাক্টের মেইন ছবি
    $bannerDesktop = $site['bannerUrl'] ?: $product?->mainImage();
    $bannerMobile  = $site['bannerMobileUrl'] ?: $bannerDesktop;
@endphp

<section id="top" class="relative overflow-hidden pt-16 pb-10 sm:pt-24 sm:pb-20">
    {{-- নরম আলো — গাঢ় ব্যাকগ্রাউন্ডে গভীরতা দেয় --}}
    <div class="pointer-events-none absolute -left-32 top-0 h-96 w-96 rounded-full bg-[color:var(--color-accent)]/5 blur-3xl"></div>
    <div class="pointer-events-none absolute -right-32 bottom-0 h-96 w-96 rounded-full bg-[color:var(--color-brand)]/10 blur-3xl"></div>

    <div class="relative mx-auto grid max-w-6xl items-center gap-6 px-6 sm:gap-10 sm:px-10 lg:grid-cols-2 lg:gap-16">

        {{-- ব্যানার — ঠিক ৪:৫ অনুপাত। মোবাইলে উচ্চতা বাঁধা, নাহলে
             শিরোনাম-দাম-বাটন সব স্ক্রিনের নিচে চলে যায়। --}}
        <div class="order-1 lg:order-2">
            {{-- মোবাইলে উচ্চতা বেঁধে দেওয়া, চওড়া সেখান থেকেই হিসাব হয় (w-auto) —
                 max-height দিলে চওড়া অপরিবর্তিত থেকে ছবি ক্রপ হয়ে ৪:৫ নষ্ট হয়। --}}
            <div class="relative mx-auto aspect-[4/5] h-[44vh] w-auto max-w-full overflow-hidden border border-[color:var(--color-accent)]/25 sm:h-auto sm:w-full sm:max-w-[420px] lg:max-w-none">
                @if ($bannerDesktop)
                    <picture class="block h-full w-full">
                        <source media="(min-width: 1024px)" srcset="{{ $bannerDesktop }}">
                        <img src="{{ $bannerMobile }}" alt="{{ $site['brandName'] }}" fetchpriority="high"
                             class="h-full w-full object-cover object-center">
                    </picture>
                @else
                    <div class="flex h-full w-full items-center justify-center bg-[color:var(--color-surface)] text-7xl">🧕</div>
                @endif

                {{-- নিচ থেকে হালকা গ্রেডিয়েন্ট + ব্যাজ --}}
                <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-[color:var(--color-bg)]/60 via-transparent to-transparent"></div>

                @if ($product?->badge)
                    <span class="absolute left-0 top-6 bg-[color:var(--color-accent)] px-4 py-1.5 text-xs font-bold uppercase tracking-[0.18em] text-[#17120a]">
                        {{ $product->badge }}
                    </span>
                @endif
            </div>
        </div>

        {{-- লেখা --}}
        <div class="order-2 text-center lg:order-1 lg:text-left rise">
            <p class="eyebrow">{{ $site['brandName'] }} · সীমিত সংস্করণ</p>

            <h1 class="mt-3 text-3xl leading-[1.15] sm:mt-5 sm:text-5xl lg:text-6xl">
                {{ $site['bannerHeadline'] }}
            </h1>

            <div class="rule-gold mx-auto mt-4 max-w-[180px] sm:mt-7 sm:max-w-[220px] lg:mx-0"></div>

            <p class="mx-auto mt-3 max-w-lg text-sm leading-relaxed text-[color:var(--color-muted)] sm:mt-6 sm:text-base lg:mx-0">
                {{ $site['bannerSubline'] }}
            </p>

            @if ($product)
                <div class="mt-4 flex flex-wrap items-baseline justify-center gap-3 sm:mt-8 sm:gap-4 lg:justify-start">
                    <span class="font-display text-3xl text-[color:var(--color-accent)] sm:text-4xl">{{ bdt($product->price) }}</span>
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

            <div class="mt-5 flex flex-wrap justify-center gap-3 sm:mt-8 sm:gap-4 lg:justify-start">
                <a href="#order" class="btn-gold px-6 py-3 text-sm sm:px-8 sm:py-4 sm:text-base">এখনই অর্ডার করুন</a>
                <a href="#detail" class="btn-outline px-6 py-3 text-sm sm:px-8 sm:py-4 sm:text-base">বিস্তারিত দেখুন</a>
            </div>
        </div>
    </div>
</section>
