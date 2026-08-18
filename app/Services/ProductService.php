<?php

namespace App\Services;

use App\Models\Product;

class ProductService
{
    /** নাম → URL-বান্ধব slug (latin + বাংলা অক্ষর রাখে, বাকি সব `-`) */
    public function slugify(?string $s): string
    {
        $s = mb_strtolower(trim((string) $s));
        $s = preg_replace('/[^a-z0-9\x{0980}-\x{09FF}]+/u', '-', $s);

        return trim((string) $s, '-');
    }

    /**
     * ইউনিক slug — খালি হলে নাম থেকে, তাও খালি হলে "product";
     * আগে থেকে থাকলে -2, -3 ... যোগ করে। $excludeId = নিজের row বাদ (edit-এ)।
     */
    public function uniqueSlug(?string $desired, ?string $name, ?int $excludeId = null): string
    {
        $base = $this->slugify($desired) ?: ($this->slugify($name) ?: 'product');

        $taken = Product::query()
            ->where(fn ($q) => $q->where('slug', $base)->orWhere('slug', 'like', $base.'-%'))
            ->when($excludeId, fn ($q) => $q->where('id', '<>', $excludeId))
            ->pluck('slug')
            ->flip();

        if (! $taken->has($base)) {
            return $base;
        }

        $n = 2;
        while ($taken->has($base.'-'.$n)) {
            $n++;
        }

        return $base.'-'.$n;
    }

    /** ফর্মের কাঁচা ইনপুট → DB কলাম */
    public function clean(array $data, ?int $excludeId = null): array
    {
        $arr = function ($v) {
            if (is_array($v)) {
                return array_values(array_filter($v, fn ($x) => $x !== null && $x !== ''));
            }
            // textarea — প্রতি লাইনে একটা আইটেম
            if (is_string($v) && trim($v) !== '') {
                return array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $v))));
            }

            return [];
        };

        $images   = $arr($data['images'] ?? []);
        $variants = $this->cleanVariants($data['variants'] ?? [], (int) ($data['price'] ?? 0));

        return [
            'slug'       => $this->uniqueSlug($data['slug'] ?? null, $data['name'] ?? null, $excludeId),
            'name'       => trim((string) ($data['name'] ?? '')),
            'price'      => (int) ($data['price'] ?? 0),
            'old_price'  => ! empty($data['old_price']) ? (int) $data['old_price'] : null,
            'badge'      => (string) ($data['badge'] ?? ''),
            'short'      => (string) ($data['short'] ?? ''),
            'image'      => $data['image'] ?: ($images[0] ?? null),
            'images'     => $images,
            'sizes'      => $arr($data['sizes'] ?? []),
            'variants'   => $variants,
            'features'   => $arr($data['features'] ?? []),
            'sort_order' => (int) ($data['sort_order'] ?? 0),
            'active'     => filter_var($data['active'] ?? true, FILTER_VALIDATE_BOOL),
        ];
    }

    /** variants: [{name, price}] — নাম খালি হলে বাদ */
    private function cleanVariants($raw, int $fallbackPrice): array
    {
        $out = [];

        foreach ((array) $raw as $v) {
            $name  = trim((string) ($v['name'] ?? ''));
            $price = (int) ($v['price'] ?? 0);

            if ($name === '') {
                continue;
            }

            $out[] = ['name' => $name, 'price' => $price ?: $fallbackPrice];
        }

        return $out;
    }
}
