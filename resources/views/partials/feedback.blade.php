@php $images = $reviewImages ?? []; @endphp
@if (count($images))
<section id="reviews" class="bg-white"
    x-data="{
        i: 0,
        paused: false,
        goTo(idx) {
            const el = this.$refs.track, cards = el ? el.children : null;
            if (!el || !cards || !cards.length) return;
            const n = cards.length;
            this.i = ((idx % n) + n) % n;
            el.scrollTo({ left: cards[this.i].offsetLeft - cards[0].offsetLeft, behavior: 'smooth' });
        },
        init() { setInterval(() => { if (!this.paused) this.goTo(this.i + 1); }, 3500); }
    }">
    <div class="mx-auto max-w-6xl px-4 py-14">
        <div class="mb-8 flex items-end justify-between gap-4">
            <div>
                <span class="text-sm font-medium text-brand">বিশ্বস্ততা</span>
                <h2 class="mt-1 text-3xl font-bold text-ink">গ্রাহকের প্রকৃত মতামত</h2>
                <p class="mt-2 text-sm text-gray-500">আমাদের পেজ, মেসেজ ও কমেন্টে গ্রাহকদের আসল রিভিউ</p>
            </div>

            {{-- arrow বাটন (ডেস্কটপ) --}}
            <div class="hidden shrink-0 gap-2 sm:flex">
                <button type="button" @click="goTo(i - 1)" aria-label="আগের"
                    class="flex h-10 w-10 items-center justify-center rounded-full border border-gray-200 text-ink transition hover:bg-brand hover:text-white">‹</button>
                <button type="button" @click="goTo(i + 1)" aria-label="পরের"
                    class="flex h-10 w-10 items-center justify-center rounded-full border border-gray-200 text-ink transition hover:bg-brand hover:text-white">›</button>
            </div>
        </div>

        {{-- single-row slider --}}
        <div x-ref="track"
             @mouseenter="paused = true" @mouseleave="paused = false"
             @touchstart="paused = true" @touchend="setTimeout(() => paused = false, 4000)"
             class="no-scrollbar flex snap-x snap-mandatory gap-4 overflow-x-auto scroll-smooth pb-4">
            @foreach ($images as $i => $src)
                <div class="h-[380px] w-[260px] shrink-0 snap-start overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm sm:w-[300px]">
                    <img src="{{ $src }}" alt="গ্রাহক রিভিউ {{ $i + 1 }}" loading="lazy"
                         class="h-full w-full object-contain object-top">
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif
