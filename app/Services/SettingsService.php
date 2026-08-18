<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

// সেটিংস key/value স্টোর। সিক্রেটগুলো Laravel Crypt দিয়ে এনক্রিপ্টেড থাকে।
// ⚠️ APP_KEY হারালে সিক্রেট ডিক্রিপ্ট হবে না — প্রতি ক্লায়েন্টে আলাদা রাখতে হবে।
class SettingsService
{
    /** কোন কী গুলো এনক্রিপ্ট করে রাখা হবে */
    public const SECRET_KEYS = [
        'telegramBotToken',
        'steadfastApiKey',
        'steadfastSecretKey',
        'capiAccessToken',
    ];

    /** সিক্রেট কী → env fallback নাম */
    public const ENV_FALLBACK = [
        'telegramBotToken'   => 'TELEGRAM_BOT_TOKEN',
        'telegramChatId'     => 'TELEGRAM_CHAT_ID',
        'steadfastApiKey'    => 'STEADFAST_API_KEY',
        'steadfastSecretKey' => 'STEADFAST_SECRET_KEY',
        'capiAccessToken'    => 'FB_CAPI_TOKEN',
    ];

    /** @var array<string,string>|null per-request cache */
    private ?array $map = null;

    /** পাবলিক (ব্র্যান্ড) ফিল্ডের ডিফল্ট — config/store.php থেকে */
    private function defaults(): array
    {
        $site = config('store.site');

        return [
            'brandName'       => $site['brand_name'],
            'tagline'         => $site['tagline'],
            'description'     => $site['description'],
            'url'             => $site['url'],
            'logoUrl'         => '',
            'logoHeight'      => '40',   // লোগোর উচ্চতা px
            'faviconUrl'      => '',     // ব্রাউজার ট্যাব আইকন
            'footerText'      => $site['tagline'],
            'phone'           => $site['phone'],
            'whatsapp'        => $site['whatsapp'],
            'facebook'        => $site['facebook'],
            'instagram'       => $site['instagram'],
            'deliveryInside'  => (string) $site['delivery']['inside_dhaka'],
            'deliveryOutside' => (string) $site['delivery']['outside_dhaka'],
            'telegramChatId'  => '',
            'facebookPixelId' => '',
            'capiTestCode'    => '',     // Meta Test Events কোড
            // কুরিয়ার লেবেল
            'merchantName'    => '',
            'merchantId'      => '',
            // থিম রঙ
            'colorPrimary'    => '#5a1436',
            'colorSecondary'  => '#7a2349',
            'colorGlobal'     => '#2c1a22',
        ];
    }

    private function map(): array
    {
        if ($this->map === null) {
            try {
                $this->map = Setting::pluck('value', 'key')->all();
            } catch (\Throwable $e) {
                $this->map = [];
            }
        }

        return $this->map;
    }

    public function forget(): void
    {
        $this->map = null;
    }

    /** পাবলিক সেটিংস (সিক্রেট ছাড়া) — লেআউট/পেজে ব্যবহারযোগ্য */
    public function public(): array
    {
        $d   = $this->defaults();
        $map = $this->map();
        $get = fn (string $k) => (isset($map[$k]) && $map[$k] !== '' && $map[$k] !== null) ? $map[$k] : $d[$k];

        return [
            'brandName'   => $get('brandName'),
            'tagline'     => $get('tagline'),
            'description' => $get('description'),
            'url'         => $get('url'),
            'logoUrl'     => $map['logoUrl'] ?? '',
            'logoHeight'  => (int) ($map['logoHeight'] ?? 0) ?: 40,
            'faviconUrl'  => $map['faviconUrl'] ?? '',
            'footerText'  => $get('footerText'),
            'phone'       => $get('phone'),
            'whatsapp'    => $get('whatsapp'),
            'facebook'    => $get('facebook'),
            'instagram'   => $get('instagram'),
            'keywords'    => config('store.site.keywords'),
            'delivery'    => [
                'insideDhaka'  => (int) $get('deliveryInside'),
                'outsideDhaka' => (int) $get('deliveryOutside'),
            ],
            'colorPrimary'    => $get('colorPrimary'),
            'colorSecondary'  => $get('colorSecondary'),
            'colorGlobal'     => $get('colorGlobal'),
            'facebookPixelId' => $map['facebookPixelId'] ?? '',
            'capiTestCode'    => $map['capiTestCode'] ?? '',
            'merchantName'    => $map['merchantName'] ?? '',
            'merchantId'      => $map['merchantId'] ?? '',
        ];
    }

    /** একটি ইন্টিগ্রেশন সিক্রেট (ডিক্রিপ্ট করে), না থাকলে env fallback */
    public function secret(string $name): string
    {
        $map = $this->map();
        $raw = $map[$name] ?? '';

        if ($raw !== '' && $raw !== null) {
            if (in_array($name, self::SECRET_KEYS, true)) {
                try {
                    return Crypt::decryptString($raw);
                } catch (\Throwable $e) {
                    Log::warning("সেটিংস ডিক্রিপ্ট ব্যর্থ ({$name}) — APP_KEY বদলে গেছে?");
                    return '';
                }
            }
            return (string) $raw;
        }

        $env = self::ENV_FALLBACK[$name] ?? null;

        return $env ? (string) env($env, '') : '';
    }

    /** অ্যাডমিনের জন্য: পাবলিক মান + সিক্রেট সেট আছে কিনা (মান নয়) */
    public function forAdmin(): array
    {
        $map   = $this->map();
        $isSet = fn (string $k) => (bool) ($map[$k] ?? '') || (bool) env(self::ENV_FALLBACK[$k] ?? '', '');

        return array_merge($this->public(), [
            'telegramChatId' => $map['telegramChatId'] ?? '',
            'secretsSet'     => [
                'telegramBotToken'   => $isSet('telegramBotToken'),
                'steadfastApiKey'    => $isSet('steadfastApiKey'),
                'steadfastSecretKey' => $isSet('steadfastSecretKey'),
                'capiAccessToken'    => $isSet('capiAccessToken'),
            ],
        ]);
    }

    /** সেটিংস আপডেট — সিক্রেট খালি হলে আগেরটা অপরিবর্তিত থাকে */
    public function update(array $data): void
    {
        foreach ($data as $key => $raw) {
            $val = $raw === null ? '' : (string) $raw;

            if (in_array($key, self::SECRET_KEYS, true)) {
                if ($val === '') {
                    continue; // খালি = অপরিবর্তিত
                }
                $val = Crypt::encryptString($val);
            }

            Setting::updateOrCreate(['key' => $key], ['value' => $val]);
        }

        $this->forget();
    }

    public function put(string $key, string $value): void
    {
        Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        $this->forget();
    }

    public function raw(string $key): ?string
    {
        return $this->map()[$key] ?? null;
    }
}
