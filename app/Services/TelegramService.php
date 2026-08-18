<?php

namespace App\Services;

use App\Models\Order;
use App\Support\Phone;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

// Telegram bot এ মেসেজ পাঠায়।
// টোকেন/চ্যাট আইডি অ্যাডমিন সেটিংস (DB) থেকে, না থাকলে env থেকে।
class TelegramService
{
    public function __construct(private SettingsService $settings) {}

    public function send(string $text): bool
    {
        $token  = $this->settings->secret('telegramBotToken');
        $chatId = $this->settings->secret('telegramChatId');

        if (! $token || ! $chatId) {
            Log::info('Telegram সেট করা নেই — মেসেজ স্কিপ করা হলো');

            return false;
        }

        try {
            $res = Http::timeout(10)->post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id'    => $chatId,
                'text'       => $text,
                'parse_mode' => 'HTML',
            ]);

            return $res->successful();
        } catch (\Throwable $e) {
            Log::error('Telegram পাঠানো ব্যর্থ: '.$e->getMessage());

            return false;
        }
    }

    /** অর্ডার সামারি ফরম্যাট */
    public function formatOrder(Order $o): string
    {
        return implode("\n", [
            '🛍️ <b>নতুন অর্ডার #'.$o->id.'</b>',
            '',
            '📦 <b>'.e($o->product).'</b>',
            'সাইজ: '.e($o->size),
            'পরিমাণ: '.$o->qty,
            '',
            '👤 '.e($o->name),
            '📞 '.Phone::intl($o->phone),
            '📍 '.e($o->address),
            '',
            '🚚 '.e($o->area).' — '.bdt($o->delivery_charge),
            '💰 <b>সর্বমোট: '.bdt($o->total).'</b>',
        ]);
    }
}
