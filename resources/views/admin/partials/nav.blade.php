@php
    $links = [
        ['route' => 'admin.orders',    'label' => 'অর্ডার',       'icon' => '🧾'],
        ['route' => 'admin.products',  'label' => 'প্রোডাক্ট',    'icon' => '📦'],
        ['route' => 'admin.reviews',   'label' => 'রিভিউ',        'icon' => '⭐'],
        ['route' => 'admin.analytics', 'label' => 'অ্যানালিটিক্স', 'icon' => '📊'],
        ['route' => 'admin.settings',  'label' => 'সেটিংস',       'icon' => '⚙️'],
    ];
@endphp

<header class="sticky top-0 z-40 border-b border-gray-200 bg-white">
    <div class="mx-auto flex max-w-7xl flex-wrap items-center gap-3 px-4 py-3">
        <a href="{{ route('admin.orders') }}" class="flex items-center gap-2 font-bold text-ink">
            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-brand text-sm text-white">🧕</span>
            <span>{{ $site['brandName'] }}</span>
            <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-500">অ্যাডমিন</span>
        </a>

        <nav class="order-3 flex w-full gap-1 overflow-x-auto text-sm md:order-2 md:w-auto md:flex-1 md:justify-center">
            @foreach ($links as $link)
                @php $active = request()->routeIs($link['route']); @endphp
                <a href="{{ route($link['route']) }}"
                   class="flex shrink-0 items-center gap-1.5 rounded-full px-3 py-1.5 font-medium transition
                          {{ $active ? 'bg-brand text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                    <span>{{ $link['icon'] }}</span>{{ $link['label'] }}
                </a>
            @endforeach
        </nav>

        <div class="order-2 ml-auto flex items-center gap-2 md:order-3 md:ml-0">
            <a href="{{ route('home') }}" target="_blank" rel="noopener"
               class="rounded-full border border-gray-200 px-3 py-1.5 text-sm text-gray-600 transition hover:bg-gray-100">
                সাইট দেখুন ↗
            </a>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="rounded-full border border-gray-200 px-3 py-1.5 text-sm text-gray-600 transition hover:bg-red-50 hover:text-red-600">
                    লগআউট
                </button>
            </form>
        </div>
    </div>
</header>
