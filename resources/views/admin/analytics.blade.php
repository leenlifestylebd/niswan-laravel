@extends('layouts.admin')

@section('title', 'অ্যানালিটিক্স')

@section('content')
@php
    $presets = ['today' => 'আজ', '7' => '৭ দিন', '30' => '৩০ দিন', '90' => '৯০ দিন', '365' => '১ বছর'];
    $maxPv   = max(1, collect($visitors['daily'])->max('pv') ?? 1);
    $maxRev  = max(1, collect($orders['daily'])->max('rev') ?? 1);
@endphp

<div class="mb-5 flex flex-wrap items-center gap-3">
    {{-- রেঞ্জ প্রিসেট --}}
    <div class="flex flex-wrap gap-1.5">
        @foreach ($presets as $key => $label)
            <a href="{{ route('admin.analytics', ['range' => $key]) }}"
               class="border px-3.5 py-1.5 text-sm transition
                      {{ $range === $key
                            ? 'border-[color:var(--color-accent)] bg-[color:var(--color-accent)] font-semibold text-[#17120a]'
                            : 'border-[color:var(--color-line)] text-[color:var(--color-muted)] hover:border-[color:var(--color-accent)] hover:text-[color:var(--color-accent)]' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    {{-- কাস্টম রেঞ্জ --}}
    <form method="GET" action="{{ route('admin.analytics') }}" class="ml-auto flex flex-wrap items-center gap-2 text-sm">
        <input type="date" name="from" value="{{ $from->format('Y-m-d') }}" class="field w-auto py-1.5">
        <span class="text-[color:var(--color-muted)]">→</span>
        <input type="date" name="to" value="{{ $to->format('Y-m-d') }}" class="field w-auto py-1.5">
        <button type="submit" class="btn-sm px-4 py-2">দেখাও</button>
    </form>
</div>

<p class="mb-5 text-xs text-[color:var(--color-muted)]">
    রেঞ্জ: {{ $from->format('d M Y') }} — {{ $to->format('d M Y') }}
</p>

{{-- ভিজিটর মেট্রিক --}}
<div class="mb-4 grid grid-cols-2 gap-3 md:grid-cols-3 lg:grid-cols-5">
    @php
        $cards = [
            ['label' => '🟢 এখন অনলাইনে', 'value' => $visitors['live'], 'hot' => true],
            ['label' => 'আজকের পেজভিউ',   'value' => $visitors['pvToday']],
            ['label' => 'আজকের ভিজিটর',   'value' => $visitors['uvToday']],
            ['label' => 'রেঞ্জে পেজভিউ',  'value' => $visitors['pvRange']],
            ['label' => 'রেঞ্জে ভিজিটর',  'value' => $visitors['uvRange']],
        ];
    @endphp
    @foreach ($cards as $c)
        <div class="stat">
            <p class="stat-label">{{ $c['label'] }}</p>
            <p class="stat-value {{ ($c['hot'] ?? false) ? 'text-[color:var(--color-accent)]' : '' }}">{{ bn_num($c['value']) }}</p>
        </div>
    @endforeach
</div>

{{-- বিক্রি মেট্রিক --}}
<div class="mb-7 grid grid-cols-2 gap-3 md:grid-cols-3 lg:grid-cols-6">
    @php
        $sales = [
            ['label' => 'আজকের অর্ডার',   'value' => bn_num($orders['ordersToday'])],
            ['label' => 'রেঞ্জে অর্ডার',   'value' => bn_num($orders['ordersRange'])],
            ['label' => 'মোট অর্ডার',      'value' => bn_num($orders['ordersTotal'])],
            ['label' => 'আজকের বিক্রি',   'value' => bdt($orders['revToday'])],
            ['label' => 'রেঞ্জে বিক্রি',   'value' => bdt($orders['revRange']), 'hot' => true],
            ['label' => 'গড় অর্ডার (AOV)', 'value' => bdt($orders['aov'])],
        ];
    @endphp
    @foreach ($sales as $c)
        <div class="stat">
            <p class="stat-label">{{ $c['label'] }}</p>
            <p class="stat-value text-xl {{ ($c['hot'] ?? false) ? 'text-[color:var(--color-accent)]' : '' }}">{{ $c['value'] }}</p>
        </div>
    @endforeach
</div>

<div class="grid gap-5 lg:grid-cols-2">
    {{-- ভিজিটর চার্ট --}}
    <div class="panel p-5">
        <h2 class="panel-title">ভিজিটর ট্রেন্ড</h2>
        @if (count($visitors['daily']))
            <div class="mt-5 flex h-40 items-end gap-1 overflow-x-auto">
                @foreach ($visitors['daily'] as $d)
                    <div class="flex min-w-[18px] flex-1 flex-col items-center gap-1.5"
                         title="{{ $d['day'] }}: {{ $d['pv'] }} পেজভিউ, {{ $d['uv'] }} ভিজিটর">
                        <div class="w-full bg-[color:var(--color-accent)]" style="height: {{ max(2, round($d['pv'] / $maxPv * 128)) }}px"></div>
                        <span class="text-[9px] text-[color:var(--color-muted)]">{{ $d['day'] }}</span>
                    </div>
                @endforeach
            </div>
        @else
            <p class="py-10 text-center text-sm text-[color:var(--color-muted)]">এই রেঞ্জে কোনো ভিজিট নেই</p>
        @endif
    </div>

    {{-- বিক্রি চার্ট --}}
    <div class="panel p-5">
        <h2 class="panel-title">বিক্রির ট্রেন্ড</h2>
        @if (count($orders['daily']))
            <div class="mt-5 flex h-40 items-end gap-1 overflow-x-auto">
                @foreach ($orders['daily'] as $d)
                    <div class="flex min-w-[18px] flex-1 flex-col items-center gap-1.5"
                         title="{{ $d['day'] }}: {{ $d['orders'] }} অর্ডার, ৳{{ $d['rev'] }}">
                        <div class="w-full bg-[color:var(--color-fg)]/70" style="height: {{ max(2, round($d['rev'] / $maxRev * 128)) }}px"></div>
                        <span class="text-[9px] text-[color:var(--color-muted)]">{{ $d['day'] }}</span>
                    </div>
                @endforeach
            </div>
        @else
            <p class="py-10 text-center text-sm text-[color:var(--color-muted)]">এই রেঞ্জে কোনো অর্ডার নেই</p>
        @endif
    </div>

    @php
        $lists = [
            ['title' => 'ট্রাফিক সোর্স', 'rows' => collect($visitors['sources'])->map(fn ($r) => ['k' => $r['source'], 'v' => bn_num($r['n'])])],
            ['title' => 'জনপ্রিয় পেজ',   'rows' => collect($visitors['topPages'])->map(fn ($r) => ['k' => $r['path'], 'v' => bn_num($r['n'])])],
            ['title' => 'ডিভাইস',        'rows' => collect($visitors['devices'])->map(fn ($r) => ['k' => $r['device'] === 'mobile' ? '📱 মোবাইল' : '💻 ডেস্কটপ', 'v' => bn_num($r['n'])])],
            ['title' => 'বেস্ট সেলার',   'rows' => collect($orders['top'])->map(fn ($r) => ['k' => $r['product'] ?: '—', 'v' => bn_num($r['n']).' · '.bdt($r['rev'])])],
        ];
    @endphp

    @foreach ($lists as $list)
        <div class="panel p-5">
            <h2 class="panel-title">{{ $list['title'] }}</h2>
            <ul class="mt-4 space-y-0 text-sm">
                @forelse ($list['rows'] as $row)
                    <li class="flex justify-between gap-3 border-b border-[color:var(--color-line)] py-2 last:border-0">
                        <span class="truncate text-[color:var(--color-muted)]">{{ $row['k'] }}</span>
                        <span class="shrink-0 font-semibold">{{ $row['v'] }}</span>
                    </li>
                @empty
                    <li class="py-5 text-center text-[color:var(--color-muted)]">ডেটা নেই</li>
                @endforelse
            </ul>
        </div>
    @endforeach

    {{-- স্ট্যাটাস ব্রেকডাউন --}}
    <div class="panel p-5 lg:col-span-2">
        <h2 class="panel-title">অর্ডার স্ট্যাটাস (রেঞ্জে)</h2>
        <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-5">
            @php
                $statusLabels = [
                    'pending' => 'পেন্ডিং', 'confirmed' => 'কনফার্মড', 'sent' => 'কুরিয়ারে',
                    'delivered' => 'ডেলিভারড', 'cancelled' => 'বাতিল',
                ];
            @endphp
            @foreach ($statusLabels as $key => $label)
                <div class="border border-[color:var(--color-line)] bg-[color:var(--color-raised)] p-3 text-center">
                    <p class="text-xs text-[color:var(--color-muted)]">{{ $label }}</p>
                    <p class="mt-1 font-display text-2xl">{{ bn_num($orders['status'][$key] ?? 0) }}</p>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
