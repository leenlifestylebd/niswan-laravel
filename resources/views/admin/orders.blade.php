@extends('layouts.admin')

@section('title', 'অর্ডার')

@section('content')
@php
    $statusMeta = [
        'pending'         => ['label' => 'পেন্ডিং',   'tone' => 'text-amber-400'],
        'confirmed'       => ['label' => 'কনফার্মড',  'tone' => 'text-sky-400'],
        'sent_to_courier' => ['label' => 'কুরিয়ারে',  'tone' => 'text-violet-400'],
        'delivered'       => ['label' => 'ডেলিভারড',  'tone' => 'text-emerald-400'],
        'cancelled'       => ['label' => 'বাতিল',     'tone' => 'text-red-400'],
    ];
@endphp

<div x-data="{
        selected: [],
        editing: null,
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
        <div class="stat">
            <p class="stat-label">মোট অর্ডার</p>
            <p class="stat-value text-[color:var(--color-accent)]">{{ bn_num($stats['total']) }}</p>
        </div>
        @foreach ($statusMeta as $key => $meta)
            <div class="stat">
                <p class="stat-label">{{ $meta['label'] }}</p>
                <p class="stat-value">{{ bn_num($stats[$key] ?? 0) }}</p>
            </div>
        @endforeach
    </div>

    {{-- বাল্ক অ্যাকশন --}}
    <div class="panel mb-5 flex flex-wrap items-center gap-3 p-4">
        <span class="text-sm text-[color:var(--color-muted)]">
            নির্বাচিত: <span class="font-semibold text-[color:var(--color-fg)]" x-text="selected.length"></span>
        </span>

        <form method="POST" action="{{ route('admin.steadfast.bulk') }}" @submit="bulkSend" class="contents">
            @csrf
            <template x-for="id in selected" :key="id">
                <input type="hidden" name="ids[]" :value="id">
            </template>
            <button type="submit" @if (! $steadfastReady) disabled title="Steadfast API key সেট করা নেই" @endif
                    class="btn-gold px-5 py-2 text-sm disabled:cursor-not-allowed">
                কুরিয়ারে পাঠাও
            </button>
        </form>

        <button type="button" @click="printLabels" class="btn-outline px-5 py-2 text-sm">
            লেবেল প্রিন্ট · ৫০×৭৫mm
        </button>

        @unless ($steadfastReady)
            <span class="text-xs text-amber-400">⚠️ Steadfast API key সেটিংসে দিন</span>
        @endunless
    </div>

    {{-- অর্ডার টেবিল --}}
    <div class="panel overflow-x-auto">
        <table class="w-full min-w-[900px] text-sm">
            <thead class="border-b border-[color:var(--color-line)] text-left">
                <tr class="[&>th]:p-3 [&>th]:text-[11px] [&>th]:font-semibold [&>th]:uppercase [&>th]:tracking-[0.16em] [&>th]:text-[color:var(--color-muted)]">
                    <th><input type="checkbox" @change="toggleAll" class="accent-[color:var(--color-accent)]"></th>
                    <th>#</th>
                    <th>তারিখ</th>
                    <th>কাস্টমার</th>
                    <th>প্রোডাক্ট</th>
                    <th>মোট</th>
                    <th>স্ট্যাটাস</th>
                    <th>কুরিয়ার</th>
                    <th></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[color:var(--color-line)]">
                @forelse ($orders as $order)
                    <tr class="align-top">
                        <td class="p-3">
                            <input type="checkbox" x-model.number="selected" value="{{ $order->id }}"
                                   class="accent-[color:var(--color-accent)]">
                        </td>
                        <td class="p-3 font-display text-lg text-[color:var(--color-accent)]">{{ $order->id }}</td>
                        <td class="p-3 whitespace-nowrap text-xs text-[color:var(--color-muted)]">
                            {{ $order->created_at?->timezone(config('app.timezone'))->format('d M, h:i A') }}
                        </td>
                        <td class="p-3">
                            <p class="font-medium">{{ $order->name }}</p>
                            <a href="tel:{{ $order->phone }}" class="text-xs text-[color:var(--color-accent)]">{{ $order->phone }}</a>
                            <p class="mt-1 max-w-[220px] text-xs text-[color:var(--color-muted)]">{{ $order->address }}</p>
                        </td>
                        <td class="p-3">
                            <p>{{ $order->product }}</p>
                            <p class="text-xs text-[color:var(--color-muted)]">
                                সাইজ: {{ $order->size }} · পরিমাণ: {{ bn_num($order->qty) }}
                            </p>
                            <p class="text-xs text-[color:var(--color-muted)]">{{ $order->area }} — {{ bdt($order->delivery_charge) }}</p>
                        </td>
                        <td class="p-3 font-display text-lg">{{ bdt($order->total) }}</td>
                        <td class="p-3">
                            <form method="POST" action="{{ route('admin.status') }}">
                                @csrf
                                <input type="hidden" name="id" value="{{ $order->id }}">
                                <select name="status" onchange="this.form.submit()"
                                        class="field w-auto px-2 py-1 text-xs font-semibold {{ $statusMeta[$order->status]['tone'] ?? '' }}">
                                    @foreach ($statusMeta as $key => $meta)
                                        <option value="{{ $key }}" @selected($order->status === $key)>{{ $meta['label'] }}</option>
                                    @endforeach
                                </select>
                            </form>
                        </td>
                        <td class="p-3 text-xs">
                            @if ($order->consignment_id)
                                <p class="font-medium text-emerald-400">✓ পাঠানো</p>
                                <p class="text-[color:var(--color-muted)]">{{ $order->tracking_code }}</p>
                            @else
                                <form method="POST" action="{{ route('admin.steadfast') }}">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $order->id }}">
                                    <button type="submit" @disabled(! $steadfastReady)
                                            class="btn-sm disabled:cursor-not-allowed disabled:opacity-40">পাঠাও</button>
                                </form>
                            @endif
                        </td>
                        <td class="p-3">
                            <button type="button" @click="editing = (editing === {{ $order->id }} ? null : {{ $order->id }})"
                                    class="btn-sm">এডিট</button>
                        </td>
                    </tr>

                    {{-- এডিট প্যানেল --}}
                    <tr x-show="editing === {{ $order->id }}" x-cloak>
                        <td colspan="9" class="bg-[color:var(--color-raised)] p-5">
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
                                  class="grid gap-4 md:grid-cols-3">
                                @csrf

                                <label class="panel-title">নাম
                                    <input name="name" value="{{ $order->name }}" required class="field mt-1.5">
                                </label>

                                <label class="panel-title">ফোন
                                    <input name="phone" value="{{ $order->phone }}" required class="field mt-1.5">
                                </label>

                                <label class="panel-title">প্রোডাক্ট
                                    <input name="product" value="{{ $order->product }}" class="field mt-1.5">
                                </label>

                                <label class="panel-title md:col-span-3">ঠিকানা
                                    <textarea name="address" rows="2" required class="field mt-1.5">{{ $order->address }}</textarea>
                                </label>

                                <label class="panel-title">সাইজ
                                    <input name="size" value="{{ $order->size }}" class="field mt-1.5">
                                </label>

                                <label class="panel-title">পরিমাণ
                                    <input name="qty" type="number" min="1" x-model.number="qty" @input="recalc" class="field mt-1.5">
                                </label>

                                <label class="panel-title">একক দাম (৳)
                                    <input type="number" min="0" x-model.number="unit" @input="recalc" class="field mt-1.5">
                                </label>

                                <label class="panel-title">এলাকা
                                    <select name="area" x-model="area" @change="syncCharge" class="field mt-1.5">
                                        <option value="ঢাকার ভিতরে">ঢাকার ভিতরে</option>
                                        <option value="ঢাকার বাইরে">ঢাকার বাইরে</option>
                                    </select>
                                </label>

                                <label class="panel-title">ডেলিভারি চার্জ (৳)
                                    <input name="delivery_charge" type="number" min="0" x-model.number="charge" @input="recalc" class="field mt-1.5">
                                </label>

                                <label class="panel-title">সর্বমোট (৳)
                                    <input name="total" type="number" min="0" x-model.number="total" class="field mt-1.5">
                                </label>

                                <div class="flex items-end gap-3 md:col-span-3">
                                    <button type="submit" class="btn-gold px-6 py-2.5 text-sm">সেভ করুন</button>
                                    <button type="button" @click="editing = null" class="btn-outline px-6 py-2.5 text-sm">বাতিল</button>
                                </div>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="p-10 text-center text-[color:var(--color-muted)]">এখনো কোনো অর্ডার নেই</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
