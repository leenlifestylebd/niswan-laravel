<?php

namespace App\Http\Controllers;

// সাইট এখন সিঙ্গেল-পেজ — পুরোনো /product/{slug} লিংক ভাঙে না, হোমে পাঠায়।
class ProductController extends Controller
{
    public function show(string $slug)
    {
        return redirect()->route('home', [], 301);
    }
}
