<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // key/value স্টোর — সিক্রেটগুলো Crypt::encryptString দিয়ে এনক্রিপ্টেড
        Schema::create('settings', function (Blueprint $table) {
            $table->text('key')->primary();
            $table->text('value')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
