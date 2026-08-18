<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\FbCapiService;
use App\Services\TelegramService;
use App\Support\Phone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

// অর্ডার গ্রহণ → DB তে সেভ → Telegram নোটিফিকেশন → Meta CAPI
class OrderController extends Controller
{
    public function store(Request $request, TelegramService $telegram, FbCapiService $capi)
    {
        $data = $request->all();

        if (empty($data['name']) || empty($data['phone']) || empty($data['address'])) {
            return response()->json(['ok' => false, 'error' => 'missing fields'], 400);
        }

        // ফোন যাচাই — বাংলাদেশি ১১ ডিজিট (01XXXXXXXXX), +880/880 থাকলেও ঠিক
        if (! Phone::isValid($data['phone'])) {
            return response()->json(['ok' => false, 'error' => 'invalid phone'], 400);
        }

        $order = null;

        try {
            $order = Order::create([
                'name'            => (string) $data['name'],
                'phone'           => (string) $data['phone'],
                'address'         => (string) $data['address'],
                'product'         => $data['product'] ?? null,
                'slug'            => $data['slug'] ?? null,
                'size'            => $data['size'] ?? null,
                'color'           => $data['color'] ?? null,
                'qty'             => (int) ($data['qty'] ?? 1),
                'area'            => $data['area'] ?? null,
                'delivery_charge' => (int) ($data['deliveryCharge'] ?? 0),
                'total'           => (int) ($data['total'] ?? 0),
            ]);
        } catch (\Throwable $e) {
            Log::error('DB সেভ ব্যর্থ: '.$e->getMessage());

            return response()->json(['ok' => false, 'error' => 'save failed'], 500);
        }

        // Telegram নোটিফিকেশন
        try {
            $telegram->send($telegram->formatOrder($order));
        } catch (\Throwable $e) {
            Log::error('Telegram ব্যর্থ: '.$e->getMessage());
        }

        // Meta Conversions API — server-side Purchase (browser pixel-এর সাথে dedup)
        try {
            $capi->sendPurchase(
                order: $order,
                eventId: $data['eventId'] ?? null,
                clientIp: $request->ip(),
                userAgent: (string) $request->userAgent(),
                fbp: $request->cookie('_fbp'),
                fbc: $request->cookie('_fbc'),
                sourceUrl: (string) $request->header('referer'),
            );
        } catch (\Throwable $e) {
            Log::error('CAPI ব্যর্থ: '.$e->getMessage());
        }

        return response()->json(['ok' => true, 'id' => $order->id]);
    }
}
