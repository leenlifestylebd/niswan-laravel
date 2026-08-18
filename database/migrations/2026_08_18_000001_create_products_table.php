<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('slug')->unique();
            $table->text('name');
            $table->integer('price');
            $table->integer('old_price')->nullable();
            $table->text('badge')->nullable();
            $table->text('short')->nullable();
            $table->text('image')->nullable();
            $table->jsonb('images')->default('[]');
            $table->jsonb('sizes')->default('[]');
            $table->jsonb('variants')->default('[]');   // [{name, price}]
            $table->jsonb('features')->default('[]');
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
