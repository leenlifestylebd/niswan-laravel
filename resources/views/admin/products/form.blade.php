@extends('layouts.admin')

@section('title', $product->exists ? 'প্রোডাক্ট এডিট' : 'নতুন প্রোডাক্ট')

@section('content')
@php
    $variants = old('variants', $product->variants ?: [['name' => 'ফুল সেট', 'price' => $product->price]]);
    $images   = old('images', $product->images ?: []);
@endphp

<form method="POST"
      action="{{ $product->exists ? route('admin.products.update', $product->id) : route('admin.products.store') }}"
      x-data="productForm({ images: {{ Js::from(array_values($images)) }}, variants: {{ Js::from(array_values($variants)) }} })"
      class="max-w-4xl space-y-5">
    @csrf
    @if ($product->exists) @method('PUT') @endif

    <div class="flex justify-end">
        <a href="{{ route('admin.products') }}" class="text-sm text-[color:var(--color-muted)] hover:text-[color:var(--color-accent)]">← লিস্টে ফিরুন</a>
    </div>

    {{-- মূল তথ্য --}}
    <section class="panel grid gap-4 p-5 md:grid-cols-2">
        <h2 class="panel-title md:col-span-2">মূল তথ্য</h2>

        <label class="panel-title md:col-span-2">প্রোডাক্টের নাম *
            <input name="name" value="{{ old('name', $product->name) }}" required class="field mt-1.5">
        </label>

        <label class="panel-title">slug (খালি রাখলে নাম থেকে অটো)
            <input name="slug" value="{{ old('slug', $product->slug) }}" placeholder="auto" class="field mt-1.5">
        </label>

        <label class="panel-title">ব্যাজ (যেমন: বেস্ট সেলার)
            <input name="badge" value="{{ old('badge', $product->badge) }}" class="field mt-1.5">
        </label>

        <label class="panel-title">দাম (৳) *
            <input name="price" type="number" min="0" value="{{ old('price', $product->price) }}" required class="field mt-1.5">
        </label>

        <label class="panel-title">আগের দাম (৳, ছাড় দেখাতে)
            <input name="old_price" type="number" min="0" value="{{ old('old_price', $product->old_price) }}" class="field mt-1.5">
        </label>

        <label class="panel-title md:col-span-2">সংক্ষিপ্ত বর্ণনা
            <input name="short" value="{{ old('short', $product->short) }}" class="field mt-1.5">
        </label>

        <label class="panel-title">সাজানোর ক্রম
            <input name="sort_order" type="number" value="{{ old('sort_order', $product->sort_order) }}" class="field mt-1.5">
        </label>

        <label class="flex items-center gap-2.5 pt-7 text-sm">
            <input type="hidden" name="active" value="0">
            <input type="checkbox" name="active" value="1" @checked(old('active', $product->active))
                   class="h-4 w-4 accent-[color:var(--color-accent)]">
            সাইটে দেখানোর যোগ্য (সক্রিয়)
        </label>
    </section>

    {{-- ছবি --}}
    <section class="panel p-5">
        <div class="mb-4 flex flex-wrap items-center gap-3">
            <h2 class="panel-title">ছবি — প্রথমটি মেইন</h2>
            <input type="file" accept="image/*" multiple @change="uploadImages($event)"
                   class="text-xs text-[color:var(--color-muted)] file:mr-2 file:border file:border-[color:var(--color-line)] file:bg-transparent file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-[color:var(--color-accent)]">
            <span x-show="uploading" x-cloak class="text-xs text-[color:var(--color-muted)]">আপলোড হচ্ছে...</span>
            <span x-show="uploadError" x-cloak class="text-xs text-red-400" x-text="uploadError"></span>
        </div>

        <input type="hidden" name="image" :value="images[0] || ''">

        <div class="flex flex-wrap gap-3">
            <template x-for="(img, idx) in images" :key="idx">
                <div class="relative h-32 w-26 overflow-hidden border border-[color:var(--color-line)] bg-[color:var(--color-raised)]">
                    <img :src="img" class="h-full w-full object-cover" alt="">
                    <input type="hidden" name="images[]" :value="img">
                    <span x-show="idx === 0" class="absolute left-0 top-0 bg-[color:var(--color-accent)] px-1.5 py-0.5 text-[10px] font-bold text-[#17120a]">মেইন</span>
                    <button type="button" @click="images.splice(idx, 1)"
                            class="absolute right-1 top-1 flex h-5 w-5 items-center justify-center border border-[color:var(--color-line)] bg-[color:var(--color-bg)] text-xs text-red-400">×</button>
                    <button type="button" x-show="idx > 0" @click="moveFirst(idx)"
                            class="absolute bottom-1 left-1 border border-[color:var(--color-line)] bg-[color:var(--color-bg)] px-1.5 py-0.5 text-[10px]">মেইন করো</button>
                </div>
            </template>
            <p x-show="!images.length" x-cloak class="py-8 text-xs text-[color:var(--color-muted)]">কোনো ছবি নেই</p>
        </div>

        <label class="panel-title mt-4 block">অথবা ছবির URL যোগ করুন
            <span class="mt-1.5 flex gap-2">
                <input x-model="urlInput" placeholder="/products/example.jpg" class="field">
                <button type="button" @click="addUrl" class="btn-sm shrink-0 px-4">যোগ</button>
            </span>
        </label>
    </section>

    {{-- ভ্যারিয়েন্ট --}}
    <section class="panel p-5">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="panel-title">ভ্যারিয়েন্ট — অপশন ও দাম</h2>
            <button type="button" @click="variants.push({ name: '', price: 0 })" class="btn-sm">+ যোগ</button>
        </div>

        <div class="space-y-2.5">
            <template x-for="(v, idx) in variants" :key="idx">
                <div class="flex gap-2">
                    <input :name="'variants[' + idx + '][name]'" x-model="v.name" placeholder="অপশনের নাম (যেমন ফুল সেট)" class="field">
                    <input :name="'variants[' + idx + '][price]'" x-model.number="v.price" type="number" min="0" placeholder="দাম" class="field w-32 shrink-0">
                    <button type="button" @click="variants.splice(idx, 1)" class="btn-sm btn-sm-danger shrink-0 px-3">×</button>
                </div>
            </template>
        </div>
        <p class="mt-3 text-xs text-[color:var(--color-muted)]">খালি রাখলে "ফুল সেট" ডিফল্ট ধরা হবে।</p>
    </section>

    {{-- সাইজ ও ফিচার --}}
    <section class="panel grid gap-4 p-5 md:grid-cols-2">
        <label class="panel-title">সাইজ — প্রতি লাইনে একটি
            <textarea name="sizes" rows="5" class="field mt-1.5">{{ old('sizes', implode("\n", $product->sizes ?: [])) }}</textarea>
        </label>

        <label class="panel-title">ফিচার — প্রতি লাইনে একটি
            <textarea name="features" rows="5" class="field mt-1.5">{{ old('features', implode("\n", $product->features ?: [])) }}</textarea>
        </label>
    </section>

    <div class="flex gap-3">
        <button type="submit" class="btn-gold px-7 py-2.5 text-sm">
            {{ $product->exists ? 'আপডেট করুন' : 'যোগ করুন' }}
        </button>
        <a href="{{ route('admin.products') }}" class="btn-outline px-7 py-2.5 text-sm">বাতিল</a>
    </div>
</form>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('productForm', (cfg) => ({
        images: cfg.images || [],
        variants: cfg.variants || [],
        urlInput: '',
        uploading: false,
        uploadError: '',

        addUrl() {
            const v = this.urlInput.trim();
            if (v) { this.images.push(v); this.urlInput = ''; }
        },

        moveFirst(idx) {
            const [img] = this.images.splice(idx, 1);
            this.images.unshift(img);
        },

        async uploadImages(e) {
            const files = Array.from(e.target.files || []);
            if (!files.length) return;
            this.uploading = true;
            this.uploadError = '';
            for (const file of files) {
                try {
                    const fd = new FormData();
                    fd.append('file', file);
                    fd.append('prefix', 'product');
                    const res = await fetch('{{ route('admin.upload') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: fd,
                    });
                    const json = await res.json();
                    if (json.ok) this.images.push(json.url);
                    else this.uploadError = json.error || 'আপলোড ব্যর্থ';
                } catch (err) {
                    this.uploadError = 'আপলোড ব্যর্থ';
                }
            }
            this.uploading = false;
            e.target.value = '';
        },
    }));
});
</script>
@endpush
