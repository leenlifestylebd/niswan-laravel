<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // key/value স্টোর — সিক্রেটগুলো Crypt::encryptString দিয়ে এনক্রিপ্টেড।
        // এনক্রিপ্টেড মান লম্বা হয়, তাই value থাকছে TEXT; key তে PRIMARY KEY
        // লাগে বলে string(191) — MySQL TEXT এ প্রাইমারি কী নেয় না।
        Schema::create('settings', function (Blueprint $table) {
            $table->string('key', 191)->primary();
            $table->text('value')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
