@php
    $variants = $product->variantList();
    // ডিফল্ট: মেইন দামের সাথে মেলে এমন ভ্যারিয়েন্ট (সাধারণত "ফুল সেট"), না পেলে প্রথমটা
    $defaultIdx = 0;
    foreach ($variants as $vi => $v) {
        if ((int) ($v['price'] ?? 0) === (int) $product->price) { $defaultIdx = $vi; break; }
    }
    $sizes = $product->sizes ?: [];
@endphp

<section id="order" class="relative overflow-hidden bg-brand-light"
    x-data="orderForm({
        variants: @js(array_values($variants)),
        sizes: @js(array_values($sizes)),
        defaultIdx: {{ $defaultIdx }},
        productName: @js($product->name),
        slug: @js($product->slug),
        image: @js($product->mainImage()),
        brandName: @js($site['brandName']),
        whatsapp: @js($site['whatsapp']),
        insideDhaka: {{ $site['delivery']['insideDhaka'] }},
        outsideDhaka: {{ $site['delivery']['outsideDhaka'] }},
        endpoint: @js(route('order.store')),
    })">

    {{-- aesthetic background শেপ --}}
    <div class="pointer-events-none absolute -left-16 top-10 h-60 w-60 rounded-full bg-brand/15 blur-3xl"></div>
    <div class="pointer-events-none absolute -right-16 bottom-10 h-72 w-72 rounded-full bg-brand/15 blur-3xl"></div>

    {{-- সফল হলে --}}
    <div x-show="status === 'success'" x-cloak class="relative mx-auto max-w-md px-4 py-20 text-center">
        <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-brand text-3xl text-white">✓</div>
        <h2 class="text-2xl font-bold text-ink">অর্ডার সফল হয়েছে!</h2>
        <p class="mt-2 text-gray-600">
            ধন্যবাদ <span x-text="name"></span>! আমরা শীঘ্রই <span x-text="phone"></span> নাম্বারে কল করে অর্ডার কনফার্ম করব।
        </p>
    </div>

    <div class="relative mx-auto max-w-2xl px-4 py-14" x-show="status !== 'success'">
        <div class="mb-8 text-center">
            <span class="text-sm font-medium text-brand">সহজ অর্ডার</span>
            <h2 class="mt-1 text-3xl font-bold text-ink">নিচের ফর্মটি পূরণ করুন</h2>
        </div>

        <form @submit.prevent="submit" @focusin="firstInteract"
              class="space-y-5 rounded-3xl bg-white p-6 shadow-lg">

            {{-- অপশন নির্বাচন --}}
            <div>
                <label class="mb-2 block text-sm font-semibold text-gray-700">অপশন নির্বাচন করুন</label>
                <div class="space-y-2">
                    <template x-for="(v, idx) in variants" :key="idx">
                        <button type="button" @click="vIdx = idx"
                                class="flex w-full items-center gap-3 rounded-xl border p-2 text-left transition"
                                :class="idx === vIdx ? 'border-brand bg-brand-light' : 'border-gray-200 hover:border-brand/50'">
                            <span class="h-12 w-12 shrink-0 overflow-hidden rounded-lg bg-white">
                                <template x-if="image">
                                    <img :src="image" :alt="productName" class="h-full w-full object-cover">
                                </template>
                                <template x-if="!image">
                                    <span class="flex h-full w-full items-center justify-center bg-brand-light text-xl">🧕</span>
                                </template>
                            </span>
                            <span class="flex-1">
                                <span class="block text-sm font-semibold text-ink" x-text="productName + ' — ' + v.name"></span>
                                <span class="mt-0.5 inline-block rounded-full px-2 py-0.5 text-[11px] font-semibold"
                                      :class="idx === vIdx ? 'bg-brand text-white' : 'bg-brand-light text-brand-dark'"
                                      x-text="v.name"></span>
                            </span>
                            <span class="font-bold text-brand" x-text="bdt(v.price)"></span>
                            <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full border-2"
                                  :class="idx === vIdx ? 'border-brand bg-brand text-white' : 'border-gray-300'">
                                <span class="text-[11px]" x-show="idx === vIdx">✓</span>
                            </span>
                        </button>
                    </template>
                </div>
            </div>

            {{-- সাইজ --}}
            <div x-show="sizes.length">
                <label class="mb-2 block text-sm font-semibold text-gray-700">সাইজ নির্বাচন করুন</label>
                <div class="flex flex-wrap gap-2">
                    <template x-for="s in sizes" :key="s">
                        <button type="button" @click="size = s"
                                class="rounded-xl border px-4 py-2 text-sm font-medium transition"
                                :class="size === s ? 'border-brand bg-brand text-white' : 'border-gray-200 text-gray-700 hover:border-brand'"
                                x-text="s"></button>
                    </template>
                </div>
            </div>

            {{-- পরিমাণ --}}
            <div>
                <label class="mb-2 block text-sm font-semibold text-gray-700">পরিমাণ</label>
                <div class="inline-flex items-center rounded-xl border border-gray-200">
                    <button type="button" @click="qty = Math.max(1, qty - 1)" class="px-4 py-2 text-lg text-brand">−</button>
                    <span class="w-10 text-center font-semibold" x-text="qty"></span>
                    <button type="button" @click="qty = qty + 1" class="px-4 py-2 text-lg text-brand">+</button>
                </div>
            </div>

            {{-- ডেলিভারি এরিয়া --}}
            <div>
                <label class="mb-2 block text-sm font-semibold text-gray-700">ডেলিভারি এলাকা</label>
                <div class="grid grid-cols-2 gap-3">
                    <button type="button" @click="area = 'inside'"
                            class="rounded-xl border px-3 py-2.5 text-sm font-medium transition"
                            :class="area === 'inside' ? 'border-brand bg-brand-light text-ink' : 'border-gray-200 text-gray-600'">
                        ঢাকার ভিতরে (<span x-text="bdt(insideDhaka)"></span>)
                    </button>
                    <button type="button" @click="area = 'outside'"
                            class="rounded-xl border px-3 py-2.5 text-sm font-medium transition"
                            :class="area === 'outside' ? 'border-brand bg-brand-light text-ink' : 'border-gray-200 text-gray-600'">
                        ঢাকার বাইরে (<span x-text="bdt(outsideDhaka)"></span>)
                    </button>
                </div>
            </div>

            {{-- কাস্টমার তথ্য --}}
            <div class="space-y-3">
                <input x-model="name" placeholder="আপনার নাম" required
                       class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm outline-none focus:border-brand">
                <div>
                    <input x-model="phone" @input="phoneErr = ''" inputmode="tel" required
                           placeholder="মোবাইল নাম্বার (১১ ডিজিট, যেমন 01712345678)"
                           class="w-full rounded-xl border px-3 py-2.5 text-sm outline-none"
                           :class="phoneErr ? 'border-red-400 focus:border-red-500' : 'border-gray-200 focus:border-brand'">
                    <p x-show="phoneErr" x-cloak class="mt-1 text-xs font-medium text-red-500">⚠️ <span x-text="phoneErr"></span></p>
                </div>
                <textarea x-model="address" rows="3" required
                          placeholder="সম্পূর্ণ ঠিকানা (গ্রাম/বাসা, থানা, জেলা)"
                          class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm outline-none focus:border-brand"></textarea>
            </div>

            {{-- অর্ডার সামারি --}}
            <div class="rounded-xl bg-gray-50 p-4 text-sm">
                <div class="flex justify-between text-gray-600">
                    <span x-text="productName + ' (' + variant.name + ') × ' + qty"></span>
                    <span x-text="bdt(subtotal)"></span>
                </div>
                <div class="mt-1 flex justify-between text-gray-600">
                    <span>ডেলিভারি চার্জ</span>
                    <span x-text="bdt(deliveryCharge)"></span>
                </div>
                <div class="mt-2 flex justify-between border-t border-gray-200 pt-2 text-base font-bold text-ink">
                    <span>সর্বমোট</span>
                    <span x-text="bdt(total)"></span>
                </div>
            </div>

            <p x-show="status === 'error'" x-cloak class="text-center text-sm text-brand">
                দুঃখিত, সমস্যা হয়েছে। অনুগ্রহ করে WhatsApp এ অর্ডার করুন।
            </p>

            <button type="submit" :disabled="status === 'sending'"
                    class="w-full rounded-full bg-brand py-3.5 text-base font-bold text-white shadow-md transition hover:bg-brand-dark disabled:opacity-60"
                    x-text="status === 'sending' ? 'পাঠানো হচ্ছে...' : 'অর্ডার কনফার্ম করুন'"></button>

            <button type="button" @click="orderViaWhatsApp"
                    class="flex w-full items-center justify-center gap-2 rounded-full border border-green-500 py-3 text-base font-semibold text-green-600 transition hover:bg-green-50">
                <span>📱</span> WhatsApp এ অর্ডার করুন
            </button>
        </form>
    </div>
