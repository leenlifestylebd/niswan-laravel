@extends('layouts.admin')

@section('title', 'সেটিংস')

@section('content')
@php $s = $settings; @endphp

<h1 class="mb-4 text-xl font-bold text-ink">সেটিংস</h1>

<form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6">
    @csrf

    {{-- ব্র্যান্ড --}}
    <section class="grid gap-4 rounded-2xl border border-gray-200 bg-white p-5 md:grid-cols-2">
        <h2 class="text-sm font-bold text-ink md:col-span-2">ব্র্যান্ড</h2>

        <label class="text-sm font-semibold text-gray-700">ব্র্যান্ডের নাম
            <input name="brandName" value="{{ $s['brandName'] }}" class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm font-normal outline-none focus:border-brand">
        </label>
        <label class="text-sm font-semibold text-gray-700">ট্যাগলাইন
            <input name="tagline" value="{{ $s['tagline'] }}" class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm font-normal outline-none focus:border-brand">
        </label>
        <label class="text-sm font-semibold text-gray-700 md:col-span-2">বর্ণনা
            <textarea name="description" rows="2" class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm font-normal outline-none focus:border-brand">{{ $s['description'] }}</textarea>
        </label>
        <label class="text-sm font-semibold text-gray-700">সাইট URL
            <input name="url" value="{{ $s['url'] }}" class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm font-normal outline-none focus:border-brand">
        </label>
        <label class="text-sm font-semibold text-gray-700">ফুটার টেক্সট
            <input name="footerText" value="{{ $s['footerText'] }}" class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm font-normal outline-none focus:border-brand">
        </label>
        <label class="text-sm font-semibold text-gray-700">লোগো URL
            <input name="logoUrl" value="{{ $s['logoUrl'] }}" class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm font-normal outline-none focus:border-brand">
        </label>
        <label class="text-sm font-semibold text-gray-700">লোগোর উচ্চতা (px)
            <input name="logoHeight" type="number" min="16" value="{{ $s['logoHeight'] }}" class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm font-normal outline-none focus:border-brand">
        </label>
        <label class="text-sm font-semibold text-gray-700 md:col-span-2">ফেভিকন URL
            <input name="faviconUrl" value="{{ $s['faviconUrl'] }}" class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm font-normal outline-none focus:border-brand">
        </label>
    </section>

    {{-- যোগাযোগ --}}
    <section class="grid gap-4 rounded-2xl border border-gray-200 bg-white p-5 md:grid-cols-2">
        <h2 class="text-sm font-bold text-ink md:col-span-2">যোগাযোগ</h2>

        <label class="text-sm font-semibold text-gray-700">ফোন
            <input name="phone" value="{{ $s['phone'] }}" class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm font-normal outline-none focus:border-brand">
        </label>
        <label class="text-sm font-semibold text-gray-700">WhatsApp (কান্ট্রি কোড সহ, + ছাড়া)
            <input name="whatsapp" value="{{ $s['whatsapp'] }}" placeholder="8801XXXXXXXXX" class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm font-normal outline-none focus:border-brand">
        </label>
        <label class="text-sm font-semibold text-gray-700">Facebook
            <input name="facebook" value="{{ $s['facebook'] }}" class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm font-normal outline-none focus:border-brand">
        </label>
        <label class="text-sm font-semibold text-gray-700">Instagram
            <input name="instagram" value="{{ $s['instagram'] }}" class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm font-normal outline-none focus:border-brand">
        </label>
    </section>

    {{-- ডেলিভারি ও রঙ --}}
    <section class="grid gap-4 rounded-2xl border border-gray-200 bg-white p-5 md:grid-cols-2">
        <h2 class="text-sm font-bold text-ink md:col-span-2">ডেলিভারি ও থিম</h2>

        <label class="text-sm font-semibold text-gray-700">ঢাকার ভিতরে চার্জ (৳)
            <input name="deliveryInside" type="number" min="0" value="{{ $s['delivery']['insideDhaka'] }}" class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm font-normal outline-none focus:border-brand">
        </label>
        <label class="text-sm font-semibold text-gray-700">ঢাকার বাইরে চার্জ (৳)
            <input name="deliveryOutside" type="number" min="0" value="{{ $s['delivery']['outsideDhaka'] }}" class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm font-normal outline-none focus:border-brand">
        </label>

        <label class="text-sm font-semibold text-gray-700">প্রাইমারি রঙ
            <input name="colorPrimary" type="color" value="{{ $s['colorPrimary'] }}" class="mt-1 h-10 w-full rounded-xl border border-gray-200 px-1">
        </label>
        <label class="text-sm font-semibold text-gray-700">সেকেন্ডারি রঙ (hover/গাঢ়)
            <input name="colorSecondary" type="color" value="{{ $s['colorSecondary'] }}" class="mt-1 h-10 w-full rounded-xl border border-gray-200 px-1">
        </label>
        <label class="text-sm font-semibold text-gray-700">গ্লোবাল রঙ (টেক্সট/ফুটার)
            <input name="colorGlobal" type="color" value="{{ $s['colorGlobal'] }}" class="mt-1 h-10 w-full rounded-xl border border-gray-200 px-1">
        </label>
    </section>

    {{-- কুরিয়ার লেবেল --}}
    <section class="grid gap-4 rounded-2xl border border-gray-200 bg-white p-5 md:grid-cols-2">
        <h2 class="text-sm font-bold text-ink md:col-span-2">কুরিয়ার লেবেল</h2>

        <label class="text-sm font-semibold text-gray-700">লেবেলে ব্যবসার নাম (খালি হলে ব্র্যান্ড নাম)
            <input name="merchantName" value="{{ $s['merchantName'] }}" class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm font-normal outline-none focus:border-brand">
        </label>
        <label class="text-sm font-semibold text-gray-700">মার্চেন্ট আইডি
            <input name="merchantId" value="{{ $s['merchantId'] }}" class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm font-normal outline-none focus:border-brand">
        </label>
    </section>

    {{-- ইন্টিগ্রেশন / সিক্রেট --}}
    <section class="grid gap-4 rounded-2xl border border-gray-200 bg-white p-5 md:grid-cols-2">
        <div class="md:col-span-2">
            <h2 class="text-sm font-bold text-ink">ইন্টিগ্রেশন</h2>
            <p class="mt-1 text-xs text-gray-500">
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

        <label class="text-sm font-semibold text-gray-700">Telegram Chat ID
            <input name="telegramChatId" value="{{ $s['telegramChatId'] }}" class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm font-normal outline-none focus:border-brand">
        </label>
        <label class="text-sm font-semibold text-gray-700">Facebook Pixel ID
            <input name="facebookPixelId" value="{{ $s['facebookPixelId'] }}" class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm font-normal outline-none focus:border-brand">
        </label>

        @foreach ($secretFields as $key => $label)
            <label class="text-sm font-semibold text-gray-700">
                {{ $label }}
                @if ($s['secretsSet'][$key] ?? false)
                    <span class="ml-1 rounded-full bg-green-100 px-2 py-0.5 text-[10px] font-semibold text-green-800">সেট আছে</span>
                @else
                    <span class="ml-1 rounded-full bg-gray-100 px-2 py-0.5 text-[10px] font-semibold text-gray-600">সেট নেই</span>
                @endif
                <input name="{{ $key }}" type="password" autocomplete="new-password" placeholder="{{ ($s['secretsSet'][$key] ?? false) ? 'বদলাতে চাইলে নতুন মান দিন' : 'টোকেন বসান' }}"
                       class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm font-normal outline-none focus:border-brand">
            </label>
        @endforeach

        <label class="text-sm font-semibold text-gray-700 md:col-span-2">Meta Test Event Code (যাচাইয়ের সময়, পরে খালি করুন)
            <input name="capiTestCode" value="{{ $s['capiTestCode'] }}" class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm font-normal outline-none focus:border-brand">
        </label>
    </section>

    <button type="submit" class="rounded-full bg-brand px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-brand-dark">
        সেটিংস সেভ করুন
    </button>
