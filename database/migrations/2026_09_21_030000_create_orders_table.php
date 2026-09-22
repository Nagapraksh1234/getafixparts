<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // the buyer
            $table->string('status')->default('pending'); // pending, fulfilled, cancelled
            $table->decimal('subtotal', 10, 2);

            // Shipping details, captured at checkout time
            $table->string('shipping_name');
            $table->string('shipping_phone');
            $table->string('shipping_address');
            $table->string('shipping_city');
            $table->string('shipping_postal_code');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};