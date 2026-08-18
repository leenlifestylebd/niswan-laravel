<?php

// ====================================================================
//  STORE — ডেমো টেমপ্লেট ডিফল্ট সেটিংস।
//  এগুলো শুধু fallback; আসল মান অ্যাডমিন → Settings থেকে আসবে।
// ====================================================================

return [
    'site' => [
        'brand_name'  => env('STORE_BRAND', 'STORE'),
        'tagline'     => 'আপনার পছন্দের সেরা কালেকশন',
        'description' => 'প্রিমিয়াম মানের প্রোডাক্ট — দ্রুত হোম ডেলিভারি সারা বাংলাদেশে।',

        'url'      => env('APP_URL', 'https://store.example.com'),
        'keywords' => ['online shop', 'bangladesh', 'ecommerce', 'STORE'],

        'phone'     => '01XXXXXXXXX',
        'whatsapp'  => '8801XXXXXXXXX', // country code সহ, + ছাড়া
        'email'     => 'store@example.com',
        'facebook'  => 'https://facebook.com/',
        'instagram' => 'https://instagram.com/',

        'delivery' => [
            'inside_dhaka'  => 60,
            'outside_dhaka' => 120,
        ],
    ],

    // trust badges (হিরোর নিচের ৪টা আইকন সেকশন)
    'features' => [
        ['icon' => '✨', 'title' => 'প্রিমিয়াম কোয়ালিটি', 'desc' => 'উন্নত মানের প্রোডাক্ট'],
        ['icon' => '✅', 'title' => '১০০% নিশ্চয়তা', 'desc' => 'যাচাই করা মান'],
        ['icon' => '🚚', 'title' => 'দ্রুত ডেলিভারি', 'desc' => 'সারা দেশে ২–৩ দিনে হোম ডেলিভারি'],
        ['icon' => '💬', 'title' => 'কাস্টমার সার্ভিস', 'desc' => 'যেকোনো প্রয়োজনে আমরা পাশে আছি'],
    ],

    // অর্ডার স্ট্যাটাস (DB-তে যেভাবে থাকে → বাংলা লেবেল)
    'statuses' => [
        'pending'         => 'পেন্ডিং',
        'confirmed'       => 'কনফার্মড',
        'sent_to_courier' => 'কুরিয়ারে',
        'delivered'       => 'ডেলিভারড',
        'cancelled'       => 'বাতিল',
    ],

    // আপলোড ডিরেক্টরি — VPS-এ persistent volume mount করতে হবে
    'upload_dir' => env('UPLOAD_DIR') ?: storage_path('app/uploads'),
];
