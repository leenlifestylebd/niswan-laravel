@extends('layouts.admin')

@section('title', 'সেটিংস')

@section('content')
@php $s = $settings; @endphp

<form method="POST" action="{{ route('admin.settings.update') }}" class="max-w-4xl space-y-5">
    @csrf

    {{-- ল্যান্ডিং পেজ --}}
    <section class="panel grid gap-4 p-5 md:grid-cols-2"
        x-data="{ uploading: '', err: '',
            async up(e, target) {
                const file = (e.target.files || [])[0]; if (!file) return;
                this.uploading = target; this.err = '';
                try {
                    const fd = new FormData(); fd.append('file', file); fd.append('prefix', 'banner');
                    const res = await fetch('{{ route('admin.upload') }}', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').content, 'Accept': 'application/json' },
                        body: fd,
                    });
                    const json = await res.json();
                    if (json.ok) document.querySelector('[name=\'' + target + '\']').value = json.url;
                    else this.err = json.error || 'আপলোড ব্যর্থ';
                } catch (err) { this.err = 'আপলোড ব্যর্থ'; }
                this.uploading = ''; e.target.value = '';
            } }">
        <div class="md:col-span-2">
            <h2 class="panel-title">ল্যান্ডিং পেজ — সিঙ্গেল প্রোডাক্ট</h2>
            <p class="mt-2 text-xs text-[color:var(--color-muted)]">
                সাইটে একটাই পেজ, একটাই প্রোডাক্ট। হিরোতে নিচের ব্যানারটি ফুল-স্ক্রিন দেখায়।
            </p>
            <p x-show="err" x-cloak class="mt-1 text-xs text-red-400" x-text="err"></p>
        </div>

        <label class="panel-title md:col-span-2">যে প্রোডাক্টটি দেখাবে
            <select name="landingProductSlug" class="field mt-1.5">
                <option value="">— প্রথম সক্রিয় প্রোডাক্ট (অটো) —</option>
                @foreach ($activeProducts as $p)
                    <option value="{{ $p->slug }}" @selected($s['landingProductSlug'] === $p->slug)>{{ $p->name }}</option>
                @endforeach
            </select>
        </label>

        <label class="panel-title">ব্যানার — ডেস্কটপ
            <input name="bannerUrl" value="{{ $s['bannerUrl'] }}" placeholder="খালি রাখলে প্রোডাক্টের মেইন ছবি" class="field mt-1.5">
            <input type="file" accept="image/*" @change="up($event, 'bannerUrl')"
                   class="mt-2 w-full text-xs text-[color:var(--color-muted)] file:mr-2 file:border file:border-[color:var(--color-line)] file:bg-transparent file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-[color:var(--color-accent)]">
            <span x-show="uploading === 'bannerUrl'" x-cloak class="text-xs text-[color:var(--color-muted)]">আপলোড হচ্ছে...</span>
        </label>

        <label class="panel-title">ব্যানার — মোবাইল (পোর্ট্রেট)
            <input name="bannerMobileUrl" value="{{ $s['bannerMobileUrl'] }}" placeholder="খালি রাখলে ডেস্কটপেরটাই" class="field mt-1.5">
            <input type="file" accept="image/*" @change="up($event, 'bannerMobileUrl')"
                   class="mt-2 w-full text-xs text-[color:var(--color-muted)] file:mr-2 file:border file:border-[color:var(--color-line)] file:bg-transparent file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-[color:var(--color-accent)]">
            <span x-show="uploading === 'bannerMobileUrl'" x-cloak class="text-xs text-[color:var(--color-muted)]">আপলোড হচ্ছে...</span>
        </label>

        <label class="panel-title">হিরো হেডলাইন (খালি হলে ট্যাগলাইন)
            <input name="bannerHeadline" value="{{ $s['bannerHeadline'] }}" class="field mt-1.5">
        </label>

        <label class="panel-title">হিরো সাবলাইন (খালি হলে বর্ণনা)
            <input name="bannerSubline" value="{{ $s['bannerSubline'] }}" class="field mt-1.5">
        </label>
    </section>

    {{-- ব্র্যান্ড --}}
    <section class="panel grid gap-4 p-5 md:grid-cols-2">
        <h2 class="panel-title md:col-span-2">ব্র্যান্ড</h2>

        <label class="panel-title">ব্র্যান্ডের নাম
            <input name="brandName" value="{{ $s['brandName'] }}" class="field mt-1.5">
        </label>
        <label class="panel-title">ট্যাগলাইন
            <input name="tagline" value="{{ $s['tagline'] }}" class="field mt-1.5">
        </label>
        <label class="panel-title md:col-span-2">বর্ণনা
            <textarea name="description" rows="2" class="field mt-1.5">{{ $s['description'] }}</textarea>
        </label>
        <label class="panel-title">সাইট URL
            <input name="url" value="{{ $s['url'] }}" class="field mt-1.5">
        </label>
        <label class="panel-title">ফুটার টেক্সট
            <input name="footerText" value="{{ $s['footerText'] }}" class="field mt-1.5">
        </label>
        <label class="panel-title">লোগো URL
            <input name="logoUrl" value="{{ $s['logoUrl'] }}" class="field mt-1.5">
        </label>
        <label class="panel-title">লোগোর উচ্চতা (px)
            <input name="logoHeight" type="number" min="16" value="{{ $s['logoHeight'] }}" class="field mt-1.5">
        </label>
        <label class="panel-title md:col-span-2">ফেভিকন URL
            <input name="faviconUrl" value="{{ $s['faviconUrl'] }}" class="field mt-1.5">
        </label>
    </section>

    {{-- যোগাযোগ --}}
    <section class="panel grid gap-4 p-5 md:grid-cols-2">
        <h2 class="panel-title md:col-span-2">যোগাযোগ</h2>

        <label class="panel-title">ফোন
            <input name="phone" value="{{ $s['phone'] }}" class="field mt-1.5">
        </label>
        <label class="panel-title">WhatsApp (কান্ট্রি কোড সহ, + ছাড়া)
            <input name="whatsapp" value="{{ $s['whatsapp'] }}" placeholder="8801XXXXXXXXX" class="field mt-1.5">
        </label>
        <label class="panel-title">Facebook
            <input name="facebook" value="{{ $s['facebook'] }}" class="field mt-1.5">
        </label>
        <label class="panel-title">Instagram
            <input name="instagram" value="{{ $s['instagram'] }}" class="field mt-1.5">
        </label>
    </section>

    {{-- ডেলিভারি ও থিম --}}
    <section class="panel grid gap-4 p-5 md:grid-cols-2">
        <h2 class="panel-title md:col-span-2">ডেলিভারি ও থিম</h2>

        <label class="panel-title">ঢাকার ভিতরে চার্জ (৳)
            <input name="deliveryInside" type="number" min="0" value="{{ $s['delivery']['insideDhaka'] }}" class="field mt-1.5">
        </label>
        <label class="panel-title">ঢাকার বাইরে চার্জ (৳)
            <input name="deliveryOutside" type="number" min="0" value="{{ $s['delivery']['outsideDhaka'] }}" class="field mt-1.5">
        </label>

        <label class="panel-title">অ্যাকসেন্ট রঙ — CTA/হাইলাইট
            <input name="colorAccent" type="color" value="{{ $s['colorAccent'] }}" class="field mt-1.5 h-11 p-1">
        </label>
        <label class="panel-title">ব্যাকগ্রাউন্ড রঙ — গাঢ়
            <input name="colorGlobal" type="color" value="{{ $s['colorGlobal'] }}" class="field mt-1.5 h-11 p-1">
        </label>
        <label class="panel-title">ব্র্যান্ড রঙ
            <input name="colorPrimary" type="color" value="{{ $s['colorPrimary'] }}" class="field mt-1.5 h-11 p-1">
        </label>
        <label class="panel-title">ব্র্যান্ড রঙ — গাঢ় (hover)
            <input name="colorSecondary" type="color" value="{{ $s['colorSecondary'] }}" class="field mt-1.5 h-11 p-1">
        </label>
    </section>

    {{-- কুরিয়ার লেবেল --}}
    <section class="panel grid gap-4 p-5 md:grid-cols-2">
        <h2 class="panel-title md:col-span-2">কুরিয়ার লেবেল</h2>

        <label class="panel-title">লেবেলে ব্যবসার নাম (খালি হলে ব্র্যান্ড নাম)
            <input name="merchantName" value="{{ $s['merchantName'] }}" class="field mt-1.5">
        </label>
        <label class="panel-title">মার্চেন্ট আইডি
            <input name="merchantId" value="{{ $s['merchantId'] }}" class="field mt-1.5">
        </label>
    </section>

    {{-- ইন্টিগ্রেশন / সিক্রেট --}}
    <section class="panel grid gap-4 p-5 md:grid-cols-2">
        <div class="md:col-span-2">
            <h2 class="panel-title">ইন্টিগ্রেশন</h2>
            <p class="mt-2 text-xs text-[color:var(--color-muted)]">
                🔒 সিক্রেট টোকেন এনক্রিপ্টেড হয়ে সেভ হয় — খালি রাখলে আগেরটাই থাকবে।
                ⚠️ APP_KEY বদলালে/হারালে টোকেন ডিক্রিপ্ট হবে না, আবার বসাতে হবে।
            </p>
        </div>

        @php
            $secretFields = [
                'telegramBotToken'   => 'Telegram Bot Token',
                'steadfastApiKey'    => 'Steadfast API Key',
                'steadfastSecretKey' => 'Steadfast Secret Key',
                'capiAccessToken'    => 'Meta CAPI Access Token',
            ];
        @endphp

        <label class="panel-title">Telegram Chat ID
            <input name="telegramChatId" value="{{ $s['telegramChatId'] }}" class="field mt-1.5">
        </label>
        <label class="panel-title">Facebook Pixel ID
            <input name="facebookPixelId" value="{{ $s['facebookPixelId'] }}" class="field mt-1.5">
        </label>

        @foreach ($secretFields as $key => $label)
            <label class="panel-title">
                {{ $label }}
                @if ($s['secretsSet'][$key] ?? false)
                    <span class="chip ml-1 text-emerald-400">সেট আছে</span>
                @else
                    <span class="chip ml-1 text-[color:var(--color-muted)]">সেট নেই</span>
                @endif
                <input name="{{ $key }}" type="password" autocomplete="new-password"
                       placeholder="{{ ($s['secretsSet'][$key] ?? false) ? 'বদলাতে চাইলে নতুন মান দিন' : 'টোকেন বসান' }}"
                       class="field mt-1.5">
            </label>
        @endforeach

        <label class="panel-title md:col-span-2">Meta Test Event Code (যাচাইয়ের সময়, পরে খালি করুন)
            <input name="capiTestCode" value="{{ $s['capiTestCode'] }}" class="field mt-1.5">
        </label>
    </section>

    <button type="submit" class="btn-gold px-7 py-2.5 text-sm">সেটিংস সেভ করুন</button>
