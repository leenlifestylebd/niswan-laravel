@php
    $notes = ['ক্যাশ অন ডেলিভারি', 'সারা দেশে ২–৩ দিনে', 'প্রিমিয়াম ফেব্রিক', '১০০% হাতে-যাচাই করা', 'সহজ রিটার্ন'];
@endphp

{{-- সরু ঘোষণা বার — একটানা চলমান --}}
<div class="overflow-hidden border-y border-[color:var(--color-line)] bg-[color:var(--color-surface)] py-3">
    <div class="marquee-track flex w-max">
        @for ($g = 0; $g < 2; $g++)
            <div class="flex shrink-0 items-center">
                @foreach ($notes as $note)
                    <span class="px-8 text-xs uppercase tracking-[0.28em] text-[color:var(--color-muted)]">{{ $note }}</span>
                    <span class="text-[color:var(--color-accent)]">◆</span>
                @endforeach
            </div>
        @endfor
    </div>
</div>
