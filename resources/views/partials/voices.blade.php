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
            let target = ((idx % n) + n) % n;
            let left = cards[target].offsetLeft - cards[0].offsetLeft;
            // চওড়া স্ক্রিনে শেষ কার্ডগুলো একসাথেই দেখা যায়, তাই ট্র্যাক আর ডানে যেতে পারে না।
            // আগে ইনডেক্স বাড়লেও স্ক্রল থেমে থাকত — স্লাইডার কয়েক সেকেন্ড মরে পড়ে থাকত।
            const maxScroll = el.scrollWidth - el.clientWidth;
            if (left > maxScroll) {
                if (el.scrollLeft >= maxScroll - 2) {
                    target = 0;      // ইতিমধ্যেই শেষ প্রান্তে — শুরুতে ফিরি
                    left = 0;
                } else {
                    left = maxScroll; // শেষ পর্যন্ত টেনে নিই, যাতে শেষ কার্ডও পুরো দেখা যায়
                }
            }
            this.i = target;
            el.scrollTo({ left, behavior: 'smooth' });
        },
        init() { setInterval(() => { if (!this.paused) this.goTo(this.i + 1); }, 3800); }
    }">
    <div class="mx-auto max-w-6xl px-6 sm:px-10">
        <div class="mb-10 flex flex-wrap items-end justify-between gap-6">
            <div>
                <p class="eyebrow reveal">গ্রাহকের কথা</p>
                <h2 class="reveal reveal-1 mt-4 text-3xl sm:text-4xl">যাঁরা আগে নিয়েছেন</h2>
                <p class="reveal reveal-2 mt-3 max-w-md text-sm text-[color:var(--color-muted)]">
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
            {{-- সব কার্ডের উচ্চতা এক, চওড়া ছবির নিজস্ব অনুপাত অনুযায়ী — তাই
                 নিচে ফাঁকা জায়গাও পড়ে না, দুপাশ কেটেও যায় না।
                 ⚠️ ছবিতে উচ্চতা সরাসরি (h-full নয়) দিতে হয়, আর loading="lazy"
                 রাখা যায় না: কার্ডের নিজস্ব মাপ না থাকায় lazy ছবি কখনো লোড হয় না
                 → চওড়া ০ → কার্ড চুপসে যায়। --}}
            @foreach ($images as $i => $src)
                <div class="shrink-0 snap-start overflow-hidden border border-[color:var(--color-line)] bg-[color:var(--color-bg)]">
                    <img src="{{ $src }}" alt="গ্রাহক রিভিউ {{ bn_num($i + 1) }}" decoding="async"
                         class="h-[300px] w-auto max-w-none sm:h-[380px]">
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif
