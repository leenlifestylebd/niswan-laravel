<?php

namespace App\Http\Controllers;

use App\Services\SettingsService;

// PWA manifest — কাস্টমার সাইট ও অ্যাডমিন প্যানেলের জন্য আলাদা
class ManifestController extends Controller
{
    public function customer(SettingsService $settings)
    {
        $s = $settings->public();

        return response()->json([
            'id'               => '/',
            'name'             => $s['brandName'],
            'short_name'       => $s['brandName'],
            'description'      => $s['description'],
            'start_url'        => '/',
            'scope'            => '/',
            'display'          => 'standalone',
            'orientation'      => 'portrait',
            'background_color' => '#ffffff',
            'theme_color'      => $s['colorPrimary'] ?: '#e23a7a',
            'icons'            => $this->icons(),
        ], 200, ['Content-Type' => 'application/manifest+json'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    public function admin(SettingsService $settings)
    {
        $s = $settings->public();

        return response()->json([
            'id'               => '/admin',
            'name'             => $s['brandName'].' অ্যাডমিন',
            'short_name'       => 'অ্যাডমিন',
            'description'      => $s['brandName'].' — অর্ডার ও প্রোডাক্ট ম্যানেজমেন্ট',
            'start_url'        => '/admin',
            'scope'            => '/admin',
            'display'          => 'standalone',
            'orientation'      => 'portrait',
            'background_color' => '#ffffff',
            'theme_color'      => $s['colorGlobal'] ?: '#111111',
            'icons'            => $this->icons(),
        ], 200, ['Content-Type' => 'application/manifest+json'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    private function icons(): array
    {
        return [
            ['src' => '/icon-192.png', 'sizes' => '192x192', 'type' => 'image/png', 'purpose' => 'any'],
            ['src' => '/icon-512.png', 'sizes' => '512x512', 'type' => 'image/png', 'purpose' => 'any'],
            ['src' => '/icon-512.png', 'sizes' => '512x512', 'type' => 'image/png', 'purpose' => 'maskable'],
        ];
    }
}
