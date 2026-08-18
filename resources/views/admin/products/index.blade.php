@extends('layouts.admin')

@section('title', 'প্রোডাক্ট')

@section('content')
<div class="mb-4 flex items-center justify-between">
    <h1 class="text-xl font-bold text-ink">প্রোডাক্ট ({{ bn_num($products->count()) }})</h1>
    <a href="{{ route('admin.products.create') }}"
       class="rounded-full bg-brand px-5 py-2 text-sm font-semibold text-white transition hover:bg-brand-dark">
        + নতুন প্রোডাক্ট
    </a>
</div>

<div class="overflow-x-auto rounded-2xl border border-gray-200 bg-white">
    <table class="w-full min-w-[760px] text-sm">
        <thead class="border-b border-gray-200 bg-gray-50 text-left text-xs uppercase tracking-wide text-gray-500">
            <tr>
                <th class="p-3">ছবি</th>
                <th class="p-3">নাম / slug</th>
                <th class="p-3">দাম</th>
                <th class="p-3">ভ্যারিয়েন্ট</th>
                <th class="p-3">ক্রম</th>
                <th class="p-3">স্ট্যাটাস</th>
                <th class="p-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse ($products as $p)
                <tr>
                    <td class="p-3">
                        <div class="h-14 w-12 overflow-hidden rounded-lg bg-gray-100">
                            <x-product-image :src="$p->mainImage()" :name="$p->name" />
                        </div>
                    </td>
                    <td class="p-3">
                        <p class="font-medium text-ink">{{ $p->name }}</p>
                        <p class="text-xs text-gray-500">/{{ $p->slug }}</p>
                    </td>
                    <td class="p-3">
                        <span class="font-bold text-ink">{{ bdt($p->price) }}</span>
                        @if ($p->old_price)
                            <span class="ml-1 text-xs text-gray-400 line-through">{{ bdt($p->old_price) }}</span>
                        @endif
                    </td>
                    <td class="p-3 text-xs text-gray-600">
                        @foreach ($p->variantList() as $v)
                            <div>{{ $v['name'] }} — {{ bdt($v['price']) }}</div>
                        @endforeach
                    </td>
                    <td class="p-3 text-gray-600">{{ bn_num($p->sort_order) }}</td>
                    <td class="p-3">
                        <span class="rounded-full px-2 py-0.5 text-xs font-semibold {{ $p->active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                            {{ $p->active ? 'সক্রিয়' : 'নিষ্ক্রিয়' }}
                        </span>
                    </td>
                    <td class="p-3">
                        <div class="flex gap-2">
                            <a href="{{ route('admin.products.edit', $p->id) }}"
                               class="rounded-full border border-gray-200 px-3 py-1 text-xs font-semibold text-gray-700 transition hover:bg-gray-100">✏️ এডিট</a>
                            <form method="POST" action="{{ route('admin.products.destroy', $p->id) }}"
                                  onsubmit="return confirm('এই প্রোডাক্ট মুছে ফেলবেন?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="rounded-full border border-red-200 px-3 py-1 text-xs font-semibold text-red-600 transition hover:bg-red-50">🗑️</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="p-8 text-center text-gray-500">এখনো কোনো প্রোডাক্ট নেই</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
