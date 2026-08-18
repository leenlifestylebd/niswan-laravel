@php $msg = rawurlencode('আসসালামু আলাইকুম, '.$site['brandName'].' থেকে অর্ডার করতে চাই।'); @endphp

<footer class="border-t border-[color:var(--color-line)] bg-[color:var(--color-bg)]">
    <div class="mx-auto max-w-6xl px-6 py-16 sm:px-10">
        <div class="grid gap-10 sm:grid-cols-3">
            {{-- ব্র্যান্ড --}}
            <div>
                @if ($site['logoUrl'])
                    <img src="{{ $site['logoUrl'] }}" alt="{{ $site['brandName'] }}"
                         style="height: {{ $site['logoHeight'] }}px" class="w-auto max-w-[180px] object-contain">
                @else
                    <p class="font-display text-2xl tracking-[0.28em]">{{ mb_strtoupper($site['brandName']) }}</p>
                @endif
                <p class="mt-4 max-w-xs text-sm leading-relaxed text-[color:var(--color-muted)]">{{ $site['footerText'] }}</p>
            </div>

            {{-- যোগাযোগ --}}
            <div>
                <p class="text-xs uppercase tracking-[0.22em] text-[color:var(--color-muted)]">যোগাযোগ</p>
                <ul class="mt-4 space-y-2.5 text-sm">
                    <li><a href="tel:{{ $site['phone'] }}" class="transition hover:text-[color:var(--color-accent)]">{{ $site['phone'] }}</a></li>
                    <li><a href="https://wa.me/{{ $site['whatsapp'] }}?text={{ $msg }}" target="_blank" rel="noopener noreferrer" class="transition hover:text-[color:var(--color-accent)]">WhatsApp</a></li>
                    @if ($site['facebook'])
                        <li><a href="{{ $site['facebook'] }}" target="_blank" rel="noopener noreferrer" class="transition hover:text-[color:var(--color-accent)]">Facebook</a></li>
                    @endif
                    @if ($site['instagram'])
                        <li><a href="{{ $site['instagram'] }}" target="_blank" rel="noopener noreferrer" class="transition hover:text-[color:var(--color-accent)]">Instagram</a></li>
                    @endif
                </ul>
            </div>

            {{-- এই পেজে --}}
            <div>
                <p class="text-xs uppercase tracking-[0.22em] text-[color:var(--color-muted)]">এই পেজে</p>
                <ul class="mt-4 space-y-2.5 text-sm">
                    <li><a href="#detail" class="transition hover:text-[color:var(--color-accent)]">প্রোডাক্ট</a></li>
                    <li><a href="#reviews" class="transition hover:text-[color:var(--color-accent)]">রিভিউ</a></li>
                    <li><a href="#order" class="transition hover:text-[color:var(--color-accent)]">অর্ডার</a></li>
                </ul>
            </div>
        </div>

        <div class="rule-gold mt-12"></div>

        <p class="mt-6 text-center text-xs text-[color:var(--color-muted)]">
            © {{ bn_num(date('Y')) }} {{ $site['brandName'] }} · সর্বস্বত্ব সংরক্ষিত
        </p>
    </div>

    {{-- মোবাইল স্টিকি বারের জন্য জায়গা --}}
    <div class="h-16 sm:hidden"></div>
</footer>