</form>

{{-- পাসওয়ার্ড বদল (আলাদা ফর্ম) --}}
<section class="panel mt-7 max-w-4xl p-5">
    <h2 class="panel-title">অ্যাডমিন পাসওয়ার্ড</h2>
    <p class="mt-2 text-xs {{ $passwordIsCustom ? 'text-[color:var(--color-muted)]' : 'text-amber-400' }}">
        @if ($passwordIsCustom)
            কাস্টম পাসওয়ার্ড সেট করা আছে।
        @else
            ⚠️ এখনো .env এর ডিফল্ট পাসওয়ার্ড ব্যবহার হচ্ছে — এখনই বদলে নিন।
        @endif
    </p>

    <form method="POST" action="{{ route('admin.password') }}" class="mt-4 grid gap-3 md:max-w-lg md:grid-cols-2">
        @csrf
        <input name="current" type="password" required placeholder="বর্তমান পাসওয়ার্ড" class="field">
        <input name="next" type="password" required minlength="4" placeholder="নতুন পাসওয়ার্ড (৪+ অক্ষর)" class="field">
        <button type="submit" class="btn-outline px-7 py-2.5 text-sm md:col-span-2 md:justify-self-start">
            পাসওয়ার্ড বদলান
        </button>
    </form>
</section>
@endsection
