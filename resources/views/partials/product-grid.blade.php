<section id="products" class="relative overflow-hidden bg-gray-50">
    {{-- aesthetic background শেপ --}}
    <div class="pointer-events-none absolute right-[-80px] top-10 h-72 w-72 rounded-full bg-brand/10 blur-3xl"></div>
    <div class="pointer-events-none absolute bottom-10 left-[-60px] h-64 w-64 rounded-full bg-brand/10 blur-3xl"></div>

    <div class="relative mx-auto max-w-6xl px-4 py-14">
        <div class="mb-10 text-center">
            <span class="text-sm font-medium text-brand">আমাদের কালেকশন</span>
            <h2 class="mt-1 text-3xl font-bold text-brand-dark">আপনার পছন্দের সেটটি বেছে নিন</h2>
        </div>

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($products as $p)
                <a href="{{ route('product', $p->slug) }}"
                   class="group flex flex-col overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm transition hover:shadow-md">
                    <div class="relative aspect-[4/5] overflow-hidden">
                        <x-product-image :src="$p->mainImage()" :name="$p->name" />
                        @if ($p->badge)
                            <span class="absolute left-3 top-3 rounded-full bg-brand px-3 py-1 text-xs font-semibold text-white">{{ $p->badge }}</span>
                        @endif
                    </div>

                    <div class="flex flex-1 flex-col p-4">
                        <h3 class="font-semibold text-ink">{{ $p->name }}</h3>
                        <p class="mt-1 text-xs text-gray-500">{{ $p->short }}</p>

                        <div class="mt-3 flex items-center gap-2">
                            <span class="text-lg font-bold text-brand">{{ bdt($p->price) }}</span>
                            @if ($p->old_price)
                                <span class="text-sm text-gray-400 line-through">{{ bdt($p->old_price) }}</span>
                            @endif
                        </div>

                        <span class="mt-4 w-full rounded-full bg-brand py-2.5 text-center text-sm font-semibold text-white transition group-hover:bg-brand-dark">
                            অর্ডার করুন
                        </span>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
