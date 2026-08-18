@php $gallery = array_values($product->images ?: array_filter([$product->mainImage()])); @endphp
<section class="bg-white">
    <div class="mx-auto grid max-w-6xl gap-8 px-4 py-10 md:grid-cols-2 md:py-14">
        {{-- অটো-স্লাইডার (থামে না) --}}
        <div class="relative aspect-[4/5] overflow-hidden rounded-3xl border border-gray-100 shadow-sm"
             x-data="{
                 i: 0,
                 n: {{ count($gallery) }},
                 init() { if (this.n > 1) setInterval(() => { this.i = (this.i + 1) % this.n; }, 3000); }
             }">
            @foreach ($gallery as $idx => $img)
                <div class="absolute inset-0 transition-opacity duration-700"
                     :class="i === {{ $idx }} ? 'opacity-100' : 'opacity-0'">
                    <x-product-image :src="$img" :name="$product->name" :loading="$idx === 0 ? 'eager' : 'lazy'" />
                </div>
            @endforeach

            @if (count($gallery) > 1)
                {{-- prev / next টাচেবল বাটন --}}
                <button type="button" @click="i = (i - 1 + n) % n" aria-label="আগের ছবি"
                        class="absolute left-2 top-1/2 flex h-9 w-9 -translate-y-1/2 items-center justify-center rounded-full bg-white/80 text-lg text-ink shadow-md backdrop-blur transition hover:bg-white active:scale-90">‹</button>
                <button type="button" @click="i = (i + 1) % n" aria-label="পরের ছবি"
                        class="absolute right-2 top-1/2 flex h-9 w-9 -translate-y-1/2 items-center justify-center rounded-full bg-white/80 text-lg text-ink shadow-md backdrop-blur transition hover:bg-white active:scale-90">›</button>

                {{-- dots --}}
                <div class="absolute bottom-3 left-0 right-0 flex justify-center gap-1.5">
                    @foreach ($gallery as $idx => $img)
                        <button type="button" @click="i = {{ $idx }}" aria-label="ছবি {{ $idx + 1 }}"
                                class="h-1.5 rounded-full transition-all"
                                :class="i === {{ $idx }} ? 'w-5 bg-white' : 'w-1.5 bg-white/60'"></button>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- ডিটেইলস --}}
        <div>
            @if ($product->badge)
                <span class="inline-block rounded-full bg-brand-light px-3 py-1 text-xs font-semibold text-brand">{{ $product->badge }}</span>
            @endif
            <h2 class="mt-3 text-3xl font-bold text-ink">{{ $product->name }}</h2>
            <p class="mt-2 text-gray-600">{{ $product->short }}</p>

            <div class="mt-4 flex items-end gap-3">
                <span class="text-3xl font-bold text-brand">{{ bdt($product->price) }}</span>
                @if ($product->old_price)
                    <span class="pb-1 text-lg text-gray-400 line-through">{{ bdt($product->old_price) }}</span>
                @endif
                @if ($product->old_price > $product->price)
                    <span class="mb-1 rounded-md bg-brand-light px-2 py-0.5 text-sm font-semibold text-brand-dark">
                        {{ bn_num(round((1 - $product->price / $product->old_price) * 100)) }}% ছাড়
                    </span>
                @endif
            </div>

            <ul class="mt-5 space-y-2">
                @foreach ($product->features ?? [] as $f)
                    <li class="flex items-start gap-2 text-sm text-gray-700">
                        <span class="mt-0.5 text-brand">✓</span>
                        <span>{{ $f }}</span>
                    </li>
                @endforeach
            </ul>

            <a href="#order" class="mt-7 inline-block rounded-full bg-brand px-8 py-3.5 text-base font-bold text-white shadow-md transition hover:bg-brand-dark">
                এখনই অর্ডার করুন
            </a>
        </div>
    </div>
</section>
