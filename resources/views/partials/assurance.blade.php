{{-- ট্রাস্ট সেকশন — নাম্বারড কলাম। মোবাইলেও ২ কলাম, যাতে সেকশন অযথা লম্বা না হয়। --}}
<section class="border-y border-[color:var(--color-line)] bg-[color:var(--color-surface)]">
    <div class="mx-auto max-w-6xl px-6 py-12 sm:px-10 sm:py-16">
        <div class="mb-9 text-center">
            <p class="eyebrow">আমাদের প্রতিশ্রুতি</p>
            <h2 class="mt-3 text-2xl sm:text-3xl">কেন {{ $site['brandName'] }}</h2>
        </div>

        <div class="grid grid-cols-2 gap-x-6 gap-y-7 sm:gap-x-10 lg:grid-cols-4">
            @foreach (config('store.features') as $i => $f)
                <div class="border-t border-[color:var(--color-line)] pt-4">
                    <span class="font-display text-xl text-[color:var(--color-accent)] sm:text-2xl">
                        {{ bn_num(str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT)) }}
                    </span>
                    <h3 class="mt-2 text-base sm:text-lg">{{ $f['title'] }}</h3>
                    <p class="mt-1.5 text-xs leading-relaxed text-[color:var(--color-muted)] sm:text-sm">{{ $f['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
