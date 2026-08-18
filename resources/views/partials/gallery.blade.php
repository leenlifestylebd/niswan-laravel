@php $shots = array_values($product->images ?: array_filter([$product->mainImage()])); @endphp

@if (count($shots) > 1)
{{-- অসম উচ্চতার এডিটোরিয়াল গ্রিড (আগের সমান-কার্ড গ্রিডের বদলে) --}}
<section class="mx-auto max-w-6xl px-6 py-24 sm:px-10">
    <div class="mb-12 flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="eyebrow">গ্যালারি</p>
            <h2 class="mt-4 text-3xl sm:text-4xl">প্রতিটি খুঁটিনাটি</h2>
        </div>
        <p class="max-w-sm text-sm text-[color:var(--color-muted)]">
            কাপড়ের বুনন, সেলাই আর ফিনিশিং — কাছ থেকে দেখুন।
        </p>
    </div>

    <div class="grid grid-cols-2 gap-4 sm:gap-6 lg:grid-cols-3">
        @foreach ($shots as $i => $src)
            <figure class="overflow-hidden border border-[color:var(--color-line)]
                           {{ $i % 3 === 0 ? 'aspect-[3/4]' : ($i % 3 === 1 ? 'aspect-square self-end' : 'aspect-[4/5]') }}">
                <img src="{{ $src }}" alt="{{ $product->name }} — ছবি {{ bn_num($i + 1) }}" loading="lazy"
                     class="h-full w-full object-cover transition duration-700 hover:scale-105">
            </figure>
        @endforeach
    </div>
</section>
@endif
