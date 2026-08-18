@extends('layouts.admin')

@section('title', 'রিভিউ')

@section('content')
<p class="mb-5 text-sm text-[color:var(--color-muted)]">
    মোট {{ bn_num($reviews->count()) }}টি স্ক্রিনশট — ল্যান্ডিং পেজের "গ্রাহকের কথা" সেকশনে দেখায়।
</p>

{{-- নতুন রিভিউ যোগ --}}
<section class="panel mb-7 max-w-2xl p-5"
     x-data="{ url: '', uploading: false, uploadError: '',
        async upload(e) {
            const file = (e.target.files || [])[0];
            if (!file) return;
            this.uploading = true; this.uploadError = '';
            try {
                const fd = new FormData();
                fd.append('file', file); fd.append('prefix', 'review');
                const res = await fetch('{{ route('admin.upload') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').content,
                        'Accept': 'application/json',
                    },
                    body: fd,
                });
                const json = await res.json();
                if (json.ok) this.url = json.url; else this.uploadError = json.error || 'আপলোড ব্যর্থ';
            } catch (err) { this.uploadError = 'আপলোড ব্যর্থ'; }
            this.uploading = false; e.target.value = '';
        } }">
    <h2 class="panel-title">নতুন রিভিউ স্ক্রিনশট</h2>

    <div class="mt-4 flex flex-wrap items-center gap-3">
        <input type="file" accept="image/*" @change="upload($event)"
               class="text-xs text-[color:var(--color-muted)] file:mr-2 file:border file:border-[color:var(--color-line)] file:bg-transparent file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-[color:var(--color-accent)]">
        <span x-show="uploading" x-cloak class="text-xs text-[color:var(--color-muted)]">আপলোড হচ্ছে...</span>
        <span x-show="uploadError" x-cloak class="text-xs text-red-400" x-text="uploadError"></span>
    </div>

    <form method="POST" action="{{ route('admin.reviews.store') }}" class="mt-4 flex gap-2">
        @csrf
        <input name="image_url" x-model="url" required placeholder="ছবির URL (আপলোড করলে অটো বসবে)" class="field">
        <button type="submit" class="btn-gold shrink-0 px-5 py-2 text-sm">যোগ করুন</button>
    </form>
</section>

{{-- রিভিউ গ্রিড --}}
<div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5">
    @forelse ($reviews as $r)
        <div class="panel overflow-hidden">
            <div class="h-56 w-full bg-[color:var(--color-raised)]">
                <img src="{{ $r->image_url }}" alt="রিভিউ {{ $r->id }}" loading="lazy" class="h-full w-full object-contain object-top">
            </div>
            <div class="flex items-center justify-between border-t border-[color:var(--color-line)] px-3 py-2 text-xs text-[color:var(--color-muted)]">
                <span>ক্রম: {{ bn_num($r->sort_order) }}</span>
                <form method="POST" action="{{ route('admin.reviews.destroy', $r->id) }}"
                      onsubmit="return confirm('এই রিভিউ মুছে ফেলবেন?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-sm btn-sm-danger px-2 py-1">🗑️</button>
                </form>
            </div>
        </div>
    @empty
        <p class="col-span-full py-10 text-center text-[color:var(--color-muted)]">এখনো কোনো রিভিউ নেই</p>
    @endforelse
</div>
@endsection
