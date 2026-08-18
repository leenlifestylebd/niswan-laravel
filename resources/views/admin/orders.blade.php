@extends('layouts.admin')

@section('title', 'অর্ডার')

@section('content')
@php
    $statusMeta = [
        'pending'         => ['label' => 'পেন্ডিং',   'chip' => 'bg-amber-100 text-amber-800'],
        'confirmed'       => ['label' => 'কনফার্মড',  'chip' => 'bg-blue-100 text-blue-800'],
        'sent_to_courier' => ['label' => 'কুরিয়ারে',  'chip' => 'bg-purple-100 text-purple-800'],
        'delivered'       => ['label' => 'ডেলিভারড',  'chip' => 'bg-green-100 text-green-800'],
        'cancelled'       => ['label' => 'বাতিল',     'chip' => 'bg-red-100 text-red-800'],
    ];
@endphp

<div x-data="{
        selected: [],
        editing: null,
        inside: {{ $site['delivery']['insideDhaka'] }},
        outside: {{ $site['delivery']['outsideDhaka'] }},
        get allIds() { return {{ Js::from($orders->pluck('id')) }}; },
        toggleAll(e) { this.selected = e.target.checked ? [...this.allIds] : []; },
        printLabels() {
            if (!this.selected.length) { alert('লেবেল প্রিন্ট করতে অর্ডার নির্বাচন করুন'); return; }
            window.open('{{ route('admin.labels') }}?ids=' + this.selected.join(','), '_blank');
        },
        bulkSend(e) {
            if (!this.selected.length) { e.preventDefault(); alert('কুরিয়ারে পাঠাতে অর্ডার নির্বাচন করুন'); }
        }
     }">

    {{-- স্ট্যাটাস স্ট্যাট --}}
    <div class="mb-6 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6">
        <div class="rounded-2xl border border-gray-200 bg-white p-4">
            <p class="text-xs text-gray-500">মোট অর্ডার</p>
            <p class="mt-1 text-2xl font-bold text-ink">{{ bn_num($stats['total']) }}</p>
        </div>
        @foreach ($statusMeta as $key => $meta)
            <div class="rounded-2xl border border-gray-200 bg-white p-4">
                <p class="text-xs text-gray-500">{{ $meta['label'] }}</p>
                <p class="mt-1 text-2xl font-bold text-ink">{{ bn_num($stats[$key] ?? 0) }}</p>
            </div>
        @endforeach
    </div>

    {{-- বাল্ক অ্যাকশন --}}
    <div class="mb-4 flex flex-wrap items-center gap-2 rounded-2xl border border-gray-200 bg-white p-3">
        <span class="text-sm text-gray-600">
            নির্বাচিত: <span class="font-semibold text-ink" x-text="selected.length"></span>
        </span>

        <form method="POST" action="{{ route('admin.steadfast.bulk') }}" @submit="bulkSend" class="contents">
            @csrf
            <template x-for="id in selected" :key="id">
                <input type="hidden" name="ids[]" :value="id">
            </template>
            <button type="submit" @if (! $steadfastReady) disabled title="Steadfast API key সেট করা নেই" @endif
                    class="rounded-full bg-brand px-4 py-2 text-sm font-semibold text-white transition hover:bg-brand-dark disabled:cursor-not-allowed disabled:opacity-50">
                🚚 কুরিয়ারে পাঠাও
            </button>
        </form>

        <button type="button" @click="printLabels"
                class="rounded-full border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-100">
            🖨️ লেবেল প্রিন্ট (৫০×৭৫mm)
        </button>

        @unless ($steadfastReady)
            <span class="text-xs text-amber-700">⚠️ Steadfast API key সেটিংসে দিন</span>
        @endunless
    </div>

    {{-- অর্ডার টেবিল --}}
    <div class="overflow-x-auto rounded-2xl border border-gray-200 bg-white">
        <table class="w-full min-w-[900px] text-sm">
            <thead class="border-b border-gray-200 bg-gray-50 text-left text-xs uppercase tracking-wide text-gray-500">
                <tr>
                    <th class="p-3"><input type="checkbox" @change="toggleAll"></th>
                    <th class="p-3">#</th>
                    <th class="p-3">তারিখ</th>
                    <th class="p-3">কাস্টমার</th>
                    <th class="p-3">প্রোডাক্ট</th>
                    <th class="p-3">মোট</th>
                    <th class="p-3">স্ট্যাটাস</th>
                    <th class="p-3">কুরিয়ার</th>
                    <th class="p-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($orders as $order)
                    <tr class="align-top">
                        <td class="p-3"><input type="checkbox" x-model.number="selected" value="{{ $order->id }}"></td>
                        <td class="p-3 font-semibold text-ink">{{ $order->id }}</td>
                        <td class="p-3 whitespace-nowrap text-xs text-gray-500">
                            {{ $order->created_at?->timezone(config('app.timezone'))->format('d M, h:i A') }}
                        </td>
                        <td class="p-3">
                            <p class="font-medium text-ink">{{ $order->name }}</p>
                            <a href="tel:{{ $order->phone }}" class="text-xs text-brand">{{ $order->phone }}</a>
                            <p class="mt-1 max-w-[220px] text-xs text-gray-500">{{ $order->address }}</p>
                        </td>
                        <td class="p-3">
                            <p class="text-ink">{{ $order->product }}</p>
                            <p class="text-xs text-gray-500">
                                সাইজ: {{ $order->size }} · পরিমাণ: {{ bn_num($order->qty) }}
                            </p>
                            <p class="text-xs text-gray-500">{{ $order->area }} — {{ bdt($order->delivery_charge) }}</p>
                        </td>
                        <td class="p-3 font-bold text-ink">{{ bdt($order->total) }}</td>
                        <td class="p-3">
                            <form method="POST" action="{{ route('admin.status') }}">
                                @csrf
                                <input type="hidden" name="id" value="{{ $order->id }}">
                                <select name="status" onchange="this.form.submit()"
                                        class="rounded-full px-2 py-1 text-xs font-semibold {{ $statusMeta[$order->status]['chip'] ?? 'bg-gray-100 text-gray-700' }}">
                                    @foreach ($statusMeta as $key => $meta)
                                        <option value="{{ $key }}" @selected($order->status === $key)>{{ $meta['label'] }}</option>
                                    @endforeach
                                </select>
                            </form>
                        </td>
                        <td class="p-3 text-xs">
                            @if ($order->consignment_id)
                                <p class="font-medium text-green-700">✓ পাঠানো</p>
                                <p class="text-gray-500">{{ $order->tracking_code }}</p>
                            @else
                                <form method="POST" action="{{ route('admin.steadfast') }}">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $order->id }}">
                                    <button type="submit" @disabled(! $steadfastReady)
                                            class="rounded-full border border-brand px-3 py-1 font-semibold text-brand transition hover:bg-brand-light disabled:cursor-not-allowed disabled:opacity-40">
                                        🚚 পাঠাও
                                    </button>
                                </form>
                            @endif
                        </td>
                        <td class="p-3">
                            <button type="button" @click="editing = (editing === {{ $order->id }} ? null : {{ $order->id }})"
                                    class="rounded-full border border-gray-200 px-3 py-1 text-xs font-semibold text-gray-700 transition hover:bg-gray-100">
                                ✏️ এডিট
                            </button>
                        </td>
                    </tr>

                    {{-- এডিট প্যানেল --}}
                    <tr x-show="editing === {{ $order->id }}" x-cloak>
                        <td colspan="9" class="bg-gray-50 p-4">
                            <form method="POST" action="{{ route('admin.orders.update', $order->id) }}"
                                  x-data="{
                                      qty: {{ $order->qty }},
                                      area: @js($order->area),
                                      unit: {{ $order->qty > 0 ? intdiv(max(0, $order->total - $order->delivery_charge), $order->qty) : 0 }},
                                      charge: {{ $order->delivery_charge }},
                                      total: {{ $order->total }},
                                      insideCharge: {{ $site['delivery']['insideDhaka'] }},
                                      outsideCharge: {{ $site['delivery']['outsideDhaka'] }},
                                      // এলাকা বদলালে ডেলিভারি চার্জ অটো, তারপর সর্বমোট নতুন করে
                                      syncCharge() {
                                          this.charge = this.area === 'ঢাকার ভিতরে' ? this.insideCharge : this.outsideCharge;
                                          this.recalc();
                                      },
                                      recalc() { this.total = (Number(this.unit) * Number(this.qty)) + Number(this.charge); }
                                  }"
                                  class="grid gap-3 md:grid-cols-3">
                                @csrf

                                <label class="text-xs font-semibold text-gray-600">নাম
                                    <input name="name" value="{{ $order->name }}" required
                                           class="mt-1 w-full rounded-lg border border-gray-200 px-2 py-1.5 text-sm font-normal outline-none focus:border-brand">
                                </label>

                                <label class="text-xs font-semibold text-gray-600">ফোন
                                    <input name="phone" value="{{ $order->phone }}" required
                                           class="mt-1 w-full rounded-lg border border-gray-200 px-2 py-1.5 text-sm font-normal outline-none focus:border-brand">
                                </label>

                                <label class="text-xs font-semibold text-gray-600">প্রোডাক্ট
                                    <input name="product" value="{{ $order->product }}"
                                           class="mt-1 w-full rounded-lg border border-gray-200 px-2 py-1.5 text-sm font-normal outline-none focus:border-brand">
                                </label>

                                <label class="text-xs font-semibold text-gray-600 md:col-span-3">ঠিকানা
                                    <textarea name="address" rows="2" required
                                              class="mt-1 w-full rounded-lg border border-gray-200 px-2 py-1.5 text-sm font-normal outline-none focus:border-brand">{{ $order->address }}</textarea>
                                </label>

                                <label class="text-xs font-semibold text-gray-600">সাইজ
                                    <input name="size" value="{{ $order->size }}"
                                           class="mt-1 w-full rounded-lg border border-gray-200 px-2 py-1.5 text-sm font-normal outline-none focus:border-brand">
                                </label>

                                <label class="text-xs font-semibold text-gray-600">পরিমাণ
                                    <input name="qty" type="number" min="1" x-model.number="qty" @input="recalc"
                                           class="mt-1 w-full rounded-lg border border-gray-200 px-2 py-1.5 text-sm font-normal outline-none focus:border-brand">
                                </label>

                                <label class="text-xs font-semibold text-gray-600">একক দাম (৳)
                                    <input type="number" min="0" x-model.number="unit" @input="recalc"
                                           class="mt-1 w-full rounded-lg border border-gray-200 px-2 py-1.5 text-sm font-normal outline-none focus:border-brand">
                                </label>

                                <label class="text-xs font-semibold text-gray-600">এলাকা
                                    <select name="area" x-model="area" @change="syncCharge"
                                            class="mt-1 w-full rounded-lg border border-gray-200 px-2 py-1.5 text-sm font-normal outline-none focus:border-brand">
                                        <option value="ঢাকার ভিতরে">ঢাকার ভিতরে</option>
                                        <option value="ঢাকার বাইরে">ঢাকার বাইরে</option>
                                    </select>
                                </label>

                                <label class="text-xs font-semibold text-gray-600">ডেলিভারি চার্জ (৳)
                                    <input name="delivery_charge" type="number" min="0" x-model.number="charge" @input="recalc"
                                           class="mt-1 w-full rounded-lg border border-gray-200 px-2 py-1.5 text-sm font-normal outline-none focus:border-brand">
                                </label>

                                <label class="text-xs font-semibold text-gray-600">সর্বমোট (৳)
                                    <input name="total" type="number" min="0" x-model.number="total"
                                           class="mt-1 w-full rounded-lg border border-gray-200 px-2 py-1.5 text-sm font-normal outline-none focus:border-brand">
                                </label>

                                <div class="flex items-end gap-2 md:col-span-3">
                                    <button type="submit" class="rounded-full bg-brand px-5 py-2 text-sm font-semibold text-white transition hover:bg-brand-dark">
                                        সেভ করুন
                                    </button>
                                    <button type="button" @click="editing = null"
                                            class="rounded-full border border-gray-200 px-5 py-2 text-sm font-semibold text-gray-600 transition hover:bg-gray-100">
                                        বাতিল
                                    </button>
                                </div>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="p-8 text-center text-gray-500">এখনো কোনো অর্ডার নেই</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
