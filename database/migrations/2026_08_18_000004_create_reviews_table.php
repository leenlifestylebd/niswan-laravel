<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // কলামগুলো Next.js lib/reviews-db.js এর সাথে হুবহু মিলিয়ে রাখা
        Schema::create('reviews', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('image_url');
            $table->integer('sort_order')->default(0);
            $table->timestampTz('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
