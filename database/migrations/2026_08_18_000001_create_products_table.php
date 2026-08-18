<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// কলামের ধরন দুই ইঞ্জিনেই (PostgreSQL ও MySQL/MariaDB) চলে এমন রাখা হয়েছে:
//  • json — jsonb শুধু Postgres এ আছে, তাই json (আমরা পুরো অ্যারে পড়ি/লিখি,
//    JSON অপারেটর ব্যবহার করি না, তাই কোনো ফিচার হারায় না)
//  • যেসব কলামে UNIQUE/INDEX দরকার সেগুলো string(191) — MySQL দৈর্ঘ্য ছাড়া
//    TEXT কলামে ইনডেক্স বসাতে দেয় না (utf8mb4 তে সর্বোচ্চ ১৯১ অক্ষর)
//  • json কলামে MySQL ডিফল্ট ভ্যালু নেয় না, তাই nullable + মডেলে ফলব্যাক
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('slug', 191)->unique();
            $table->string('name', 191);
            $table->integer('price');
            $table->integer('old_price')->nullable();
            $table->string('badge', 191)->nullable();
            $table->text('short')->nullable();
            $table->text('image')->nullable();
            $table->json('images')->nullable();
            $table->json('sizes')->nullable();
            $table->json('variants')->nullable();   // [{name, price}]
            $table->json('features')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('active')->default(true);
            $table->timestampTz('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
