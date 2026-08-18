@extends('layouts.app')

@section('title', $site['brandName'].' — '.$site['tagline'])
@section('description', $product?->short ?: $site['description'])

@push('head')
@php
    $base = rtrim($site['url'], '/');
    $img  = $product?->mainImage();

    $jsonLd = array_filter([
        '@context'    => 'https://schema.org',
        '@type'       => 'Product',
        'name'        => $product?->name ?? $site['brandName'],
        'description' => $product?->short ?: $site['description'],
        'sku'         => $product?->slug,
        'brand'       => ['@type' => 'Brand', 'name' => $site['brandName']],
        'image'       => $img ? (str_starts_with($img, 'http') ? $img : $base.$img) : null,
        'offers'      => $product ? [
            '@type'         => 'Offer',
            'url'           => route('home'),
            'priceCurrency' => 'BDT',
            'price'         => $product->price,
            'availability'  => 'https://schema.org/InStock',
            'seller'        => ['@type' => 'Organization', 'name' => $site['brandName']],
        ] : null,
    ]);

    $viewContent = $product ? [
        'content_name' => $product->name,
        'content_type' => 'product',
        'content_ids'  => [$product->slug],
        'value'        => $product->price,
        'currency'     => 'BDT',
    ] : null;
@endphp
<script type="application/ld+json">
    @json($jsonLd, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
</script>
@if ($viewContent)
<script>
    window.addEventListener('load', function () {
        if (typeof window.fbq === 'function') {
            fbq('track', 'ViewContent', @json($viewContent));
        }
    });
</script>
@endif
@endpush

@section('body')
    @include('partials.topbar')

    <main>
        @include('partials.banner-hero')

        @if ($product)
            @include('partials.marquee-strip')
            @include('partials.showcase')
            @include('partials.assurance')
            @include('partials.voices')
            @include('partials.order')
        @else
            <section class="mx-auto max-w-3xl px-6 py-32 text-center">
                <p class="eyebrow">সেটআপ বাকি</p>
                <h2 class="mt-4 text-3xl">কোনো সক্রিয় প্রোডাক্ট নেই</h2>
                <p class="mt-3 text-[color:var(--color-muted)]">
                    অ্যাডমিন → প্রোডাক্ট থেকে একটি প্রোডাক্ট যোগ করে সক্রিয় করুন,
                    তারপর সেটিংস থেকে ল্যান্ডিং প্রোডাক্ট বাছাই করুন।
                </p>
            </section>
        @endif
    </main>

    @include('partials.site-footer')
    @include('partials.whatsapp-float')
    @include('partials.sticky-bar')
@endsection
