<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminAuthService;
use App\Services\SettingsService;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    /** যেসব কী অ্যাডমিন ফর্ম থেকে সেভ হয় */
    private const FIELDS = [
        'brandName', 'tagline', 'description', 'url',
        'logoUrl', 'logoHeight', 'faviconUrl', 'footerText',
        'phone', 'whatsapp', 'facebook', 'instagram',
        'deliveryInside', 'deliveryOutside',
        'colorPrimary', 'colorSecondary', 'colorGlobal',
        'facebookPixelId', 'capiTestCode',
        'merchantName', 'merchantId',
        'telegramChatId',
        // সিক্রেট — খালি রাখলে আগেরটাই থাকে
        'telegramBotToken', 'steadfastApiKey', 'steadfastSecretKey', 'capiAccessToken',
    ];

    public function edit(SettingsService $settings, AdminAuthService $auth)
    {
        return view('admin.settings', [
            'settings'        => $settings->forAdmin(),
            'passwordIsCustom' => $auth->isCustom(),
        ]);
    }

    public function update(Request $request, SettingsService $settings)
    {
        $data = [];

        foreach (self::FIELDS as $key) {
            if ($request->has($key)) {
                $data[$key] = (string) $request->input($key, '');
            }
        }

        $settings->update($data);

        return back()->with('status', 'সেটিংস সেভ হয়েছে');
    }
}