</form>

{{-- পাসওয়ার্ড বদল (আলাদা ফর্ম) --}}
<section class="mt-8 rounded-2xl border border-gray-200 bg-white p-5">
    <h2 class="text-sm font-bold text-ink">অ্যাডমিন পাসওয়ার্ড</h2>
    <p class="mt-1 text-xs text-gray-500">
        @if ($passwordIsCustom)
            কাস্টম পাসওয়ার্ড সেট করা আছে।
        @else
            ⚠️ এখনো .env এর ডিফল্ট পাসওয়ার্ড ব্যবহার হচ্ছে — এখনই বদলে নিন।
        @endif
    </p>

    <form method="POST" action="{{ route('admin.password') }}" class="mt-3 grid gap-3 md:max-w-lg md:grid-cols-2">
        @csrf
        <input name="current" type="password" required placeholder="বর্তমান পাসওয়ার্ড"
               class="rounded-xl border border-gray-200 px-3 py-2 text-sm outline-none focus:border-brand">
        <input name="next" type="password" required minlength="4" placeholder="নতুন পাসওয়ার্ড (৪+ অক্ষর)"
               class="rounded-xl border border-gray-200 px-3 py-2 text-sm outline-none focus:border-brand">
        <button type="submit" class="rounded-full bg-ink px-6 py-2.5 text-sm font-semibold text-white transition hover:opacity-90 md:col-span-2 md:justify-self-start">
            পাসওয়ার্ড বদলান
        </button>
    </form>
</section>
@endsection
