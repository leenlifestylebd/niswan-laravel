@php
    $variants = $product->variantList();
    // ডিফল্ট: মেইন দামের সাথে মেলে এমন ভ্যারিয়েন্ট, না পেলে প্রথমটা
    $defaultIdx = 0;
    foreach ($variants as $vi => $v) {
        if ((int) ($v['price'] ?? 0) === (int) $product->price) { $defaultIdx = $vi; break; }
    }
@endphp

<section id="order" class="relative py-24 sm:py-28"
    x-data="orderForm({
        variants: @js(array_values($variants)),
        sizes: @js(array_values($product->sizes ?: [])),
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

    <div class="mx-auto max-w-5xl px-6 sm:px-10">

        {{-- সফল হলে --}}
        <div x-show="status === 'success'" x-cloak class="mx-auto max-w-lg border border-[color:var(--color-accent)]/40 bg-[color:var(--color-surface)] px-8 py-16 text-center">
            <div class="mx-auto flex h-16 w-16 items-center justify-center border border-[color:var(--color-accent)] text-3xl text-[color:var(--color-accent)]">✓</div>
            <h2 class="mt-6 text-3xl">অর্ডার গৃহীত হয়েছে</h2>
            <div class="rule-gold mx-auto mt-5 max-w-[140px]"></div>
            <p class="mt-5 text-[color:var(--color-muted)]">
                ধন্যবাদ <span class="text-[color:var(--color-fg)]" x-text="name"></span>!
                আমরা শীঘ্রই <span class="text-[color:var(--color-fg)]" x-text="phone"></span> নাম্বারে কল করে অর্ডার কনফার্ম করব।
            </p>
        </div>

        <div x-show="status !== 'success'">
            <div class="mb-12 text-center">
                <p class="eyebrow">অর্ডার</p>
                <h2 class="mt-4 text-4xl sm:text-5xl">ফর্মটি পূরণ করুন</h2>
                <div class="rule-gold mx-auto mt-6 max-w-[180px]"></div>
                <p class="mt-5 text-sm text-[color:var(--color-muted)]">
                    ক্যাশ অন ডেলিভারি — পণ্য হাতে পেয়ে টাকা দিন।
                </p>
            </div>

            <form @submit.prevent="submit" @focusin="firstInteract"
                  class="grid gap-10 border border-[color:var(--color-line)] bg-[color:var(--color-surface)] p-6 sm:p-10 lg:grid-cols-[1.15fr_1fr] lg:gap-14">

                {{-- বাঁ কলাম: পছন্দ --}}
                <div class="space-y-8">
                    {{-- ভ্যারিয়েন্ট --}}
                    <div>
                        <p class="text-xs uppercase tracking-[0.22em] text-[color:var(--color-muted)]">অপশন</p>
                        <div class="mt-4 space-y-3">
                            <template x-for="(v, idx) in variants" :key="idx">
                                <button type="button" @click="vIdx = idx"
                                        class="flex w-full items-center gap-4 border p-3 text-left transition"
                                        :class="idx === vIdx ? 'border-[color:var(--color-accent)] bg-[color:var(--color-raised)]' : 'border-[color:var(--color-line)] hover:border-[color:var(--color-accent)]/50'">
                                    <span class="h-16 w-14 shrink-0 overflow-hidden bg-[color:var(--color-bg)]">
                                        <template x-if="image">
                                            <img :src="image" :alt="productName" class="h-full w-full object-cover">
                                        </template>
                                    </span>
                                    <span class="flex-1">
                                        <span class="block text-sm text-[color:var(--color-fg)]" x-text="v.name"></span>
                                        <span class="mt-1 block text-xs text-[color:var(--color-muted)]" x-text="productName"></span>
                                    </span>
                                    <span class="font-display text-lg text-[color:var(--color-accent)]" x-text="bdt(v.price)"></span>
                                    <span class="flex h-5 w-5 shrink-0 items-center justify-center border"
                                          :class="idx === vIdx ? 'border-[color:var(--color-accent)] bg-[color:var(--color-accent)] text-[#17120a]' : 'border-[color:var(--color-line)]'">
                                        <span class="text-[10px]" x-show="idx === vIdx">✓</span>
                                    </span>
                                </button>
                            </template>
                        </div>
                    </div>

                    {{-- সাইজ --}}
                    <div x-show="sizes.length">
                        <p class="text-xs uppercase tracking-[0.22em] text-[color:var(--color-muted)]">সাইজ</p>
                        <div class="mt-4 flex flex-wrap gap-2.5">
                            <template x-for="s in sizes" :key="s">
                                <button type="button" @click="size = s"
                                        class="border px-5 py-2.5 text-sm transition"
                                        :class="size === s ? 'border-[color:var(--color-accent)] bg-[color:var(--color-accent)] text-[#17120a] font-semibold' : 'border-[color:var(--color-line)] text-[color:var(--color-fg)] hover:border-[color:var(--color-accent)]'"
                                        x-text="s"></button>
                            </template>
                        </div>
                    </div>

                    {{-- পরিমাণ --}}
                    <div>
                        <p class="text-xs uppercase tracking-[0.22em] text-[color:var(--color-muted)]">পরিমাণ</p>
                        <div class="mt-4 inline-flex items-center border border-[color:var(--color-line)]">
                            <button type="button" @click="qty = Math.max(1, qty - 1)"
                                    class="px-5 py-2.5 text-lg text-[color:var(--color-accent)]">−</button>
                            <span class="w-12 text-center font-display text-lg" x-text="qty"></span>
                            <button type="button" @click="qty = qty + 1"
                                    class="px-5 py-2.5 text-lg text-[color:var(--color-accent)]">+</button>
                        </div>
                    </div>

                    {{-- ডেলিভারি এলাকা --}}
                    <div>
                        <p class="text-xs uppercase tracking-[0.22em] text-[color:var(--color-muted)]">ডেলিভারি এলাকা</p>
                        <div class="mt-4 grid gap-3 sm:grid-cols-2">
                            <button type="button" @click="area = 'inside'"
                                    class="border px-4 py-3 text-sm transition"
                                    :class="area === 'inside' ? 'border-[color:var(--color-accent)] bg-[color:var(--color-raised)]' : 'border-[color:var(--color-line)] text-[color:var(--color-muted)] hover:border-[color:var(--color-accent)]/50'">
                                ঢাকার ভিতরে · <span x-text="bdt(insideDhaka)"></span>
                            </button>
                            <button type="button" @click="area = 'outside'"
                                    class="border px-4 py-3 text-sm transition"
                                    :class="area === 'outside' ? 'border-[color:var(--color-accent)] bg-[color:var(--color-raised)]' : 'border-[color:var(--color-line)] text-[color:var(--color-muted)] hover:border-[color:var(--color-accent)]/50'">
                                ঢাকার বাইরে · <span x-text="bdt(outsideDhaka)"></span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- ডান কলাম: তথ্য + সামারি --}}
                <div class="space-y-6 lg:border-l lg:border-[color:var(--color-line)] lg:pl-14">
                    <div class="space-y-3">
                        <p class="text-xs uppercase tracking-[0.22em] text-[color:var(--color-muted)]">আপনার তথ্য</p>
                        <input x-model="name" placeholder="আপনার নাম" required class="field">
                        <div>
                            <input x-model="phone" @input="phoneErr = ''" inputmode="tel" required
                                   placeholder="মোবাইল নাম্বার (১১ ডিজিট)"
                                   class="field" :class="phoneErr ? 'border-red-500' : ''">
                            <p x-show="phoneErr" x-cloak class="mt-2 text-xs text-red-400">⚠️ <span x-text="phoneErr"></span></p>
                        </div>
                        <textarea x-model="address" rows="3" required
                                  placeholder="সম্পূর্ণ ঠিকানা (গ্রাম/বাসা, থানা, জেলা)" class="field"></textarea>
                    </div>

                    {{-- সামারি --}}
                    <div class="border-t border-[color:var(--color-line)] pt-6 text-sm">
                        <div class="flex justify-between text-[color:var(--color-muted)]">
                            <span x-text="variant.name + ' × ' + qty"></span>
                            <span x-text="bdt(subtotal)"></span>
                        </div>
                        <div class="mt-2 flex justify-between text-[color:var(--color-muted)]">
                            <span>ডেলিভারি চার্জ</span>
                            <span x-text="bdt(deliveryCharge)"></span>
                        </div>
                        <div class="mt-4 flex items-baseline justify-between border-t border-[color:var(--color-line)] pt-4">
                            <span class="text-xs uppercase tracking-[0.22em] text-[color:var(--color-muted)]">সর্বমোট</span>
                            <span class="font-display text-3xl text-[color:var(--color-accent)]" x-text="bdt(total)"></span>
                        </div>
                    </div>

                    <p x-show="status === 'error'" x-cloak class="text-sm text-red-400">
                        দুঃখিত, সমস্যা হয়েছে। অনুগ্রহ করে WhatsApp এ অর্ডার করুন।
                    </p>

                    <button type="submit" :disabled="status === 'sending'" class="btn-gold w-full"
                            x-text="status === 'sending' ? 'পাঠানো হচ্ছে...' : 'অর্ডার কনফার্ম করুন'"></button>

                    <button type="button" @click="orderViaWhatsApp" class="btn-whatsapp w-full">
                        <svg viewBox="0 0 24 24" class="h-5 w-5 fill-current" aria-hidden="true">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                        WhatsApp এ অর্ডার করুন
                    </button>
                </div>
            </form>
        </div>
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
                this.$nextTick(() => document.getElementById('order').scrollIntoView({ behavior: 'smooth' }));
            } catch (e) {
                this.status = 'error';
            }
        },
    }));
});
</script>
@endpush
