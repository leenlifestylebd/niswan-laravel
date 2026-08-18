@extends('layouts.app')

@section('title', $product->name.' | '.$site['brandName'])
@section('description', $product->short.' — মাত্র '.bdt($product->price).'। '.$site['brandName'].' থেকে অর্ডার করুন।')
@section('canonical', route('product', $product->slug))

@push('head')
@php
    $base  = rtrim($site['url'], '/');
    $img   = $product->mainImage();
    $jsonLd = array_filter([
        '@context'    => 'https://schema.org',
        '@type'       => 'Product',
        'name'        => $product->name,
        'description' => $product->short,
        'sku'         => $product->slug,
        'brand'       => ['@type' => 'Brand', 'name' => $site['brandName']],
        'image'       => $img ? (str_starts_with($img, 'http') ? $img : $base.$img) : null,
        'offers'      => [
            '@type'         => 'Offer',
            'url'           => route('product', $product->slug),
            'priceCurrency' => 'BDT',
            'price'         => $product->price,
            'availability'  => 'https://schema.org/InStock',
            'seller'        => ['@type' => 'Organization', 'name' => $site['brandName']],
        ],
    ]);
    $viewContent = [
        'content_name' => $product->name,
        'content_type' => 'product',
        'content_ids'  => [$product->slug],
        'value'        => $product->price,
        'currency'     => 'BDT',
    ];
@endphp
<script type="application/ld+json">
    @json($jsonLd, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
</script>
<script>
    // ViewContent — Pixel লোড থাকলে
    window.addEventListener('load', function () {
        if (typeof window.fbq === 'function') {
            fbq('track', 'ViewContent', @json($viewContent));
        }
    });
</script>
@endpush

@section('body')
    @include('partials.navbar')

    <main class="flex-1">
        @include('partials.product-marquee')
        @include('partials.product-detail')
        @include('partials.feedback')
        @include('partials.order-banner')
        @include('partials.order-form')
    </main>

    @include('partials.footer')
    @include('partials.whatsapp-float')
@endsection
