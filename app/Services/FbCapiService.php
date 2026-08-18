<?php

namespace App\Services;

use App\Models\Order;
use App\Support\Phone;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

// Meta Conversions API (CAPI) — server থেকে সরাসরি Meta-তে event পাঠায়।
// Pixel ID + Access Token (অ্যাডমিন সেটিংস) সেট থাকলেই কাজ করে, নাহলে চুপচাপ স্কিপ।
// browser pixel-এর সাথে একই event_id ব্যবহার করায় Meta ডাবল count করে না।
class FbCapiService
{
    private const VERSION = 'v21.0';

    public function __construct(private SettingsService $settings) {}

    /** Meta-র জন্য PII অবশ্যই SHA-256 (lowercase, trimmed) করে পাঠাতে হয় */
    private function sha256(?string $v): ?string
    {
        $s = mb_strtolower(trim((string) $v));

        return $s === '' ? null : hash('sha256', $s);
    }

    private function hashPhone(?string $p): ?string
    {
        $d = Phone::e164Digits($p);

        return $d === '' ? null : hash('sha256', $d);
    }

    /** null/খালি ভ্যালু বাদ দেয় (Meta খালি ফিল্ড পছন্দ করে না) */
    private function clean(array $arr): array
    {
        return array_filter($arr, fn ($v) => $v !== null && $v !== '' && $v !== []);
    }

    public function sendPurchase(
        Order $order,
        ?string $eventId,
        ?string $clientIp,
        ?string $userAgent,
        ?string $fbp = null,
        ?string $fbc = null,
        ?string $sourceUrl = null,
    ): bool {
        $token   = $this->settings->secret('capiAccessToken');
        $site    = $this->settings->public();
        $pixelId = $site['facebookPixelId'];

        if (! $token || ! $pixelId) {
            return false; // সেট করা নেই → স্কিপ
        }

        $userData = $this->clean([
            'ph'                => $this->hashPhone($order->phone),
            'fn'                => $this->sha256($order->name),
            'client_ip_address' => $clientIp,
            'client_user_agent' => $userAgent,
            'fbp'               => $fbp,
            'fbc'               => $fbc,
        ]);

        $customData = $this->clean([
            'currency'     => 'BDT',
            'value'        => (int) $order->total,
            'content_name' => $order->product,
            'content_ids'  => $order->slug ? [$order->slug] : null,
            'content_type' => 'product',
            'num_items'    => (int) ($order->qty ?: 1),
        ]);

        $event = $this->clean([
            'event_name'       => 'Purchase',
            'event_time'       => time(),
            'event_id'         => $eventId, // ← browser pixel-এর সাথে মিলে দ্বৈত গণনা ঠেকায়
            'action_source'    => 'website',
            'event_source_url' => $sourceUrl ?: ($site['url'] ?: null),
            'user_data'        => $userData,
            'custom_data'      => $customData,
        ]);

        $body = ['data' => [$event]];

        if ($site['capiTestCode']) {
            $body['test_event_code'] = $site['capiTestCode'];
        }

        try {
            $url = sprintf(
                'https://graph.facebook.com/%s/%s/events?access_token=%s',
                self::VERSION,
                $pixelId,
                urlencode($token)
            );

            $res = Http::timeout(10)->asJson()->post($url, $body);

            if (! $res->successful()) {
                Log::error('CAPI ব্যর্থ: '.$res->status().' '.$res->body());

                return false;
            }

            return true;
        } catch (\Throwable $e) {
            Log::error('CAPI পাঠানো ব্যর্থ: '.$e->getMessage());

            return false;
        }
    }
}
