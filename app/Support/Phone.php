<?php

namespace App\Support;

// বাংলাদেশি মোবাইল নাম্বার — ১১ ডিজিট, 01 দিয়ে শুরু (01XXXXXXXXX)।
// +880 / 880 প্রিফিক্স থাকলেও ঠিক ধরা হয়।
class Phone
{
    /** ডিজিট বের করে শুরুর 88 বাদ দেয় */
    public static function normalize(?string $p): string
    {
        $d = preg_replace('/\D/', '', (string) $p);

        return preg_replace('/^88/', '', $d);
    }

    public static function isValid(?string $p): bool
    {
        return (bool) preg_match('/^01\d{9}$/', self::normalize($p));
    }

    /** লোকাল নাম্বার → +880 আন্তর্জাতিক (Telegram অটো ট্যাপেবল করে) */
    public static function intl(?string $p): string
    {
        $d = preg_replace('/\D/', '', (string) $p);

        if ($d === '') {
            return '';
        }
        if (str_starts_with($d, '880')) {
            return '+'.$d;
        }
        if (str_starts_with($d, '0')) {
            return '+880'.substr($d, 1);
        }

        return '+'.$d;
    }

    /** Meta CAPI হ্যাশের জন্য: 880 কান্ট্রি কোড সহ, + ছাড়া */
    public static function e164Digits(?string $p): string
    {
        $d = preg_replace('/\D/', '', (string) $p);

        if ($d === '') {
            return '';
        }
        if (str_starts_with($d, '0')) {
            return '880'.substr($d, 1);
        }
        if (! str_starts_with($d, '880')) {
            return '880'.$d;
        }

        return $d;
    }
}
