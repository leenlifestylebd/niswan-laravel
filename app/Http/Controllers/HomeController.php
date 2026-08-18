<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use App\Services\SettingsService;

// সিঙ্গেল-পেজ ল্যান্ডিং — একটাই প্রোডাক্ট, সব সেকশন এক পেজে।
class HomeController extends Controller
{
    public function index(SettingsService $settings)
    {
        return view('landing', [
            'product'      => $this->landingProduct($settings),
            'reviewImages' => Review::ordered()->pluck('image_url')->all(),
        ]);
    }

    /**
     * অ্যাডমিন সেটিংসে বাছাই করা প্রোডাক্ট; না থাকলে প্রথম active প্রোডাক্ট।
     * (সাইটে একটাই প্রোডাক্ট দেখানো হয় — বাকিগুলো অ্যাডমিনে থাকে।)
     */
    public function landingProduct(SettingsService $settings): ?Product
    {
        $slug = $settings->public()['landingProductSlug'];

        if ($slug) {
            $chosen = Product::where('slug', $slug)->where('active', true)->first();

            if ($chosen) {
                return $chosen;
            }
        }

        return Product::active()->ordered()->first();
    }
}