</section>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('orderForm', (cfg) => ({
        ...cfg,
        vIdx: cfg.defaultIdx,
        size: cfg.sizes[0] || '',
        qty: 1,
        area: 'inside',      // inside | outside
        name: '', phone: '', address: '',
        phoneErr: '',
        status: 'idle',      // idle | sending | success | error
        checkoutFired: false,

        get variant() { return this.variants[this.vIdx] || { name: '', price: 0 }; },
        get deliveryCharge() { return this.area === 'inside' ? this.insideDhaka : this.outsideDhaka; },
        get subtotal() { return (this.variant.price || 0) * this.qty; },
        get total() { return this.subtotal + this.deliveryCharge; },
        get fullName() { return this.productName + ' (' + this.variant.name + ')'; },

        bdt(n) { return '৳' + Number(n || 0).toLocaleString('bn-BD'); },

        // বাংলাদেশি মোবাইল: ১১ ডিজিট, 01 দিয়ে শুরু। +880/880 থাকলেও ধরে।
        normalizePhone(p) {
            let d = String(p || '').replace(/\D/g, '');
            if (d.startsWith('88')) d = d.slice(2);
            return d;
        },
        validPhone(p) { return /^01\d{9}$/.test(this.normalizePhone(p)); },

        buildSummary() {
            return [
                '🛍️ নতুন অর্ডার — ' + this.brandName,
                'প্রোডাক্ট: ' + this.fullName,
                'সাইজ: ' + this.size,
                'পরিমাণ: ' + this.qty,
                'ডেলিভারি: ' + (this.area === 'inside' ? 'ঢাকার ভিতরে' : 'ঢাকার বাইরে') + ' (' + this.bdt(this.deliveryCharge) + ')',
                'মোট: ' + this.bdt(this.total),
                'নাম: ' + this.name,
                'ফোন: ' + this.phone,
                'ঠিকানা: ' + this.address,
            ].join('\n');
        },

        orderViaWhatsApp() {
            window.open('https://wa.me/' + this.whatsapp + '?text=' + encodeURIComponent(this.buildSummary()), '_blank');
        },

        // Pixel ইভেন্ট — Pixel লোড না থাকলে নিরাপদে কিছুই করে না
        track(event, data, options) {
            if (typeof window.fbq !== 'function') return;
            try { options ? window.fbq('track', event, data || {}, options) : window.fbq('track', event, data || {}); }
            catch (e) { /* ignore */ }
        },

        // প্রথম interaction এ একবার InitiateCheckout
        firstInteract() {
            if (this.checkoutFired) return;
            this.checkoutFired = true;
            this.track('InitiateCheckout', {
                value: this.total, currency: 'BDT',
                content_name: this.fullName, content_ids: [this.slug],
            });
        },

        async submit() {
            if (!this.name || !this.phone || !this.address) return;
            if (!this.validPhone(this.phone)) {
                this.phoneErr = 'সঠিক ১১ ডিজিটের মোবাইল নাম্বার দিন (যেমন 01712345678)';
                return;
            }
            this.phoneErr = '';
            this.status = 'sending';

            // একই event_id browser pixel + server CAPI দুই জায়গায় → Meta dedup করে
            const eventId = (window.crypto && crypto.randomUUID)
                ? crypto.randomUUID()
                : Date.now() + '-' + Math.random().toString(16).slice(2);

            try {
                const res = await fetch(this.endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        product: this.fullName,
                        slug: this.slug,
                        price: this.variant.price,
                        size: this.size,
                        qty: this.qty,
                        area: this.area === 'inside' ? 'ঢাকার ভিতরে' : 'ঢাকার বাইরে',
                        deliveryCharge: this.deliveryCharge,
                        total: this.total,
                        name: this.name,
                        phone: this.phone,
                        address: this.address,
                        eventId: eventId,
                    }),
                });
                if (!res.ok) throw new Error('failed');

                this.track('Purchase', {
                    value: this.total, currency: 'BDT',
                    content_name: this.fullName, content_ids: [this.slug], content_type: 'product',
                }, { eventID: eventId });

                this.status = 'success';
            } catch (e) {
                this.status = 'error';
            }
        },
    }));
});
</script>
@endpush
