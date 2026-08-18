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
      class="space-y-6">
    @csrf
    @if ($product->exists) @method('PUT') @endif

    <div class="flex items-center justify-between">
        <h1 class="text-xl font-bold text-ink">
            {{ $product->exists ? 'প্রোডাক্ট এডিট' : 'নতুন প্রোডাক্ট' }}
        </h1>
        <a href="{{ route('admin.products') }}" class="text-sm text-gray-500 hover:text-brand">← লিস্টে ফিরুন</a>
    </div>

    {{-- মূল তথ্য --}}
    <div class="grid gap-4 rounded-2xl border border-gray-200 bg-white p-5 md:grid-cols-2">
        <label class="text-sm font-semibold text-gray-700 md:col-span-2">প্রোডাক্টের নাম *
            <input name="name" value="{{ old('name', $product->name) }}" required
                   class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm font-normal outline-none focus:border-brand">
        </label>

        <label class="text-sm font-semibold text-gray-700">slug (খালি রাখলে নাম থেকে অটো)
            <input name="slug" value="{{ old('slug', $product->slug) }}" placeholder="auto"
                   class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm font-normal outline-none focus:border-brand">
        </label>

        <label class="text-sm font-semibold text-gray-700">ব্যাজ (যেমন: বেস্ট সেলার)
            <input name="badge" value="{{ old('badge', $product->badge) }}"
                   class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm font-normal outline-none focus:border-brand">
        </label>

        <label class="text-sm font-semibold text-gray-700">দাম (৳) *
            <input name="price" type="number" min="0" value="{{ old('price', $product->price) }}" required
                   class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm font-normal outline-none focus:border-brand">
        </label>

        <label class="text-sm font-semibold text-gray-700">আগের দাম (৳, ছাড় দেখাতে)
            <input name="old_price" type="number" min="0" value="{{ old('old_price', $product->old_price) }}"
                   class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm font-normal outline-none focus:border-brand">
        </label>

        <label class="text-sm font-semibold text-gray-700 md:col-span-2">সংক্ষিপ্ত বর্ণনা
            <input name="short" value="{{ old('short', $product->short) }}"
                   class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm font-normal outline-none focus:border-brand">
        </label>

        <label class="text-sm font-semibold text-gray-700">সাজানোর ক্রম
            <input name="sort_order" type="number" value="{{ old('sort_order', $product->sort_order) }}"
                   class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm font-normal outline-none focus:border-brand">
        </label>

        <label class="flex items-center gap-2 pt-6 text-sm font-semibold text-gray-700">
            <input type="hidden" name="active" value="0">
            <input type="checkbox" name="active" value="1" @checked(old('active', $product->active))
                   class="h-4 w-4 rounded border-gray-300">
            সাইটে দেখাও (সক্রিয়)
        </label>
    </div>

    {{-- ছবি --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-5">
        <div class="mb-3 flex flex-wrap items-center gap-3">
            <h2 class="text-sm font-semibold text-gray-700">ছবি (প্রথমটি মেইন ছবি)</h2>
            <input type="file" accept="image/*" multiple @change="uploadImages($event)"
                   class="text-xs text-gray-600 file:mr-2 file:rounded-full file:border-0 file:bg-brand file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-white">
            <span x-show="uploading" x-cloak class="text-xs text-gray-500">আপলোড হচ্ছে...</span>
            <span x-show="uploadError" x-cloak class="text-xs text-red-500" x-text="uploadError"></span>
        </div>

        <input type="hidden" name="image" :value="images[0] || ''">

        <div class="flex flex-wrap gap-3">
            <template x-for="(img, idx) in images" :key="idx">
                <div class="relative h-28 w-24 overflow-hidden rounded-xl border border-gray-200 bg-gray-50">
                    <img :src="img" class="h-full w-full object-cover" alt="">
                    <input type="hidden" name="images[]" :value="img">
                    <span x-show="idx === 0" class="absolute left-1 top-1 rounded-full bg-brand px-1.5 py-0.5 text-[10px] font-semibold text-white">মেইন</span>
                    <button type="button" @click="images.splice(idx, 1)"
                            class="absolute right-1 top-1 flex h-5 w-5 items-center justify-center rounded-full bg-white/90 text-xs text-red-600 shadow">×</button>
                    <button type="button" x-show="idx > 0" @click="moveFirst(idx)"
                            class="absolute bottom-1 left-1 rounded-full bg-white/90 px-1.5 py-0.5 text-[10px] text-gray-700 shadow">মেইন করো</button>
                </div>
            </template>
            <p x-show="!images.length" x-cloak class="py-6 text-xs text-gray-400">কোনো ছবি নেই</p>
        </div>

        <label class="mt-3 block text-xs text-gray-500">অথবা ছবির URL যোগ করুন
            <div class="mt-1 flex gap-2">
                <input x-model="urlInput" placeholder="/products/example.jpg"
                       class="flex-1 rounded-xl border border-gray-200 px-3 py-1.5 text-sm outline-none focus:border-brand">
                <button type="button" @click="addUrl"
                        class="rounded-xl border border-gray-200 px-3 py-1.5 text-sm font-semibold text-gray-700 hover:bg-gray-100">যোগ</button>
            </div>
        </label>
    </div>

    {{-- ভ্যারিয়েন্ট --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-5">
        <div class="mb-3 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-gray-700">ভ্যারিয়েন্ট (অপশন ও দাম)</h2>
            <button type="button" @click="variants.push({ name: '', price: 0 })"
                    class="rounded-full border border-gray-200 px-3 py-1 text-xs font-semibold text-gray-700 hover:bg-gray-100">+ যোগ</button>
        </div>

        <div class="space-y-2">
            <template x-for="(v, idx) in variants" :key="idx">
                <div class="flex gap-2">
                    <input :name="'variants[' + idx + '][name]'" x-model="v.name" placeholder="অপশনের নাম (যেমন ফুল সেট)"
                           class="flex-1 rounded-xl border border-gray-200 px-3 py-2 text-sm outline-none focus:border-brand">
                    <input :name="'variants[' + idx + '][price]'" x-model.number="v.price" type="number" min="0" placeholder="দাম"
                           class="w-32 rounded-xl border border-gray-200 px-3 py-2 text-sm outline-none focus:border-brand">
                    <button type="button" @click="variants.splice(idx, 1)"
                            class="rounded-xl border border-red-200 px-3 text-sm text-red-600 hover:bg-red-50">×</button>
                </div>
            </template>
        </div>
        <p class="mt-2 text-xs text-gray-400">খালি রাখলে "ফুল সেট" ডিফল্ট ধরা হবে।</p>
    </div>

    {{-- সাইজ ও ফিচার --}}
    <div class="grid gap-4 rounded-2xl border border-gray-200 bg-white p-5 md:grid-cols-2">
        <label class="text-sm font-semibold text-gray-700">সাইজ (প্রতি লাইনে একটি)
            <textarea name="sizes" rows="5"
                      class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm font-normal outline-none focus:border-brand">{{ old('sizes', implode("\n", $product->sizes ?: [])) }}</textarea>
        </label>

        <label class="text-sm font-semibold text-gray-700">ফিচার (প্রতি লাইনে একটি)
            <textarea name="features" rows="5"
                      class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm font-normal outline-none focus:border-brand">{{ old('features', implode("\n", $product->features ?: [])) }}</textarea>
        </label>
    </div>

    <div class="flex gap-2">
        <button type="submit" class="rounded-full bg-brand px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-brand-dark">
            {{ $product->exists ? 'আপডেট করুন' : 'যোগ করুন' }}
        </button>
        <a href="{{ route('admin.products') }}"
           class="rounded-full border border-gray-200 px-6 py-2.5 text-sm font-semibold text-gray-600 transition hover:bg-gray-100">বাতিল</a>
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
