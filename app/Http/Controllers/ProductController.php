<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;

class ProductController extends Controller
{
    public function show(string $slug)
    {
        $product = Product::where('slug', $slug)->where('active', true)->firstOrFail();

        return view('product', [
            'product'      => $product,
            'reviewImages' => Review::ordered()->pluck('image_url')->all(),
        ]);
    }
}
