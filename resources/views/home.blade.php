@extends('layouts.app')

@push('head')
@php
    $jsonLd = [
        '@context'    => 'https://schema.org',
        '@type'       => 'Store',
        'name'        => $site['brandName'],
        'description' => $site['description'],
        'url'         => $site['url'],
        'telephone'   => $site['phone'],
        'areaServed'  => 'BD',
    ];
@endphp
<script type="application/ld+json">
    @json($jsonLd, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
</script>
@endpush

@section('body')
    @include('partials.navbar')

    <main class="flex-1">
        @include('partials.hero')
        @include('partials.features')
        @include('partials.product-grid')
        @include('partials.order-banner')
        @include('partials.feedback')
    </main>

    @include('partials.footer')
    @include('partials.whatsapp-float')
@endsection
