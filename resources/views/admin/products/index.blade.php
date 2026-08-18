@extends('layouts.admin')

@section('title', 'প্রোডাক্ট')

@section('content')
@php $landingSlug = $site['landingProductSlug']; @endphp

<div class="mb-5 flex flex-wrap items-center justify-between gap-3">
    <p class="text-sm text-[color:var(--color-muted)]">
        মোট {{ bn_num($products->count()) }}টি — সাইটে শুধু ল্যান্ডিং প্রোডাক্টটি দেখায়
        (<a href="{{ route('admin.settings') }}" class="text-[color:var(--color-accent)] hover:underline">সেটিংসে বদলান</a>)
    </p>
    <a href="{{ route('admin.products.create') }}" class="btn-gold px-5 py-2 text-sm">+ নতুন প্রোডাক্ট</a>
</div>

<div class="panel overflow-x-auto">
    <table class="w-full min-w-[780px] text-sm">
        <thead class="border-b border-[color:var(--color-line)] text-left">
            <tr class="[&>th]:p-3 [&>th]:text-[11px] [&>th]:font-semibold [&>th]:uppercase [&>th]:tracking-[0.16em] [&>th]:text-[color:var(--color-muted)]">
                <th>ছবি</th>
                <th>নাম / slug</th>
                <th>দাম</th>
                <th>ভ্যারিয়েন্ট</th>
                <th>ক্রম</th>
                <th>স্ট্যাটাস</th>
                <th></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-[color:var(--color-line)]">
            @forelse ($products as $i => $p)
                @php $isLanding = $landingSlug ? $landingSlug === $p->slug : $loop->first && $p->active; @endphp
                <tr>
                    <td class="p-3">
                        <div class="h-16 w-14 overflow-hidden border border-[color:var(--color-line)] bg-[color:var(--color-raised)]">
                            @if ($p->mainImage())
                                <img src="{{ $p->mainImage() }}" alt="{{ $p->name }}" class="h-full w-full object-cover">
                            @else
                                <span class="flex h-full w-full items-center justify-center text-2xl">🧕</span>
                            @endif
                        </div>
                    </td>
                    <td class="p-3">
                        <p class="font-medium">
                            {{ $p->name }}
                            @if ($isLanding)
                                <span class="chip ml-1.5 text-[color:var(--color-accent)]">ল্যান্ডিং</span>
                            @endif
                        </p>
                        <p class="text-xs text-[color:var(--color-muted)]">/{{ $p->slug }}</p>
                    </td>
                    <td class="p-3">
                        <span class="font-display text-lg">{{ bdt($p->price) }}</span>
                        @if ($p->old_price)
                            <span class="ml-1 text-xs text-[color:var(--color-muted)] line-through">{{ bdt($p->old_price) }}</span>
                        @endif
                    </td>
                    <td class="p-3 text-xs text-[color:var(--color-muted)]">
                        @foreach ($p->variantList() as $v)
                            <div>{{ $v['name'] }} — {{ bdt($v['price']) }}</div>
                        @endforeach
                    </td>
                    <td class="p-3 text-[color:var(--color-muted)]">{{ bn_num($p->sort_order) }}</td>
                    <td class="p-3">
                        <span class="chip {{ $p->active ? 'text-emerald-400' : 'text-[color:var(--color-muted)]' }}">
                            {{ $p->active ? 'সক্রিয়' : 'নিষ্ক্রিয়' }}
                        </span>
                    </td>
                    <td class="p-3">
                        <div class="flex gap-2">
                            <a href="{{ route('admin.products.edit', $p->id) }}" class="btn-sm">এডিট</a>
                            <form method="POST" action="{{ route('admin.products.destroy', $p->id) }}"
                                  onsubmit="return confirm('এই প্রোডাক্ট মুছে ফেলবেন?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-sm btn-sm-danger">🗑️</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="p-10 text-center text-[color:var(--color-muted)]">এখনো কোনো প্রোডাক্ট নেই</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
