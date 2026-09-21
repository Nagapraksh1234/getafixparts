<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            // The seller who owns this listing — a user with role = 'seller'.
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('name');
            $table->string('category');
            $table->string('image')->nullable(); // path relative to public/, e.g. images/products/walnut-desk.jpg
            $table->decimal('price', 10, 2);
            $table->decimal('original_price', 10, 2)->nullable(); // set this when the item is on sale
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};