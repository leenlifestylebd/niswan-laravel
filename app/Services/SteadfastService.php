<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use RuntimeException;

// Steadfast Courier API — এক ক্লিকে consignment তৈরি।
// API key/secret অ্যাডমিন সেটিংস (DB) থেকে, না থাকলে env থেকে।
class SteadfastService
{
    private const BASE = 'https://portal.packzy.com/api/v1';

    public function __construct(private SettingsService $settings) {}

    public function keys(): array
    {
        return [
            'apiKey'    => $this->settings->secret('steadfastApiKey'),
            'secretKey' => $this->settings->secret('steadfastSecretKey'),
        ];
    }

    public function ready(): bool
    {
        ['apiKey' => $a, 'secretKey' => $s] = $this->keys();

        return (bool) ($a && $s);
    }

    /** @return array{consignment_id:mixed, tracking_code:mixed} */
    public function createConsignment(Order $order): array
    {
        ['apiKey' => $apiKey, 'secretKey' => $secretKey] = $this->keys();

        if (! $apiKey || ! $secretKey) {
            throw new RuntimeException('Steadfast API key সেট করা নেই');
        }

        $res = Http::timeout(20)
            ->withHeaders([
                'Api-Key'    => $apiKey,
                'Secret-Key' => $secretKey,
            ])
            ->asJson()
            ->post(self::BASE.'/create_order', [
                'invoice'           => 'ORD-'.$order->id,
                'recipient_name'    => $order->name,
                'recipient_phone'   => $order->phone,
                'recipient_address' => $order->address,
                'cod_amount'        => (int) $order->total,
                'note'              => sprintf('%s | সাইজ: %s | পরিমাণ: %s', $order->product, $order->size, $order->qty),
            ]);

        $data = $res->json() ?: [];

        if (! $res->successful() || (int) ($data['status'] ?? 0) !== 200) {
            throw new RuntimeException($data['message'] ?? 'Steadfast অর্ডার তৈরি ব্যর্থ');
        }

        return $data['consignment'] ?? [];
    }
}
