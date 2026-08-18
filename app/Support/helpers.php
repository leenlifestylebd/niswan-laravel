<?php

use App\Services\SettingsService;

if (! function_exists('bdt')) {
    /** টাকার অঙ্ক বাংলা সংখ্যায় — ৳২,৭০০ */
    function bdt($n): string
    {
        $n = (int) $n;

        if (class_exists(\NumberFormatter::class)) {
            $f = new \NumberFormatter('bn_BD', \NumberFormatter::DECIMAL);
            return '৳'.$f->format($n);
        }

        return '৳'.strtr(number_format($n), ['0' => '০', '1' => '১', '2' => '২', '3' => '৩', '4' => '৪', '5' => '৫', '6' => '৬', '7' => '৭', '8' => '৮', '9' => '৯']);
    }
}

if (! function_exists('bn_num')) {
    /** সাধারণ সংখ্যা বাংলা অঙ্কে (গ্রুপিং ছাড়া) */
    function bn_num($n): string
    {
        return strtr((string) $n, ['0' => '০', '1' => '১', '2' => '২', '3' => '৩', '4' => '৪', '5' => '৫', '6' => '৬', '7' => '৭', '8' => '৮', '9' => '৯']);
    }
}

if (! function_exists('lighten')) {
    /** hex কে সাদার দিকে মিশিয়ে হালকা টিন্ট (brand-light) বানায় */
    function lighten(?string $hex, float $amt = 0.88): string
    {
        $h = ltrim((string) $hex, '#');

        if (strlen($h) !== 6 || ! ctype_xdigit($h)) {
            return '#fce7f1';
        }

        $mix = fn (int $c) => (int) round($c + (255 - $c) * $amt);

        return sprintf(
            'rgb(%d, %d, %d)',
            $mix(hexdec(substr($h, 0, 2))),
            $mix(hexdec(substr($h, 2, 2))),
            $mix(hexdec(substr($h, 4, 2)))
        );
    }
}

if (! function_exists('store_settings')) {
    /** পাবলিক সেটিংস (per-request cached) */
    function store_settings(): array
    {
        return app(SettingsService::class)->public();
    }
}
