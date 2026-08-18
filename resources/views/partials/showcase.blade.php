@php $gallery = array_values($product->images ?: array_filter([$product->mainImage()])); @endphp

{{-- প্রোডাক্ট শোকেস — বাঁয়ে বড় ছবি + থাম্বনেইল, ডানে বিবরণ --}}
<section id="detail" class="mx-auto max-w-6xl px-6 py-24 sm:px-10 lg:py-32">
    <div class="grid gap-14 lg:grid-cols-[1.05fr_1fr] lg:gap-20">

        {{-- ছবি: বড় ভিউ + থাম্বনেইল কলাম --}}
        <div x-data="{ i: 0, n: {{ count($gallery) }} }" class="flex flex-col-reverse gap-4 sm:flex-row">
            @if (count($gallery) > 1)
                <div class="flex gap-3 overflow-x-auto no-scrollbar sm:w-20 sm:flex-col sm:overflow-visible">
                    @foreach ($gallery as $idx => $img)
                        <button type="button" @click="i = {{ $idx }}"
                                class="aspect-[3/4] w-16 shrink-0 overflow-hidden border transition sm:w-full"
                                :class="i === {{ $idx }} ? 'border-[color:var(--color-accent)]' : 'border-[color:var(--color-line)] opacity-60 hover:opacity-100'">
                            <img src="{{ $img }}" alt="" loading="lazy" class="h-full w-full object-cover">
                        </button>
                    @endforeach
                </div>
            @endif

            <div class="relative aspect-[3/4] flex-1 overflow-hidden border border-[color:var(--color-line)]">
                @forelse ($gallery as $idx => $img)
                    <img src="{{ $img }}" alt="{{ $product->name }}"
                         loading="{{ $idx === 0 ? 'eager' : 'lazy' }}"
                         class="absolute inset-0 h-full w-full object-cover transition-opacity duration-500"
                         :class="i === {{ $idx }} ? 'opacity-100' : 'opacity-0'">
                @empty
                    <div class="flex h-full w-full items-center justify-center bg-[color:var(--color-surface)] text-6xl">🧕</div>
                @endforelse

                @if ($product->badge)
                    <span class="absolute left-0 top-6 bg-[color:var(--color-accent)] px-4 py-1.5 text-xs font-bold uppercase tracking-[0.18em] text-[#17120a]">
                        {{ $product->badge }}
                    </span>
                @endif
            </div>
        </div>

        {{-- বিবরণ --}}
        <div class="flex flex-col justify-center">
            <p class="eyebrow">প্রোডাক্ট</p>
            <h2 class="mt-4 text-4xl leading-tight sm:text-5xl">{{ $product->name }}</h2>

            <div class="rule-gold mt-6 max-w-[160px]"></div>

            <p class="mt-6 text-base leading-relaxed text-[color:var(--color-muted)]">{{ $product->short }}</p>

            <div class="mt-8 flex flex-wrap items-baseline gap-4">
                <span class="font-display text-4xl text-[color:var(--color-accent)]">{{ bdt($product->price) }}</span>
                @if ($product->old_price)
                    <span class="text-lg text-[color:var(--color-muted)] line-through">{{ bdt($product->old_price) }}</span>
                @endif
            </div>

            @if ($product->features)
                <ul class="mt-9 space-y-4 border-t border-[color:var(--color-line)] pt-8">
                    @foreach ($product->features as $f)
                        <li class="flex items-start gap-4 text-sm">
                            <span class="mt-1.5 h-px w-6 shrink-0 bg-[color:var(--color-accent)]"></span>
                            <span class="text-[color:var(--color-fg)]">{{ $f }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif

            @if ($product->sizes)
                <div class="mt-8">
                    <p class="text-xs uppercase tracking-[0.22em] text-[color:var(--color-muted)]">উপলব্ধ সাইজ</p>
                    <div class="mt-3 flex flex-wrap gap-2">
                        @foreach ($product->sizes as $s)
                            <span class="border border-[color:var(--color-line)] px-3.5 py-1.5 text-sm text-[color:var(--color-fg)]">{{ $s }}</span>
                        @endforeach
                    </div>
                </div>
            @endif

            <a href="#order" class="btn-gold mt-10 self-start">অর্ডার ফর্মে যান</a>
        </div>
    </div>
</section>
