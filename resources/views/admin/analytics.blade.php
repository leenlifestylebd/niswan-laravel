@extends('layouts.admin')

@section('title', 'অ্যানালিটিক্স')

@section('content')
@php
    $presets = ['today' => 'আজ', '7' => '৭ দিন', '30' => '৩০ দিন', '90' => '৯০ দিন', '365' => '১ বছর'];
    $maxPv   = max(1, collect($visitors['daily'])->max('pv') ?? 1);
    $maxRev  = max(1, collect($orders['daily'])->max('rev') ?? 1);
@endphp

<div class="mb-5 flex flex-wrap items-center gap-3">
    <h1 class="text-xl font-bold text-ink">অ্যানালিটিক্স</h1>

    {{-- রেঞ্জ প্রিসেট --}}
    <div class="flex flex-wrap gap-1">
        @foreach ($presets as $key => $label)
            <a href="{{ route('admin.analytics', ['range' => $key]) }}"
               class="rounded-full px-3 py-1.5 text-sm font-medium transition
                      {{ $range === $key ? 'bg-brand text-white' : 'border border-gray-200 text-gray-600 hover:bg-gray-100' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    {{-- কাস্টম রেঞ্জ --}}
    <form method="GET" action="{{ route('admin.analytics') }}" class="flex flex-wrap items-center gap-2 text-sm">
        <input type="date" name="from" value="{{ $from->format('Y-m-d') }}"
               class="rounded-xl border border-gray-200 px-2 py-1.5 outline-none focus:border-brand">
        <span class="text-gray-400">→</span>
        <input type="date" name="to" value="{{ $to->format('Y-m-d') }}"
               class="rounded-xl border border-gray-200 px-2 py-1.5 outline-none focus:border-brand">
        <button type="submit" class="rounded-full bg-ink px-4 py-1.5 font-semibold text-white">দেখাও</button>
    </form>
</div>

<p class="mb-4 text-xs text-gray-500">
    রেঞ্জ: {{ $from->format('d M Y') }} — {{ $to->format('d M Y') }}
</p>

{{-- ভিজিটর মেট্রিক --}}
<div class="mb-6 grid grid-cols-2 gap-3 md:grid-cols-3 lg:grid-cols-5">
    @php
        $cards = [
            ['label' => '🟢 এখন অনলাইনে', 'value' => $visitors['live']],
            ['label' => 'আজকের পেজভিউ',   'value' => $visitors['pvToday']],
            ['label' => 'আজকের ভিজিটর',   'value' => $visitors['uvToday']],
            ['label' => 'রেঞ্জে পেজভিউ',  'value' => $visitors['pvRange']],
            ['label' => 'রেঞ্জে ভিজিটর',  'value' => $visitors['uvRange']],
        ];
    @endphp
    @foreach ($cards as $c)
        <div class="rounded-2xl border border-gray-200 bg-white p-4">
            <p class="text-xs text-gray-500">{{ $c['label'] }}</p>
            <p class="mt-1 text-2xl font-bold text-ink">{{ bn_num($c['value']) }}</p>
        </div>
    @endforeach
</div>

{{-- বিক্রি মেট্রিক --}}
<div class="mb-6 grid grid-cols-2 gap-3 md:grid-cols-3 lg:grid-cols-6">
    @php
        $sales = [
            ['label' => 'আজকের অর্ডার',   'value' => bn_num($orders['ordersToday'])],
            ['label' => 'রেঞ্জে অর্ডার',   'value' => bn_num($orders['ordersRange'])],
            ['label' => 'মোট অর্ডার',      'value' => bn_num($orders['ordersTotal'])],
            ['label' => 'আজকের বিক্রি',   'value' => bdt($orders['revToday'])],
            ['label' => 'রেঞ্জে বিক্রি',   'value' => bdt($orders['revRange'])],
            ['label' => 'গড় অর্ডার (AOV)', 'value' => bdt($orders['aov'])],
        ];
    @endphp
    @foreach ($sales as $c)
        <div class="rounded-2xl border border-gray-200 bg-white p-4">
            <p class="text-xs text-gray-500">{{ $c['label'] }}</p>
            <p class="mt-1 text-xl font-bold text-ink">{{ $c['value'] }}</p>
        </div>
    @endforeach
</div>

<div class="grid gap-5 lg:grid-cols-2">
    {{-- ভিজিটর চার্ট --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-5">
        <h2 class="mb-4 text-sm font-bold text-ink">ভিজিটর ট্রেন্ড</h2>
        @if (count($visitors['daily']))
            <div class="flex h-40 items-end gap-1 overflow-x-auto">
                @foreach ($visitors['daily'] as $d)
                    <div class="flex min-w-[18px] flex-1 flex-col items-center gap-1" title="{{ $d['day'] }}: {{ $d['pv'] }} পেজভিউ, {{ $d['uv'] }} ভিজিটর">
                        <div class="w-full rounded-t bg-brand" style="height: {{ max(2, round($d['pv'] / $maxPv * 130)) }}px"></div>
                        <span class="text-[9px] text-gray-400">{{ $d['day'] }}</span>
                    </div>
                @endforeach
            </div>
        @else
            <p class="py-8 text-center text-sm text-gray-400">এই রেঞ্জে কোনো ভিজিট নেই</p>
        @endif
    </div>

    {{-- বিক্রি চার্ট --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-5">
        <h2 class="mb-4 text-sm font-bold text-ink">বিক্রির ট্রেন্ড</h2>
        @if (count($orders['daily']))
            <div class="flex h-40 items-end gap-1 overflow-x-auto">
                @foreach ($orders['daily'] as $d)
                    <div class="flex min-w-[18px] flex-1 flex-col items-center gap-1" title="{{ $d['day'] }}: {{ $d['orders'] }} অর্ডার, ৳{{ $d['rev'] }}">
                        <div class="w-full rounded-t bg-brand-dark" style="height: {{ max(2, round($d['rev'] / $maxRev * 130)) }}px"></div>
                        <span class="text-[9px] text-gray-400">{{ $d['day'] }}</span>
                    </div>
                @endforeach
            </div>
        @else
            <p class="py-8 text-center text-sm text-gray-400">এই রেঞ্জে কোনো অর্ডার নেই</p>
        @endif
    </div>

    {{-- সোর্স --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-5">
        <h2 class="mb-3 text-sm font-bold text-ink">ট্রাফিক সোর্স</h2>
        <ul class="space-y-1 text-sm">
            @forelse ($visitors['sources'] as $s)
                <li class="flex justify-between border-b border-gray-50 py-1">
                    <span class="text-gray-600">{{ $s['source'] }}</span>
                    <span class="font-semibold text-ink">{{ bn_num($s['n']) }}</span>
                </li>
            @empty
                <li class="py-4 text-center text-gray-400">ডেটা নেই</li>
            @endforelse
        </ul>
    </div>

    {{-- টপ পেজ --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-5">
        <h2 class="mb-3 text-sm font-bold text-ink">জনপ্রিয় পেজ</h2>
        <ul class="space-y-1 text-sm">
            @forelse ($visitors['topPages'] as $p)
                <li class="flex justify-between border-b border-gray-50 py-1">
                    <span class="truncate text-gray-600">{{ $p['path'] }}</span>
                    <span class="ml-2 font-semibold text-ink">{{ bn_num($p['n']) }}</span>
                </li>
            @empty
                <li class="py-4 text-center text-gray-400">ডেটা নেই</li>
            @endforelse
        </ul>
    </div>

    {{-- ডিভাইস --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-5">
        <h2 class="mb-3 text-sm font-bold text-ink">ডিভাইস</h2>
        <ul class="space-y-1 text-sm">
            @forelse ($visitors['devices'] as $d)
                <li class="flex justify-between border-b border-gray-50 py-1">
                    <span class="text-gray-600">{{ $d['device'] === 'mobile' ? '📱 মোবাইল' : '💻 ডেস্কটপ' }}</span>
                    <span class="font-semibold text-ink">{{ bn_num($d['n']) }}</span>
                </li>
            @empty
                <li class="py-4 text-center text-gray-400">ডেটা নেই</li>
            @endforelse
        </ul>
    </div>

    {{-- বেস্ট সেলার --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-5">
        <h2 class="mb-3 text-sm font-bold text-ink">বেস্ট সেলার</h2>
        <ul class="space-y-1 text-sm">
            @forelse ($orders['top'] as $t)
                <li class="flex justify-between gap-2 border-b border-gray-50 py-1">
                    <span class="truncate text-gray-600">{{ $t['product'] ?: '—' }}</span>
                    <span class="shrink-0 font-semibold text-ink">{{ bn_num($t['n']) }} · {{ bdt($t['rev']) }}</span>
                </li>
            @empty
                <li class="py-4 text-center text-gray-400">ডেটা নেই</li>
            @endforelse
        </ul>
    </div>

    {{-- স্ট্যাটাস ব্রেকডাউন --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-5 lg:col-span-2">
        <h2 class="mb-3 text-sm font-bold text-ink">অর্ডার স্ট্যাটাস (রেঞ্জে)</h2>
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-5">
            @php
                $statusLabels = [
                    'pending' => 'পেন্ডিং', 'confirmed' => 'কনফার্মড', 'sent' => 'কুরিয়ারে',
                    'delivered' => 'ডেলিভারড', 'cancelled' => 'বাতিল',
                ];
            @endphp
            @foreach ($statusLabels as $key => $label)
                <div class="rounded-xl bg-gray-50 p-3 text-center">
                    <p class="text-xs text-gray-500">{{ $label }}</p>
                    <p class="mt-1 text-lg font-bold text-ink">{{ bn_num($orders['status'][$key] ?? 0) }}</p>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
