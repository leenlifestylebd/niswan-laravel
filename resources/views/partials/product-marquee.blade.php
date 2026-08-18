{{-- উপরের সেকশন: হেডিং + সাবটাইটেল + একটানা (right-to-left) ছবি স্লাইডার + CTA --}}
@php $gallery = $product->images ?: array_filter([$product->mainImage()]); @endphp
<section class="bg-brand-light pb-6 pt-8">
    <div class="mx-auto mb-6 max-w-6xl px-4 text-center">
        <span class="text-xs font-semibold uppercase tracking-wider text-brand">প্রিমিয়াম কালেকশন</span>
        <h1 class="mt-2 text-3xl font-bold text-ink md:text-4xl">{{ $product->name }}</h1>
        @if ($product->short)
            <p class="mx-auto mt-2 max-w-xl text-sm text-gray-600">{{ $product->short }}</p>
        @endif
    </div>

    @if (count($gallery))
        {{-- একটানা স্লাইডার — কন্টেইনারের মধ্যে, দুপাশে gutter --}}
        <div class="mx-auto max-w-6xl overflow-hidden px-4">
            <div class="marquee-track flex w-max">
                @for ($g = 0; $g < 2; $g++)
                    <div class="flex shrink-0 gap-3 pr-3 sm:gap-4 sm:pr-4">
                        @foreach ($gallery as $src)
                            <div class="aspect-[3/4] w-[78vw] shrink-0 overflow-hidden rounded-2xl border-2 border-white bg-white shadow-sm sm:w-[42vw] lg:w-[360px]">
                                <x-product-image :src="$src" name="" />
                            </div>
                        @endforeach
                    </div>
                @endfor
            </div>
        </div>
    @endif

    <div class="mt-7 px-4 text-center">
        <a href="#order" class="inline-block rounded-full bg-brand px-8 py-3.5 text-base font-bold text-white shadow-md transition hover:bg-brand-dark">
            অর্ডার করতে এখানে ক্লিক করুন
        </a>
    </div>
</section>
