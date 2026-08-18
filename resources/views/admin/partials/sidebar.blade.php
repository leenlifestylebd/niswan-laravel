@php
    $links = [
        ['route' => 'admin.orders',    'label' => 'অর্ডার',        'icon' => 'M4 5h16M4 12h16M4 19h10'],
        ['route' => 'admin.products',  'label' => 'প্রোডাক্ট',     'icon' => 'M4 7l8-4 8 4v10l-8 4-8-4V7zm8-4v18'],
        ['route' => 'admin.reviews',   'label' => 'রিভিউ',         'icon' => 'M12 4l2.4 5 5.6.8-4 3.9.9 5.5L12 16.6 7.1 19.2l.9-5.5-4-3.9L9.6 9 12 4z'],
        ['route' => 'admin.analytics', 'label' => 'অ্যানালিটিক্স', 'icon' => 'M4 20V10m6 10V4m6 16v-7m4 7H4'],
        ['route' => 'admin.settings',  'label' => 'সেটিংস',        'icon' => 'M12 15a3 3 0 100-6 3 3 0 000 6zm8-3l1.5-1-1.5-2.6-1.8.6a7 7 0 00-1.6-1L16 6h-3l-.6 1.9a7 7 0 00-1.6 1l-1.8-.6L7.5 11 9 12l-1.5 1 1.5 2.6 1.8-.6a7 7 0 001.6 1L13 18h3l.6-1.9a7 7 0 001.6-1l1.8.6L21.5 13 20 12z'],
    ];
@endphp

<aside x-cloak
       :class="menu ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
       class="fixed inset-y-0 left-0 z-50 flex w-64 flex-col border-r border-[color:var(--color-line)] bg-[color:var(--color-surface)] transition-transform duration-200 lg:translate-x-0">

    {{-- ব্র্যান্ড --}}
    <div class="flex items-center justify-between border-b border-[color:var(--color-line)] px-5 py-5">
        <a href="{{ route('admin.orders') }}" class="block">
            @if ($site['logoUrl'])
                <img src="{{ $site['logoUrl'] }}" alt="{{ $site['brandName'] }}"
                     style="height: {{ min($site['logoHeight'], 34) }}px" class="w-auto max-w-[150px] object-contain">
            @else
                <span class="font-display text-lg tracking-[0.24em]">{{ mb_strtoupper($site['brandName']) }}</span>
            @endif
            <span class="mt-1 block text-[10px] uppercase tracking-[0.28em] text-[color:var(--color-muted)]">অ্যাডমিন</span>
        </a>

        <button type="button" @click="menu = false" aria-label="বন্ধ করুন"
                class="text-[color:var(--color-muted)] lg:hidden">✕</button>
    </div>

    {{-- নেভিগেশন --}}
    <nav class="flex-1 overflow-y-auto py-4">
        @foreach ($links as $link)
            @php $active = request()->routeIs($link['route']); @endphp
            <a href="{{ route($link['route']) }}"
               class="nav-link {{ $active ? 'nav-link-active' : '' }}"
               @if ($active) aria-current="page" @endif>
                <svg viewBox="0 0 24 24" class="h-4.5 w-4.5 shrink-0 stroke-current" fill="none" stroke-width="1.5">
                    <path d="{{ $link['icon'] }}" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                {{ $link['label'] }}
            </a>
        @endforeach
    </nav>

    {{-- নিচে: লগআউট --}}
    <div class="border-t border-[color:var(--color-line)] p-4">
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit" class="btn-sm btn-sm-danger w-full justify-center">লগআউট</button>
        </form>
    </div>
</aside>
