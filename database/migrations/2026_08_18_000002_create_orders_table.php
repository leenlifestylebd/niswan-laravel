<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestampTz('created_at')->useCurrent();
            $table->text('name');
            $table->text('phone');
            $table->text('address');
            $table->text('product')->nullable();
            $table->text('slug')->nullable();
            $table->text('size')->nullable();
            $table->text('color')->nullable();
            $table->integer('qty')->default(1);
            $table->text('area')->nullable();
            $table->integer('delivery_charge')->default(0);
            $table->integer('total')->default(0);
            // pending | confirmed | sent_to_courier | delivered | cancelled
            $table->text('status')->default('pending');
            $table->text('consignment_id')->nullable();
            $table->text('tracking_code')->nullable();

            $table->index('created_at');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
