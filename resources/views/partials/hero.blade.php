@php $slides = $products->take(6)->values(); @endphp
<section id="top" class="relative overflow-hidden bg-gradient-to-b from-brand-light to-white"
    x-data="{
        i: 0,
        paused: false,
        n: {{ $slides->count() }},
        init() { if (this.n > 1) setInterval(() => { if (!this.paused) this.i = (this.i + 1) % this.n; }, 4000); }
    }">
    {{-- aesthetic background শেপ --}}
    <div class="pointer-events-none absolute -left-24 -top-24 h-72 w-72 rounded-full bg-brand/20 blur-3xl"></div>
    <div class="pointer-events-none absolute right-[-60px] top-32 h-80 w-80 rounded-full bg-brand/10 blur-3xl"></div>

    <div class="relative mx-auto grid max-w-6xl items-center gap-8 px-4 py-8 md:grid-cols-2 md:py-16">
        {{-- স্লাইডার (মোবাইলে উপরে) --}}
        <div class="order-1 md:order-2"
             @mouseenter="paused = true" @mouseleave="paused = false" @touchstart="paused = true">
            <div class="relative mx-auto aspect-[4/5] w-full max-w-[300px] overflow-hidden rounded-3xl bg-brand-light shadow-xl sm:max-w-sm sm:aspect-[3/4]">
                @if ($slides->isEmpty())
                    <div class="flex h-full w-full items-center justify-center text-7xl">🧕</div>
                @endif

                @foreach ($slides as $idx => $p)
                    <div class="absolute inset-0 transition-all duration-700 ease-out"
                         :class="i === {{ $idx }} ? 'scale-100 opacity-100' : 'scale-105 opacity-0'">
                        <x-product-image :src="$p->mainImage()" :name="$p->name" :loading="$idx === 0 ? 'eager' : 'lazy'" />
                    </div>
                @endforeach

                {{-- প্রোডাক্ট নাম + দাম overlay --}}
                @foreach ($slides as $idx => $p)
                    <a href="{{ route('product', $p->slug) }}" x-show="i === {{ $idx }}" x-cloak
                       class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent p-4 pt-10">
                        <p class="text-sm font-semibold text-white drop-shadow">{{ $p->name }}</p>
                        <p class="text-xs text-white/90">
                            {{ bdt($p->price) }}
                            @if ($p->old_price)
                                <span class="ml-2 line-through opacity-70">{{ bdt($p->old_price) }}</span>
                            @endif
                        </p>
                    </a>
                @endforeach

                {{-- dots --}}
                @if ($slides->count() > 1)
                    <div class="absolute left-0 right-0 top-3 flex justify-center gap-1.5">
                        @foreach ($slides as $idx => $p)
                            <button type="button" @click="i = {{ $idx }}" aria-label="স্লাইড {{ $idx + 1 }}"
                                class="h-1.5 rounded-full transition-all"
                                :class="i === {{ $idx }} ? 'w-5 bg-white' : 'w-1.5 bg-white/60'"></button>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- টেক্সট + বাটন --}}
        <div class="order-2 text-center md:order-1 md:text-left">
            <span class="inline-block rounded-full bg-brand/10 px-4 py-1 text-sm font-medium text-brand">
                প্রিমিয়াম মডেস্ট কালেকশন
            </span>
            <h1 class="mt-4 text-3xl font-bold leading-tight text-ink sm:text-4xl md:text-5xl">{{ $site['tagline'] }}</h1>
            <p class="mx-auto mt-4 max-w-md text-gray-600 md:mx-0">{{ $site['description'] }}</p>

            <div class="mt-7 flex flex-wrap justify-center gap-3 md:justify-start">
                <a href="{{ $slides->isNotEmpty() ? route('product', $slides->first()->slug) : '#products' }}"
                   class="rounded-full bg-brand px-7 py-3 font-semibold text-white shadow-md transition hover:bg-brand-dark">
                    এখনই অর্ডার করুন
                </a>
                <a href="#products" class="rounded-full border border-brand px-7 py-3 font-semibold text-brand transition hover:bg-brand-light">
                    প্রোডাক্ট দেখুন
                </a>
            </div>

            <div class="mt-7 flex items-center justify-center gap-2 text-sm text-gray-500 md:justify-start">
                <span class="text-accent">★★★★★</span>
                <span>৫০০+ সন্তুষ্ট গ্রাহক</span>
            </div>
        </div>
    </div>
</section>
