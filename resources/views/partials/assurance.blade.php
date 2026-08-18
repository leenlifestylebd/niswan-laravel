{{-- ট্রাস্ট সেকশন — কার্ড নয়, নাম্বারড কলাম (আগের আইকন-কার্ড গ্রিডের বদলে) --}}
<section class="border-y border-[color:var(--color-line)] bg-[color:var(--color-surface)]">
    <div class="mx-auto max-w-6xl px-6 py-20 sm:px-10">
        <div class="mb-14 text-center">
            <p class="eyebrow">আমাদের প্রতিশ্রুতি</p>
            <h2 class="mt-4 text-3xl sm:text-4xl">কেন {{ $site['brandName'] }}</h2>
        </div>

        <div class="grid gap-x-10 gap-y-12 sm:grid-cols-2 lg:grid-cols-4">
            @foreach (config('store.features') as $i => $f)
                <div class="border-t border-[color:var(--color-line)] pt-6">
                    <span class="font-display text-3xl text-[color:var(--color-accent)]">
                        {{ bn_num(str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT)) }}
                    </span>
                    <h3 class="mt-4 text-xl">{{ $f['title'] }}</h3>
                    <p class="mt-2 text-sm leading-relaxed text-[color:var(--color-muted)]">{{ $f['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
