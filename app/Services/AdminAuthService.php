<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Hash;

// অ্যাডমিন পাসওয়ার্ড যাচাই/পরিবর্তন।
// হ্যাশ settings টেবিলে 'adminPasswordHash' কী-তে (bcrypt), env-নিরপেক্ষ।
class AdminAuthService
{
    public const SESSION_KEY = 'store_admin';

    private function storedHash(): ?string
    {
        try {
            return Setting::where('key', 'adminPasswordHash')->value('value') ?: null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function verify(?string $input): bool
    {
        if (! $input) {
            return false;
        }

        $hash = $this->storedHash();

        if ($hash) {
            return Hash::check($input, $hash);
        }

        // DB-তে সেট না থাকলে .env fallback (প্রথম লগইনের জন্য)।
        // ⚠️ env() নয় — config:cache করা থাকলে env() null দেয়, তখন লগইনই আটকে যায়।
        $envPassword = (string) config('store.admin_password', '');

        return $envPassword !== '' && hash_equals($envPassword, $input);
    }

    public function setPassword(string $next): void
    {
        Setting::updateOrCreate(
            ['key' => 'adminPasswordHash'],
            ['value' => Hash::make($next)]
        );
    }

    /** কাস্টম পাসওয়ার্ড সেট করা আছে কি না (env ডিফল্ট নয়) */
    public function isCustom(): bool
    {
        return (bool) $this->storedHash();
    }
}
