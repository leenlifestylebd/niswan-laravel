{{-- মিনিমাল ট্রান্সপারেন্ট হেডার — স্ক্রল করলে গাঢ় হয় --}}
<header x-data="{ solid: false }"
        @scroll.window="solid = window.scrollY > 40"
        class="fixed inset-x-0 top-0 z-40 transition-colors duration-300"
        :class="solid ? 'bg-[color:var(--color-bg)]/95 backdrop-blur border-b border-[color:var(--color-line)]' : ''">
    <div class="mx-auto flex max-w-6xl items-center justify-between px-5 py-4">
        <a href="#top" class="flex items-center gap-2.5">
            @if ($site['logoUrl'])
                <img src="{{ $site['logoUrl'] }}" alt="{{ $site['brandName'] }}"
                     style="height: {{ $site['logoHeight'] }}px" class="w-auto max-w-[200px] object-contain">
            @else
                <span class="font-display text-xl tracking-[0.28em] text-[color:var(--color-fg)]">
                    {{ mb_strtoupper($site['brandName']) }}
                </span>
            @endif
        </a>

        <a href="#order" class="border border-[color:var(--color-accent)] px-5 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-[color:var(--color-accent)] transition hover:bg-[color:var(--color-accent)] hover:text-[#17120a]">
            অর্ডার
        </a>
    </div>
</header>
