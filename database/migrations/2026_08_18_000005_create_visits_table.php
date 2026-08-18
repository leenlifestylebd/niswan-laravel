<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // প্রাইভেসি-বান্ধব ভিজিট লগ — কোনো PII নেই, visitor একমুখী হ্যাশ
        Schema::create('visits', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestampTz('created_at')->useCurrent();
            $table->string('path', 191)->nullable();
            $table->string('ref', 191)->nullable();     // referrer host: facebook/google/direct
            $table->string('visitor', 24)->nullable();  // sha256(ip|ua|APP_KEY) এর প্রথম ২৪ অক্ষর
            $table->string('device', 16)->nullable();   // mobile | desktop

            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visits');
    }
};
