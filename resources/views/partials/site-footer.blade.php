@php $msg = rawurlencode('আসসালামু আলাইকুম, '.$site['brandName'].' থেকে অর্ডার করতে চাই।'); @endphp

{{-- এক লাইনের অনুভূমিক ফুটার --}}
<footer class="border-t border-[color:var(--color-line)] bg-[color:var(--color-bg)]">
    {{-- sm:pb-24 — নিচের ডান কোণে ভাসমান WhatsApp বাটন যেন টেক্সট না ঢাকে --}}
    <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-center gap-x-6 gap-y-3 px-6 py-5 text-sm sm:justify-between sm:px-10 sm:pb-24">

        {{-- ব্র্যান্ড --}}
        <a href="#top" class="shrink-0">
            @if ($site['logoUrl'])
                <img src="{{ $site['logoUrl'] }}" alt="{{ $site['brandName'] }}"
                     style="height: {{ min($site['logoHeight'], 28) }}px" class="w-auto max-w-[140px] object-contain">
            @else
                <span class="font-display text-base tracking-[0.24em]">{{ mb_strtoupper($site['brandName']) }}</span>
            @endif
        </a>

        {{-- লিংক --}}
        <nav class="flex flex-wrap items-center justify-center gap-x-5 gap-y-2 text-[color:var(--color-muted)]">
            <a href="#detail" class="transition hover:text-[color:var(--color-accent)]">প্রোডাক্ট</a>
            <a href="#reviews" class="transition hover:text-[color:var(--color-accent)]">রিভিউ</a>
            <a href="#order" class="transition hover:text-[color:var(--color-accent)]">অর্ডার</a>
            <span class="text-[color:var(--color-accent)]">◆</span>
            <a href="tel:{{ $site['phone'] }}" class="transition hover:text-[color:var(--color-accent)]">{{ $site['phone'] }}</a>
            <a href="https://wa.me/{{ $site['whatsapp'] }}?text={{ $msg }}" target="_blank" rel="noopener noreferrer"
               class="transition hover:text-[color:var(--color-accent)]">WhatsApp</a>
            @if ($site['facebook'])
                <a href="{{ $site['facebook'] }}" target="_blank" rel="noopener noreferrer"
                   class="transition hover:text-[color:var(--color-accent)]">Facebook</a>
            @endif
            @if ($site['instagram'])
                <a href="{{ $site['instagram'] }}" target="_blank" rel="noopener noreferrer"
                   class="transition hover:text-[color:var(--color-accent)]">Instagram</a>
            @endif
        </nav>

        {{-- কপিরাইট --}}
        <p class="shrink-0 text-xs text-[color:var(--color-muted)]">
            © {{ bn_num(date('Y')) }} {{ $site['brandName'] }}
        </p>
    </div>

    {{-- মোবাইল স্টিকি বারের জন্য জায়গা --}}
    <div class="h-16 sm:hidden"></div>
</footer>
