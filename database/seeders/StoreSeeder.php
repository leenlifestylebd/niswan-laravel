<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Review;
use Illuminate\Database\Seeder;

// টেমপ্লেটের ডেমো প্রোডাক্ট + রিভিউ — টেবিল খালি থাকলেই বসে।
// (Next.js এ ensureProductsSchema/ensureReviewsSchema যা করত)
class StoreSeeder extends Seeder
{
    public function run(): void
    {
        if (Product::count() === 0) {
            $products = json_decode(file_get_contents(__DIR__.'/data-products.json'), true) ?: [];

            foreach ($products as $i => $p) {
                Product::create([
                    'slug'       => $p['slug'],
                    'name'       => $p['name'],
                    'price'      => (int) $p['price'],
                    'old_price'  => isset($p['oldPrice']) ? (int) $p['oldPrice'] : null,
                    'badge'      => $p['badge'] ?? '',
                    'short'      => $p['short'] ?? '',
                    'image'      => $p['image'] ?? ($p['images'][0] ?? null),
                    'images'     => $p['images'] ?? [],
                    'sizes'      => $p['sizes'] ?? [],
                    'variants'   => $p['variants'] ?? [],
                    'features'   => $p['features'] ?? [],
                    'sort_order' => $i,
                    'active'     => true,
                ]);
            }
        }

        if (Review::count() === 0) {
            $images = json_decode(file_get_contents(__DIR__.'/data-feedback.json'), true) ?: [];

            foreach ($images as $i => $url) {
                Review::create(['image_url' => $url, 'sort_order' => $i]);
            }
        }
    }
}
