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
            $table->string('name', 191);
            $table->string('phone', 32);
            $table->text('address');
            $table->string('product', 191)->nullable();
            $table->string('slug', 191)->nullable();
            $table->string('size', 64)->nullable();
            $table->string('color', 64)->nullable();
            $table->integer('qty')->default(1);
            $table->string('area', 64)->nullable();
            $table->integer('delivery_charge')->default(0);
            $table->integer('total')->default(0);
            // pending | confirmed | sent_to_courier | delivered | cancelled
            $table->string('status', 32)->default('pending');
            $table->string('consignment_id', 64)->nullable();
            $table->string('tracking_code', 64)->nullable();

            // MySQL এ TEXT কলামে দৈর্ঘ্য ছাড়া ইনডেক্স বসে না — তাই উপরে string()
            $table->index('created_at');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
