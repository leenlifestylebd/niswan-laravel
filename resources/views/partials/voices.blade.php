@php $images = $reviewImages ?? []; @endphp

@if (count($images))
{{-- গ্রাহকের রিভিউ স্ক্রিনশট — ডার্ক স্ট্রিপে অনুভূমিক স্লাইডার --}}
<section id="reviews" class="border-y border-[color:var(--color-line)] bg-[color:var(--color-surface)] py-20"
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
        init() { setInterval(() => { if (!this.paused) this.goTo(this.i + 1); }, 3800); }
    }">
    <div class="mx-auto max-w-6xl px-6 sm:px-10">
        <div class="mb-10 flex flex-wrap items-end justify-between gap-6">
            <div>
                <p class="eyebrow">গ্রাহকের কথা</p>
                <h2 class="mt-4 text-3xl sm:text-4xl">যাঁরা আগে নিয়েছেন</h2>
                <p class="mt-3 max-w-md text-sm text-[color:var(--color-muted)]">
                    পেজ, ইনবক্স ও কমেন্টে আসা আসল মেসেজ — কোনোটাই সাজানো নয়।
                </p>
            </div>

            <div class="hidden shrink-0 gap-3 sm:flex">
                <button type="button" @click="goTo(i - 1)" aria-label="আগের"
                    class="flex h-11 w-11 items-center justify-center border border-[color:var(--color-line)] text-[color:var(--color-fg)] transition hover:border-[color:var(--color-accent)] hover:text-[color:var(--color-accent)]">‹</button>
                <button type="button" @click="goTo(i + 1)" aria-label="পরের"
                    class="flex h-11 w-11 items-center justify-center border border-[color:var(--color-line)] text-[color:var(--color-fg)] transition hover:border-[color:var(--color-accent)] hover:text-[color:var(--color-accent)]">›</button>
            </div>
        </div>

        <div x-ref="track"
             @mouseenter="paused = true" @mouseleave="paused = false"
             @touchstart="paused = true" @touchend="setTimeout(() => paused = false, 4000)"
             class="no-scrollbar flex snap-x snap-mandatory gap-5 overflow-x-auto scroll-smooth pb-2">
            @foreach ($images as $i => $src)
                <div class="h-[400px] w-[264px] shrink-0 snap-start overflow-hidden border border-[color:var(--color-line)] bg-[color:var(--color-bg)]">
                    <img src="{{ $src }}" alt="গ্রাহক রিভিউ {{ bn_num($i + 1) }}" loading="lazy"
                         class="h-full w-full object-contain object-top">
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif
