<footer class="bg-ink text-white">
    <div class="mx-auto max-w-6xl px-4 py-10 text-center">
        <div class="flex items-center justify-center gap-2">
            @if ($site['logoUrl'])
                <img src="{{ $site['logoUrl'] }}" alt="{{ $site['brandName'] }}"
                     style="height: {{ $site['logoHeight'] }}px" class="w-auto max-w-[200px] object-contain">
            @else
                <span class="flex h-9 w-9 items-center justify-center rounded-full bg-brand text-lg">🧕</span>
                <span class="text-lg font-bold">{{ $site['brandName'] }}</span>
            @endif
        </div>
        <p class="mx-auto mt-3 max-w-md text-sm text-white/70">{{ $site['footerText'] }}</p>

        <div class="mt-5 flex flex-wrap items-center justify-center gap-4 text-sm">
            <a href="tel:{{ $site['phone'] }}" class="hover:text-brand">📞 {{ $site['phone'] }}</a>
            <a href="https://wa.me/{{ $site['whatsapp'] }}" target="_blank" rel="noopener noreferrer" class="hover:text-brand">💬 WhatsApp</a>
            @if ($site['facebook'])<a href="{{ $site['facebook'] }}" class="hover:text-brand">Facebook</a>@endif
            @if ($site['instagram'])<a href="{{ $site['instagram'] }}" class="hover:text-brand">Instagram</a>@endif
        </div>

        <p class="mt-6 text-xs text-white/50">© {{ date('Y') }} {{ $site['brandName'] }}. সর্বস্বত্ব সংরক্ষিত।</p>
    </div>
</footer>
