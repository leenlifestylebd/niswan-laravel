@props(['src' => null, 'name' => '', 'loading' => 'lazy'])

{{-- ছবি থাকলে দেখাবে, না থাকলে সুন্দর placeholder। প্যারেন্টের নির্দিষ্ট সাইজ লাগবে। --}}
@if ($src)
    <img src="{{ $src }}" alt="{{ $name }}" loading="{{ $loading }}"
         {{ $attributes->merge(['class' => 'h-full w-full object-cover']) }}>
@else
    <div {{ $attributes->merge(['class' => 'flex h-full w-full flex-col items-center justify-center bg-gradient-to-br from-brand-light to-brand/10']) }}>
        <span class="text-5xl">🧕</span>
        <span class="mt-2 px-3 text-center text-sm font-medium text-brand-dark">{{ $name }}</span>
    </div>
@endif
