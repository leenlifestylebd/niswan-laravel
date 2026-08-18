@extends('layouts.admin')

@section('title', 'রিভিউ')

@section('content')
<h1 class="mb-4 text-xl font-bold text-ink">রিভিউ স্ক্রিনশট ({{ bn_num($reviews->count()) }})</h1>

{{-- নতুন রিভিউ যোগ --}}
<div class="mb-6 rounded-2xl border border-gray-200 bg-white p-5"
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
    <h2 class="mb-3 text-sm font-semibold text-gray-700">নতুন রিভিউ স্ক্রিনশট</h2>

    <div class="flex flex-wrap items-center gap-3">
        <input type="file" accept="image/*" @change="upload($event)"
               class="text-xs text-gray-600 file:mr-2 file:rounded-full file:border-0 file:bg-brand file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-white">
        <span x-show="uploading" x-cloak class="text-xs text-gray-500">আপলোড হচ্ছে...</span>
        <span x-show="uploadError" x-cloak class="text-xs text-red-500" x-text="uploadError"></span>
    </div>

    <form method="POST" action="{{ route('admin.reviews.store') }}" class="mt-3 flex gap-2">
        @csrf
        <input name="image_url" x-model="url" required placeholder="ছবির URL (আপলোড করলে অটো বসবে)"
               class="flex-1 rounded-xl border border-gray-200 px-3 py-2 text-sm outline-none focus:border-brand">
        <button type="submit" class="rounded-xl bg-brand px-5 py-2 text-sm font-semibold text-white transition hover:bg-brand-dark">
            যোগ করুন
        </button>
    </form>
</div>

{{-- রিভিউ গ্রিড --}}
<div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5">
    @forelse ($reviews as $r)
        <div class="relative overflow-hidden rounded-2xl border border-gray-200 bg-white">
            <div class="h-56 w-full bg-gray-50">
                <img src="{{ $r->image_url }}" alt="রিভিউ {{ $r->id }}" loading="lazy" class="h-full w-full object-contain object-top">
            </div>
            <div class="flex items-center justify-between px-3 py-2 text-xs text-gray-500">
                <span>ক্রম: {{ bn_num($r->sort_order) }}</span>
                <form method="POST" action="{{ route('admin.reviews.destroy', $r->id) }}"
                      onsubmit="return confirm('এই রিভিউ মুছে ফেলবেন?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="rounded-full border border-red-200 px-2 py-0.5 text-red-600 transition hover:bg-red-50">🗑️</button>
                </form>
            </div>
        </div>
    @empty
        <p class="col-span-full py-8 text-center text-gray-500">এখনো কোনো রিভিউ নেই</p>
    @endforelse
</div>
@endsection
