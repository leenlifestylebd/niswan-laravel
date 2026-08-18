<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;

class HomeController extends Controller
{
    public function index()
    {
        return view('home', [
            'products'     => Product::active()->ordered()->get(),
            'reviewImages' => Review::ordered()->pluck('image_url')->all(),
        ]);
    }
}
