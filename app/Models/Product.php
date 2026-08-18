<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';

    // শুধু created_at আছে, updated_at নেই
    const UPDATED_AT = null;

    protected $guarded = [];

    protected $casts = [
        'images'     => 'array',
        'sizes'      => 'array',
        'variants'   => 'array',
        'features'   => 'array',
        'active'     => 'boolean',
        'price'      => 'integer',
        'old_price'  => 'integer',
        'sort_order' => 'integer',
        'created_at' => 'datetime',
    ];

    // মেইন ছবি না থাকলে গ্যালারির প্রথমটা
    public function mainImage(): ?string
    {
        return $this->image ?: (($this->images ?? [])[0] ?? null);
    }

    // ভ্যারিয়েন্ট খালি হলে ডিফল্ট "ফুল সেট" (Next.js rowToProduct এর মতো)
    public function variantList(): array
    {
        $v = $this->variants ?? [];
        return $v ?: [['name' => 'ফুল সেট', 'price' => $this->price]];
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
